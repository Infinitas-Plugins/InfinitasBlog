<?php
	$hasNewer = $this->Paginator->hasPrev();
	$hasOlder = $this->Paginator->hasNext();

	if (!($hasNewer && $hasOlder) && !$this->Paginator->request->params['paging'][$this->Paginator->defaultModel()]['current']) {
		echo sprintf('<p class="pagination empty">%s</p>', __d(Inflector::underscore($this->plugin), Configure::read('Pagination.nothing_found_message')));
		return true;
	}

	if(!$hasNewer && !$hasOlder) {
		echo sprintf('<p class="pagination low">%s</p>', __d(Inflector::underscore($this->plugin), 'No more posts'));
		return;
	}
?>
<div id="pagination">
	<?php
		if($hasNewer) {
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

		if($hasOlder) {
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