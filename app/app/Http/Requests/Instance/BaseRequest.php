<?php

declare(strict_types=1);

namespace App\Http\Requests\Instance;

use Illuminate\Foundation\Http\FormRequest;

abstract class BaseRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['string', 'nullable', 'max:255'],
        ];
    }
}
