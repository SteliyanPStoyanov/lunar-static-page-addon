<?php

namespace Lunar\StaticPages\ActivityLog;

use Lunar\Hub\Base\ActivityLog\AbstractRender;
use Spatie\Activitylog\Models\Activity;

class Delete extends AbstractRender
{
    public function getEvent(): string
    {
        return 'deleted';
    }

    public function render(Activity $log)
    {
        return view('pages::partials.activity-log.delete', [
            'log' => $log,
        ]);
    }
}
