<?php 
/* SVN FILE: $Id$ */
/* Blog schema generated on: 2011-07-20 19:07:42 : 1311187482*/
class BlogSchema extends CakeSchema {
	var $name = 'Blog';

	function before($event = array()) {
		return true;
	}

	function after($event = array()) {
	}

	var $posts = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'comment_count' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'active' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'key' => 'index'),
		'views' => array('type' => 'integer', 'null' => false, 'default' => '0', 'key' => 'index'),
		'rating' => array('type' => 'float', 'null' => false, 'default' => '0'),
		'rating_count' => array('type' => 'integer', 'null' => false, 'default' => '0'),
		'parent_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'key' => 'index'),
		'ordering' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'category_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'index'),
		'tags' => array('type' => 'string', 'null' => false, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'category_id' => array('column' => 'category_id', 'unique' => 0), 'active' => array('column' => 'active', 'unique' => 0), 'most_views' => array('column' => array('views', 'id'), 'unique' => 0), 'parent' => array('column' => 'parent_id', 'unique' => 0)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);
}
?>