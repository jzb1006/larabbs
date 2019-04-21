<?php

use Illuminate\Database\Seeder;
use App\Models\Topic;

class TopicsTableSeeder extends Seeder
{
    public function run()
    {
        //所有用户的 ID数组，如【1，2，3】
        $user_ids = \App\Models\User::all()->pluck('id')->toArray();
        //所有的分类ID数组
        $category_ids = \App\Models\Category::all()->pluck('id')->toArray();
        //获取Faker的实例
        $faker = app(Faker\Generator::class);

        $topics = factory(Topic::class)
            ->times(50)
            ->make()
            ->each(function ($topic, $index) use($user_ids,$category_ids,$faker) {
            //从用户ID的数组章中，随机取出一个并赋值
            $topic->user_id = $faker->randomElement($user_ids);
            //话题分类也是
            $topic->category_id = $faker->randomElement($category_ids);
        });
        // 将数据集合转换为数组，并插入到数据库中
        Topic::insert($topics->toArray());
    }

}

