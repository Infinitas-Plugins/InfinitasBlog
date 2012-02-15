<?php
	class BlogController extends BlogAppController {
		public $uses = array(
			'Blog.BlogPost'
		);
		
		public function admin_dashboard() {

			$this->set('requreSetup', count($this->BlogPost->GlobalContent->GlobalLayout->find('list')) >= 1);
			$this->set('hasContent', $this->BlogPost->find('count') >= 1);
		}
	}
