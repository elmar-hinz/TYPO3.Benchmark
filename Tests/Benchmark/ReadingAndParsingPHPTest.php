<?php

namespace ElmarHinz\Tests\Benchmark;

require_once("vendor/autoload.php");

/**
 * IMPORTANT: The test must to be run in isolation,
 *            else parsed classes are reused,
 *            with different results.
 *
 * vendor/bin/phpunit --process-isolation Tests/Benchmark/ReadingAndParsingPHPTest.php
 */
class ReadingAndParsingPHPTest extends \PHPUnit_Framework_TestCase
{
	protected $reportFile = 'Reports/readingAndParsingPHP.rst';
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
		$amountOfClasses = count(get_declared_classes());
		$ratio = $parseDuration/$readDuration;
		$out  = "\n\n";
		$out .= "Class Loading: File I/O vs. PHP Parsing Time\n";
		$out .= "============================================\n";
		$out .= '
In this test all class files of the system extensions are read once by
**file()** and once by **require_once()**. The latter step includes the
interpretion of the PHP code to declare classes.

I use the terminology parsing here, to stress the difference of class loading
and script execution, when methodes are called, objects are created and lots of
memory operations are done.

The difference of the execution time of **file()** and **require_once()** gives
an estimation of the parsing time. It takes ways longer than the I/O operations
to read the files, at least on SSD.

Bottomline: Even if all classes of the system extensions are required, the
mere I/O file operations would not become a critical part, while the
interpreter part of class loading matters. Combining all classes into a single
file would slow down, because even non-required class code would be
interpreted.
';
		$out .= "\n";
		$out .= sprintf(":glob:               %s\n", addcslashes($this->allClassPattern,
			'*'));
		$out .= sprintf(":files:              %d\n", count($this->allClasses));
		$out .= sprintf(":lines:              %d\n", $lines);
		$out .= sprintf(":declared classes:   %d\n", $amountOfClasses);
		$out .= "\n";
		$out .= sprintf(":file():             %' 3d milliseconds\n", $readDuration);
		$out .= sprintf(":require_once():     %' 3d milliseconds\n", $requireDuration);
		$out .= sprintf(":-> PHP parsetime:   %' 3d milliseconds\n", $parseDuration);
		$out .= "\n";
		$out .= sprintf(":parse/read ratio:   %.1f times\n", $ratio);
		file_put_contents($this->reportFile, $out);
	}

}

