<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class PresenceTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:presenceTest {ext} {status}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Laravel Echo Presence Test Command';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        event(new \App\Events\PresenceUpdated($this->argument('ext'), $this->argument('status')));
    }
}
