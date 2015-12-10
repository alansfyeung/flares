<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(App\SystemUser::class, function (Faker\Generator $faker) {
    return [
        'forums_username' => $faker->name,
        'access_level' => $faker->numberBetween(0, 99),
        'last_login_time' => $faker->dateTimeThisYear(),
        'fallback_pwd' => bcrypt(str_random(10))
    ];
});

$factory->define(App\Member::class, function (Faker\Generator $faker) {
    return [
		'last_name' => $faker->lastName,
		'first_name' => $faker->firstName,
		'dob' => $faker->dateTimeThisCentury()->format('Y-m-d'),
		'sex' => rand(0,1) == 0 ? 'M' : 'F',
		'school' => $faker->city . ' High School',
		'member_email' => $faker->freeEmail,
		'parent_email' => $faker->companyEmail
    ];
});

$factory->define(App\Activity::class, function (Faker\Generator $faker) {
	$isParadeNight = rand(0,1);
    return [
		'name' => $faker->lastName,
		'type' => ['Parade','Bivouac','Activity','Annual Camp'][rand(0,3)],
		'start_date' => $faker->dateTimeThisCentury()->format('Y-m-d H:i:s'),
		'end_date' => $faker->dateTimeThisCentury()->format('Y-m-d H:i:s'),
		'desc' => $faker->catchPhrase,
		'is_parade_night' => $isParadeNight,
		'is_half_day' => $isParadeNight
    ];
});

$factory->define(App\Attendance::class, function (Faker\Generator $faker) {
    return [
		'date' => $faker->dateTimeThisCentury()->format('Y-m-d H:i:s'),
		'recorded_value' => '0',
		'is_late' => rand(0,1)
    ];
});
