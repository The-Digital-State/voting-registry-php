<?php


namespace App\Http\Controllers;


use App\Models;

class Polls extends Controller
{
    protected $entity;

    public function __construct(Models\Poll $entity)
    {
        $this->entity = $entity;
    }

    public function list(): array
    {
        return $this->entity->export();
    }

    public function get($id): array
    {
        $this->entity->load($id);
        $data = $this->entity->get();

        $data['choices']=$this->entity->ref('Choices')->export();

        return $data;
    }
}
