<?php


namespace App\Http\Controllers;


use App\Models;
use Illuminate\Http\Request;

class Lists extends Controller
{
    protected $entity;

    public function __construct(Models\EmailList $entity)
    {
        $this->entity = $entity;
    }

    public function list(): array
    {
        $this->entity->onlyFields(['name']);
        return $this->entity->export();
    }

    public function add(Request $request): array
    {
        return $this->entity->save($request->json()->all())->get();
    }

    public function save(Request $request, $id): array
    {
        return $this->entity->load($id)->save($request->json()->all())->get();
    }

    public function delete(Request $request, $id): array
    {
        $data = $this->entity->load($id)->get();
        $this->entity->delete();
        return ['deleted'=>$data];
    }

    public function get($id): array
    {
        $this->entity->load($id);
        $data = $this->entity->get();
        return $data;
    }
}
