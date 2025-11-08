<?php

namespace App\Observers;

use App\Models\Faculty;
use App\Models\FacultyAuditLog;
use Illuminate\Support\Facades\Auth;

class FacultyObserver
{
    /**
     * Handle the Faculty "created" event.
     */
    public function created(Faculty $faculty): void
    {
        $this->logFacultyAudit(
            $faculty,
            FacultyAuditLog::ACTION_CREATE,
            null,
            $faculty->getAttributes(),
            ['event' => 'created']
        );
    }

    /**
     * Handle the Faculty "updated" event.
     */
    public function updated(Faculty $faculty): void
    {
        $changes = $faculty->getChanges();

        if (!empty($changes)) {
            $this->logFacultyAudit(
                $faculty,
                FacultyAuditLog::ACTION_UPDATE,
                $faculty->getOriginal(),
                $changes,
                ['changed' => array_keys($changes)]
            );
        }
    }

    /**
     * Handle the Faculty "deleted" event.
     */
    public function deleted(Faculty $faculty): void
    {
        $this->logFacultyAudit(
            $faculty,
            FacultyAuditLog::ACTION_DELETE,
            $faculty->getOriginal(),
            null,
            ['event' => 'deleted']
        );
    }

    /**
     * Create a FacultyAuditLog entry.
     */
    protected function logFacultyAudit(
        Faculty $faculty,
        string $action,
        ?array $oldValues,
        ?array $newValues,
        array $metadata = []
    ): void {
        FacultyAuditLog::create(array_merge($this->requestContext(), [
            'target_faculty_id' => $faculty->id,
            'action_type' => $action,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'metadata' => $metadata,
        ]));
    }

    /**
     * Common context pulled from the current request/auth.
     */
    protected function requestContext(): array
    {
        return [
            'modified_by' => Auth::id(),
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->userAgent(),
        ];
    }
}