<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SacetypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('spacetype')->insert([
            'vr_SpaceType'=> 'personal',
            'vr_SpaceName'=> '个人网盘',
            'vr_SpaceSize'=> 50,
            'vr_CreateTime'=> '2018-12-19',
            'vr_UpdateTime'=> '2018-12-19'
        ]);
        DB::table('spacetype')->insert([
            'vr_SpaceType'=> 'public',
            'vr_SpaceName'=> '公共网盘',
            'vr_SpaceSize'=> 0,
            'vr_CreateTime'=> '2018-12-19',
            'vr_UpdateTime'=> '2018-12-19'
        ]);
    }
}
