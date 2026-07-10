<?php

namespace JeffersonGoncalves\Newsletter\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use JeffersonGoncalves\Newsletter\NewsletterServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\MediaLibrary\MediaLibraryServiceProvider;

abstract class TestCase extends Orchestra
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'JeffersonGoncalves\\Newsletter\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app): array
    {
        return [
            MediaLibraryServiceProvider::class,
            NewsletterServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        $app['config']->set('filesystems.disks.public', [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => '/storage',
            'visibility' => 'public',
        ]);

        $app['config']->set('queue.default', 'sync');
        $app['config']->set('queue.batching.database', 'testing');

        $app['config']->set('newsletter.schedule_enabled', false);
    }

    protected function defineDatabaseMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');

        $migrationPath = __DIR__.'/../database/migrations';
        $files = glob($migrationPath.'/*.php.stub');

        foreach ($files as $file) {
            $migrationFile = $migrationPath.'/'.basename($file, '.stub');

            if (! file_exists($migrationFile)) {
                copy($file, $migrationFile);
            }
        }

        $this->loadMigrationsFrom($migrationPath);

        $this->beforeApplicationDestroyed(function () use ($migrationPath, $files) {
            foreach ($files as $file) {
                $migrationFile = $migrationPath.'/'.basename($file, '.stub');

                if (file_exists($migrationFile)) {
                    unlink($migrationFile);
                }
            }
        });
    }
}
