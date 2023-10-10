<?php

namespace Lunar\StaticPages\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Lunar\FieldTypes\Text;
use Lunar\StaticPages\Models\Page;

class PageFactory extends Factory
{
    protected $model = Page::class;

    public function definition(): array
    {
        return [
            'status' => 'published',
            'name' => new Text($this->faker->name),
            'description' => new Text($this->faker->sentence),
        ];
    }
}
