<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\PasswordReset;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $data = request()->validate([
            'email' => 'required|string|email|max:255',
            'password' => 'required|string'
        ]);

        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            $responseData = [401, "Wrong email or password!"];
            return response()->json($responseData);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        $responseData = [
            $user,
            $token
        ];

        return response()->json([200, $responseData]);
    }

    public function logout(Request $request)
    {
        auth('sanctum')->user()->tokens()->delete();

        $responseData = [200, "Logged out"];

        return response()->json($responseData);
    }

    public function forgot_password(Request $request)
    {
        $data = request()->validate([
            'email' => 'required|string|email|max:255'
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user) {
//            return response()->json([200,"Reset password instructions sent to your email"]);


            $code = rand(111111, 999999);
            $user->verification_code = $code;
            $user->save();

            $data = [
                'name' => $user->name,
                'code' => $code,
            ];

            Mail::to($user->email)->send(new PasswordReset($data));
            return response()->json([200, "Reset password instructions sent to your email"]);
        }
        return response()->json([401, "Email not found!"]);
    }

    public function reset_password(Request $request){
        $data = request()->validate([
            'password' => 'required|string|min:8|confirmed',
            'otp_code' => 'required',
        ]);

        $user = User::where('verification_code',$request->otp_code)->first();

        if (!$user){
            return response()->json([401,"Bad Request"]);
        }

        $currentTime = now();

        // Calculate the difference in minutes
        $differenceInMinutes = $user->updated_at->diffInMinutes($currentTime);

        // Check if the difference is greater than 5 minutes
        if ($differenceInMinutes > 5) {
            $user->verification_code = null;
            $user->save();
            return response()->json([401,"Sorry OTP Code Expired"]);
        } else {
            //Validate the otp code
            if ($user->verification_code == $request->otp_code){
                $user->verification_code = null;
                $user->password = Hash::make($request->password);
                $user->save();
                return response()->json([200,"Password Updated Successful"]);
            }else{
                return response()->json([401,"Sorry Unknown OTP Code"]);
            }
        }
    }

    public function get_user_profile($id){
        $user = User::findOrfail($id);
        return response()->json([200,$user]);
    }

    public function update_profile(Request $request, $id)
    {
        $data = request()->validate([
            'name' => 'required',
            'email' => 'required|string|email|max:255',
        ]);

        $user = User::findOrfail($id);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();

        $responseData = [200, $user];

        return response()->json($responseData);
    }

    public function update_password(Request $request, $id){
        $data = request()->validate([
            'old_password' => ['required'],
            'new_password' => ['required','string','min:8','confirmed']
        ]);

        $user = User::findOrfail($id);

        // Check if old password match the current existing password
        if(!Hash::check($request->old_password, $user->password)){
            $responseData = [403,"Old password provided does not match existing password"];
            return response()->json($responseData);
        }

        //Updating the password to new provided password
        $user->password = Hash::make($request->new_password);

        $user->save();
        $responseData = [200,"Password Updated Successfully!",$user];

        return response()->json($responseData);
    }
}
