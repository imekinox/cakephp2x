<?php
/**
 * CacheHelperTest file
 *
 * Long description for file
 *
 * PHP Version 5.x
 *
 * CakePHP(tm) Tests <https://trac.cakephp.org/wiki/Developement/TestSuite>
 * Copyright 2005-2009, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The Open Group Test Suite License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2009, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          https://trac.cakephp.org/wiki/Developement/TestSuite CakePHP(tm) Tests
 * @package       cake
 * @subpackage    cake.tests.cases.libs.view.helpers
 * @since         CakePHP(tm) v 1.2.0.4206
 * @license       http://www.opensource.org/licenses/opengroup.php The Open Group Test Suite License
 */
if (!defined('CAKEPHP_UNIT_TEST_EXECUTION')) {
	define('CAKEPHP_UNIT_TEST_EXECUTION', 1);
}
App::import('Core', array('Controller', 'Model', 'View'));
App::import('Helper', 'Cache');

/**
 * CacheTestController class
 *
 * @package       cake
 * @subpackage    cake.tests.cases.libs.view.helpers
 */
class CacheTestController extends Controller {

/**
 * helpers property
 *
 * @var array
 * @access public
 */
	var $helpers = array('Html', 'Cache');

/**
 * cache_parsing method
 *
 * @access public
 * @return void
 */
	function cache_parsing() {
		$this->viewPath = 'posts';
		$this->layout = 'cache_layout';
		$this->set('variable', 'variableValue');
		$this->set('superman', 'clark kent');
		$this->set('batman', 'bruce wayne');
		$this->set('spiderman', 'peter parker');
	}
}

/**
 * CacheHelperTest class
 *
 * @package       cake
 * @subpackage    cake.tests.cases.libs.view.helpers
 */
class CacheHelperTest extends CakeTestCase {

/**
 * Checks if TMP/views is writable, and skips the case if it is not.
 *
 * @return void
 */
	function skip() {
		$this->skipUnless(is_writable(TMP . 'cache' . DS . 'views' . DS), 'TMP/views is not writable %s');
	}
/**
 * setUp method
 *
 * @access public
 * @return void
 */
	function setUp() {
		$this->Controller = new CacheTestController();
		$this->Cache = new CacheHelper();
		$this->_cacheSettings = Configure::read('Cache');
		Configure::write('Cache.check', true);
		Configure::write('Cache.disable', false);
	}

/**
 * Start Case - switch view paths
 *
 * @access public
 * @return void
 */
	function startCase() {
		App::build(array(
			'views' => array(TEST_CAKE_CORE_INCLUDE_PATH . 'tests' . DS . 'test_app' . DS . 'views'. DS)
		), true);
	}

/**
 * End Case - restore view Paths
 *
 * @access public
 * @return void
 */
	function endCase() {
		App::build();
	}

/**
 * tearDown method
 *
 * @access public
 * @return void
 */
	function tearDown() {
		clearCache();
		unset($this->Cache);
		Configure::write('Cache', $this->_cacheSettings);
	}

/**
 * test cache parsing with no cake:nocache tags in view file.
 *
 * @access public
 * @return void
 */
	function testLayoutCacheParsingNoTagsInView() {
		$this->Controller->cache_parsing();
		$this->Controller->cacheAction = 21600;
		$this->Controller->here = '/cacheTest/cache_parsing';
		$this->Controller->action = 'cache_parsing';

		$View = new View($this->Controller);
		$result = $View->render('index');
		$this->assertNoPattern('/cake:nocache/', $result);
		$this->assertNoPattern('/php echo/', $result);

		$filename = CACHE . 'views' . DS . 'cachetest_cache_parsing.php';
		$this->assertTrue(file_exists($filename));

		$contents = file_get_contents($filename);
		$this->assertPattern('/php echo \$variable/', $contents);
		$this->assertPattern('/php echo microtime\(true\)/', $contents);
		$this->assertPattern('/clark kent/', $result);

		@unlink($filename);
	}

/**
 * test cache parsing with non-latin characters in current route
 *
 * @access public
 * @return void
 */
	function testCacheNonLatinCharactersInRoute() {
		$this->Controller->cache_parsing();
		$this->Controller->cacheAction = 21600;
		$this->Controller->here = '/posts/view/風街ろまん';
		$this->Controller->action = 'view';

		$View = new View($this->Controller);
		$result = $View->render('index');

		$filename = CACHE . 'views' . DS . 'posts_view_風街ろまん.php';
		$this->assertTrue(file_exists($filename));

		@unlink($filename);
	}
/**
 * Test cache parsing with cake:nocache tags in view file.
 *
 * @access public
 * @return void
 */
	function testLayoutCacheParsingWithTagsInView() {
		$this->Controller->cache_parsing();
		$this->Controller->cacheAction = 21600;
		$this->Controller->here = '/cacheTest/cache_parsing';
		$this->Controller->action = 'cache_parsing';

		$View = new View($this->Controller);
		$result = $View->render('test_nocache_tags');
		$this->assertNoPattern('/cake:nocache/', $result);
		$this->assertNoPattern('/php echo/', $result);

		$filename = CACHE . 'views' . DS . 'cachetest_cache_parsing.php';
		$this->assertTrue(file_exists($filename));

		$contents = file_get_contents($filename);
		$this->assertPattern('/if \(is_writable\(TMP\)\)\:/', $contents);
		$this->assertPattern('/php echo \$variable/', $contents);
		$this->assertPattern('/php echo microtime\(true\)/', $contents);
		$this->assertNoPattern('/cake:nocache/', $contents);

		@unlink($filename);
	}

/**
 * test that multiple <cake:nocache> tags function with multiple nocache tags in the layout.
 *
 * @return void
 */
	function testMultipleNoCacheTagsInViewfile() {
		$this->Controller->cache_parsing();
		$this->Controller->cacheAction = 21600;
		$this->Controller->here = '/cacheTest/cache_parsing';
		$this->Controller->action = 'cache_parsing';

		$View = new View($this->Controller);
		$result = $View->render('multiple_nocache');

		$this->assertNoPattern('/cake:nocache/', $result);
		$this->assertNoPattern('/php echo/', $result);

		$filename = CACHE . 'views' . DS . 'cachetest_cache_parsing.php';
		$this->assertTrue(file_exists($filename));

		$contents = file_get_contents($filename);
		$this->assertNoPattern('/cake:nocache/', $contents);
		@unlink($filename);
	}

/**
 * testComplexNoCache method
 *
 * @return void
 * @access public
 */
	function testComplexNoCache () {
		$this->Controller->cache_parsing();
		$this->Controller->cacheAction = array('cache_complex' => 21600);
		$this->Controller->here = '/cacheTest/cache_complex';
		$this->Controller->action = 'cache_complex';
		$this->Controller->layout = 'multi_cache';
		$this->Controller->viewPath = 'posts';

		$View = new View($this->Controller);
		$result = $View->render('sequencial_nocache');

		$this->assertNoPattern('/cake:nocache/', $result);
		$this->assertNoPattern('/php echo/', $result);
		$this->assertPattern('/A\. Layout Before Content/', $result);
		$this->assertPattern('/B\. In Plain Element/', $result);
		$this->assertPattern('/C\. Layout After Test Element/', $result);
		$this->assertPattern('/D\. In View File/', $result);
		$this->assertPattern('/E\. Layout After Content/', $result);
		//$this->assertPattern('/F\. In Element With No Cache Tags/', $result);
		$this->assertPattern('/G\. Layout After Content And After Element With No Cache Tags/', $result);
		$this->assertNoPattern('/1\. layout before content/', $result);
		$this->assertNoPattern('/2\. in plain element/', $result);
		$this->assertNoPattern('/3\. layout after test element/', $result);
		$this->assertNoPattern('/4\. in view file/', $result);
		$this->assertNoPattern('/5\. layout after content/', $result);
		//$this->assertNoPattern('/6\. in element with no cache tags/', $result);
		$this->assertNoPattern('/7\. layout after content and after element with no cache tags/', $result);

		$filename = CACHE . 'views' . DS . 'cachetest_cache_complex.php';
		$this->assertTrue(file_exists($filename));
		$contents = file_get_contents($filename);
		@unlink($filename);

		$this->assertPattern('/A\. Layout Before Content/', $contents);
		$this->assertNoPattern('/B\. In Plain Element/', $contents);
		$this->assertPattern('/C\. Layout After Test Element/', $contents);
		$this->assertPattern('/D\. In View File/', $contents);
		$this->assertPattern('/E\. Layout After Content/', $contents);
		//$this->assertPattern('/F\. In Element With No Cache Tags/', $contents);
		$this->assertPattern('/G\. Layout After Content And After Element With No Cache Tags/', $contents);
		$this->assertPattern('/1\. layout before content/', $contents);
		$this->assertNoPattern('/2\. in plain element/', $contents);
		$this->assertPattern('/3\. layout after test element/', $contents);
		$this->assertPattern('/4\. in view file/', $contents);
		$this->assertPattern('/5\. layout after content/', $contents);
		//$this->assertPattern('/6\. in element with no cache tags/', $contents);
		$this->assertPattern('/7\. layout after content and after element with no cache tags/', $contents);
	}

/**
 * test cacheAction set to a boolean
 *
 * @return void
 */
	function testCacheActionArray() {
		$this->Controller->cache_parsing();
		$this->Controller->cacheAction = array(
			'cache_parsing' => 21600
		);
		$this->Controller->here = '/cache_test/cache_parsing';
		$this->Controller->action = 'cache_parsing';

		$View = new View($this->Controller);
		$result = $View->render('index');

		$this->assertNoPattern('/cake:nocache/', $result);
		$this->assertNoPattern('/php echo/', $result);

		$filename = CACHE . 'views' . DS . 'cache_test_cache_parsing.php';
		$this->assertTrue(file_exists($filename));
		@unlink($filename);


		$this->Controller->cache_parsing();
		$this->Controller->cacheAction = array(
			'cache_parsing/' => 21600
		);
		$this->Controller->here = '/cacheTest/cache_parsing';
		$this->Controller->action = 'cache_parsing';

		$View = new View($this->Controller);
		$result = $View->render('index');

		$this->assertNoPattern('/cake:nocache/', $result);
		$this->assertNoPattern('/php echo/', $result);

		$filename = CACHE . 'views' . DS . 'cachetest_cache_parsing.php';
		$this->assertTrue(file_exists($filename));
		@unlink($filename);


		$this->Controller->cache_parsing();
		$this->Controller->cacheAction = array(
			'cache_parsing/33' => 21600
		);
		$this->Controller->here = '/cacheTest/cache_parsing/33';
		$this->Controller->action = 'cache_parsing';

		$View = new View($this->Controller);
		$result = $View->render('index');

		$this->assertNoPattern('/cake:nocache/', $result);
		$this->assertNoPattern('/php echo/', $result);

		$filename = CACHE . 'views' . DS . 'cachetest_cache_parsing_33.php';
		$this->assertTrue(file_exists($filename));
		@unlink($filename);
		
		$this->Controller->cache_parsing();
		$this->Controller->cacheAction = array(
			'cache_parsing/33' => 21600
		);
		$this->Controller->here = '/cacheTest/cache_parsing';
		$this->Controller->action = 'cache_parsing';

		$View = new View($this->Controller);
		$result = $View->render('index');

		$this->assertNoPattern('/cake:nocache/', $result);
		$this->assertNoPattern('/php echo/', $result);

		$filename = CACHE . 'views' . DS . 'cachetest_cache_parsing.php';
		$this->assertFalse(file_exists($filename));
	}
/**
 * testCacheEmptySections method
 *
 * This test must be uncommented/fixed in next release (1.2+)
 *
 * @return void
 * @access public
 *
	function testCacheEmptySections () {
		$this->Controller->cache_parsing();
		$this->Controller->cacheAction = array('cacheTest' => 21600);
		$this->Controller->here = '/cacheTest/cache_empty_sections';
		$this->Controller->action = 'cache_empty_sections';
		$this->Controller->layout = 'cache_empty_sections';
		$this->Controller->viewPath = 'posts';

		$View = new View($this->Controller);
		$result = $View->render('cache_empty_sections');
		$this->assertNoPattern('/cake:nocache/', $result);
		$this->assertNoPattern('/php echo/', $result);
		$this->assertPattern(
			'@</title>\s*</head>\s*' .
			'<body>\s*' .
			'View Content\s*' .
			'cached count is: 3\s*' .
			'</body>@', $result);

		$filename = CACHE . 'views' . DS . 'cachetest_cache_empty_sections.php';
		$this->assertTrue(file_exists($filename));
		$contents = file_get_contents($filename);
		$this->assertNoPattern('/cake:nocache/', $contents);
		$this->assertPattern(
			'@<head>\s*<title>Posts</title>\s*' .
			"<\?php \$x = 1; \?>\s*" .
			'</head>\s*' .
			'<body>\s*' .
			"<\?php \$x\+\+; \?>\s*" .
			"<\?php \$x\+\+; \?>\s*" .
			'View Content\s*' .
			"<\?php \$y = 1; \?>\s*" .
			"<\?php echo 'cached count is:' . \$x; \?>\s*" .
			'@', $contents);
		@unlink($filename);
	}
*/
}
?>