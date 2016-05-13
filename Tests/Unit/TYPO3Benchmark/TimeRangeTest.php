<?php

namespace ElmarHinz\Tests\Unit\TYPO3Benchmark;

use ElmarHinz\TYPO3Benchmark\TimeRange as TimeRange;

require_once("vendor/autoload.php");

class TimeRangeTest extends \PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$this->node = new TimeRange();
	}

	/**
	 * @test
	 */
	public function construct()
	{
		$this->assertInstanceOf('\ElmarHinz\TYPO3Benchmark\TimeRange', $this->node);
	}

	/**
	 * @test
	 */
	public function setGet()
	{
		$this->node->setRemark('remark');
		$this->assertSame('remark', $this->node->getRemark());
	}

	/**
	 * @test
	 */
	public function times()
	{
		$this->node->start();
		$this->node->stop();
		$startTime = $this->node->getStartTime();
		$stopTime = $this->node->getStopTime();
		$duration = $this->node->getDuration();
		$this->assertInternalType('float', $startTime);
		$this->assertInternalType('float', $stopTime);
		$this->assertInternalType('float', $duration);
		$this->assertTrue($duration > 0);
		$this->assertSame($duration, $stopTime - $startTime);
	}

}

