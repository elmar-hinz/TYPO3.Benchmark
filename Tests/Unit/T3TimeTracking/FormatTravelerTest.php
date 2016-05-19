<?php

namespace ElmarHinz\Tests\Unit\T3TimeTracking;

use ElmarHinz\T3TimeTracking\FormatTraveler as Traveler;
use ElmarHinz\T3TimeTracking\TimeRange as TimeRange;

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
		$this->assertInstanceOf('\ElmarHinz\T3TimeTracking\FormatTraveler', $this->traveler);
	}

	/**
	 * @test
	 */
	public function formatWithGaps()
	{
		$mockA = $this->mockNode('A', 0.0, 0.222, true);
		$mockAA = $this->mockNode('AA', 0.0, 0.111);
		$mockAB = $this->mockNode('AB', 0.111, 0.111);
		$mockB = $this->mockNode('B', 0.222, 0.333);
		$this->traveler->enableGaps();
		$this->traveler->onDown($mockA);
		$this->traveler->onDown($mockAA);
		$this->traveler->onUp($mockAA);
		$this->traveler->onDown($mockAB);
		$this->traveler->onUp($mockAB);
		$this->traveler->onUp($mockA);
		$this->traveler->onDown($mockB);
		$this->traveler->onUp($mockB);
		$expect = '
    Offset    Duration              Name
    ----------------------------------------------------------------------
     0.00000   0.22200              A
     1.00000     1.00000              [GAP]
     0.00000     0.11100              AA
     2.00000     2.00000              [GAP]
     0.11100     0.11100              AB
     3.00000     3.00000              [GAP]
     0.22200   0.33300              B
';
		$this->stringEndsWith($expect, $this->traveler->getOutput());
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
		$this->stringEndsWith($expect, $this->traveler->getOutput());
	}

	protected function mockNode($name, $offset, $duration, $isRoot = false)
	{
		$node = $this->getMockBuilder('\\ElmarHinz\\T3TimeTracking\\TimeRange')->getMock();
		$node->method('isRoot')->willReturn($isRoot);
		$node->method('getTimeOffset')->willReturn($offset);
		$node->method('getDuration')->willReturn($duration);
		$node->method('getName')->willReturn($name);
		if($name == 'A') {
			$distance = $this->mockNode('[GAP]', 1.0, 1.0);
			$node->method('getDistanceToFirstChild')->willReturn($distance);
		} else {
			$node->method('getDistanceToFirstChild')->willReturn(Null);
		}
		if($name == 'AB') {
			$distance = $this->mockNode('[GAP]', 2.0, 2.0);
			$node->method('getDistanceToNeighbourBefore')->willReturn($distance);
		} else {
			$node->method('getDistanceToNeighbourBefore')->willReturn(Null);
		}
		if($name == 'A') {
			$distance = $this->mockNode('[GAP]', 3.0, 3.0);
			$node->method('getDistanceFromLastChild')->willReturn($distance);
		} else {
			$node->method('getDistanceFromLastChild')->willReturn(Null);
		}
		return $node;
	}

}

