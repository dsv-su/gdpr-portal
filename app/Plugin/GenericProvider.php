<?php

namespace App\Plugin;

use App\Plugin;
use App\Searchcase;
use App\Status;

abstract class GenericPlugin
{
    public function __construct(Searchcase $case, Plugin $plugin, Status $status)
    {
        $this->case = $case;
        $this->plugin = $plugin;
        $this->status = $status;
    }

    public function auth()
    {

    }

    //abstract public function getResource($token);
}
