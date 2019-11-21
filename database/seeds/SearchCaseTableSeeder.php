<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class SearchCaseTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('searchcases')->insert([
            'case_id' => '2019-0',
            'request' => Str::random(10).'@gmail.com',
            'status' => 0,
            'registrar' => 0,
            'download' => 0,
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),ol
}
