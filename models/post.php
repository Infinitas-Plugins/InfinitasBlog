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
	class Post extends BlogAppModel {
		public $name = 'Post';

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
			'Tags.Taggable'
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
						'message' => __('Please enter the title of your post', true)
					)
				),
				'body' => array(
					'notEmpty' => array(
						'rule' => 'notEmpty',
						'message' => __('Please enter your post', true)
					)
				),
				'category_id' => array(
					'comparison' => array(
						'rule' => array('comparison', '>', 0),
						'message' => __('Please select a category', true)
					)
				)
			);
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

			$post = $this->find(
				'first',
				array(
					'fields' => array(
						'Post.id',
						'Post.active',
						'Post.views',
						'Post.comment_count',
						'Post.rating',
						'Post.rating_count',
						'Post.created',
						'Post.modified'
					),
					'conditions' => $conditions,
					'contain' => array(
						'Category',
						'ChildPost',
						'ParentPost',
						'Tag'
					)
				)
			);

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
		 * Get years and months of all posts.
		 *
		 * The years are cached cos they wont change much so it saves a
		 * little bit of database calls. only gets active posts so if a month
		 * has no active posts it will not get them.
		 *
		 * @return array $dates an array or years and months
		 */
		public function getDates() {
			$dates = Cache::read('posts_dates');
			if ($dates !== false) {
				//return $dates;
			}

			$dates = $this->find(
				'all',
				array(
					'fields' => array(
						'created_year',
						'created_month',
						'year_month'
					),
					'group' => array(
						'year_month'
					),
					'order' => array(
						'created' => 'desc'
					)
				)
			);

			if(empty($dates)){
				return array();
			}

			$dates = Set::extract('/Post/year_month', $dates);

			$return = array();
			foreach($dates as $date) {
				$date = explode('_', $date);
				$return[$date[0]][] = $date[1];
			}
			
			Cache::write('posts_dates', $return, 'blog');

			return $return;
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
				$pending[] = __('And More...', true);
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

			$tags = $this->Tag->find(
				'all',
				array(
					'conditions' => array(
						'or' => array(
							'Tag.id' => $tag,
							'Tag.name' => $tag
						)
					),
					'fields' => array(
						'Tag.id'
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
				'model' => null,
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

			if ($model === null) {
				$model = $this->alias;
			}

			if ($year === null) {
				$year = date('Y');
			}

			if ($month !== null) {
				// Get days for selected month
				$days = cal_days_in_month(CAL_GREGORIAN, intval($month), intval($year));
				$begin = sprintf($ymTmplBegin, $year, $month);
				$end = sprintf($ymTmplEnd, $year, $month, $days);
			}

			else {
				$begin = sprintf($yTmplBegin, $year);
				$end = sprintf($yTmplEnd, $year);
			}

			$paginate['conditions'] += array(
				$model . '.' . $created.' BETWEEN ? AND ?' => array($begin,$end)
			);

			return $paginate;
		}

		/**
		 * Get count of tags.
		 *
		 * Used for things like generating the tag cloud.
		 */
		public function getTags($limit = 50) {
			$cacheName = cacheName('post_tags', $limit);
			$tags = Cache::read($cacheName, 'shop');
			if($tags !== false){
				return $tags;
			}

			$tags = $this->Tagged->find('cloud', array('limit' => $limit));

			Cache::write($cacheName, $tags, 'blog');
			return $tags;
		}
	}