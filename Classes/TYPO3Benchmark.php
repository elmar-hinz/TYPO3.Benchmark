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
		$trackTree->travel($traveler);
		return $traveler->getOutput();
	}

	public function reportTrackTree($file)
	{
		$tree = $this->readTrackTree();
		$output = $this->formatTrackTree($tree);
		$this->write($output, $file);
	}

	/**
	 * Track a moment in time
	 *
	 * @param float	The timestamp
	 * @param string Point of track
	 * @return void
	 */
	static public function track($trackpoint)
	{
		$timestamp = (microtime(true) * 1000);
		$out = sprintf("%f:%s\n", $timestamp, $trackpoint);
		if(!self::$handle) {
			self::$handle = fopen(self::$logFile, 'a');
		}
		if(self::$handle) {
			fwrite(self::$handle, $out);
		}
	}

	static public function stopTracking()
	{
		if(self::$handle) {
			fclose(self::$handle);
			self::$handle = Null;
		}
	}

	/**
	 * Report
	 *
	 * @param str	Filepath to write to.
	 * @return void
	 */
	public function report($file)
	{
		$results = $this->getTrackPoints();
		$results = $this->evaluateTrack($results);
		$results = $this->format($results);
		$this->write($results, $file);
	}

	public function evaluateTrack($trackPoints)
	{
		if(empty($trackPoints)) return [];
		$start = (int)round(array_keys($trackPoints)[0], 0);
		$previous = $start;
		$results = [];
		foreach($trackPoints as $time => $title) {
			$time = (int)round($time, 0);
			$current = $time - $start;
			$duration = $time - $previous;
			$results[] = [$current, $duration, $title];
			$previous = $time;
		}
		return $results;
	}

	public function getTrackPoints(){
		$trackPoints = [];
		foreach(file(self::$logFile) as $line) {
			$parts = explode(':', trim($line));
			$trackPoints[$parts[0]] = $parts[1];
		}
		unlink(self::$logFile);
		return $trackPoints;
	}

	/**
	 * Format
	 */
	public function format($results)
	{
		$format = "\n * %5d %5d %s";
		$out  = "\nReport";
		$out .= "\n======";
		foreach($results as $result) {
			$out .= sprintf($format, $result[0], $result[1], $result[2]);
		}
		return $out;
	}

	/**
	 * Write
	 */
	public function write($report, $file)
	{
		file_put_contents($file, $report);
	}

}

