<?php
	/* 
	 * Short Description / title.
	 * 
	 * Overview of what the file does. About a paragraph or two
	 * 
	 * Copyright (c) 2010 Carl Sutton ( dogmatic69 )
	 * 
	 * @filesource
	 * @copyright Copyright (c) 2010 Carl Sutton ( dogmatic69 )
	 * @link http://www.infinitas-cms.org
	 * @package {see_below}
	 * @subpackage {see_below}
	 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
	 * @since {check_current_milestone_in_lighthouse}
	 * 
	 * @author {your_name}
	 * 
	 * Licensed under The MIT License
	 * Redistributions of files must retain the above copyright notice.
	 */

	$popularPosts = ClassRegistry::init('Blog.BlogPost')->getMostViewed($config['limit']);
?>
<h4><?php echo __('Popular Posts'); ?></h4>
<?php
	$links = array();
	foreach($popularPosts as $post){
		$url = $this->Event->trigger('blog.slugUrl', array('type' => 'posts', 'data' => $post));
		$links[] = $this->Html->link(
			$this->Text->truncate(strip_tags($post['Post']['title']), 40),
			current($url['slugUrl']),
			array(
				'title' => $post['Post']['title']
			)
		);
	}

	if(!empty($links)){
		?><ul class="popularPosts"><li><?php echo implode('</li><li>', $links); ?></li></ul><?php
	}
?>