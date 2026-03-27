<?php

namespace MaxieWright\TtdfOrbat\Commands;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    protected $signature = 'ttdf-orbat:install
        {--with-user-fields : Publish migration that adds ORBAT FK columns to the users table}';

    protected $description = 'Publish TTDF ORBAT config and migrations into your application';

    public function handle(): int
    {
        $this->comment('Installing TTDF ORBAT...');

        $configExitCode = $this->callSilently('vendor:publish', [
            '--tag' => 'ttdf-orbat-config',
            '--force' => false,
        ]);
        if ($configExitCode !== self::SUCCESS) {
            $this->error('  ✗ Failed to publish config (tag: ttdf-orbat-config)');
            return self::FAILURE;
        }
        $this->info('  ✓ Config published to config/ttdf-orbat.php');

        $migrationsExitCode = $this->callSilently('vendor:publish', [
            '--tag' => 'ttdf-orbat-migrations',
            '--force' => false,
        ]);
        if ($migrationsExitCode !== self::SUCCESS) {
            $this->error('  ✗ Failed to publish migrations (tag: ttdf-orbat-migrations)');
            return self::FAILURE;
        }
        $this->info('  ✓ Migrations published');

        if ($this->option('with-user-fields')) {
            $userFieldsExitCode = $this->callSilently('vendor:publish', [
                '--tag' => 'ttdf-orbat-user-fields',
                '--force' => false,
            ]);
            if ($userFieldsExitCode !== self::SUCCESS) {
                $this->error('  ✗ Failed to publish user-fields migration (tag: ttdf-orbat-user-fields)');
                return self::FAILURE;
            }
            $this->info('  ✓ User-fields migration published (add BelongsToOrbat to your User model)');
        }

        $this->newLine();
        $this->info('Next steps:');
        $this->line('  php artisan migrate');
        $this->line('  php artisan ttdf-orbat:seed');

        return self::SUCCESS;
    }
}
