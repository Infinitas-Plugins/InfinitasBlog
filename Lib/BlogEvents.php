<?php
	final class BlogEvents extends AppEvents{
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
				'Posts' => array('controller' => 'posts', 'action' => 'index'),
				'Active' => array('controller' => 'posts', 'action' => 'index', 'BlogPost.active' => 1),
				'Pending' => array('controller' => 'posts', 'action' => 'index', 'BlogPost.active' => 0)
			);

			return $menu;
		}

		public function onSetupConfig(){
			return Configure::load('Blog.config');
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

			$data['data']['Post'] = isset($data['data']['Post']) ? $data['data']['Post'] : $data['data'];

			$categorySlug = 'news-feed';
			
			if(!empty($data['data']['GlobalCategory']['slug'])) {
				$categorySlug = $data['data']['GlobalCategory']['slug'];
			}

			else if(!empty($data['data']['Post']['GlobalCategory']['slug'])) {
				$categorySlug = $data['data']['Post']['GlobalCategory']['slug'];
			}
			
			switch($data['type']){
				case 'posts':
					return array(
						'plugin' => 'blog',
						'controller' => 'posts',
						'action' => 'view',
						'id' => $data['data']['Post']['id'],
						'category' => $categorySlug,
						'slug' => $data['data']['Post']['slug']
					);
					break;

				case 'year':
					return array(
						'plugin' => 'blog',
						'controller' => 'posts',
						'action' => 'index',
						'year' => $data['data']['year']
					);
					break;

				case 'year_month':
					return array(
						'plugin' => 'blog',
						'controller' => 'posts',
						'action' => 'index',
						'year' => $data['data']['year'],
						$data['data']['month']
					);
					break;

				case 'tag':
					return array(
						'plugin' => 'blog',
						'controller' => 'posts',
						'action' => 'index',
						'tag' => $data['data']['tag']
					);
					break;
			} // switch
		}

		public function onRequireHelpersToLoad($event){
			
		}

		public function onRequireCssToLoad($event){
			if($event->Handler->params['plugin'] == 'blog'){
				return '/blog/css/blog';
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