<?php

namespace ElmarHinz\Tests\Unit;

use ElmarHinz\TYPO3Benchmark as Benchmark;
use ElmarHinz\TYPO3Benchmark\TimeRange as TimeRange;

/* require_once("vendor/autoload.php"); */

class TYPO3BenchmarkTest extends \PHPUnit_Framework_TestCase
{
	protected $testReportFile = "/tmp/testReport.txt";

    public function setUp()
    {
		$this->tearDown();
		$this->benchmark = new Benchmark();
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
		$this->assertEquals('ElmarHinz\TYPO3Benchmark\TimeRange', TimeRange::class);
	}

	/**
	 * @test
	 */
	public function registerTimeTracker()
	{
		Benchmark::registerTimeTracker();
		$this->assertTrue(isset($GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects']['TYPO3\\CMS\\Core\\TimeTracker\\TimeTracker']['className']));
		$expect = '\\ElmarHinz\\TYPO3Benchmark\\TimeTracker';
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
		Benchmark::writeTrackTree($tree);
		$actual = $this->benchmark->readTrackTree();
		$this->assertEquals($tree, $actual);
	}

	/**
	 * @test
	 */
	public function track()
	{
		Benchmark::track('p1');
		usleep(1000);
		Benchmark::track('p2');
		usleep(1000);
		Benchmark::track('p3');
		Benchmark::stopTracking();
		$points = $this->benchmark->getTrackPoints();
		$this->assertSame(['p1','p2','p3'], array_values($points));
	}

	/**
	 * @test
	 */
	public function evaluateTrack()
	{
		$input = [
			10000 => 'alpha',
			20000 => 'beta',
			40000 => 'gamma',
		];
		$expectation = [
			[0, 0, 'alpha'],
			[10000, 10000, 'beta'],
			[30000, 20000, 'gamma'],
		];
		$actual = $this->benchmark->evaluateTrack($input);
		$this->assertSame($expectation, $actual);
	}

	/**
	 * @test
	 */
	public function format()
	{
		$input = [
			[10000, 0, 'alpha'],
			[20000, 10000, 'beta'],
			[40000, 20000, 'gamma'],
		];
		$actual = $this->benchmark->format($input);
		$this->assertContains('beta', $actual);
		$this->assertContains('20000', $actual);
		$this->assertContains('10000', $actual);
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

    /**
     * @test
     */
    public function trackAndReport()
	{
		Benchmark::track('p1');
		usleep(1000);
		Benchmark::track('p2');
		usleep(1000);
		Benchmark::stopTracking();
		$this->benchmark->report($this->testReportFile);
		$actual = file_get_contents($this->testReportFile);
		$this->assertContains('p2', $actual);
		$this->assertContains('p1', $actual);
	}
}

