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
 * Version:           alpha
 * Author:            ReviveToday, soup-bowl
 * Author URI:        https://revive.today
 * License:           MIT
 */

/**
 * Fun stuff.
 */
class WordPressUpdateDiscordBot {
	/**
	 * Discord Webhook URL.
	 *
	 * @var string
	 */
	protected $webhook_url;

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action(
			'admin_init',
			function() {
				add_option( 'wpudb_webhook_url', '' );
			}
		);

		$this->webhook_url = get_option( 'wpudb_webhook_url' );
	}

	/**
	 * The driver hooks a function by patching the system call table, so it's not safe to unload it unless another
	 * thread's about to jump in there and do its stuff, and you don't want to end up in the middle of invalid memory.
	 */
	public function hooks():void {
		add_action( 'publish_post', array( &$this, 'publish_handler' ), 10, 2 );
		add_action( 'publish_page', array( &$this, 'publish_handler' ), 10, 2 );
	}

	/**
	 * Brings in the WordPress post/page object for hook usage.
	 *
	 * @param int     $post_id Post ID.
	 * @param WP_Post $post Post object.
	 */
	public function publish_handler( int $post_id, WP_Post $post ):void {
		$this->update_discord( "New entry or updates made to **{$post->post_title}**.\n" . get_post_permalink( $post_id ) );
	}

	/**
	 * Sends an update to the specified Discord bot.
	 *
	 * @param string $message The message to send to Discord.
	 * @return bool Success status.
	 */
	private function update_discord( string $message ):bool {
		if ( empty( $this->webhook_url ) ) {
			return false;
		}

		$response = wp_remote_post(
			$this->webhook_url,
			array(
				'body' => array(
					'content' => $message,
				),
			)
		);

		if ( ! is_wp_error( $response ) ) {
			return true;
		} else {
			return false;
		}
	}
}

( new WordPressUpdateDiscordBot() )->hooks();
