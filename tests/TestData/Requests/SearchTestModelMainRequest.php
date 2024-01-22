<?php

namespace Poruchik85\LaravelSearchProcessor\Test\TestData\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @position query
 */
class SearchTestModelMainRequest extends FormRequest
{
    /**
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array>
     */
    public function rules(): array
    {
        return [
            'string_field' => ['string'],
            'int_field' => ['integer'],
            'float_field' => ['number'],
            'bool_field' => ['bool'],
            'time_field' => ['array', 'min' => 1, 'max' => 2],
            'time_field.*' => ['string', 'min' => 10, 'max' => 20],
        ];
    }
}
