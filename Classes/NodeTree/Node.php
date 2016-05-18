<?php

namespace ElmarHinz\NodeTree;

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

	public function isRoot()
	{
		return ($this->parent === Null);
	}

	public function getRoot()
	{
		if($this->isRoot())
			return $this;
		else
			return $this->parent->getRoot();
	}

	public function appendChild($node)
	{
		$node->setParent($this);
		$this->children[] = $node;
	}

	public function getChildren()
	{
		return $this->children;
	}

	public function getFirstChild()
	{
		if($first = reset($this->children))
			return $first;
		else
			return Null;
	}

	public function getLastChild()
	{
		if($last = end($this->children))
			return $last;
		else
			return Null;
	}

	public function getNeighbourBefore()
	{
		if($this->isRoot()) {
			return Null;
		} else {
			$neighbours = $this->getParent()->getChildren();
			$key = array_search($this, $neighbours) - 1;
			if($key < 0)
				return Null;
			else
				return $neighbours[$key];
		}
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

