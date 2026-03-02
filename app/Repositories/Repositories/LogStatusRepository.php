<?php

namespace App\Repositories\Repositories;

use App\Models\LogStatus;
use App\Repositories\Interface\LogStatusInterface;

class LogStatusRepository implements LogStatusInterface
{
    protected LogStatus $model;

    public function __construct(LogStatus $model)
    {
        $this->model = $model;
    }

    /**
     * Ambil semua data dengan pagination (limit & offset)
     */
    public function getAll($limit, $offset, $modul, $id)
    {
        return $this->model
            ->whereNull('deleted_at')
            ->where('modul_name', 'LIKE', "%$modul%")
            ->where('data_id', $id)
            ->orderByDesc('created_at')
            ->limit($limit)
            ->offset($offset)
            ->get();
    }

    /**
     * Create data baru
     */
    public function create(array $data, $modul, $id)
    {
        $data['modul_name'] = $modul;
        $data['data_id'] = $id;
        return $this->model->create($data);
    }
}
