<?php

namespace Lunar\StaticPages\Http\Livewire\Components;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Livewire\Component;
use Livewire\WithPagination;
use Lunar\Facades\ModelManifest;
use Lunar\Hub\Base\ActivityLog\Comment;
use Lunar\Hub\Base\ActivityLog\Create;
use Lunar\Hub\Base\ActivityLog\Manifest;
use Lunar\Hub\Base\ActivityLog\Update;
use Lunar\Hub\Facades\ActivityLog;
use Lunar\Hub\Http\Livewire\Traits\Notifies;
use Lunar\StaticPages\ActivityLog\Delete;
use Lunar\StaticPages\ActivityLog\Restore;
use Lunar\StaticPages\Models\Page;

class ActivityLogFeed extends Component
{
    use Notifies, WithPagination;

    /**
     * The log subject to get activity for.
     */
    public Model $subject;


    /**
     * The new comment for the subject.
     */
    public ?string $comment = null;

    /**
     * {@inheritDoc}
     */
    public function rules()
    {
        return [
            'comment' => 'string|required',
        ];
    }

     public function mount()
    {
        $this->renderers = new Collection();
        $this->renderers->add(
            [
                "event" => 'comment',
                "class" => app(Comment::class)
            ],
        );
        $this->renderers->add(
            [
                "event" => 'updated',
                "class" => app(Update::class)
            ],
        );
        $this->renderers->add(
            [
                "event" => 'created',
                "class" => app(Create::class)
            ],
        );
        $this->renderers->add(
            [
                "event" => 'deleted',
                "class" => app(Delete::class)
            ],
        );
         $this->renderers->add(
            [
                "event" => 'restored',
                "class" => app(Restore::class)
            ],
        );

    }
    /**
     * Add a comment to the order.
     *
     * @return void
     */
    public function addComment()
    {
        activity()
            ->performedOn($this->subject)
            ->causedBy(
                auth()->user()
            )
            ->event('comment')
            ->withProperties(['content' => $this->comment])
            ->log('comment');

        $this->notify(
            __('adminhub::notifications.order.comment_added')
        );

        $this->comment = null;
    }

    /**
     * Returns the activity log for the order.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getActivityLogProperty()
    {



        return $this->subject->activities()
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function ($log) {
                return $log->created_at->format('Y-m-d');
            })->map(function ($logs) {

                return [
                    'date' => $logs->first()->created_at->startOfDay(),
                    'items' => $logs->map(function ($log) {
                        return [
                            'log' => $log,
                            'renderers' => $this->renderers->filter(function ($render) use ($log) {
                                return $render['event'] == $log->event;
                            })->pluck('class'),
                        ];
                    }),
                ];
            });
    }

    public function getRenderersProperty()
    {
        $subjectClass = ModelManifest::getMorphClassBaseModel(get_class($this->subject)) ?? get_class($this->subject);

        return ActivityLog::getItems($subjectClass);
    }

    /**
     * Render the livewire component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('adminhub::livewire.components.activity-log-feed')
            ->layout('adminhub::layouts.base');
    }
}
