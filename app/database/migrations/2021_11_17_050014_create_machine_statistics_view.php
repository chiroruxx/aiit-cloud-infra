<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMachineStatisticsView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('DROP VIEW IF EXISTS machine_statistics');
        DB::statement(<<<'SQL'
            CREATE VIEW machine_statistics AS
            SELECT machine_id,
                   name,
                   max_cpu_count,
                   max_memory_size,
                   memory_used,
                   max_memory_size - memory_used   AS memory_free,
                   max_storage_size,
                   storage_used,
                   max_storage_size - storage_used AS storage_free,
                   floor((memory_used / max_memory_size + storage_used / max_storage_size) * 100) AS score
            FROM (
                     SELECT id                                                          AS machine_id,
                            name,
                            max_cpu_count,
                            max_memory_size,
                            IF(memory_used_nullable IS NULL, 0, memory_used_nullable)   AS memory_used,
                            max_storage_size,
                            IF(storage_used_nullable IS NULL, 0, storage_used_nullable) AS storage_used
                     FROM machines
                              LEFT JOIN
                          (
                              SELECT machine_id,
                                     SUM(memory_size)  AS memory_used_nullable,
                                     SUM(storage_size) AS storage_used_nullable
                              FROM (SELECT machine_id, memory_size, storage_size
                                    FROM containers
                                    WHERE machine_id IS NOT NULL) AS machine_containers
                              GROUP BY machine_id
                          ) AS machine_used ON machines.id = machine_used.machine_id
                 ) AS machine_base_statistics
          SQL
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('DROP VIEW IF EXISTS machine_statistics');
    }
}
