<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddQueueTimestampsToAppointments extends Migration
{
    public function up()
    {
        $this->forge->addColumn('appointments', [
            'started_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
                'default' => null,
                'after'   => 'status',
            ],
        ]);

        $this->forge->addColumn('appointments', [
            'finished_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
                'default' => null,
                'after'   => 'started_at',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('appointments', 'started_at');
        $this->forge->dropColumn('appointments', 'finished_at');
    }
}