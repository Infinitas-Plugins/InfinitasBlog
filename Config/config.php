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

	 $config['Blog'] = array(
		 'allow_comments' => true,
		 'allow_ratings' => true,
		 'depreciate' => '6 months', // the time before the post is marked as old
		 'preview' => 400, // the length of the text to show on index pages
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
		 )
	 );