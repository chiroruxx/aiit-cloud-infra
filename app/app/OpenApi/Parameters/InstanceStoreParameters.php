<?php

declare(strict_types=1);

namespace App\OpenApi\Parameters;

use GoldSpecDigital\ObjectOrientedOAS\Objects\Parameter;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;
use Vyuldashev\LaravelOpenApi\Factories\ParametersFactory;

class InstanceStoreParameters extends ParametersFactory
{
    public function build(): array
    {
        return [
            Parameter::query()
                ->name('name')
                ->description('インスタンスの名前')
                ->required(false)
                ->example('My Instance')
                ->schema(Schema::string()->maxLength(255)),
            Parameter::query()
                ->name('image')
                ->description('マシンイメージ')
                ->required(true)
                ->example('centos:8')
                ->schema(Schema::string()),
            Parameter::query()
                ->name('key')
                ->description('公開鍵のハッシュ')
                ->required(true)
                ->example('hfW8gh')
                ->schema(Schema::string()),
            Parameter::query()
                ->name('cpus')
                ->description('インスタンスのCPU数')
                ->required(false)
                ->example(2)
                ->schema(Schema::integer()->maximum(2)),
            Parameter::query()
                ->name('memory')
                ->description('インスタンスのメモリサイズ')
                ->required(false)
                ->example('512m')
                ->schema(Schema::string()->default('4m')),
            Parameter::query()
                ->name('storage')
                ->description('インスタンスのストレージサイズ')
                ->required(false)
                ->example('120g')
                ->schema(Schema::string()->default('100g')),
        ];
    }
}
