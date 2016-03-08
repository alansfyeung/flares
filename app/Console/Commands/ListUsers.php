<?php

namespace App\Console\Commands;

use \App\FlaresUser;
use Illuminate\Console\Command;

class ListUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:list {--all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List users currently allowed to login.';

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
        if (!empty($this->option('all'))){
            $headers = ['ID', 'Username', 'Email', 'Access Level', 'Created', 'Updated', 'Deleted'];
            $columns = ['user_id', 'username', 'email', 'access_level', 'created_at', 'updated_at', 'deleted_at'];
        }
        else {
            $headers = ['Username', 'Email', 'Access Level'];
            $columns = ['username', 'email', 'access_level'];

        }

        $users = FlaresUser::all($columns)->toArray();
        $this->table($headers, $users);
        
    }
}
