================
TYPO3 Benchmarks
================

Getting TYPO3 dynamic
^^^^^^^^^^^^^^^^^^^^^

How does TYPO3 perform without caching?
Which processes eat up what part or the time?

Given a MySQL database to run the tests, the tool shall install a full
TYPO3 system. It shall be able to install different examples of distributions
including example content.

It shall be able to clear all caches and do the different measurements while
firering up a page.

Finally it shall report the results, printing tables and Diagrams.

PLan
====

* Investigating the functional test case.
    * Result: It should be possible to inherit from
      TYPO3\CMS\Core\Tests\FunctionalTestCase
      to setup a test environment quickly.
      Maybe run FunctionalTestsBootstrap before.
    * Try the setyp
* Investigating the install tool.
* Finding the time tracking hooks in the core.
* Finding hooks to track the time of I/O connections of files and DB.

