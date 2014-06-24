<?php
/**
 * Various functions, filters, and actions used by the plugin.
 *
 * @package   CustomContentProfiles
 * @version   0.1.0
 * @since     0.1.0
 * @author    David Cavins
 * @copyright Copyright (c) 2014
 * @link      http://cares.missouri.edu/
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/* Filter the post type archive title. */
add_filter( 'post_type_archive_title', 'ccsp_post_type_archive_title' );

/* Filter the post type permalink. */
// add_filter( 'post_type_link', 'ccsp_post_type_link', 10, 2 );

/* Filter the breadcrumb trail items (Breadcrumb Trail script/plugin). */
// add_filter( 'breadcrumb_trail_items', 'ccsp_breadcrumb_trail_items' );

/**
 * Returns the default settings for the plugin.
 *
 * @since  0.1.0
 * @access public
 * @return array
 */
function ccsp_get_default_settings() {

	$settings = array(
		'profiles_root'      => 'profiles',
		'profile_base'      => 'about',          // defaults to 'portfolio_root'
		'profile_item_base' => '%profile%'
	);

	return $settings;
}

/**
 * Filter on 'post_type_archive_title' to allow for the use of the 'archive_title' label that isn't supported 
 * by WordPress.  That's okay since we can roll our own labels.
 *
 * @since  0.1.0
 * @access public
 * @param  string $title
 * @return string
 */
function ccsp_post_type_archive_title( $title ) {

	if ( is_post_type_archive( 'profile' ) ) {
		$post_type = get_post_type_object( 'profile' );
		$title = isset( $post_type->labels->archive_title ) ? $post_type->labels->archive_title : $title;
	}

	return $title;
}

/**
 * Filter on 'post_type_link' to allow users to use '%profile%' (the 'portfolio' taxonomy) in their 
 * portfolio item URLs.
 *
 * @since  0.1.0
 * @access public
 * @param  string $post_link
 * @param  object $post
 * @return string
 */
function ccsp_post_type_link( $post_link, $post ) {

	if ( 'profile' !== $post->post_type )
		return $post_link;

	/* Allow %portfolio% in the custom post type permalink. */
	if ( false !== strpos( $post_link, '%portfolio%' ) ) {
	
		/* Get the terms. */
		$terms = get_the_terms( $post, 'portfolio' ); // @todo apply filters to tax name.

		/* Check that terms were returned. */
		if ( $terms ) {

			usort( $terms, '_usort_terms_by_ID' );

			$post_link = str_replace( '%portfolio%', $terms[0]->slug, $post_link );

		} else {
			$post_link = str_replace( '%portfolio%', 'item', $post_link );
		}
	}

	return $post_link;
}

/**
 * Filters the 'breadcrumb_trail_items' hook from the Breadcrumb Trail plugin and the script version 
 * included in the Hybrid Core framework.  At best, this is a neat hack to add the portfolio to the 
 * single view of portfolio items based off the '%portfolio%' rewrite tag.  At worst, it's potentially 
 * a huge management nightmare in the long term.  A better solution is definitely needed baked right 
 * into Breadcrumb Trail itself that takes advantage of its built-in features for figuring out this type 
 * of thing.
 *
 * @since  0.1.0
 * @access public
 * @param  array  $items
 * @return array
 */
function ccsp_breadcrumb_trail_items( $items ) {

	if ( is_singular( 'portfolio_item' ) ) {

		$settings = get_option( 'plugin_custom_content_portfolio', ccp_get_default_settings() );

		if ( false !== strpos( $settings['portfolio_item_base'], '%portfolio%' ) ) {
			$post_id = get_queried_object_id();

			$terms = get_the_terms( $post_id, 'portfolio' );

			if ( !empty( $terms ) ) {

				usort( $terms, '_usort_terms_by_ID' );
				$term = get_term( $terms[0], 'portfolio' );
				$term_id = $term->term_id;

				$parents = array();

				while ( $term_id ) {

					/* Get the parent term. */
					$term = get_term( $term_id, 'portfolio' );

					/* Add the formatted term link to the array of parent terms. */
					$parents[] = '<a href="' . get_term_link( $term, 'portfolio' ) . '" title="' . esc_attr( $term->name ) . '">' . $term->name . '</a>';

					/* Set the parent term's parent as the parent ID. */
					$term_id = $term->parent;
				}

				$items   = array_splice( $items, 0, -1 );
				$items   = array_merge( $items, array_reverse( $parents ) );
				$items[] = single_post_title( '', false );
			}
		}
	}

	return $items;
}

/**
 * Add image sizes for profile display.
 */
function ccsp_add_thumbnail_sizes() {
	if ( function_exists( 'add_image_size' ) ) { 
		add_image_size( 'profile-small', 120, 160, true );
		add_image_size( 'profile-large', 300, 300, true );
	}
}
add_action( 'after_setup_theme', 'ccsp_add_thumbnail_sizes' );

// TEMPLATE TAGS
/**
 * Prints HTML with meta information for the project client.
 */
function cares_profile_job_title( $post_id = null ) {
	$post_id = ( $post_id ) ? $post_id : get_the_ID();

	if ( $job_title = get_post_meta(  $post_id, 'profile_job_title', true ) )
		echo apply_filters( 'the_content', $job_title );
}
function cares_profile_phone_number( $post_id = null ) {
	$post_id = ( $post_id ) ? $post_id : get_the_ID();

	if ( $phone = get_post_meta(  $post_id, 'profile_phone_number', true ) )
		echo apply_filters( 'the_content', $phone );
}
function cares_profile_email_address( $post_id = null ) {
	$post_id = ( $post_id ) ? $post_id : get_the_ID();

	if ( $email = get_post_meta(  $post_id, 'profile_email_address', true ) )
		echo apply_filters( 'the_content', '<a href="mailto:' . $email . '">' . $email . '</a>' );
}
function cares_profile_physical_address( $post_id = null ) {
	$post_id = ( $post_id ) ? $post_id : get_the_ID();

	if ( $address = get_post_meta(  $post_id, 'profile_address', true ) )
		echo apply_filters( 'the_content', $address );
}

if ( ! function_exists( 'cares_responsive_thumbnail' ) ) :

/**
 * Prints HTML to create thumbnail compliant with picturefill library.
 */
function cares_responsive_profile_thumbnail( $columns = 1 ) {
	if ( ! $post_id = get_the_ID() )
		return false;

	// Breakpoints in my theme:
	// 1 columns: max 1024px wide
	// 2 columns: default one col, max 544px wide; 37em to two, max 442px wide
	// 3 columns: default one col, max 735px wide; 50em to three, max 289px wide
	// 4 columns: default one col, max 544px wide; 37em to two, max 442px wide; 64em to four, 206px wide
	// "srcset" will always be the same, since it simply tells the browser what options are available.
	// "sizes" will change based on number of columns, that's where we warn the browser about our breakpoints
	// the unit "vw" is basically equal to % viewport width. Plus, who doesn't like VWs?

	// Get the thumbnail image urls and alt data
	$thumb_id 		= get_post_thumbnail_id( $post_id );
	$m_image_url 	= wp_get_attachment_image_src( $thumb_id, 'profile-large');
	$s_image_url 	= wp_get_attachment_image_src( $thumb_id, 'profile-small');

	$alt_text = get_post_meta( $thumb_id, '_wp_attachment_image_alt', true );
	// Lazy people leave the alt data empty, so we'll fill it in with the post title, if necessary
	$alt_text = $alt_text ? $alt_text : get_the_title( $post_id );

	switch ( $columns ) {
		case '4':
			$breakpoints = '(min-width: 37em) 50vw,
							(min-width: 64em) 25vw,
							100vw';
			break;
		case '3':
			$breakpoints = '(min-width: 50em) 33.3vw, 
							100vw';
			break;
		case '2':
			$breakpoints = '(min-width: 37em) 50vw, 
							100vw';
			break;
		case '1':
		default:
			$breakpoints = '100vw';
			break;
	}


	?>
	<img src="<?php echo $xs_image_url[0]; ?>"
     srcset="<?php echo $m_image_url[0]; ?> 240w,
             <?php echo $s_image_url[0]; ?> 120w"
     sizes="<?php echo $breakpoints; ?>"
     alt="<?php echo $alt_text; ?>" />
     <?php
}
endif;

/**
 * Reorder the results on the Profiles page by administrative importance.
 * Posts have a postmeta profile_management_level that ranges from 1 "director" to 77 "footsoldier"
 */
function modify_profile_archive_main_loop( $query ) {

    if( is_post_type_archive( 'profile' ) && !is_admin() && $query->is_main_query() ) {   	
        $query->set('posts_per_page', -1); // Show them all
        $query->set('orderby', 'meta_value'); 
		$query->set('order', 'ASC');
		$query->set('meta_key', 'profile_management_level'); 
    }
}
add_filter('pre_get_posts', 'modify_profile_archive_main_loop', 45);
