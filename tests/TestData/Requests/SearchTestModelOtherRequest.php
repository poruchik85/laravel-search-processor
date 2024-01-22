<?php

namespace Poruchik85\LaravelSearchProcessor\Test\TestData\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @position query
 */
class SearchTestModelOtherRequest extends FormRequest
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
            
        ];
    }
}
