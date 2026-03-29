<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ClinicSeeder extends Seeder
{
    public function run()
    {
        // Services only (admin naa na)
        $services = [
            ['name' => 'General Consultation', 'description' => 'General check-up with a doctor',    'duration' => 30],
            ['name' => 'Dental Check-up',       'description' => 'Oral health examination',           'duration' => 45],
            ['name' => 'Blood Test',             'description' => 'Laboratory blood work',            'duration' => 15],
            ['name' => 'X-Ray',                  'description' => 'Radiological imaging',             'duration' => 20],
            ['name' => 'Vaccination',            'description' => 'Immunization services',            'duration' => 15],
            ['name' => 'Prenatal Check-up',      'description' => 'Maternal and fetal health check', 'duration' => 30],
            ['name' => 'Pediatric Consultation', 'description' => 'Child health consultation',        'duration' => 30],
        ];

        foreach ($services as $s) {
            $this->db->table('services')->insert(array_merge($s, [
                'is_active'  => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]));
        }
    }
}