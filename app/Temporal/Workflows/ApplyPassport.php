<?php

namespace App\Temporal\Workflows;

use Keepsuit\LaravelTemporal\Facade\Temporal;
use Temporal\Workflow\WorkflowInterface;
use Temporal\Workflow\WorkflowMethod;
use Temporal\Internal\Workflow\ActivityProxy;
use App\Temporal\Activities\HelloWorldActivity;

#[WorkflowInterface]
class ApplyPassport
{
    protected ActivityProxy $activity;

    public function __construct()
    {
        $this->activity = Temporal::newActivity()->withStartToCloseTimeout(CarbonInterval::seconds(1))->build(HelloWorldActivity::class);
    }

    #[WorkflowMethod]
    public function handle(): \Generator
    {
        yield ["result" => $this->activity->handle()];
    }
}
