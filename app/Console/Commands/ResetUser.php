<?php

namespace App\Console\Commands;

use App\User;
use Illuminate\Console\Command;

class ResetUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:reset {username?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset a user password.';

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
        $data = [];
        $username = $this->argument('username');
        if (empty($username)){
            $username = $this->ask('Username of account to reset');
        }

        // Check the user exists
        $user = User::where('username', $username)->first();
        if (empty($user)){
            $this->error("Cannot find user $username");
            return;
        }

        do {
            $passwordOnce = $this->secret('Type password (not shown)');
            $passwordTwice = $this->secret('Type password again (not shown)');
            
            if ($passwordOnce != $passwordTwice){
                $this->error('Passwords do not match');
            }
        } while ($passwordOnce != $passwordTwice);

        try {
            $user->password = bcrypt($passwordOnce);
            $user->save();
            $this->info("User password updated");
        }
        catch (\Exception $ex){
            $this->error($ex->getCode() . ' ' . $ex->getMessage());
        }
    }
}
