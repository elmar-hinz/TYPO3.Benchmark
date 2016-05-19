<?php

namespace ElmarHinz\Tests\Benchmark;

use ElmarHinz\T3TimeTracking as Tracking;
use ElmarHinz\Tests\BenchmarkTestCase;
use TYPO3\CMS\Core\Database\DatabaseConnection;

require_once("vendor/autoload.php");

class TimeTrackingTest extends BenchmarkTestCase
{
	protected $testExtensionsToLoad = [ 't3bench' ];
	protected $reportFile = 'Reports/timeTracking.rst';

    public function setUp()
    {
		parent::setUp();
        $this->importDataSet(
            'typo3/sysext/core/Tests/Functional/Fixtures/pages.xml');
        $this->setUpFrontendRootPage(1,
		  ['EXT:t3bench/hello.ts']);
		$this->tracking = new Tracking();
    }

    /**
     * @test
     */
    public function timeTracker()
    {
		$this->getFrontendResponse(1);
		$this->tracking->report($this->reportFile);
	}

}

