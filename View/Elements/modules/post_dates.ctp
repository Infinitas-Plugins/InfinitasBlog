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
		$postDates = ClassRegistry::init('Blog.BlogPost')->find('dates');
	}

	if(empty($postDates)){
		echo __('No posts found');
		return;
	}

	?><h3><?php echo __('Browse By Date'); ?></h3><?php

	$lis = array();
	foreach($postDates as $year => $months){
		$url = $this->Event->trigger('Blog.slugUrl', array('type' => 'year', 'data' => array('year' => $year)));
		echo sprintf('<h4>%s</h4>%s', $this->Html->link($year, current($url['slugUrl'])), is_string($months) ? $months : '');
		
		if (!empty($months)){
			$_monthsLi = array();
			foreach($months as $month){
				$url = $this->Event->trigger('Blog.slugUrl', array('type' => 'year_month', 'data' => array('year' => $year, 'month' => $month)));
				$_monthsLi[] = $this->Html->link(
					date('F', mktime(0,0,0,$month)),
					current($url['slugUrl'])
				);
			}

			echo sprintf('<ul><li>%s</li></ul>', implode('</li><li>', $_monthsLi));
		}
	}
?>