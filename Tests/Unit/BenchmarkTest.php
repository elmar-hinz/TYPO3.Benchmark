<?php

namespace ElmarHinz\Tests\Unit;

use ElmarHinz\TYPO3Benchmark as Benchmark;

require_once("vendor/autoload.php");

class BenchmarkTest extends \PHPUnit_Framework_TestCase
{
	protected $testReportFile = "/tmp/testReport.txt";

    public function setUp()
    {
		$this->tearDown();
		$this->Benchmark = new Benchmark();
    }

    public function tearDown()
    {
		if(file_exists($this->testReportFile)) unlink($this->testReportFile);
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
		$points = $this->Benchmark->getTrackPoints();
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
		$actual = $this->Benchmark->evaluateTrack($input);
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
		$actual = $this->Benchmark->format($input);
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
		$this->Benchmark->write($text, $this->testReportFile);
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
		$this->Benchmark->report($this->testReportFile);
		$actual = file_get_contents($this->testReportFile);
		$this->assertContains('p2', $actual);
		$this->assertContains('p1', $actual);
	}

}

