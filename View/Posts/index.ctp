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

	//echo $this->element('modules/tag_data', array('plugin' => 'tags', 'tagData' => $tagData));
	
    foreach($posts as $k => &$post) {
		$eventData = $this->Event->trigger('blogBeforeContentRender', array('_this' => $this, 'post' => $post));
		$post['Post']['events_before'] = '';
		foreach((array)$eventData['blogBeforeContentRender'] as $_plugin => $_data){
			$post['Post']['events_before'] .= '<div class="'.$_plugin.'">'.$_data.'</div>';
		}

		$eventData = $this->Event->trigger('blogAfterContentRender', array('_this' => $this, 'post' => $post));
		$post['Post']['events_after'] = '';
		foreach((array)$eventData['blogAfterContentRender'] as $_plugin => $_data){
			$post['Post']['events_after'] .= '<div class="'.$_plugin.'">'.$_data.'</div>';
		}

		$eventData = $this->Event->trigger('blog.slugUrl', array('type' => 'posts', 'data' => $post));
		$url = Router::url(current($eventData['slugUrl']), true);
		$post['Post']['title_link'] = $this->Html->link($post['Post']['title'], $url);
		$post['Post']['url'] = $url;

		$time = strtotime($post['Post']['created']);
		$post['Post']['created'] = sprintf(
			'%s %s | %s',
			date('M', $time),
			date('Y', $time),
			date('H:i', $time)
		);

		if(!($firstPage && $k === 0)) {
			$post['Post']['body'] = $this->Text->truncate($post['Post']['body'], Configure::read('Blog.preview'), array('html' => true));
		}

		$post['Post']['module_comments'] = $this->element(
			'modules/comment',
			array(
				'plugin' => 'comments',
				'content' => $post,
				'modelName' => 'Post',
				'foreign_id' => $post['Post']['id']
			)
		);


		$post['Post']['module_tags_list'] = $this->TagCloud->tagList($post, ',');
		$post['Post']['module_tags'] = $this->element(
			'modules/post_tag_cloud',
			array(
				'plugin' => 'blog',
				'tags' => $post['GlobalTagged'],
				'title' => 'Tags'
			)
		);

		$post['Post']['author_link'] = $this->GlobalContents->author($post);
		$post['Post']['module_comment_count'] = sprintf(__d('comments', '%d Comments'), $post['Post']['comment_count']);
    }
	
	if(count($posts) > 0) {
		$this->set('posts', $posts);
		$this->set('paginationNavigation', $this->element('pagination/navigation'));
	}

	if(empty($globalLayoutTemplate)) {
		throw new Exception('Template was not loaded, make sure one exists');
	}
	
	echo $this->GlobalContents->renderTemplate($globalLayoutTemplate);