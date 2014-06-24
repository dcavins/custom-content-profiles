<?php
/**
 * Plugin Name: Custom Content Profiles
 * Plugin URI: http://cares.missouri.edu/
 * Description: Staff profiles manager for WordPress.  This plugin allows you to manage, edit, and create new staff profiles.
 * Version: 0.1
 * Author: David Cavins, from work by Justin Tadlock
 *
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU 
 * General Public License version 2, as published by the Free Software Foundation.  You may NOT assume 
 * that you can use any other version of the GPL.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without 
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @package   CustomContentProfiles
 * @version   0.1.0
 * @since     0.1.0
 * @author    David Cavins
 * @copyright Copyright (c) 2014
 * @link      http://cares.missouri.edu/
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

class Custom_Content_Profiles {

	/**
	 * PHP5 constructor method.
	 *
	 * @since  0.1.0
	 * @access public
	 * @return void
	 */
	public function __construct() {

		/* Set the constants needed by the plugin. */
		add_action( 'plugins_loaded', array( &$this, 'constants' ), 1 );

		/* Internationalize the text strings used. */
		add_action( 'plugins_loaded', array( &$this, 'i18n' ), 2 );

		/* Load the functions files. */
		add_action( 'plugins_loaded', array( &$this, 'includes' ), 3 );

		/* Load the admin files. */
		add_action( 'plugins_loaded', array( &$this, 'admin' ), 4 );

		/* Register activation hook. */
		register_activation_hook( __FILE__, array( &$this, 'activation' ) );
	}

	/**
	 * Defines constants used by the plugin.
	 *
	 * @since  0.1.0
	 * @access public
	 * @return void
	 */
	public function constants() {

		/* Set constant path to the plugin directory. */
		define( 'CCSP_DIR', trailingslashit( plugin_dir_path( __FILE__ ) ) );

		/* Set the constant path to the plugin directory URI. */
		define( 'CCSP_URI', trailingslashit( plugin_dir_url( __FILE__ ) ) );

		/* Set the constant path to the includes directory. */
		define( 'CCSP_INCLUDES', CCSP_DIR . trailingslashit( 'includes' ) );

		/* Set the constant path to the admin directory. */
		define( 'CCSP_ADMIN', CCSP_DIR . trailingslashit( 'admin' ) );
	}

	/**
	 * Loads the initial files needed by the plugin.
	 *
	 * @since  0.1.0
	 * @access public
	 * @return void
	 */
	public function includes() {

		require_once( CCSP_INCLUDES . 'functions.php' );
		require_once( CCSP_INCLUDES . 'meta.php' );
		require_once( CCSP_INCLUDES . 'post-types.php' );
		require_once( CCSP_INCLUDES . 'taxonomies.php' );
	}

	/**
	 * Loads the translation files.
	 *
	 * @since  0.1.0
	 * @access public
	 * @return void
	 */
	public function i18n() {

		/* Load the translation of the plugin. */
		load_plugin_textdomain( 'custom-content-profiles', false, 'custom-content-profiles/languages' );
	}

	/**
	 * Loads the admin functions and files.
	 *
	 * @since  0.1.0
	 * @access public
	 * @return void
	 */
	public function admin() {

		if ( is_admin() )
			require_once( CCSP_ADMIN . 'admin.php' );
	}

	/**
	 * Method that runs only when the plugin is activated.
	 *
	 * @since  0.1.0
	 * @access public
	 * @return void
	 */
	function activation() {

		/* Get the administrator role. */
		$role =& get_role( 'administrator' );

		/* If the administrator role exists, add required capabilities for the plugin. */
		if ( !empty( $role ) ) {

			$role->add_cap( 'manage_profiles' );
			$role->add_cap( 'create_profiles' );
			$role->add_cap( 'edit_profiles' );
		}
	}
}

new Custom_Content_Profiles();

?>