<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Roles;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $roles = ['Admin','Moderator','Prem-Sub','Sub'];

        $data = array();
        foreach ($roles as $role) {
            # code...
            array_push($data,[
                'title'=>$role
            ]);
        }
        Roles::insert($data);
    }
}
