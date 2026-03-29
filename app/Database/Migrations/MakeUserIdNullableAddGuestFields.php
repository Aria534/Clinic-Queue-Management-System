<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MakeUserIdNullableAddGuestFields extends Migration
{
    public function up()
    {
        // Make user_id nullable (guest booking)
        $this->forge->modifyColumn('appointments', [
            'user_id' => [
                'type'       => 'INT',
                'unsigned'   => true,
                'null'       => true,
                'default'    => null,
            ],
        ]);

        // Add patient_name directly on appointments
        $this->forge->addColumn('appointments', [
            'patient_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'default'    => null,
                'after'      => 'user_id',
            ],
        ]);

        // Add patient_email directly on appointments
        $this->forge->addColumn('appointments', [
            'patient_email' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
                'default'    => null,
                'after'      => 'patient_name',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('appointments', 'patient_name');
        $this->forge->dropColumn('appointments', 'patient_email');
        $this->forge->modifyColumn('appointments', [
            'user_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => false,
            ],
        ]);
    }
}