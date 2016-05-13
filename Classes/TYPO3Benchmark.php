<?php

namespace ElmarHinz;

class TYPO3Benchmark
{
	static protected $trackPoints = array();
	static protected $logFile = '/tmp/trackPoints.txt';
	static protected $handle = Null;

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

