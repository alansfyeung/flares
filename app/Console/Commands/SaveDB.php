<?php

namespace App\Console\Commands;

use DB;
use App;
use Illuminate\Console\Command;

class SaveDB extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:save {what=s}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Save the MySQL schema into a dump file.';

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
		$what = strtolower($this->argument('what'));
		/*
		 * S = schema
		 * R = reference data
		 * D = data
		 */
		 
		// $this->info(print_r(App::environmentFile(), true));
		// $this->info(print_r($_ENV, true));
		
		
		
		if (strpos($what, 's') !== false){
			$dbName = $_ENV['DB_DATABASE'];
			$this->comment("Saving the schema from $dbName...");
			
			$relativeFilePath = sprintf('storage/dbdumps/%s', "mysqldump-$what-".date('Ymd').'.sql'); 
			$fullFilePath = sprintf('%s/../../../%s', __DIR__, $relativeFilePath); 
			$fh = fopen($fullFilePath, 'w');
			
			$dumpHeader  = '--==================================' . PHP_EOL;
			$dumpHeader .= '-- 206 FLARES MySQL Schema Dump' . PHP_EOL;
			$dumpHeader .= '-- Environment: '. App::environment() . PHP_EOL;
			$dumpHeader .= '-- Date: ' . date('Y-m-d H:i:s') . PHP_EOL;
			$dumpHeader .= '-- Triggered by: `artisan db:save s`' . PHP_EOL;
			$dumpHeader .= '--==================================' . PHP_EOL;
			$dumpHeader .= PHP_EOL . PHP_EOL;
			fwrite($fh, $dumpHeader);
			
			$results = DB::select("select table_name from information_schema.tables where table_schema = ?", [$dbName]);
			// $this->info(print_r($results, true));
			
			foreach ($results as $result){
				$tableName = $result->table_name;
				$showcreate = DB::select("show create table `$tableName`");
				// $this->info(print_r($showcreate, true));
				fwrite($fh, $showcreate[0]->{'Create Table'});
				fwrite($fh, PHP_EOL . PHP_EOL);
			}
			
			fclose($fh);
			$this->info("File saved to $relativeFilePath.");
		}
    }
}
