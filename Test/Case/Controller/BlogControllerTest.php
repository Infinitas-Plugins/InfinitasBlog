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
		'plugin.blog.global_content',
		'plugin.blog.global_layout',
		'plugin.blog.theme',
		'plugin.blog.global_category',
		'plugin.blog.group',
		'plugin.blog.view_counter_view',
		'plugin.blog.user',
		'plugin.blog.global_tagged',
		'plugin.blog.global_tag',
		'plugin.blog.infinitas_comment',
		'plugin.blog.infinitas_comment_attribute',
		'plugin.blog.infinitas_comment_attributes'
	);

/**
 * testAdminDashboard method
 *
 * @return void
 */
	public function testAdminDashboard() {
	}

}
