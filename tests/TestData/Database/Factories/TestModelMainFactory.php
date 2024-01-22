<?php

namespace Poruchik85\LaravelSearchProcessor\Test\TestData\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Poruchik85\LaravelSearchProcessor\Test\TestData\Models\TestModelDictionary;

class TestModelMainFactory extends Factory
{
    protected $model = 'Poruchik85\LaravelSearchProcessor\Test\TestData\Models\TestModelMain';
    
    public function definition()
    {
        $dictionaries = TestModelDictionary::all();
        return [
            'string_field' => fake()->name(),
            'int_field' => fake()->randomNumber(),
            'float_field' => fake()->randomFloat(),
            'bool_field' => fake()->boolean,
            'time_field' => fake()->dateTimeBetween('-30 days'),
            'test_model_dictionary_id' => $dictionaries->random(),
        ];
    }
}
