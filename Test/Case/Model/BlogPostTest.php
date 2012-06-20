<?php
	/* Post Test cases generated on: 2010-03-13 15:03:45 : 1268486985*/
	App::uses('BlogPost', 'Blog.Model');

	class BlogPostTest extends CakeTestCase {
		public $fixtures = array(
			'plugin.contents.global_category',
			'plugin.blog.post',
			//'plugin.contents.posts_tag',
			'plugin.contents.global_tag',

			'plugin.users.user',
			'plugin.users.group',
			'core.aco',
			'core.aro',
			'core.aros_aco',
		);

		public function startTest() {
			$this->Post =& ClassRegistry::init('Blog.BlogPost');
		}

		public function endTest() {
			unset($this->Post);
			ClassRegistry::flush();
		}

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
			$this->assertEqual($result, $expected);

			$expected = array(
				'conditions' => array(
					'BlogPost.id' => array(1,2,3,4),
					'BlogPost.created BETWEEN ? AND ?' => array('2009-11-01 00:00:00', '2009-11-30 23:59:59')
				)
			);
			$paginate['month'] = 11;
			$result = $this->Post->setPaginateDateOptions($paginate);
			$this->assertEqual($result, $expected);

			$expected = array(
				'conditions' => array(
					'BlogPost.id' => array(1,2,3,4),
					'BlogPost.created BETWEEN ? AND ?' => array('2010-05-01 00:00:00', '2010-05-31 23:59:59')
				)
			);

			$paginate['year'] = 2010;
			$paginate['month'] = 5;
			$result = $this->Post->setPaginateDateOptions($paginate);
			$this->assertEqual($result, $expected);

			$expected = array(
				'conditions' => array(
					'BlogPost.id' => array(1,2,3,4)				)
			);
			unset($paginate['month'], $paginate['year']);
			$result = $this->Post->setPaginateDateOptions($paginate);
			$this->assertEqual($result, $expected);

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
			$this->assertEqual($result, $expected);

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
			$this->assertEqual($result, $expected);
		}
	}