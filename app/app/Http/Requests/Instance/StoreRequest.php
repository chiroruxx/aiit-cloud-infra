<?php

declare(strict_types=1);

namespace App\Http\Requests\Instance;

use App\ByteSize;
use App\Models\Machine;
use Illuminate\Validation\Validator;

class StoreRequest extends BaseRequest
{
    public function rules(): array
    {
        $maxCpuCount = Machine::max('max_cpu_count');

        return array_merge(
            parent::rules(),
            [
                'image' => ['required', 'string', 'max:255', 'exists:images,name'],
                'key' => ['required', 'string', 'max:255', 'exists:public_keys,hash'],
                'cpus' => ['integer', 'min:1', "max:{$maxCpuCount}"],
                'memory' => ['string'],
                'storage' => ['string'],
            ]
        );
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $this->validateByteSize(validator: $validator, key: 'memory', min: '4m');
            $this->validateByteSize(validator: $validator, key: 'storage', min: '100g');
        });
    }

    private function validateByteSize(Validator $validator, string $key, string $min): void
    {
        if ($validator->errors()->has($key)) {
            return;
        }

        $input = $this->input($key);
        if ($input === null) {
            return;
        }

        if (!ByteSize::validate($input)) {
            $validator->errors()->add($key, 'Given value is invalid.');
            return;
        }

        $input = ByteSize::createWithUnit($input);
        if ($input->lessThan($min)) {
            $validator->errors()->add(
                $key,
                ucfirst("{$key} size should be greater than or equals {$min}")
            );
        }
    }
}
