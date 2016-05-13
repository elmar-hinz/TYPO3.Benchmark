<?php

namespace ElmarHinz\Tests\Unit\TYPO3Benchmark;

use ElmarHinz\TYPO3Benchmark\FormatTraveler as Traveler;

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
	public function print()
	{
		$this->traveler->onDown(Null);
		$this->traveler->onDown(Null);
		$this->traveler->onUp(Null);
		$this->traveler->onDown(Null);
		$this->traveler->onUp(Null);
		$this->traveler->onUp(Null);
		$expect = "1\n2\n1\n2\n1\n0\n";
		$this->assertSame($expect, $this->traveler->getOutput());
	}


}

