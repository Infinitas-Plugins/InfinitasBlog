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

    echo $this->Form->create('BlogPost');
        echo $this->Infinitas->adminEditHead();

		$tabs = array(
			__d('contents', 'Content'),
			__d('contents', 'Author'),
			__d('blog', 'Other Data')
		);

		$content = array(
			$this->element('content_form', array('plugin' => 'Contents', 'intro' => false)),
			$this->element('author_form', array('plugin' => 'Contents')),
			implode('', array(
				$this->Form->input('id'),
				$this->Form->input('active'),
				$this->Form->hidden('ContentConfig.id'),
				$this->element('meta_form', array('plugin' => 'Contents'),
				$this->Form->input('parent_id', array('options' => $parents, 'empty' => __('No Parent'))))))
		);

		echo $this->Design->tabs($tabs, $content);
    echo $this->Form->end();