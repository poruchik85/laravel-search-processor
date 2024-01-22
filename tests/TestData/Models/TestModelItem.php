<?php

namespace Poruchik85\LaravelSearchProcessor\Test\TestData\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Poruchik85\LaravelSearchProcessor\Test\TestData\Database\Factories\TestModelItemFactory;

/**
 * @property int $id
 * @property string $name
 * @property int $test_model_main_id
 * 
 * @property TestModelMain $testModelMain
 */
class TestModelItem extends Model
{
    use HasFactory;
    
    public $table = 'test_model_item';
    
    public $timestamps = false;

    protected static function newFactory(): Factory
    {
        return TestModelItemFactory::new();
    }
    
    /**
     * @return BelongsTo
     */
    public function testModelMain(): BelongsTo
    {
        return $this->belongsTo(TestModelMain::class);
    }
}
