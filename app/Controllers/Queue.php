<?php

namespace App\Controllers;

use App\Models\AppointmentModel;

class Queue extends BaseController
{
    public function status(int $appointmentId)
    {
        $model       = new AppointmentModel();
        $appointment = $model->getWithDetails($appointmentId);

        if (!$appointment) {
            return redirect()->to('/')->with('error', 'Queue ticket not found.');
        }

        $db    = \Config\Database::connect();
        $ahead = $db->table('appointments')
            ->where('appointment_date', $appointment['appointment_date'])
            ->where('queue_number <', $appointment['queue_number'])
            ->whereIn('status', ['confirmed', 'in_queue', 'pending', 'serving'])
            ->countAllResults();

        return view('User/queue_ticket', [
            'appointment' => $appointment,
            'ahead'       => $ahead,
        ]);
    }

    public function next()
    {
        $model   = new AppointmentModel();
        $db      = \Config\Database::connect();
        $adminId = session()->get('user_id');
        $today   = date('Y-m-d');

        // STEP 1: Finish currently serving
        $current = $model
            ->where('appointment_date', $today)
            ->where('status', 'serving')
            ->first();

        if ($current) {
            $model->update($current['id'], [
                'status'      => 'completed',
                'finished_at' => date('Y-m-d H:i:s'),
            ]);
            $this->logQueue($db, $current['id'], 'completed', $adminId);
        }

        // STEP 2: Get next waiting patient
        $next = $model
            ->where('appointment_date', $today)
            ->whereIn('status', ['confirmed', 'in_queue', 'pending'])
            ->orderBy('queue_number', 'ASC')
            ->first();

        if (!$next) {
            return redirect()->to('/admin/queue')
                ->with('success', 'Queue is empty for today.');
        }

        $model->update($next['id'], [
            'status'     => 'serving',
            'started_at' => date('Y-m-d H:i:s'),
        ]);
        $this->logQueue($db, $next['id'], 'serving', $adminId);

        return redirect()->to('/admin/queue')
            ->with('success', 'Now Serving #' . $next['queue_number'] . ' — ' . $next['patient_name']);
    }

    public function checkQueue()
    {
        $q     = strtoupper(trim($this->request->getGet('q')));
        $model = new AppointmentModel();
        $db    = \Config\Database::connect();
        $today = date('Y-m-d');

        $num = (int) preg_replace('/\D/', '', $q);

        if (!$num) {
            return $this->response->setJSON(['found' => false]);
        }

        $appt = $model
            ->select('appointments.*, services.name as service_name')
            ->join('services', 'services.id = appointments.service_id', 'left')
            ->where('appointments.appointment_date', $today)
            ->where('appointments.queue_number', $num)
            ->first();

        if (!$appt) {
            return $this->response->setJSON(['found' => false]);
        }

        $ahead = $db->table('appointments')
            ->where('appointment_date', $today)
            ->where('queue_number <', $num)
            ->whereIn('status', ['confirmed', 'in_queue', 'pending', 'serving'])
            ->countAllResults();

        return $this->response->setJSON([
            'found'        => true,
            'queue_number' => $appt['queue_number'],
            'status'       => $appt['status'],
            'status_label' => ucfirst(str_replace('_', ' ', $appt['status'])),
            'service'      => $appt['service_name'] ?? null,
            'ahead'        => $ahead,
        ]);
    }

    private function logQueue($db, int $appointmentId, string $action, ?int $actedBy): void
    {
        $db->table('queue_logs')->insert([
            'appointment_id' => $appointmentId,
            'action'         => $action,
            'acted_by'       => $actedBy,
            'created_at'     => date('Y-m-d H:i:s'),
        ]);
    }
}