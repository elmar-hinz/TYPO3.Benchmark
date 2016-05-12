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
		$timestamp = round(microtime(true) * 10000);
		$out = sprintf("%s:%s\n", $timestamp, $trackpoint);
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
		$report = $this->format($this->getTrackPoints());
		$this->write($report, $file);
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
		$format = "\n * %d %s";
		$out  = "\nReport";
		$out .= "\n======";
		foreach($results as $stamp => $point) {
			$out .= sprintf($format, $stamp, $point);
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

