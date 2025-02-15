<?php

namespace App\Temporal\Workflows;

use Temporal\Workflow\WorkflowInterface;
use Temporal\Workflow\WorkflowMethod;

#[WorkflowInterface]
class ApplyPassport
{
    public function __construct()
    {
        //
    }

    #[WorkflowMethod]
    public function handle(): \Generator
    {
        //
    }
}
