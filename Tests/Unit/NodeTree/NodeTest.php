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
		$this->node->setName('name');
		$this->assertSame('name', $this->node->getName());

		$parent = new Node();
		$child1 = new Node();
		$child2 = new Node();
		$parent->appendChild($child1);
		$parent->appendChild($child2);
		$this->assertSame([$child1, $child2], $parent->getChildren());
		$this->assertSame($parent, $parent->getRoot());
		$this->assertSame($parent, $child1->getRoot());
		$this->assertNull($parent->getParent());
		$this->assertSame($parent, $child1->getParent());
		$this->assertSame($parent, $child2->getParent());
		$this->assertTrue($parent->isRoot());
		$this->assertFalse($child1->isRoot());
		$this->assertFalse($child2->isRoot());
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

	/**
	 * @test
	 */
	public function getNeighbourBefore()
	{
		$parent = $this->node;
		$child1 = new Node();
		$child1->setName("child1");
		$child2 = new Node();
		$child2->setName("child2");
		$parent->appendChild($child1);
		$parent->appendChild($child2);
		$this->assertNull($parent->getNeighbourBefore());
		$this->assertNull($child1->getNeighbourBefore());
		$this->assertSame($child1, $child2->getNeighbourBefore());
	}

	/**
	 * @test
	 */
	public function getFirstAndLastChild()
	{
		$parent = $this->node;
		$child1 = new Node();
		$child1->setName("child1");
		$child2 = new Node();
		$child2->setName("child2");
		$parent->appendChild($child1);
		$parent->appendChild($child2);
		$this->assertSame($child1, $parent->getFirstChild());
		$this->assertSame($child2, $parent->getLastChild());
		$this->assertNull($child1->getFirstChild());
		$this->assertNull($child1->getLastChild());
	}
}

