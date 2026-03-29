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
            return redirect()->to('/admin/queue')->with('success', 'Queue is empty for today.');
        }

        $model->update($next['id'], [
            'status'     => 'serving',
            'started_at' => date('Y-m-d H:i:s'),
        ]);
        $this->logQueue($db, $next['id'], 'serving', $adminId);

        // STEP 3: Email — It's your turn
        $this->sendEmail($next['patient_email'], $next['patient_name'], $next['queue_number'], 'called');

        // STEP 4: Email — You're next (to the one after)
        $upcoming = $model
            ->where('appointment_date', $today)
            ->whereIn('status', ['confirmed', 'in_queue', 'pending'])
            ->orderBy('queue_number', 'ASC')
            ->first();

        if ($upcoming) {
            $this->sendEmail($upcoming['patient_email'], $upcoming['patient_name'], $upcoming['queue_number'], 'next');
        }

        return redirect()->to('/admin/queue')
            ->with('success', 'Now Serving #' . $next['queue_number'] . ' — ' . $next['patient_name']);
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

    private function sendEmail(string $to, string $name, string $queueNum, string $type): void
    {
        try {
            $email = \Config\Services::email();

            $subjects = [
                'queued' => "You're in the queue! — QueueMed",
                'called' => "It's Your Turn! Queue #{$queueNum} — QueueMed",
                'next'   => "You're Next! Queue #{$queueNum} — QueueMed",
                'done'   => "Consultation Complete — QueueMed",
            ];

            $messages = [
                'queued' => "Hi {$name},<br><br>You have been added to the queue as <strong>#{$queueNum}</strong>. We will notify you when it's your turn.<br><br>Thank you for choosing QueueMed.",
                'called' => "Hi {$name},<br><br>It's your turn! You are now being called as <strong>Queue #{$queueNum}</strong>.<br><br>Please proceed to the consultation room now.",
                'next'   => "Hi {$name},<br><br>You are <strong>next in line</strong> as Queue #{$queueNum}. Please be ready and stay nearby.",
                'done'   => "Hi {$name},<br><br>Your consultation (Queue #{$queueNum}) has been completed. Thank you for visiting QueueMed!",
            ];

            $email->setFrom('aeravrl@gmail.com', 'QueueMed');
            $email->setTo($to);
            $email->setMailType('html');
            $email->setSubject($subjects[$type] ?? 'QueueMed Notification');
            $email->setMessage($messages[$type] ?? '');
            $email->send();
        } catch (\Throwable $e) {
            log_message('error', 'QueueMed email failed: ' . $e->getMessage());
        }
    }
}