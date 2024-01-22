<?php

namespace Poruchik85\LaravelSearchProcessor\Test\TestData\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TestModelItemFactory extends Factory
{
    protected $model = 'Poruchik85\LaravelSearchProcessor\Test\TestData\Models\TestModelItem';
    
    public function definition()
    {
        return [
            'name' => fake()->sentence(),
        ];
    }
}
