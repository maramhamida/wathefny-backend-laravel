<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompanyController extends Controller
{
    public function dashboard(Request $request)
    {
        $company = $request->user()->company;

        if (!$company) {
            return response()->json(['message' => 'Company profile not found'], 404);
        }

        $totalJobs = $company->jobs()->count();

        $closedJobs = $company->jobs()->where('status', 'closed')->count();

        // عدد المتقدمين
        $totalApplicants = DB::table('job_applications')
            ->join('job_posts', 'job_applications.job_post_id', '=', 'job_posts.id')
            ->where('job_posts.company_id', $company->id)
            ->count();

        // عدد الموظفين (المقبولين فقط)
        $totalEmployees = DB::table('employees')
            ->where('company_id', $company->id)
            ->where('status', 'active')
            ->count();

        // عدد المرفوضين (FIX للخطأ اللي عندك)
        $rejected = DB::table('job_applications')
            ->join('job_posts', 'job_applications.job_post_id', '=', 'job_posts.id')
            ->where('job_posts.company_id', $company->id)
            ->where('job_applications.status', 'rejected')
            ->count();

        return response()->json([
            'stats' => [
                'applicants' => $totalApplicants,
                'employees'  => $totalEmployees,
                'rejected'   => $rejected,
                'jobs'       => $totalJobs,
            ],
            'recent_jobs' => $company->jobs()->latest()->take(5)->get(),
            'recent_applicants' => [],
            'company_photo' => $company->photo_company
        ], 200);
    }

    public function allJobs(Request $request)
    {
        $company = $request->user()->company;

        if (!$company) {
            return response()->json(['message' => 'Company profile not found'], 404);
        }

        return response()->json([
            'jobs' => $company->jobs()->latest()->get()
        ], 200);
    }

    public function updateProfile(Request $request)
    {
        $company = $request->user()->company;

        if (!$company) {
            return response()->json(['message' => 'Company profile not found'], 404);
        }

        $company->update($request->only([
            'bio',
            'services',
            'company_address'
        ]));

        return response()->json([
            'message' => 'Profile updated successfully',
            'company' => $company
        ], 200);
    }

    public function getProfile(Request $request)
    {
        return response()->json($request->user()->company, 200);
    }

    public function updatePhoto(Request $request)
    {
        $request->validate([
            'photo_company' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $company = $request->user()->company;

        if (!$company) {
            return response()->json(['message' => 'Company profile not found'], 404);
        }

        $path = $request->file('photo_company')->store('companies', 'public');

        $company->update(['photo_company' => $path]);

        return response()->json([
            'message' => 'Photo updated successfully',
            'url' => asset('storage/' . $path)
        ], 200);
    }

    public function getAllApplicants(Request $request)
    {
        $companyId = $request->user()->company->id;

        $applications = DB::table('job_applications')
            ->join('job_posts', 'job_applications.job_post_id', '=', 'job_posts.id')
            ->join('users', 'job_applications.user_id', '=', 'users.id')
            ->where('job_posts.company_id', $companyId)
            ->where('job_applications.status', 'pending')
            ->select(
                'job_applications.id',
                'job_applications.cv_path',
                'job_applications.ats_score',
                'job_applications.status',
                'users.id as user_id',
                'users.name as user_name',
                'users.email',
                'job_posts.title as job_title'
            )
            ->orderByDesc('job_applications.ats_score')
            ->get()
            ->map(function ($item) {
                $item->cv_url = asset('storage/' . $item->cv_path);
                return $item;
            });

        return response()->json([
            'data' => $applications
        ]);
    }

    public function updateApplicationStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:accepted,rejected'
        ]);

        $application = DB::table('job_applications')
            ->join('job_posts', 'job_applications.job_post_id', '=', 'job_posts.id')
            ->select('job_applications.*', 'job_posts.company_id')
            ->where('job_applications.id', $id)
            ->first();

        if (!$application) {
            return response()->json(['message' => 'Application not found'], 404);
        }

        DB::table('job_applications')
            ->where('id', $id)
            ->update([
                'status' => $request->status,
                'updated_at' => now()
            ]);

        if ($request->status === 'accepted') {
            DB::table('employees')->updateOrInsert(
                [
                    'user_id' => $application->user_id,
                    'company_id' => $application->company_id,
                    'job_post_id' => $application->job_post_id,
                ],
                [
                    'status' => 'active',
                    'hire_date' => now(),
                    'updated_at' => now()
                ]
            );
        }

        return response()->json([
            'message' => 'Status updated successfully'
        ], 200);
    }

public function getEmployees(Request $request)
{
    $company = $request->user()->company;

    $employees = DB::table('employees')
        ->join('users', 'employees.user_id', '=', 'users.id')
        ->join('job_applications', function ($join) {
            $join->on('job_applications.user_id', '=', 'employees.user_id');
        })
        ->where('employees.company_id', $company->id)
        ->where('job_applications.status', 'accepted')
        ->select(
            'employees.id',
            'employees.status',
            'employees.hire_date',
            'users.name',
            'users.email',
            'job_applications.cv_path'
        )
        ->orderBy('job_applications.id', 'desc')
        ->get()
        ->map(function ($emp) {
            $emp->cv_url = $emp->cv_path
                ? asset('storage/' . $emp->cv_path)
                : null;

            return $emp;
        });

    return response()->json([
        'data' => $employees
    ]);
}
}