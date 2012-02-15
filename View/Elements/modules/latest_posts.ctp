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

	$defaultConfig = array(
		'title' => 'Latest Posts',
		'limit' => 5,
		'title_length' => 60
	);
	$config = array_merge($defaultConfig, $config);

	$latestPosts = ClassRegistry::init('Blog.BlogPost')->getLatest($config['limit']);
?>
<h3><?php echo __($config['title']); ?></h3>
<?php
	$latestPostlinks = array();
	foreach($latestPosts as $latestPost){
		$url = $this->Event->trigger('blog.slugUrl', array('type' => 'posts', 'data' => $latestPost));
		$latestPostlinks[] = $this->Html->link(
			$this->Text->truncate(strip_tags($latestPost['BlogPost']['title']), $config['title_length']),
			current($url['slugUrl']),
			array(
				'title' => $latestPost['BlogPost']['title']
			)
		);
	}

	if(!empty($latestPostlinks)){
		echo $this->Design->arrayToList($latestPostlinks, 'posts latest');
	}
	unset($latestPosts, $latestPost, $latestPostlinks);