<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Auth;
use App\Http\Traits\RoleTrait;

class AuthController extends Controller
{

    use RoleTrait;

    // get csrf token via postman -- for testing purpose
    public function csrf(){
        return csrf_token();
    }

    // login function
    public function login(Request $request){    
        
        // dd('hello');
        $validator = Validator::make($request->all(), [           
            'email' => 'required',
            'password' => 'required',                                         
        ]);

        if($validator->fails()){
            $response = [
                'message' => 'Email/Password Incorrect!', 
                'error' => $validator->messages()              
            ];
            return response($response, 201);       
        }

        if (Auth::guard()->attempt($request->only('email', 'password'))) {          
            $user = Auth::user();  

            // revoke all previous tokens
            auth()->user()->tokens()->delete();
                        
            switch ($user->role) {
                case 1:
                    # admin
                    $token = $user->createToken('personal-token',['admin'])->plainTextToken;      
                    break;
                case 2:
                    # mode
                    $token = $user->createToken('personal-token',['moderator'])->plainTextToken;      
                    break;
                case 3:
                    # prem-sub
                    $token = $user->createToken('personal-token',['prem-subscriber'])->plainTextToken;      
                    break;
                case 4:
                    # sub
                    $token = $user->createToken('personal-token',['subscriber'])->plainTextToken;      
                    break;                
                default:
                    # sub
                    $token = $user->createToken('personal-token',['subscriber'])->plainTextToken;      
                    break;
            }                          
            return response()->json([
                'user'=>$user,
                'token'=>$token
            ], 201);
        }

        return response()->json(['message' => 'Invalid credentials']);
    }


     // web route logout - web
    public function logout(Request $request)
    {
         // dd(auth()->user()->currentAccessToken());
         Auth::guard('web')->logout();    
         
         // auth()->user()->currentAccessToken()->delete();
 
         $request->session()->invalidate();
 
         $request->session()->regenerateToken();
 
         return response()->json(['message'=>'Logout success!'], 201);
    }


    // api route logout - mobile
    public function logout_api(Request $request)
    {
        // revoke current token
        // auth()->user()->currentAccessToken()->delete();

        // revoke all tokens
        auth()->user()->tokens()->delete();

        return response()->json([
            'message'=>'Logout success!',            
        ], 201);
    }



    // unauthorized
    public function unauthorized(){
        $response = [
            'message' => 'Unauthorized access!',            
        ];    
         return response($response, 401);
    }

}
