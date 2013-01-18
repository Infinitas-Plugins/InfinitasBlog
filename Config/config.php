<?php
/**
 * Blog config defaults
 * 
 * @copyright Copyright (c) 2010 Carl Sutton ( dogmatic69 )
 *
 * @link http://www.infinitas-cms.org
 * @package Blog.Config
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @since 0.6a
 *
 * @author Carl Sutton <dogmatic69@infinitas-cms.org>
 */

$config['Blog'] = array(
	'allow_comments' => true,
	'allow_ratings' => true,
	'depreciate' => '6 months', // the time before the post is marked as old
	'preview' => 400, // the length of the text to show on index pages
	'time_format' => 'jS M Y',
	'before' => array(
	),
	'after' => array(
		'view_count'
	),
	'info' => array(
		'posts' => __d('blog', 'You can view the posts on your site now using the list icons. You can see everything or filter out active or disabled posts also. You can add new posts using the add icon.'),
		'config' => __d('blog', 'Configure and manage how your posts are displayed. Create different SEO urls, manage images, restore trash and more')
	),
	'robots' => array(
		'index' => array(
			'index' => false,
			'follow' => true
		),
		'view' => array(
			'index' => true,
			'follow' => true
		),
	),
	'meta' => array(
		'keywords' => 'infinitas blog plugin,cakephp powered blog,php blog software',
		'description' => 'The Infinitas blog plugin is designed for building powerful flexible blogs running on the Infinitas platform. For more information see http://infinitas-cms.org',
		'title' => ''
	),
	'slugUrl' => array(
		'posts' => array(
			'BlogPost.id' => 'id',
			'BlogPost.slug' => 'slug',
			'GlobalCategory.id' => 'category_id',
			'GlobalCategory.slug' => 'category',
			'BlogPost.GlobalCategory.id' => 'category_id',
			'BlogPost.GlobalCategory.slug' => 'category',
			'url' => array(
				'plugin' => 'plugin',
				'controller' => 'blog_posts',
				'action' => 'view'
			)
		),
		'year' => array(
			'BlogPost.created_year' => 'year',
			'url' => array(
				'plugin' => 'plugin',
				'controller' => 'blog_posts',
				'action' => 'index'
			)
		),
		'year_month' => array(
			'BlogPost.created_year' => 'year',
			'BlogPost.created_month' => 'month',
			'url' => array(
				'plugin' => 'plugin',
				'controller' => 'blog_posts',
				'action' => 'index'
			)
		),
		'tag' => array(
			'tag' => 'tag',
			'url' => array(
				'plugin' => 'plugin',
				'controller' => 'blog_posts',
				'action' => 'index'
			)
		)
	)
);