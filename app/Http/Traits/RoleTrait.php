<?php
namespace App\Http\Traits;

trait RoleTrait{

    // check admin
    protected function isAdmin($user){
        if (!empty($user)) {
            return $user->tokenCan('admin');
        }

        return false;
    }
    // check mod
    protected function isModerator($user){
        if (!empty($user)) {
            return $user->tokenCan('moderator');
        }

        return false;
    }
    // check prem-sub
    protected function isPremSub($user){
        if (!empty($user)) {
            return $user->tokenCan('prem-subscriber');
        }

        return false;
    }
    // check sub
    protected function isSub($user){
        if (!empty($user)) {            
            return $user->tokenCan('subscriber');
        }

        return false;
    }
}
