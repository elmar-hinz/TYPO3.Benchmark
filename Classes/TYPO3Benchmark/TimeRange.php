<?php

namespace ElmarHinz\TYPO3Benchmark;

/* require_once("vendor/autoload.php"); */

class TimeRange extends \ElmarHinz\NodeTree\Node
{
	protected $remark = '';
	protected $startTime = 0;
	protected $stopTime = 0;

	public function setRemark($remark)
	{
		$this->remark = $remark;
	}

	public function getRemark()
	{
		return $this->remark;
	}

	public function getDuration()
	{
		return $this->stopTime - $this->startTime;
	}

	public function getTimeOffset()
	{
		return $this->startTime - $this->getRoot()->getStartTime();
	}

	public function setStartTime($time)
	{
		$this->startTime = $time;
	}

	public function getStartTime()
	{
		return $this->startTime;
	}

	public function setStopTime($time)
	{
		$this->stopTime = $time;
	}

	public function getStopTime()
	{
		return $this->stopTime;
	}

	public function getDistanceToNeighbourBefore()
	{
		$neighbour = $this->getNeighbourBefore();
		if (!$neighbour) {
			return Null;
		} else {
			$range = new TimeRange();
			$range->setName('[GAP]');
			$range->setStartTime($neighbour->getStopTime());
			$range->setStopTime($this->getStartTime());
			return $range;
		}
	}

	public function start()
	{
		$this->startTime = microtime(true);
	}

	public function stop()
	{
		$this->stopTime = microtime(true);
	}

}

