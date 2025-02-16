<?php

namespace App\Workflows;

use Workflow\Workflow;
use Workflow\WorkflowStub;

class ApplyPassportWorkflow extends Workflow
{
    private bool $completed = false;

    public function execute()
    {
        yield WorkflowStub::await(fn () => $this->completed);
    }
}
