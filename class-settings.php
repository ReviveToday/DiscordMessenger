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
	 * Registers the relevant WordPress hooks upon creation.
	 */
	public function hooks() {
		add_action( 'admin_menu', array( &$this, 'add_admin_menu' ) );
		add_action( 'admin_init', array( &$this, 'settings_init' ) );
	}

	/**
	 * Intialises the options page.
	 */
	public function options_page() {
		$this->render_settings();
	}

	/**
	 * Registers the 'Mail' setting underneath 'Settings' in the admin GUI.
	 */
	public function add_admin_menu() {
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
	public function settings_init() {
		register_setting( 'wpupdatediscordbot', 'wpupdatediscordbot_hookurl' );
		register_setting(
			'wpupdatediscordbot',
			'wpupdatediscordbot_timeout',
			array(
				'type'              => 'integer',
				'sanitize_callback' => 'intval',
				'default'           => 60,
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
			function () {
				$opt = get_option( 'wpupdatediscordbot_hookurl' );
				?>
				<input class='regular-text ltr' type='text' name='wpupdatediscordbot_hookurl' value='<?php echo esc_attr( $opt ); ?>' placeholder='https://discord.com/api/webhooks/blahblah...'>
				<?php
			},
			'wpupdatediscordbot',
			'wpupdatediscordbot_section'
		);

		add_settings_field(
			'wpupdatediscordbot_timeout',
			__( 'Post Timeout', 'wordcord' ),
			function () {
				$opt = get_option( 'wpupdatediscordbot_timeout' );
				?>
				<input class='ltr' type='number' name='wpupdatediscordbot_timeout' value='<?php echo intval( $opt ); ?>'> <?php esc_html_e( 'seconds', 'wordcord' ); ?>
				<p class='description'><?php esc_html_e( 'If a post/page is published during this timeframe, no Discord post will happen', 'wordcord' ); ?></p>
				<?php
			},
			'wpupdatediscordbot',
			'wpupdatediscordbot_section'
		);
	}

	/**
	 * Shows the configuration pane on the current page.
	 */
	private function render_settings() {
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
}
