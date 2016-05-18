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
		usleep(1000);
		$this->node->stop();
		$startTime = $this->node->getStartTime();
		$stopTime = $this->node->getStopTime();
		$duration = $this->node->getDuration();
		$this->assertInternalType('float', $startTime);
		$this->assertInternalType('float', $stopTime);
		$this->assertInternalType('float', $duration);
		$this->assertGreaterThan(0.001, $duration);
		$this->assertLessThan(0.002, $duration);
		$this->assertSame($stopTime - $startTime, $duration);
	}

	/**
	 * @test
	 */
	public function timeOffset()
	{
		$root = new TimeRange();
		$child = new TimeRange();
		$root->start();
		usleep(1000);
		$child->start();
		$root->appendChild($child);
		$this->assertLessThan(0.000001, $root->getTimeOffset());
		$this->assertGreaterThan(0.001, $child->getTimeOffset());
		$this->assertLessThan(0.002, $child->getTimeOffset());
	}

	/**
	 * @test
	 */
	public function getDistanceToChildren()
	{
		$root = new TimeRange();
		$child1 = new TimeRange();
		$child2 = new TimeRange();
		$root->appendChild($child1);
		$root->appendChild($child2);
		$root->start();
		usleep(1000);
		$child1->start();
		$child1->stop();
		$child2->start();
		$child2->stop();
		usleep(1000);
		$root->stop();
		$this->assertNull($child1->getDistanceToFirstChild());
		$node = $root->getDistanceToFirstChild();
		$this->assertInstanceOf('\\ElmarHinz\\TYPO3Benchmark\\TimeRange', $node);
		$this->assertGreaterThan(0.001, $node->getDuration());
		$this->assertLessThan(0.002, $node->getDuration());
		$this->assertSame('[GAP]', $node->getName());
		$node = $root->getDistanceFromLastChild();
		$this->assertInstanceOf('\\ElmarHinz\\TYPO3Benchmark\\TimeRange', $node);
		$this->assertGreaterThan(0.001, $node->getDuration());
		$this->assertLessThan(0.002, $node->getDuration());
		$this->assertSame('[GAP]', $node->getName());
	}

	/**
	 * @test
	 */
	public function getDistanceToNeighbourBefore()
	{
		$root = new TimeRange();
		$child1 = new TimeRange();
		$child2 = new TimeRange();
		$root->appendChild($child1);
		$root->start();
		$child1->start();
		$child1->stop();
		usleep(1000);
		$root->appendChild($child2);
		$child2->start();
		$child2->stop();
		$root->stop();
		$this->assertNull($root->getDistanceToNeighbourBefore());
		$this->assertNull($child1->getDistanceToNeighbourBefore());
		$nb = $child2->getDistanceToNeighbourBefore();
		$this->assertInstanceOf('\\ElmarHinz\\TYPO3Benchmark\\TimeRange', $nb);
		$this->assertGreaterThan(0.001, $nb->getDuration());
		$this->assertLessThan(0.002, $nb->getDuration());
		$this->assertSame('[GAP]', $nb->getName());
	}
}

