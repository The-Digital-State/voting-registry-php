<?php


namespace App\Http\Controllers;


use App\Models;

class Polls extends Controller
{
    protected $polls;

    public function __construct(Models\Poll $polls)
    {
        $this->polls = $polls;
    }

    public function list(): array
    {
        return $this->polls->export();
    }
}
