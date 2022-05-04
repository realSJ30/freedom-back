<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Http\Traits\ApiTrait;
use App\Http\Traits\RoleTrait;
use DB;
use Auth;

class UserController extends Controller
{
    //

    use ApiTrait;
    use RoleTrait;

    public function index(){

        // $users = User::all();
         // check token ability
        if(auth()->user()->tokenCan('admin')){
            $users = User::join('roles','users.role','roles.id')
            ->select('users.id','users.name','users.email','users.is_active','users.role as role_id','roles.title as role')
            ->get();
           return $this->onSuccess($users);
        }
        
        return $this->onError(401, 'Unauthorized access!');        
    }

    public function register_user(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required',
            'password' => 'required',                                         
        ]);

        if($validator->fails()){
            $response = [
                'message' => 'User not created!', 
                'error' => $validator->messages()              
            ];
            return response($response, 201);       
        }

        $user = User::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'role'=>4,
            'password'=>Hash::make($request->password),
            'created_at'=>DB::raw('CURRENT_TIMESTAMP'),
            'updated_at'=>DB::raw('CURRENT_TIMESTAMP'),
        ]);
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
    }

    public function create_user(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required',
            'password' => 'required',  
            'user_role' => 'required',                                         
        ]);

        if($validator->fails()){
            $response = [
                'message' => 'User not created!', 
                'error' => $validator->messages()              
            ];
            return response($response, 201);       
        }
        if($this->isAdmin(auth()->user())){
            $user = User::create([
                'name'=>$request->name,
                'email'=>$request->email,
                'user_role'=>$request->user_role,
                'password'=>Hash::make($request->password),
                'created_at'=>DB::raw('CURRENT_TIMESTAMP'),
                'updated_at'=>DB::raw('CURRENT_TIMESTAMP'),
            ]);
            return $this->onSuccess($user,'User created successfully!');
        }

        return $this->onError(401, 'Unauthorized access!');        
    }

    public function update_user(Request $request){       
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required',            
        ]);
        if($validator->fails()){
            $response = [
                'message' => 'User not updated!', 
                'error' => $validator->messages()              
            ];
            return response($response, 201);       
        }

        $user = User::where('id',auth()->user()->id);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,            
        ]);            

        return response()->json(
        [
            'user'=>$user->first()
        ], 200);
    }


    public function update_user_role(Request $request,$id){       
        $validator = Validator::make($request->all(), [
            'role' => 'required',            
        ]);
        if($validator->fails()){
            $response = [
                'message' => 'User not updated!', 
                'error' => $validator->messages()              
            ];
            return response($response, 201);       
        }

        $user = User::find($id);

        $user->update([
            'role' => $request->role,            
        ]);            


        return $this->onSuccess($user->join('roles','users.role','roles.id')->select('users.id','users.name','users.email','users.is_active','users.role as role_id','roles.title as role')->first(),'User updated successfully!');
    }

    public function delete($id){
        $user = User::find($id);
        if($this->isAdmin(auth()->user())){
            $user->update([
                'is_active'=>0
            ]);
            return $this->onSuccess($user->first(),'Successfully deleted!');
        }
        return $this->onError(401,'Unauthorized access!');
    }
    public function restore($id){
        $user = User::find($id);
        if($this->isAdmin(auth()->user())){
            $user->update([
                'is_active'=>1
            ]);
            return $this->onSuccess($user->first(),'Successfully restored!');
        }
        return $this->onError(401,'Unauthorized access!');
    }
}
