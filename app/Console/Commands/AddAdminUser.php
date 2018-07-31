<?php

namespace App\Console\Commands;

use App\User;
use Illuminate\Console\Command;

class AddAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:addAdminUser {directoryID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Adds an admin user to the application';

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
        $user = $this->argument('directoryID');

        User::create(array('dirID' => $user, 'group' => User::$admin));
    }
}