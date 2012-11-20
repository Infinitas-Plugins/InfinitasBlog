<?php
App::uses('BlogController', 'Blog.Controller');

/**
 * BlogController Test Case
 *
 */
class BlogControllerTest extends ControllerTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.blog.blog_post',

		'plugin.contents.global_content',
		'plugin.contents.global_layout',
		'plugin.contents.global_category',
		'plugin.contents.global_tagged',
		'plugin.contents.global_tag',

		'plugin.themes.theme',
		'plugin.view_counter.view_counter_view',
		'plugin.users.user',
		'plugin.users.group',

		'plugin.comments.infinitas_comment',
		'plugin.comments.infinitas_comment_attribute'
	);

/**
 * testAdminDashboard method
 *
 * @return void
 */
	public function testAdminDashboard() {
	}

}
