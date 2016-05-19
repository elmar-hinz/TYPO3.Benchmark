<?php

namespace ElmarHinz\Tests\Benchmark;

function exception_error_handler($severity, $message, $file, $line) {
    throw new \ErrorException($message, 0, $severity, $file, $line);
}
set_error_handler("\\ElmarHinz\\Tests\\Benchmark\\exception_error_handler");

require_once("vendor/autoload.php");

/**
 * IMPORTANT: The test must to be run in isolation,
 *            else parsed classes are reused,
 *            with different results.
 *
 * vendor/bin/phpunit --process-isolation Tests/Benchmark/FileIncludeTest.php
 */
class FileIncludeTest extends \PHPUnit_Framework_TestCase
{
	protected $reportFile = 'Reports/requireFiles.rst';
	protected $coreClassPattern = 'typo3_src/typo3/sysext/core/Classes/**/*.php';
	protected $allClassPattern = 'typo3_src/typo3/sysext/*/Classes/**/*.php';
	protected $excludes = [
		'typo3_src/typo3/sysext/backend/Classes/View/PageTreeView.php',
	];
	protected $coreClasses = [];
	protected $allClasses = [];

	public function setUp()
	{
		$files = glob($this->coreClassPattern);
		foreach($files as $file) {
			if(!in_array($file, $this->excludes)) {
				$this->coreClasses[] = $file;
			}
		}
		$files = glob($this->allClassPattern);
		foreach($files as $file) {
			if(!in_array($file, $this->excludes)) {
				$this->allClasses[] = $file;
			}
		}
	}

	/**
	 * @test
	 */
	public function findFiles()
	{
		$this->assertGreaterThan(700, count($this->allClasses));
	}

	/**
	 * @test
	 */
	public function report()
	{
		// simple read
		$start = microtime(true);
		$lines = 0;
		foreach($this->allClasses as $file)
			$lines += count(file($file));
		$stop = microtime(true);
		$readDuration = round(1000 * ($stop - $start));
		// require_once
		$start = microtime(true);
		foreach($this->allClasses as $file) require_once($file);
		$stop = microtime(true);
		$requireDuration = round(1000 * ($stop - $start));
		$parseDuration = $requireDuration - $readDuration;
		$ratio = $parseDuration/$readDuration;
		$out  = "\n\n";
		$out .= "File I/O vs. PHP parsetime\n";
		$out .= "==========================\n";
		$out .= "\n::\n\n";
		$out .= sprintf("    glob: %s\n", $this->allClassPattern);
		$out .= sprintf("    files: %d\n", count($this->allClasses));
		$out .= sprintf("    lines: %d\n", $lines);
		$out .= "\n";
		$out .= sprintf("    file():           %' 3d milliseconds\n", $readDuration);
		$out .= sprintf("    require_once():   %' 3d milliseconds\n", $requireDuration);
		$out .= sprintf("    -> parsing PHP:   %' 3d milliseconds\n", $parseDuration);
		$out .= "\n";
		$out .= sprintf("    parse read ratio: %.1f times\n", $ratio);
		file_put_contents($this->reportFile, $out);
	}

}

