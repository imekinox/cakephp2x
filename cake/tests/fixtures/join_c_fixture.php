<?php
/**
 * Short description for file.
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
 * @subpackage    cake.tests.fixtures
 * @since         CakePHP(tm) v 1.2.0.6317
 * @license       http://www.opensource.org/licenses/opengroup.php The Open Group Test Suite License
 */

/**
 * Short description for class.
 *
 * @package       cake
 * @subpackage    cake.tests.fixtures
 */
class JoinCFixture extends CakeTestFixture {

/**
 * name property
 *
 * @var string 'JoinC'
 * @access public
 */
	var $name = 'JoinC';

/**
 * fields property
 *
 * @var array
 * @access public
 */
	var $fields = array(
		'id' => array('type' => 'integer', 'key' => 'primary'),
		'name' => array('type' => 'string', 'default' => ''),
		'created' => array('type' => 'datetime', 'null' => true),
		'updated' => array('type' => 'datetime', 'null' => true)
	);

/**
 * records property
 *
 * @var array
 * @access public
 */
	var $records = array(
		array('name' => 'Join C 1', 'created' => '2008-01-03 10:56:11', 'updated' => '2008-01-03 10:56:11'),
		array('name' => 'Join C 2', 'created' => '2008-01-03 10:56:12', 'updated' => '2008-01-03 10:56:12'),
		array('name' => 'Join C 3', 'created' => '2008-01-03 10:56:13', 'updated' => '2008-01-03 10:56:13')
	);
}
?>