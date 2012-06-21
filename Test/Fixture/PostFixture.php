<?php
/* BlogPost Fixture generated on: 2010-03-13 15:03:30 : 1268487090 */
class PostFixture extends CakeTestFixture {
	var $name = 'Post';

	var $table = 'blog_posts';

	var $fields = array(
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

	var $records = array(
		array(
			'id' => 1,
			'comment_count' => 0,
			'active' => 1,
			'views' => 10,
			'rating' => 3,
			'rating_count' => 1,
			'category_id' => 1,
			'parent_id' => null,
			'ordering' => 3,
			'created' => '2010-01-01 00:00:00',
			'modified' => '2010-01-01 00:00:00',
		),
		array(
			'id' => 2,
			'comment_count' => 0,
			'active' => 1,
			'views' => 10,
			'rating' => 3,
			'rating_count' => 1,
			'category_id' => 1,
			'parent_id' => 1,
			'ordering' => 3,
			'created' => '2010-01-02 00:00:00',
			'modified' => '2010-01-02 00:00:00',
		),
	);
}
?>