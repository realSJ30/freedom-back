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

        return $this->onSuccess($store->first(),'Store updated successfully!');
    }

    public function getStores(){

        // if admin|moderator retreive all stores
        if($this->isAdmin(auth()->user()) || $this->isModerator(auth()->user())){
            $stores = Stores::all();
            return $this->onSuccess($stores);
        }else{
            // if not find all store that associated with user id
            $stores = User::with('stores')->find(auth()->user()->id)->stores;
            return $this->onSuccess($stores);
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
