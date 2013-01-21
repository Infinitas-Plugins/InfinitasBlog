<?php
App::uses('AllTestsBase', 'Test/Lib');

class AllBlogTestsTest extends AllTestsBase {

/**
 * Suite define the tests for this suite
 *
 * @return void
 */
	public static function suite() {
		$suite = new CakeTestSuite('All Blog tests');

		$path = CakePlugin::path('Blog') . 'Test' . DS . 'Case' . DS;
		$suite->addTestDirectoryRecursive($path);

		return $suite;
	}
}
