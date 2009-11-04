<?php
/**
 * Short description for file.
 *
 * Long description for file.
 *
 * PHP Version 5.x
 *
 * CakePHP(tm) : Rapid Development Framework (http://www.cakephp.org)
 * Copyright 2005-2009, Cake Software Foundation, Inc. (http://www.cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2009, Cake Software Foundation, Inc. (http://www.cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       cake
 * @subpackage    cake.cake.libs
 * @since         CakePHP(tm) v 1.0.0.2363
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */

/**
 * Configuration class (singleton). Used for managing runtime configuration information.
 *
 * @package       cake
 * @subpackage    cake.cake.libs
 * @link          http://book.cakephp.org/view/42/The-Configuration-Class
 */
final class Configure extends Object {

/**
 * Array of values written and read
 *
 * @var array
 * @access private
 */
	private static $__values = array('debug' => null);

/**
 * Initialization of required libraries
 *
 * @return void
 * @access private
 */
	public static function init() {
		self::__loadBootstrap(true);
	}

/**
 * Used to store a dynamic variable in the Configure instance.
 *
 * Usage:
 * {{{
 * Configure::write('One.key1', 'value of the Configure::One[key1]');
 * Configure::write(array('One.key1' => 'value of the Configure::One[key1]'));
 * Configure::write('One', array(
 *     'key1' => 'value of the Configure::One[key1]',
 *     'key2' => 'value of the Configure::One[key2]'
 * );
 *
 * Configure::write(array(
 *     'One.key1' => 'value of the Configure::One[key1]',
 *     'One.key2' => 'value of the Configure::One[key2]'
 * ));
 * }}}
 *
 * @link http://book.cakephp.org/view/412/write
 * @param array $config Name of var to write
 * @param mixed $value Value to set for var
 * @return void
 * @access public
 */
	public static function write($config, $value = null) {
		if (!is_array($config)) {
			$config = array($config => $value);
		}
		
		foreach ($config as $name => $value) {
			if (strpos($name, '.') === false) {
				self::$__values[$name] = $value;
			} else {
				$names = explode('.', $name, 2);
				if (!isset(self::$__values[$names[0]])) {
					self::$__values[$names[0]] = array();
				}
				self::$__values[$names[0]] = Set::insert(self::$__values[$names[0]], $names[1], $value);
			}
		}

		if (isset($config['debug'])) {
			$reporting = 0;
			if (self::$__values['debug']) {
				if (!class_exists('Debugger')) {
					require LIBS . 'debugger.php';
				}
				$reporting = E_ALL & ~E_DEPRECATED;
				if (function_exists('ini_set')) {
					ini_set('display_errors', 1);
				}
			} elseif (function_exists('ini_set')) {
				ini_set('display_errors', 0);
			}

			if (isset(self::$__values['log']) && self::$__values['log']) {
				if (!class_exists('CakeLog')) {
					require LIBS . 'cake_log.php';
				}
				if (is_integer(self::$__values['log']) && !self::$__values['debug']) {
					$reporting = self::$__values['log'];
				} else {
					$reporting = E_ALL & ~E_DEPRECATED;
				}
			}
			error_reporting($reporting);
		}
	}

/**
 * Used to read information stored in the Configure instance.
 *
 * Usage
 * Configure::read('Name'); will return all values for Name
 * Configure::read('Name.key'); will return only the value of Configure::Name[key]
 *
 * @link http://book.cakephp.org/view/413/read
 * @param string $var Variable to obtain
 * @return string value of Configure::$var
 * @access public
 */
	public static function read($var = 'debug') {
		if (strpos($var, '.') !== false) {
			$names = explode('.', $var, 2);
			$var = $names[0];
		}
		if (!isset(self::$__values[$var])) {
			return null;
		}
		if (!empty($names[1])) {
			return Set::extract(self::$__values[$var], $names[1]);
		}

		return self::$__values[$var];
	}

/**
 * Used to delete a variable from the Configure instance.
 *
 * Usage:
 * Configure::delete('Name'); will delete the entire Configure::Name
 * Configure::delete('Name.key'); will delete only the Configure::Name[key]
 *
 * @link          http://book.cakephp.org/view/414/delete
 * @param string $var the var to be deleted
 * @return void
 * @access public
 */
	public static function delete($var = null) {
		if (strpos($var, '.') === false) {
			unset(self::$__values[$var]);
			return;
		}
		$names = explode('.', $var, 2);
		self::$__values[$names[0]] = Set::remove(self::$__values[$names[0]], $names[1]);
	}

/**
 * Loads a file from app/config/configure_file.php.
 * Config file variables should be formated like:
 *  $config['name'] = 'value';
 * These will be used to create dynamic Configure vars.
 *
 * Usage Configure::load('configure_file');
 *
 * @link          http://book.cakephp.org/view/415/load
 * @param string $fileName name of file to load, extension must be .php and only the name
 *                         should be used, not the extenstion
 * @return mixed false if file not found, void if load successful
 * @access public
 */
	public static function load($fileName) {
		$found = false;

		if (file_exists(CONFIGS . $fileName . '.php')) {
			include(CONFIGS . $fileName . '.php');
			$found = true;
		} elseif (file_exists(CACHE . 'persistent' . DS . $fileName . '.php')) {
			include(CACHE . 'persistent' . DS . $fileName . '.php');
			$found = true;
		} else {
			foreach (App::core('cake') as $key => $path) {
				if (file_exists($path . DS . 'config' . DS . $fileName . '.php')) {
					include($path . DS . 'config' . DS . $fileName . '.php');
					$found = true;
					break;
				}
			}
		}

		if (!$found) {
			return false;
		}

		if (!isset($config)) {
			$error = __("Configure::load() - no variable \$config found in %s.php", true);
			trigger_error(sprintf($error, $fileName), E_USER_WARNING);
			return false;
		}
		return Configure::write($config);
	}

/**
 * Used to determine the current version of CakePHP.
 *
 * Usage Configure::version();
 *
 * @link          http://book.cakephp.org/view/416/version
 * @return string Current version of CakePHP
 * @access public
 */
	public static function version() {
		if (!isset(self::$__values['Cake']['version'])) {
			require(CORE_PATH . 'cake' . DS . 'config' . DS . 'config.php');
			self::write($config);
		}
		return self::$__values['Cake']['version'];
	}

/**
 * Used to write a config file to disk.
 *
 * Configure::store('Model', 'class.paths', array('Users' => array(
 *      'path' => 'users', 'plugin' => true
 * )));
 *
 * @param string $type Type of config file to write, ex: Models, Controllers, Helpers, Components
 * @param string $name file name.
 * @param array $data array of values to store.
 * @return void
 * @access public
 */
	public static function store($type, $name, $data = array()) {
		$write = true;
		$content = '';

		foreach ($data as $key => $value) {
			$content .= "\$config['$type']['$key']";

			if (is_array($value)) {
				$content .= " = array(";

				foreach ($value as $key1 => $value2) {
					$value2 = addslashes($value2);
					$content .= "'$key1' => '$value2', ";
				}
				$content .= ");\n";
			} else {
				$value = addslashes($value);
				$content .= " = '$value';\n";
			}
		}
		if (is_null($type)) {
			$write = false;
		}
		Configure::__writeConfig($content, $name, $write);
	}

/**
 * Creates a cached version of a configuration file.
 * Appends values passed from Configure::store() to the cached file
 *
 * @param string $content Content to write on file
 * @param string $name Name to use for cache file
 * @param boolean $write true if content should be written, false otherwise
 * @return void
 * @access private
 */
	private static function __writeConfig($content, $name, $write = true) {
		$file = CACHE . 'persistent' . DS . $name . '.php';

		if (Configure::read() > 0) {
			$expires = "+10 seconds";
		} else {
			$expires = "+999 days";
		}
		$cache = cache('persistent' . DS . $name . '.php', null, $expires);

		if ($cache === null) {
			cache('persistent' . DS . $name . '.php', "<?php\n\$config = array();\n", $expires);
		}

		if ($write === true) {
			if (!class_exists('File')) {
				require LIBS . 'file.php';
			}
			$fileClass = new File($file);

			if ($fileClass->writable()) {
				$fileClass->append($content);
			}
		}
	}

/**
 * Loads app/config/bootstrap.php.
 * If the alternative paths are set in this file
 * they will be added to the paths vars.
 *
 * @param boolean $boot Load application bootstrap (if true)
 * @return void
 * @access private
 */
	private function __loadBootstrap($boot) {
		$libPaths = $modelPaths = $behaviorPaths = $controllerPaths = $componentPaths = $viewPaths = $helperPaths = $pluginPaths = $vendorPaths = $localePaths = $shellPaths = null;

		if ($boot) {
			self::write('App', array('base' => false, 'baseUrl' => false, 'dir' => APP_DIR, 'webroot' => WEBROOT_DIR));

			if (!include(CONFIGS . 'core.php')) {
				trigger_error(sprintf(__("Can't find application core file. Please create %score.php, and make sure it is readable by PHP.", true), CONFIGS), E_USER_ERROR);
			}

			if (!include(CONFIGS . 'bootstrap.php')) {
				trigger_error(sprintf(__("Can't find application bootstrap file. Please create %sbootstrap.php, and make sure it is readable by PHP.", true), CONFIGS), E_USER_ERROR);
			}

			if (self::read('Cache.disable') !== true) {
				$cache = Cache::config('default');

				if (empty($cache['settings'])) {
					trigger_error('Cache not configured properly. Please check Cache::config(); in APP/config/core.php', E_USER_WARNING);
					$cache = Cache::config('default', array('engine' => 'File'));
				}
				$path = $prefix = $duration = null;

				if (!empty($cache['settings']['path'])) {
					$path = realpath($cache['settings']['path']);
				} else {
					$prefix = $cache['settings']['prefix'];
				}

				if (self::read() >= 1) {
					$duration = '+10 seconds';
				} else {
					$duration = '+999 days';
				}

				if (Cache::config('_cake_core_') === false) {
					Cache::config('_cake_core_', array_merge((array)$cache['settings'], array(
						'prefix' => $prefix . 'cake_core_', 'path' => $path . DS . 'persistent' . DS,
						'serialize' => true, 'duration' => $duration
					)));
				}

				if (Cache::config('_cake_model_') === false) {
					Cache::config('_cake_model_', array_merge((array)$cache['settings'], array(
						'prefix' => $prefix . 'cake_model_', 'path' => $path . DS . 'models' . DS,
						'serialize' => true, 'duration' => $duration
					)));
				}
				Cache::config('default');
			}
			if (App::path('controllers') == array()) {
				App::build(array(
					'models' => $modelPaths, 'views' => $viewPaths, 'controllers' => $controllerPaths,
					'helpers' => $helperPaths, 'components' => $componentPaths, 'behaviors' => $behaviorPaths,
					'plugins' => $pluginPaths, 'vendors' => $vendorPaths, 'locales' => $localePaths,
					'shells' => $shellPaths, 'libs' => $libPaths
				));
			}
		}
	}
}

/**
 * Class and file loader.
 *
 * @link          http://book.cakephp.org/view/499/The-App-Class
 * @since         CakePHP(tm) v 1.2.0.6001
 * @package       cake
 * @subpackage    cake.cake.libs
 */
class App extends Object {

/**
 * List of object types and their properties
 *
 * @var array
 * @access public
 */
	private static $types = array(
		'class' => array('suffix' => '.php', 'extends' => null, 'core' => true),
		'file' => array('suffix' => '.php', 'extends' => null, 'core' => true),
		'model' => array('suffix' => '.php', 'extends' => 'AppModel', 'core' => false),
		'behavior' => array('suffix' => '.php', 'extends' => 'ModelBehavior', 'core' => true),
		'controller' => array('suffix' => '_controller.php', 'extends' => 'AppController', 'core' => true),
		'component' => array('suffix' => '.php', 'extends' => null, 'core' => true),
		'lib' => array('suffix' => '.php', 'extends' => null, 'core' => true),
		'view' => array('suffix' => '.php', 'extends' => null, 'core' => true),
		'helper' => array('suffix' => '.php', 'extends' => 'AppHelper', 'core' => true),
		'vendor' => array('suffix' => '', 'extends' => null, 'core' => true),
		'shell' => array('suffix' => '.php', 'extends' => 'Shell', 'core' => true),
		'plugin' => array('suffix' => '', 'extends' => null, 'core' => true)
	);

/**
 * List of additional path(s) where model files reside.
 *
 * @var array
 * @access public
 */
	private static $models = array();

/**
 * List of additional path(s) where behavior files reside.
 *
 * @var array
 * @access public
 */
	private static $behaviors = array();

/**
 * List of additional path(s) where controller files reside.
 *
 * @var array
 * @access public
 */
	private static $controllers = array();

/**
 * List of additional path(s) where component files reside.
 *
 * @var array
 * @access public
 */
	private static $components = array();

/**
 * List of additional path(s) where datasource files reside.
 *
 * @var array
 * @access private
 */
	private static $datasources = array();

/**
 * List of additional path(s) where libs files reside.
 *
 * @var array
 * @access public
 */
	private static $libs = array();
/**
 * List of additional path(s) where view files reside.
 *
 * @var array
 * @access public
 */
	private static $views = array();

/**
 * List of additional path(s) where helper files reside.
 *
 * @var array
 * @access public
 */
	private static $helpers = array();

/**
 * List of additional path(s) where plugins reside.
 *
 * @var array
 * @access public
 */
	private static $plugins = array();

/**
 * List of additional path(s) where vendor packages reside.
 *
 * @var array
 * @access public
 */
	private static $vendors = array();

/**
 * List of additional path(s) where locale files reside.
 *
 * @var array
 * @access public
 */
	private static $locales = array();

/**
 * List of additional path(s) where console shell files reside.
 *
 * @var array
 * @access public
 */
	private static $shells = array();

/**
 * Paths to search for files.
 *
 * @var array
 * @access public
 */
	private static $search = array();

/**
 * Whether or not to return the file that is loaded.
 *
 * @var boolean
 * @access public
 */
	private static $return = false;

/**
 * Determines if $__maps and $__paths cache should be written.
 *
 * @var boolean
 * @access private
 */
	private static $__cache = false;

/**
 * Holds key/value pairs of $type => file path.
 *
 * @var array
 * @access private
 */
	private static $__map = array();

/**
 * Holds paths for deep searching of files.
 *
 * @var array
 * @access private
 */
	private static $__paths = array();

/**
 * Holds loaded files.
 *
 * @var array
 * @access private
 */
	private static $__loaded = array();

/**
 * Holds and key => value array of object types.
 *
 * @var array
 * @access private
 */
	private static $__objects = array();

/**
 * Used to read information stored path
 *
 * Usage
 * App::path('models'); will return all paths for models
 *
 * @param string $type type of path
 * @return string array
 * @access public
 */
	function path($type) {
		if (!isset(self::${$type})) {
			return array();
		}
		return self::${$type};
	}

/**
 * Build path references. Merges the supplied $paths
 * with the base paths and the default core paths.
 *
 * @param array $paths paths defines in config/bootstrap.php
 * @param boolean $reset true will set paths, false merges paths [default] false
 * @return void
 * @access public
 */
	public function build($paths = array(), $reset = false) {
		$defaults = array(
			'models' => array(MODELS),
			'behaviors' => array(BEHAVIORS),
			'datasources' => array(MODELS . 'datasources'),
			'controllers' => array(CONTROLLERS),
			'components' => array(COMPONENTS),
			'libs' => array(APPLIBS),
			'views' => array(VIEWS),
			'helpers' => array(HELPERS),
			'locales' => array(APP . 'locale' . DS),
			'shells' => array(APP . 'vendors' . DS . 'shells' . DS, VENDORS . 'shells' . DS),
			'vendors' => array(APP . 'vendors' . DS, VENDORS),
			'plugins' => array(APP . 'plugins' . DS)
		);

		if ($reset == true) {
			foreach ($paths as $type => $new) {
				self::${$type} = (array)$new;
			}
			return $paths;
		}

		$core = self::core();
		$app = array('models' => true, 'controllers' => true, 'helpers' => true);

		foreach ($defaults as $type => $default) {
			$merge = array();

			if (isset($app[$type])) {
				$merge = array(APP);
			}
			if (isset($core[$type])) {
				$merge = array_merge($merge, (array)$core[$type]);
			}

			self::${$type} = $default;

			if (!empty($paths[$type])) {
				$path = array_flip(array_flip((array_merge(
					self::${$type}, (array)$paths[$type], $merge
				))));
				self::${$type} = array_values($path);
			} else {
				$path = array_flip(array_flip((array_merge(self::${$type}, $merge))));
				self::${$type} = array_values($path);
			}
		}
	}

/**
 * Get the path that a plugin is on.  Searches through the defined plugin paths.
 *
 * @param string $plugin CamelCased plugin name to find the path of.
 * @return string full path to the plugin.
 **/
	function pluginPath($plugin) {
		$_this = App::getInstance();
		$pluginDir = Inflector::underscore($plugin);
		for ($i = 0, $length = count($_this->plugins); $i < $length; $i++) {
			if (is_dir($_this->plugins[$i] . $pluginDir)) {
				return $_this->plugins[$i] . $pluginDir . DS ;
			}
		}
		return $_this->plugins[0] . $pluginDir . DS;
	}

/**
 * Returns a key/value list of all paths where core libs are found.
 * Passing $type only returns the values for a given value of $key.
 *
 * @param string $type valid values are: 'model', 'behavior', 'controller', 'component',
 *                      'view', 'helper', 'datasource', 'libs', and 'cake'
 * @return array numeric keyed array of core lib paths
 * @access public
 */
	function core($type = null) {
		$paths = Cache::read('core_paths', '_cake_core_');
		if (!$paths) {
			$paths = array();
			$openBasedir = ini_get('open_basedir');
			if ($openBasedir) {
				$all = explode(PATH_SEPARATOR, $openBasedir);
				$all = array_flip(array_flip((array_merge(array(CAKE_CORE_INCLUDE_PATH), $all))));
			} else {
				$all = explode(PATH_SEPARATOR, ini_get('include_path'));
				$all = array_flip(array_flip((array_merge(array(CAKE_CORE_INCLUDE_PATH), $all))));
			}
			foreach ($all as $path) {
				if ($path !== DS) {
					$path = rtrim($path, DS);
				}
				if (empty($path) || $path === '.') {
					continue;
				}
				$cake = $path .  DS . 'cake' . DS;
				$libs = $cake . 'libs' . DS;
				if (is_dir($libs)) {
					$paths['cake'][] = $cake;
					$paths['libs'][] = $libs;
					$paths['models'][] = $libs . 'model' . DS;
					$paths['datasources'][] = $libs . 'model' . DS . 'datasources' . DS;
					$paths['behaviors'][] = $libs . 'model' . DS . 'behaviors' . DS;
					$paths['controllers'][] = $libs . 'controller' . DS;
					$paths['components'][] = $libs . 'controller' . DS . 'components' . DS;
					$paths['views'][] = $libs . 'view' . DS;
					$paths['helpers'][] = $libs . 'view' . DS . 'helpers' . DS;
					$paths['plugins'][] = $path . DS . 'plugins' . DS;
					$paths['vendors'][] = $path . DS . 'vendors' . DS;
					$paths['shells'][] = $cake . 'console' . DS . 'libs' . DS;
					break;
				}
			}
			Cache::write('core_paths', array_filter($paths), '_cake_core_');
		}
		if ($type && isset($paths[$type])) {
			return $paths[$type];
		}
		return $paths;
	}

/**
 * Returns an index of objects of the given type, with the physical path to each object.
 *
 * @param string	$type Type of object, i.e. 'model', 'controller', 'helper', or 'plugin'
 * @param mixed		$path Optional
 * @return Configure instance
 * @access public
 */
	function objects($type, $path = null, $cache = true) {
		$objects = array();
		$extension = false;
		$name = $type;

		if ($type === 'file' && !$path) {
			return false;
		} elseif ($type === 'file') {
			$extension = true;
			$name = $type . str_replace(DS, '', $path);
		}

		if (empty(self::$__objects) && $cache === true) {
			self::$__objects = Cache::read('object_map', '_cake_core_');
		}

		if (empty(self::$__objects) || !isset(self::$__objects[$type]) || $cache !== true) {
			$types = self::$types;

			if (!isset($types[$type])) {
				return false;
			}
			$objects = array();

			if (empty($path)) {
				$path = self::${"{$type}s"};
				if (isset($types[$type]['core']) && $types[$type]['core'] === false) {
					array_pop($path);
				}
			}
			$items = array();

			foreach ((array)$path as $dir) {
				if ($type === 'file' || $type === 'class' || strpos($dir, $type) !== false) {
					$items = self::__list($dir, $types[$type]['suffix'], $extension);
					$objects = array_merge($items, array_diff($objects, $items));
				}
			}

			if ($type !== 'file') {
				foreach ($objects as $key => $value) {
					$objects[$key] = Inflector::camelize($value);
				}
			}

			if ($cache === true) {
				self::$__cache = true;
			}
			self::$__objects[$name] = $objects;
		}

		return self::$__objects[$name];
	}

/**
 * Finds classes based on $name or specific file(s) to search.
 *
 * @link          http://book.cakephp.org/view/529/Using-App-import
 * @param mixed $type The type of Class if passed as a string, or all params can be passed as
 *                    an single array to $type,
 * @param string $name Name of the Class or a unique name for the file
 * @param mixed $parent boolean true if Class Parent should be searched, accepts key => value
 *              array('parent' => $parent ,'file' => $file, 'search' => $search, 'ext' => '$ext');
 *              $ext allows setting the extension of the file name
 *              based on Inflector::underscore($name) . ".$ext";
 * @param array $search paths to search for files, array('path 1', 'path 2', 'path 3');
 * @param string $file full name of the file to search for including extension
 * @param boolean $return, return the loaded file, the file must have a return
 *                         statement in it to work: return $variable;
 * @return boolean true if Class is already in memory or if file is found and loaded, false if not
 * @access public
 */
	public static function import($type = null, $name = null, $parent = true, $search = array(), $file = null, $return = false) {
		if (empty(self::$__map)) {
			self::$__map = Cache::read('file_map', '_cake_core_');
		}
		
		$plugin = $directory = null;

		if (is_array($type)) {
			extract($type, EXTR_OVERWRITE);
		}

		if (is_array($parent)) {
			extract($parent, EXTR_OVERWRITE);
		}

		if ($name === null && $file === null) {
			$name = $type;
			$type = 'Core';
		} elseif ($name === null) {
			$type = 'File';
		}

		if (is_array($name)) {
			foreach ($name as $class) {
				$tempType = $type;
				$plugin = null;

				if (strpos($class, '.') !== false) {
					$value = explode('.', $class);
					$count = count($value);

					if ($count > 2) {
						$tempType = $value[0];
						$plugin = $value[1] . '.';
						$class = $value[2];
					} elseif ($count === 2 && ($type === 'Core' || $type === 'File')) {
						$tempType = $value[0];
						$class = $value[1];
					} else {
						$plugin = $value[0] . '.';
						$class = $value[1];
					}
				}

				if (!self::import($tempType, $plugin . $class, $parent)) {
					return false;
				}
			}
			return true;
		}

		if ($name != null && strpos($name, '.') !== false) {
			list($plugin, $name) = explode('.', $name);
			$plugin = Inflector::camelize($plugin);
		}

		self::$return = $return;

		if (isset($ext)) {
			$file = Inflector::underscore($name) . ".{$ext}";
		}
		$ext = self::__settings($type, $plugin, $parent);
		if ($name != null && !class_exists($name . $ext['class'])) {
			if ($load = self::__mapped($name . $ext['class'], $type, $plugin)) {
				if (self::__load($load)) {

					if (self::$return) {
						$value = include $load;
						return $value;
					}
					return true;
				} else {
					self::__remove($name . $ext['class'], $type, $plugin);
					self::$__cache = true;
				}
			}
			if (!empty($search)) {
				self::$search = $search;
			} elseif ($plugin) {
				self::$search = self::__paths('plugin');
			} else {
				self::$search = self::__paths($type);
			}
			$find = $file;

			if ($find === null) {
				$find = Inflector::underscore($name . $ext['suffix']).'.php';

				if ($plugin) {
					$paths = self::$search;
					foreach ($paths as $key => $value) {
						self::$search[$key] = $value . $ext['path'];
					}
				}
			}

			if (strtolower($type) !== 'vendor' && empty($search) && self::__load($file)) {
				$directory = false;
			} else {
				$file = $find;
				$directory = self::__find($find, true);
			}

			if ($directory !== null) {
				self::$__cache = true;
				self::__map($directory . $file, $name . $ext['class'], $type, $plugin);

				if (self::$return) {
					$value = include $directory . $file;
					return $value;
				}
				return true;
			}
			return false;
		}
		return true;
	}

/**
 * Locates the $file in $__paths, searches recursively.
 *
 * @param string $file full file name
 * @param boolean $recursive search $__paths recursively
 * @return mixed boolean on fail, $file directory path on success
 * @access private
 */
	private static function __find($file, $recursive = true) {
		if (empty(self::$search)) {
			return null;
		} elseif (is_string(self::$search)) {
			self::$search = array(self::$search);
		}

		if (empty(self::$__paths)) {
			self::$__paths = Cache::read('dir_map', '_cake_core_');
		}

		foreach (self::$search as $path) {
			$path = rtrim($path, DS);

			if ($path === rtrim(APP, DS)) {
				$recursive = false;
			}
			if ($recursive === false) {
				if (self::__load($path . DS . $file)) {
					return $path . DS;
				}
				continue;
			}

			if (!isset(self::$__paths[$path])) {
				if (!class_exists('Folder')) {
					require LIBS . 'folder.php';
				}
				$Folder = new Folder();
				$directories = $Folder->tree($path, array('.svn', 'tests', 'templates'), 'dir');
				sort($directories);
				self::$__paths[$path] = $directories;
			}

			foreach (self::$__paths[$path] as $directory) {
				if (self::__load($directory . DS . $file)) {
					return $directory . DS;
				}
			}
		}
		return null;
	}

/**
 * Attempts to load $file.
 *
 * @param string $file full path to file including file name
 * @return boolean
 * @access private
 */
	private static function __load($file) {
		if (empty($file)) {
			return false;
		}
		if (!self::$return && isset(self::$__loaded[$file])) {
			return true;
		}
		if (file_exists($file)) {
			if (!self::$return) {
				require($file);
				self::$__loaded[$file] = true;
			}
			return true;
		}
		return false;
	}

/**
 * Maps the $name to the $file.
 *
 * @param string $file full path to file
 * @param string $name unique name for this map
 * @param string $type type object being mapped
 * @param string $plugin camelized if object is from a plugin, the name of the plugin
 * @access private
 */
	private static function __map($file, $name, $type, $plugin) {
		if ($plugin) {
			self::$__map['Plugin'][$plugin][$type][$name] = $file;
		} else {
			self::$__map[$type][$name] = $file;
		}
	}

/**
 * Returns a file's complete path.
 *
 * @param string $name unique name
 * @param string $type type object
 * @param string $plugin camelized if object is from a plugin, the name of the plugin
 * @return mixed, file path if found, false otherwise
 * @access private
 */
	private static function __mapped($name, $type, $plugin) {
		if ($plugin) {
			if (isset(self::$__map['Plugin'][$plugin][$type]) && isset(self::$__map['Plugin'][$plugin][$type][$name])) {
				return self::$__map['Plugin'][$plugin][$type][$name];
			}
			return false;
		}

		if (isset(self::$__map[$type]) && isset(self::$__map[$type][$name])) {
			return self::$__map[$type][$name];
		}
		return false;
	}

/**
 * Used to overload objects as needed.
 *
 * @param string $type Model or Helper
 * @param string $name Class name to overload
 * @access private
 * @todo remove
 */
	private function __overload($type, $name, $parent) {
		if (($type === 'Model' || $type === 'Helper') && $parent !== false) {
			Overloadable::overload($name);
		}
	}

/**
 * Loads parent classes based on $type.
 * Returns a prefix or suffix needed for loading files.
 *
 * @param string $type type of object
 * @param string $plugin camelized name of plugin
 * @param boolean $parent false will not attempt to load parent
 * @return array
 * @access private
 */
	private static function __settings($type, $plugin, $parent) {
		if (!$parent) {
			return null;
		}

		if ($plugin) {
			$pluginPath = Inflector::underscore($plugin);
		}
		$path = null;
		$load = strtolower($type);

		switch ($load) {
			case 'model':
				if (!class_exists('Model')) {
					require LIBS . 'model' . DS . 'model.php';
				}
				if (!class_exists('AppModel')) {
					self::import($type, 'AppModel', false);
				}
				if ($plugin) {
					if (!class_exists($plugin . 'AppModel')) {
						self::import($type, $plugin . '.' . $plugin . 'AppModel', false, array(), $pluginPath . DS . $pluginPath . '_app_model.php');
					}
					$path = $pluginPath . DS . 'models' . DS;
				}
				return array('class' => null, 'suffix' => null, 'path' => $path);
			break;
			case 'behavior':
				if ($plugin) {
					$path = $pluginPath . DS . 'models' . DS . 'behaviors' . DS;
				}
				return array('class' => $type, 'suffix' => null, 'path' => $path);
			break;
			case 'controller':
				self::import($type, 'AppController', false);
				if ($plugin) {
					self::import($type, $plugin . '.' . $plugin . 'AppController', false, array(), $pluginPath . DS . $pluginPath . '_app_controller.php');
					$path = $pluginPath . DS . 'controllers' . DS;
				}
				return array('class' => $type, 'suffix' => $type, 'path' => $path);
			break;
			case 'component':
				if ($plugin) {
					$path = $pluginPath . DS . 'controllers' . DS . 'components' . DS;
				}
				return array('class' => $type, 'suffix' => null, 'path' => $path);
			break;
			case 'lib':
				if ($plugin) {
					$path = $pluginPath . DS . 'libs' . DS;
				}
				return array('class' => null, 'suffix' => null, 'path' => $path);
			break;
			case 'view':
				if ($plugin) {
					$path = $pluginPath . DS . 'views' . DS;
				}
				return array('class' => $type, 'suffix' => null, 'path' => $path);
			break;
			case 'helper':
				if (!class_exists('AppHelper')) {
					self::import($type, 'AppHelper', false);
				}
				if ($plugin) {
					$path = $pluginPath . DS . 'views' . DS . 'helpers' . DS;
				}
				return array('class' => $type, 'suffix' => null, 'path' => $path);
			break;
			case 'vendor':
				if ($plugin) {
					$path = $pluginPath . DS . 'vendors' . DS;
				}
				return array('class' => null, 'suffix' => null, 'path' => $path);
			break;
			default:
				$type = $suffix = $path = null;
			break;
		}
		return array('class' => null, 'suffix' => null, 'path' => null);
	}

/**
 * Returns default search paths.
 *
 * @param string $type type of object to be searched
 * @return array list of paths
 * @access private
 */
	private static function __paths($type) {
		$type = strtolower($type);
		$paths = array();

		if ($type === 'core') {
			return self::core('libs');
		}
		if ($paths = App::path($type .'s')) {
			return $paths;
		}
		return $paths;
	}

/**
 * Removes file location from map if the file has been deleted.
 *
 * @param string $name name of object
 * @param string $type type of object
 * @param string $plugin camelized name of plugin
 * @return void
 * @access private
 */
	private static function __remove($name, $type, $plugin) {
		if ($plugin) {
			unset(self::$__map['Plugin'][$plugin][$type][$name]);
		} else {
			unset(self::$__map[$type][$name]);
		}
	}

/**
 * Returns an array of filenames of PHP files in the given directory.
 *
 * @param  string $path Path to scan for files
 * @param  string $suffix if false, return only directories. if string, match and return files
 * @return array  List of directories or files in directory
 */
	function __list($path, $suffix = false, $extension = false) {
		if (!class_exists('Folder')) {
			require LIBS . 'folder.php';
		}
		$items = array();
		$Folder =& new Folder($path);
		$contents = $Folder->read(false, true);

		if (is_array($contents)) {
			if (!$suffix) {
				return $contents[0];
			} else {
				foreach ($contents[1] as $item) {
					if (substr($item, - strlen($suffix)) === $suffix) {
						if ($extension) {
							$items[] = $item;
						} else {
							$items[] = substr($item, 0, strlen($item) - strlen($suffix));
						}
					}
				}
			}
		}
		return $items;
	}

/**
 * Object destructor.
 *
 * Writes cache file if changes have been made to the $__map or $__paths
 *
 * @return void
 * @access public
 */
	public static function destruct__() {
		if (self::$__cache) {
			$core = self::core('cake');
			unset(self::$__paths[rtrim($core[0], DS)]);
			Cache::write('dir_map', array_filter(self::$__paths), '_cake_core_');
			Cache::write('file_map', array_filter(self::$__map), '_cake_core_');
			Cache::write('object_map', self::$__objects, '_cake_core_');
		}
	}
}
register_shutdown_function(array('App','destruct__'));
?>