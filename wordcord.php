<?php
/**
 * Sends post and page interactions to a designated bot user webhook.
 *
 * @package wp-update-discord-bot
 * @author soup-bowl <code@soupbowl.io>
 * @license MIT
 *
 * @wordpress-plugin
 * Plugin Name:       WordPress Update Discord Bot
 * Description:       Sends post and page interactions to a designated bot user webhook.
 * Plugin URI:        https://github.com/ReviveToday/WPUpdateDiscordBot
 * Version:           1.2
 * Author:            ReviveToday, soup-bowl
 * Author URI:        https://revive.today
 * License:           MIT
 */

/**
 * Autoloader.
 */
require_once __DIR__ . '/vendor/autoload.php';

use wpupdatediscordbot\Discord;
use wpupdatediscordbot\Settings;
use wpupdatediscordbot\Metabox;

/**
 * Fun stuff.
 */
class WordPressUpdateDiscordBot {
	/**
	 * Discord functions.
	 *
	 * @var Discord
	 */
	protected $discord;

	/**
	 * Settings API actions.
	 *
	 * @var Settings
	 */
	protected $settings;

	/**
	 * Metabox actions.
	 *
	 * @var Metabox
	 */
	protected $metabox;
	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->discord  = new Discord();
		$this->settings = new Settings();
		$this->metabox  = new Metabox();
	}

	/**
	 * The driver hooks a function by patching the system call table, so it's not safe to unload it unless another
	 * thread's about to jump in there and do its stuff, and you don't want to end up in the middle of invalid memory.
	 */
	public function hooks():void {
		$this->settings->hooks();
		$this->metabox->hooks();

		add_action( 'publish_post', array( &$this->discord, 'publish_handler' ), 10, 2 );
		add_action( 'publish_page', array( &$this->discord, 'publish_handler' ), 10, 2 );
	}
}

( new WordPressUpdateDiscordBot() )->hooks();
