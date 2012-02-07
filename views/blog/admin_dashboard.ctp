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
?>
<div class="dashboard grid_16">
	<h1><?php __d('blog', 'Posts'); ?></h1>
	<?php echo $this->Design->arrayToList(current($this->Menu->builDashboardLinks($links['main'], 'blog_main_icons')), 'icons'); ?>
	<p class="info"><?php echo Configure::read('Blog.info.posts'); ?></p>
</div>
<?php
	echo $this->element(
		'modules/admin/dashboard_links',
		array(
			'plugin' => 'contents'
		)
	);

	echo $this->element(
		'modules/admin/popular_items',
		array(
			'plugin' => 'view_counter',
			'model' => 'Blog.post'
		)
	);