<h3><?php echo __('Tag cloud', true); ?></h3>
<p>
	<?php
		$tags = ClassRegistry::init('Blog.Post')->getTags();
		echo $this->TagCloud->display(
			$tags,
			array(
				'before' => '<li size="%size%" class="tag">',
				'after'  => '</li>',
				'url' => array(
					'plugin' => 'blog',
					'controller' => 'posts',
					'action' => 'index'
				),
				'named' => 0
			)
		);
	?>
</p>