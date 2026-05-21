<?php

namespace App\Models;

use CodeIgniter\Model;

class QueueTicketModel extends Model
{
    protected $table      = 'queue_tickets';
    protected $primaryKey = 'id';
    protected $useTimestamps = false;

    protected $allowedFields = [
        'queue_number',
        'patient_name',
        'service_id',
        'status',
        'date',
        'called_at',
        'completed_at',
        'created_at',
    ];

    // ----------------------------------------------------------------
    // Get next queue number for today (resets daily)
    // ----------------------------------------------------------------
    public function getNextNumber(): int
    {
        $today = date('Y-m-d');
        $last  = $this->where('date', $today)
                      ->orderBy('queue_number', 'DESC')
                      ->first();

        return $last ? $last['queue_number'] + 1 : 1;
    }

    // ----------------------------------------------------------------
    // Today's full queue with service name joined
    // ----------------------------------------------------------------
    public function getTodayQueue(): array
    {
        return $this->select('queue_tickets.*, services.name as service_name')
                    ->join('services', 'services.id = queue_tickets.service_id', 'left')
                    ->where('queue_tickets.date', date('Y-m-d'))
                    ->whereNotIn('queue_tickets.status', ['completed', 'skipped'])
                    ->orderBy('queue_tickets.queue_number', 'ASC')
                    ->findAll();
    }

    // ----------------------------------------------------------------
    // Currently serving ticket today
    // ----------------------------------------------------------------
    public function getCurrentlyServing(): ?array
    {
        return $this->select('queue_tickets.*, services.name as service_name')
                    ->join('services', 'services.id = queue_tickets.service_id', 'left')
                    ->where('queue_tickets.date', date('Y-m-d'))
                    ->where('queue_tickets.status', 'serving')
                    ->first();
    }

    // ----------------------------------------------------------------
    // Next waiting ticket today
    // ----------------------------------------------------------------
    public function getNextWaiting(): ?array
    {
        return $this->where('date', date('Y-m-d'))
                    ->where('status', 'waiting')
                    ->orderBy('queue_number', 'ASC')
                    ->first();
    }

    // ----------------------------------------------------------------
    // Stats for today
    // ----------------------------------------------------------------
    public function getTodayStats(): array
    {
        $today     = date('Y-m-d');
        $waiting   = $this->where('date', $today)->where('status', 'waiting')->countAllResults();
        $serving   = $this->where('date', $today)->where('status', 'serving')->countAllResults();
        $completed = $this->where('date', $today)->where('status', 'completed')->countAllResults();
        $total     = $this->where('date', $today)->countAllResults();

        // Per-service breakdown
        $db         = \Config\Database::connect();
        $by_service = $db->query("
            SELECT
                s.name AS service_name,
                SUM(qt.status = 'waiting')   AS waiting,
                SUM(qt.status = 'serving')   AS serving,
                SUM(qt.status = 'completed') AS completed
            FROM queue_tickets qt
            JOIN services s ON s.id = qt.service_id
            WHERE qt.date = ?
            GROUP BY s.id, s.name
            ORDER BY s.name
        ", [$today])->getResultArray();

        return compact('waiting', 'serving', 'completed', 'total', 'by_service');
    }
}