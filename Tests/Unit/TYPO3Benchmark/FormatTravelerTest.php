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
	public function format()
	{
		$this->traveler->onDown($this->mockNode('A', 0.0, 222.222));
		$this->traveler->onDown($this->mockNode('AA', 0.0, 111.111));
		$this->traveler->onUp(Null);
		$this->traveler->onDown($this->mockNode('AB', 111.111, 111.111));
		$this->traveler->onUp(Null);
		$this->traveler->onUp(Null);
		$this->traveler->onDown($this->mockNode('B', 222.222, 333.333));
		$this->traveler->onUp(Null);
		$expect = '
0000.0000  0222.2220              A
0000.0000    0111.1110              AA
0111.1110    0111.1110              AB
0222.2220  0333.3330              B';
		$this->assertSame($expect, $this->traveler->getOutput());
	}

	protected function mockNode($name, $startTime, $duration)
	{
		$node = $this->getMockBuilder('\\ElmarHinz\\TYPO3Benchmark\\TimeRange')->getMock();
		$node->method('getName')->willReturn($name);
		$node->method('getStartTime')->willReturn($startTime);
		$node->method('getDuration')->willReturn($duration);
		return $node;
	}

}

