<?php

/*
  Plugin Name: Confirm Publish
  Plugin URI:vagra
  Description: Simple plugin that adds confirm prompts for posts and pages when a page is published or updated.
  Author: Colin Mitchell
  Author URI: http://colinmitchellweb.com/
  Text Domain: confirm-publish
  Version: 1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


if ( ! class_exists( 'Confirm_Publish' ) ) :


	final class Confirm_Publish {

		/**
		 * Current version number
		 *
		 * @var   string
		 * @since 1.0
		 */
		const VERSION = '1.0';

		/**
		 * Localized strings for confirm modals
		 *
		 * @var array
		 * @since 1.0
		 */
		public $strings = array();

		/**
		 * Confirm_Publish constructor.
		 */
		public function __construct() {

			// Plugin Folder Path
			if ( ! defined( 'CONFIRM_PUBLISH_PLUGIN_DIR' ) ) {
				define( 'CONFIRM_PUBLISH_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
			}

			// Plugin Folder URL
			if ( ! defined( 'CONFIRM_PUBLISH_PLUGIN_URL' ) ) {
				define( 'CONFIRM_PUBLISH_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
			}

			// Plugin Root File
			if ( ! defined( 'CONFIRM_PUBLISH_PLUGIN_FILE' ) ) {
				define( 'CONFIRM_PUBLISH_PLUGIN_FILE', __FILE__ );
			}

			// Plugin Text Domain
			if ( ! defined( 'CONFIRM_PUBLISH_PLUGIN_FILE' ) ) {
				define( 'CONFIRM_PUBLISH_TEXT_DOMAIN', 'confirm-publish' );
			}

			// @todo - Generate i18n localization .pot file
			// Load plugin localization
			// add_action( 'plugins_loaded', array( $this, 'load_plugin_textdomain' ) );

			// Admin Hooks
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

			// Set strings
			$this->set_strings();

		}

		/**
		 * Enqueue Scripts for plugin
		 *
		 * @param string $hook current admin page hook
		 */
		public function enqueue_scripts( $hook ) {
			global $post;

			// Get current admin screen
			$screen = get_current_screen();

			// Admin pages to hook into
			$admin_pages = array(
				'post.php',
				'post-new.php'
			);

			// If the hook is part of the $admin_pages array, enqueue scripts.
			if( in_array( $hook, $admin_pages) ) {

				// Register the javascript
				wp_register_script( 'confirm-publish-admin-script', CONFIRM_PUBLISH_PLUGIN_URL . 'assets/js/admin-scripts.js', array( 'jquery' ), self::VERSION, true );

				// Localize the script by passing screen, current post, and strings variables.
				wp_localize_script( 'confirm-publish-admin-script', 'confirmPublish', array(
					'screen' => $screen,
					'current_post' => $post,
					'strings' => apply_filters( 'confirm_publish_strings', $this->format_strings( $post ) )
				));

				// Enqueue script
				wp_enqueue_script( 'confirm-publish-admin-script' );

			}

		}

		/**
		 * Format strings with the post details
		 *
		 * @param object $post
		 *
		 * @return array formatted strings
		 */
		private function format_strings( $post ) {

			// Get the post title
			$post_title = !empty( $post->post_title ) ? ' "' . $post->post_title . '"' : "";

			// Loop through the strings for apply the post type and title to them.
			foreach( $this->strings as $key => $string ) {
				$string = sprintf( $string, $post->post_type, $post_title );
				$this->strings[ $key ] = $string;
			}

			return $this->strings;
		}

		private function set_strings() {
			$this->strings = array(
				'publish' => __( 'You are about to make the %1$s%2$s public. Are you sure you want to publish?', 'confirm-publish' ),
				'update' => __( 'You are about to make updates to a live %1$s. Are you sure you want to update?', 'confirm-publish' )
			);
		}

		public function get_strings() {
			return $this->strings;
		}

		/**
		 * Load the plugin text domain for translation.
		 *
		 * @since    1.0.0
		 */
		public function load_plugin_textdomain() {

			load_plugin_textdomain(
				CONFIRM_PUBLISH_TEXT_DOMAIN,
				false,
				CONFIRM_PUBLISH_PLUGIN_DIR . '/languages/'
			);

		}
	}

endif;

$confirm_publish = new Confirm_Publish();