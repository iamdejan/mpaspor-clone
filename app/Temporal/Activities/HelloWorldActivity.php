<?php

namespace App\Temporal\Activities;

use Temporal\Activity\ActivityInterface;
use Temporal\Activity\ActivityMethod;

#[ActivityInterface]
class HelloWorldActivity
{
    public function __construct()
    {
        //
    }

    #[ActivityMethod]
    public function handle(): string
    {
        return "Hello from Activity";
    }
}
