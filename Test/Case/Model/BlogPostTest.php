<?php
/* Post Test cases generated on: 2010-03-13 15:03:45 : 1268486985*/
App::uses('BlogPost', 'Blog.Model');

class BlogPostTest extends CakeTestCase {
	public $fixtures = array(
		'plugin.view_counter.view_counter_view',
		'plugin.management.ticket',
		'plugin.locks.global_lock',
		'plugin.contents.global_category',
		'plugin.contents.global_content',
		'plugin.contents.global_layout',
		'plugin.contents.global_tagged',
		'plugin.contents.global_tag',

		'plugin.blog.blog_post',

		'plugin.comments.infinitas_comment',
		'plugin.users.user',
		'plugin.users.group',
		'plugin.management.aco',
		'plugin.management.aro',
		'plugin.management.aros_aco',
		'plugin.installer.plugin',
	);

/**
 * @brief set up at the start
 */
	public function startTest() {
		$this->Post = ClassRegistry::init('Blog.BlogPost');
	}

/**
 * @brief break down at the end
 */
	public function endTest() {
		unset($this->Post);
		ClassRegistry::flush();
	}

/**
 * @brief test year / month pagination
 */
	public function testYearAndMonthPaginateOptions() {
		$paginate = array(
			'conditions' => array(
				'BlogPost.id' => array(1,2,3,4)
			),
			'year' => 2009
		);
		$expected = array(
			'conditions' => array(
				'BlogPost.id' => array(1,2,3,4),
				'BlogPost.created BETWEEN ? AND ?' => array('2009-01-01 00:00:00', '2009-12-31 23:59:59')
			)
		);
		$result = $this->Post->setPaginateDateOptions($paginate);
		$this->assertEquals($expected, $result);

		$expected = array(
			'conditions' => array(
				'BlogPost.id' => array(1,2,3,4),
				'BlogPost.created BETWEEN ? AND ?' => array('2009-11-01 00:00:00', '2009-11-30 23:59:59')
			)
		);
		$paginate['month'] = 11;
		$result = $this->Post->setPaginateDateOptions($paginate);
		$this->assertEquals($expected, $result);

		$expected = array(
			'conditions' => array(
				'BlogPost.id' => array(1,2,3,4),
				'BlogPost.created BETWEEN ? AND ?' => array('2010-05-01 00:00:00', '2010-05-31 23:59:59')
			)
		);

		$paginate['year'] = 2010;
		$paginate['month'] = 5;
		$result = $this->Post->setPaginateDateOptions($paginate);
		$this->assertEquals($expected, $result);

		$expected = array(
			'conditions' => array(
				'BlogPost.id' => array(1,2,3,4)				)
		);
		unset($paginate['month'], $paginate['year']);
		$result = $this->Post->setPaginateDateOptions($paginate);
		$this->assertEquals($expected, $result);

		$paginate = array(
			'conditions' => array(
				'GlobalCategory.id' => array(1,2,3,4)
			)
		);
		$expected = array(
			'conditions' => array(
				'GlobalCategory.id' => array(1,2,3,4),
				'GlobalCategory.xxxxx BETWEEN ? AND ?' => array('2010-01-01 00:00:00', '2010-12-31 23:59:59')
			)
		);

		$paginate['model'] = 'GlobalCategory';
		$paginate['created'] = 'xxxxx';
		$paginate['year'] = 2010;
		$result = $this->Post->setPaginateDateOptions($paginate);
		$this->assertEquals($expected, $result);

		// Test leap year
		$paginate = array(
			'conditions' => array(
				'BlogPost.id' => array(1,2,3,4)
			),
			'year' => 2008,
			'month' => 2
		);
		$expected = array(
			'conditions' => array(
				'BlogPost.id' => array(1,2,3,4),
				'BlogPost.created BETWEEN ? AND ?' => array('2008-02-01 00:00:00', '2008-02-29 23:59:59')				)
		);
		$result = $this->Post->setPaginateDateOptions($paginate);
		$this->assertEquals($expected, $result);
	}

/**
 * @brief test getting the parent posts
 */
	public function testGetParentPosts() {
		$expected = array(
			'blog-post-1' => 'blog-post-1',
			'blog-post-2' => 'blog-post-2'
		);
		$result = $this->Post->getParentPosts();
		$this->assertEquals($expected, $result);

		$this->Post->id = 'blog-post-2-1';
		$this->assertTrue((bool)$this->Post->saveField('parent_id', null));
		$expected = array(
			'blog-post-1' => 'blog-post-1',
			'blog-post-2' => 'blog-post-2',
			'blog-post-2-1' => 'blog-post-2-1'
		);
		$result = $this->Post->getParentPosts();
		$this->assertEquals($expected, $result);
	}

/**
 * @brief test afterFind
 */
	public function testAfterFind() {

	}

/**
 * @brief test getting view data
 */
	public function testGetViewData() {

	}

/**
 * @brief latest posts
 */
	public function testGetLatestPosts() {
		$expected = array(
			array(
				'BlogPost' => array(
					'id' => 'blog-post-2',
					'model' => null,
					'foreign_key' => null,
					'title' => null,
					'slug' => null,
					'introduction' => null,
					'body' => null,
					'image' => null,
					'dir' => null,
					'full_text_search' => null,
					'keyword_density' => null,
					'global_category_id' => null,
					'meta_keywords' => null,
					'meta_description' => null,
					'group_id' => null,
					'layout_id' => null,
					'author_id' => null,
					'author_alias' => null,
					'editor_id' => null,
					'editor_alias' => null,
					'canonical_url' => null,
					'canonical_redirect' => null,
					'created' => null,
					'modified' => null,
					'content_image_path_full' => '/contents/img/no-image.png',
					'content_image_path_jumbo' => '/contents/img/no-image.png',
					'content_image_path_large' => '/contents/img/no-image.png',
					'content_image_path_medium' => '/contents/img/no-image.png',
					'content_image_path_small' => '/contents/img/no-image.png',
					'content_image_path_thumb' => '/contents/img/no-image.png',
					'created_year' => '2010',
					'created_month' => '1',
				),
				'GlobalContent' => array(
					'id' => null,
					'model' => null,
					'foreign_key' => null,
					'title' => null,
					'slug' => null,
					'introduction' => null,
					'body' => null,
					'image' => null,
					'dir' => null,
					'full_text_search' => null,
					'keyword_density' => null,
					'global_category_id' => null,
					'meta_keywords' => null,
					'meta_description' => null,
					'group_id' => null,
					'layout_id' => null,
					'author_id' => null,
					'author_alias' => null,
					'editor_id' => null,
					'editor_alias' => null,
					'canonical_url' => null,
					'canonical_redirect' => null,
					'created' => null,
					'modified' => null,
				),
				'Layout' => array(
					'id' => null,
					'name' => null,
					'model' => null,
					'auto_load' => null,
					'css' => null,
					'html' => null,
					'php' => null,
					'content_count' => null,
					'created' => null,
					'modified' => null,
					'theme_id' => null,
					'layout' => null
				),
				'GlobalCategory' => array(
					'id' => null,
					'title' => null,
					'slug' => null,
					'meta_keywords' => null,
					'meta_description' => null,
					'active' => null,
					'group_id' => null,
					'item_count' => null,
					'parent_id' => null,
					'lft' => null,
					'rght' => null,
					'views' => null,
					'created' => null,
					'modified' => null,
					'hide' => null,
					'path_depth' => null
				),
				'GlobalCategoryContent' => array(
					'id' => null,
					'title' => null,
					'slug' => null,
					'meta_keywords' => null,
					'meta_description' => null,
				),
				'ContentGroup' => array(
					'id' => null,
					'name' => null,
				),
				'ContentEditor' => array(
					'id' => null,
					'username' => null,
				),
				'ContentAuthor' => array(
					'id' => null,
					'username' => null,
				),
				'Lock' => array(
					'id' => null,
					'class' => null,
					'foreign_key' => null,
					'user_id' => null,
					'created' => null,
				),
				'LockLocker' => array(
					'id' => null,
					'username' => null,
				),
				'GlobalTagged' => null,
			),
			array(
				'BlogPost' => array(
					'id' => 'blog-post-1',
					'model' => null,
					'foreign_key' => null,
					'title' => null,
					'slug' => null,
					'introduction' => null,
					'body' => null,
					'image' => null,
					'dir' => null,
					'full_text_search' => null,
					'keyword_density' => null,
					'global_category_id' => null,
					'meta_keywords' => null,
					'meta_description' => null,
					'group_id' => null,
					'layout_id' => null,
					'author_id' => null,
					'author_alias' => null,
					'editor_id' => null,
					'editor_alias' => null,
					'canonical_url' => null,
					'canonical_redirect' => null,
					'created' => null,
					'modified' => null,
					'content_image_path_full' => '/contents/img/no-image.png',
					'content_image_path_jumbo' => '/contents/img/no-image.png',
					'content_image_path_large' => '/contents/img/no-image.png',
					'content_image_path_medium' => '/contents/img/no-image.png',
					'content_image_path_small' => '/contents/img/no-image.png',
					'content_image_path_thumb' => '/contents/img/no-image.png',
					'created_year' => '2010',
					'created_month' => '1',
				),
				'GlobalContent' => array(
					'id' => null,
					'model' => null,
					'foreign_key' => null,
					'title' => null,
					'slug' => null,
					'introduction' => null,
					'body' => null,
					'image' => null,
					'dir' => null,
					'full_text_search' => null,
					'keyword_density' => null,
					'global_category_id' => null,
					'meta_keywords' => null,
					'meta_description' => null,
					'group_id' => null,
					'layout_id' => null,
					'author_id' => null,
					'author_alias' => null,
					'editor_id' => null,
					'editor_alias' => null,
					'canonical_url' => null,
					'canonical_redirect' => null,
					'created' => null,
					'modified' => null,
				),
				'Layout' => array(
					'id' => null,
					'name' => null,
					'model' => null,
					'auto_load' => null,
					'css' => null,
					'html' => null,
					'php' => null,
					'content_count' => null,
					'created' => null,
					'modified' => null,
					'theme_id' => null,
					'layout' => null
				),
				'GlobalCategory' => array(
					'id' => null,
					'title' => null,
					'slug' => null,
					'meta_keywords' => null,
					'meta_description' => null,
					'active' => null,
					'group_id' => null,
					'item_count' => null,
					'parent_id' => null,
					'lft' => null,
					'rght' => null,
					'views' => null,
					'created' => null,
					'modified' => null,
					'hide' => null,
					'path_depth' => null
				),
				'GlobalCategoryContent' => array(
					'id' => null,
					'title' => null,
					'slug' => null,
					'meta_keywords' => null,
					'meta_description' => null,
				),
				'ContentGroup' => array(
					'id' => null,
					'name' => null,
				),
				'ContentEditor' => array(
					'id' => null,
					'username' => null,
				),
				'ContentAuthor' => array(
					'id' => null,
					'username' => null,
				),
				'Lock' => array(
					'id' => null,
					'class' => null,
					'foreign_key' => null,
					'user_id' => null,
					'created' => null,
				),
				'LockLocker' => array(
					'id' => null,
					'username' => null,
				),
				'GlobalTagged' => null,
			),
		);

		$result = $this->Post->getLatest();
		$this->assertEquals($expected, $result);

		$expected = array(
			array(
				'BlogPost' => array(
					'id' => 'blog-post-2',
					'model' => null,
					'foreign_key' => null,
					'title' => null,
					'slug' => null,
					'introduction' => null,
					'body' => null,
					'image' => null,
					'dir' => null,
					'full_text_search' => null,
					'keyword_density' => null,
					'global_category_id' => null,
					'meta_keywords' => null,
					'meta_description' => null,
					'group_id' => null,
					'layout_id' => null,
					'author_id' => null,
					'author_alias' => null,
					'editor_id' => null,
					'editor_alias' => null,
					'canonical_url' => null,
					'canonical_redirect' => null,
					'created' => null,
					'modified' => null,
					'content_image_path_full' => '/contents/img/no-image.png',
					'content_image_path_jumbo' => '/contents/img/no-image.png',
					'content_image_path_large' => '/contents/img/no-image.png',
					'content_image_path_medium' => '/contents/img/no-image.png',
					'content_image_path_small' => '/contents/img/no-image.png',
					'content_image_path_thumb' => '/contents/img/no-image.png',
					'created_year' => '2010',
					'created_month' => '1',
				),
				'GlobalContent' => array(
					'id' => null,
					'model' => null,
					'foreign_key' => null,
					'title' => null,
					'slug' => null,
					'introduction' => null,
					'body' => null,
					'image' => null,
					'dir' => null,
					'full_text_search' => null,
					'keyword_density' => null,
					'global_category_id' => null,
					'meta_keywords' => null,
					'meta_description' => null,
					'group_id' => null,
					'layout_id' => null,
					'author_id' => null,
					'author_alias' => null,
					'editor_id' => null,
					'editor_alias' => null,
					'canonical_url' => null,
					'canonical_redirect' => null,
					'created' => null,
					'modified' => null,
				),
				'Layout' => array(
					'id' => null,
					'name' => null,
					'model' => null,
					'auto_load' => null,
					'css' => null,
					'html' => null,
					'php' => null,
					'content_count' => null,
					'created' => null,
					'modified' => null,
					'theme_id' => null,
					'layout' => null
				),
				'GlobalCategory' => array(
					'id' => null,
					'title' => null,
					'slug' => null,
					'meta_keywords' => null,
					'meta_description' => null,
					'active' => null,
					'group_id' => null,
					'item_count' => null,
					'parent_id' => null,
					'lft' => null,
					'rght' => null,
					'views' => null,
					'created' => null,
					'modified' => null,
					'hide' => null,
					'path_depth' => null
				),
				'GlobalCategoryContent' => array(
					'id' => null,
					'title' => null,
					'slug' => null,
					'meta_keywords' => null,
					'meta_description' => null,
				),
				'ContentGroup' => array(
					'id' => null,
					'name' => null,
				),
				'ContentEditor' => array(
					'id' => null,
					'username' => null,
				),
				'ContentAuthor' => array(
					'id' => null,
					'username' => null,
				),
				'Lock' => array(
					'id' => null,
					'class' => null,
					'foreign_key' => null,
					'user_id' => null,
					'created' => null,
				),
				'LockLocker' => array(
					'id' => null,
					'username' => null,
				),
				'GlobalTagged' => null,
			)
		);
		$result = $this->Post->getLatest(1);
		$this->assertEquals($expected, $result);

		$expected = array(
			array(
				'BlogPost' => array(
					'id' => 'blog-post-2-1',
					'model' => null,
					'foreign_key' => null,
					'title' => null,
					'slug' => null,
					'introduction' => null,
					'body' => null,
					'image' => null,
					'dir' => null,
					'full_text_search' => null,
					'keyword_density' => null,
					'global_category_id' => null,
					'meta_keywords' => null,
					'meta_description' => null,
					'group_id' => null,
					'layout_id' => null,
					'author_id' => null,
					'author_alias' => null,
					'editor_id' => null,
					'editor_alias' => null,
					'canonical_url' => null,
					'canonical_redirect' => null,
					'created' => null,
					'modified' => null,
					'content_image_path_full' => '/contents/img/no-image.png',
					'content_image_path_jumbo' => '/contents/img/no-image.png',
					'content_image_path_large' => '/contents/img/no-image.png',
					'content_image_path_medium' => '/contents/img/no-image.png',
					'content_image_path_small' => '/contents/img/no-image.png',
					'content_image_path_thumb' => '/contents/img/no-image.png',
					'created_year' => '2010',
					'created_month' => '1',
				),
				'GlobalContent' => array(
					'id' => null,
					'model' => null,
					'foreign_key' => null,
					'title' => null,
					'slug' => null,
					'introduction' => null,
					'body' => null,
					'image' => null,
					'dir' => null,
					'full_text_search' => null,
					'keyword_density' => null,
					'global_category_id' => null,
					'meta_keywords' => null,
					'meta_description' => null,
					'group_id' => null,
					'layout_id' => null,
					'author_id' => null,
					'author_alias' => null,
					'editor_id' => null,
					'editor_alias' => null,
					'canonical_url' => null,
					'canonical_redirect' => null,
					'created' => null,
					'modified' => null,
				),
				'Layout' => array(
					'id' => null,
					'name' => null,
					'model' => null,
					'auto_load' => null,
					'css' => null,
					'html' => null,
					'php' => null,
					'content_count' => null,
					'created' => null,
					'modified' => null,
					'theme_id' => null,
					'layout' => null,
				),
				'GlobalCategory' => array(
					'id' => null,
					'title' => null,
					'slug' => null,
					'meta_keywords' => null,
					'meta_description' => null,
					'active' => null,
					'group_id' => null,
					'item_count' => null,
					'parent_id' => null,
					'lft' => null,
					'rght' => null,
					'views' => null,
					'created' => null,
					'modified' => null,
					'hide' => null,
					'path_depth' => null,
				),
				'GlobalCategoryContent' => array(
					'id' => null,
					'title' => null,
					'slug' => null,
					'meta_keywords' => null,
					'meta_description' => null,
				),
				'ContentGroup' => array(
					'id' => null,
					'name' => null,
				),
				'ContentEditor' => array(
					'id' => null,
					'username' => null,
				),
				'ContentAuthor' => array(
					'id' => null,
					'username' => null,
				),
				'Lock' => array(
					'id' => null,
					'class' => null,
					'foreign_key' => null,
					'user_id' => null,
					'created' => null,
				),
				'LockLocker' => array(
					'id' => null,
					'username' => null,
				),
				'GlobalTagged' => null,
			),
		);
		$result = $this->Post->getLatest(1, 0);
		$this->assertEquals($expected, $result);
	}

/**
 * @brief get counts
 */
	public function testGetCounts() {
		$expected = array(
			'active' => 2,
			'pending' => 1
		);
		$result = $this->Post->getCounts();
		$this->assertEquals($expected, $result);

		$this->Post->id = 'blog-post-1';
		$this->Post->saveField('active', 0);

		$expected = array(
			'active' => 1,
			'pending' => 2
		);
		$result = $this->Post->getCounts();
		$this->assertEquals($expected, $result);
	}

/**
 * @brief get pending
 */
	public function testGetPendingPosts() {
		$expected = array(
			'blog-post-2-1' => 'blog-post-2-1'
		);
		$result = $this->Post->getPending();
		$this->assertEquals($expected, $result);

		$this->Post->id = 'blog-post-1';
		$this->Post->saveField('active', 0);

		$this->Post->id = 'blog-post-2';
		$this->Post->saveField('active', 0);

		$expected = array(
			'blog-post-2-1' => 'blog-post-2-1',
			'blog-post-1' => 'blog-post-1',
			'blog-post-2' => 'blog-post-2'
		);
		$result = $this->Post->getPending();
		$this->assertEquals($expected, $result);

		$expected = array(
			'blog-post-2-1' => 'blog-post-2-1',
			0 => 'And More...'
		);
		$result = $this->Post->getPending(1);
		$this->assertEquals($expected, $result);
	}

/**
 * @brief find by tags
 */
	public function testFindByTag() {

	}

/**
 * @brief test paginated custom find
 */
	public function testFindPaginated() {

	}

/**
 * @brief find post dates
 */
	public function testPostDates() {

	}

/**
 * @brief get view data
 */
	public function testFindViewData() {

	}
}