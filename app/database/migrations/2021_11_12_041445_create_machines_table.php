<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMachinesTable extends Migration
{
    public function up(): void
    {
        Schema::create('machines', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->unique();
            $table->unsignedTinyInteger('max_cpu_count');
            $table->unsignedBigInteger('max_memory_size');
            $table->unsignedBigInteger('max_storage_size');
            $table->string('ip_range');
            $table->string('queue_name');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('machines');
    }
}
