<?php

namespace hypeJunction\Apps;

/**
 * @property-read \ElggPlugin                        $plugin
 * @property-read \hypeJunction\Apps\Config          $config
 * @property-read \hypeJunction\Apps\HookHandlers    $hooks
 * @property-read \hypeJunction\Controllers\Actions  $actions
 * @property-read \hypeJunction\Services\Uploader    $uploader
 * @property-read \hypeJunction\Services\IconFactory $iconFactory
 * @property-read \hypeJunction\Data\Graph           $graph
 */
final class Plugin extends \hypeJunction\Plugin {

	/**
	 * Instance
	 * @var self
	 */
	static $instance;

	/**
	 * {@inheritdoc}
	 */
	protected function __construct(\ElggPlugin $plugin) {

		$this->setValue('plugin', $plugin);

		$this->setFactory('config', function (Plugin $p) {
			return new \hypeJunction\Apps\Config($p->plugin);
		});

		$this->setFactory('actions', function(Plugin $p) {
			return new \hypeJunction\Controllers\Actions(new \hypeJunction\Controllers\ActionResult());
		});

		$this->setFactory('uploader', function(Plugin $p) {
			return new \hypeJunction\Services\Uploader($p->config, $p->iconFactory);
		});

		$this->setFactory('iconFactory', function(Plugin $p) {
			return new \hypeJunction\Services\IconFactory($p->config);
		});

		$this->setFactory('graph', function(Plugin $p) {
			return new \hypeJunction\Data\Graph($p->config);
		});
	}

	/**
	 * {@inheritdoc}
	 */
	public static function factory() {
		if (null === self::$instance) {
			$plugin = elgg_get_plugin_from_id('hypeApps');
			self::$instance = new self($plugin);
		}
		return self::$instance;
	}

	/**
	 * {@inheritdoc}
	 */
	public function boot() {
		elgg_register_event_handler('init', 'system', array($this, 'init'));
	}

	/**
	 * 'init','system' callback
	 */
	public function init() {
		elgg_register_plugin_hook_handler('entity:icon:url', 'all', new Handlers\EntityIconUrlHook());

		elgg_register_plugin_hook_handler('graph:properties', 'all', new Handlers\PropertiesHook());

		elgg_register_plugin_hook_handler('graph:properties', 'user', new Handlers\UserPropertiesHook());
		elgg_register_plugin_hook_handler('graph:properties', 'group', new Handlers\GroupPropertiesHook());
		elgg_register_plugin_hook_handler('graph:properties', 'site', new Handlers\SitePropertiesHook());

		elgg_register_plugin_hook_handler('graph:properties', 'object', new Handlers\ObjectPropertiesHook());
		elgg_register_plugin_hook_handler('graph:properties', 'object:blog', new Handlers\BlogPropertiesHook());
		elgg_register_plugin_hook_handler('graph:properties', 'object:file', new Handlers\FilePropertiesHook());
		elgg_register_plugin_hook_handler('graph:properties', 'object:messages', new Handlers\MessagePropertiesHook());

		elgg_register_plugin_hook_handler('graph:properties', 'metadata', new Handlers\ExtenderPropertiesHook());
		elgg_register_plugin_hook_handler('graph:properties', 'annotation', new Handlers\ExtenderPropertiesHook());
		elgg_register_plugin_hook_handler('graph:properties', 'relationship', new Handlers\RelationshipPropertiesHook());
		elgg_register_plugin_hook_handler('graph:properties', 'river:item', new Handlers\RiverPropertiesHook());

	}
	

}
