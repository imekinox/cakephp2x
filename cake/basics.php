<?php
/**
 * Basic Cake functionality.
 *
 * Core functions for including other source files, loading models and so forth.
 *
 * PHP Version 5.x
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2009, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2009, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       cake
 * @subpackage    cake.cake
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * Basic defines for timing functions.
 */
	define('SECOND', 1);
	define('MINUTE', 60 * SECOND);
	define('HOUR', 60 * MINUTE);
	define('DAY', 24 * HOUR);
	define('WEEK', 7 * DAY);
	define('MONTH', 30 * DAY);
	define('YEAR', 365 * DAY);

/**
 * Loads configuration files. Receives a set of configuration files
 * to load.
 * Example:
 *
 * `config('config1', 'config2');`
 *
 * @return boolean Success
 */
	function config() {
		$args = func_get_args();
		foreach ($args as $arg) {
			if ($arg === 'database' && file_exists(CONFIGS . 'database.php')) {
				include_once(CONFIGS . $arg . '.php');
			} elseif (file_exists(CONFIGS . $arg . '.php')) {
				include_once(CONFIGS . $arg . '.php');

				if (count($args) == 1) {
					return true;
				}
			} else {
				if (count($args) == 1) {
					return false;
				}
			}
		}
		return true;
	}

/**
 * Prints out debug information about given variable.
 *
 * Only runs if debug level is greater than zero.
 *
 * @param boolean $var Variable to show debug information for.
 * @param boolean $showHtml If set to true, the method prints the debug data in a screen-friendly way.
 * @param boolean $showFrom If set to true, the method prints from where the function was called.
 * @link http://book.cakephp.org/view/458/Basic-Debugging
 */
	function debug($var = false, $showHtml = false, $showFrom = true) {
		if (Configure::read() > 0) {
			if ($showFrom) {
				$calledFrom = debug_backtrace();
				echo '<strong>' . substr(str_replace(ROOT, '', $calledFrom[0]['file']), 1) . '</strong>';
				echo ' (line <strong>' . $calledFrom[0]['line'] . '</strong>)';
			}
			echo "\n<pre class=\"cake-debug\">\n";

			$var = print_r($var, true);
			if ($showHtml) {
				$var = str_replace('<', '&lt;', str_replace('>', '&gt;', $var));
			}
			echo $var . "\n</pre>\n";
		}
	}
/**
 * Sorts given $array by key $sortby.
 *
 * @param array $array Array to sort
 * @param string $sortby Sort by this key
 * @param string $order  Sort order asc/desc (ascending or descending).
 * @param integer $type Type of sorting to perform
 * @return mixed Sorted array
 */
	function sortByKey(&$array, $sortby, $order = 'asc', $type = SORT_NUMERIC) {
		if (!is_array($array)) {
			return null;
		}

		foreach ($array as $key => $val) {
			$sa[$key] = $val[$sortby];
		}

		if ($order == 'asc') {
			asort($sa, $type);
		} else {
			arsort($sa, $type);
		}

		foreach ($sa as $key => $val) {
			$out[] = $array[$key];
		}
		return $out;
	}
/**
 * Convenience method for htmlspecialchars.
 *
 * @param string $text Text to wrap through htmlspecialchars
 * @param string $charset Character set to use when escaping.  Defaults to config value in 'App.encoding' or 'UTF-8'
 * @return string Wrapped text
 * @link http://book.cakephp.org/view/703/h
 */
	function h($text, $charset = null) {
		if (is_array($text)) {
			return array_map('h', $text);
		}
		if (empty($charset)) {
			$charset = Configure::read('App.encoding');
		}
		if (empty($charset)) {
			$charset = 'UTF-8';
		}
		return htmlspecialchars($text, ENT_QUOTES, $charset);
	}

/**
 * Splits a dot syntax plugin name into its plugin and classname.
 * If $name does not have a dot, then index 0 will be null.
 *
 * Commonly used like `list($plugin, $name) = pluginSplit($name);`
 *
 * @param string $name The name you want to plugin split.
 * @param boolean $dotAppend Set to true if you want the plugin to have a '.' appended to it.
 * @param string $plugin Optional default plugin to use if no plugin is found. Defaults to null.
 * @return array Array with 2 indexes.  0 => plugin name, 1 => classname
 */
	function pluginSplit($name, $dotAppend = false, $plugin = null) {
		if (strpos($name, '.') !== false) {
			$parts = explode('.', $name, 2);
			if ($dotAppend) {
				$parts[0] .= '.';
			}
			return $parts;
		}
		return array($plugin, $name);
	}

/**
 * Returns an array of all the given parameters.
 *
 * Example:
 *
 * `a('a', 'b')`
 *
 * Would return:
 *
 * `array('a', 'b')`
 *
 * @return array Array of given parameters
 * @link http://book.cakephp.org/view/694/a
 */
	function a() {
		$args = func_get_args();
		return $args;
	}

/**
 * Constructs associative array from pairs of arguments.
 *
 * Example:
 *
 * `aa('a','b')`
 *
 * Would return:
 *
 * `array('a'=>'b')`
 *
 * @return array Associative array
 * @link http://book.cakephp.org/view/695/aa
 */
	function aa() {
		$args = func_get_args();
		$argc = count($args);
		for ($i = 0; $i < $argc; $i++) {
			if ($i + 1 < $argc) {
				$a[$args[$i]] = $args[$i + 1];
			} else {
				$a[$args[$i]] = null;
			}
			$i++;
		}
		return $a;
	}

/**
 * Convenience method for echo().
 *
 * @param string $text String to echo
 * @link http://book.cakephp.org/view/700/e
 */
	function e($text) {
		echo $text;
	}

/**
 * Print_r convenience function, which prints out <PRE> tags around
 * the output of given array. Similar to debug().
 *
 * @see	debug()
 * @param array $var Variable to print out
 * @param boolean $showFrom If set to true, the method prints from where the function was called
 * @link http://book.cakephp.org/view/707/pr
 */
	function pr($var) {
		if (Configure::read() > 0) {
			echo '<pre>';
			print_r($var);
			echo '</pre>';
		}
	}

/**
 * Display parameters.
 *
 * @param mixed $p Parameter as string or array
 * @return string
 */
	function params($p) {
		if (!is_array($p) || count($p) == 0) {
			return null;
		}
		if (is_array($p[0]) && count($p) == 1) {
			return $p[0];
		}
		return $p;
	}

/**
 * Merge a group of arrays
 *
 * @param array First array
 * @param array Second array
 * @param array Third array
 * @param array Etc...
 * @return array All array parameters merged into one
 * @link http://book.cakephp.org/view/696/am
 */
	function am() {
		$r = array();
		$args = func_get_args();
		foreach ($args as $a) {
			if (!is_array($a)) {
				$a = array($a);
			}
			$r = array_merge($r, $a);
		}
		return $r;
	}

/**
 * Gets an environment variable from available sources, and provides emulation
 * for unsupported or inconsistent environment variables (i.e. DOCUMENT_ROOT on
 * IIS, or SCRIPT_NAME in CGI mode).  Also exposes some additional custom
 * environment information.
 *
 * @param  string $key Environment variable name.
 * @return string Environment variable setting.
 * @link http://book.cakephp.org/view/701/env
 */
	function env($key) {
		if ($key == 'HTTPS') {
			if (isset($_SERVER['HTTPS'])) {
				return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
			}
			return (strpos(env('SCRIPT_URI'), 'https://') === 0);
		}

		if ($key == 'SCRIPT_NAME') {
			if (env('CGI_MODE') && isset($_ENV['SCRIPT_URL'])) {
				$key = 'SCRIPT_URL';
			}
		}

		$val = null;
		if (isset($_SERVER[$key])) {
			$val = $_SERVER[$key];
		} elseif (isset($_ENV[$key])) {
			$val = $_ENV[$key];
		} elseif (getenv($key) !== false) {
			$val = getenv($key);
		}

		if ($key === 'REMOTE_ADDR' && $val === env('SERVER_ADDR')) {
			$addr = env('HTTP_PC_REMOTE_ADDR');
			if ($addr !== null) {
				$val = $addr;
			}
		}

		if ($val !== null) {
			return $val;
		}

		switch ($key) {
			case 'SCRIPT_FILENAME':
				if (defined('SERVER_IIS') && SERVER_IIS === true) {
					return str_replace('\\\\', '\\', env('PATH_TRANSLATED'));
				}
			break;
			case 'DOCUMENT_ROOT':
				$name = env('SCRIPT_NAME');
				$filename = env('SCRIPT_FILENAME');
				$offset = 0;
				if (!strpos($name, '.php')) {
					$offset = 4;
				}
				return substr($filename, 0, strlen($filename) - (strlen($name) + $offset));
			break;
			case 'PHP_SELF':
				return str_replace(env('DOCUMENT_ROOT'), '', env('SCRIPT_FILENAME'));
			break;
			case 'CGI_MODE':
				return (PHP_SAPI === 'cgi');
			break;
			case 'HTTP_BASE':
				$host = env('HTTP_HOST');
				if (substr_count($host, '.') !== 1) {
					return preg_replace('/^([^.])*/i', null, env('HTTP_HOST'));
				}
			return '.' . $host;
			break;
		}
		return null;
	}

/**
 * Reads/writes temporary data to cache files or session.
 *
 * @param  string $path	File path within /tmp to save the file.
 * @param  mixed  $data	The data to save to the temporary file.
 * @param  mixed  $expires A valid strtotime string when the data expires.
 * @param  string $target  The target of the cached data; either 'cache' or 'public'.
 * @return mixed  The contents of the temporary file.
 * @deprecated Please use Cache::write() instead
 */
	function cache($path, $data = null, $expires = '+1 day', $target = 'cache') {
		if (Configure::read('Cache.disable')) {
			return null;
		}
		$now = time();

		if (!is_numeric($expires)) {
			$expires = strtotime($expires, $now);
		}

		switch (strtolower($target)) {
			case 'cache':
				$filename = CACHE . $path;
			break;
			case 'public':
				$filename = WWW_ROOT . $path;
			break;
			case 'tmp':
				$filename = TMP . $path;
			break;
		}
		$timediff = $expires - $now;
		$filetime = false;

		if (file_exists($filename)) {
			$filetime = @filemtime($filename);
		}

		if ($data === null) {
			if (file_exists($filename) && $filetime !== false) {
				if ($filetime + $timediff < $now) {
					@unlink($filename);
				} else {
					$data = @file_get_contents($filename);
				}
			}
		} elseif (is_writable(dirname($filename))) {
			@file_put_contents($filename, $data);
		}
		return $data;
	}

/**
 * Used to delete files in the cache directories, or clear contents of cache directories
 *
 * @param mixed $params As String name to be searched for deletion, if name is a directory all files in
 *   directory will be deleted. If array, names to be searched for deletion. If clearCache() without params,
 *   all files in app/tmp/cache/views will be deleted
 * @param string $type Directory in tmp/cache defaults to view directory
 * @param string $ext The file extension you are deleting
 * @return true if files found and deleted false otherwise
 */
	function clearCache($params = null, $type = 'views', $ext = '.php') {
		if (is_string($params) || $params === null) {
			$params = preg_replace('/\/\//', '/', $params);
			$cache = CACHE . $type . DS . $params;

			if (is_file($cache . $ext)) {
				@unlink($cache . $ext);
				return true;
			} elseif (is_dir($cache)) {
				$files = glob($cache . '*');

				if ($files === false) {
					return false;
				}

				foreach ($files as $file) {
					if (is_file($file)) {
						@unlink($file);
					}
				}
				return true;
			} else {
				$cache = array(
					CACHE . $type . DS . '*' . $params . $ext,
					CACHE . $type . DS . '*' . $params . '_*' . $ext
				);
				$files = array();
				while ($search = array_shift($cache)) {
					$results = glob($search);
					if ($results !== false) {
						$files = array_merge($files, $results);
					}
				}
				if (empty($files)) {
					return false;
				}
				foreach ($files as $file) {
					if (is_file($file)) {
						@unlink($file);
					}
				}
				return true;
			}
		} elseif (is_array($params)) {
			foreach ($params as $file) {
				clearCache($file, $type, $ext);
			}
			return true;
		}
		return false;
	}

/**
 * Recursively strips slashes from all values in an array
 *
 * @param array $values Array of values to strip slashes
 * @return mixed What is returned from calling stripslashes
 * @link http://book.cakephp.org/view/709/stripslashes_deep
 */
	function stripslashes_deep($values) {
		if (is_array($values)) {
			foreach ($values as $key => $value) {
				$values[$key] = stripslashes_deep($value);
			}
		} else {
			$values = stripslashes($values);
		}
		return $values;
	}

/**
 * Returns a translated string if one is found; Otherwise, the submitted message.
 *
 * @param string $singular Text to translate
 * @param boolean $return Set to true to return translated string, or false to echo
 * @return mixed translated string if $return is false string will be echoed
 * @link http://book.cakephp.org/view/693/__
 */
	function __($singular, $return = true) {
		if (!$singular) {
			return;
		}
		if (!class_exists('I18n')) {
			App::import('Core', 'i18n');
		}

		if ($return === false) {
			echo I18n::translate($singular);
		} else {
			return I18n::translate($singular);
		}
	}

/**
 * Returns correct plural form of message identified by $singular and $plural for count $count.
 * Some languages have more than one form for plural messages dependent on the count.
 *
 * @param string $singular Singular text to translate
 * @param string $plural Plural text
 * @param integer $count Count
 * @param boolean $return true to return, false to echo
 * @return mixed plural form of translated string if $return is false string will be echoed
 */
	function __n($singular, $plural, $count, $return = true) {
		if (!$singular) {
			return;
		}
		if (!class_exists('I18n')) {
			App::import('Core', 'i18n');
		}

		if ($return === false) {
			echo I18n::translate($singular, $plural, null, 6, $count);
		} else {
			return I18n::translate($singular, $plural, null, 6, $count);
		}
	}

/**
 * Allows you to override the current domain for a single message lookup.
 *
 * @param string $domain Domain
 * @param string $msg String to translate
 * @param string $return true to return, false to echo
 * @return translated string if $return is false string will be echoed
 */
	function __d($domain, $msg, $return = true) {
		if (!$msg) {
			return;
		}
		if (!class_exists('I18n')) {
			App::import('Core', 'i18n');
		}

		if ($return === false) {
			echo I18n::translate($msg, null, $domain);
		} else {
			return I18n::translate($msg, null, $domain);
		}
	}

/**
 * Allows you to override the current domain for a single plural message lookup.
 * Returns correct plural form of message identified by $singular and $plural for count $count
 * from domain $domain.
 *
 * @param string $domain Domain
 * @param string $singular Singular string to translate
 * @param string $plural Plural
 * @param integer $count Count
 * @param boolean $return true to return, false to echo
 * @return plural form of translated string if $return is false string will be echoed
 */
	function __dn($domain, $singular, $plural, $count, $return = true) {
		if (!$singular) {
			return;
		}
		if (!class_exists('I18n')) {
			App::import('Core', 'i18n');
		}

		if ($return === false) {
			echo I18n::translate($singular, $plural, $domain, 6, $count);
		} else {
			return I18n::translate($singular, $plural, $domain, 6, $count);
		}
	}

/**
 * Allows you to override the current domain for a single message lookup.
 * It also allows you to specify a category.
 *
 * The category argument allows a specific category of the locale settings to be used for fetching a message.
 * Valid categories are: LC_CTYPE, LC_NUMERIC, LC_TIME, LC_COLLATE, LC_MONETARY, LC_MESSAGES and LC_ALL.
 *
 * Note that the category must be specified with a numeric value, instead of the constant name.  The values are:
 *
 * - LC_ALL       0
 * - LC_COLLATE   1
 * - LC_CTYPE     2
 * - LC_MONETARY  3
 * - LC_NUMERIC   4
 * - LC_TIME      5
 * - LC_MESSAGES  6
 *
 * @param string $domain Domain
 * @param string $msg Message to translate
 * @param integer $category Category
 * @param boolean $return true to return, false to echo
 * @return translated string if $return is false string will be echoed
 */
	function __dc($domain, $msg, $category, $return = true) {
		if (!$msg) {
			return;
		}
		if (!class_exists('I18n')) {
			App::import('Core', 'i18n');
		}

		if ($return === false) {
			echo I18n::translate($msg, null, $domain, $category);
		} else {
			return I18n::translate($msg, null, $domain, $category);
		}
	}

/**
 * Allows you to override the current domain for a single plural message lookup.
 * It also allows you to specify a category.
 * Returns correct plural form of message identified by $singular and $plural for count $count
 * from domain $domain.
 *
 * The category argument allows a specific category of the locale settings to be used for fetching a message.
 * Valid categories are: LC_CTYPE, LC_NUMERIC, LC_TIME, LC_COLLATE, LC_MONETARY, LC_MESSAGES and LC_ALL.
 *
 * Note that the category must be specified with a numeric value, instead of the constant name.  The values are:
 *
 * - LC_ALL       0
 * - LC_COLLATE   1
 * - LC_CTYPE     2
 * - LC_MONETARY  3
 * - LC_NUMERIC   4
 * - LC_TIME      5
 * - LC_MESSAGES  6
 *
 * @param string $domain Domain
 * @param string $singular Singular string to translate
 * @param string $plural Plural
 * @param integer $count Count
 * @param integer $category Category
 * @param boolean $return true to return, false to echo
 * @return plural form of translated string if $return is false string will be echoed
 */
	function __dcn($domain, $singular, $plural, $count, $category, $return = true) {
		if (!$singular) {
			return;
		}
		if (!class_exists('I18n')) {
			App::import('Core', 'i18n');
		}

		if ($return === false) {
			echo I18n::translate($singular, $plural, $domain, $category, $count);
		} else {
			return I18n::translate($singular, $plural, $domain, $category, $count);
		}
	}

/**
 * The category argument allows a specific category of the locale settings to be used for fetching a message.
 * Valid categories are: LC_CTYPE, LC_NUMERIC, LC_TIME, LC_COLLATE, LC_MONETARY, LC_MESSAGES and LC_ALL.
 *
 * Note that the category must be specified with a numeric value, instead of the constant name.  The values are:
 *
 * - LC_ALL       0
 * - LC_COLLATE   1
 * - LC_CTYPE     2
 * - LC_MONETARY  3
 * - LC_NUMERIC   4
 * - LC_TIME      5
 * - LC_MESSAGES  6
 *
 * @param string $msg String to translate
 * @param integer $category Category
 * @param string $return true to return, false to echo
 * @return translated string if $return is false string will be echoed
 */
	function __c($msg, $category, $return = true) {
		if (!$msg) {
			return;
		}
		if (!class_exists('I18n')) {
			App::import('Core', 'i18n');
		}

		if ($return === false) {
			echo I18n::translate($msg, null, null, $category);
		} else {
			return I18n::translate($msg, null, null, $category);
		}
	}

/**
 * Shortcut to Log::write.
 *
 * @param string $message Message to write to log
 */
	function LogError($message) {
		if (!class_exists('CakeLog')) {
			App::import('Core', 'CakeLog');
		}
		$bad = array("\n", "\r", "\t");
		$good = ' ';
		CakeLog::write('error', str_replace($bad, $good, $message));
	}

/**
 * Searches include path for files.
 *
 * @param string $file File to look for
 * @return Full path to file if exists, otherwise false
 * @link http://book.cakephp.org/view/702/fileExistsInPath
 */
	function fileExistsInPath($file) {
		$paths = explode(PATH_SEPARATOR, ini_get('include_path'));
		foreach ($paths as $path) {
			$fullPath = $path . DS . $file;

			if (file_exists($fullPath)) {
				return $fullPath;
			} elseif (file_exists($file)) {
				return $file;
			}
		}
		return false;
	}

/**
 * Convert forward slashes to underscores and removes first and last underscores in a string
 *
 * @param string String to convert
 * @return string with underscore remove from start and end of string
 * @link http://book.cakephp.org/view/697/convertSlash
 */
	function convertSlash($string) {
		$string = trim($string, '/');
		$string = preg_replace('/\/\//', '/', $string);
		$string = str_replace('/', '_', $string);
		return $string;
	}

/**
 * Wraps ternary operations. If $condition is a non-empty value, $val1 is returned, otherwise $val2.
 * Don't use for isset() conditions, or wrap your variable with @ operator:
 * Example:
 *
 * `ife(isset($variable), @$variable, 'default');`
 *
 * @param mixed $condition Conditional expression
 * @param mixed $val1 Value to return in case condition matches
 * @param mixed $val2 Value to return if condition doesn't match
 * @return mixed $val1 or $val2, depending on whether $condition evaluates to a non-empty expression.
 * @link http://book.cakephp.org/view/704/ife
 */
	function ife($condition, $val1 = null, $val2 = null) {
		if (!empty($condition)) {
			return $val1;
		}
		return $val2;
	}
?>