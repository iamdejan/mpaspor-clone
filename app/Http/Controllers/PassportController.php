<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Workflow\WorkflowStub;
use App\Workflows\ApplyPassportWorkflow;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use App\Models\PassportApplication;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class PassportController extends Controller
{
    public function index(Request $request): Response {
        $list = PassportApplication::where('created_by', $request->user()->id)->orderBy('created_at')->get();

        $applications = [];
        foreach ($list as $entry) {
            $workflow = WorkflowStub::load($entry->workflow_id);
            array_push($applications, [
                "workflow_id" => $workflow->id(),
                "status" => $this->mapStatus($workflow->status()),
            ]);
        }

        return Inertia::render('Dashboard')->with(["passport_applications" => $applications]);
    }

    public function createApplyRequest(Request $request): RedirectResponse {
        $workflow = WorkflowStub::make(ApplyPassportWorkflow::class);
        $workflow->start();

        PassportApplication::create([
            "workflow_id" => $workflow->id(),
            "created_by" => $request->user()->id
        ]);

        return Redirect::route('passport.first-page.view', ["workflow_id" => $workflow->id()]);
    }

    public function viewFirstPageForm(Request $request, string $workflow_id): Response {
        PassportApplication::where("workflow_id", $workflow_id)->firstOrFail();
        $workflow = WorkflowStub::load($workflow_id);
        if (!$workflow->running()) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException();
        }

        return Inertia::render('Passport/FirstPage')->with("workflow_id", $workflow_id);
    }

    public function submitFirstPageForm(Request $request, string $workflow_id): RedirectResponse {
        $request->validate([
            'identity_card' => 'required|image',
            'old_passport' => 'image'
        ]);

        $workflow = WorkflowStub::load($workflow_id);

        $identity_card_path = "workflows/".$workflow_id."/identity_image";
        Storage::put($identity_card_path, $request->identity_card);
        $workflow->setIdentityCardPath($identity_card_path);

        $old_passport_path = "";
        if ($request->old_passport) {
            $old_passport_path = "workflows/".$workflow_id."/old_passport";
            Storage::put($old_passport_path, $request->old_passport);
            $workflow->setOldPassportPath($old_passport_path);
        }

        $workflow->setAsCompleted();

        return Redirect::route("dashboard");
    }

    private static $status_map = [
        "Workflow\States\WorkflowFailedStatus" => "failed",
        "Workflow\States\WorkflowCompletedStatus" => "completed",
        "Workflow\States\WorkflowCreatedStatus" => "created",
        "Workflow\States\WorkflowPendingStatus" => "pending",
        "Workflow\States\WorkflowRunningStatus" => "running",
        "Workflow\States\WorkflowWaitingStatus" => "waiting",
    ];

    private function mapStatus(string $status): string {
        return static::$status_map[$status];
    }
}
