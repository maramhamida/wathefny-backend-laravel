<!DOCTYPE html>
<html>
<head>
    <title>Account Status</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #EDF2EC; padding: 20px;">

    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 30px; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        
        <h2 style="color: #4CA6A8; text-align: center;">Welcome to Wazafni Platform</h2>
        
        <p>Dear <strong>{{ $user->name }}</strong>,</p>

        @if($status === 'approved')
            <div style="background-color: #edf7ed; color: #1e4620; padding: 15px; border-radius: 10px; margin: 20px 0;">
                <strong>Congratulations!</strong> Your company account has been reviewed and approved by our admin team. You can now log in to your app and start publishing jobs.
            </div>
        @else
            <div style="background-color: #fdeded; color: #5f2120; padding: 15px; border-radius: 10px; margin: 20px 0;">
                <strong>Account Update:</strong> Unfortunately, your account request has been rejected at this time because your credentials or certificate could not be verified.
            </div>
        @endif

        <p style="color: #666; font-size: 14px; text-align: center; margin-top: 30px;">
            Thank you for choosing Wazafni.<br>
            © 2026 Wazafni Team. All rights reserved.
        </p>
    </div>

</body>
</html>