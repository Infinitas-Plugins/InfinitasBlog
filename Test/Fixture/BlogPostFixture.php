<?php
/**
 * @brief fixture file for BlogPost tests.
 *
 * @package Blog.Fixture
 * @since 0.9b1
 */
class BlogPostFixture extends CakeTestFixture {
	public $name = 'BlogPost';

	public $table = 'blog_posts';

	public $fields = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'key' => 'primary'),
		'comment_count' => array('type' => 'integer', 'null' => false, 'default' => null),
		'active' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'key' => 'index'),
		'views' => array('type' => 'integer', 'null' => false, 'default' => '0', 'key' => 'index'),
		'rating' => array('type' => 'float', 'null' => false, 'default' => '0'),
		'rating_count' => array('type' => 'integer', 'null' => false, 'default' => '0'),
		'parent_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36, 'key' => 'index'),
		'ordering' => array('type' => 'integer', 'null' => false, 'default' => null),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'active' => array('column' => 'active', 'unique' => 0),
			'most_views' => array('column' => array('views', 'id'), 'unique' => 0),
			'parent' => array('column' => 'parent_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $records = array(
		array(
			'id' => 'blog-post-1',
			'comment_count' => 0,
			'active' => 1,
			'views' => 10,
			'rating' => 3,
			'rating_count' => 1,
			'parent_id' => null,
			'ordering' => 1,
			'created' => '2010-01-01 00:00:00',
			'modified' => '2010-01-01 00:00:00',
		),
		array(
			'id' => 'blog-post-2',
			'comment_count' => 0,
			'active' => 1,
			'views' => 10,
			'rating' => 3,
			'rating_count' => 1,
			'parent_id' => null,
			'ordering' => 2,
			'created' => '2010-01-02 00:00:00',
			'modified' => '2010-01-02 00:00:00',
		),
		array(
			'id' => 'blog-post-2-1',
			'comment_count' => 0,
			'active' => 0,
			'views' => 10,
			'rating' => 3,
			'rating_count' => 1,
			'parent_id' => 'blog-post-2',
			'ordering' => 1,
			'created' => '2010-01-02 00:00:00',
			'modified' => '2010-01-02 00:00:00',
		),
	);
}