<?php

namespace ElmarHinz\TYPO3Benchmark;

class FormatTraveler implements \ElmarHinz\NodeTree\TravelerInterface
{
	protected $level = 0;
	protected $out;

	public function onDown($node)
	{
		$start = $node->getStartTime();
		$indent = 2 * $this->level;
		$duration = $node->getDuration();
		$name = $node->getName();
		$format = "\n%'09.4f %' ".$indent."s %09.4f              %s";
		$this->out .= sprintf($format, $start, '', $duration, $name);
		$this->level++;
	}

	public function onUp($node)
	{
		$this->level--;
	}

	public function getOutput()
	{
		return $this->out;
	}

}

