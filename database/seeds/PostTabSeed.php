<?php

use Illuminate\Database\Seeder;

class PostTabSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()

    {
      /**
      * поместил код в цикл чтобы создать 10 случайных постов
      **/

      $handle_json = fopen("schopenhauer.json", "r");
      $blank_arr = json_decode(fgets($handle_json), true);
      fclose($handle_json);

      for ($i=0; $i < count($blank_arr); $i++) {
        DB::table('posts')->insert([
          'user_id' => 11,
          /*
          'header' => str_random(128),
          'message' => str_random(512),
          */
          'header' => $blank_arr[$i]['header'],
          'message' => $blank_arr[$i]['message'],
          'tags' => $blank_arr[$i]['tags'],
          'created_at' => date('Y-m-d G:i:s'),
        ]);
      }
    }
}
