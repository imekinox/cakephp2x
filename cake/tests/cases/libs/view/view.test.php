<?php
/**
 * ViewTest file
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
 * @subpackage    cake.tests.cases.libs
 * @since         CakePHP(tm) v 1.2.0.4206
 * @license       http://www.opensource.org/licenses/opengroup.php The Open Group Test Suite License
 */
App::import('Core', array('View', 'Controller'));
App::import('Helper', 'Cache');

Mock::generate('Helper', 'CallbackMockHelper');
Mock::generate('CacheHelper', 'ViewTestMockCacheHelper');

if (!class_exists('ErrorHandler')) {
	App::import('Core', array('Error'));
}

/**
 * ViewPostsController class
 *
 * @package       cake
 * @subpackage    cake.tests.cases.libs.view
 */
class ViewPostsController extends Controller {

/**
 * name property
 *
 * @var string 'Posts'
 * @access public
 */
	public $name = 'Posts';

/**
 * uses property
 *
 * @var mixed null
 * @access public
 */
	public $uses = null;

/**
 * index method
 *
 * @access public
 * @return void
 */
	public function index() {
		$this->set('testData', 'Some test data');
		$test2 = 'more data';
		$test3 = 'even more data';
		$this->set(compact('test2', 'test3'));
	}

/**
 * nocache_tags_with_element method
 *
 * @access public
 * @return void
 */
	public function nocache_multiple_element() {
		$this->set('foo', 'this is foo var');
		$this->set('bar', 'this is bar var');
	}
}

/**
 * ViewTestErrorHandler class
 *
 * @package       cake
 * @subpackage    cake.tests.cases.libs.view
 */
class ViewTestErrorHandler extends ErrorHandler {

/**
 * stop method
 *
 * @access public
 * @return void
 */
	function _stop() {
		return;
	}
}

/**
 * TestView class
 *
 * @package       cake
 * @subpackage    cake.tests.cases.libs.view
 */
class TestView extends View {

/**
 * getViewFileName method
 *
 * @param mixed $name
 * @access public
 * @return void
 */
	function getViewFileName($name = null) {
		return $this->_getViewFileName($name);
	}

/**
 * getLayoutFileName method
 *
 * @param mixed $name
 * @access public
 * @return void
 */
	function getLayoutFileName($name = null) {
		return $this->_getLayoutFileName($name);
	}

/**
 * loadHelpers method
 *
 * @param mixed $loaded
 * @param mixed $helpers
 * @param mixed $parent
 * @access public
 * @return void
 */
	function loadHelpers(&$loaded, $helpers, $parent = null) {
		return $this->_loadHelpers($loaded, $helpers, $parent);
	}

/**
 * paths method
 *
 * @param string $plugin
 * @param boolean $cached
 * @access public
 * @return void
 */
	function paths($plugin = null, $cached = true) {
		return $this->_paths($plugin, $cached);
	}

/**
 * cakeError method
 *
 * @param mixed $method
 * @param mixed $messages
 * @access public
 * @return void
 */
	function cakeError($method, $messages) {
		$error = new ViewTestErrorHandler($method, $messages);
		return $error;
	}
}

/**
 * TestAfterHelper class
 *
 * @package       cake
 * @subpackage    cake.tests.cases.libs.view
 */
class TestAfterHelper extends Helper {

/**
 * property property
 *
 * @var string ''
 * @access public
 */
	var $property = '';

/**
 * beforeLayout method
 *
 * @access public
 * @return void
 */
	function beforeLayout() {
		$this->property = 'Valuation';
	}

/**
 * afterLayout method
 *
 * @access public
 * @return void
 */
	function afterLayout() {
		$View = ClassRegistry::getObject('afterView');
		$View->output .= 'modified in the afterlife';
	}
}


/**
 * ViewTest class
 *
 * @package       cake
 * @subpackage    cake.tests.cases.libs
 */
class ViewTest extends CakeTestCase {

/**
 * setUp method
 *
 * @access public
 * @return void
 */
	function setUp() {
		Router::reload();
		$this->Controller = new Controller();
		$this->PostsController = new ViewPostsController();
		$this->PostsController->viewPath = 'posts';
		$this->PostsController->index();
		$this->View = new View($this->PostsController);
	}

/**
 * tearDown method
 *
 * @access public
 * @return void
 */
	function tearDown() {
		unset($this->View);
		unset($this->PostsController);
		unset($this->Controller);
	}

/**
 * endTest
 *
 * @access public
 * @return void
 */
	function startTest() {
		App::build(array(
			'plugins' => array(TEST_CAKE_CORE_INCLUDE_PATH . 'tests' . DS . 'test_app' . DS . 'plugins' . DS),
			'views' => array(
				TEST_CAKE_CORE_INCLUDE_PATH . 'tests' . DS . 'test_app' . DS . 'views'. DS,
				TEST_CAKE_CORE_INCLUDE_PATH . 'libs' . DS . 'view' . DS
			)
		), true);
	}

/**
 * endTest
 *
 * @access public
 * @return void
 */
	function endTest() {
		App::build();
	}

/**
 * testPluginGetTemplate method
 *
 * @access public
 * @return void
 */
	function testPluginGetTemplate() {
		$this->Controller->plugin = 'test_plugin';
		$this->Controller->name = 'TestPlugin';
		$this->Controller->viewPath = 'tests';
		$this->Controller->action = 'index';

		$View = new TestView($this->Controller);

		$expected = TEST_CAKE_CORE_INCLUDE_PATH . 'tests' . DS . 'test_app' . DS . 'plugins' . DS .'test_plugin' . DS . 'views' . DS .'tests' . DS .'index.ctp';
		$result = $View->getViewFileName('index');
		$this->assertEqual($result, $expected);

		$expected = TEST_CAKE_CORE_INCLUDE_PATH . 'tests' . DS . 'test_app' . DS . 'plugins' . DS .'test_plugin' . DS . 'views' . DS . 'layouts' . DS .'default.ctp';
		$result = $View->getLayoutFileName();
		$this->assertEqual($result, $expected);
	}

/**
 * test that plugin/$plugin_name is only appended to the paths it should be.
 *
 * @return void
 */
	function testPluginPathGeneration() {
		$this->Controller->plugin = 'test_plugin';
		$this->Controller->name = 'TestPlugin';
		$this->Controller->viewPath = 'tests';
		$this->Controller->action = 'index';

		$View = new TestView($this->Controller);
		$paths = $View->paths();
		$this->assertEqual($paths, App::path('views'));

		$paths = $View->paths('test_plugin');

		$expected = array(
			TEST_CAKE_CORE_INCLUDE_PATH . 'tests' . DS . 'test_app' . DS . 'views' . DS . 'plugins' . DS . 'test_plugin' . DS,
			TEST_CAKE_CORE_INCLUDE_PATH . 'tests' . DS . 'test_app' . DS . 'plugins' . DS . 'test_plugin' . DS . 'views' . DS,
			TEST_CAKE_CORE_INCLUDE_PATH . 'tests' . DS . 'test_app' . DS . 'views' . DS,
			TEST_CAKE_CORE_INCLUDE_PATH . 'libs' . DS . 'view' . DS
		);
		$this->assertEqual($paths, $expected);
	}

/**
 * test that CamelCase plugins still find their view files.
 *
 * @return void
 */
	function testCamelCasePluginGetTemplate() {
		$this->Controller->plugin = 'TestPlugin';
		$this->Controller->name = 'TestPlugin';
		$this->Controller->viewPath = 'tests';
		$this->Controller->action = 'index';

		$View = new TestView($this->Controller);
		App::build(array(
			'plugins' => array(TEST_CAKE_CORE_INCLUDE_PATH . 'tests' . DS . 'test_app' . DS . 'plugins' . DS),
			'views' => array(TEST_CAKE_CORE_INCLUDE_PATH . 'tests' . DS . 'test_app' . DS . 'views'. DS)
		));

		$expected = TEST_CAKE_CORE_INCLUDE_PATH . 'tests' . DS . 'test_app' . DS . 'plugins' . DS .'test_plugin' . DS . 'views' . DS .'tests' . DS .'index.ctp';
		$result = $View->getViewFileName('index');
		$this->assertEqual($result, $expected);

		$expected = TEST_CAKE_CORE_INCLUDE_PATH . 'tests' . DS . 'test_app' . DS . 'plugins' . DS .'test_plugin' . DS . 'views' . DS . 'layouts' . DS .'default.ctp';
		$result = $View->getLayoutFileName();
		$this->assertEqual($result, $expected);
	}

/**
 * testGetTemplate method
 *
 * @access public
 * @return void
 */
	function testGetTemplate() {
		$this->Controller->plugin = null;
		$this->Controller->name = 'Pages';
		$this->Controller->viewPath = 'pages';
		$this->Controller->action = 'display';
		$this->Controller->params['pass'] = array('home');

		$View = new TestView($this->Controller);

		$expected = TEST_CAKE_CORE_INCLUDE_PATH . 'tests' . DS . 'test_app' . DS . 'views' . DS .'pages' . DS .'home.ctp';
		$result = $View->getViewFileName('home');
		$this->assertEqual($result, $expected);

		$expected = TEST_CAKE_CORE_INCLUDE_PATH . 'tests' . DS . 'test_app' . DS . 'views' . DS .'posts' . DS .'index.ctp';
		$result = $View->getViewFileName('/posts/index');
		$this->assertEqual($result, $expected);

		$expected = TEST_CAKE_CORE_INCLUDE_PATH . 'tests' . DS . 'test_app' . DS . 'views' . DS .'posts' . DS .'index.ctp';
		$result = $View->getViewFileName('../posts/index');
		$this->assertEqual($result, $expected);

		$expected = TEST_CAKE_CORE_INCLUDE_PATH . 'tests' . DS . 'test_app' . DS . 'views' . DS . 'layouts' . DS .'default.ctp';
		$result = $View->getLayoutFileName();
		$this->assertEqual($result, $expected);

		$View->layoutPath = 'rss';
		$expected = TEST_CAKE_CORE_INCLUDE_PATH . 'tests' . DS . 'test_app' . DS . 'views' . DS . 'layouts' . DS . 'rss' . DS . 'default.ctp';
		$result = $View->getLayoutFileName();
		$this->assertEqual($result, $expected);

		$View->layoutPath = 'email' . DS . 'html';
		$expected = TEST_CAKE_CORE_INCLUDE_PATH . 'tests' . DS . 'test_app' . DS . 'views' . DS . 'layouts' . DS . 'email' . DS . 'html' . DS . 'default.ctp';
		$result = $View->getLayoutFileName();

		$this->assertEqual($result, $expected);
	}

/**
 * testMissingView method
 *
 * @access public
 * @return void
 */
	function testMissingView() {
		$this->Controller->plugin = null;
		$this->Controller->name = 'Pages';
		$this->Controller->viewPath = 'pages';
		$this->Controller->action = 'display';
		$this->Controller->params['pass'] = array('home');

		$View = new TestView($this->Controller);
		ob_start();
		$result = $View->getViewFileName('does_not_exist');
		$expected = str_replace(array("\t", "\r\n", "\n"), "", ob_get_clean());

		$this->assertPattern("/PagesController::/", $expected);
		$this->assertPattern("/pages(\/|\\\)does_not_exist.ctp/", $expected);
	}

/**
 * testMissingLayout method
 *
 * @access public
 * @return void
 */
	function testMissingLayout() {
		$this->Controller->plugin = null;
		$this->Controller->name = 'Posts';
		$this->Controller->viewPath = 'posts';
		$this->Controller->layout = 'whatever';

		$View = new TestView($this->Controller);
		ob_start();
		$result = $View->getLayoutFileName();
		$expected = str_replace(array("\t", "\r\n", "\n"), "", ob_get_clean());

		$this->assertPattern("/Missing Layout/", $expected);
		$this->assertPattern("/layouts(\/|\\\)whatever.ctp/", $expected);
	}

/**
 * testUUIDGeneration method
 *
 * @access public
 * @return void
 */
	function testUUIDGeneration() {
		$result = $this->View->uuid('form', array('controller' => 'posts', 'action' => 'index'));
		$this->assertEqual($result, 'form0425fe3bad');
		$result = $this->View->uuid('form', array('controller' => 'posts', 'action' => 'index'));
		$this->assertEqual($result, 'forma9918342a7');
		$result = $this->View->uuid('form', array('controller' => 'posts', 'action' => 'index'));
		$this->assertEqual($result, 'form3ecf2e3e96');
	}

/**
 * testAddInlineScripts method
 *
 * @access public
 * @return void
 */
	function testAddInlineScripts() {
		$this->View->addScript('prototype.js');
		$this->View->addScript('prototype.js');
		$this->assertEqual($this->View->scripts(), array('prototype.js'));

		$this->View->addScript('mainEvent', 'Event.observe(window, "load", function() { doSomething(); }, true);');
		$this->assertEqual($this->View->scripts(), array('prototype.js', 'mainEvent' => 'Event.observe(window, "load", function() { doSomething(); }, true);'));
	}

/**
 * testElement method
 *
 * @access public
 * @return void
 */
	function testElement() {
		$result = $this->View->element('test_element');
		$this->assertEqual($result, 'this is the test element');

		$result = $this->View->element('non_existant_element');
		$this->assertPattern('/Not Found:/', $result);
		$this->assertPattern('/non_existant_element/', $result);
	}

/**
 * testElementCacheHelperNoCache method
 *
 * @access public
 * @return void
 */
	function testElementCacheHelperNoCache() {
		$Controller = new ViewPostsController();
		$View = new View($Controller);
		$empty = array();
		$helpers = $View->_loadHelpers($empty, array('cache'));
		$View->loaded = $helpers;
		$result = $View->element('test_element', array('ram' => 'val', 'test' => array('foo', 'bar')));
		$this->assertEqual($result, 'this is the test element');
	}

/**
 * testElementCache method
 *
 * @access public
 * @return void
 */
	function testElementCache() {
		$View = new TestView($this->PostsController);
		$element = 'test_element';
		$expected = 'this is the test element';
		$result = $View->element($element);
		$this->assertEqual($result, $expected);

		$cached = false;
		$result = $View->element($element, array('cache'=>'+1 second'));
		if (file_exists(CACHE . 'views' . DS . 'element_cache_'.$element)) {
			$cached = true;
			unlink(CACHE . 'views' . DS . 'element_cache_'.$element);
		}
		$this->assertTrue($cached);

		$cached = false;
		$result = $View->element($element, array('cache'=>'+1 second', 'other_param'=> true, 'anotherParam'=> true));
		if (file_exists(CACHE . 'views' . DS . 'element_cache_other_param_anotherParam_'.$element)) {
			$cached = true;
			unlink(CACHE . 'views' . DS . 'element_cache_other_param_anotherParam_'.$element);
		}
		$this->assertTrue($cached);

		$cached = false;
		$result = $View->element($element, array('cache'=>array('time'=>'+1 second', 'key'=>'/whatever/here')));
		if (file_exists(CACHE . 'views' . DS . 'element_'.Inflector::slug('/whatever/here').'_'.$element)) {
			$cached = true;
			unlink(CACHE . 'views' . DS . 'element_'.Inflector::slug('/whatever/here').'_'.$element);
		}
		$this->assertTrue($cached);

		$cached = false;
		$result = $View->element($element, array('cache'=>array('time'=>'+1 second', 'key'=>'whatever_here')));
		if (file_exists(CACHE . 'views' . DS . 'element_whatever_here_'.$element)) {
			$cached = true;
			unlink(CACHE . 'views' . DS . 'element_whatever_here_'.$element);
		}
		$this->assertTrue($cached);
		$this->assertEqual($result, $expected);

	}

/**
 * testLoadHelpers method
 *
 * @access public
 * @return void
 */
	function testLoadHelpers() {
		$View = new TestView($this->PostsController);

		$loaded = array();
		$result = $View->loadHelpers($loaded, array('Html', 'Form', 'Ajax'));
		$this->assertTrue(is_object($result['Html']));
		$this->assertTrue(is_object($result['Form']));
		$this->assertTrue(is_object($result['Form']->Html));
		$this->assertTrue(is_object($result['Ajax']->Html));

		$View->plugin = 'test_plugin';
		$result = $View->loadHelpers($loaded, array('TestPlugin.PluggedHelper'));
		$this->assertTrue(is_object($result['PluggedHelper']));
		$this->assertTrue(is_object($result['PluggedHelper']->OtherHelper));
	}

/**
 * test the correct triggering of helper callbacks
 *
 * @return void
 */
	function testHelperCallbackTriggering() {
		$this->PostsController->helpers = array('Html', 'CallbackMock');
		$View = new TestView($this->PostsController);
		$loaded = array();
		$View->loaded = $View->loadHelpers($loaded, $this->PostsController->helpers);
		$View->loaded['CallbackMock']->expectOnce('beforeRender');
		$View->loaded['CallbackMock']->expectOnce('afterRender');
		$View->loaded['CallbackMock']->expectOnce('beforeLayout');
		$View->loaded['CallbackMock']->expectOnce('afterLayout');
		$View->render('index');
	}

/**
 * testBeforeLayout method
 *
 * @access public
 * @return void
 */
	function testBeforeLayout() {
		$this->PostsController->helpers = array('TestAfter', 'Html');
		$View = new View($this->PostsController);
		$out = $View->render('index');
		$this->assertEqual($View->loaded['testAfter']->property, 'Valuation');
	}

/**
 * testAfterLayout method
 *
 * @access public
 * @return void
 */
	function testAfterLayout() {
		$this->PostsController->helpers = array('TestAfter', 'Html');
		$this->PostsController->set('variable', 'values');

		$View = new View($this->PostsController);
		ClassRegistry::addObject('afterView', $View);

		$content = 'This is my view output';
		$result = $View->renderLayout($content, 'default');
		$this->assertPattern('/modified in the afterlife/', $result);
		$this->assertPattern('/This is my view output/', $result);
	}

/**
 * testRenderLoadHelper method
 *
 * @access public
 * @return void
 */
	function testRenderLoadHelper() {
		$this->PostsController->helpers = array('Html', 'Form', 'Ajax');
		$View = new TestView($this->PostsController);

		$result = $View->render($View->getViewFileName('index'), array());
		$this->assertEqual($result, 'posts index');

		$helpers = $View->loaded;
		$this->assertTrue(is_object($helpers['html']));
		$this->assertTrue(is_object($helpers['form']));
		$this->assertTrue(is_object($helpers['form']->Html));
		$this->assertTrue(is_object($helpers['ajax']->Html));

		$this->PostsController->helpers = array('Html', 'Form', 'Ajax', 'TestPlugin.PluggedHelper');
		$View = new TestView($this->PostsController);

		$result = $View->render($View->getViewFileName('index'), array());
		$this->assertEqual($result, 'posts index');

		$helpers = $View->loaded;
		$this->assertTrue(is_object($helpers['html']));
		$this->assertTrue(is_object($helpers['form']));
		$this->assertTrue(is_object($helpers['form']->Html));
		$this->assertTrue(is_object($helpers['ajax']->Html));
		$this->assertTrue(is_object($helpers['pluggedHelper']->OtherHelper));

		$this->assertTrue(is_object($View->Html));
		$this->assertTrue(is_object($View->Form));
		$this->assertTrue(is_object($View->Form->Html));
		$this->assertTrue(is_object($View->PluggedHelper->OtherHelper));
		$this->assertReference($View->Form, $View->loaded['form']);
		$this->assertReference($View->Html, $View->loaded['html']);
		$this->assertReference($View->PluggedHelper->OtherHelper, $View->loaded['otherHelper']);
	}

/**
 * testRender method
 *
 * @access public
 * @return void
 */
	function testRender() {
		$View = new TestView($this->PostsController);
		$result = str_replace(array("\t", "\r\n", "\n"), "", $View->render('index'));

		$this->assertPattern("/<meta http-equiv=\"Content-Type\" content=\"text\/html; charset=utf-8\" \/><title>/", $result);
		$this->assertPattern("/<div id=\"content\">posts index<\/div>/", $result);
		$this->assertPattern("/<div id=\"content\">posts index<\/div>/", $result);

		$this->PostsController->set('url', 'flash');
		$this->PostsController->set('message', 'yo what up');
		$this->PostsController->set('pause', 3);
		$this->PostsController->set('page_title', 'yo what up');

		$View = new TestView($this->PostsController);
		$result = str_replace(array("\t", "\r\n", "\n"), "", $View->render(false, 'flash'));

		$this->assertPattern("/<title>yo what up<\/title>/", $result);
		$this->assertPattern("/<p><a href=\"flash\">yo what up<\/a><\/p>/", $result);

		$this->assertTrue($View->render(false, 'flash'));

		$this->PostsController->helpers = array('Cache', 'Html');
		$this->PostsController->constructClasses();
		$this->PostsController->cacheAction = array('index' => 3600);
		Configure::write('Cache.check', true);

		$View = new TestView($this->PostsController);
		$result = str_replace(array("\t", "\r\n", "\n"), "", $View->render('index'));

		$this->assertPattern("/<meta http-equiv=\"Content-Type\" content=\"text\/html; charset=utf-8\" \/><title>/", $result);
		$this->assertPattern("/<div id=\"content\">posts index<\/div>/", $result);
		$this->assertPattern("/<div id=\"content\">posts index<\/div>/", $result);
	}

/**
 * test rendering layout with cache helper loaded
 *
 * @return void
 */
	function testRenderLayoutWithMockCacheHelper() {
		$_check = Configure::read('Cache.check');
		Configure::write('Cache.check', true);

		$Controller = new ViewPostsController();
		$Controller->cacheAction = '1 day';
		$View = new View($Controller);
		$View->loaded['cache'] = new ViewTestMockCacheHelper();
		$View->loaded['cache']->expectCallCount('cache', 2);

		$result = $View->render('index');
		$this->assertPattern('/posts index/', $result);

		Configure::write('Cache.check', $_check);
	}

/**
 * testGetViewFileName method
 *
 * @access public
 * @return void
 */
	function testViewFileName() {
		$View = new TestView($this->PostsController);

		$result = $View->getViewFileName('index');
		$this->assertPattern('/posts(\/|\\\)index.ctp/', $result);

		$result = $View->getViewFileName('/pages/home');
		$this->assertPattern('/pages(\/|\\\)home.ctp/', $result);

		$result = $View->getViewFileName('../elements/test_element');
		$this->assertPattern('/elements(\/|\\\)test_element.ctp/', $result);

		$result = $View->getViewFileName('../themed/test_theme/posts/index');
		$this->assertPattern('/themed(\/|\\\)test_theme(\/|\\\)posts(\/|\\\)index.ctp/', $result);

		$expected = TEST_CAKE_CORE_INCLUDE_PATH . 'tests' . DS . 'test_app' . DS . 'views' . DS .'posts' . DS .'index.ctp';
		$result = $View->getViewFileName('../posts/index');
		$this->assertEqual($result, $expected);

	}

/**
 * testRenderCache method
 *
 * @access public
 * @return void
 */
	function testRenderCache() {
		$view = 'test_view';
		$View = new View($this->PostsController);
		$path = CACHE . 'views' . DS . 'view_cache_'.$view;

		$cacheText = '<!--cachetime:'.time().'-->some cacheText';
		$f = fopen($path, 'w+');
		fwrite($f, $cacheText);
		fclose($f);

		$result = $View->renderCache($path, '+1 second');
		$this->assertFalse($result);
		@unlink($path);

		$cacheText = '<!--cachetime:'.(time() + 10).'-->some cacheText';
		$f = fopen($path, 'w+');
		fwrite($f, $cacheText);
		fclose($f);
		ob_start();
		$View->renderCache($path, '+1 second');
		$result = ob_get_clean();

		$expected = 'some cacheText';
		$this->assertPattern('/^some cacheText/', $result);

		@unlink($path);
	}

/**
 * testRenderNocache method
 *
 * @access public
 * @return void
 */

/* This is a new test case for a pending enhancement
	function testRenderNocache() {
		$this->PostsController->helpers = array('Cache', 'Html');
		$this->PostsController->constructClasses();
		$this->PostsController->cacheAction = 21600;
		$this->PostsController->here = '/posts/nocache_multiple_element';
		$this->PostsController->action = 'nocache_multiple_element';
		$this->PostsController->nocache_multiple_element();
		Configure::write('Cache.check', true);
		Configure::write('Cache.disable', false);

		$filename = CACHE . 'views' . DS . 'posts_nocache_multiple_element.php';

		$View = new TestView($this->PostsController);
		$View->render();

		ob_start();
		$View->renderCache($filename, microtime(true));
		$result = ob_get_clean();
		@unlink($filename);

		$this->assertPattern('/php echo \$foo;/', $result);
		$this->assertPattern('/php echo \$bar;/', $result);
		$this->assertPattern('/php \$barfoo = \'in sub2\';/', $result);
		$this->assertPattern('/php echo \$barfoo;/', $result);
		$this->assertPattern('/printing: "in sub2"/', $result);
		$this->assertPattern('/php \$foobar = \'in sub1\';/', $result);
		$this->assertPattern('/php echo \$foobar;/', $result);
		$this->assertPattern('/printing: "in sub1"/', $result);
	}
*/

/**
 * testSet method
 *
 * @access public
 * @return void
 */
	function testSet() {
		$View = new TestView($this->PostsController);
		$View->viewVars = array();
		$View->set('somekey', 'someValue');
		$this->assertIdentical($View->viewVars, array('somekey' => 'someValue'));
		$this->assertIdentical($View->getVars(), array('somekey'));

		$View->viewVars = array();
		$keys = array('key1', 'key2');
		$values = array('value1', 'value2');
		$View->set($keys, $values);
		$this->assertIdentical($View->viewVars, array('key1' => 'value1', 'key2' => 'value2'));
		$this->assertIdentical($View->getVars(), array('key1', 'key2'));
		$this->assertIdentical($View->getVar('key1'), 'value1');
		$this->assertNull($View->getVar('key3'));

		$View->set(array('key3' => 'value3'));
		$this->assertIdentical($View->getVar('key3'), 'value3');
	}

/**
 * testEntityReference method
 *
 * @access public
 * @return void
 */
	function testEntityReference() {
		$View = new TestView($this->PostsController);
		$View->model = 'Post';
		$View->field = 'title';
		$this->assertEqual($View->entity(), array('Post', 'title'));

		$View->association = 'Comment';
		$View->field = 'user_id';
		$this->assertEqual($View->entity(), array('Comment', 'user_id'));
	}

/**
 * testBadExt method
 *
 * @access public
 * @return void
 */
	function testBadExt() {
		$this->PostsController->action = 'something';
		$this->PostsController->ext = '.whatever';
		restore_error_handler();
		ob_start();
		$View = new TestView($this->PostsController);
		$View->render('this_is_missing');
		$result = str_replace(array("\t", "\r\n", "\n"), "", ob_get_clean());
		set_error_handler('simpleTestErrorHandler');

		$this->assertPattern("/<em>PostsController::<\/em><em>something\(\)<\/em>/", $result);
		$this->assertPattern("/posts(\/|\\\)this_is_missing.whatever/", $result);

		$this->PostsController->ext = ".bad";
		$View = new TestView($this->PostsController);
		$result = str_replace(array("\t", "\r\n", "\n"), "", $View->render('index'));

		$this->assertPattern("/<meta http-equiv=\"Content-Type\" content=\"text\/html; charset=utf-8\" \/><title>/", $result);
		$this->assertPattern("/<div id=\"content\">posts index<\/div>/", $result);
	}
}
?>