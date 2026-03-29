<?php

namespace App\Models;

use CodeIgniter\Model;

class ServiceModel extends Model
{
    protected $table         = 'services';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['name', 'description', 'duration', 'is_active'];
    protected $useTimestamps = true;

    public function getActive()
    {
        return $this->where('is_active', 1)->findAll();
    }
}