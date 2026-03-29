<?php

namespace App\Controllers;

use App\Models\AppointmentModel;
use App\Models\ServiceModel;

class Appointment extends BaseController
{
    public function book()
    {
        $serviceModel = new ServiceModel();
        return view('User/book', [
            'services' => $serviceModel->getActive(),
        ]);
    }

    public function store()
    {
        $rules = [
            'service_id' => 'required|integer',
            'appt_date'  => 'required|valid_date',
            'appt_time'  => 'required',
            'notes'      => 'permit_empty|max_length[500]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $date  = $this->request->getPost('appt_date');
        $model = new AppointmentModel();

        if (strtotime($date) < strtotime(date('Y-m-d'))) {
            return redirect()->back()->withInput()->with('error', 'Cannot book a past date.');
        }

        $queueNum = $model->getNextQueueNumber($date);

        $model->save([
            'user_id'          => session()->get('user_id'),
            'service_id'       => $this->request->getPost('service_id'),
            'appointment_date' => $date,
            'appointment_time' => $this->request->getPost('appt_time'),
            'queue_number'     => $queueNum,
            'status'           => 'pending',
            'notes'            => $this->request->getPost('notes'),
        ]);

        return redirect()->to('/patient/dashboard')->with('success', 'Appointment booked! Queue #' . $queueNum);
    }

    public function index()
    {
        $model = new AppointmentModel();
        return view('User/appointments', [
            'appointments' => $model->getByUser(session()->get('user_id')),
        ]);
    }

    public function cancel(int $id)
    {
        $model       = new AppointmentModel();
        $appointment = $model->find($id);

        if (!$appointment || $appointment['user_id'] != session()->get('user_id')) {
            return redirect()->to('/patient/dashboard')->with('error', 'Appointment not found.');
        }

        if (in_array($appointment['status'], ['completed', 'serving'])) {
            return redirect()->to('/patient/dashboard')->with('error', 'Cannot cancel this appointment.');
        }

        $model->update($id, ['status' => 'cancelled']);
        return redirect()->to('/patient/dashboard')->with('success', 'Appointment cancelled.');
    }
}