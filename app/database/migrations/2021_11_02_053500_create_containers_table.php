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
            $table->unsignedTinyInteger('cpus');
            $table->string('memory_size');
            $table->string('vm')->nullable();
            $table->string('container_id')->nullable();
            $table->timestamps();

            $table->unique(['vm', 'container_id']);

            $table->foreign('instance_id')
                ->references('id')
                ->on('instances')
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
