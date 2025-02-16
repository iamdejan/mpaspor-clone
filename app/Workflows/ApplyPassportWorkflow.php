<?php

namespace App\Workflows;

use Workflow\Workflow;

class ApplyPassportWorkflow extends Workflow
{
    public function execute()
    {
        $result = yield ActivityStub::make(HelloWorldActivity::class);

        return $result;
    }
}
