<?php
	class BlogController extends BlogAppController {
		public $name = 'Blog';

		public $uses = array();
		
		public function admin_dashboard() {
			$Post = ClassRegistry::init('Blog.Post');

			$requireSetup = count($Post->Layout->find('list')) >= 1;
			$this->set('requreSetup', $requireSetup);
			$this->set('hasContent', $Post->find('count') >= 1);
		}
	}
