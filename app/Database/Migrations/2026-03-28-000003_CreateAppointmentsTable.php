<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAppointmentsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'               => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'user_id'          => ['type' => 'INT', 'unsigned' => true],
            'service_id'       => ['type' => 'INT', 'unsigned' => true],
            'appointment_date' => ['type' => 'DATE'],
            'appointment_time' => ['type' => 'TIME'],
            'queue_number'     => ['type' => 'INT', 'null' => true],
            'status'           => ['type' => 'ENUM', 'constraint' => ['pending', 'confirmed', 'in_queue', 'serving', 'completed', 'cancelled'], 'default' => 'pending'],
            'notes'            => ['type' => 'TEXT', 'null' => true],
            'created_at'       => ['type' => 'DATETIME', 'null' => true],
            'updated_at'       => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('service_id', 'services', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('appointments');
    }

    public function down()
    {
        $this->forge->dropTable('appointments', true);
    }
}