<?php

namespace App\Console\Commands;

use App\User;
use Illuminate\Console\Command;

class CreateUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:create {username? : The username to be used for login} {--sso}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Flares admin user.';

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
        $providedUsername = $this->argument('username');
        $ssoUser = $this->option('sso');
        if ($ssoUser) {
            if (empty($providedUsername)){
                $providedUsername = $this->ask('Forums username');
            }
            $providedUsername = str_replace(' ', '', $providedUsername);
            $providedUsername = strtolower($providedUsername);
            $data['username'] = 'f.' . $providedUsername;
            $data['forums_username'] = $providedUsername;
            $data['allow_sso'] = 1;
            $this->line('Note: Password has been skipped because this user is an SSO user');

        } else {
            if (empty($providedUsername)){
                $providedUsername = $this->ask('New username');
            }
            $providedUsername = str_replace(' ', '', $providedUsername);
            $providedUsername = strtolower($providedUsername);
            $data['username'] = $providedUsername;
            $data['forums_username'] = null;
            $data['allow_sso'] = 0;
            do {
                $passwordOnce = $this->secret('Type password (not shown)');
                $passwordTwice = $this->secret('Type password again (not shown)');
                
                if ($passwordOnce != $passwordTwice){
                    $this->error('Passwords do not match');
                }
            } while ($passwordOnce != $passwordTwice);
            $data['password'] = $passwordOnce;
        }

        $data['email'] = $this->ask('Email (Enter to skip)', "staff@206acu.org.au");

        $tableHeaders = ['Code', 'Description'];
        $tableData = [
            [ User::ACCESS_NONE, 'No access (cannot login)' ],
            [ User::ACCESS_READONLY, 'Can view members profiles' ],
            [ User::ACCESS_ASSIGN, 'All above and can assign decorations' ],
            [ User::ACCESS_CREATE, 'All above and can create new decorations' ],
            [ User::ACCESS_ADMIN, 'All above and can add other admin users' ],
        ];
        $this->table($tableHeaders, $tableData);
        $accessLevelCodes = array_map(function ($accessCodeEntry) {
            return $accessCodeEntry[0];        // Get a list of just the access level codes
        }, $tableData);
        do {
            $accessLevel = $this->ask('New access level code (enter to default)', User::ACCESS_ASSIGN);
            $accessLevelValid = in_array((int) $accessLevel, $accessLevelCodes);
            if (!$accessLevelValid) {
                $this->error('Access level code must be one of: '.implode(', ', $accessLevelCodes));
            }
        } while (!$accessLevelValid);
        $data['access_level'] = $accessLevel;

        try {
            $newUser = $this->create($data, $ssoUser);
            $this->info("User created: {$newUser->username} " . ($ssoUser ? '(with SSO)' : '(with password)'));
        }
        catch (\Exception $ex){
            $this->error($ex->getCode() . ' ' . $ex->getMessage());
        }
    }


    /**
     *  Create the new user account
     *  
     *  @return \App\User $user
     */
    private function create(array $data, $skipPassword = false)
    {
        $user = new User(); 
        $user->fill([
            'username' => $data['username'],
            'forums_username' => $data['forums_username'],
            'email' => $data['email'],
            'allow_sso' => $data['allow_sso'],
            'access_level' => $data['access_level'],
        ]);
        if ($skipPassword) {
            $user->password = 'x';          // They will never be able to login with this
        } else {
            $user->password = bcrypt($data['password']);
        }
        $user->save();
        return $user;
    }




}
