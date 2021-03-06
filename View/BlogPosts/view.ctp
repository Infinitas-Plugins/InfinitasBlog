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
	$eventData = $this->Event->trigger('blogBeforeContentRender', array('post' => $post));
	$post['BlogPost']['events_before'] = '';
	foreach((array)$eventData['blogBeforeContentRender'] as $_plugin => $_data) {
		$post['BlogPost']['events_before'] .= '<div class="'.$_plugin.'">'.$_data.'</div>';
	}

	/**
	 * events for after the output
	 */
	$eventData = $this->Event->trigger('blogAfterContentRender', array('post' => $post));
	$post['BlogPost']['events_after'] = '';
	foreach((array)$eventData['blogAfterContentRender'] as $_plugin => $_data) {
		$post['BlogPost']['events_after'] .= '<div class="'.$_plugin.'">'.$_data.'</div>';
	}

	$eventData = $this->Event->trigger('Blog.slugUrl', array('type' => 'posts', 'data' => $post));
	$post['BlogPost']['url'] = Router::url(current($eventData['slugUrl']), true);
	$post['BlogPost']['title_link'] = $this->Html->link($post['BlogPost']['title'], $post['BlogPost']['url']);

	$post['BlogPost']['created'] = CakeTime::format(Configure::read('Blog.time_format'), $post['BlogPost']['created']);
	$post['BlogPost']['modified'] = CakeTime::format(Configure::read('Blog.time_format'), $post['BlogPost']['modified']);

	$post['BlogPost']['module_tags_list'] = $this->ModuleLoader->loadDirect('Contents.tag_cloud', array(
		'tags' => $post['GlobalTagged'],
		'title' => 'Tags',
		'box' => false,
		'category' => $post['GlobalCategory']['slug']
	));
	$post['BlogPost']['module_tags'] = $this->ModuleLoader->loadDirect('Contents.tag_cloud', array(
		'tags' => $post['GlobalTagged'],
		'title' => 'Tags',
		'category' => $post['GlobalCategory']['slug']
	));

	$post['BlogPost']['author_gravatar'] = $this->GlobalContents->gravatar($post);
	$post['BlogPost']['author_gravatar_ulr'] = $this->Gravatar->url($post['ContentAuthor']['email']);
	$post['BlogPost']['author_link'] = $this->GlobalContents->author($post);
	$post['BlogPost']['module_comment_count'] = $this->Html->link(
		sprintf(__d('comments', '%s Comments'), $post['BlogPost']['comment_count']),
		'#comment'
	);

	$post['BlogPost']['module_comments'] = $this->ModuleLoader->loadDirect('Comments.comment', array(
		'content' => $post,
		'modelName' => 'BlogPost',
		'foreignId' => $post['BlogPost']['id']
	));

	// need to overwrite the stuff in the viewVars for mustache
	$this->set('post', $post);
	echo $this->GlobalContents->renderTemplate($post);