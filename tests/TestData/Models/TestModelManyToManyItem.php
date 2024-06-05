<?php

namespace Poruchik85\LaravelSearchProcessor\Test\TestData\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Poruchik85\LaravelSearchProcessor\Test\TestData\Database\Factories\TestModelManyToManyItemFactory;

/**
 * @property int $id
 * @property string $code
 */
class TestModelManyToManyItem extends Model
{
    use HasFactory;
    
    public $table = 'test_model_many_to_many_item';
    
    public $timestamps = false;

    protected static function newFactory(): Factory
    {
        return TestModelManyToManyItemFactory::new();
    }

    public function testModelMains(): BelongsToMany
    {
        return $this->belongsToMany(TestModelMain::class);
    }
}
