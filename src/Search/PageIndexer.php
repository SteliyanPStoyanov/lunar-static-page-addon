<?php

namespace Lunar\StaticPages\Search;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Lunar\Search\ScoutIndexer;

class PageIndexer extends ScoutIndexer
{
    public function getSortableFields(): array
    {
        return [
            'created_at',
            'updated_at',
            'status',
        ];
    }

    public function getFilterableFields(): array
    {
        return [
            '__soft_deleted',
            'status',
        ];
    }

    public function makeAllSearchableUsing(Builder $query): Builder
    {
        return $query;
    }

    public function toSearchableArray(Model $model): array
    {
        // Do this here so other additions to the data appear under the attributes,
        // more of a vanity thing than anything else.
        $data = array_merge([
            'id' => $model->id,
            'status' => $model->status,
            'title' => $model->title,
            'created_at' => $model->created_at->timestamp,
        ], $this->mapSearchableAttributes($model));

        return $data;
    }
}
