<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\JobSeeker;

class JobSeekerProfileController extends Controller
{
    public function getProfile()
    {
        $user = Auth::user();
      
        $jobSeeker = JobSeeker::where('user_id', $user->id)->with('user')->first();

        if (!$jobSeeker) {
            return response()->json(['message' => 'Profile not found'], 404);
        }

        return response()->json(['data' => $jobSeeker], 200);
    }

   
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $jobSeeker = JobSeeker::where('user_id', $user->id)->first();

        if (!$jobSeeker) {
            return response()->json(['message' => 'Profile not found'], 404);
        }

        $jobSeeker->update($request->only([
            'major', 
            'experience_area', 
            'about_me'
        ]));

        return response()->json([
            'message' => 'Profile updated successfully',
            'data' => $jobSeeker
        ], 200);
    }

    public function updatePhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = Auth::user();
        $jobSeeker = JobSeeker::where('user_id', $user->id)->first();

        if (!$jobSeeker) {
            return response()->json(['message' => 'Profile not found'], 404);
        }

        if ($request->hasFile('photo')) {
            if ($jobSeeker->photo && Storage::disk('public')->exists($jobSeeker->photo)) {
                Storage::disk('public')->delete($jobSeeker->photo);
            }

       
            $path = $request->file('photo')->store('jobseekers_photos', 'public');
            
            $jobSeeker->photo = $path;
            $jobSeeker->save();

            return response()->json([
                'message' => 'Photo updated successfully',
                'photo_path' => $path
            ], 200);
        }

        return response()->json(['message' => 'No image uploaded'], 400);
    }
}