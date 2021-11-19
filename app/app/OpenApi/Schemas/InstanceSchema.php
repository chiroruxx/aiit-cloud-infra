<?php

namespace App\OpenApi\Schemas;

use GoldSpecDigital\ObjectOrientedOAS\Contracts\SchemaContract;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;
use Vyuldashev\LaravelOpenApi\Contracts\Reusable;
use Vyuldashev\LaravelOpenApi\Factories\SchemaFactory;

class InstanceSchema extends SchemaFactory implements Reusable
{
    public function build(): SchemaContract
    {
        return Schema::object('Instance')
            ->properties(
                Schema::string('name')->description('インスタンスの名前')->example('My Instance'),
                Schema::string('hash')->description('インスタンスを一意に表す名前')->example('je67liUb'),
                Schema::string('status')->description('インスタンスの状態')->example('running'),
                Schema::string('updated_at')->format(Schema::FORMAT_DATE_TIME)->description('インスタンスの更新日'),
                Schema::string('created_at')->format(Schema::FORMAT_DATE_TIME)->description('インスタンスの作成日'),
                Schema::string('image')->description('インスタンスで使用しているマシンイメージ')->example('centos:8'),
                Schema::integer('cpus')->description('インスタンスのCPU数')->example('2'),
                Schema::string('memory_size')->description('インスタンスのメモリサイズ')->example('512m'),
                Schema::string('key')->description('インスタンスの公開鍵のハッシュ')->example('hfW8gh'),
                Schema::string('ip')->description('インスタンスのIP。インスタンスが立ち上がっていない場合は null になります')->example('10.10.10.146'),
            );
    }
}
