<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('test_model_main', function (Blueprint $table) {
            $table->id();
            $table->string('string_field');
            $table->integer('int_field');
            $table->integer('test_model_dictionary_id');
            $table->float('float_field');
            $table->boolean('bool_field');
            $table->timestampTz('time_field');
            $table->timestampsTz();
        });

        Schema::create('test_model_dictionary', function (Blueprint $table) {
            $table->id();
            $table->string('code');
        });

        Schema::create('test_model_item', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('test_model_main_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('test_model_item');
        Schema::dropIfExists('test_model_dictionary');
        Schema::dropIfExists('test_model_main');
    }
};
