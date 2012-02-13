<?php
class R4e27221a05c449569c611b306318cd70 extends CakeRelease {

/**
 * Migration description
 *
 * @var string
 * @access public
 */
	public $description = 'Migration for Blog version 0.2';

/**
 * Plugin name
 *
 * @var string
 * @access public
 */
	public $plugin = 'Blog';

/**
 * Actions to be performed
 *
 * @var array $migration
 * @access public
 */
	public $migration = array(
		'up' => array(
			'alter_field' => array(
				'posts' => array(
					'parent_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'key' => 'index'),
				),
			),
			'create_field' => array(
				'posts' => array(
					'indexes' => array(
						'parent' => array('column' => 'parent_id', 'unique' => 0),
					),
				),
			),
		),
		'down' => array(
			'alter_field' => array(
				'posts' => array(
					'parent_id' => array('type' => 'integer', 'null' => true, 'default' => '0'),
				),
			),
			'drop_field' => array(
				'posts' => array('', 'indexes' => array('parent')),
			),
		),
	);

	
/**
 * Before migration callback
 *
 * @param string $direction, up or down direction of migration process
 * @return boolean Should process continue
 * @access public
 */
	public function before($direction) {
		return true;
	}

/**
 * After migration callback
 *
 * @param string $direction, up or down direction of migration process
 * @return boolean Should process continue
 * @access public
 */
	public function after($direction) {
		return true;
	}
}
?>