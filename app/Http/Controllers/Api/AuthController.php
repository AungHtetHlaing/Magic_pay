<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\Request;
use App\Helpers\UUIDGenerate;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\StoreUserRequest;

class AuthController extends Controller
{
    public function register(StoreUserRequest $request)
    {
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->password = Hash::make($request->password);
        $user->ip = $request->ip();
        $user->user_agent = $request->server("HTTP_USER_AGENT");
        $user->login_at = date("Y-m-d H:i:s");
        $user->save();

        Wallet::firstOrCreate(
            [
                'user_id' => $user->id
            ],
            [
                'account_number' => UUIDGenerate::accountNumber(),
                'amount' => 0
            ]
        );

        $token = $user->createToken("magic_pay")->plainTextToken;

        return success("Register Successfully", ["token" => $token]);
    }

    public function login(Request $request) {
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $token = Auth::user()->createToken("magic_pay")->plainTextToken;

            return success("Login Successfully", ["token" => $token]);
        }

        return fail("Login Fail", ["error" => "Email or Password is wrong!"]);

    }

    public function logout() {
        Auth::user()->currentAccessToken()->delete();
        return success("Logout Successfully", null);
    }
}
