<?php

namespace App\Http\Controllers;

use Keepsuit\LaravelTemporal\Facade\Temporal;
use Illuminate\Http\Request;
use App\Temporal\Workflows\ApplyPassport;
use Inertia\Response;

class PassportController extends Controller
{
    public function createApplyRequest(): Response {
        $workflow = Temporal::newWorkflow()->build(ApplyPassport::class);

        $result = $workflow->handle();

        Temporal::workflowClient()->start($workflow);

        return Redirect::route('dashboard');
    }
}
