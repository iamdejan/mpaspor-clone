<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Workflow\WorkflowStub;
use App\Workflows\ApplyPassportWorkflow;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use App\Models\PassportApplication;

class PassportController extends Controller
{
    public function createApplyRequest(Request $request): RedirectResponse {
        $workflow = WorkflowStub::make(ApplyPassportWorkflow::class);
        $workflow->start();
        $workflow->complete();

        $passportApplication = PassportApplication::create([
            "workflow_id" => $workflow->id(),
            "created_by" => $request->user()->id
        ]);

        return Redirect::route('dashboard');
    }
}
