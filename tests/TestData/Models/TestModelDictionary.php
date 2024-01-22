<?php

namespace Poruchik85\LaravelSearchProcessor\Test\TestData\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Poruchik85\LaravelSearchProcessor\Test\TestData\Database\Factories\TestModelDictionaryFactory;

/**
 * @property int $id
 * @property string $code
 */
class TestModelDictionary extends Model
{
    use HasFactory;
    
    public $table = 'test_model_dictionary';
    
    public $timestamps = false;

    protected static function newFactory(): Factory
    {
        return TestModelDictionaryFactory::new();
    }
}
