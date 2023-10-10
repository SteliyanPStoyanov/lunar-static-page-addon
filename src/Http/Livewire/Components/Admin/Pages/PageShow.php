<?php

namespace Lunar\StaticPages\Http\Livewire\Components\Admin\Pages;

class PageShow extends AbstractPage
{
    /**
     * Called when the component is mounted.
     *
     * @return void
     */
    public function mount()
    {

    }

    /**
     * Delete the product.
     *
     * @return void
     */
    public function delete()
    {
        $this->page->delete();
        $this->notify(
            __('pages::notifications.pages.deleted'),
            'hub.pages.index'
        );
    }

    /**
     * Restore the product.
     *
     * @return void
     */
    public function restore()
    {
        $this->page->restore();
        $this->showRestoreConfirm = false;
        $this->notify(
            __('pages::notifications.pages.page_restored')
        );
    }

    /**
     * Render the livewire component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('pages::livewire.components.admin.pages.show')->layout('adminhub::layouts.base');
    }

    protected function getSlotContexts()
    {
        return ['page.all', 'page.show'];
    }
}
