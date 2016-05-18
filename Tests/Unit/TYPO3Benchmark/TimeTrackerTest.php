<?php

namespace ElmarHinz\Tests\Unit\TYPO3Benchmark;

use ElmarHinz\TYPO3Benchmark\TimeTracker as Tracker;

require_once("vendor/autoload.php");

class TimeTrackerTest extends \PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$this->tracker = new Tracker();
	}

	/**
	 * @test
	 */
	public function construct()
	{
		$this->assertInstanceOf('\ElmarHinz\TYPO3Benchmark\TimeTracker', $this->tracker);
		$this->assertInstanceOf('\ElmarHinz\TYPO3Benchmark\TimeRange',
		$this->tracker->getTree());
	}

	/**
	 * @test
	 */
	public function singleRange()
	{
		$this->tracker->push('one');
		usleep(1000);
		$this->tracker->pull();
		$range = $this->tracker->getTree()->getChildren()[0];
		$duration = $range->getDuration();
		$this->assertInternalType('float', $duration);
		$this->assertGreaterThan(0, $duration);
	}

	/**
	 * @test
	 */
	public function neighbours()
	{
		$this->tracker->push('one');
		usleep(1000);
		$this->tracker->pull();
		$this->tracker->push('two');
		usleep(1000);
		$this->tracker->pull();
		$children = $this->tracker->getTree()->getChildren();
		$this->assertSame('one', $children[0]->getName());
	}

	/**
	 * @test
	 */
	public function parentChild()
	{
		$this->tracker->push('parent');
		usleep(1000);
		$this->tracker->push('child');
		usleep(1000);
		$this->tracker->pull();
		$this->tracker->pull();
		$parent = $this->tracker->getTree()->getChildren()[0];
		$child = $parent->getChildren()[0];
		$this->assertSame('parent', $parent->getName());
		$this->assertSame('child', $child->getName());
		$this->assertGreaterThan(1 , 2);
		$this->assertGreaterThan($child->getDuration(), $parent->getDuration());
	}
}


