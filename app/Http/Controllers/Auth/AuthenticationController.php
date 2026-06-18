<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterCompanyRequest;
use App\Http\Requests\RegisterJobSeekerRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
class AuthenticationController extends Controller
{


 public function login(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required'
    ]);

    $user = User::where('email', $request->email)->first();

    if (!$user) {
        return response()->json(['message' => 'User not found'], 404);
    }

    if (!Hash::check($request->password, $user->password)) {
        return response()->json(['message' => 'Wrong password'], 401);
    }


$token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
        'message' => 'Login successful',
        'debug_token' => $token,
        'user' => $user,
        'role' => $user->role,
       'token' => $token   
    ], 200);
}
    public function registerJobSeeker(RegisterJobSeekerRequest $request)
{


    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'role' => 'job_seeker'
    ]);
      $certificate = $request->certificate;
    

    $user->jobSeeker()->create([
        'id_number' => $request->id_number,
        'major' => $request->major,
        'experience_area' => $request->experience_area,
        'about_me' => $request->about_me,
        'certificate' => $certificate,
        'photo' =>  $request->photo,
    ]);

    return response()->json([
        'message' => 'Job seeker registered successfully'
    ]);
}
public function registerCompany(RegisterCompanyRequest $request)
{
    $user = User::create([
        'name' => $request->company_name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'role' => 'company'
    ]);

       $accreditation_certificate = $request->accreditation_certificate;
   

        $user->company()->create([
        'company_name' => $request->company_name,
        'company_email' => $request->company_email,
        'company_code' => $request->company_code,
        'company_address' => $request->company_address,
        'services' => $request->services,
        'bio' => $request->bio,
        'accreditation_certificate' => $accreditation_certificate,
        'photo_company' => $request->photo_company,
    ]);

    return response()->json([
        'message' => 'Company registered successfully'
    ]);
}
}