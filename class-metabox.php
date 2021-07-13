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
 * Handles control inputs found on Gutenberg & Classic Editor.
 */
class Metabox {
	/**
	 * Hooks into the WordPress system.
	 *
	 * @return void Runs add_action to hook into WP.
	 */
	public function hooks():void {
		add_action( 'add_meta_boxes', array( &$this, 'form_setup' ) );
	}

	/**
	 * Adds a box in editor view to enable custom settings.
	 *
	 * @return void Adds meta boxes into WP.
	 */
	public function form_setup():void {
		add_meta_box(
			'wordcordsettings',
			__( 'Post to Discord', 'wordcord' ),
			function( $post ) {
				?>
				<input type="hidden" name="wordcord_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wordcord_nonce' ) ); ?>">
				<div>
					<input type="checkbox" name="wordcord_postit" value="1" checked />
					<label for="wordcord_postit"><?php esc_html_e( 'Post to Discord', 'wordcord' ); ?></label>
				</div>
				<?php
			},
			array( 'post', 'page' ),
			'side',
			'low'
		);
	}
}
