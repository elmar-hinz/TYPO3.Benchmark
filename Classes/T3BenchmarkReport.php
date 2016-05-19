<?php

namespace ElmarHinz;

class T3BenchmarkReport
{

	protected $reportFile = 'Reports/Report.rst';
	protected $htmlCmd = 'rst2html.py Reports/Report.rst > Reports/Report.html';
	protected $timeTrackingReport = 'Reports/timeTracking.rst';
	protected $phpReport = 'Reports/readingAndParsingPHP.rst';

	public static function main()
	{
		(new T3BenchmarkReport())->create();
	}

	public function create()
	{
		$phpReport = file_get_contents($this->phpReport);
		$timeTrackingReport = @file_get_contents($this->timeTrackingReport);
		$out = '';
		$out .= "\n";
		$out .= "===============\n";
		$out .= "TYPO3 Benchmark\n";
		$out .= "===============\n";
		$out .= "\n";
		$out .= $phpReport;
		$out .= "\n";
		$out .= $timeTrackingReport;
		file_put_contents($this->reportFile, $out);
		@shell_exec($this->htmlCmd);
	}

}
