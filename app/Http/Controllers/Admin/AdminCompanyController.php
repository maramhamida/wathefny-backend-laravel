<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Mail\CompanyStatusMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AdminCompanyController extends Controller
{public function getPendingCompanies()
    {
        $companies = User::where('role', 'company')->where('status', 'pending')->get();
        
        return response()->json($companies, 200);
    }
    public function updateStatus(Request $request, int $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected'
        ]);

        $user = User::findOrFail($id);
        
        $user->status = $request->status;
        $user->save();

        try {
            Mail::to($user->email)->send(new CompanyStatusMail($user, $request->status));
            
            return response()->json([
                'message' => 'Company status updated and email notification sent successfully!'
            ], 200);
            
        } catch (\Exception $e) {
            
            return response()->json([
                'message' => 'Status updated, but email failed to send: ' . $e->getMessage()
            ], 200);
        }
    }
  public function index()
{
    $companies = User::where('role', 'company')->get(); 
    return view('admin.dashboard', compact('companies'));
}

   public function updateStatusWeb(Request $request, int $id)
{
    $request->validate(['status' => 'required|in:approved,rejected']);
    
    $user = User::findOrFail($id);
    
  
    $user->status = $request->status;
    $user->save();

    try {
        \Illuminate\Support\Facades\Mail::to($user->email)
            ->send(new \App\Mail\CompanyStatusMail($user, $request->status));
            
        return redirect()->back()->with('success', 'تم التحديث وإرسال الإيميل بنجاح!');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'تم التحديث ولكن فشل الإيميل: ' . $e->getMessage());
    }
}
}