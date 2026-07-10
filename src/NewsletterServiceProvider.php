<?php

namespace JeffersonGoncalves\Newsletter;

use Illuminate\Console\Scheduling\Schedule;
use JeffersonGoncalves\Newsletter\Commands\SendScheduledNewslettersCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class NewsletterServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('newsletter')
            ->hasConfigFile()
            ->hasViews()
            ->hasTranslations()
            ->hasMigrations([
                'create_newsletters_table',
                'create_email_groups_table',
                'create_email_group_members_table',
                'create_newsletter_email_group_table',
                'create_newsletter_sent_recipients_table',
            ])
            ->hasRoute('web')
            ->hasCommand(SendScheduledNewslettersCommand::class);
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(NewsletterManager::class);
    }

    public function packageBooted(): void
    {
        $this->registerSchedule();
    }

    protected function registerSchedule(): void
    {
        if (! config('newsletter.schedule_enabled', true)) {
            return;
        }

        $this->app->booted(function (): void {
            $schedule = $this->app->make(Schedule::class);

            $schedule->command(SendScheduledNewslettersCommand::class)->hourly();
        });
    }
}
