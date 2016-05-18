<?php

namespace ElmarHinz\Tests\Unit\TYPO3Benchmark;

use ElmarHinz\TYPO3Benchmark\FormatTraveler as Traveler;
use ElmarHinz\TYPO3Benchmark\TimeRange as TimeRange;

require_once("vendor/autoload.php");

class FormatTravelerTest extends \PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$this->traveler = new Traveler();
	}

	/**
	 * @test
	 */
	public function construct()
	{
		$this->assertInstanceOf('\ElmarHinz\TYPO3Benchmark\FormatTraveler', $this->traveler);
	}

	/**
	 * @test
	 */
	public function formatWithGaps()
	{
		$this->traveler->enableGaps();
		$this->traveler->onDown($this->mockNode('A', 0.0, 0.222, true));
		$this->traveler->onDown($this->mockNode('AA', 0.0, 0.111));
		$this->traveler->onUp(Null);
		$this->traveler->onDown($this->mockNode('AB', 0.111, 0.111));
		$this->traveler->onUp(Null);
		$this->traveler->onUp(Null);
		$this->traveler->onDown($this->mockNode('B', 0.222, 0.333));
		$this->traveler->onUp(Null);
		$expect = '
Offset    Duration              Name
----------------------------------------------------------------------
 0.00000   0.22200              A
 0.00000     0.11100              AA
 0.99900     0.99900              [GAP]
 0.11100     0.11100              AB
 0.22200   0.33300              B
';
		$this->assertSame($expect, $this->traveler->getOutput());
	}

	/**
	 * @test
	 */
	public function format()
	{
		$this->traveler->onDown($this->mockNode('A', 0.0, 0.222, true));
		$this->traveler->onDown($this->mockNode('AA', 0.0, 0.111));
		$this->traveler->onUp(Null);
		$this->traveler->onDown($this->mockNode('AB', 0.111, 0.111));
		$this->traveler->onUp(Null);
		$this->traveler->onUp(Null);
		$this->traveler->onDown($this->mockNode('B', 0.222, 0.333));
		$this->traveler->onUp(Null);
		$expect = '
Offset    Duration              Name
----------------------------------------------------------------------
 0.00000   0.22200              A
 0.00000     0.11100              AA
 0.11100     0.11100              AB
 0.22200   0.33300              B
';
		$this->assertSame($expect, $this->traveler->getOutput());
	}

	protected function mockNode($name, $offset, $duration, $isRoot = false)
	{
		$node = $this->getMockBuilder('\\ElmarHinz\\TYPO3Benchmark\\TimeRange')->getMock();
		$node->method('isRoot')->willReturn($isRoot);
		$node->method('getTimeOffset')->willReturn($offset);
		$node->method('getDuration')->willReturn($duration);
		$node->method('getName')->willReturn($name);
		if($name == 'AB') {
			$distance = $this->mockNode('[GAP]', 0.999, 0.999);
			$node->method('getDistanceToNeighbourBefore')->willReturn($distance);
		} else {
			$node->method('getDistanceToNeighbourBefore')->willReturn(Null);
		}
		return $node;
	}

}

