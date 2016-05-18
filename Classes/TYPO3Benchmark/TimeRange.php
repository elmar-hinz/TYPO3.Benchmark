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

	public function start()
	{
		$this->startTime = microtime(true);
	}

	public function getStartTime()
	{
		return $this->startTime;
	}

	public function stop()
	{
		$this->stopTime = microtime(true);
	}

	public function getStopTime()
	{
		return $this->stopTime;
	}

}

