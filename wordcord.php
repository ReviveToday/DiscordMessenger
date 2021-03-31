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

		$payload = json_encode(
			array(
				'content' => $message,
			)
		);

		$curlybob = curl_init( $this->webhook_url );
		curl_setopt( $curlybob, CURLOPT_HTTPHEADER, array( 'Content-type: application/json' ) );
		curl_setopt( $curlybob, CURLOPT_POST, 1 );
		curl_setopt( $curlybob, CURLOPT_POSTFIELDS, $payload );
		curl_setopt( $curlybob, CURLOPT_FOLLOWLOCATION, 1 );
		curl_setopt( $curlybob, CURLOPT_HEADER, 0 );
		curl_setopt( $curlybob, CURLOPT_RETURNTRANSFER, 1 );

		$response = curl_exec( $curlybob );
		//error_log( var_export( $response, true ) );
		curl_close( $curlybob );

		return true;
	}
}

( new WordPressUpdateDiscordBot() )->hooks();
