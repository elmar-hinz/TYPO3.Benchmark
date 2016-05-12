<?php

namespace ElmarHinz\Tests\Benchmark;

use ElmarHinz\TYPO3Benchmark as Benchmark;
use ElmarHinz\Tests\BenchmarkTestCase;
use TYPO3\CMS\Core\Database\DatabaseConnection;
use TYPO3\CMS\Core\Utility\GeneralUtility as Utility;

require_once("vendor/autoload.php");

class BenchmarkTest extends BenchmarkTestCase
{
	protected $testExtensionsToLoad = [ 'typo3conf/ext/ehfaq' ];
	protected $reportFile = 'Reports/report.rst';

    public function setUp()
    {
        parent::setUp();
        $this->importDataSet(
            'typo3/sysext/core/Tests/Functional/Fixtures/pages.xml');
        $this->setUpFrontendRootPage(1,
		  ['EXT:ehfaq/Tests/Functional/Fixtures/Hello.ts']);
		$this->benchmark = new Benchmark();
    }

    /**
     * @test
     */
    public function main()
    {
        $this->getFrontendResponse(1);
		Benchmark::stopTracking();
		$this->benchmark->report($this->reportFile);
	}


}

