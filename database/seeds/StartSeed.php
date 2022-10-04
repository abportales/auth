<?php

use App\Rol;
use Illuminate\Database\Seeder;

class StartSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $userRol = new Rol();
        $userRol->name = "user";
        $userRol->save();

        $userRol = new Rol();
        $userRol->name = "Admin";
        $userRol->save();
    }
}
