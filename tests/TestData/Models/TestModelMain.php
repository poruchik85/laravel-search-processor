<?php

namespace Poruchik85\LaravelSearchProcessor\Test\TestData\Models;

use DateTimeImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Poruchik85\LaravelSearchProcessor\Test\TestData\Database\Factories\TestModelMainFactory;

/**
 * @property int $id
 * @property string $string_field
 * @property int $int_field
 * @property float $float_field
 * @property bool $bool_field
 * @property DateTimeImmutable $time_field
 * @property int $test_model_dictionary_id
 * 
 * @property TestModelDictionary $testModelDictionary
 * @property Collection $testModelItems
 */
class TestModelMain extends Model
{
    use HasFactory;
    
    public $table = 'test_model_main';

    protected $casts = [
        'time_field' => 'timestamp',
    ];
    
    protected static function newFactory(): Factory
    {
        return TestModelMainFactory::new();
    }

    /**
     * @return BelongsTo
     */
    public function testModelDictionary(): BelongsTo
    {
        return $this->belongsTo(TestModelDictionary::class);
    }

    /**
     * @return HasMany
     */
    public function testModelItems(): HasMany
    {
        return $this->hasMany(TestModelItem::class);
    }
}
