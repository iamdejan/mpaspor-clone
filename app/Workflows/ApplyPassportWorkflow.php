<?php

namespace App\Workflows;

use Workflow\SignalMethod;
use Workflow\Workflow;
use Workflow\WorkflowStub;

class ApplyPassportWorkflow extends Workflow
{
    private bool $completed = false;
    private ?string $identity_card_path = null;
    private ?string $old_passport_path = null;

    public function execute()
    {
        yield WorkflowStub::await(fn () => $this->completed);

        return [
            "identity_card_path" => $this->identity_card_path,
            "old_passport_path" => $this->old_passport_path,
        ];
    }

    #[SignalMethod]
    public function setIdentityCardPath(string $path): void {
        $this->identity_card_path = $path;
    }

    #[SignalMethod]
    public function setOldPassportPath(string $path): void {
        $this->old_passport_path = $path;
    }

    #[SignalMethod]
    public function setAsCompleted(): void {
        $this->completed = true;
    }
}
