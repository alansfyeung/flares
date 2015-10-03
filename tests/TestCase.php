<?php

class TestCase extends Illuminate\Foundation\Testing\TestCase
{
    /**
     * The base URL to use while testing the application.
     *
     * @var string
     */
    protected $baseUrl = 'http://localhost';

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        return $app;
    }
	
	/**
     * Randomly generate some new records
     *
	 * @param howMany
	 * @param model The class to be factoried
     * @return Array of {$this->model}
     */
	protected function newRecords($howMany = 3, $model = false){
		if (!$model){
			$model = $this->model;
		}
		$records = factory($model, $howMany)->make()->toArray();
		return $records;
	}
	
}
