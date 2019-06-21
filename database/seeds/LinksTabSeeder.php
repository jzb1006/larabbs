<?php

use Illuminate\Database\Seeder;

class LinksTabSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //生成数据集合
        $links = factory(\App\Models\Link::class)->times(5)->make();

        \App\Models\Link::insert($links->toArray());
    }
}
