<?php
/**
 * Registers metadata and related functions for the plugin.
 *
 * @package   CustomContentProfiles
 * @version   0.1.0
 * @since     0.1.0
 * @author    David Cavins
 * @copyright Copyright (c) 2014
 * @link      http://cares.missouri.edu/
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/* Register meta on the 'init' hook. */
add_action( 'init', 'ccsp_register_meta' );

/**
 * Registers custom metadata for the plugin.
 *
 * @since  0.1.0
 * @access public
 * @return void
 */
function ccsp_register_meta() {

	register_meta( 'profile', 'profile_name', 'ccsp_sanitize_meta' );
	register_meta( 'profile', 'profile_phone_number', 'ccsp_sanitize_meta' );
	register_meta( 'profile', 'profile_email_address', 'ccsp_sanitize_meta' );
	register_meta( 'profile', 'profile_job_title', 'ccsp_sanitize_meta' );
	register_meta( 'profile', 'profile_management_level', 'ccsp_sanitize_meta' );

}

/**
 * Callback function for sanitizing meta when add_metadata() or update_metadata() is called by WordPress. 
 * If a developer wants to set up a custom method for sanitizing the data, they should use the 
 * "sanitize_{$meta_type}_meta_{$meta_key}" filter hook to do so.
 *
 * @since  0.1.0
 * @access public
 * @param  mixed  $meta_value The value of the data to sanitize.
 * @param  string $meta_key   The meta key name.
 * @param  string $meta_type  The type of metadata (post, comment, user, etc.)
 * @return mixed  $meta_value
 */
function ccsp_sanitize_meta( $meta_value, $meta_key, $meta_type ) {

	if ( 'portfolio_item_url' === $meta_key )
		return esc_url( $meta_value );

	return strip_tags( $meta_value );
}

?>