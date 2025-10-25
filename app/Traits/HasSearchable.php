<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait HasSearchable
{
    /**
     * Scope a query to search by multiple fields.
     */
    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        if (empty($search)) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            foreach ($this->getSearchableFields() as $field) {
                $q->orWhere($field, 'like', "%{$search}%");
            }
        });
    }

    /**
     * Get the fields that should be searchable.
     */
    protected function getSearchableFields(): array
    {
        return $this->searchableFields ?? [];
    }
}