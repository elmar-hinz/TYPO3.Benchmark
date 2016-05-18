<?php

namespace ElmarHinz\TYPO3Benchmark;

class FormatTraveler implements \ElmarHinz\NodeTree\TravelerInterface
{
	protected $level = 0;
	protected $out;
	protected $header = '
Offset    Duration              Name
----------------------------------------------------------------------
';
	protected $enableGaps = false;
	protected $minimumGapLength = 0.01;

	public function enableGaps()
	{
		$this->enableGaps = true;
	}

	public function onDown($node)
	{
		if($node->isRoot())
			$this->out = $this->header;
		if($this->enableGaps) {
			$distance = $node->getDistanceToNeighbourBefore();
			if($distance && $distance->getDuration() > $this->minimumGapLength)
				$this->addEntry($distance);
		}
		$this->addEntry($node);
		$this->level++;
		if($this->enableGaps) {
			$distance = $node->getDistanceToFirstChild();
			if($distance && $distance->getDuration() > $this->minimumGapLength)
				$this->addEntry($distance);
		}
	}

	protected function addEntry($node)
	{
		$indent = 2 * $this->level;
		$format = "%' 8.5f %' ".$indent."s %' 8.5f              %s\n";
		$offset = $node->getTimeOffset();
		$duration = $node->getDuration();
		$name = $node->getName();
		$this->out .= sprintf($format, $offset, '', $duration, $name);
	}

	public function onUp($node)
	{
		if($this->enableGaps) {
			$distance = $node->getDistanceFromLastChild();
			if($distance && $distance->getDuration() > $this->minimumGapLength)
				$this->addEntry($distance);
		}
		$this->level--;
	}

	public function getOutput()
	{
		return $this->out;
	}

}

