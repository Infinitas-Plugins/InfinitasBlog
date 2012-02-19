<?php
	$hasPrev = $this->Paginator->hasPrev();
	$hasNext = $this->Paginator->hasNext();

	if (!($hasPrev && $hasNext) && !$this->Paginator->request->params['paging'][$this->Paginator->defaultModel()]['current']) {
		echo sprintf('<p class="pagination empty">%s</p>', __d(Inflector::underscore($this->plugin), Configure::read('Pagination.nothing_found_message')));
		return true;
	}

	if(!$hasPrev && !$hasNext) {
		echo sprintf('<p class="pagination low">%s</p>', __d(Inflector::underscore($this->plugin), 'No more posts'));
		return;
	}
?>
<div id="pagination">
	<?php
		if($hasNext) {
			echo $this->Paginator->prev(
				__d('blog', 'Newer'),
				array(
					'escape' => false,
					'tag' => 'span',
					'class' => 'round-all'
				),
				null,
				null
			);
		}

		if($hasPrev) {
			echo $this->Paginator->next(
				__d('blog', 'Older'),
				array(
					'escape' => false,
					'tag' => 'span',
					'class' => 'round-all'
				),
				null,
				null
			);
		}
	?>
</div>