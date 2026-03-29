<?php

namespace App\Controllers;

use App\Models\AppointmentModel;
use App\Models\ServiceModel;

class Home extends BaseController
{
    // Show booking form
    public function index()
    {
        $serviceModel = new ServiceModel();
        return view('User/register', [
            'services' => $serviceModel->where('is_active', 1)->findAll(),
        ]);
    }

    // Handle form submit — create guest booking
    public function book()
    {
        $rules = [
            'patient_name'  => 'required|min_length[2]',
            'patient_email' => 'required|valid_email',
            'service_id'    => 'required|integer',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('error', implode(' ', $this->validator->getErrors()));
        }

        $model       = new AppointmentModel();
        $today       = date('Y-m-d');
        $queueNumber = $model->getNextQueueNumber($today);

        $id = $model->insert([
            'user_id'          => null,
            'patient_name'     => $this->request->getPost('patient_name'),
            'patient_email'    => $this->request->getPost('patient_email'),
            'service_id'       => $this->request->getPost('service_id'),
            'appointment_date' => $today,
            'appointment_time' => date('H:i:s'),
            'queue_number'     => $queueNumber,
            'status'           => 'pending',
            'notes'            => $this->request->getPost('notes'),
        ]);

        // Send confirmation email with ticket link
        $this->sendConfirmationEmail(
            $this->request->getPost('patient_email'),
            $this->request->getPost('patient_name'),
            $queueNumber,
            $id
        );

        return redirect()->to('/queue/status/' . $id);
    }

    private function sendConfirmationEmail(string $to, string $name, int $queueNum, int $appointmentId): void
    {
        try {
            $email      = \Config\Services::email();
            $ticketLink = base_url('queue/status/' . $appointmentId);

            $email->setFrom('aeravrl@gmail.com', 'QueueMed');
            $email->setTo($to);
            $email->setMailType('html');
            $email->setSubject("Your Queue Number #{$queueNum} — QueueMed");
            $email->setMessage("
                Hi {$name},<br><br>
                You have successfully joined the queue!<br><br>
                <strong>Queue Number: #{$queueNum}</strong><br><br>
                Track your queue status here:<br>
                <a href='{$ticketLink}'>{$ticketLink}</a><br><br>
                Thank you for choosing QueueMed.
            ");

            if (!$email->send()) {
                log_message('error', 'Email send failed: ' . $email->printDebugger(['headers']));
            }

        } catch (\Throwable $e) {
            log_message('error', 'Confirmation email failed: ' . $e->getMessage());
        }
    }
    public function testEmail()
{
    $email = \Config\Services::email();
    $email->setFrom('aeravrl@gmail.com', 'QueueMed');
    $email->setTo('aeravrl@gmail.com');
    $email->setMailType('html');
    $email->setSubject('Test Email - QueueMed');
    $email->setMessage('This is a test email from QueueMed.');

    if ($email->send()) {
        echo 'Email sent successfully!';
    } else {
        echo '<pre>' . $email->printDebugger() . '</pre>';
    }
}
}