<?php

require_once("vendor/autoload.php");

task('default', 'list');

desc('List all tasks.');
task('list', function($application){
        $task_list = $application->get_task_list();
        if (count($task_list)) {
                $max = max(array_map('strlen', array_keys($task_list)));
                foreach ($task_list as $name => $desc) {
                        if($name != 'default')
                                echo str_pad($name, $max + 4) . $desc . "\n";
                }
        }
});

desc('Full report');
task('report', 'clean', 'test:time', 'test:php', function() {
	\ElmarHinz\T3BenchmarkReport::main();
		print(PHP_EOL);
		print(PHP_EOL);
		print(@file_get_contents('Reports/Report.rst'));
});

desc('Clean up');
task('clean', function() {
	passthru('rm -f Reports/*');
	passthru('rm -rf typo3temp/*');
	passthru('ls -al typo3temp/');
});

group('setup', function() {
	desc('Help to setup the DB.');
	task('db', function() {
		print '
Databse setup
=============
cd TYPO3.v7/
make start
tunnnel vagrant/vagrant
127.0.0.1:33333 dev/dev
';
	});
});

group('test', function() {
	desc('Run all non-benchmark tests');
	task('all', 'test:unit', 'test:func');

	desc('Functional');
	task('func', function() {
		$cmd =
			'typo3DatabaseName="test" typo3DatabaseUsername="dev" '.
			'typo3DatabasePassword="dev" typo3DatabaseHost="127.0.0.1:33333" '.
			'vendor/bin/phpunit -c typo3/sysext/core/Build/FunctionalTests.xml '.
			'Tests/Functional/';
		/* print($cmd . "\n"); */
		passthru($cmd);
	});

	desc('PHP class files reading and parsing');
	task('php', function() {
		$cmd =
			'typo3DatabaseName="test" typo3DatabaseUsername="dev" '.
			'typo3DatabasePassword="dev" typo3DatabaseHost="127.0.0.1:33333" '.
			'vendor/bin/phpunit -c typo3/sysext/core/Build/FunctionalTests.xml '.
			'Tests/Benchmark/ReadingAndParsingPHPTest.php';
		/* print($cmd ."\n"); */
		passthru($cmd);
		print(PHP_EOL);
		print(PHP_EOL);
		print(@file_get_contents('Reports/readingAndParsingPHP.rst'));
		print(PHP_EOL);
		print(PHP_EOL);
	});

	desc('TimeTracker');
	task('time', function() {
		$cmd =
			'typo3DatabaseName="test" typo3DatabaseUsername="dev" '.
			'typo3DatabasePassword="dev" typo3DatabaseHost="127.0.0.1:33333" '.
			'vendor/bin/phpunit -c typo3/sysext/core/Build/FunctionalTests.xml '.
			'Tests/Benchmark/TimeTrackingTest.php';
		/* print($cmd ."\n"); */
		passthru($cmd);
		print(PHP_EOL);
		print(PHP_EOL);
		print(@file_get_contents('Reports/timeTracking.rst'));
		print(PHP_EOL);
		print(PHP_EOL);
	});

	desc('Unit tests');
	task('unit', function() {
		passthru('./vendor/bin/phpunit ./Tests/Unit/');
	});

	desc('Unit tests --testdox');
	task('dox', function() {
		passthru('./vendor/bin/phpunit --testdox ./Tests/Unit/');
	});
});
