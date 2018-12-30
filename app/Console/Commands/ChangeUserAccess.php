<?php

namespace App\Console\Commands;

use App\User;
use Illuminate\Console\Command;

class ChangeUserAccess extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:access {username?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Display or change a user\'s access level.';

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
        if (empty($username)) {
            $username = $this->ask('Username to check');
        }

        // Check the user exists
        $user = User::where('username', $username)->first();
        if (empty($user)) {
            $this->error("Cannot find user $username");
            return;
        }

        $this->line("User '$user->username' currently has access level $user->access_level");
        $tableHeaders = ['Code', 'Description'];
        $tableData = [
            [ User::ACCESS_NONE, 'No access (cannot login)' ],
            [ User::ACCESS_READONLY, 'Can view members profiles' ],
            [ User::ACCESS_ASSIGN, 'All above and can assign decorations' ],
            [ User::ACCESS_CREATE, 'All above and can create new decorations' ],
            [ User::ACCESS_ADMIN, 'All above and can add other admin users' ],
        ];
        $this->table($tableHeaders, $tableData);

        $accessLevelCodes = array_map(function ($data) {
            return $data[0];        // Get a list of just the access level codes
        }, $tableData);

        do {
            $accessLevel = $this->anticipate('New access level code', $accessLevelCodes);
            $accessLevelValid = in_array((int) $accessLevel, $accessLevelCodes);
            if (!$accessLevelValid) {
                $this->error('Access level code must be one of: ' . implode(', ', $accessLevelCodes));
            }
        } while (!$accessLevelValid);

        try {
            $user->access_level = $accessLevel;
            $user->save();
            $this->info("User access level updated");
        }
        catch (\Exception $ex){
            $this->error($ex->getCode() . ' ' . $ex->getMessage());
        }
    }
}
