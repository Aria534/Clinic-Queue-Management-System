<?php

namespace App\Controllers;

use App\Models\AppointmentModel;
use App\Models\UserModel;
use App\Models\ServiceModel;

class Admin extends BaseController
{
    public function index()
    {
        $apptModel = new AppointmentModel();
        $userModel = new UserModel();

        $today = date('Y-m-d');
        $allAppts = $apptModel->getWithDetails();

        return view('admin/dashboard', [
            'page'             => 'dashboard',
            'total_today'      => count(array_filter($allAppts, fn($a) => $a['appointment_date'] === $today)),
            'pending_count'    => count(array_filter($allAppts, fn($a) => $a['status'] === 'pending')),
            'serving_count'    => count(array_filter($allAppts, fn($a) => $a['status'] === 'serving')),
            'completed_today'  => count(array_filter($allAppts, fn($a) => $a['appointment_date'] === $today && $a['status'] === 'completed')),
            'total_patients'   => $userModel->where('role', 'patient')->countAllResults(),
            'recent_appts'     => array_slice($allAppts, 0, 10),
        ]);
    }

    public function appointments()
    {
        $model = new AppointmentModel();
        return view('admin/dashboard', [
            'page'         => 'appointments',
            'appointments' => $model->getWithDetails(),
        ]);
    }

    public function updateStatus()
    {
        $id     = $this->request->getPost('id');
        $status = $this->request->getPost('status');
        $allowed = ['pending', 'confirmed', 'in_queue', 'serving', 'completed', 'cancelled'];

        if (!in_array($status, $allowed)) {
            return redirect()->back()->with('error', 'Invalid status.');
        }

        $model = new AppointmentModel();
        $model->update($id, ['status' => $status]);

        return redirect()->back()->with('success', 'Status updated.');
    }

    public function queue()
    {
        $model = new AppointmentModel();
        return view('admin/dashboard', [
            'page'  => 'queue',
            'queue' => $model->getTodayQueue(),
        ]);
    }

    public function nextQueue()
    {
        $model = new AppointmentModel();
        // Mark currently serving as completed
        $model->where('status', 'serving')->set(['status' => 'completed'])->update();

        // Get next in_queue
        $next = $model->where('appointment_date', date('Y-m-d'))
            ->whereIn('status', ['confirmed', 'in_queue'])
            ->orderBy('queue_number', 'ASC')
            ->first();

        if ($next) {
            $model->update($next['id'], ['status' => 'serving']);
        }

        return redirect()->to('/admin/queue')->with('success', $next ? 'Now serving #' . $next['queue_number'] : 'Queue is empty.');
    }

    public function users()
    {
        $model = new UserModel();
        return view('admin/dashboard', [
            'page'  => 'users',
            'users' => $model->where('role', 'patient')->findAll(),
        ]);
    }

    public function services()
    {
        $model = new ServiceModel();
        return view('admin/dashboard', [
            'page'     => 'services',
            'services' => $model->findAll(),
        ]);
    }

    public function storeService()
    {
        $rules = [
            'name'     => 'required',
            'duration' => 'required|integer',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }

        $model = new ServiceModel();
        $model->save([
            'name'        => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'duration'    => $this->request->getPost('duration'),
            'is_active'   => 1,
        ]);

        return redirect()->back()->with('success', 'Service added.');
    }

    public function toggleService(int $id)
    {
        $model   = new ServiceModel();
        $service = $model->find($id);
        $model->update($id, ['is_active' => $service['is_active'] ? 0 : 1]);
        return redirect()->back()->with('success', 'Service updated.');
    }

    public function deleteAppointment()
    {
        $id = $this->request->getPost('id');

        if (!$id) {
            return redirect()->back()->with('error', 'Invalid appointment.');
        }

        $model = new AppointmentModel();
        $appt  = $model->find($id);

        if (!$appt || $appt['status'] !== 'completed') {
            return redirect()->back()->with('error', 'Only completed appointments can be deleted.');
        }

        $model->delete($id);

        return redirect()->back()->with('success', 'Appointment deleted successfully.');
    }
}