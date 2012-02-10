<?php
    /**
     * Blog Comments view
     *
     * this is the page for users to view blog posts
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
     * @subpackage    blog.views.posts.view
     * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
     */

	/**
	 * events for before the output
	 */
	$eventData = $this->Event->trigger('blogBeforeContentRender', array('_this' => $this, 'post' => $post));
	$post['Post']['events_before'] = '';
	foreach((array)$eventData['blogBeforeContentRender'] as $_plugin => $_data){
		$post['Post']['events_before'] .= '<div class="'.$_plugin.'">'.$_data.'</div>';
	}

	/**
	 * events for after the output
	 */
	$eventData = $this->Event->trigger('blogAfterContentRender', array('_this' => $this, 'post' => $post));
	$post['Post']['events_after'] = '';
	foreach((array)$eventData['blogAfterContentRender'] as $_plugin => $_data){
		$post['Post']['events_after'] .= '<div class="'.$_plugin.'">'.$_data.'</div>';
	}

	$eventData = $this->Event->trigger('blog.slugUrl', array('type' => 'posts', 'data' => $post));
	$post['Post']['url'] = Router::url(current($eventData['slugUrl']), true);
	$post['Post']['title_link'] = $this->Html->link($post['Post']['title'], $post['Post']['url']);
	
	$post['Post']['created'] = $this->Time->niceShort($post['Post']['created']);
	$post['Post']['modified'] = $this->Time->niceShort($post['Post']['modified']);

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

	$post['Post']['module_comment_count'] = $this->Html->link(
		sprintf(__d('comments', '%d Comments', true), count($post['PostComment'])),
		'#comments-top'
	);

	$post['Post']['module_comments'] = $this->element(
		'modules/comment',
		array(
			'plugin' => 'comments',
			'content' => $post,
			'modelName' => 'Post',
			'foreign_id' => $post['Post']['id']
		)
	);
	
	// need to overwrite the stuff in the viewVars for mustache
	$this->set('post', $post);
	echo $this->GlobalContents->renderTemplate($post);