<?php

namespace App\Controllers;

use App\Models\AppointmentModel;
use App\Models\ServiceModel;

class Patient extends BaseController
{
    public function dashboard()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to(base_url('login'));
        }

        $model    = new AppointmentModel();
        $services = new ServiceModel();
        $userId   = session()->get('user_id');

        $appointments = $model
            ->select('appointments.*, services.name as service_name')
            ->join('services', 'services.id = appointments.service_id', 'left')
            ->where('appointments.user_id', $userId)
            ->orderBy('appointments.queue_number', 'DESC')
            ->findAll();

        $stats = ['pending' => 0, 'confirmed' => 0, 'completed' => 0];
        foreach ($appointments as $a) {
            if (isset($stats[$a['status']])) {
                $stats[$a['status']]++;
            }
        }

        return view('Patient/user', [
            'appointments' => $appointments,
            'stats'        => $stats,
            'services'     => $services->where('is_active', 1)->findAll(),
        ]);
    }

    public function book()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to(base_url('login'));
        }

        $rules = [
            'name'       => 'required|min_length[2]',
            'contact'    => 'required|min_length[7]',
            'service_id' => 'required|integer',
            'appt_date'  => 'required|valid_date',
            'appt_time'  => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $model       = new AppointmentModel();
        $date        = $this->request->getPost('appt_date');
        $queueNumber = $model->getNextQueueNumber($date);

        $model->insert([
            'user_id'          => session()->get('user_id'),
            'patient_name'     => $this->request->getPost('name'),
            'patient_contact'  => $this->request->getPost('contact'),
            'service_id'       => $this->request->getPost('service_id'),
            'appointment_date' => $date,
            'appointment_time' => $this->request->getPost('appt_time'),
            'queue_number'     => $queueNumber,
            'status'           => 'pending',
        ]);

        return redirect()->to(base_url('patient/dashboard'))
            ->with('success', 'Appointment booked! Your queue number is #' . $queueNumber);
    }

    public function cancel(int $id)
    {
        if (!session()->get('logged_in')) {
            return redirect()->to(base_url('login'));
        }

        $model = new AppointmentModel();
        $appt  = $model->find($id);

        if (!$appt || $appt['user_id'] !== session()->get('user_id')) {
            return redirect()->to(base_url('patient/dashboard'))
                ->with('error', 'Appointment not found.');
        }

        if (in_array($appt['status'], ['completed', 'cancelled', 'serving'])) {
            return redirect()->to(base_url('patient/dashboard'))
                ->with('error', 'Cannot cancel this appointment.');
        }

        $model->update($id, ['status' => 'cancelled']);

        return redirect()->to(base_url('patient/dashboard'))
            ->with('success', 'Appointment cancelled successfully.');
    }
}