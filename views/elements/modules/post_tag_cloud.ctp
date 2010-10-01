<h3><?php $title = isset($title) && !empty($title) ? $title : 'Tag cloud'; echo __($title, true); ?></h3>
<p>
	<?php
		if(!isset($tags)){
			$tags = ClassRegistry::init('Blog.Post')->getTags();
		}
		
		// format is different of views / the find above
		if(!isset($tags[0]['Tag'])){
			foreach($tags as $tag){
				$_tags[]['Tag'] = $tag;
			}
			$tags = $_tags;
			unset($_tags);
		}

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
				'named' => 'tag'
			)
		);
	?>
</p>