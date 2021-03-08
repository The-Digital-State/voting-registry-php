<?php


namespace App\Http\Controllers;


use App\Models;
use Atk4\Data\Persistence;

class Migrator extends Controller
{
    protected $db;

    public function __construct(Persistence $db)
    {
        $this->db = $db;
    }

    /**
     * @throws
     */
    public function migrate(): array
    {
        $res = [];
        $res[] = (new \Atk4\Schema\Migration(new Models\User($this->db)))->dropIfExists()->create();
        $res[] = (new \Atk4\Schema\Migration(new Models\Poll($this->db)))->dropIfExists()->create();
        return $res;
    }
}
