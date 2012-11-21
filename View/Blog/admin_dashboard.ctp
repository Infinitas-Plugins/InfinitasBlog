<?php
if(!$requreSetup) { ?>
	<div class="dashboard grid_16">
		<h1><?php echo __d('blog', 'Please setup the Blog plugin before use'); ?></h1>
		<p class="info">
			<?php
				echo sprintf(
					__d('blog', 'Add some %s before you start blogging'),
					$this->Html->link(
						__d('contents', 'layouts'),
						array(
							'plugin' => 'contents',
							'controller' => 'global_layouts',
							'action' => 'add'
						)
					)
				);
			?>
		</p>
	</div> <?php
	return;
}

$links = array();
$links['main'] = array(
	array(
		'name' => __d('blog', 'List'),
		'description' => __d('blog', 'View all your posts'),
		'icon' => '/blog/img/icon.png',
		'dashboard' => array('controller' => 'blog_posts', 'action' => 'index')
	),
	array(
		'name' => __d('blog', 'Add'),
		'description' => __d('blog', 'Create a new post'),
		'icon' => '/blog/img/icon.png',
		'dashboard' => array('controller' => 'blog_posts', 'action' => 'add')
	),
	array(
		'name' => __d('blog', 'Active'),
		'description' => __d('blog', 'See what items are currently active'),
		'icon' => '/blog/img/icon.png',
		'dashboard' => array('controller' => 'blog_posts', 'action' => 'index', 'BlogPost.active' => 1)
	),
	array(
		'name' => __d('blog', 'Pending'),
		'description' => __d('blog', 'See what items are currently pending'),
		'icon' => '/blog/img/icon.png',
		'dashboard' => array('controller' => 'blog_posts', 'action' => 'index', 'BlogPost.active' => 0)
	)
);

foreach($links as $name => &$link) {
	$link = $this->Design->arrayToList(current((array)$this->Menu->builDashboardLinks($link, 'newsletter_dashboard_' . $name)), array(
		'ul' => 'icons'
	));
}

echo $this->Design->dashboard($links['main'], __d('blog', 'Posts'), array(
	'class' => 'dashboard span6',
	'info' => Configure::read('Blog.info.posts')
));

echo $this->ModuleLoader->loadDirect('Contents.dashboard_links');

echo $this->ModuleLoader->loadDirect('ViewCounter.popular_items', array(
	'model' => 'Blog.post'
));