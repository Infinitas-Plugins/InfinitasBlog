<?php
    /**
     * Blog Comments admin edit posts
     *
     * this is the page for admins to edit blog posts
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
     * @subpackage    blog.views.posts.admin_edit
     * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
     */

    echo $this->Form->create('Post');
        echo $this->Infinitas->adminEditHead();
		echo $this->element('content_form', array('plugin' => 'contents')); ?>
		<fieldset>
			<h1><?php echo __('Other Info', true); ?></h1><?php
			echo $this->Form->input('id');
			echo $this->element('category_list', array('plugin' => 'Categories'));
			echo $this->Form->input('parent_id', array('options' => $parents, 'empty' => __('No Parent', true)));
			echo $this->Form->input('active');
			echo $this->Form->input('tags'); ?>
		</fieldset><?php
    echo $this->Form->end();
?>