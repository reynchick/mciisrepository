<?php

namespace App\Observers;

use App\Models\Faculty;
use App\Models\FacultyAuditLog;
use Illuminate\Support\Facades\Auth;

class FacultyObserver
{
    /**
     * Temporary metadata storage for custom context.
     * Controllers can set this before creating/updating faculty.
     */
    public static ?array $customMetadata = null;

    /**
     * Handle the Faculty "created" event.
     */
    public function created(Faculty $faculty): void
    {
        $metadata = array_merge(
            ['event' => 'created'],
            self::$customMetadata ?? []
        );

        $this->logFacultyAudit(
            $faculty,
            FacultyAuditLog::ACTION_CREATE,
            null,
            $faculty->getAttributes(),
            $metadata
        );

        // Clear custom metadata after use
        self::$customMetadata = null;
    }

    /**
     * Handle the Faculty "updated" event.
     */
    public function updated(Faculty $faculty): void
    {
        $changes = $faculty->getChanges();

        if (!empty($changes)) {
            $metadata = array_merge(
                ['changed' => array_keys($changes)],
                self::$customMetadata ?? []
            );

            $this->logFacultyAudit(
                $faculty,
                FacultyAuditLog::ACTION_UPDATE,
                $faculty->getOriginal(),
                $changes,
                $metadata
            );

            // Clear custom metadata after use
            self::$customMetadata = null;
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
        $modifiedBy = Auth::id() ?? (self::$customMetadata['modified_by'] ?? null);

        return [
            'modified_by' => $modifiedBy,
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->userAgent(),
        ];
    }
}