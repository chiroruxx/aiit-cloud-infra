<?php

declare(strict_types=1);

namespace App\Http\Requests\Instance;

use App\ByteSize;
use App\Models\Instance;
use App\Models\Machine;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use LogicException;

class UpdateRequest extends BaseRequest
{
    public function rules(): array
    {
        return array_merge(
            parent::rules(),
            [
                'status' => ['string', Rule::in(Instance::getChangeableStatuses())],
            ],
        );
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if (!$this->has('status')) {
                return;
            }

            if ($validator->errors()->has('status')) {
                return;
            }

            $instance = $this->route()->parameter('instance');
            if (!$instance instanceof Instance) {
                throw new LogicException();
            }

            $status = $this->get('status');
            if (!$instance->canChangeStatusTo($status)) {
                $validator->errors()->add('status', "Can not change instance status to {$status}");
            }
        });
    }}
