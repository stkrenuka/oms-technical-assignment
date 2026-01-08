<?php

namespace App\Repositories;
use App\Models\User;

class userRepository
{
     protected User $model;
      public function __construct(User $model)
    {
        $this->model = $model;
    }
    public function all()
    {
        return $this->model->all();
    }
    public function find($id)
    {
        return $this->model->find($id);
    }
    public function create(array $data)
    {
        return $this->model->create($data);
    }
}