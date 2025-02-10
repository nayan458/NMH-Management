<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;

class AuthController extends Controller
{
    use DispatchesJobs, AuthorizesRequests, ValidatesRequests, HasApiTokens;

    //register Admin
    function register(Request $request) {
        try {
            $data = $request->validate([
                'name' => 'required',
                'email' => 'required | email',
                'phone_number' => 'required',
                'password' => 'required',
                'role' => 'required'
            ]);
    
            $admin = Admin::create([
                'name' => $data['name' ],
                'email' => $data['email' ],
                'phone_number' => $data['phone_number' ],
                'password' => Hash::make($data['password' ]),
                'role' => $data['role' ]
            ]);
    
            $token = $admin->createToken('admin_token',['Admin'])->plainTextToken;
            return (["message"=>"Registered Successfully","token"=>$token]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    //login Admin
    function adminLogin(Request $request) {
        $credentials = $request->validate([
            'email' => 'required| email',
            'password' => 'required'
        ]);

        if(Auth::guard('admin')->attempt($credentials)){
            $admin = Auth::guard('admin')->user();
            $token = $admin->createToken('admin_token', ['Admin'])->plainTextToken;

            return response()->json([
                'user' => $admin->name,
                'token' => $token,
                'role' => 'admin',
            ]);
        }
    }

    //login Admin
    function logout(){
        $user = Auth::user();
        $token = $user->tokens()->delete();
        return response()->json(["result" => $token]);
    }

    //check login status
    function is_login(){
        if(Auth::user())
            return true;
        else
            return false;
    }
}
