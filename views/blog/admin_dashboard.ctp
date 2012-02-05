<?php
	if(!$requreSetup) { ?>
		<div class="dashboard grid_16">
			<h1><?php echo __d('blog', 'Please setup the Blog plugin before use', true); ?></h1>
			<p class="info">
				<?php
					echo sprintf(
						__d('blog', 'Add some %s before you start blogging', true),
						$this->Html->link(
							__d('contents', 'layouts', true),
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
			'name' => __d('blog', 'Categories', true),
			'description' => __d('blog', 'Configure the categories for your content', true),
			'icon' => '/categories/img/icon.png',
			'dashboard' => array('plugin' => 'categories', 'controller' => 'categories', 'action' => 'index')
		),
		array(
			'name' => __d('blog', 'List', true),
			'description' => __d('blog', 'View all your posts', true),
			'icon' => '/blog/img/icon.png',
			'dashboard' => array('controller' => 'posts', 'action' => 'index')
		),
		array(
			'name' => __d('blog', 'Add', true),
			'description' => __d('blog', 'Create a new post', true),
			'icon' => '/blog/img/icon.png',
			'dashboard' => array('controller' => 'posts', 'action' => 'add')
		),
		array(
			'name' => __d('blog', 'Active', true),
			'description' => __d('blog', 'See what items are currently active', true),
			'icon' => '/blog/img/icon.png',
			'dashboard' => array('controller' => 'posts', 'action' => 'index', 'Post.active' => 1)
		),
		array(
			'name' => __d('blog', 'Pending', true),
			'description' => __d('blog', 'See what items are currently pending', true),
			'icon' => '/blog/img/icon.png',
			'dashboard' => array('controller' => 'posts', 'action' => 'index', 'Post.active' => 0)
		)
	);

	$links['config'] = array(
		array(
			'name' => __d('contents', 'Layouts', true),
			'description' => __d('blog', 'Configure the layouts for your post', true),
			'icon' => '/contents/img/icon.png',
			'dashboard' => array('plugin' => 'contents', 'controller' => 'global_layouts', 'action' => 'index', 'GlobalLayout.model' => 'Blog')
		),
		array(
			'name' => __d('routes', 'Routes', true),
			'description' => __d('blog', 'Manage post routes', true),
			'icon' => '/routes/img/icon.png',
			'dashboard' => array('plugin' => 'routes', 'controller' => 'routes', 'action' => 'index', 'Route.plugin' => 'Blog')
		),
		array(
			'name' => __d('modules', 'Modules', true),
			'description' => __d('blog', 'Manage post modules', true),
			'icon' => '/modules/img/icon.png',
			'dashboard' => array('plugin' => 'modules', 'controller' => 'modules', 'action' => 'index', 'Module.plugin' => 'Blog')
		),
		array(
			'name' => __d('filemanager', 'Assets', true),
			'description' => __d('blog', 'Manage post assets', true),
			'icon' => '/filemanager/img/icon.png',
			'dashboard' => array('plugin' => 'filemanager', 'controller' => 'filemanager', 'action' => 'index', 'webroot', 'img')
		),
		array(
			'name' => __d('locks', 'Locked', true),
			'description' => __d('blog', 'Manage locked posts', true),
			'icon' => '/locks/img/icon.png',
			'dashboard' => array('plugin' => 'locks', 'controller' => 'locks', 'action' => 'index', 'Lock.class' => 'Blog')
		),
		array(
			'name' => __d('trash', 'Trash', true),
			'description' => __d('blog', 'View / Restore previously removed posts', true),
			'icon' => '/trash/img/icon.png',
			'dashboard' => array('plugin' => 'trash', 'controller' => 'trash', 'action' => 'index', 'Trash.model' => 'Blog')
		)
	);

	if($this->Infinitas->hasPlugin('ViewCounter')) {
		$links['config'][] =  array(
			'name' => __d('view_counter', 'Views', true),
			'description' => __d('blog', 'Track post popularity', true),
			'icon' => '/view_counter/img/icon.png',
			'dashboard' => array('plugin' => 'view_counter', 'controller' => 'view_counts', 'action' => 'reports', 'ViewCount.model' => 'Blog')
		);
	}
?>
<div class="dashboard grid_16">
	<h1><?php __d('blog', 'Posts'); ?></h1>
	<?php echo $this->Design->arrayToList(current($this->Menu->builDashboardLinks($links['main'], 'blog_main_icons')), 'icons'); ?>
	<p class="info"><?php echo Configure::read('Blog.info.posts'); ?></p>
</div>
<div class="dashboard grid_16">
	<h1><?php __d('blog', 'Config / Data'); ?></h1>
	<?php echo $this->Design->arrayToList(current($this->Menu->builDashboardLinks($links['config'], 'blog_config_icons')), 'icons'); ?>
	<p class="info"><?php echo Configure::read('Blog.info.config'); ?></p>
</div>
<?php
	echo $this->element(
		'modules/admin/popular_items',
		array(
			'plugin' => 'view_counter',
			'model' => 'Blog.post'
		)
	);