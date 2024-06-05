<?php

namespace Poruchik85\LaravelSearchProcessor\Test\TestData\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TestModelManyToManyItemFactory extends Factory
{
    protected $model = 'Poruchik85\LaravelSearchProcessor\Test\TestData\Models\TestModelManyToManyItem';
    
    public function definition()
    {
        return [
            'name' => fake()->sentence(),
        ];
    }
}
