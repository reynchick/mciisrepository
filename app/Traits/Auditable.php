<?php

namespace App\Traits;

use App\Models\Audit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

trait Auditable
{
    /**
     * Boot the auditable trait
     */
    protected static function bootAuditable(): void
    {
        static::created(function (Model $model) {
            $model->logAudit('created', null, $model->getDirty());
        });

        static::updated(function (Model $model) {
            $model->logAudit('updated', $model->getOriginal(), $model->getDirty());
        });

        static::deleted(function (Model $model) {
            $model->logAudit('deleted', $model->getOriginal(), null);
        });

        // Only listen for restored event if the model uses soft deletes
        if (in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses_recursive(static::class))) {
            static::restored(function (Model $model) {
                $model->logAudit('restored', null, $model->getDirty());
            });
        }
    }

    /**
     * Log an audit entry
     */
    public function logAudit(string $event, ?array $oldValues = null, ?array $newValues = null, ?array $metadata = null): void
    {
        // Filter out sensitive fields
        $oldValues = $this->filterSensitiveFields($oldValues);
        $newValues = $this->filterSensitiveFields($newValues);

        // Skip if no changes
        if ($event === 'updated' && empty($newValues)) {
            return;
        }

        Audit::create([
            'auditable_type' => static::class,
            'auditable_id' => $this->getKey(),
            'user_id' => Auth::id(),
            'event' => $event,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'metadata' => $metadata,
        ]);
    }

    /**
     * Log relationship changes
     */
    public function logRelationshipChange(string $event, string $relation, array $changes, ?array $metadata = null): void
    {
        $this->logAudit($event, null, [
            'relation' => $relation,
            'changes' => $changes
        ], $metadata);
    }

    /**
     * Filter out sensitive fields from audit logs
     */
    protected function filterSensitiveFields(?array $values): ?array
    {
        if (!$values) {
            return $values;
        }

        $sensitiveFields = ['password', 'remember_token', 'email_verified_at'];
        
        return array_diff_key($values, array_flip($sensitiveFields));
    }

    /**
     * Get audit logs for this model
     */
    public function audits()
    {
        return $this->morphMany(Audit::class, 'auditable');
    }
}
