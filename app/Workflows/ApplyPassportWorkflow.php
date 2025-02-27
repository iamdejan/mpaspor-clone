<?php

namespace App\Workflows;

use Workflow\SignalMethod;
use Workflow\Workflow;
use Workflow\WorkflowStub;

class ApplyPassportWorkflow extends Workflow
{
    private static array $valid_inputs = [
        "identity_card_path",
        "old_passport_path",
        "street_address",
        "rt",
        "rw",
        "sub_district_code",
        "district_code",
        "city_code",
        "province_code",
    ];

    private bool $completed = false;

    private array $input_data = [];

    public function execute()
    {
        yield WorkflowStub::await(fn() => $this->completed);

        return $this->input_data;
    }

    #[SignalMethod]
    public function setInput(string $field_name, string $data): void
    {
        if (!in_array($field_name, self::$valid_inputs)) {
            throw new \Exception("Invalid input field name");
        }

        $this->input_data[$field_name] = $data;
    }

    #[SignalMethod]
    public function setAsCompleted(): void
    {
        $this->completed = true;
    }
}
