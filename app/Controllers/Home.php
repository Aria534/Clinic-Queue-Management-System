<?php

namespace App\Controllers;

use App\Models\AppointmentModel;
use App\Models\ServiceModel;

class Home extends BaseController
{
    public function index()
    {
        $serviceModel = new ServiceModel();
        return view('User/register', [
            'services' => $serviceModel->where('is_active', 1)->findAll(),
        ]);
    }

    public function book()
    {
        $rules = [
            'patient_name' => 'required|min_length[2]',
            'service_id'   => 'required|integer',
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
            'patient_email'    => null,
            'service_id'       => $this->request->getPost('service_id'),
            'appointment_date' => $today,
            'appointment_time' => date('H:i:s'),
            'queue_number'     => $queueNumber,
            'status'           => 'pending',
            'notes'            => $this->request->getPost('notes'),
        ]);

        return redirect()->to('/queue/status/' . $id);
    }
}