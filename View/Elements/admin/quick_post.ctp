<?php
	if (!empty($categories)) {
		echo $this->Form->create('BlogPost', array('url' => array('plugin' => 'blog', 'controller' => 'blog_posts', 'action' => 'add')));
			echo $this->Form->input('category_id', array('empty' => Configure::read('Website.empty_select')));
			echo $this->Form->input('title', array('class' => 'title'));
			echo $this->Form->input('new_tags', array('label' => __('Tags'), 'class'=>'title'));
			echo '<br/>';
			echo $this->Infinitas->wysiwyg('BlogPost.body', array('toolbar' => 'AdminBasic'));
			echo $this->Form->input('active', array('type' => 'checkbox', 'checked' => true));
		echo $this->Form->submit('Save', array('style' => 'float:right; clear:none;'));
	}
	else{
		echo sprintf(
			__('No categories found, %s', true ),
			$this->Html->link(
				__('set some up', true ),
				array(
					'plugin' => 'blog',
					'controller' => 'categories',
					'action' => 'add'
				)
			)
		);
	}