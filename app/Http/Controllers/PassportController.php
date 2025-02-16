<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Workflow\WorkflowStub;
use App\Workflows\ApplyPassportWorkflow;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;

class PassportController extends Controller
{
    public function createApplyRequest(): RedirectResponse {
        $workflow = WorkflowStub::make(ApplyPassportWorkflow::class);
        $workflow->start();

        return Redirect::route('dashboard');
    }
}
