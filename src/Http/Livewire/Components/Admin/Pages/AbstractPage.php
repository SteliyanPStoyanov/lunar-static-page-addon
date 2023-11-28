<?php

namespace Lunar\StaticPages\Http\Livewire\Components\Admin\Pages;

use Illuminate\Validation\Validator;
use Livewire\Component;
use Livewire\WithFileUploads;
use Lunar\Facades\DB;
use Lunar\Hub\Http\Livewire\Traits\CanExtendValidation;
use Lunar\Hub\Http\Livewire\Traits\HasImages;
use Lunar\Hub\Http\Livewire\Traits\HasSlots;
use Lunar\Hub\Http\Livewire\Traits\HasUrls;
use Lunar\Hub\Http\Livewire\Traits\Notifies;
use Lunar\Hub\Http\Livewire\Traits\SearchesProducts;
use Lunar\Hub\Http\Livewire\Traits\WithAttributes;
use Lunar\Hub\Http\Livewire\Traits\WithLanguages;
use Lunar\Models\Attribute;
use Lunar\StaticPages\Models\Page;

abstract class AbstractPage extends Component
{
    use CanExtendValidation;
    use HasImages;
    use HasSlots;
    use HasUrls;
    use Notifies;
    use SearchesProducts;
    use WithAttributes;
    use WithFileUploads;
    use WithLanguages;

    /**
     * The current product we are editing.
     */
    public Page $page;


    /**
     * Whether to show the delete confirmation modal.
     *
     * @var bool
     */
    public $showDeleteConfirm = false;

    /**
     * Whether to show the delete confirmation modal.
     *
     * @var bool
     */
    public $showRestoreConfirm = false;

    protected function getListeners()
    {
        return array_merge(
            [
                'updatedAttributes',
                'urlSaved' => 'refreshUrls'
            ],
            $this->getHasImagesListeners(),
            $this->getHasSlotsListeners()
        );
    }

    /**
     * Returns any custom validation messages.
     *
     * @return array
     */
    protected function getValidationMessages()
    {
        return array_merge(
            []

        );
    }

    /**
     * Define the validation rules.
     *
     * @return array
     */
    protected function rules()
    {
        $baseRules = [
            'page.status' => 'required|string',
            'page.title' => 'required|string',
            'page.description' => 'required|string',
        ];

        return array_merge(
            $baseRules,
            $this->hasImagesValidationRules(),
            $this->hasUrlsValidationRules(),
            $this->withAttributesValidationRules()
        );
    }

    /**
     * Define the validation attributes.
     *
     * @return array
     */
    protected function validationAttributes()
    {
        return $this->getUrlsValidationAttributes();
    }


    /**
     * Universal method to handle saving the product.
     *
     * @return void|\Symfony\Component\HttpFoundation\Response
     */
    public function save()
    {
        $this->withValidator(function (Validator $validator) {
            $validator->after(function ($validator) {

                if ($validator->errors()->count()) {
                    $this->notify(
                        __('pages::validation.generic'),
                        level: 'error'
                    );
                }
            });
        })->validate();

        $this->validateUrls();
        $isNew = !$this->page->id;

        DB::transaction(function () use ($isNew) {
            $data = $this->prepareAttributeData();
            $this->page->attribute_data = $data;

            $this->page->save();
            $this->saveUrls();
            $this->updateImages();
            $this->updateSlots();
            $this->page->refresh();
            $this->dispatchBrowserEvent('remove-images');
            $this->notify(__('pages::notifications.page.is_save'));
        });

        if ($isNew) {
            return redirect()->route('hub.pages.show', [
                'page' => $this->page->id,
            ]);
        }
    }

    /**
     * Returns the attribute data.
     *
     * @return array
     */
    public function getAttributeDataProperty()
    {
        return $this->page->attribute_data;
    }


    /**
     * Returns all available attributes.
     *
     * @return void
     */
    public function getAvailableAttributesProperty()
    {
        return Attribute::whereAttributeType(Page::class)->orderBy('position')->get();
    }


    /**
     * Return the side menu links.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getSideMenuProperty()
    {
        $collect = collect([
            [
                'title' => __('pages::menu.page.basic-information'),
                'id' => 'basic-information',
                'has_errors' => $this->errorBag->hasAny([
                    'page.title',
                    'page.description',
                ]),
            ],
            [
                'title' => __('pages::menu.page.images'),
                'id' => 'images',
                'has_errors' => $this->errorBag->hasAny([
                    'newImages.*',
                ]),
            ],
            [
                'title' => __('pages::menu.page.urls'),
                'id' => 'urls',
                'has_errors' => $this->errorBag->hasAny([
                    'urls',
                    'urls.*',
                ]),
            ],
        ])->reject(fn($item) => ($item['hidden'] ?? false));

        if(count($this->getAvailableAttributesProperty()) > 0){
            $collect->splice(1 , 0,[[
                'title' => __('pages::menu.page.attributes'),
                'id' => 'attributes',
                'has_errors' => $this->errorBag->hasAny([
                    'attributeMapping.*',
                ]),
            ]]);

        }

         return $collect;
    }


    protected function getHasUrlsModel()
    {
        return $this->page;
    }


    /**
     * Returns the model which has media associated.
     *
     * @return Page
     */
    protected function getMediaModel()
    {
        return $this->page;
    }

    /**
     * Returns the model which has slots associated.
     *
     * @return Page
     */
    protected function getSlotModel()
    {
        return $this->page;
    }

    /**
     * Returns the contexts for any slots.
     *
     * @return array
     */
    protected function getSlotContexts()
    {
        return ['page.all'];
    }

    /**
     * Render the livewire component.
     *
     * @return \Illuminate\View\View
     */
    abstract public function render();
}
