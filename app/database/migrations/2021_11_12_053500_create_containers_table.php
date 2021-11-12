<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContainersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('containers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('instance_id')->unique();
            $table->unsignedBigInteger('public_key_id');
            $table->unsignedBigInteger('machine_id')->nullable();
            $table->unsignedTinyInteger('cpus');
            $table->unsignedBigInteger('memory_size');
            $table->string('ip')->nullable();
            $table->string('container_id')->nullable();
            $table->timestamps();

            $table->unique(['machine_id', 'container_id']);

            $table->foreign('instance_id')
                ->references('id')
                ->on('instances')
                ->restrictOnDelete()
                ->cascadeOnUpdate();

            $table->foreign('public_key_id')
                ->references('id')
                ->on('public_keys')
                ->restrictOnDelete()
                ->cascadeOnUpdate();

            $table->foreign('machine_id')
                ->references('id')
                ->on('machines')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('containers');
    }
}
