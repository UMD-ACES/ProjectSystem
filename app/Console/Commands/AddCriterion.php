<?php

namespace App\Console\Commands;

use App\Criterion;
use Illuminate\Console\Command;

class AddCriterion extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:addCriterion {criterion}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Adds a criterion';

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
        $criterion = $this->argument('criterion');

        Criterion::create(array('name' => str_replace(' ', '_', strtolower($criterion)), 'humanName' => $criterion));
    }
}
