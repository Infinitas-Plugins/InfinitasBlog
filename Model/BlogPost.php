<?php
	/**
	 * Blog Post Model class file.
	 *
	 * This is the main model for Blog Posts. There are a number of
	 * methods for getting the counts of all posts, active posts, pending
	 * posts etc.  It extends {@see BlogAppModel} for some all round
	 * functionality. look at {@see BlogAppModel::afterSave} for an example
	 *
	 * Copyright (c) 2009 Carl Sutton ( dogmatic69 )
	 *
	 * Licensed under The MIT License
	 * Redistributions of files must retain the above copyright notice.
	 *
	 * @filesource
	 * @copyright Copyright (c) 2009 Carl Sutton ( dogmatic69 )
	 * @link http://infinitas-cms.org
	 * @package blog
	 * @subpackage blog.models.post
	 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
	 */
	class BlogPost extends BlogAppModel {
		public $lockable = true;

		public $contentable = true;

		/**
		 * always sort posts so newest is at the top
		 */
		public $order = array(
			'Post.created' => 'desc',
		);

		public $actsAs = array(
			'Feed.Feedable',
			'Contents.Taggable'
		);

		public $hasMany = array(
			'ChildPost' => array(
				'className' => 'Blog.Post',
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
				'className' => 'Blog.Post',
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

		public $virtualFields = array(
			'created_year' => 'EXTRACT(YEAR FROM `Post`.`created`)',
			'created_month' => 'EXTRACT(MONTH FROM `Post`.`created`)',
			'year_month' => 'CONCAT_WS("_", EXTRACT(YEAR FROM `Post`.`created`), EXTRACT(MONTH FROM `Post`.`created`))',
		);

		/**
		 * Get a list of possible parents for the post create page. Setting a
		 * parent will make multi page posts
		 *
		 * @return array
		 */
		public function getParentPosts(){
			return $this->find(
				'list',
				array(
					'conditions' => array(
						'Post.parent_id IS NULL'
					)
				)
			);
		}
		
		public function __construct($id = false, $table = null, $ds = null) {
			parent::__construct($id, $table, $ds);

			$this->validate = array(
				'title' => array(
					'notEmpty' => array(
						'rule' => 'notEmpty',
						'message' => __('Please enter the title of your post')
					)
				),
				'body' => array(
					'notEmpty' => array(
						'rule' => 'notEmpty',
						'message' => __('Please enter your post')
					)
				),
				'category_id' => array(
					'comparison' => array(
						'rule' => array('comparison', '>', 0),
						'message' => __('Please select a category')
					)
				)
			);

			$this->findMethods['paginated'] = true;
			$this->findMethods['viewData'] = true;
			$this->findMethods['dates'] = true;
		}

		public function afterFind($results, $primary = false) {
			switch($this->findQueryType) {
				case 'viewData':
					$results = $this->attachComments($results);
					if(!empty($results[0])) {
						$results = $results[0];
					}
					break;
			}
			
			return $results;
		}

		/**
		 * General method for the view pages. Gets the required data and relations
		 * and can be used for the admin preview also.
		 *
		 * @param array $conditions conditions for the find
		 * @return array the data that was found
		 */
		public function getViewData($conditions = array()){
			if(!$conditions){
				return false;
			}

			if (!empty($post['ParentPost']['id'])) {
				$post['ParentPost']['ChildPost'] = $this->find(
					'all',
					array(
						'conditions' => array(
							'Post.parent_id' => $post['ParentPost']['id']
						),
						'fields' => array(
							'Post.id',
							'Post.title',
							'Post.slug',
						),
						'contain' => false
					)
				);
			}

			return $post;
		}

		/**
		 * Gets the latest posts.
		 *
		 * returns a list of the latest addes posts
		 *
		 * @param int $limit the number of posts to return
		 * @param int $active if the posts should be active or not
		 * @return array $dates an array or years and months
		 */
		public function getLatest($limit = 5, $active = 1) {
			$cacheName = cacheName('posts_latest', array($limit, $active));
			$posts = Cache::read($cacheName, 'blog');
			if($posts !== false){
				return $posts;
			}

			$posts = $this->find(
				'all',
				array(
					'fields' => array(
						$this->alias . '.id'
					),
					'conditions' => array(
						'Post.active' => $active
					),
					'limit' => $limit,
					'order' => array(
						'Post.created' => 'DESC'
					)
				)
			);

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
		public function getCounts($model = null) {
			$cacheName = cacheName('posts_count', $model);
			$counts = Cache::read($cacheName, 'blog');
			if($counts !== false){
				return $counts;
			}

			$counts['active'] = $this->find(
				'count',
				array(
					'conditions' => array(
						'Post.active' => 1
					),
					'contain' => false
				)
			);
			
			$counts['pending'] = $this->find(
				'count',
				array(
					'conditions' => array(
						'Post.active' => 0
					),
					'contain' => false
				)
			);

			Cache::write($cacheName, $counts, 'blog');

			return $counts;
		}

		/**
		 * Get the pending posts.
		 *
		 * if the count of pending is > the limit it will add "and more..."
		 * to the end of the list
		 *
		 * @param integer $limit how many items to return
		 * @return array the list of pending posts
		 */
		public function getPending($limit = 10) {
			$cacheName = cacheName('posts_pending', $limit);
			$pending = Cache::read($cacheName, 'blog');
			if($pending !== false){
				return $pending;
			}

			$pending = $this->find(
				'list',
				array(
					'conditions' => array(
						'Post.active' => 0
					),
					'order' => array(
						'Post.modified' => 'ASC'
					),
					'limit' => $limit
				)
			);

			$count = $this->find(
				'count',
				array(
					'conditions' => array(
						'Post.active' => 0
					)
				)
			);

			if ($count > count($pending)) {
				$pending[] = __('And More...');
			}

			Cache::write($cacheName, $pending, 'blog');

			return $pending;
		}

		/**
		 * find posts with a certain tag.
		 *
		 * @param string $tag the tag to search for
		 * @return array the ids of the posts that were found
		 */
		public function findPostsByTag($tag) {
			$cacheName = cacheName('posts_by_tag', $tag);
			$tags = Cache::read($cacheName, 'blog');
			if($tags !== false){
				return $tags;
			}

			$tags = $this->GlobalTag->find(
				'all',
				array(
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
						'Post' => array(
							'fields' => array(
								'Post.id'
							)
						)
					)
				)
			);

			$tags = Set::extract('/Post/id', $tags);
			Cache::write($cacheName, $pending, 'blog');

			return $tags;
		}
		
		/**
		 * Adds BETWEEN conditions for $year and $month to any array.
		 * You can pass a custom Model and a custom created field, too.
		 *
		 * @param array $paginate the pagination array to be processed
		 * @param array $options
		 * 	###	possible options:
		 * 			- year (int) year of the format YYYY (defaults null)
		 * 			- month (int) month of the year in the format 01 - 12 (defaults null)
		 * 			- model (string) custom Model Alias to pass (defaults calling Model)
		 * 			- created (string) the name of the field to use in the Between statement (defaults 'created')
		 * @todo take just reference parameter?
		 */
		public function setPaginateDateOptions($paginate, $options = array()) {
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
				return $paginate;
			}

			// SQL time templates for sprintf
			$yTmplBegin = "%s-01-01 00:00:00";
			$yTmplEnd = "%s-12-31 23:59:59";
			$ymTmplBegin = "%s-%02d-01 00:00:00";
			$ymTmplEnd = "%s-%02d-%02d 23:59:59";

			$begin = sprintf($yTmplBegin, $year);
			$end = sprintf($yTmplEnd, $year);
			if ($month !== null) {
				// Get days for selected month
				$days = cal_days_in_month(CAL_GREGORIAN, intval($month), intval($year));
				$begin = sprintf($ymTmplBegin, $year, $month);
				$end = sprintf($ymTmplEnd, $year, $month, $days);
			}

			$paginate['conditions'] += array(
				$model . '.' . $created.' BETWEEN ? AND ?' => array($begin,$end)
			);

			unset($paginate['year'], $paginate['month'], $paginate['created'], $paginate['model']);

			return $paginate;
		}

		protected function findPaginated($state, $query, $results = array()) {
			if ($state === 'before') {
				$query = $this->setPaginateDateOptions($query);

				if(empty($query['fields'])) {
					$query['fields'] = array($this->alias . '.*');
				}

				/*array(
					'table' => 'blog_posts',
					'alias' => 'ChildPost',
					'type' => 'LEFT',
					'conditions' => array(
						'ChildPost.parent_id = Post.id'
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

			if (!empty($query['operation'])) {
				return $this->findPaginatecount($state, $query, $results);
			}

			return $results;
		}

		/**
		 * @brief Get years and months of all posts.
		 *
		 * This is used to get all the dates that posts are available in. It can then 
		 * be used to generate links to archived posts etc.
		 *
		 * @access protected
		 *
		 * @param string $state before or after
		 * @param array $query the details of the find being done
		 * @param array $results the results from the find
		 *
		 * @return array an array or years and months
		 */
		protected function findDates($state, $query, $results = array()) {
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

				return array_merge($conditions, $query);
			}

			if (!empty($query['operation'])) {
				return $this->findPaginatecount($state, $query, $results);
			}

			$return = array();
			foreach(Set::extract('/' . $this->alias . '/year_month', $results) as $date) {
				$date = explode('_', $date);
				if(empty($return[$date[0]]) || !in_array($date[1], $return[$date[0]])) {
					$return[$date[0]][] = $date[1];
				}
			}

			return $return;
		}

		/**
		 * @brief Get the data that is used in when viewing a post
		 *
		 * This gets all the required data to view a post. Including things like
		 * comments and other relations.
		 *
		 * @access protected
		 *
		 * @param string $state before or after
		 * @param array $query the details of the find being done
		 * @param array $results the results from the find
		 *
		 * @return array of data from the db
		 */
		protected function findViewData($state, $query, $results = array()) {
			if ($state === 'before') {
				$query['fields'] = array_merge(
					(array)$query['fields'],
					array(
						'Post.id',
						'Post.active',
						'Post.views',
						'Post.comment_count',
						'Post.rating',
						'Post.rating_count',
						'Post.created',
						'Post.modified'
					)
				);

				$query['joins'][] = array(
					'table' => 'blog_posts',
					'alias' => 'ChildPost',
					'type' => 'LEFT',
					'conditions' => array(
						'ChildPost.parent_id = Post.id'
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
						'ParentPost.id = Post.parent_id'
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

			if (!empty($query['operation'])) {
				return $this->findPaginatecount($state, $query, $results);
			}

			return $results;
		}
	}