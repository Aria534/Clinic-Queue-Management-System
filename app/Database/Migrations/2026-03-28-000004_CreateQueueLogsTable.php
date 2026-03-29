<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateQueueLogsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'             => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'appointment_id' => ['type' => 'INT', 'unsigned' => true],
            'action'         => ['type' => 'ENUM', 'constraint' => ['called', 'serving', 'completed', 'skipped']],
            'acted_by'       => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'created_at'     => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('appointment_id', 'appointments', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('queue_logs');
    }

    public function down()
    {
        $this->forge->dropTable('queue_logs', true);
    }
}