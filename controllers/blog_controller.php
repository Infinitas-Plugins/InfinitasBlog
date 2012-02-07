<?php
	class BlogController extends BlogAppController {
		public $name = 'Blog';

		public $uses = array();
		
		public function admin_dashboard() {
			$Post = ClassRegistry::init('Blog.Post');

			$this->set('requreSetup', count($Post->GlobalContent->GlobalLayout->find('list')) >= 1);
			$this->set('hasContent', $Post->find('count') >= 1);
		}
	}
