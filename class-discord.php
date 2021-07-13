<?php
/**
 * Sends post and page interactions to a designated bot user webhook.
 *
 * @package wp-update-discord-bot
 * @author soup-bowl <code@soupbowl.io>
 * @license MIT
 */

namespace wpupdatediscordbot;

/**
 * Everything Discord API related.
 */
class Discord {
	/**
	 * Discord Webhook URL.
	 *
	 * @var string
	 */
	protected $webhook_url;

	/**
	 * Time allowed between updates, in seconds.
	 *
	 * @var int
	 */
	protected $timer;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->webhook_url = get_option( 'wpupdatediscordbot_hookurl' );
		$this->timer       = get_option( 'wpupdatediscordbot_timeout', 60 );
	}

	/**
	 * Sends an update to the specified Discord bot.
	 *
	 * @param string $message The message to send to Discord.
	 * @return bool Success status.
	 */
	public function update_discord( string $message ):bool {
		if ( empty( $this->webhook_url ) ) {
			return false;
		}

		if ( ! $this->timer_check() ) {
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
			$this->timer_store();
			return true;
		} else {
			return false;
		}
	}

		/**
		 * Checks the time with the stored timer, and gives a boolean response if a minute passed since last update.
		 *
		 * @return bool True if the timer check succeeds, false if not.
		 */
	private function timer_check():bool {
		$lu_time = get_option( 'wpupdatediscordbot_lastupdate', 0 ) + $this->timer;

		if ( time() > $lu_time ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Stores the current time.
	 *
	 * @return bool Success.
	 */
	private function timer_store():bool {
		update_option( 'wpupdatediscordbot_lastupdate', time() );

		return true;
	}
}
