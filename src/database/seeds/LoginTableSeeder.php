<?php

use Illuminate\Database\Seeder;
use Quill\Login\Models\Login;

class LoginTableSeeder extends Seeder
{
   	/**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Login::class, 10)->create();
    }

}
