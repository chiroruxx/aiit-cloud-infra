<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\ByteSize;
use App\Models\Machine;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class InstanceRequest extends FormRequest
{
    public function rules(): array
    {
        $maxCpuCount = Machine::max('max_cpu_count');

        return [
            'name' => ['string', 'max:255'],
            'key' => ['required', 'string', 'max:255', 'exists:public_keys,hash'],
            'cpus' => ['integer', 'min:1', "max:{$maxCpuCount}"],
            'memory' => ['string'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($validator->errors()->has('memory')) {
                return;
            }

            $memory = $this->input('memory');
            if ($memory === null) {
                return;
            }

            if (!ByteSize::validate($memory)) {
                $validator->errors()->add('memory', 'Given value is invalid.');
                return;
            }

            $memory = ByteSize::createWithUnit($memory);
            if ($memory->lessThan('4m')) {
                $validator->errors()->add('memory', 'Memory size should be greater than or equals 4m');
            }
        });
    }
}
