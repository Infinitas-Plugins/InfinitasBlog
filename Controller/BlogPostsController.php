<?php
/**
 * Blog Posts Controller class file.
 *
 * This is the main controller for all the blog posts.  It extends
 * {@see BlogAppController} for some functionality.
 *
 * Copyright (c) 2009 Carl Sutton ( dogmatic69 )
 *
 * @copyright Copyright (c) 2009 Carl Sutton ( dogmatic69 )
 * @link http://infinitas-cms.org
 * @package Blog.Controller
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @since 0.5a
 *
 * @author Carl Sutton <dogmatic69@gmail.com>
 */

App::uses('BlogAppController', 'Blog.Controller');

class BlogPostsController extends BlogAppController {

/**
 * Index for users
 *
 * @param string $tag used to find posts with a tag
 * @param string $year used to find posts in a cetain year
 * @param string $month used to find posts in a year and month needs year
 *
 * @return void
 */
	public function index() {
		$this->Session->delete('Pagination.Post');
		$titleForLayout = $year = $month = $slug = $tagData = null;
		$limit = 6;
		$url = array_merge(array('action' => 'index'), $this->request->params['named']);

		$conditions = array(
			$this->modelClass . '.active' => 1,
			$this->modelClass . '.parent_id IS NULL',
			'or' => array(
				'GlobalCategory.active' => 1,
				'GlobalCategory.id IS NULL'
			)
		);

		if (!empty($this->request->params['year'])) {
			$year = $this->request->params['year'];
			$titleForLayout = sprintf(__d('blog', 'Posts for the year %s'), $year);
			$url['year'] = $year;

			$month = !empty($this->request->params['pass'][1]) ? $this->request->params['pass'][1] : null;
			if (!empty($this->request->params['month'])) {
				$month = $this->request->params['month'];
			}
			if ((int)$month > 0 && (int)$month < 13) {
				$titleForLayout = sprintf(__d('blog', 'Posts in %s, %s'), __(date('F', mktime(0, 0, 0, $month))), $year);
				$url[] = $month;
			}
		} else if (!empty($this->request->params['tag'])) {
			$tag = $this->request->params['tag'];
			if (empty($titleForLayout)) {
				$titleForLayout = __d('blog', 'Posts');
			}

			$titleForLayout = sprintf(__d('blog', '%s related to %s'), $titleForLayout, $tag);
			$tagData = $this->{$this->modelClass}->GlobalTag->getViewData($tag);
			$limit = 50;

			$url['tag'] = $tag;

			if (!empty($tagData['GlobalTag']['meta_keywords'])) {
				$this->set('seoMetaKeywords', $tagData['GlobalTag']['meta_keywords']);
			}

			if (!empty($tagData['GlobalTag']['meta_description'])) {
				$this->set('seoMetaDescription', $tagData['GlobalTag']['meta_description']);
			}
		}

		if (!empty($tag)) {
			$postIds = ClassRegistry::init('Contents.GlobalTag')->find('list', array(
				'fields' => array(
					'GlobalContent.foreign_key', 'GlobalContent.foreign_key'
				),
				'conditions' => array(
					'GlobalTag.keyname' => $tag
				),
				'joins' => array(
					ClassRegistry::init('Contents.GlobalTag')->autoJoinModel('Contents.GlobalTagged'),
					ClassRegistry::init('Contents.GlobalTag')->autoJoinModel(array(
						'from' => 'Contents.GlobalTagged',
						'model' => 'Contents.GlobalContent',
						'conditions' => array(
							'GlobalContent.id = GlobalTagged.foreign_key'
						)
					))
				)
			));

			$conditions['GlobalContent.foreign_key'] = array_values($postIds);
		}

		$this->Paginator->settings = array(
			'paginated',
			'fields' => array(
				$this->modelClass . '.id',
				$this->modelClass . '.comment_count',
				$this->modelClass . '.views',
				$this->modelClass . '.created',
				$this->modelClass . '.parent_id',
				$this->modelClass . '.ordering',
			),
			'conditions' => $conditions,
			'limit' => $limit,
			'year' => $year,
			'month' => $month
		);

		$this->set('posts', $this->Paginator->paginate());
		$this->set('seoCanonicalUrl', $url);
		$this->set('tagData', $tagData);
		$this->set('title_for_layout', $titleForLayout);
	}

/**
 * User view
 *
 * @param string $slug the slug for the record
 *
 * @return void
 */
	public function view() {
		if (!isset($this->request->params['infinitas'][$this->modelClass . '.id'])) {
			$this->notice('invalid');
		}

		$post = $this->{$this->modelClass}->find('viewData', array(
			'conditions' => array(
				$this->modelClass . '.active' => 1,
				$this->modelClass . '.id' => $this->request->params['infinitas'][$this->modelClass . '.id']
			)
		));

		if (empty($post)) {
			$this->notice('invalid');
		}

		$this->set('post', $post);

		$canonicalUrl = $this->Event->trigger('Blog.slugUrl', $post);
		$this->set('seoCanonicalUrl', $canonicalUrl['slugUrl']['Blog']);

		$this->set('seoMetaDescription', $post[$this->modelClass]['meta_description']);
		$this->set('seoMetaKeywords', $post[$this->modelClass]['meta_keywords']);

		$this->set('title_for_layout', $post[$this->modelClass]['title']);

		Configure::write('Website.keywords', $post[$this->modelClass]['meta_keywords']);
	}

/**
 * Admin dashboard
 *
 * @return void
 */
	public function admin_dashboard() {
		$feed = $this->{$this->modelClass}->find('feed', array(
			'setup' => array(
				'plugin' => 'Blog',
				'controller' => 'blog_posts',
				'action' => 'view',
			),
			'fields' => array(
				$this->modelClass . '.id',
				$this->modelClass . '.title',
				$this->modelClass . '.intro',
				$this->modelClass . '.created'
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
		));

		$this->set('blogFeeds', $feed);

		$this->set('dashboardPostCount', $this->{$this->modelClass}->getCounts());
		$this->set('dashboardPostLatest', $this->{$this->modelClass}->getLatest());
		$this->set('dashboardCommentsCount', $this->{$this->modelClass}->Comment->getCounts('Blog.' . $this->modelClass));
	}

/**
 * Admin index.
 *
 * Uses the {@see FilterComponent} component to filter results.
 *
 * @return void
 */
	public function admin_index() {
		$posts = $this->Paginator->paginate(null, $this->Filter->filter);

		$filterOptions = $this->Filter->filterOptions;
		$filterOptions['fields'] = array(
			'title',
			'body',
			'category_id' => $this->{$this->modelClass}->GlobalContent->find('categoryList'),
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

		$parents = $this->{$this->modelClass}->getParentPosts();
		$contentTags = $this->{$this->modelClass}->GlobalTag->find('all');
		$relatedContent = $this->{$this->modelClass}->find('related', array(
			'admin' => true
		));
		$this->set(compact('contentTags', 'parents', 'relatedContent'));
	}

	public function admin_edit($id = null) {
		parent::admin_edit($id);

		$parents = $this->{$this->modelClass}->getParentPosts();
		$contentTags = $this->{$this->modelClass}->GlobalTag->find('all');
		$relatedContent = $this->{$this->modelClass}->find('related', array(
			'admin' => true
		));
		$this->set(compact('contentTags', 'parents', 'relatedContent'));
	}

	public function admin_view($slug = null) {
		if (!$slug) {
			$this->notice('invalid');
		}

		$post = ((int)$slug > 0)
		? $this->{$this->modelClass}->read(null, $slug)
		: $this->{$this->modelClass}->find('first', array(
			'conditions' => array(
				$this->modelClass . '.slug' => $slug
			)
		));

		$this->set(compact('post'));
		$this->render('view');
	}
}