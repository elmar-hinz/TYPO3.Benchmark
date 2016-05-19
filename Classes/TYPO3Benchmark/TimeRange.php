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

	public function getDistanceFromLastChild()
	{
		$child = $this->getLastChild();
		if (!$child) {
			return Null;
		} else {
			return $this->getDistance('[GAP]',
				$child->getStopTime(), $this->getStopTime());
		}
	}

	public function getDistanceToFirstChild()
	{
		$child = $this->getFirstChild();
		if (!$child) {
			return Null;
		} else {
			return $this->getDistance('[GAP]',
				$this->getStartTime(), $child->getStartTime());
		}
	}

	public function getDistanceToNeighbourBefore()
	{
		$neighbour = $this->getNeighbourBefore();
		if (!$neighbour) {
			return Null;
		} else {
			return $this->getDistance('[GAP]',
				$neighbour->getStopTime(), $this->getStartTime());
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

	protected function getDistance($name, $begin, $end)
	{
			$range = new TimeRange();
			$range->setName($name);
			$range->setStartTime($begin);
			$range->setStopTime($end);
			return $range;
	}

}

