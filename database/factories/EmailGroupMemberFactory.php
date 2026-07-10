<?php

namespace JeffersonGoncalves\Newsletter\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JeffersonGoncalves\Newsletter\Models\EmailGroup;
use JeffersonGoncalves\Newsletter\Models\EmailGroupMember;

/**
 * @extends Factory<EmailGroupMember>
 */
class EmailGroupMemberFactory extends Factory
{
    protected $model = EmailGroupMember::class;

    public function definition(): array
    {
        return [
            'email_group_id' => EmailGroup::factory(),
            'email' => fake()->unique()->safeEmail(),
            'unsubscribed' => false,
            'unsubscribed_at' => null,
            'confirmed_at' => now(),
        ];
    }

    public function unconfirmed(): static
    {
        return $this->state(fn (): array => [
            'confirmed_at' => null,
        ]);
    }

    public function unsubscribed(): static
    {
        return $this->state(fn (): array => [
            'unsubscribed' => true,
            'unsubscribed_at' => now(),
        ]);
    }
}
