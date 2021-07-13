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
 * Handles the visibility and setup with the WordPress Settings API.
 */
class Settings {
	/**
	 * The default timeout value if none is set.
	 *
	 * @var integer
	 */
	protected $default_timeout;

	/**
	 * The default Discord message.
	 *
	 * @var string
	 */
	protected $default_message;

	/**
	 * Registers the relevant WordPress hooks upon creation.
	 */
	public function hooks():void {
		$this->default_timeout = 60;
		$this->default_message = 'New entry or updates made to **{{post_title}}**.';

		add_action( 'admin_menu', array( &$this, 'add_admin_menu' ) );
		add_action( 'admin_init', array( &$this, 'settings_init' ) );
	}

	/**
	 * Intialises the options page.
	 */
	public function options_page():void {
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Discord Integration', 'wordcord' ); ?></h1>
			<form id='wpss-conf' action='options.php' method='post'>
			<?php
			settings_fields( 'wpupdatediscordbot' );
			do_settings_sections( 'wpupdatediscordbot' );
			submit_button();
			?>
			</form>
		</div>
		<?php
	}

	/**
	 * Registers the 'Mail' setting underneath 'Settings' in the admin GUI.
	 */
	public function add_admin_menu():void {
		add_options_page(
			__( 'Discord', 'wordcord' ),
			__( 'Discord', 'wordcord' ),
			'manage_options',
			'plummeted16ftthroughanannouncerstable',
			array( &$this, 'options_page' )
		);
	}

	/**
	 * Initialises the settings implementation.
	 */
	public function settings_init():void {
		register_setting( 'wpupdatediscordbot', 'wpupdatediscordbot_hookurl' );
		register_setting(
			'wpupdatediscordbot',
			'wpupdatediscordbot_timeout',
			array(
				'type'              => 'integer',
				'sanitize_callback' => 'intval',
				'default'           => $this->default_timeout,
			)
		);
		register_setting(
			'wpupdatediscordbot',
			'wpupdatediscordbot_message',
			array(
				'sanitize_callback' => 'esc_html',
				'default'           => $this->default_message,
			)
		);

		add_settings_section(
			'wpupdatediscordbot_section',
			__( 'Discord Settings', 'wordcord' ),
			function () {
				esc_html_e( 'Configure WordPress to communicate with your Discord.', 'wordcord' );
			},
			'wpupdatediscordbot'
		);

		add_settings_field(
			'wpupdatediscordbot_hookurl',
			__( 'Hook URL', 'wordcord' ),
			array( &$this, 'render_setting_hook' ),
			'wpupdatediscordbot',
			'wpupdatediscordbot_section'
		);

		add_settings_field(
			'wpupdatediscordbot_timeout',
			__( 'Post Timeout', 'wordcord' ),
			array( &$this, 'render_setting_timeout' ),
			'wpupdatediscordbot',
			'wpupdatediscordbot_section'
		);

		add_settings_field(
			'wpupdatediscordbot_message',
			__( 'Post Message', 'wordcord' ),
			array( &$this, 'render_setting_message' ),
			'wpupdatediscordbot',
			'wpupdatediscordbot_section'
		);
	}

	/**
	 * Writes the hook input box to the page.
	 */
	public function render_setting_hook():void {
		$opt = get_option( 'wpupdatediscordbot_hookurl' );
		?>
		<input class='regular-text ltr' type='text' name='wpupdatediscordbot_hookurl' value='<?php echo esc_attr( $opt ); ?>' placeholder='https://discord.com/api/webhooks/blahblah...'>
		<p class='description'><?php esc_html_e( 'The hook URL can be found in Discord at Server Settings > Integrations > Webhooks.', 'wordcord' ); ?></p>
		<?php
	}

	/**
	 * Writes the timeout input box to the page.
	 */
	public function render_setting_timeout():void {
		$opt = get_option( 'wpupdatediscordbot_timeout' );
		?>
		<input class='ltr' type='number' name='wpupdatediscordbot_timeout' value='<?php echo intval( $opt ); ?>'> <?php esc_html_e( 'seconds', 'wordcord' ); ?>
		<p class='description'><?php esc_html_e( 'If a post/page is published during this timeframe, no Discord post will happen', 'wordcord' ); ?></p>
		<?php
	}

	/**
	 * Writes the message input box to the page.
	 */
	public function render_setting_message():void {
		$opt = get_option( 'wpupdatediscordbot_message' );
		?>
		<textarea class="large-text" name="wpupdatediscordbot_message"><?php echo esc_attr( $opt ); ?></textarea>
		<p class='description'><?php esc_html_e( 'Accepted values are:', 'wordcord' ); ?> <strong>post_id</strong>, <strong>post_title</strong>, <strong>post_author</strong>, <strong>post_date</strong>, <strong>post_modified</strong>, <strong>post_content</strong>, <strong>post_excerpt</strong>.</p>
		<?php
	}
}
