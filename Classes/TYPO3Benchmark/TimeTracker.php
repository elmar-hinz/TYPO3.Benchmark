<?php

namespace ElmarHinz\TYPO3Benchmark;

/* require_once("vendor/autoload.php"); */

use TYPO3\CMS\Core\TimeTracker\TimeTracker as CoreTimeTracker;
use ElmarHinz\TYPO3Benchmark\TimeRange;

class TimeTracker extends CoreTimeTracker
{
	protected $root = Null;
	protected $current = Null;

	public function __construct()
	{
		$this->root = new TimeRange();
		$this->root->setName('root');
		$this->current = $this->root;
		$this->current->start();
	}

    /**
     * Pushes an element to the TypoScript tracking array
     *
     * @param string Label string for the entry, eg. TypoScript property name
     * @param string Additional value(?)
     * @return void
     */
    public function push($label, $value = '')
    {
		$child = new TimeRange();
		$child->setName($label);
		$child->setRemark($value);
		$this->current->appendChild($child);
		$this->current = $child;
		$this->current->start();
    }

    /**
     * Pulls an element from the TypoScript tracking array
     *
     * @param string The content string generated within the push/pull part.
     * @return void
     */
    public function pull($content = '')
    {
		$this->current->stop();
		$this->current = $this->current->getParent();
    }

	public function getTree()
	{
		$this->root->stop();
		return $this->root;
	}

	// Stubs for currently non-intersting function calls
    public function start() { }
    public function setTSselectQuery(array $data, $msg = '') { }
    public function setTSlogMessage($content, $num = 0) { }
    public function printTSlog() { }
    public function incStackPointer() { }
    public function decStackPointer() { }
    public function getMilliseconds($microtime = null) { }
}
