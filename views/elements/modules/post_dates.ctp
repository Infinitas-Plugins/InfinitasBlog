<?php
    /**
     * Blog post_dates view element file.
     *
     * date menu for the users in blog
     *
     * @todo -c Implement . move to {@see PostLayoutHelper}
     * @todo -c Implement . move css to a file
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
     * @subpackage    blog.views.elements.post_dates
     * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
     */

	if(!isset($postDates)){
		$postDates = ClassRegistry::init('Blog.Post')->getDates();
	}

	if(empty($postDates)){
		echo __('No posts found', true);
		return;
	}
?>
<h3><?php echo __('Browse By Date', true); ?></h3>
<ul>
	<?php
		foreach($postDates as $year => $months){
			echo '<li><h4>', $this->Html->link(
				$year,
				array(
					'plugin' => 'blog',
					'controller' => 'posts',
					'action'  => 'index',
					'all',
					$year
				)
			), '</h4>';

			if (!empty($months)){
				echo '<ul>';
					foreach($months as $month){
						echo '<li>', $this->Html->link(
							date('F', mktime(0,0,0,$month)),
							array(
								'plugin' => 'blog',
								'controller' => 'posts',
								'action'  => 'index',
								'all',
								$year,
								$month
							)
						), '</li>';
					}
				echo '</ul>';
			}

			echo '</li>';
		}
	?>
</ul>