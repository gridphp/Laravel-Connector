<?php

use Illuminate\Database\Seeder;

class SuperherosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\App\Superhero::class,30)->create();


    }
}
