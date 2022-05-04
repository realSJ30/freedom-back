<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Traits\ApiTrait;
use App\Models\Roles;

class RolesController extends Controller
{
    //

    use ApiTrait;
    public function index(){
        $roles = Roles::all();

        return $this->onSuccess($roles);
    }
}
