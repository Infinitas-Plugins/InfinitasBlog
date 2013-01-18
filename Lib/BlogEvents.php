<?php
class BlogEvents extends AppEvents {

	public function onPluginRollCall(Event $Event) {
		return array(
			'name' => 'Blog',
			'description' => 'Blogging platform',
			'icon' => '/blog/img/icon.png',
			'author' => 'Infinitas',
			'dashboard' => array(
				'plugin' => 'blog',
				'controller' => 'blog',
				'action' => 'dashboard'
			)
		);
	}

	public function onRequireTodoList(Event $Event) {
		return array(
			array(
				'name' => 'warning no categories',
				'type' => 'warning',
				'url' => array('plugin' => 'categories', 'controlelr' => 'categories', 'action' => 'add')
			),
			array(
				'name' => 'Testing: error',
				'type' => 'error',
				'url' => array('plugin' => 'categories', 'controlelr' => 'categories', 'action' => 'index')
			),
			array(
				'name' => 'Testing: info',
				'type' => 'info',
				'url' => array('plugin' => 'categories', 'controlelr' => 'categories', 'action' => 'add')
			)
		);
	}

	public function onAdminMenu(Event $Event) {
		$menu['main'] = array(
			'Dashboard' => array('plugin' => 'blog', 'controller' => 'blog', 'action' => 'dashboard'),
			'Posts' => array('plugin' => 'blog', 'controller' => 'blog_posts', 'action' => 'index'),
			'Active' => array('plugin' => 'blog', 'controller' => 'blog_posts', 'action' => 'index', 'BlogPost.active' => 1),
			'Pending' => array('plugin' => 'blog', 'controller' => 'blog_posts', 'action' => 'index', 'BlogPost.active' => 0)
		);

		return $menu;
	}

	public function onSetupCache(Event $Event) {
		return array(
			'name' => 'blog',
			'config' => array(
				'prefix' => 'blog.'
			)
		);
	}

	public function onSlugUrl(Event $Event, $data = null, $type = null) {
		if (!isset($data['data'])) {
			$data['data'] = $data;
		}
		if (!isset($data['type'])) {
			$data['type'] = 'posts';
		}

		if (empty($data['data']['GlobalCategory']['slug'])) {
			$data['data']['GlobalCategory']['slug'] = __d('blog', 'news-feed');
		}

		return parent::onSlugUrl($Event, $data['data'], $data['type']);
	}

	public function onRequireCssToLoad(Event $Event) {
		if ($Event->Handler->params['plugin'] == 'blog') {
			return array(
				'Blog.blog'
			);
		}
	}

	public function onSetupRoutes(Event $Event, $data = null) {
		InfinitasRouter::connect('/admin/blog', array(
			'admin' => true,
			'prefix' => 'admin',
			'plugin' => 'blog',
			'controller' => 'blog',
			'action' => 'dashboard'
		));
	}

	public function onRouteParse(Event $Event, $data = null) {
		$count = 0;
		if (!empty($data['slug']) && ($data['controller'] == 'blog_posts' && $data['action'] == 'view')) {
			$count = ClassRegistry::init('Blog.BlogPost')->find('count', array(
				'conditions' => array(
					'GlobalContent.slug' => $data['slug']
				)
			));

			if ($count < 1) {
				return false;
			}

			return $data;
		}
	}
}