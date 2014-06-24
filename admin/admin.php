<?php
/**
 * Admin functions for the plugin.
 *
 * @package   CustomContentProfiles
 * @version   0.1.0
 * @since     0.1.0
 * @author    David Cavins
 * @copyright Copyright (c) 2014
 * @link      http://cares.missouri.edu/
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/* Set up the admin functionality. */
add_action( 'admin_menu', 'ccsp_admin_setup' );

/**
 * Adds actions where needed for setting up the plugin's admin functionality.
 *
 * @since  0.1.0
 * @access public
 * @return void
 */
function ccsp_admin_setup() {

	// Waiting on @link http://core.trac.wordpress.org/ticket/9296
	//add_action( 'admin_init', 'ccsp_admin_setup' );

	/* Custom columns on the edit portfolio items screen. */
	add_filter( 'manage_edit-profile_columns', 'ccsp_edit_profile_columns' );
	add_action( 'manage_profile_posts_custom_column', 'ccsp_manage_profile_columns', 10, 2 );

	/* Add meta boxes an save metadata. */
	add_action( 'add_meta_boxes', 'ccsp_add_meta_boxes' );
	add_action( 'save_post', 'ccsp_profile_info_meta_box_save', 10, 2 );

	/* Add 32px screen icon. */
	add_action( 'admin_head', 'ccsp_admin_head_style' );
}

/**
 * Sets up custom columns on the portfolio items edit screen.
 *
 * @since  0.1.0
 * @access public
 * @param  array  $columns
 * @return array
 */
function ccsp_edit_profile_columns( $columns ) {

	unset( $columns['title'] );
	unset( $columns['taxonomy-portfolio'] );

	$new_columns = array(
		'cb' => '<input type="checkbox" />',
		'title' => __( 'Portfolio Item', 'custom-content-profiles' )
	);

	if ( current_theme_supports( 'post-thumbnails' ) )
		$new_columns['thumbnail'] = __( 'Thumbnail', 'custom-content-profiles' );

	$new_columns['taxonomy-portfolio'] = __( 'Portfolio', 'custom-content-profiles' );

	return array_merge( $new_columns, $columns );
}

/**
 * Displays the content of custom portfolio item columns on the edit screen.
 *
 * @since  0.1.0
 * @access public
 * @param  string  $column
 * @param  int     $post_id
 * @return void
 */
function ccsp_manage_profile_columns( $column, $post_id ) {
	global $post;

	switch( $column ) {

		case 'thumbnail' :

			if ( has_post_thumbnail() )
				the_post_thumbnail( array( 40, 40 ) );

			elseif ( function_exists( 'get_the_image' ) )
				get_the_image( array( 'image_scan' => true, 'width' => 40, 'height' => 40 ) );

			break;

		/* Just break out of the switch statement for everything else. */
		default :
			break;
	}
}

/**
 * Registers new meta boxes for the 'portfolio_item' post editing screen in the admin.
 *
 * @since  0.1.0
 * @access public
 * @param  string  $post_type
 * @return void
 */
function ccsp_add_meta_boxes( $post_type ) {

	if ( 'profile' === $post_type ) {

		add_meta_box( 
			'ccsp-profile-info', 
			__( 'Profile Info', 'custom-content-profiles' ), 
			'ccsp_profile_info_meta_box_display', 
			$post_type, 
			'side', 
			'core'
		);
	}
}

/**
 * Displays the content of the portfolio item info meta box.
 *
 * @since  0.1.0
 * @access public
 * @param  object  $post
 * @param  array   $metabox
 * @return void
 */
function ccsp_profile_info_meta_box_display( $post, $metabox ) {

	wp_nonce_field( basename( __FILE__ ), 'ccsp-profile-info-nonce' );
	// register_meta( 'profile', 'profile_phone_number', 'ccsp_sanitize_meta' );
	// register_meta( 'profile', 'profile_email_address', 'ccsp_sanitize_meta' );

	$management = get_post_meta( $post->ID, 'profile_management_level', true );
	// Set a default if none set.
	$management = ( $management ) ? (int) $management : 77;
	?>

	<p>
		<label for="ccsp-profile-job-title"><?php _e( 'Job Title', 'custom-content-profiles' ); ?></label>
		<br />
		<input type="text" name="ccsp-profile-job-title" id="ccsp-profile-job-title" value="<?php echo get_post_meta( $post->ID, 'profile_job_title', true ); ?>" size="30" tabindex="30" style="width: 99%;" />

		<label for="ccsp-management-radio-group">Directory Group</label>
		<ul id="ccsp-management-radio-group" class="radio">
			<li><input type="radio" name="ccsp-profile-management-level" id="ccsp-management-director" value="1" <?php checked( $management, 1 ); ?>> <label for="ccsp-management-director">Director</label></li>
			<li><input type="radio" name="ccsp-profile-management-level" id="ccsp-management-asst-director" value="5" <?php checked( $management, 5 ); ?>> <label for="ccsp-management-asst-director">Assistant Director</label></li>
			<li><input type="radio" name="ccsp-profile-management-level" id="ccsp-management-none" value="77" <?php checked( $management, 77 ); ?>> <label for="ccsp-management-none">Neither</label></li>
		</ul>

		<label for="ccsp-profile-phone-number"><?php _e( 'Phone Number', 'custom-content-profiles' ); ?></label>
		<br />
		<input type="text" name="ccsp-profile-phone-number" id="ccsp-profile-phone-number" value="<?php echo get_post_meta( $post->ID, 'profile_phone_number', true ); ?>" size="30" tabindex="30" style="width: 99%;" />

		<label for="ccsp-profile-email-address"><?php _e( 'Email Address', 'custom-content-profiles' ); ?></label>
		<br />
		<input type="text" name="ccsp-profile-email-address" id="ccsp-profile-email-address" value="<?php echo get_post_meta( $post->ID, 'profile_email_address', true ); ?>" size="30" tabindex="30" style="width: 99%;" />

		<label for="ccsp-profile-physical-address"><?php _e( 'Office Address', 'custom-content-profiles' ); ?></label>
		<br />
		<input type="text" name="ccsp-profile-physical-address" id="ccsp-profile-physical-address" value="<?php echo get_post_meta( $post->ID, 'profile_address', true ); ?>" size="30" tabindex="30" style="width: 99%;" />
	</p>
	<?php

	/* Allow devs to hook in their own stuff here. */
	do_action( 'ccsp_profile_info_meta_box', $post, $metabox );
}

/**
 * Saves the metadata for the portfolio item info meta box.
 *
 * @since  0.1.0
 * @access public
 * @param  int     $post_id
 * @param  object  $post
 * @return void
 */
function ccsp_profile_info_meta_box_save( $post_id, $post ) {

	if ( !isset( $_POST['ccsp-profile-info-nonce'] ) || !wp_verify_nonce( $_POST['ccsp-profile-info-nonce'], basename( __FILE__ ) ) )
		return;

	$meta = array(
		'profile_job_title' 	=> ( $_POST['ccsp-profile-job-title'] ),
		'profile_management_level' => ( $_POST['ccsp-profile-management-level'] ),
		'profile_email_address' => ( $_POST['ccsp-profile-email-address'] ),
		'profile_phone_number' 	=> ( $_POST['ccsp-profile-phone-number'] ),
		'profile_address' 	=> ( $_POST['ccsp-profile-physical-address'] ),


	);

	foreach ( $meta as $meta_key => $new_meta_value ) {

		/* Get the meta value of the custom field key. */
		$meta_value = get_post_meta( $post_id, $meta_key, true );

		/* If there is no new meta value but an old value exists, delete it. */
		if ( current_user_can( 'delete_post_meta', $post_id, $meta_key ) && '' == $new_meta_value && $meta_value )
			delete_post_meta( $post_id, $meta_key, $meta_value );

		/* If a new meta value was added and there was no previous value, add it. */
		elseif ( current_user_can( 'add_post_meta', $post_id, $meta_key ) && $new_meta_value && '' == $meta_value )
			add_post_meta( $post_id, $meta_key, $new_meta_value, true );

		/* If the new meta value does not match the old value, update it. */
		elseif ( current_user_can( 'edit_post_meta', $post_id, $meta_key ) && $new_meta_value && $new_meta_value != $meta_value )
			update_post_meta( $post_id, $meta_key, $new_meta_value );
	}
}

/**
 * Adds plugin settings.  At the moment, this function isn't being used because we're waiting for a bug fix
 * in core.  For more information, see: http://core.trac.wordpress.org/ticket/9296
 *
 * @since  0.1.0
 * @access public
 * @return void
 */
function ccsp_plugin_settings() {

	/* Register settings for the 'permalink' screen in the admin. */
	register_setting(
		'permalink',
		'plugin_custom_content_profiles',
		'ccsp_validate_settings'
	);

	/* Adds a new settings section to the 'permalink' screen. */
	add_settings_section(
		'ccsp-permalink',
		__( 'Profile Settings', 'custom-content-profiles' ),
		'ccsp_permalink_section',
		'permalink'
	);

	/* Get the plugin settings. */
	$settings = get_option( 'plugin_ccsp', ccsp_get_default_settings() );

	add_settings_field(
		'ccsp-root',
		__( 'Profile archive', 'custom-content-profiles' ),
		'ccsp_root_field',
		'permalink',
		'ccsp-permalink',
		$settings
	);
	// add_settings_field(
	// 	'ccsp-base',
	// 	__( 'Portfolio taxonomy slug', 'custom-content-profiles' ),
	// 	'ccp_base_field',
	// 	'permalink',
	// 	'ccp-permalink',
	// 	$settings
	// );
	add_settings_field(
		'ccsp-profile-base',
		__( 'Profile item slug', 'custom-content-profiles' ),
		'ccsp_item_base_field',
		'permalink',
		'ccsp-permalink',
		$settings
	);
}

/**
 * Validates the plugin settings.
 *
 * @since  0.1.0
 * @access public
 * @param  array  $settings
 * @return array
 */
function ccsp_validate_settings( $settings ) {

	// @todo Sanitize for alphanumeric characters
	// @todo Both the portfolio_base and portfolio_item_base can't match.

	$settings['profiles_base'] = $settings['profiles_base'];

	$settings['profile_item_base'] = $settings['profile_item_base'];

	$settings['profile_root'] = !empty( $settings['profile_root'] ) ? $settings['profile_root'] : 'profile';

	return $settings;
}

/**
 * Adds the portfolio permalink section.
 *
 * @since  0.1.0
 * @access public
 * @return void
 */
function ccsp_permalink_section() { ?>
	<table class="form-table">
		<?php do_settings_fields( 'permalink', 'custom-content-profiles' ); ?>
	</table>
<?php }

/**
 * Adds the portfolio root settings field.
 *
 * @since  0.1.0
 * @access public
 * @return void
 */
function ccsp_root_field( $settings ) { ?>
	<input type="text" name="plugin_ccsp[profiles-root]" id="ccsp-profiles-root" class="regular-text code" value="<?php echo esc_attr( $settings['profiles-root'] ); ?>" />
	<code><?php echo home_url( $settings['profiles-root'] ); ?></code> 
<?php }

/**
 * Adds the portfolio (taxonomy) base settings field.
 *
 * @since  0.1.0
 * @access public
 * @return void
 */
function ccsp_base_field( $settings ) { ?>
	<input type="text" name="plugin_ccsp[profile_base]" id="ccsp-profile-base" class="regular-text code" value="<?php echo esc_attr( $settings['profile_base'] ); ?>" />
	<code><?php echo trailingslashit( home_url( "{$settings['profile_root']}/{$settings['profile_base']}" ) ); ?>%profile%</code> 
<?php }

/**
 * Adds the portfolio item (post type) base settings field.
 *
 * @since  0.1.0
 * @access public
 * @return void
 */
function ccsp_item_base_field( $settings ) { ?>
	<input type="text" name="plugin_ccsp[profile_item_base]" id="ccsp-profile-item-base" class="regular-text code" value="<?php echo esc_attr( $settings['profile_item_base'] ); ?>" />
	<code><?php echo trailingslashit( home_url( "{$settings['profile_root']}/{$settings['profile_item_base']}" ) ); ?>%postname%</code> 
<?php }

/**
 * Overwrites the screen icon for portfolio screens in the admin.
 *
 * @since  0.1.0
 * @access public
 * @return void
 */
function ccsp_admin_head_style() {
        global $post_type;

	if ( 'portfolio_item' === $post_type ) { ?>
		<style type="text/css">
			#icon-edit.icon32-posts-portfolio_item {
				background: transparent url( '<?php echo CCSP_URI . 'images/screen-icon.png'; ?>' ) no-repeat;
			}
		</style>
	<?php }
}

?>