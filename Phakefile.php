<?php

$GLOBALS['reportFile'] = 'Reports/report.rst';

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

desc('Clean up');
task('clean', function() {
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
	desc('Run all tests');
	task('all', 'test:unit', 'test:func');

	desc('Functional');
	task('func', function() {
		passthru(
			'typo3DatabaseName="test" typo3DatabaseUsername="dev" '.
			'typo3DatabasePassword="dev" typo3DatabaseHost="127.0.0.1:33333" '.
			'vendor/bin/phpunit -c typo3/sysext/core/Build/FunctionalTests.xml '.
			'Tests/Functional/');
	});

	desc('Benchmark tests');
	task('bench', function() {
		passthru(
			'typo3DatabaseName="test" typo3DatabaseUsername="dev" '.
			'typo3DatabasePassword="dev" typo3DatabaseHost="127.0.0.1:33333" '.
			'vendor/bin/phpunit -c typo3/sysext/core/Build/FunctionalTests.xml '.
			'Tests/Benchmark/BenchmarkTest.php');
		print(PHP_EOL);
		print(PHP_EOL);
		print(file_get_contents($GLOBALS['reportFile']));
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
