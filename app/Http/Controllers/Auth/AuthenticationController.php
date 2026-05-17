<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterCompanyRequest;
use App\Http\Requests\RegisterJobSeekerRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthenticationController extends Controller
{
    public function registerJobSeeker(RegisterJobSeekerRequest $request)
{
    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'role' => 'job_seeker'
    ]);

    $certificate = $request->file('certificate')?->store('certificates','public');
    $photo = $request->file('photo')?->store('photos','public');

    $user->jobSeeker()->create([
        'id_number' => $request->id_number,
        'major' => $request->major,
        'experience_area' => $request->experience_area,
        'about_me' => $request->about_me,
        'certificate' => $certificate,
        'photo' => $photo,
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

    $certificate = $request->file('accreditation_certificate')?->store('company_certificates','public');

    $user->company()->create([
        'company_name' => $request->company_name,
        'company_email' => $request->company_email,
        'company_code' => $request->company_code,
        'company_address' => $request->company_address,
        'services' => $request->services,
        'bio' => $request->bio,
        'accreditation_certificate' => $certificate,
    ]);

    return response()->json([
        'message' => 'Company registered successfully'
    ]);
}
}
