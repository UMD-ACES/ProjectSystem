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
    protected $signature = 'app:addAdminUser {name} {directoryID}';

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
        $name = $this->argument('name');

        /** @var User $user */
        $user = User::create(array('name' => $name, 'dirID' => $user));
        $user->type = User::$admin;
        $user->save();
    }
}