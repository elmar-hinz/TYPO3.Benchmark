<?php

namespace ElmarHinz\Tests\Functional;

use TYPO3\CMS\Core\Tests\FunctionalTestCase;
use TYPO3\CMS\Core\Utility\GeneralUtility as Utility;
use TYPO3\CMS\Core\Database\DatabaseConnection;

require_once("vendor/autoload.php");

class DirectorySetupTest extends FunctionalTestCase
{
	protected $testExtensionsToLoad = [ 't3bench' ];
	protected $functionalTest = 'typo3temp/var/tests/functional-e1b5725';
	protected $extension = 'typo3temp/var/tests/functional-e1b5725/typo3conf/ext/t3bench';

    public function setUp()
    {
		passthru('rm -rf ' . $this->functionalTest);
		assert(!file_exists($this->functionalTest));
        parent::setUp();
    }

    public function tearDown()
    {
        parent::tearDown();
		passthru('rm -rf ' . $this->functionalTest);
		assert(!file_exists($this->functionalTest));
    }

    /**
     * @test
     */
    public function hello()
    {
		$this->assertFileExists($this->extension);
	}

}

