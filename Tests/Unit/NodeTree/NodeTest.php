<?php

namespace ElmarHinz\Tests\Unit\NodeTree;

use ElmarHinz\NodeTree\Node as Node;

require_once("vendor/autoload.php");

class NodeTest extends \PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$this->node = new Node();
	}

	/**
	 * @test
	 */
	public function construct()
	{
		$this->assertInstanceOf('\ElmarHinz\NodeTree\Node', $this->node);
	}

	/**
	 * @test
	 */
	public function setGet()
	{
		$parent = new Node();
		$child1 = new Node();
		$child2 = new Node();
		$this->node->setName('name');
		$this->node->setParent($parent);
		$this->node->appendChild($child1);
		$this->node->appendChild($child2);
		$this->assertSame('name', $this->node->getName());
		$this->assertSame($parent, $this->node->getParent());
		$this->assertSame([$child1, $child2], $this->node->getChildren());
	}

	/**
	 * @test
	 */
	public function travelNode()
	{
		$traveler = $this->getMockBuilder('TravelerInterface')
			->setMethods(['onUp', 'onDown'])->getMock();
		$traveler->expects($this->exactly(1))->method('onUp');
		$traveler->expects($this->exactly(1))->method('onDown');
		$this->node->travel($traveler);
	}

	/**
	 * @test
	 */
	public function travelTree()
	{
		$parent = $this->node;
		$parent->setName("parent");
		$child1 = new Node();
		$parent->setName("child1");
		$child2 = new Node();
		$parent->setName("child2");
		$parent->appendChild($child1);
		$parent->appendChild($child2);
		$traveler = $this->getMockBuilder('TravelerInterface')
			->setMethods(['onUp', 'onDown'])->getMock();
		$traveler->expects($this->exactly(3))->method('onDown')
			->withConsecutive(
				[$this->identicalTo($parent)],
				[$this->identicalTo($child1)],
				[$this->identicalTo($child2)]
			);
		$traveler->expects($this->exactly(3))->method('onUp')
			->withConsecutive(
				[$this->identicalTo($child1)],
				[$this->identicalTo($child2)],
				[$this->identicalTo($parent)]
			);
		$parent->travel($traveler);
	}

	/**
	 * @test
	 */
	public function hibernate()
	{
		$parent = $this->node;
		$parent->setName("parent");
		$child1 = new Node();
		$parent->setName("child1");
		$pack = serialize($parent);
		$this->assertEquals($parent, unserialize($pack));
	}
}

