<?php
    /**
     * Blog index view file.
     *
     * Generate the index page for the blog posts
     *
     * Copyright (c) 2009 Carl Sutton ( dogmatic69 )
     *
     * Licensed under The MIT License
     * Redistributions of files must retain the above copyright notice.
     *
     * @filesource
     * @copyright     Copyright (c) 2009 Carl Sutton ( dogmatic69 )
     * @link          http://infinitas-cms.org
     * @package       blog
     * @subpackage    blog.views.index
     * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
     */

	$firstPage = false;
	$mainPageCheck = empty($this->params['tag']) && empty($this->params['named']['page']) || (!empty($this->params['named']['page']) && $this->params['named']['page'] == 1);
	if($mainPageCheck) {
		$firstPage = true;
	}

	echo $this->element('modules/tag_data', array('plugin' => 'tags', 'tagData' => $tagData));
	
    foreach($posts as $k => $post) {  ?>
		<div class="beforeEvent">
			<?php
				$eventData = $this->Event->trigger('blogBeforeContentRender', array('_this' => $this, 'post' => $post));
				
				foreach((array)$eventData['blogBeforeContentRender'] as $_plugin => $_data){
					echo '<div class="'.$_plugin.'">'.$_data.'</div>';
				}
			?>
		</div>
		<div class="wrapper">
			<div class="introduction <?php echo $this->layout; ?>">
				<h2>
					<?php
						$eventData = $this->Event->trigger('blog.slugUrl', array('type' => 'posts', 'data' => $post));
						$urlArray = current($eventData['slugUrl']);
						echo $this->Html->link(
							$post['Post']['title'],
							$urlArray
						);
					?>
					<small>
						<?php
							$time = strtotime($post['Post']['created']);
							echo sprintf(
								'%s %s | %s',
								date('M', $time),
								date('Y', $time),
								date('H:i', $time)
							);
							//echo $this->Time->niceShort($post['Post']['created']);
						?>
					</small>
				</h2>
				<div class="content <?php echo $this->layout; ?>">
					<?php
						if($firstPage && !$k) {
							echo $post['Post']['body'];
						}
						else {
							?><p><?php echo $this->Text->truncate($post['Post']['body'], Configure::read('Blog.preview'), array('html' => true)); ?></p><?php
						}

						echo sprintf('<p>Let me know what you %s</p>', $this->Html->link('think', $urlArray + array('#' => 'comment')));
					?>
				</div>
			</div>
			<?php
				echo $this->element(
					'modules/post_tag_cloud',
					array(
						'plugin' => 'blog',
						'tags' => $post['Tag'],
						'title' => 'Tags'
					)
				);

				echo $this->element(
					'modules/comment',
					array(
						'plugin' => 'comments',
						'content' => $post,
						'modelName' => 'Post',
						'foreign_id' => $post['Post']['id']
					)
				);
			?>
		</div>
		<div class="afterEvent">
			<?php
				$eventData = $this->Event->trigger('blogAfterContentRender', array('_this' => $this, 'post' => $post));
				foreach((array)$eventData['blogAfterContentRender'] as $_plugin => $_data){
					echo '<div class="'.$_plugin.'">'.$_data.'</div>';
				}
			?>
		</div> <?php
    }

    echo $this->element('pagination/navigation');
?>