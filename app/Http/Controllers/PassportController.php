<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Workflow\WorkflowStub;
use App\Workflows\ApplyPassportWorkflow;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use App\Models\PassportApplication;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
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

        $input_data = $workflow->getInputData();
        $identity_card_path = array_key_exists("identity_card_path", $input_data) ? Storage::url($input_data["identity_card_path"]) : null;
        $old_passport_path = array_key_exists("old_passport_path", $input_data) ? Storage::url($input_data["old_passport_path"]) : null;

        return Inertia::render('Passport/FirstPage')->with("workflow_id", $workflow_id)
            ->with("identity_card_path", $identity_card_path)
            ->with("old_passport_path", $old_passport_path);
    }

    public function submitFirstPageForm(Request $request, string $workflow_id): RedirectResponse
    {
        $this->saveFirstPageForm($request, $workflow_id);

        $workflow = WorkflowStub::load($workflow_id);
        $input_data = $workflow->getInputData();
        $validator = Validator::make($input_data, [
            "identity_card_path" => "required",
            "old_passport_path" => "nullable",
        ]);
        $validator->validate();

        return Redirect::route("passport.second-page.view", ["workflow_id" => $workflow_id]);
    }

    public function viewSecondPageForm(Request $request, string $workflow_id): Response
    {
        PassportApplication::where("workflow_id", $workflow_id)->firstOrFail();
        $workflow = WorkflowStub::load($workflow_id);
        if (!$workflow->running()) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException();
        }

        $input_data = $workflow->getInputData();
        $street_address = array_key_exists("street_address", $input_data) ? $input_data["street_address"] : null;
        $rt = array_key_exists("rt", $input_data) ? $input_data["rt"] : null;
        $rw = array_key_exists("rw", $input_data) ? $input_data["rw"] : null;
        $sub_district_code = array_key_exists("sub_district_code", $input_data) ? $input_data["sub_district_code"] : null;
        $district_code = array_key_exists("district_code", $input_data) ? $input_data["district_code"] : null;
        $city_code = array_key_exists("city_code", $input_data) ? $input_data["city_code"] : null;
        $province_code = array_key_exists("province_code", $input_data) ? $input_data["province_code"] : null;

        return Inertia::render('Passport/SecondPage')
            ->with("workflow_id", $workflow_id)
            ->with("street_address", $street_address)
            ->with("rt", $rt)
            ->with("rw", $rw)
            ->with("sub_district_code", $sub_district_code)
            ->with("district_code", $district_code)
            ->with("city_code", $city_code)
            ->with("province_code", $province_code);
    }

    public function submitSecondPageForm(Request $request, string $workflow_id): RedirectResponse
    {
        $this->saveSecondPageForm($request, $workflow_id);

        $workflow = WorkflowStub::load($workflow_id);

        $input_data = $workflow->getInputData();
        $validator = Validator::make($input_data, [
            "street_address" => "required",
            "rt" => "required",
            "rw" => "required",
            "sub_district_code" => "required",
            "district_code" => "required",
            "city_code" => "required",
            "province_code" => "required"
        ]);
        $validator->validate();

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

        if ($request->identity_card) {
            $identity_card_path = "workflows/" . $workflow_id;
            Storage::putFileAs($identity_card_path, $request->identity_card, "identity_card.jpg");
            $workflow->setInput("identity_card_path", $identity_card_path . "/identity_card.jpg");
        }

        if ($request->old_passport) {
            $old_passport_path = "workflows/" . $workflow_id;
            Storage::putFileAs($old_passport_path, $request->old_passport, "old_passport.jpg");
            $workflow->setInput("old_passport_path", $old_passport_path . "/old_passport.jpg");
        }
    }

    private function saveSecondPageForm(Request $request, string $workflow_id): void
    {
        $workflow = WorkflowStub::load($workflow_id);

        if ($request->street_address) {
            $workflow->setInput("street_address", $request->street_address);
        }

        if ($request->rt) {
            $workflow->setInput("rt", $request->rt);
        }

        if ($request->rw) {
            $workflow->setInput("rw", $request->rw);
        }

        if ($request->sub_district_code) {
            $workflow->setInput("sub_district_code", $request->sub_district_code);
        }

        if ($request->district_code) {
            $workflow->setInput("district_code", $request->district_code);
        }

        if ($request->city_code) {
            $workflow->setInput("city_code", $request->city_code);
        }

        if ($request->province_code) {
            $workflow->setInput("province_code", $request->province_code);
        }
    }
}
