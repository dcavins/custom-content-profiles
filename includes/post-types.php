<?php
/**
 * File for registering custom post types.
 *
 * @package   CustomContentProfiles
 * @version   0.1.0
 * @since     0.1.0
 * @author    David Cavins
 * @copyright Copyright (c) 2014
 * @link      http://cares.missouri.edu/
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/* Register custom post types on the 'init' hook. */
add_action( 'init', 'ccsp_register_post_types' );

/**
 * Registers post types needed by the plugin.
 *
 * @since  0.1.0
 * @access public
 * @return void
 */
function ccsp_register_post_types() {

	/* Get the plugin settings. */
	$settings = get_option( 'plugin_custom_content_profiles', ccsp_get_default_settings() );

	/* Set up the arguments for the profile post type. */
	$args = array(
		'description'         => '',
		'public'              => true,
		'publicly_queryable'  => true,
		'show_in_nav_menus'   => false,
		'show_in_admin_bar'   => true,
		'exclude_from_search' => false,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 12,
		'menu_icon'           => CCSP_URI . 'images/menu-icon.png',
		'can_export'          => true,
		'delete_with_user'    => false,
		'hierarchical'        => false,
		'has_archive'         => $settings['profiles_root'],
		'query_var'           => 'profile',
		'capability_type'     => 'profile',
		'map_meta_cap'        => true,

		/* Only 3 caps are needed: 'manage_profiles', 'create_profiles', and 'edit_profiles'. */
		'capabilities' => array(

			// meta caps (don't assign these to roles)
			'edit_post'              => 'edit_profile',
			'read_post'              => 'read_profile',
			'delete_post'            => 'delete_profile',

			// primitive/meta caps
			'create_posts'           => 'create_profiles',

			// primitive caps used outside of map_meta_cap()
			'edit_posts'             => 'edit_profiles',
			'edit_others_posts'      => 'manage_profiles',
			'publish_posts'          => 'manage_profiles',
			'read_private_posts'     => 'read',

			// primitive caps used inside of map_meta_cap()
			'read'                   => 'read',
			'delete_posts'           => 'manage_profiles',
			'delete_private_posts'   => 'manage_profiles',
			'delete_published_posts' => 'manage_profiles',
			'delete_others_posts'    => 'manage_profiles',
			'edit_private_posts'     => 'edit_profiles',
			'edit_published_posts'   => 'edit_profiles'
		),

		/* The rewrite handles the URL structure. */
		'rewrite' => array(
			'slug'       => !empty( $settings['profiles_base'] ) ? "{$settings['profiles_root']}/{$settings['profiles_base']}" : $settings['profiles_root'],
			'with_front' => false,
			'pages'      => true,
			'feeds'      => true,
			'ep_mask'    => EP_PERMALINK,
		),

		/* What features the post type supports. */
		'supports' => array(
			'title',
			'editor',
			'excerpt',
			'author',
			'thumbnail'
		),

		/* Labels used when displaying the posts. */
		'labels' => array(
			'name'               => __( 'Profiles',                   'custom-content-profile' ),
			'singular_name'      => __( 'Profile',                    'custom-content-profile' ),
			'menu_name'          => __( 'Profiles',                    'custom-content-profile' ),
			'name_admin_bar'     => __( 'profile',                    'custom-content-profile' ),
			'add_new'            => __( 'Add New',                    'custom-content-profile' ),
			'add_new_item'       => __( 'Add New Profile',            'custom-content-profile' ),
			'edit_item'          => __( 'Edit Profile',               'custom-content-profile' ),
			'new_item'           => __( 'New Profile',                'custom-content-profile' ),
			'view_item'          => __( 'View Profile',               'custom-content-profile' ),
			'search_items'       => __( 'Search Profiles',            'custom-content-profile' ),
			'not_found'          => __( 'No profiles found',          'custom-content-profile' ),
			'not_found_in_trash' => __( 'No profiles found in trash', 'custom-content-profile' ),
			'all_items'          => __( 'Profiles',                   'custom-content-profile' ),

			// Custom labels b/c WordPress doesn't have anything to handle this.
			'archive_title'      => __( 'Profiles',                   'custom-content-profile' ),
		)
	);

	/* Register the profile post type. */
	register_post_type( 'profile', $args );
}

?>