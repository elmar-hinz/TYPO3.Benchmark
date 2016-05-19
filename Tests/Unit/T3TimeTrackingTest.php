<?php

namespace ElmarHinz\Tests\Unit;

use ElmarHinz\T3TimeTracking as Tracking;
use ElmarHinz\T3TimeTracking\TimeRange as TimeRange;

/* require_once("vendor/autoload.php"); */

class TYPO3BenchmarkTest extends \PHPUnit_Framework_TestCase
{
	protected $testReportFile = "/tmp/testReport.txt";

    public function setUp()
    {
		$this->tearDown();
		$this->benchmark = new Tracking();
    }

    public function tearDown()
    {
		if(file_exists($this->testReportFile)) unlink($this->testReportFile);
	}

	/**
	 * @test
	 */
	public function classname()
	{
		$this->assertEquals('ElmarHinz\T3TimeTracking\TimeRange', TimeRange::class);
	}

	/**
	 * @test
	 */
	public function registerTimeTracker()
	{
		Tracking::registerTimeTracker();
		$this->assertTrue(isset($GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects']['TYPO3\\CMS\\Core\\TimeTracker\\TimeTracker']['className']));
		$expect = '\\ElmarHinz\\T3TimeTracking\\TimeTracker';
		$actual = $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects']['TYPO3\\CMS\\Core\\TimeTracker\\TimeTracker']['className'];
		$this->assertSame($expect, $actual);
	}

	/**
	 * @test
	 */
	public function writeReadTrackTree()
	{
		$tree = new TimeRange();
		$tree->setName('Name');
		Tracking::writeTrackTree($tree);
		$actual = $this->benchmark->readTrackTree();
		$this->assertEquals($tree, $actual);
	}

	/**
	 * @test
	 */
	public function write()
	{
		$text = "hello\n";
		$this->assertFalse(file_exists($this->testReportFile));
		$this->benchmark->write($text, $this->testReportFile);
		$this->assertTrue(file_exists($this->testReportFile));
		$this->assertEquals($text, file_get_contents($this->testReportFile));
	}

}

