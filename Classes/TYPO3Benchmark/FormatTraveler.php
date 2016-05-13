<?php

namespace ElmarHinz\TYPO3Benchmark;

class FormatTraveler implements \ElmarHinz\NodeTree\TravelerInterface
{
	protected $level = 0;
	protected $out;

	public function onDown($object)
	{
		$this->level++;
		$this->out .= sprintf("%s\n", $this->level);
	}

	public function onUp($object)
	{
		$this->level--;
		$this->out .= sprintf("%s\n", $this->level);
	}

	public function getOutput()
	{
		return $this->out;
	}

}

