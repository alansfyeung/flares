<?php

namespace App\Console\Commands;

use App\FlaresUser;
use Illuminate\Console\Command;

class RegisterUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:create {username? : The username to be used for login}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Flares user.';

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
        $data['username'] = $this->argument('username');
        if (empty($data['username'])){
            $data['username'] = $this->ask('New username');
        }

        $data['username'] = str_replace(' ', '', $data['username']);

        do {
            $passwordOnce = $this->secret('Type password (not shown)');
            $passwordTwice = $this->secret('Type password again (not shown)');
            
            if ($passwordOnce != $passwordTwice){
                $this->error('Passwords do not match');
            }
        } while ($passwordOnce != $passwordTwice);

        $data['password'] = $passwordOnce;
        $data['email'] = $this->ask('Email (Enter to skip)', "staff@206acu.org.au");
        //if (empty(trim($data['email']))){
        //    $data['email'] = 'staff@206acu.org.au';
        //}

        try {
            $newUser = $this->create($data);
            $this->info("User created: [{$newUser->user_id}] {$newUser->username} {$newUser->email}");
        }
        catch (\Exception $ex){
            $this->error($ex->getCode() . ' ' . $ex->getMessage());
        }
    }


    /**
     *  Create the new user account
     *  
     *  @return \App\FlaresUser $user
     */
    private function create(array $data)
    {
        $user =FlaresUser::create([
            'username' => $data['username'],
            'email' => $data['email'],
            //'password' => bcrypt($data['password']),
        ]);
        $user->password = bcrypt($data['password']);
        $user->save();
        return $user;
    }




}
