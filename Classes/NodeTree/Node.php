<?php

namespace ElmarHinz\NodeTree;

require_once("vendor/autoload.php");

class Node
{
	protected $parent = Null;
	protected $children = [];
	protected $name = '';

	public function setName($name)
	{
		$this->name = $name;
	}

	public function getName()
	{
		return $this->name;
	}

	public function setParent($node)
	{
		$this->parent = $node;
	}

	public function getParent()
	{
		return $this->parent;
	}

	public function appendChild($node)
	{
		$this->children[] = $node;
	}

	public function getChildren()
	{
		return $this->children;
	}

	public function travel($traveler)
	{
		$traveler->onDown($this);
		foreach($this->children as $child) {
			$child->travel($traveler);
		}
		$traveler->onUp($this);
		return $traveler;
	}
}

