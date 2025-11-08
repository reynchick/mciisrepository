<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait HasSearchable
{
    /**
     * Scope a query to search by multiple fields and relations.
     */
    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        if (empty($search)) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            foreach ($this->getSearchableFields() as $field) {
                if (str_contains($field, '.')) {
                    // Handle relation searches
                    [$relation, $relationField] = explode('.', $field, 2);
                    $q->orWhereHas($relation, function ($relationQuery) use ($relationField, $search) {
                        $relationQuery->where($relationField, 'like', "%{$search}%");
                    });
                } else {
                    // Handle direct field searches
                    $q->orWhere($field, 'like', "%{$search}%");
                }
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