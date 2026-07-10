<?php

namespace JeffersonGoncalves\Newsletter\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JeffersonGoncalves\Newsletter\Enums\RecipientDeliveryStatus;
use JeffersonGoncalves\Newsletter\Models\Newsletter;
use JeffersonGoncalves\Newsletter\Models\NewsletterSentRecipient;

/**
 * @extends Factory<NewsletterSentRecipient>
 */
class NewsletterSentRecipientFactory extends Factory
{
    protected $model = NewsletterSentRecipient::class;

    public function definition(): array
    {
        return [
            'newsletter_id' => Newsletter::factory(),
            'email' => fake()->unique()->safeEmail(),
            'status' => RecipientDeliveryStatus::Sent,
            'error_message' => null,
            'sent_at' => now(),
        ];
    }

    public function failed(): static
    {
        return $this->state(fn (): array => [
            'status' => RecipientDeliveryStatus::Failed,
            'error_message' => fake()->sentence(),
            'sent_at' => null,
        ]);
    }
}
