<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Topic::class, function (Faker $faker) {
    $sentence = $faker->sentence(); //成成随机的小句子
    $updated_at = $faker->dateTimeThisMonth();//随机成成一个月内的时间
    $created_at = $faker->dateTimeThisMonth($updated_at);//传参是为生成最大的时间不超过，传入的时间。

    return [
        // 'name' => $faker->name,
        'title'=>$sentence,
        'body'=>$faker->text(),//生成大段的句子
        'excerpt'=>$sentence,
        'created_at'=>$created_at,
        'updated_at'=>$updated_at
    ];
});
