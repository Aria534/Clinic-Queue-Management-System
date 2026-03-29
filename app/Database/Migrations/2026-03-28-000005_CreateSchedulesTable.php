<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSchedulesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'day_of_week' => ['type' => 'TINYINT', 'constraint' => 1],
            'open_time'   => ['type' => 'TIME'],
            'close_time'  => ['type' => 'TIME'],
            'is_open'     => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('schedules');
    }

    public function down()
    {
        $this->forge->dropTable('schedules', true);
    }
}