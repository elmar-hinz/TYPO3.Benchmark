<?php

namespace ElmarHinz;

class TYPO3Benchmark
{
	static protected $trackPoints = array();
	static protected $trackTreeFile = '/tmp/trackTree.txt';
	static protected $logFile = '/tmp/trackPoints.txt';
	static protected $handle = Null;

	/**
	 * Register time Tracker
	 *
	 * Registers the time tracker as drop in replacement
	 * for the original time trakcer.
	 */
	static public function registerTimeTracker()
	{
         $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects']['TYPO3\\CMS\\Core\\TimeTracker\\TimeTracker']['className'] = '\\ElmarHinz\\TYPO3Benchmark\\TimeTracker';
	}

	/**
	 * Finish time tracking
	 *
	 * @param timetracker The timeTree to store
	 */
	static public function finishTimeTracking($timeTracker)
	{
		self::writeTrackTree($timeTracker->getTree());
	}

	/**
	 * Write track tree
	 *
	 * Serializes the track tree and writes it to the
	 * temporary file for interprocess transfer.
	 *
	 * @param trackTree The track tree.
	 */
	static public function writeTrackTree($trackTree)
	{
		$string = serialize($trackTree);
		file_put_contents(self::$trackTreeFile, $string);
	}

	/**
	 * Report
	 *
	 * Read track tree, format and write report.
	 *
	 * Reads the tempoary file for interprocess transfer
	 * and unserialise the content to tree.
	 */
	public function report($file)
	{
		$tree = $this->readTrackTree();
		$output = $this->formatTrackTree($tree);
		$this->write($output, $file);
	}

	/**
	 * Read track tree
	 *
	 * Reads the tempoary file for interprocess transfer
	 * and unserialise the content to tree.
	 *
	 * @return trackTree The track tree.
	 */
	public function readTrackTree()
	{
		$string = file_get_contents(self::$trackTreeFile);
		if(!$string) {
			throw new \Exception("Could not read " . self::$trackTreeFile);
		}
		return unserialize($string);
	}

	/**
	 * Format track tree
	 *
	 * Format the track tree as RST
	 */
	public function formatTrackTree($trackTree)
	{
		$traveler = new \ElmarHinz\TYPO3Benchmark\FormatTraveler();
		$traveler->enableGaps();
		$trackTree->travel($traveler);
		return $traveler->getOutput();
	}

	/**
	 * Write
	 */
	public function write($report, $file)
	{
		file_put_contents($file, $report);
	}

}

