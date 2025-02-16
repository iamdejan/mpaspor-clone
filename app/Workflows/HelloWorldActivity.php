<?php

namespace App\Workflows;

use Workflow\Activity;

class HelloWorldActivity extends Activity
{
    public function execute()
    {
        return "Hello from HelloWorldActivity!";
    }
}
