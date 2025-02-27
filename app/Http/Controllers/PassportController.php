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
    public function index(Request $request): Response
    {
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

    public function createApplyRequest(Request $request): RedirectResponse
    {
        $workflow = WorkflowStub::make(ApplyPassportWorkflow::class);
        $workflow->start();

        PassportApplication::create([
            "workflow_id" => $workflow->id(),
            "created_by" => $request->user()->id
        ]);

        return Redirect::route('passport.first-page.view', ["workflow_id" => $workflow->id()]);
    }

    public function viewFirstPageForm(Request $request, string $workflow_id): Response
    {
        PassportApplication::where("workflow_id", $workflow_id)->firstOrFail();
        $workflow = WorkflowStub::load($workflow_id);
        if (!$workflow->running()) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException();
        }

        return Inertia::render('Passport/FirstPage')->with("workflow_id", $workflow_id);
    }

    public function submitFirstPageForm(Request $request, string $workflow_id): RedirectResponse
    {
        $this->saveFirstPageForm($request, $workflow_id);

        return Redirect::route("passport.second-page.view", ["workflow_id" => $workflow_id]);
    }

    public function viewSecondPageForm(Request $request, string $workflow_id): Response
    {
        PassportApplication::where("workflow_id", $workflow_id)->firstOrFail();
        $workflow = WorkflowStub::load($workflow_id);
        if (!$workflow->running()) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException();
        }

        return Inertia::render('Passport/SecondPage')
            ->with("workflow_id", $workflow_id);
    }

    public function submitSecondPageForm(Request $request, string $workflow_id): RedirectResponse
    {
        $this->saveSecondPageForm($request, $workflow_id);

        $workflow = WorkflowStub::load($workflow_id);
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

    private function mapStatus(string $status): string
    {
        return static::$status_map[$status];
    }

    private function saveFirstPageForm(Request $request, string $workflow_id): void
    {
        $workflow = WorkflowStub::load($workflow_id);

        if ($request->has("identity_card")) {
            $identity_card_path = "workflows/" . $workflow_id . "/identity_image";
            Storage::put($identity_card_path, $request->identity_card);
            $workflow->setInput("identity_card_path", $identity_card_path);
        }

        if ($request->has("old_passport")) {
            $old_passport_path = "workflows/" . $workflow_id . "/old_passport";
            Storage::put($old_passport_path, $request->old_passport);
            $workflow->setInput("old_passport_path", $old_passport_path);
        }
    }

    private function saveSecondPageForm(Request $request, string $workflow_id): void
    {
        $workflow = WorkflowStub::load($workflow_id);

        if ($request->has("street_address")) {
            $workflow->setInput("street_address", $request->street_address);
        }

        if ($request->has("rt")) {
            $workflow->setInput("rt", $request->rt);
        }

        if ($request->has("rw")) {
            $workflow->setInput("rw", $request->rw);
        }

        if ($request->has("sub_district_code")) {
            $workflow->setInput("sub_district_code", $request->sub_district_code);
        }

        if ($request->has("district_code")) {
            $workflow->setInput("district_code", $request->district_code);
        }

        if ($request->has("city_code")) {
            $workflow->setInput("city_code", $request->city_code);
        }

        if ($request->has("province_code")) {
            $workflow->setInput("province_code", $request->province_code);
        }
    }
}
