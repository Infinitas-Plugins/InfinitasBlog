<?php
/**
 * Blog Post Model class file.
 *
 * This is the main model for Blog Posts. There are a number of
 * methods for getting the counts of all posts, active posts, pending
 * posts etc.  It extends {@see BlogAppModel} for some all round
 * functionality. look at {@see BlogAppModel::afterSave} for an example
 *
 * @copyright Copyright (c) 2009 Carl Sutton ( dogmatic69 )
 * 
 * @link http://infinitas-cms.org
 * @package Blog.Model
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @since 0.6a
 *
 * @author Carl Sutton <dogmatic69@infinitas-cms.org>
 */

class BlogPost extends BlogAppModel {

	public $lockable = true;

	public $contentable = true;

	public $actsAs = array(
		'Feed.Feedable',
		'Contents.Taggable'
	);

	public $hasMany = array(
		'ChildPost' => array(
			'className' => 'Blog.BlogPost',
			'foreignKey' => 'parent_id',
			'dependent' => true,
			'conditions' => '',
			'fields' => array(
				'ChildPost.id',
				// 'ChildPost.title',
				// 'ChildPost.slug',
			),
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		)
	);

	public $belongsTo = array(
		'ParentPost' => array(
			'className' => 'Blog.BlogPost',
			'foreignKey' => 'parent_id',
			'conditions' => '',
			'fields' => array(
				'ParentPost.id',
				// 'ParentPost.title',
				// 'ParentPost.slug',
			),
			'order' => ''
		)
	);

/**
 * Constructor
 *
 * @param type $id
 * @param type $table
 * @param type $ds
 *
 * @return void
 */
	public function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);

		$this->order = array(
			$this->alias . '.created' => 'desc',
		);

		$this->validate = array(
			'title' => array(
				'notEmpty' => array(
					'rule' => 'notEmpty',
					'message' => __d('blog', 'Please enter the title of your post')
				)
			),
			'body' => array(
				'notEmpty' => array(
					'rule' => 'notEmpty',
					'message' => __d('blog', 'Please enter your post')
				)
			),
			'category_id' => array(
				'comparison' => array(
					'rule' => array('comparison', '>', 0),
					'message' => __d('blog', 'Please select a category')
				)
			)
		);

		$this->virtualFields = array_merge(
			(array)$this->virtualFields,
			array(
				'created_year' => 'EXTRACT(YEAR FROM `' . $this->alias . '`.`created`)',
				'created_month' => 'EXTRACT(MONTH FROM `' . $this->alias . '`.`created`)',
				'year_month' => 'CONCAT_WS("_", EXTRACT(YEAR FROM `' . $this->alias . '`.`created`), EXTRACT(MONTH FROM `' . $this->alias . '`.`created`))'
			)
		);

		$this->findMethods['paginated'] = true;
		$this->findMethods['viewData'] = true;
		$this->findMethods['dates'] = true;
	}

/**
 * AfterFind callback
 *
 * @param array $results
 * @param boolean $primary
 *
 * @return array
 */
	public function afterFind($results, $primary = false) {
		switch($this->findQueryType) {
			case 'viewData':
				$results = $this->attachComments($results);
				if (!empty($results[0])) {
					$results = $results[0];
				}
				break;
		}

		return $results;
	}

/**
 * Get parent posts
 *
 * Get a list of possible parents for the post create page. Setting a
 * parent will make multi page posts
 *
 * @return array
 */
	public function getParentPosts() {
		return array_filter($this->find('list', array(
			'conditions' => array(
				$this->alias . '.parent_id' => null
			)
		)));
	}

/**
 * BeforeFind callback
 *
 * @param array $queryData
 *
 * @return array
 */
	public function beforeFind($queryData) {
		if ($this->findQueryType == 'count') {
			return $queryData;
		}

		$queryData['fields'] = array_merge(
			(array)$queryData['fields'],
			array(
				'created_year',
				'created_month',
			)
		);

		return $queryData;
	}

/**
 * Get view data
 *
 * General method for the view pages. Gets the required data and relations
 * and can be used for the admin preview also.
 *
 * @param array $conditions conditions for the find
 *
 * @return array
 */
	public function getViewData($conditions = array()) {
		if (!$conditions) {
			return false;
		}

		if (!empty($post['ParentPost']['id'])) {
			$post['ParentPost']['ChildPost'] = $this->find('all', array(
				'conditions' => array(
					$this->alias . '.parent_id' => $post['ParentPost']['id']
				),
				'fields' => array(
					$this->alias . '.id',
					$this->alias . '.title',
					$this->alias . '.slug',
				),
				'contain' => false
			));
		}

		return $post;
	}

/**
 * Gets the latest posts.
 *
 * returns a list of the latest addes posts
 *
 * @param integer $limit the number of posts to return
 * @param integer $active if the posts should be active or not
 *
 * @return array
 */
	public function getLatest($limit = 5, $active = 1) {
		$cacheName = cacheName('posts_latest', array($limit, $active));
		$posts = Cache::read($cacheName, 'blog');
		if ($posts !== false) {
			return $posts;
		}

		$posts = $this->find('all', array(
			'fields' => array(
				$this->alias . '.id'
			),
			'conditions' => array(
				$this->alias . '.active' => $active
			),
			'limit' => $limit,
			'order' => array(
				$this->alias . '.created' => 'desc'
			)
		));

		Cache::write($cacheName, $posts, 'blog');

		return $posts;
	}

/**
 * get a count of active vs inactive posts, used to show some stats around
 * the admin pages.
 *
 * @param  $model idk
 * @return array the counts
 */
	public function getCounts() {
		$counts = Cache::read('posts_count', 'blog');
		if ($counts !== false) {
			return $counts;
		}

		$counts['active'] = $this->find('count', array(
			'conditions' => array(
				$this->alias . '.active' => 1
			),
			'contain' => false
		));

		$counts['pending'] = $this->find('count', array(
			'conditions' => array(
				$this->alias . '.active' => 0
			),
			'contain' => false
		));

		Cache::write('posts_count', $counts, 'blog');

		return $counts;
	}

/**
 * Get the pending posts.
 *
 * if the count of pending is > the limit it will add "and more..."
 * to the end of the list
 *
 * @param integer $limit how many items to return
 *
 * @return array
 */
	public function getPending($limit = 10) {
		$cacheName = cacheName('posts_pending', $limit);
		$pending = Cache::read($cacheName, 'blog');
		if ($pending !== false) {
			return $pending;
		}

		$pending = $this->find('list', array(
			'conditions' => array(
				$this->alias . '.active' => 0
			),
			'order' => array(
				$this->alias . '.modified' => 'ASC'
			),
			'limit' => $limit
		));

		$count = $this->find('count', array(
			'conditions' => array(
				$this->alias . '.active' => 0
			)
		));

		if ($count > count($pending)) {
			$pending[] = __d('blog', 'And More...');
		}

		Cache::write($cacheName, $pending, 'blog');

		return $pending;
	}

/**
 * find posts with a certain tag.
 *
 * @param string $tag the tag to search for
 *
 * @return array
 */
	public function findPostsByTag($tag) {
		$cacheName = cacheName('posts_by_tag', $tag);
		$tags = Cache::read($cacheName, 'blog');
		if ($tags !== false) {
			return $tags;
		}

		$tags = $this->GlobalTag->find('all', array(
			'conditions' => array(
				'or' => array(
					'GlobalTag.id' => $tag,
					'GlobalTag.name' => $tag
				)
			),
			'fields' => array(
				'GlobalTag.id'
			),
			'contain' => array(
				$this->alias => array(
					'fields' => array(
						$this->alias . '.id'
					)
				)
			)
		));

		$tags = Set::extract(sprintf('/%s/%s', $this->alias, $this->primaryKey), $tags);
		Cache::write($cacheName, $pending, 'blog');

		return $tags;
	}

/**
 * pagination options
 *
 * Adds BETWEEN conditions for $year and $month to any array.
 * You can pass a custom Model and a custom created field, too.
 *
 * @param array $paginate the pagination array to be processed
 * @param array $options
 *
 * 	###	possible options:
 * 			- year (int) year of the format YYYY (defaults null)
 * 			- month (int) month of the year in the format 01 - 12 (defaults null)
 * 			- model (string) custom Model Alias to pass (defaults calling Model)
 * 			- created (string) the name of the field to use in the Between statement (defaults 'created')
 *
 * @return array
 */
	public function setPaginateDateOptions($options = array()) {
		$default = array(
			'year' => null,
			'month' => null,
			'model' => $this->alias,
			'created' => 'created'
		);
		// Extract Options
		extract(array_merge($default, $options));

		// If nothing is given, add nothing
		if ($year === null && $month === null) {
			return $options;
		}

		// SQL time templates for sprintf
		$yTmplBegin = "%s-01-01 00:00:00";
		$yTmplEnd = "%s-12-31 23:59:59";
		$ymTmplBegin = "%s-%02d-01 00:00:00";
		$ymTmplEnd = "%s-%02d-%02d 23:59:59";

		$begin = sprintf($yTmplBegin, $year);
		$end = sprintf($yTmplEnd, $year);
		if ($month !== null && $month <= 12) {
			// Get days for selected month
			$days = date('t', mktime(0, 0, 0, $month, 1, $year));
			$begin = sprintf($ymTmplBegin, $year, $month);
			$end = sprintf($ymTmplEnd, $year, $month, $days);
		}

		$options['conditions'] += array(
			$model . '.' . $created . ' BETWEEN ? AND ?' => array($begin, $end)
		);

		unset($options['conditions']['year'], $options['conditions']['month'], $options['year'],
				$options['month'], $options['created'], $options['model']);

		return $options;
	}

	protected function _findPaginated($state, $query, $results = array()) {
		if ($state === 'before') {
			$query = $this->setPaginateDateOptions($query);

			if (empty($query['fields'])) {
				$query['fields'] = array($this->alias . '.*');
			}

			/*array(
				'table' => 'blog_posts',
				'alias' => 'ChildPost',
				'type' => 'LEFT',
				'conditions' => array(
					'ChildPost.parent_id = BlogPost.id'
				)
			),
			array(
				'table' => 'blog_posts',
				'alias' => 'ChildPostGlobalContent',
				'type' => 'LEFT',
				'conditions' => array(
					'ChildPostGlobalContent.floreign_key = ChildPost.id'
				)
			),
			array(
				'table' => 'global_categories',
				'alias' => 'ChildPostGlobalCategory',
				'type' => 'LEFT',
				'conditions' => array(
					'ChildPostGlobalCategory.id = ChildPostGlobalContent.global_category_id'
				)
			)*/
			return $query;
		}

		return $results;
	}

/**
 * Get years and months of all posts.
 *
 * This is used to get all the dates that posts are available in. It can then
 * be used to generate links to archived posts etc.
 *
 * @param string $state before or after
 * @param array $query the details of the find being done
 * @param array $results the results from the find
 *
 * @return array an array or years and months
 */
	protected function _findDates($state, $query, $results = array()) {
		if ($state === 'before') {
			$conditions = array(
				'fields' => array(
					'created_year',
					'created_month',
					'year_month'
				),
				'conditions' => array(
					$this->alias . '.active' => 1
				),
				'group' => array(
					'year_month'
				),
				'order' => array(
					$this->alias . '.created' => 'desc'
				)
			);

			$query = array_merge_recursive($conditions, $query);
			foreach ($query as $k => &$v) {
				if (!is_array($v)) {
					continue;
				}

				$v = array_filter($v);
			}
			return $query;
		}

		$return = array();
		foreach (Set::extract('/' . $this->alias . '/year_month', $results) as $date) {
			$date = explode('_', $date);
			if (empty($return[$date[0]]) || !in_array($date[1], $return[$date[0]])) {
				$return[$date[0]][] = $date[1];
			}
		}

		return $return;
	}

/**
 * Get the data that is used in when viewing a post
 *
 * This gets all the required data to view a post. Including things like
 * comments and other relations.
 *
 * @param string $state before or after
 * @param array $query the details of the find being done
 * @param array $results the results from the find
 *
 * @return array of data from the db
 */
	protected function _findViewData($state, $query, $results = array()) {
		if ($state === 'before') {
			$query['fields'] = array_merge((array)$query['fields'], array(
				$this->alias . '.id',
				$this->alias . '.active',
				$this->alias . '.views',
				$this->alias . '.comment_count',
				$this->alias . '.rating',
				$this->alias . '.rating_count',
				$this->alias . '.created',
				$this->alias . '.modified'
			));

			$query['joins'][] = array(
				'table' => 'blog_posts',
				'alias' => 'ChildPost',
				'type' => 'LEFT',
				'conditions' => array(
					'ChildPost.parent_id = ' . $this->alias . '.id'
				)
			);

			$query['joins'][] = array(
				'table' => 'global_contents',
				'alias' => 'ChildPostGlobalContent',
				'type' => 'LEFT',
				'conditions' => array(
					'ChildPostGlobalContent.foreign_key = ChildPost.id'
				)
			);

			$query['joins'][] = array(
				'table' => 'global_categories',
				'alias' => 'ChildPostGlobalCategory',
				'type' => 'LEFT',
				'conditions' => array(
					'ChildPostGlobalCategory.id = ChildPostGlobalContent.global_category_id'
				)
			);

			$query['joins'][] = array(
				'table' => 'blog_posts',
				'alias' => 'ParentPost',
				'type' => 'LEFT',
				'conditions' => array(
					'ParentPost.id = ' . $this->alias . '.parent_id'
				)
			);

			$query['joins'][] = array(
				'table' => 'global_contents',
				'alias' => 'ParentPostGlobalContent',
				'type' => 'LEFT',
				'conditions' => array(
					'ParentPostGlobalContent.foreign_key = ChildPost.id'
				)
			);

			$query['joins'][] = array(
				'table' => 'global_categories',
				'alias' => 'ParentPostGlobalCategory',
				'type' => 'LEFT',
				'conditions' => array(
					'ParentPostGlobalCategory.id = ChildPostGlobalContent.global_category_id'
				)
			);
			return $query;
		}

		return $results;
	}
}