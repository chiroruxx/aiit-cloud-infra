<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Machine;
use Illuminate\Foundation\Http\FormRequest;

class InstanceRequest extends FormRequest
{
    public function rules(): array
    {
        $maxCpuCount = Machine::max('max_cpu_count');

        return [
            'name' => ['string', 'max:255'],
            'key' => ['required', 'string', 'max:255', 'exists:public_keys,hash'],
            'cpus' => ['integer', 'min:1', "max:{$maxCpuCount}"],
        ];
    }
}
