<?php

namespace Poruchik85\LaravelSearchProcessor\Test\TestData\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TestModelDictionaryFactory extends Factory
{
    protected $model = 'Poruchik85\LaravelSearchProcessor\Test\TestData\Models\TestModelDictionary';
    
    public function definition()
    {
        return [
            'code' => fake()->word(),
        ];
    }
}
