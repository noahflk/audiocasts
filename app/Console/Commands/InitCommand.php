<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\Kernel as Artisan;

class InitCommand extends Command
{
    protected $signature = 'audiocasts:init';
    protected $description = 'Initialize Audiocasts';

    private $artisan;

    public function __construct(Artisan $artisan,)
    {
        parent::__construct();
        $this->artisan = $artisan;
    }


    public function handle(): void
    {
        try {
            $this->maybeGenerateAppKey();
        } catch (\Throwable $error) {
            $this->error("Audiocast installation didn't finish successfully. Sorry for this!");
        }

        $this->comment(PHP_EOL . 'Success! Audiocasts is now ready ✅');
    }

    private function maybeGenerateAppKey(): void
    {
        if (!config('app.key')) {
            $this->info('Generating app key');
            $this->artisan->call('key:generate');
        } else {
            $this->comment('App key exists -- skipping');
        }
    }
}
