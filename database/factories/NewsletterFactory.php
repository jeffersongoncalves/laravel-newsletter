<?php

namespace JeffersonGoncalves\Newsletter\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JeffersonGoncalves\Newsletter\Enums\NewsletterContentType;
use JeffersonGoncalves\Newsletter\Enums\NewsletterStatus;
use JeffersonGoncalves\Newsletter\Models\Newsletter;

/**
 * @extends Factory<Newsletter>
 */
class NewsletterFactory extends Factory
{
    protected $model = Newsletter::class;

    public function definition(): array
    {
        return [
            'subject' => fake()->sentence(),
            'sender_name' => fake()->name(),
            'sender_email' => fake()->safeEmail(),
            'content' => '<p>'.fake()->paragraph().'</p>',
            'content_type' => NewsletterContentType::Html,
            'status' => NewsletterStatus::Draft,
            'route' => fake()->unique()->slug(),
            'published' => false,
            'send_unsubscribe_link' => true,
            'send_webview_link' => false,
            'scheduled_at' => null,
            'sent_at' => null,
            'total_recipients' => 0,
            'total_views' => 0,
            'utm_campaign' => fake()->word(),
        ];
    }

    public function scheduled(): static
    {
        return $this->state(fn (): array => [
            'status' => NewsletterStatus::Scheduled,
            'scheduled_at' => now()->addHour(),
        ]);
    }

    public function sent(): static
    {
        return $this->state(fn (): array => [
            'status' => NewsletterStatus::Sent,
            'sent_at' => now(),
        ]);
    }

    public function published(): static
    {
        return $this->state(fn (): array => [
            'published' => true,
        ]);
    }
}
