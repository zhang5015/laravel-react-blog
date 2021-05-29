<?php

use Illuminate\Database\Seeder;

class MoviesTableSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    for ($i = 1; $i <= 16; $i++) {
      $titie = '跟徐湛学国画' . str_pad($i, 3, "0", STR_PAD_LEFT);
      DB::table('movies')->insert([
          'id' => 2 + $i,
          'title' =>   $titie ,
          'cover' => $i,
          'content' => 'admin@qq.com',
          'source' => '000 (' . $i . ').flv',
          'is_top' => false,
          'is_hidden' => false,
          'updated_at' => now(),
          'created_at' => now(),
          ]);
    }
  }
}
