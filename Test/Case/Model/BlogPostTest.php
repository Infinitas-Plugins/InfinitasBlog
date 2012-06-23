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

		'plugin.blog.post',

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
			1 => 1
		);
		$result = $this->Post->getParentPosts();
		$this->assertEqual($expected, $result);

		$this->Post->id = 2;
		$this->assertTrue((bool)$this->Post->saveField('parent_id', null));
		$expected = array(
			1 => 1,
			2 => 2
		);
		$result = $this->Post->getParentPosts();
		$this->assertEqual($expected, $result);
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
					'id' => '2',
					'created_year' => '2010',
					'created_month' => '1',
				),
			),
			array (
				'BlogPost' => array(
					'id' => '1',
					'created_year' => '2010',
					'created_month' => '1',
				),
			),
		);
		$result = $this->Post->getLatest();
		$this->assertEquals($expected, $result);

		$expected = array(
			array (
				'BlogPost' => array(
					'id' => '2',
					'created_year' => '2010',
					'created_month' => '1',
				),
			),
		);
		$result = $this->Post->getLatest(1);
		$this->assertEquals($expected, $result);

		$expected = array();
		$result = $this->Post->getLatest(1, 0);
		$this->assertEquals($expected, $result);
	}

/**
 * @brief get counts
 */
	public function testGetCounts() {
		$expected = array(
			'active' => 2,
			'pending' => 0
		);
		$result = $this->Post->getCounts();
		$this->assertEquals($expected, $result);

		$this->Post->id = 1;
		$this->Post->saveField('active', 0);

		$expected = array(
			'active' => 1,
			'pending' => 1
		);
		$result = $this->Post->getCounts();
		$this->assertEquals($expected, $result);
	}

/**
 * @brief get pending
 */
	public function testGetPendingPosts() {
		$expected = array();
		$result = $this->Post->getPending();
		$this->assertEquals($expected, $result);

		$this->Post->id = 1;
		$this->Post->saveField('active', 0);

		$this->Post->id = 2;
		$this->Post->saveField('active', 0);

		$expected = array(
			1 => 1,
			2 => 2
		);
		$result = $this->Post->getPending();
		$this->assertEquals($expected, $result);

		$expected = array(
			1 => '1',
			2 => 'And More...'
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