<?php

namespace App\Console\Commands;

use App\TechnicalCategory;
use Illuminate\Console\Command;

class AddTechnicalCategory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:addTechnicalCategory {category}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Adds a technical category';

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
        TechnicalCategory::query()->create([
            'name' => $this->argument('category')
        ]);
    }
}
