<?php
/**
 * Plugin Name: Web Stories Templates Studio
 * Plugin URI: https://wordpress.org/plugins/web-stories-templates-studio/
 * Description: Sick, modern, high-converting templates for Google Web Stories. Easy to adjust, fully compliant with Google Web Stories dimensions, safe zones, and AMP specifications.
 * Version: 1.3.6
 * Author: pennion
 * Author URI: https://pennion.com
 * License: GPLv2 or later
 * Text Domain: web-stories-templates-studio
 * Domain Path: /languages
 * Requires at least: 5.8
 * Requires PHP: 7.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'WSTS_VERSION', '1.3.6' );
define( 'WSTS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'WSTS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'WSTS_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

// Autoload includes
require_once WSTS_PLUGIN_DIR . 'includes/class-spec-validator.php';
require_once WSTS_PLUGIN_DIR . 'includes/class-template-registry.php';
require_once WSTS_PLUGIN_DIR . 'includes/class-story-builder.php';
require_once WSTS_PLUGIN_DIR . 'includes/class-admin-studio.php';
require_once WSTS_PLUGIN_DIR . 'includes/class-ajax-handler.php';
require_once WSTS_PLUGIN_DIR . 'includes/class-story-player.php';

/**
 * Main Plugin Class
 */
final class Web_Stories_Templates_Studio {

	/**
	 * Single instance
	 *
	 * @var Web_Stories_Templates_Studio
	 */
	private static $instance = null;

	/**
	 * Main instance accessor
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor
	 */
	private function __construct() {
		$this->init_hooks();
	}

	/**
	 * Initialize hooks
	 */
	private function init_hooks() {
		add_action( 'plugins_loaded', array( $this, 'on_plugins_loaded' ) );
		add_action( 'init', array( $this, 'register_web_story_cpt_safely' ), 20 );
		register_activation_hook( __FILE__, array( $this, 'on_activation' ) );
		register_deactivation_hook( __FILE__, array( $this, 'on_deactivation' ) );
	}

	/**
	 * Runs on plugins_loaded
	 */
	public function on_plugins_loaded() {
		// Initialize Core Components
		WSTS_Template_Registry::instance();
		WSTS_Admin_Studio::instance();
		WSTS_Ajax_Handler::instance();
		WSTS_Story_Player::instance();

		// Add custom action links
		add_filter( 'plugin_action_links_' . WSTS_PLUGIN_BASENAME, array( $this, 'add_action_links' ) );
	}

	/**
	 * Register web-story custom post type if not already registered
	 * Ensures complete harmony whether Google Web Stories plugin is installed or not.
	 */
	public function register_web_story_cpt_safely() {
		// Register official web_story_category taxonomy if Google Web Stories plugin is not active
		if ( ! taxonomy_exists( 'web_story_category' ) ) {
			register_taxonomy(
				'web_story_category',
				array( 'web-story' ),
				array(
					'labels'            => array(
						'name'          => __( 'Story Categories', 'web-stories-templates-studio' ),
						'singular_name' => __( 'Story Category', 'web-stories-templates-studio' ),
						'search_items'  => __( 'Search Categories', 'web-stories-templates-studio' ),
						'all_items'     => __( 'All Categories', 'web-stories-templates-studio' ),
						'edit_item'     => __( 'Edit Category', 'web-stories-templates-studio' ),
						'update_item'   => __( 'Update Category', 'web-stories-templates-studio' ),
						'add_new_item'  => __( 'Add New Category', 'web-stories-templates-studio' ),
						'new_item_name' => __( 'New Category Name', 'web-stories-templates-studio' ),
						'menu_name'     => __( 'Categories', 'web-stories-templates-studio' ),
					),
					'hierarchical'      => true,
					'show_ui'           => true,
					'show_in_menu'      => true,
					'show_admin_column' => true,
					'query_var'         => true,
					'rewrite'           => array( 'slug' => 'web-story-category' ),
					'show_in_rest'      => true,
				)
			);
		}

		if ( ! post_type_exists( 'web-story' ) ) {
			register_post_type(
				'web-story',
				array(
					'labels'              => array(
						'name'               => _x( 'Stories', 'post type general name', 'web-stories-templates-studio' ),
						'singular_name'      => _x( 'Story', 'post type singular name', 'web-stories-templates-studio' ),
						'menu_name'          => _x( 'Stories', 'admin menu', 'web-stories-templates-studio' ),
						'name_admin_bar'     => _x( 'Story', 'add new on admin bar', 'web-stories-templates-studio' ),
						'add_new'            => _x( 'Add New', 'story', 'web-stories-templates-studio' ),
						'add_new_item'       => __( 'Add New Story', 'web-stories-templates-studio' ),
						'new_item'           => __( 'New Story', 'web-stories-templates-studio' ),
						'edit_item'          => __( 'Edit Story', 'web-stories-templates-studio' ),
						'view_item'          => __( 'View Story', 'web-stories-templates-studio' ),
						'all_items'          => __( 'All Stories', 'web-stories-templates-studio' ),
						'search_items'       => __( 'Search Stories', 'web-stories-templates-studio' ),
						'not_found'          => __( 'No stories found.', 'web-stories-templates-studio' ),
						'not_found_in_trash' => __( 'No stories found in Trash.', 'web-stories-templates-studio' ),
					),
					'public'              => true,
					'publicly_queryable'  => true,
					'show_ui'             => true,
					'show_in_menu'        => true,
					'query_var'           => true,
					'rewrite'             => array(
						'slug'       => 'web-stories',
						'with_front' => false,
					),
					'capability_type'     => 'post',
					'map_meta_cap'        => true,
					'has_archive'         => true,
					'hierarchical'        => false,
					'menu_position'       => 25,
					'menu_icon'           => 'dashicons-format-gallery',
					'supports'            => array( 'title', 'editor', 'thumbnail', 'author', 'excerpt', 'custom-fields' ),
					'taxonomies'          => array( 'web_story_category', 'category', 'post_tag' ),
					'show_in_rest'        => true,
				)
			);
		} else {
			register_taxonomy_for_object_type( 'web_story_category', 'web-story' );
			register_taxonomy_for_object_type( 'category', 'web-story' );
		}

		// Ensure administrators and editors have complete permissions to create, edit, and publish web-stories
		if ( function_exists( 'get_role' ) ) {
			foreach ( array( 'administrator', 'editor' ) as $role_slug ) {
				$role = get_role( $role_slug );
				if ( $role ) {
					$role->add_cap( 'edit_web_story' );
					$role->add_cap( 'read_web_story' );
					$role->add_cap( 'delete_web_story' );
					$role->add_cap( 'edit_web_stories' );
					$role->add_cap( 'edit_others_web_stories' );
					$role->add_cap( 'publish_web_stories' );
					$role->add_cap( 'read_private_web_stories' );
				}
			}
		}
	}

	/**
	 * Plugin activation
	 */
	public function on_activation() {
		$this->register_web_story_cpt_safely();
		flush_rewrite_rules();
	}

	/**
	 * Plugin deactivation
	 */
	public function on_deactivation() {
		flush_rewrite_rules();
	}

	/**
	 * Add settings link on plugins page
	 */
	public function add_action_links( $links ) {
		$custom_links = array(
			'<a href="' . esc_url( admin_url( 'admin.php?page=web-stories-templates' ) ) . '" style="color:#10b981;font-weight:600;">' . esc_html__( 'Template Studio', 'web-stories-templates-studio' ) . '</a>',
			'<a href="' . esc_url( admin_url( 'admin.php?page=web-stories-sizes-guide' ) ) . '">' . esc_html__( 'Sizes & Specs Guide', 'web-stories-templates-studio' ) . '</a>',
		);
		return array_merge( $custom_links, $links );
	}
}

// Bootstrap plugin
Web_Stories_Templates_Studio::instance();
