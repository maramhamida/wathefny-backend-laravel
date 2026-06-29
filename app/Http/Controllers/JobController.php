<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Job;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon; 
use Smalot\PdfParser\Parser;

class JobController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'salary' => 'required|string|max:255',
            'employment_type' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $company = $request->user()->company;

        if (!$company) {
            return response()->json(['message' => 'الملف الشخصي للشركة غير موجود.'], 403);
        }

        $job = $company->jobs()->create([
            'title' => $request->title,
            'location' => $request->location,
            'salary' => $request->salary,
            'employment_type' => $request->employment_type,
            'description' => $request->description,
        ]);

        return response()->json([
            'message' => 'Job created successfully',
            'job' => $job
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $company = $request->user()->company;

        if (!$company) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $job = $company->jobs()->find($id);

        if (!$job) {
            return response()->json(['message' => 'Job not found'], 404);
        }

        $data = $request->only(['title', 'location', 'salary', 'employment_type', 'description']);
        
        if ($job->status == 'closed') {
            $data['status'] = 'active';
        }

        $job->update($data);

        return response()->json([
            'message' => 'Job updated and activated successfully',
            'job' => $job
        ], 200);
    }

    public function destroy(Request $request, $id)
    {
        $company = $request->user()->company;

        if (!$company) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $job = $company->jobs()->find($id);

        if (!$job) {
            return response()->json(['message' => 'Job not found or unauthorized'], 404);
        }

        $job->delete();

        return response()->json(['message' => 'Job deleted successfully'], 200);
    }

    public function closeJob(Request $request, $job)
    {
        $company = $request->user()->company;

        if (!$company) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $jobInstance = $company->jobs()->find($job);

        if (!$jobInstance) {
            return response()->json(['message' => 'Job not found or unauthorized'], 404);
        }

        $jobInstance->update(['status' => 'closed']);

        return response()->json(['message' => 'Job closed successfully'], 200);
    }

public function index(Request $request)
{
    $query = \App\Models\Job::with('company')
        ->where(function($q) {
            $q->where('status', '!=', 'closed')
              ->orWhereNull('status');
        });

    // 1. فلتر التصنيفات (Remote, Full Time, Senior)
    if ($request->has('filter') && $request->filter != 'All') {
        $filter = strtolower(trim($request->filter));
        $query->where(function($q) use ($filter) {
            if ($filter == 'remote') {
                $q->whereRaw('LOWER(location) LIKE ?', ['%remote%'])
                  ->orWhereRaw('LOWER(employment_type) LIKE ?', ['%remote%']);
            } elseif ($filter == 'full time' || $filter == 'full-time') {
                $q->whereRaw('LOWER(employment_type) LIKE ?', ['%full%time%']);
            } elseif ($filter == 'senior') {
                $q->whereRaw('LOWER(title) LIKE ?', ['%senior%'])
                  ->orWhereRaw('LOWER(description) LIKE ?', ['%senior%']);
            }
        });
    }

    // 2. البحث (Search)
    if ($request->has('search') && !empty($request->search)) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('title', 'LIKE', "%{$search}%")
              ->orWhere('description', 'LIKE', "%{$search}%")
              ->orWhere('employment_type', 'LIKE', "%{$search}%");
        });
    }

    $jobs = $query->latest()->get();

    return response()->json([
        'status' => 'success',
        'data' => $jobs
    ], 200);
}
    public function apply(Request $request, Job $job)
    {
        $request->validate(['cv' => 'required|file|mimes:pdf|max:2048']);
        $cvPath = $request->file('cv')->store('cvs', 'public');
        $fullPath = storage_path('app/public/' . $cvPath);

        try {
            $parser = new Parser();
            $pdf = $parser->parseFile($fullPath);
            $cvText = $pdf->getText();
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'فشل في قراءة ملف الـ PDF: ' . $e->getMessage()
            ], 400);
        }

        $jobDescription = strtolower($job->description);
        $words = str_word_count($jobDescription, 1);
        $stopWords = ['the', 'and', 'with', 'for', 'you', 'your', 'that', 'this', 'work', 'job', 'a', 'an', 'in', 'on', 'at'];
        $keywords = array_unique(array_diff($words, $stopWords));

        $matchedSkills = [];
        $missingSkills = [];
        $cvTextLower = strtolower($cvText);

        foreach ($keywords as $word) {
            if (str_contains($cvTextLower, $word)) {
                $matchedSkills[] = $word;
            } else {
                $missingSkills[] = $word;
            }
        }

        $totalKeywords = count($keywords);
        $matches = count($matchedSkills);
        $atsScore = $totalKeywords > 0 ? (int)min(($matches / $totalKeywords) * 100, 100) : 0;

        if ($request->has('final_submit') && $request->final_submit == '1') {
            DB::table('job_applications')->insert([
                'job_post_id' => $job->id,
                'user_id' => $request->user()->id,
                'cv_path' => $cvPath, 
                'ats_score' => $atsScore,
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json([
                'message' => 'تم التقديم للوظيفة بنجاح!',
                'ats_score' => $atsScore
            ], 200);
        }

        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($cvPath)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($cvPath);
        }

        return response()->json([
            'message' => 'تم الفحص بنجاح',
            'ats_score' => $atsScore,
            'matched_skills' => $matchedSkills,
            'missing_skills' => $missingSkills
        ], 200);
    }

    public function getSavedJobs(Request $request)
    {
        $savedJobs = $request->user()->savedJobs()->with('company')->get();
        
        $formatted = $savedJobs->map(function ($job) {
            return [
                'id' => $job->id,
                'title' => $job->title,
                'company_name' => $job->company ? $job->company->company_name : 'Unknown',
                'location' => $job->location,
                'employment_type' => $job->employment_type,
                'salary' => $job->salary,
                'created_at' => $job->created_at ? $job->created_at->diffForHumans() : 'Just now',
            ];
        });

        return response()->json(['data' => $formatted], 200);
    }
public function toggleSave(Request $request, $jobId)
{
    $user = $request->user();
    
    $job = \App\Models\Job::find($jobId);
    if (!$job) {
        return response()->json(['message' => 'Job not found'], 404);
    }

    if ($user->savedJobs->contains($jobId)) {
        $user->savedJobs()->detach($jobId);
        return response()->json(['status' => 'removed'], 200);
    } else {
        $user->savedJobs()->attach($jobId);
        return response()->json(['status' => 'saved'], 200);
    }
}
    public function show($id)
{
    $job = \App\Models\Job::with('company')->findOrFail($id);
    return response()->json(['data' => $job], 200);
} 
public function myApplications(Request $request)
{
    
    $applications = DB::table('job_applications')
        ->join('job_posts', 'job_applications.job_post_id', '=', 'job_posts.id')
        ->where('job_applications.user_id', $request->user()->id)
        ->select(
            'job_applications.*',
            'job_posts.title as job_title', 
            'job_posts.company_id'
        )
        ->orderBy('job_applications.created_at', 'desc')
        ->get();

    return response()->json([
        'status' => 'success',
        'data' => $applications
    ], 200);
}
public function getJobSeekerProfile(Request $request)
{
    return response()->json(
        $request->user()->jobSeeker
    );
}
public function updateJobSeekerProfile(Request $request)
{
    $jobSeeker = $request->user()->jobSeeker;

    if (!$jobSeeker) {
        return response()->json(['message' => 'Profile not found'], 404);
    }

    $jobSeeker->update($request->only([
        'major',
        'experience_area',
        'about_me',
    ]));

    return response()->json([
        'message' => 'Profile updated successfully',
        'jobSeeker' => $jobSeeker
    ]);
}
public function updateJobSeekerPhoto(Request $request)
{
    $request->validate([
        'photo' => 'required|image|max:2048',
    ]);

    $jobSeeker = $request->user()->jobSeeker;

    $path = $request->file('photo')->store('jobseekers', 'public');

    $jobSeeker->update([
        'photo' => $path
    ]);

    return response()->json([
        'message' => 'Photo updated',
        'url' => asset('storage/' . $path)
    ]);
}
}