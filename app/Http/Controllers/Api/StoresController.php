<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Traits\RoleTrait;
use App\Http\Traits\ApiTrait;
use App\Models\Stores;
use App\Models\User;
use DB;

class StoresController extends Controller
{
    use RoleTrait;
    use ApiTrait;
    //
    public function create(Request $request){        
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'location' => 'required',
            'lat' => 'required',                                        
            'long' => 'required',                                      
            // 'user_id' => 'required',
        ]);

        if($validator->fails()){
            return $this->onError(201,'Store not created!');
        }
        
        if($this->isAdmin(auth()->user()) || $this->isPremSub(auth()->user()) || $this->isSub(auth()->user())){                                            
            $store = Stores::create([
                'name' => $request->name,
                'location' => $request->location,
                'lat' => $request->lat,
                'long' => $request->long,
                'user_id' => auth()->user()->id,
                'created_at'=>DB::raw('CURRENT_TIMESTAMP'),
                'updated_at'=>DB::raw('CURRENT_TIMESTAMP'),
            ]);
            $store->owner = auth()->user()->name;

            return $this->onSuccess($store,'Store created successfully!', 200);
        }        

        return $this->onError(201,'Store not created!');
    }

    public function update(Request $request,$id){
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'location' => 'required',
            'lat' => 'required',                                        
            'long' => 'required',                                      
            // 'user_id' => 'required',
        ]);

        if($validator->fails()){
            return $this->onError(201,'Store not updated!');
            // $response = [
            //     'message' => 'Store not updated!', 
            //     'error' => $validator->messages()              
            // ];
        }
        $store = Stores::find($id);   
        

        if($store->user_id != auth()->user()->id){
            return $this->onError(401,'Unauthorized store update!');
        }

        $store->update([
            'name' => $request->name,
            'location' => $request->location,
            'lat' => $request->lat,
            'long' => $request->long
        ]);

        return $this->onSuccess($store,'Store updated successfully!');
    }

    public function get_stores(){
        $role = auth()->user()->role;
        // web
        if($role == 1 || $role == 2){
            $stores = Stores::join('users','stores.user_id','users.id')
            ->select('stores.*','users.name as owner')
            ->orderBy('stores.created_at','desc')
            ->get();
            return $this->onSuccess($stores);
        }else{
            $stores = Stores::join('users','stores.user_id','users.id')
            ->where('stores.user_id','=',auth()->user()->id)
            ->select('stores.*','users.name as owner')
            ->orderBy('stores.created_at','desc')
            ->get();
            return $this->onSuccess($stores);
        }
        // if admin|moderator retreive all stores
        if($this->isAdmin(auth()->user()) || $this->isModerator(auth()->user())){
            $stores = Stores::join('users','stores.user_id','users.id')
            ->select('stores.*','users.name as owner')
            ->orderBy('stores.created_at','desc')
            ->get();
            return $this->onSuccess($stores);
        }else{
            // if not find all store that associated with user id
            // $stores = User::with('stores')->find(auth()->user()->id)->stores;
            $stores = Stores::join('users','stores.user_id','users.id')
            ->where('stores.user_id','=',auth()->user()->id)
            ->select('stores.*','users.name as owner')
            ->orderBy('stores.created_at','desc')
            ->get();
            return $this->onSuccess($stores);
        }                
    }

    public function get_store($id){

        $role = auth()->user()->role;
        // web
        if($role == 1 || $role == 2){
            $store = Stores::join('users','stores.user_id','users.id')
            ->select('stores.*','users.name as owner')
            ->where('stores.id','=',$id)
            ->orderBy('stores.created_at','desc')
            ->first();
            return $this->onSuccess($store);
        }else{
            $store = Stores::join('users','stores.user_id','users.id')
            ->where('stores.user_id','=',auth()->user()->id)
            ->where('stores.id','=',$id)
            ->select('stores.*','users.name as owner')
            ->orderBy('stores.created_at','desc')
            ->first();
            return $this->onSuccess($store);
        }

        // mobile
        // if admin|moderator retreive all stores
        if($this->isAdmin(auth()->user()) || $this->isModerator(auth()->user())){
            $store = Stores::join('users','stores.user_id','users.id')
            ->select('stores.*','users.name as owner')
            ->where('stores.id','=',$id)
            ->orderBy('stores.created_at','desc')
            ->first();
            return $this->onSuccess($store);
        }else{
            // if not find all store that associated with user id
            // $store = User::with('stores')->find(auth()->user()->id)->stores;
            $store = Stores::join('users','stores.user_id','users.id')
            ->where('stores.user_id','=',auth()->user()->id)
            ->where('stores.id','=',$id)
            ->select('stores.*','users.name as owner')
            ->orderBy('stores.created_at','desc')
            ->first();
            return $this->onSuccess($store);
        }                
    }

    public function delete($id){
        $store = Stores::find($id);
        if($this->isAdmin(auth()->user())){            
            $store->update([
                'is_active'=>0
            ]);
            return $this->onSuccess($store->first(),'Successfully removed!');
        }
        if($store->user_id == auth()->user()->id){
            $store->update([
                'is_active'=>0
            ]);
            return $this->onSuccess($store->first(),'Successfully removed!');
        }

        return $this->onError(401, 'Unauthorized access!');
    }

    public function restore($id){
        $store = Stores::find($id);
        if($this->isAdmin(auth()->user())){            
            $store->update([
                'is_active'=>1
            ]);
            return $this->onSuccess($store->first(),'Successfully restored!');
        }
        if($store->user_id == auth()->user()->id){
            $store->update([
                'is_active'=>1
            ]);
            return $this->onSuccess($store->first(),'Successfully restored!');
        }

        return $this->onError(401, 'Unauthorized access!');
    }

}
