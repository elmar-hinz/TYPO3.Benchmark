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
		usleep(5000);
		Benchmark::track('p2');
		usleep(5000);
		Benchmark::track('p3');
		Benchmark::stopTracking();
		$points = $this->Benchmark->getTrackPoints();
		/* $this->assertContains('p1', $points); */
		$this->assertSame(
			['p1','p2','p3'], array_values($points));
	}

	/**
	 * @test
	 */
	public function format()
	{
		$results = [
			12345 => 'alpha',
			24345 => 'beta',
		];
		$expected = "xxx";
		$actual = $this->Benchmark->format($results);
		$this->assertContains('alpha', $actual);
		$this->assertContains('beta', $actual);
		$this->assertContains('24345', $actual);
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
		Benchmark::track('p2');
		Benchmark::stopTracking();
		$this->Benchmark->report($this->testReportFile);
		$actual = file_get_contents($this->testReportFile);
		$this->assertContains('p2', $actual);
		$this->assertContains('p1', $actual);
	}

}

