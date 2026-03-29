<?php

namespace App\Controllers;

use App\Models\AppointmentModel;

class API extends BaseController
{
    public function queueStatus(int $appointmentId)
    {
        $model       = new AppointmentModel();
        $appointment = $model->getWithDetails($appointmentId);

        if (!$appointment) {
            return $this->response->setJSON([
                'status' => 'not_found',
                'ahead'  => 0,
            ])->setStatusCode(404);
        }

        $db = \Config\Database::connect();

        $ahead = $db->table('appointments')
            ->where('appointment_date', $appointment['appointment_date'])
            ->where('queue_number <', $appointment['queue_number'])
            ->whereIn('status', ['confirmed', 'in_queue', 'pending', 'serving'])
            ->countAllResults();

        $serving = $db->table('appointments')
            ->select('queue_number')
            ->where('appointment_date', $appointment['appointment_date'])
            ->where('status', 'serving')
            ->get()->getRowArray();

        return $this->response->setJSON([
            'status'       => $appointment['status'],
            'ahead'        => $ahead,
            'queue_number' => $appointment['queue_number'],
            'now_serving'  => $serving['queue_number'] ?? null,
            'patient_name' => $appointment['patient_name'],
            'service_name' => $appointment['service_name'],
        ]);
    }

    public function queueLive()
    {
        $db    = \Config\Database::connect();
        $today = date('Y-m-d');

        $serving = $db->table('appointments a')
            ->select('a.queue_number, u.name as patient_name, s.name as service_name, a.started_at')
            ->join('users u', 'u.id = a.user_id', 'left')
            ->join('services s', 's.id = a.service_id', 'left')
            ->where('a.appointment_date', $today)
            ->where('a.status', 'serving')
            ->get()->getRowArray();

        $waiting = $db->table('appointments')
            ->where('appointment_date', $today)
            ->whereIn('status', ['confirmed', 'in_queue', 'pending'])
            ->countAllResults();

        $completed = $db->table('appointments')
            ->where('appointment_date', $today)
            ->where('status', 'completed')
            ->countAllResults();

        return $this->response->setJSON([
            'serving'   => $serving,
            'waiting'   => $waiting,
            'completed' => $completed,
            'timestamp' => date('H:i:s'),
        ]);
    }
}