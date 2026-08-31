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
        $app['config']->set('database.connections.testing', $this->testing_connection());

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

    /**
     * Defaults to an in-memory SQLite connection for local development; CI
     * (tests.yml) sets NEWSLETTER_TEST_DB_* to run the same suite against
     * real MySQL and PostgreSQL instances too. Deliberately not the plain
     * DB_* names: Orchestra Testbench itself sets DB_CONNECTION=testing by
     * convention, which would collide with (and always win over) a driver
     * value read from the same variable here.
     *
     * @return array<string, mixed>
     */
    protected function testing_connection(): array
    {
        $driver = env('NEWSLETTER_TEST_DB_DRIVER', 'sqlite');

        if ($driver === 'sqlite') {
            return ['driver' => 'sqlite', 'database' => ':memory:', 'prefix' => ''];
        }

        return [
            'driver' => $driver,
            'host' => env('NEWSLETTER_TEST_DB_HOST', '127.0.0.1'),
            'port' => env('NEWSLETTER_TEST_DB_PORT'),
            'database' => env('NEWSLETTER_TEST_DB_DATABASE', 'testing'),
            'username' => env('NEWSLETTER_TEST_DB_USERNAME', 'root'),
            'password' => env('NEWSLETTER_TEST_DB_PASSWORD', ''),
            'charset' => $driver === 'pgsql' ? 'utf8' : 'utf8mb4',
            'prefix' => '',
        ];
    }

    /**
     * Same order as NewsletterServiceProvider::hasMigrations(). SQLite
     * doesn't enforce foreign keys at CREATE TABLE time, but MySQL/Postgres
     * do, and alphabetical order breaks it here: "email_group_members" and
     * "newsletter_email_group"/"newsletter_sent_recipients" all sort before
     * the "email_groups"/"newsletters" tables they reference ('_' < 's').
     */
    private const MIGRATION_ORDER = [
        'create_newsletters_table',
        'create_email_groups_table',
        'create_email_group_members_table',
        'create_newsletter_email_group_table',
        'create_newsletter_sent_recipients_table',
    ];

    protected function defineDatabaseMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');

        $stubsPath = __DIR__.'/../database/migrations';
        $tempPath = sys_get_temp_dir().'/laravel-newsletter-migrations';

        if (! is_dir($tempPath)) {
            mkdir($tempPath, 0755, true);
        }

        foreach (self::MIGRATION_ORDER as $index => $name) {
            copy($stubsPath.'/'.$name.'.php.stub', $tempPath.'/'.sprintf('%03d_%s.php', $index, $name));
        }

        $this->loadMigrationsFrom($tempPath);
    }
}
