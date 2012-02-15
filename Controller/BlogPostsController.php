<?php
	/**
	 * Blog Posts Controller class file.
	 *
	 * This is the main controller for all the blog posts.  It extends
	 * {@see BlogAppController} for some functionality.
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
	 * @subpackage blog.controllers.posts
	 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
	 * @since 0.5a
	 */

	class BlogPostsController extends BlogAppController {
		/**
		 * Index for users
		 *
		 * @param string $tag used to find posts with a tag
		 * @param string $year used to find posts in a cetain year
		 * @param string $month used to find posts in a year and month needs year
		 * @return
		 */
		public function index() {
			$this->Session->delete('Pagination.Post');
			$titleForLayout = $year = $month = $slug = $tagData = null;

			$limit = 6;


			$url = array_merge(array('action' => 'index'), $this->params['named']);

			if(isset($this->params['year'])){
				$year = $this->params['year'];
				$titleForLayout = sprintf(__d('blog', 'Posts for the year %s'), $year);
				$url['year'] = $year;
				
				if(isset($this->params['pass'][0])){
					$month = substr((int)$this->params['pass'][0], 0, 2);
					$titleForLayout = sprintf(__d('blog', 'Posts in %s, %s'), __(date('F', mktime(0, 0, 0, $month))), $year);
					$url[] = $month;
				}
			}
			
			else if(isset($this->params['tag'])){
				$tag = $this->params['tag'];
				if(empty($titleForLayout)){
					$titleForLayout = __d('blog', 'Posts');
				}
				
				$titleForLayout = sprintf(__d('blog', '%s related to %s'), $titleForLayout, $tag);
				$tagData = $this->BlogPost->GlobalTag->getViewData($tag);
				$limit = 50;

				$url['tag'] = $tag;
			}
			
			$post_ids = array();
			if (!empty($tag)) {
				$tag_id = ClassRegistry::init('Contents.GlobalTag')->find(
					'list',
					array(
						'fields' => array(
							'GlobalTag.id', 'GlobalTag.id'
						),
						'conditions' => array(
							'GlobalTag.name' => $tag
						)
					)
				);

				$post_ids = $this->BlogPost->GlobalTagged->find(
					'list',
					array(
						'fields' => array(
							'GlobalTagged.foreign_key', 'GlobalTagged.foreign_key'
						),
						'conditions' => array(
							'GlobalTagged.tag_id' => $tag_id
						)
					)
				);
			}

			$conditions = array(
				'BlogPost.active' => 1,
				'BlogPost.parent_id IS NULL',
				'GlobalCategory.active' => 1
			);

			if(!empty($post_ids)) {
				$conditions['GlobalContent.id'] = $post_ids;
			}

			$this->paginate = array(
				'paginated',
				'fields' => array(
					'BlogPost.id',
					'BlogPost.comment_count',
					'BlogPost.views',
					'BlogPost.created',
					'BlogPost.parent_id',
					'BlogPost.ordering',
				),
				'conditions' => $conditions,
				'limit' => $limit,
				'year' => $year,
				'month' => $month
			);

			$this->set('posts', $this->paginate('Post'));
			$this->set('seoContentIndex', Configure::read('Blog.robots.index.index'));
			$this->set('seoContentFollow', Configure::read('Blog.robots.index.follow'));
			$this->set('seoCanonicalUrl', $url);
			$this->set('tagData', $tagData);
			$this->set('title_for_layout', $titleForLayout);
		}

		/**
		 * User view
		 *
		 * @param string $slug the slug for the record
		 * @return na
		 */
		public function view() {
			if (!isset($this->params['slug'])) {
				$this->notice('invalid');
			}

			$post = $this->BlogPost->find(
				'viewData',
				array(
					'conditions' => array(
						'GlobalContent.slug' => $this->params['slug'],
						'BlogPost.active' => 1
					)
				)
			);

			/**
			 * make sure there is something found
			 */
			if (empty($post)) {
				$this->notice('invalid');
			}
			
			$this->set('post', $post);

			$canonicalUrl = $this->Event->trigger('blog.slugUrl', $post);
			$this->set('seoCanonicalUrl', $canonicalUrl['slugUrl']['blog']);
			
			$this->set('seoContentIndex', Configure::read('Blog.robots.view.index'));
			$this->set('seoContentFollow', Configure::read('Blog.robots.view.follow'));
			$this->set('title_for_layout', $post['Post']['title']);
		}

		/**
		 * Admin Section.
		 *
		 * All the admin methods.
		 */
		/**
		 * Admin dashboard
		 *
		 * @return na
		 */
		public function admin_dashboard() {
			$feed = $this->BlogPost->find(
				'feed',
				array(
					'setup' => array(
						'plugin' => 'Blog',
						'controller' => 'posts',
						'action' => 'view',
					),
					'fields' => array(
						'BlogPost.id',
						'BlogPost.title',
						'BlogPost.intro',
						'BlogPost.created'
					),
					'feed' => array(
						'Core.Comment' => array(
							'setup' => array(
								'plugin' => 'Comments',
								'controller' => 'infinitas_comments',
								'action' => 'view',
							),
							'fields' => array(
								'InfinitasComment.id',
								'InfinitasComment.name',
								'InfinitasComment.comment',
								'InfinitasComment.created'
							)
						)
					),
					'order' => array(
						'created' => 'DESC'
					)
				)
			);

			$this->set('blogFeeds', $feed);

			$this->set('dashboardPostCount', $this->BlogPost->getCounts());
			$this->set('dashboardPostLatest', $this->BlogPost->getLatest());
			$this->set('dashboardCommentsCount', $this->BlogPost->Comment->getCounts('Blog.BlogPost'));
		}

		/**
		 * Admin index.
		 *
		 * Uses the {@see FilterComponent} component to filter results.
		 *
		 * @return na
		 */
		public function admin_index() {
			$posts = $this->paginate(null, $this->Filter->filter);

			$filterOptions = $this->Filter->filterOptions;
			$filterOptions['fields'] = array(
				'title',
				'body',
				'category_id' => $this->BlogPost->GlobalContent->find('categoryList'),
				'active' => Configure::read('CORE.active_options')
			);

			$this->set(compact('posts', 'filterOptions'));
		}

		/**
		 * Admin add.
		 *
		 * This does some trickery for creating tags from the textarea comma
		 * delimited. also makes sure there are no duplicates created.
		 *
		 * @return void
		 */
		public function admin_add() {
			parent::admin_add();

			$parents = $this->BlogPost->getParentPosts();
			$this->set(compact('tags', 'parents'));
		}

		public function admin_edit($id = null) {
			parent::admin_edit($id);

			$parents = $this->BlogPost->getParentPosts();
			$this->set(compact('parents'));
		}

		public function admin_view($slug = null) {
			if (!$slug) {
				$this->notice('invalid');
			}

			$post = ((int)$slug > 0)
			? $this->BlogPost->read(null, $slug)
			: $this->BlogPost->find(
				'first',
				array(
					'conditions' => array(
						'BlogPost.slug' => $slug
					)
				)
			);

			$this->set(compact('post'));
			$this->render('view');
		}
	}