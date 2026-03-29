<?php

namespace App\Models;

use CodeIgniter\Model;

class AppointmentModel extends Model
{
    protected $table      = 'appointments';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'user_id', 'service_id', 'appointment_date',
        'appointment_time', 'queue_number', 'status',
        'notes', 'started_at', 'finished_at',
        'patient_name', 'patient_email'
    ];
    protected $useTimestamps = true;

    public function getWithDetails(int $id = null)
    {
        $builder = $this->db->table('appointments a')
            ->select('
                a.*,
                COALESCE(a.patient_name, u.name) as patient_name,
                COALESCE(a.patient_email, u.email) as email,
                s.name as service_name,
                s.duration
            ')
            ->join('services s', 's.id = a.service_id')
            ->join('users u', 'u.id = a.user_id', 'left')
            ->orderBy('a.appointment_date', 'ASC')
            ->orderBy('a.queue_number', 'ASC');

        if ($id) {
            return $builder->where('a.id', $id)->get()->getRowArray();
        }

        return $builder->get()->getResultArray();
    }

    public function getTodayQueue()
    {
        return $this->db->table('appointments a')
            ->select('
                a.*,
                COALESCE(a.patient_name, u.name) as patient_name,
                COALESCE(a.patient_email, u.email) as email,
                s.name as service_name
            ')
            ->join('services s', 's.id = a.service_id')
            ->join('users u', 'u.id = a.user_id', 'left')
            ->where('a.appointment_date', date('Y-m-d'))
            ->whereIn('a.status', ['confirmed', 'in_queue', 'pending', 'serving'])
            ->orderBy('a.queue_number', 'ASC')
            ->get()->getResultArray();
    }

    public function getNextQueueNumber(string $date): int
    {
        $result = $this->db->table('appointments')
            ->selectMax('queue_number')
            ->where('appointment_date', $date)
            ->get()->getRowArray();

        return ($result['queue_number'] ?? 0) + 1;
    }
}