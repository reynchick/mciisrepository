<?php

namespace App\Observers;

use App\Models\User;
use App\Models\UserAuditLog;
use Illuminate\Support\Facades\Auth;

class UserObserver
{
    /**
     * Temporary metadata storage for custom context.
     * Controllers can set this before creating/updating users.
     */
    public static ?array $customMetadata = null;

    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        $metadata = array_merge(
            ['event' => 'created'],
            self::$customMetadata ?? []
        );

        $this->logUserAudit(
            $user,
            UserAuditLog::ACTION_CREATE,
            null,
            $user->getAttributes(),
            $metadata
        );

        // Clear custom metadata after use
        self::$customMetadata = null;
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        $changes = $user->getChanges();

        // Figure out which fields actually changed
        $changedKeys = array_keys($changes);

        // Fields we consider "noise" (framework / technical)
        $ignored = ['remember_token', 'updated_at'];

        // Keep only meaningful fields
        $meaningfulChanged = array_diff($changedKeys, $ignored);

        // If nothing meaningful changed, do NOT create an audit log
        if (empty($meaningfulChanged)) {
            // Clear custom metadata just in case
            self::$customMetadata = null;
            return;
        }

        $metadata = array_merge(
            ['changed' => $meaningfulChanged],
            self::$customMetadata ?? []
        );

        $this->logUserAudit(
            $user,
            UserAuditLog::ACTION_UPDATE,
            $user->getOriginal(),
            $changes,
            $metadata
        );

        self::$customMetadata = null;
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        $this->logUserAudit(
            $user,
            UserAuditLog::ACTION_DEACTIVATE,
            $user->getOriginal(),
            null,
            ['event' => 'deleted']
        );
    }

    /**
     * Create a UserAuditLog entry.
     */
    protected function logUserAudit(
        User $user,
        string $action,
        ?array $oldValues,
        ?array $newValues,
        array $metadata = []
    ): void {
        UserAuditLog::create(array_merge($this->requestContext($user), [
            'target_user_id' => $user->id,
            'action_type' => $action,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'metadata' => $metadata,
        ]));
    }

    /**
     * Common context pulled from the current request/auth.
     */
    protected function requestContext(User $user = null): array
    {
        return [
            // Use Auth::id() if available, otherwise use the user's own ID (self-modification during registration/login)
            'modified_by' => Auth::id() ?? $user?->id,
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->userAgent(),
        ];
    }
}