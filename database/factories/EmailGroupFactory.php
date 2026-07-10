<?php

namespace JeffersonGoncalves\Newsletter\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JeffersonGoncalves\Newsletter\Models\EmailGroup;

/**
 * @extends Factory<EmailGroup>
 */
class EmailGroupFactory extends Factory
{
    protected $model = EmailGroup::class;

    public function definition(): array
    {
        return [
            'title' => fake()->unique()->words(2, true),
        ];
    }
}
