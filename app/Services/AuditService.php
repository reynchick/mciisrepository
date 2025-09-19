<?php

namespace App\Services;

use App\Models\Audit;
use App\Models\Research;
use App\Models\Keyword;
use App\Models\Faculty;
use App\Models\Researcher;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditService
{
    /**
     * Log keyword attachment to research
     */
    public function logKeywordAttached(Research $research, Keyword $keyword): void
    {
        $this->logRelationshipChange($research, 'attached', 'keywords', [
            'keyword_id' => $keyword->id,
            'keyword_name' => $keyword->keyword_name
        ]);
    }

    /**
     * Log keyword detachment from research
     */
    public function logKeywordDetached(Research $research, Keyword $keyword): void
    {
        $this->logRelationshipChange($research, 'detached', 'keywords', [
            'keyword_id' => $keyword->id,
            'keyword_name' => $keyword->keyword_name
        ]);
    }

    /**
     * Log panelist assignment to research
     */
    public function logPanelistAssigned(Research $research, Faculty $faculty): void
    {
        $this->logRelationshipChange($research, 'attached', 'panelists', [
            'faculty_id' => $faculty->id,
            'faculty_name' => $faculty->first_name . ' ' . $faculty->last_name
        ]);
    }

    /**
     * Log panelist removal from research
     */
    public function logPanelistRemoved(Research $research, Faculty $faculty): void
    {
        $this->logRelationshipChange($research, 'detached', 'panelists', [
            'faculty_id' => $faculty->id,
            'faculty_name' => $faculty->first_name . ' ' . $faculty->last_name
        ]);
    }

    /**
     * Log researcher addition to research
     */
    public function logResearcherAdded(Research $research, Researcher $researcher): void
    {
        $this->logRelationshipChange($research, 'attached', 'researchers', [
            'researcher_id' => $researcher->id,
            'researcher_name' => $researcher->first_name . ' ' . $researcher->last_name
        ]);
    }

    /**
     * Log researcher removal from research
     */
    public function logResearcherRemoved(Research $research, Researcher $researcher): void
    {
        $this->logRelationshipChange($research, 'detached', 'researchers', [
            'researcher_id' => $researcher->id,
            'researcher_name' => $researcher->first_name . ' ' . $researcher->last_name
        ]);
    }

    /**
     * Log bulk relationship changes
     */
    public function logBulkRelationshipChange(Model $model, string $event, string $relation, array $changes): void
    {
        $this->logRelationshipChange($model, $event, $relation, $changes);
    }

    /**
     * Helper method to log relationship changes
     */
    private function logRelationshipChange(Model $model, string $event, string $relation, array $changes): void
    {
        Audit::create([
            'auditable_type' => get_class($model),
            'auditable_id' => $model->getKey(),
            'user_id' => Auth::id(),
            'event' => $event,
            'old_values' => null,
            'new_values' => [
                'relation' => $relation,
                'changes' => $changes
            ],
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'metadata' => [
                'relation_type' => $relation,
                'performed_at' => now()->toISOString()
            ]
        ]);
    }
}

