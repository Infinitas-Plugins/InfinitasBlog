<?php
	/* Tag Test cases generated on: 2010-03-13 15:03:17 : 1268487017*/
	App::import('Model', 'blog.GlobalTag');

	class TagTestCase extends CakeTestCase {
		var $fixtures = array(
			'plugin.blog.category',
			'plugin.blog.post',
			'plugin.blog.posts_tag',
			'plugin.blog.tag',

			'plugin.management.user',
			'plugin.management.group',
			'plugin.management.aco',
			'plugin.management.aro',
			'plugin.management.aros_aco',
		);

		function startTest() {
			$this->GlobalTag =& ClassRegistry::init('Contents.GlobalTag');
		}

		function endTest() {
			unset($this->GlobalTag);
			ClassRegistry::flush();
		}

	}
?>