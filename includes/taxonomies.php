<?php
/**
 * File for registering custom taxonomies.
 *
 * @package   CustomContentProfiles
 * @version   0.1.0
 * @since     0.1.0
 * @author    David Cavins
 * @copyright Copyright (c) 2014
 * @link      http://cares.missouri.edu/
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/* Register taxonomies on the 'init' hook. */
// not sure we need a taxonomy for our staff profiles?
// add_action( 'init', 'ccsp_register_taxonomies' );

/**
 * Register taxonomies for the plugin.
 *
 * @since  0.1.0
 * @access public
 * @return void.
 */
function ccsp_register_taxonomies() {

	/* Get the plugin settings. */
	$settings = get_option( 'plugin_custom_content_portfolio', ccsp_get_default_settings() );

	/* Set up the arguments for the portfolio taxonomy. */
	$args = array(
		'public'            => true,
		'show_ui'           => true,
		'show_in_nav_menus' => true,
		'show_tagcloud'     => true,
		'show_admin_column' => true,
		'hierarchical'      => false,
		'query_var'         => 'profile',

		/* Only 2 caps are needed: 'manage_portfolio' and 'edit_portfolio_items'. */
		'capabilities' => array(
			'manage_terms' => 'manage_portfolio',
			'edit_terms'   => 'manage_portfolio',
			'delete_terms' => 'manage_portfolio',
			'assign_terms' => 'edit_portfolio_items',
		),

		/* The rewrite handles the URL structure. */
		'rewrite' => array(
			'slug'         => !empty( $settings['portfolio_base'] ) ? "{$settings['portfolio_root']}/{$settings['portfolio_base']}" : $settings['portfolio_root'],
			'with_front'   => false,
			'hierarchical' => false,
			'ep_mask'      => EP_NONE
		),

		/* Labels used when displaying taxonomy and terms. */
		'labels' => array(
			'name'                       => __( 'Portfolios',                           'custom-content-portfolio' ),
			'singular_name'              => __( 'Portfolio',                            'custom-content-portfolio' ),
			'menu_name'                  => __( 'Portfolios',                           'custom-content-portfolio' ),
			'name_admin_bar'             => __( 'Portfolio',                            'custom-content-portfolio' ),
			'search_items'               => __( 'Search Portfolios',                    'custom-content-portfolio' ),
			'popular_items'              => __( 'Popular Portfolios',                   'custom-content-portfolio' ),
			'all_items'                  => __( 'All Portfolios',                       'custom-content-portfolio' ),
			'edit_item'                  => __( 'Edit Portfolio',                       'custom-content-portfolio' ),
			'view_item'                  => __( 'View Portfolio',                       'custom-content-portfolio' ),
			'update_item'                => __( 'Update Portfolio',                     'custom-content-portfolio' ),
			'add_new_item'               => __( 'Add New Portfolio',                    'custom-content-portfolio' ),
			'new_item_name'              => __( 'New Portfolio Name',                   'custom-content-portfolio' ),
			'separate_items_with_commas' => __( 'Separate portfolios with commas',      'custom-content-portfolio' ),
			'add_or_remove_items'        => __( 'Add or remove portfolios',             'custom-content-portfolio' ),
			'choose_from_most_used'      => __( 'Choose from the most used portfolios', 'custom-content-portfolio' ),
		)
	);

	/* Register the 'portfolio' taxonomy. */
	register_taxonomy( 'portfolio', array( 'portfolio_item' ), $args );
}

?>