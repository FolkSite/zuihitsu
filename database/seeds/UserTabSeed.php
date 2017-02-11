<?php

use Illuminate\Database\Seeder;

class UserTabSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()

    {
      /**
      * поместил код в цикл чтобы создать 10 случайных пользователей
      **/
      
      for ($i=0; $i < 10; $i++) {
        DB::table('users')->insert([
          'name' => str_random(10),
          'email' => str_random(10).'@gmail.com',
          'password' => bcrypt('secret'),
        ]);
      }
    }
}
