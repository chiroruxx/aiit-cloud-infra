<?php

declare(strict_types=1);

namespace App\OpenApi\Parameters;

use GoldSpecDigital\ObjectOrientedOAS\Objects\Parameter;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;
use Vyuldashev\LaravelOpenApi\Factories\ParametersFactory;

class InstanceUpdateParameters extends ParametersFactory
{
    public function build(): array
    {
        return [
            Parameter::query()
                ->name('name')
                ->description('インスタンスの名前')
                ->required(false)
                ->example('My Instance')
                ->schema(Schema::string()),
            Parameter::query()
                ->name('status')
                ->description('インスタンスのステータス')
                ->required(false)
                ->example('halted')
                ->schema(Schema::string()->pattern('^[halted|running]$')),
        ];
    }
}
