<?php
	final class BlogEvents extends AppEvents {
		public function onPluginRollCall(){
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

		public function onRequireTodoList($event){
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

		public function onAdminMenu($event){
			$menu['main'] = array(
				'Dashboard' => array('plugin' => 'blog', 'controller' => 'blog', 'action' => 'dashboard'),
				'Posts' => array('plugin' => 'blog', 'controller' => 'blog_posts', 'action' => 'index'),
				'Active' => array('plugin' => 'blog', 'controller' => 'blog_posts', 'action' => 'index', 'BlogPost.active' => 1),
				'Pending' => array('plugin' => 'blog', 'controller' => 'blog_posts', 'action' => 'index', 'BlogPost.active' => 0)
			);

			return $menu;
		}
		
		public function onSetupCache(){
			return array(
				'name' => 'blog',
				'config' => array(
					'prefix' => 'blog.'
				)
			);
		}

		public function onSlugUrl($event, $data){
			if(!isset($data['data'])){
				$data['data'] = $data;
			}
			if(!isset($data['type'])){
				$data['type'] = 'posts';
			}

			if(empty($data['data']['GlobalCategory']['slug'])) {
				$data['data']['GlobalCategory']['slug'] = __d('blog', 'news-feed');
			}
			
			return parent::onSlugUrl($event, $data['data'], $data['type']);
		}

		public function onRequireHelpersToLoad($event){
			
		}

		public function onRequireCssToLoad($event){
			if($event->Handler->params['plugin'] == 'blog'){
				return array(
					'Blog.blog'
				);
			}
		}

		public function onSetupRoutes($event, $data = null) {
			Router::connect(
				'/admin/blog',
				array(
					'admin' => true,
					'prefix' => 'admin',
					'plugin' => 'blog',
					'controller' => 'blog',
					'action' => 'dashboard'
				)
			);
		}
	}