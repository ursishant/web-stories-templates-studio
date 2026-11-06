<?php
/**
 * AJAX Handler
 *
 * Processes asynchronous requests from the Admin Studio:
 * - 1-Click Story Creation & Publishing
 * - Generating Standalone AMP Story code
 * - Real-time Story Compliance Audit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WSTS_Ajax_Handler {

	private static $instance = null;

	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'wp_ajax_wsts_create_story', array( $this, 'ajax_create_story' ) );
		add_action( 'wp_ajax_wsts_export_amp', array( $this, 'ajax_export_amp' ) );
		add_action( 'wp_ajax_wsts_live_preview', array( $this, 'ajax_live_preview' ) );
		add_action( 'wp_ajax_nopriv_wsts_live_preview', array( $this, 'ajax_live_preview' ) );
		add_action( 'wp_ajax_wsts_save_design_system', array( $this, 'ajax_save_design_system' ) );
		add_action( 'wp_ajax_wsts_search_images', array( $this, 'ajax_search_images' ) );
		add_action( 'wp_ajax_wsts_save_api_keys', array( $this, 'ajax_save_api_keys' ) );
		add_action( 'wp_ajax_wsts_toggle_favorite', array( $this, 'ajax_toggle_favorite' ) );
		add_action( 'wp_ajax_wsts_set_default_template', array( $this, 'ajax_set_default_template' ) );
		add_action( 'wp_ajax_wsts_auto_generate_from_post', array( $this, 'ajax_auto_generate_from_post' ) );
	}

	/**
	 * AJAX endpoint to create or publish a Web Story
	 */
	public function ajax_create_story() {
		check_ajax_referer( 'wsts_studio_nonce', 'nonce' );

		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_send_json_error( array( 'message' => __( 'Insufficient permissions to create stories.', 'web-stories-templates-studio' ) ) );
		}

		$raw_data = isset( $_POST['story_data'] ) ? wp_unslash( $_POST['story_data'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$status   = isset( $_POST['status'] ) && 'publish' === $_POST['status'] ? 'publish' : 'draft';

		// If user cannot publish posts directly, fall back safely to draft
		if ( 'publish' === $status && ! current_user_can( 'publish_posts' ) ) {
			$status = 'draft';
		}

		if ( empty( $raw_data ) ) {
			wp_send_json_error( array( 'message' => __( 'No story payload provided.', 'web-stories-templates-studio' ) ) );
		}

		$story_data = is_array( $raw_data ) ? $raw_data : json_decode( $raw_data, true );

		if ( ! is_array( $story_data ) ) {
			wp_send_json_error( array( 'message' => __( 'Malformed story data.', 'web-stories-templates-studio' ) ) );
		}

		$default_site_logo = class_exists( 'WSTS_Admin_Studio' ) ? WSTS_Admin_Studio::get_website_logo() : WSTS_PLUGIN_URL . 'assets/images/default-logo.png';
		$default_site_name = class_exists( 'WSTS_Admin_Studio' ) ? WSTS_Admin_Studio::get_website_name() : get_bloginfo( 'name' );

		// Sanitize story payload
		$sanitized_story = array(
			'template_id'       => ! empty( $story_data['template_id'] ) ? sanitize_key( $story_data['template_id'] ) : '',
			'title'             => ! empty( $story_data['title'] ) ? sanitize_text_field( $story_data['title'] ) : 'Sick Web Story',
			'story_category'    => ! empty( $story_data['story_category'] ) ? sanitize_text_field( $story_data['story_category'] ) : ( ! empty( $story_data['category'] ) ? sanitize_text_field( $story_data['category'] ) : 'general' ),
			'category'          => ! empty( $story_data['category'] ) ? sanitize_text_field( $story_data['category'] ) : ( ! empty( $story_data['story_category'] ) ? sanitize_text_field( $story_data['story_category'] ) : 'general' ),
			'category_label'    => ! empty( $story_data['category_label'] ) ? sanitize_text_field( $story_data['category_label'] ) : '',
			'publisher'         => ! empty( $story_data['publisher'] ) ? sanitize_text_field( $story_data['publisher'] ) : $default_site_name,
			'publisher_logo'    => ! empty( $story_data['publisher_logo'] ) ? esc_url_raw( $story_data['publisher_logo'] ) : $default_site_logo,
			'poster_image'      => ! empty( $story_data['poster_image'] ) ? esc_url_raw( $story_data['poster_image'] ) : '',
			'accent_color'         => ! empty( $story_data['accent_color'] ) ? sanitize_hex_color( $story_data['accent_color'] ) : '#00f2fe',
			'secondary_color'      => ! empty( $story_data['secondary_color'] ) ? sanitize_hex_color( $story_data['secondary_color'] ) : '#fe0979',
			'bg_color'             => ! empty( $story_data['bg_color'] ) ? sanitize_hex_color( $story_data['bg_color'] ) : '#080811',
			'global_scrim'         => ! empty( $story_data['global_scrim'] ) ? preg_replace( '/[^a-zA-Z0-9,()_ %.-]/', '', $story_data['global_scrim'] ) : '',
			'headline_color'       => ! empty( $story_data['headline_color'] ) ? sanitize_hex_color( $story_data['headline_color'] ) : '#ffffff',
			'badge_bg'             => ! empty( $story_data['badge_bg'] ) ? sanitize_hex_color( $story_data['badge_bg'] ) : '',
			'badge_color'          => ! empty( $story_data['badge_color'] ) ? sanitize_hex_color( $story_data['badge_color'] ) : '',
			'custom_font_url'      => ! empty( $story_data['custom_font_url'] ) ? esc_url_raw( $story_data['custom_font_url'] ) : '',
			'font_family_title'    => ! empty( $story_data['font_family_title'] ) ? sanitize_text_field( $story_data['font_family_title'] ) : '',
			'font_family_body'     => ! empty( $story_data['font_family_body'] ) ? sanitize_text_field( $story_data['font_family_body'] ) : '',
			'headline_size'        => ! empty( $story_data['headline_size'] ) ? absint( $story_data['headline_size'] ) : 32,
			'body_size'            => ! empty( $story_data['body_size'] ) ? absint( $story_data['body_size'] ) : 15,
			'stat_size'            => ! empty( $story_data['stat_size'] ) ? absint( $story_data['stat_size'] ) : 40,
			'headline_tracking'    => ! empty( $story_data['headline_tracking'] ) ? sanitize_text_field( $story_data['headline_tracking'] ) : '-0.01em',
			'headline_line_height' => ! empty( $story_data['headline_line_height'] ) ? sanitize_text_field( $story_data['headline_line_height'] ) : '1.18',
			'google_fonts_url'     => ! empty( $story_data['google_fonts_url'] ) ? esc_url_raw( $story_data['google_fonts_url'] ) : '',
			'background_audio'     => ! empty( $story_data['background_audio'] ) ? esc_url_raw( $story_data['background_audio'] ) : '',
			'adsense_client'       => ! empty( $story_data['adsense_client'] ) ? sanitize_text_field( $story_data['adsense_client'] ) : '',
			'adsense_slot'         => ! empty( $story_data['adsense_slot'] ) ? sanitize_text_field( $story_data['adsense_slot'] ) : '',
			'ga4_id'               => ! empty( $story_data['ga4_id'] ) ? sanitize_text_field( $story_data['ga4_id'] ) : '',
			'seo_title'            => ! empty( $story_data['seo_title'] ) ? sanitize_text_field( $story_data['seo_title'] ) : '',
			'seo_desc'             => ! empty( $story_data['seo_desc'] ) ? sanitize_textarea_field( $story_data['seo_desc'] ) : '',
			'canonical_url'        => ! empty( $story_data['canonical_url'] ) ? esc_url_raw( $story_data['canonical_url'] ) : '',
			'schema_type'          => ! empty( $story_data['schema_type'] ) ? sanitize_text_field( $story_data['schema_type'] ) : 'NewsArticle',
			'og_image'             => ! empty( $story_data['og_image'] ) ? esc_url_raw( $story_data['og_image'] ) : '',
			'pages'                => array(),
		);

		if ( ! empty( $story_data['pages'] ) && is_array( $story_data['pages'] ) ) {
			foreach ( $story_data['pages'] as $page ) {
				$sanitized_story['pages'][] = array(
					'id'             => ! empty( $page['id'] ) ? sanitize_key( $page['id'] ) : 'page-' . uniqid(),
					'page_type'      => ! empty( $page['page_type'] ) ? sanitize_key( $page['page_type'] ) : 'content',
					'badge'          => ! empty( $page['badge'] ) ? sanitize_text_field( $page['badge'] ) : '',
					'badge_bg'       => ! empty( $page['badge_bg'] ) ? sanitize_hex_color( $page['badge_bg'] ) : '',
					'badge_color'    => ! empty( $page['badge_color'] ) ? sanitize_hex_color( $page['badge_color'] ) : '',
					'headline'       => ! empty( $page['headline'] ) ? sanitize_text_field( $page['headline'] ) : '',
					'headline_size'  => ! empty( $page['headline_size'] ) ? absint( $page['headline_size'] ) : 32,
					'headline_color' => ! empty( $page['headline_color'] ) ? sanitize_hex_color( $page['headline_color'] ) : '',
					'text_align'     => ! empty( $page['text_align'] ) && in_array( $page['text_align'], array( 'left', 'center', 'right' ), true ) ? $page['text_align'] : 'left',
					'body'           => ! empty( $page['body'] ) ? sanitize_textarea_field( $page['body'] ) : '',
					'body_color'     => ! empty( $page['body_color'] ) ? sanitize_hex_color( $page['body_color'] ) : '',
					'stat_number'    => ! empty( $page['stat_number'] ) ? sanitize_text_field( $page['stat_number'] ) : '',
					'stat_color'     => ! empty( $page['stat_color'] ) ? sanitize_hex_color( $page['stat_color'] ) : '',
					'stat_label'     => ! empty( $page['stat_label'] ) ? sanitize_text_field( $page['stat_label'] ) : '',
					'bg_image'       => ! empty( $page['bg_image'] ) ? esc_url_raw( $page['bg_image'] ) : '',
					'scrim'          => ! empty( $page['scrim'] ) ? sanitize_text_field( $page['scrim'] ) : '',
					'cta_text'       => ! empty( $page['cta_text'] ) ? sanitize_text_field( $page['cta_text'] ) : '',
					'cta_url'        => ! empty( $page['cta_url'] ) ? esc_url_raw( $page['cta_url'] ) : '',
					'animation'      => ! empty( $page['animation'] ) ? sanitize_key( $page['animation'] ) : 'fade-in',
					'anim_duration'  => ! empty( $page['anim_duration'] ) ? floatval( $page['anim_duration'] ) : 0.8,
					'anim_delay'     => ! empty( $page['anim_delay'] ) ? floatval( $page['anim_delay'] ) : 0.1,
					'duration'       => ! empty( $page['duration'] ) ? absint( $page['duration'] ) : 8,
				);
			}
		}

		// Create story post
		$post_id = WSTS_Story_Builder::create_web_story_post( $sanitized_story, $status );

		if ( is_wp_error( $post_id ) ) {
			wp_send_json_error( array( 'message' => $post_id->get_error_message() ) );
		}

		$edit_url = admin_url( 'post.php?post=' . $post_id . '&action=edit' );
		$view_url = get_permalink( $post_id );
		if ( empty( $view_url ) ) {
			$view_url = home_url( '/?p=' . $post_id );
		}

		wp_send_json_success(
			array(
				'post_id'  => $post_id,
				'edit_url' => $edit_url,
				'view_url' => $view_url,
				'status'   => $status,
				'message'  => 'publish' === $status
					? __( 'Sick Web Story successfully published!', 'web-stories-templates-studio' )
					: __( 'Story draft successfully created!', 'web-stories-templates-studio' ),
			)
		);
	}

	/**
	 * AJAX endpoint to export standalone AMP HTML and cache preview
	 */
	public function ajax_export_amp() {
		check_ajax_referer( 'wsts_studio_nonce', 'nonce' );

		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_send_json_error( array( 'message' => __( 'Insufficient permissions.', 'web-stories-templates-studio' ) ) );
		}

		$raw_data = isset( $_POST['story_data'] ) ? wp_unslash( $_POST['story_data'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

		if ( empty( $raw_data ) ) {
			wp_send_json_error( array( 'message' => __( 'No story payload provided.', 'web-stories-templates-studio' ) ) );
		}

		$story_data = is_array( $raw_data ) ? $raw_data : json_decode( $raw_data, true );

		if ( ! is_array( $story_data ) ) {
			wp_send_json_error( array( 'message' => __( 'Malformed story data.', 'web-stories-templates-studio' ) ) );
		}

		$amp_html = WSTS_Story_Builder::generate_amp_html( $story_data );

		// Cache story data in transient for live preview endpoint using dual keys (token + user ID)
		$preview_token = function_exists( 'wp_generate_password' ) ? wp_generate_password( 24, false, false ) : md5( uniqid( (string) wp_rand(), true ) );
		set_transient( 'wsts_prev_' . $preview_token, $story_data, 2 * HOUR_IN_SECONDS );

		$user_id = get_current_user_id();
		if ( $user_id ) {
			set_transient( 'wsts_preview_' . $user_id, $story_data, 2 * HOUR_IN_SECONDS );
		}

		$preview_url = add_query_arg(
			array(
				'action'   => 'wsts_live_preview',
				'token'    => $preview_token,
				'_wpnonce' => wp_create_nonce( 'wsts_preview_nonce' ),
				't'        => time(),
			),
			admin_url( 'admin-ajax.php' )
		);

		wp_send_json_success(
			array(
				'amp_html'    => $amp_html,
				'preview_url' => $preview_url,
			)
		);
	}

	/**
	 * AJAX endpoint to render real same-origin Live Story preview
	 * Enables official Google AMP runtime to execute natively without iframe sandbox blocks
	 */
	public function ajax_live_preview() {
		$nonce = isset( $_REQUEST['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) ) : '';
		if ( ! wp_verify_nonce( $nonce, 'wsts_preview_nonce' ) ) {
			wp_die( esc_html__( 'Preview session expired. Please re-open preview from the studio.', 'web-stories-templates-studio' ), 403 );
		}

		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_die( esc_html__( 'Insufficient permissions to preview story.', 'web-stories-templates-studio' ), 403 );
		}

		$token      = isset( $_REQUEST['token'] ) ? sanitize_key( $_REQUEST['token'] ) : '';
		$story_data = null;

		if ( ! empty( $token ) ) {
			$story_data = get_transient( 'wsts_prev_' . $token );
		}

		if ( empty( $story_data ) || ! is_array( $story_data ) ) {
			$user_id    = get_current_user_id();
			$story_data = get_transient( 'wsts_preview_' . $user_id );
		}

		if ( empty( $story_data ) || ! is_array( $story_data ) ) {
			$registry   = WSTS_Template_Registry::instance();
			$story_data = $registry->get( 'cyber-tech-radar' );
		}

		$amp_html = WSTS_Story_Builder::generate_amp_html( $story_data );

		if ( ! headers_sent() ) {
			header( 'Content-Type: text/html; charset=utf-8' );
			header( 'X-Robots-Tag: noindex, nofollow' );
		}

		echo $amp_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		exit;
	}

	/**
	 * AJAX endpoint to save global Brand Design System preset
	 */
	public function ajax_save_design_system() {
		check_ajax_referer( 'wsts_studio_nonce', 'nonce' );

		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_send_json_error( array( 'message' => __( 'Insufficient permissions.', 'web-stories-templates-studio' ) ) );
		}

		$raw_data = isset( $_POST['design_system'] ) ? wp_unslash( $_POST['design_system'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

		if ( empty( $raw_data ) ) {
			wp_send_json_error( array( 'message' => __( 'No design system payload provided.', 'web-stories-templates-studio' ) ) );
		}

		$ds = is_array( $raw_data ) ? $raw_data : json_decode( $raw_data, true );

		if ( ! is_array( $ds ) ) {
			wp_send_json_error( array( 'message' => __( 'Invalid design system payload.', 'web-stories-templates-studio' ) ) );
		}

		$sanitized_ds = array(
			'publisher'            => ! empty( $ds['publisher'] ) ? sanitize_text_field( $ds['publisher'] ) : '',
			'publisher_logo'       => ! empty( $ds['publisher_logo'] ) ? esc_url_raw( $ds['publisher_logo'] ) : '',
			'accent_color'         => ! empty( $ds['accent_color'] ) ? sanitize_hex_color( $ds['accent_color'] ) : '#a78bfa',
			'secondary_color'      => ! empty( $ds['secondary_color'] ) ? sanitize_hex_color( $ds['secondary_color'] ) : '#38bdf8',
			'bg_color'             => ! empty( $ds['bg_color'] ) ? sanitize_hex_color( $ds['bg_color'] ) : '#09090b',
			'global_scrim'         => ! empty( $ds['global_scrim'] ) ? preg_replace( '/[^a-zA-Z0-9,()_ %.-]/', '', $ds['global_scrim'] ) : '',
			'headline_color'       => ! empty( $ds['headline_color'] ) ? sanitize_hex_color( $ds['headline_color'] ) : '#ffffff',
			'badge_bg'             => ! empty( $ds['badge_bg'] ) ? sanitize_hex_color( $ds['badge_bg'] ) : '',
			'badge_color'          => ! empty( $ds['badge_color'] ) ? sanitize_hex_color( $ds['badge_color'] ) : '',
			'custom_font_url'      => ! empty( $ds['custom_font_url'] ) ? esc_url_raw( $ds['custom_font_url'] ) : '',
			'font_family_title'    => ! empty( $ds['font_family_title'] ) ? sanitize_text_field( $ds['font_family_title'] ) : "'Space Grotesk', sans-serif",
			'font_family_body'     => ! empty( $ds['font_family_body'] ) ? sanitize_text_field( $ds['font_family_body'] ) : "'Inter', sans-serif",
			'headline_size'        => ! empty( $ds['headline_size'] ) ? absint( $ds['headline_size'] ) : 32,
			'body_size'            => ! empty( $ds['body_size'] ) ? absint( $ds['body_size'] ) : 15,
			'stat_size'            => ! empty( $ds['stat_size'] ) ? absint( $ds['stat_size'] ) : 40,
			'headline_tracking'    => ! empty( $ds['headline_tracking'] ) ? sanitize_text_field( $ds['headline_tracking'] ) : '-0.01em',
			'headline_line_height' => ! empty( $ds['headline_line_height'] ) ? sanitize_text_field( $ds['headline_line_height'] ) : '1.18',
			'google_fonts_url'     => ! empty( $ds['google_fonts_url'] ) ? esc_url_raw( $ds['google_fonts_url'] ) : '',
		);

		update_option( 'wsts_brand_design_system', $sanitized_ds, false );

		wp_send_json_success( array(
			'message'       => __( 'Brand Design System saved as your global preset.', 'web-stories-templates-studio' ),
			'design_system' => $sanitized_ds,
		) );
	}

	/**
	 * AJAX endpoint to search stock images from Unsplash, Pexels, or Media Library
	 */
	public function ajax_search_images() {
		check_ajax_referer( 'wsts_studio_nonce', 'nonce' );

		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_send_json_error( array( 'message' => __( 'Insufficient permissions.', 'web-stories-templates-studio' ) ) );
		}

		$query  = isset( $_POST['query'] ) ? sanitize_text_field( wp_unslash( $_POST['query'] ) ) : '';
		$source = isset( $_POST['source'] ) ? sanitize_key( wp_unslash( $_POST['source'] ) ) : 'all';
		$images = WSTS_Template_Registry::get_stock_images( $query, $source );

		wp_send_json_success( array(
			'images' => $images,
			'count'  => count( $images ),
			'source' => $source,
		) );
	}

	/**
	 * AJAX endpoint to save optional Unsplash and Pexels API keys
	 */
	public function ajax_save_api_keys() {
		check_ajax_referer( 'wsts_studio_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) && ! current_user_can( 'edit_posts' ) ) {
			wp_send_json_error( array( 'message' => __( 'Insufficient permissions to save API keys.', 'web-stories-templates-studio' ) ) );
		}

		$unsplash_key = isset( $_POST['unsplash_key'] ) ? sanitize_text_field( wp_unslash( $_POST['unsplash_key'] ) ) : '';
		$pexels_key   = isset( $_POST['pexels_key'] ) ? sanitize_text_field( wp_unslash( $_POST['pexels_key'] ) ) : '';

		update_option( 'wsts_unsplash_access_key', $unsplash_key, false );
		update_option( 'wsts_pexels_api_key', $pexels_key, false );

		wp_send_json_success( array(
			'message'      => __( 'Unsplash & Pexels API configuration saved successfully.', 'web-stories-templates-studio' ),
			'has_unsplash' => ! empty( $unsplash_key ),
			'has_pexels'   => ! empty( $pexels_key ),
		) );
	}

	/**
	 * AJAX endpoint to toggle a template in user's favorites
	 */
	public function ajax_toggle_favorite() {
		check_ajax_referer( 'wsts_studio_nonce', 'nonce' );

		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_send_json_error( array( 'message' => __( 'Insufficient permissions.', 'web-stories-templates-studio' ) ) );
		}

		$template_id = isset( $_POST['template_id'] ) ? sanitize_key( wp_unslash( $_POST['template_id'] ) ) : '';
		if ( empty( $template_id ) ) {
			wp_send_json_error( array( 'message' => __( 'No template ID specified.', 'web-stories-templates-studio' ) ) );
		}

		$user_id   = get_current_user_id();
		$favorites = get_user_meta( $user_id, 'wsts_favorite_templates', true );
		if ( ! is_array( $favorites ) ) {
			$favorites = array();
		}

		$is_fav = in_array( $template_id, $favorites, true );
		if ( $is_fav ) {
			$favorites = array_values( array_diff( $favorites, array( $template_id ) ) );
			$status    = 'removed';
			$message   = __( 'Removed from favorites.', 'web-stories-templates-studio' );
		} else {
			$favorites[] = $template_id;
			$favorites   = array_values( array_unique( $favorites ) );
			$status      = 'added';
			$message     = __( 'Added to favorites!', 'web-stories-templates-studio' );
		}

		update_user_meta( $user_id, 'wsts_favorite_templates', $favorites );

		wp_send_json_success( array(
			'template_id' => $template_id,
			'status'      => $status,
			'is_favorite' => ( 'added' === $status ),
			'favorites'   => $favorites,
			'count'       => count( $favorites ),
			'message'     => $message,
		) );
	}

	/**
	 * AJAX endpoint to set default template
	 */
	public function ajax_set_default_template() {
		check_ajax_referer( 'wsts_studio_nonce', 'nonce' );

		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_send_json_error( array( 'message' => __( 'Insufficient permissions.', 'web-stories-templates-studio' ) ) );
		}

		$template_id = isset( $_POST['template_id'] ) ? sanitize_key( wp_unslash( $_POST['template_id'] ) ) : '';
		if ( empty( $template_id ) ) {
			wp_send_json_error( array( 'message' => __( 'No template ID specified.', 'web-stories-templates-studio' ) ) );
		}

		$user_id = get_current_user_id();
		update_user_meta( $user_id, 'wsts_default_template', $template_id );

		// Also ensure template is in favorites
		$favorites = get_user_meta( $user_id, 'wsts_favorite_templates', true );
		if ( ! is_array( $favorites ) ) {
			$favorites = array();
		}
		if ( ! in_array( $template_id, $favorites, true ) ) {
			$favorites[] = $template_id;
			update_user_meta( $user_id, 'wsts_favorite_templates', $favorites );
		}

		wp_send_json_success( array(
			'template_id'      => $template_id,
			'default_template' => $template_id,
			'favorites'        => $favorites,
			'message'          => __( 'Default template saved successfully!', 'web-stories-templates-studio' ),
		) );
	}

	/**
	 * AJAX endpoint to automatically generate a complete Web Story from a WordPress post
	 */
	public function ajax_auto_generate_from_post() {
		check_ajax_referer( 'wsts_studio_nonce', 'nonce' );

		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_send_json_error( array( 'message' => __( 'Insufficient permissions.', 'web-stories-templates-studio' ) ) );
		}

		$post_id   = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;
		$raw_url   = isset( $_POST['url'] ) ? esc_url_raw( wp_unslash( $_POST['url'] ) ) : '';
		$raw_text  = isset( $_POST['text'] ) ? wp_strip_all_tags( wp_unslash( $_POST['text'] ) ) : '';
		$target_cat = ! empty( $_POST['category_slug'] ) ? sanitize_key( $_POST['category_slug'] ) : ( ! empty( $_POST['category'] ) ? sanitize_key( $_POST['category'] ) : 'tech' );

		$target_post = null;
		if ( $post_id > 0 ) {
			$target_post = get_post( $post_id );
		} elseif ( ! empty( $raw_url ) ) {
			$found_id = url_to_postid( $raw_url );
			if ( $found_id > 0 ) {
				$target_post = get_post( $found_id );
			}
		}

		$post_title     = '';
		$post_content   = '';
		$post_permalink = '';
		$featured_img   = '';

		if ( $target_post ) {
			$post_title     = get_the_title( $target_post );
			$post_content   = $target_post->post_content;
			$post_permalink = get_permalink( $target_post );
			$feat_id        = get_post_thumbnail_id( $target_post );
			if ( $feat_id ) {
				$feat_src = wp_get_attachment_image_src( $feat_id, 'full' );
				if ( ! empty( $feat_src[0] ) ) {
					$featured_img = $feat_src[0];
				}
			}
		} elseif ( ! empty( $raw_text ) ) {
			$lines          = explode( "\n", trim( $raw_text ) );
			$post_title     = array_shift( $lines );
			$post_content   = implode( "\n", $lines );
			$post_permalink = home_url( '/' );
		} else {
			wp_send_json_error( array( 'message' => __( 'Please select a WordPress post or provide article text.', 'web-stories-templates-studio' ) ) );
		}

		if ( empty( $post_title ) ) {
			$post_title = __( 'Visual Story Highlights', 'web-stories-templates-studio' );
		}
		$post_title = trim( preg_replace( '/^[#>*_\-\s]+/', '', $post_title ) );

		// Strip Gutenberg block delimiters, HTML tags, and markdown artifacts
		$clean_content = preg_replace( '/<!--(.|\s)*?-->/', '', $post_content );
		$stripped      = wp_strip_all_tags( $clean_content );
		$lines         = explode( "\n", $stripped );
		$paragraphs    = array();
		foreach ( $lines as $line ) {
			$t = trim( preg_replace( '/^[#>*_\-\s]+/', '', trim( $line ) ) );
			if ( mb_strlen( $t ) > 25 ) {
				$paragraphs[] = $t;
			}
		}

		// Extract headings from raw content if available
		preg_match_all( '/<h[2-4][^>]*>(.*?)<\/h[2-4]>/i', $post_content, $h_matches );
		$extracted_headings = array();
		if ( ! empty( $h_matches[1] ) ) {
			foreach ( $h_matches[1] as $hm ) {
				$clean_h = trim( preg_replace( '/^[#>*_\-\s]+/', '', wp_strip_all_tags( $hm ) ) );
				if ( ! empty( $clean_h ) ) {
					$extracted_headings[] = $clean_h;
				}
			}
		}

		// Default fallback stock imagery if post has none
		$default_poster    = ! empty( $featured_img ) ? $featured_img : 'https://images.unsplash.com/photo-1550745165-9bc0b252726f?w=1080&h=1920&fit=crop&q=80';
		$default_site_logo = class_exists( 'WSTS_Admin_Studio' ) ? WSTS_Admin_Studio::get_website_logo() : '';
		$default_site_name = class_exists( 'WSTS_Admin_Studio' ) ? WSTS_Admin_Studio::get_website_name() : get_bloginfo( 'name' );

		// Build 4 slides
		$slide1_head = mb_substr( $post_title, 0, 65 );
		$p0 = ! empty( $paragraphs[0] ) ? $paragraphs[0] : '';
		$slide1_body = ! empty( $p0 ) ? mb_substr( $p0, 0, 160 ) . '...' : __( 'Discover key takeaways and insights directly from our latest publication.', 'web-stories-templates-studio' );

		$slide2_head = ! empty( $extracted_headings[0] ) ? mb_substr( $extracted_headings[0], 0, 60 ) : __( 'Core Highlights & Breakthroughs', 'web-stories-templates-studio' );
		$p1 = ! empty( $paragraphs[1] ) ? $paragraphs[1] : '';
		$slide2_body = ! empty( $p1 ) ? mb_substr( $p1, 0, 160 ) . '...' : ( ! empty( $p0 ) ? mb_substr( $p0, 160, 160 ) . '...' : __( 'Explore the fundamental principles driving modern retention and reader engagement.', 'web-stories-templates-studio' ) );

		$slide3_head = ! empty( $extracted_headings[1] ) ? mb_substr( $extracted_headings[1], 0, 60 ) : __( 'Proven Strategies in Practice', 'web-stories-templates-studio' );
		$p2 = ! empty( $paragraphs[2] ) ? $paragraphs[2] : '';
		$slide3_body = ! empty( $p2 ) ? mb_substr( $p2, 0, 160 ) . '...' : __( 'Tested frameworks engineered for sustainable growth and dependable results.', 'web-stories-templates-studio' );

		$slide4_head = __( 'Read Full In-Depth Analysis', 'web-stories-templates-studio' );
		$slide4_body = __( 'Tap below to read the complete article on our website.', 'web-stories-templates-studio' );

		// Find stock image matching post topic and chosen niche
		$query_to_search = ! empty( $target_cat ) && 'all' !== $target_cat ? $target_cat : $post_title;
		$stock_photos = WSTS_Template_Registry::get_stock_images( $query_to_search, 'all' );
		$img1 = ! empty( $featured_img ) ? $featured_img : ( ! empty( $stock_photos[0]['url'] ) ? $stock_photos[0]['url'] : $default_poster );
		$img2 = ! empty( $stock_photos[1]['url'] ) ? $stock_photos[1]['url'] : $default_poster;
		$img3 = ! empty( $stock_photos[2]['url'] ) ? $stock_photos[2]['url'] : $default_poster;
		$img4 = ! empty( $stock_photos[3]['url'] ) ? $stock_photos[3]['url'] : $default_poster;

		// Color palettes tailored by niche
		$palette_map = array(
			'tech'    => array( 'accent' => '#00f2fe', 'secondary' => '#3b82f6', 'bg' => '#080811', 'font_title' => "'Space Grotesk', sans-serif" ),
			'fashion' => array( 'accent' => '#d4af37', 'secondary' => '#f59e0b', 'bg' => '#141416', 'font_title' => "'Playfair Display', serif" ),
			'viral'   => array( 'accent' => '#f43f5e', 'secondary' => '#8b5cf6', 'bg' => '#09090b', 'font_title' => "'Syne', sans-serif" ),
			'food'    => array( 'accent' => '#f97316', 'secondary' => '#ef4444', 'bg' => '#18120c', 'font_title' => "'Playfair Display', serif" ),
			'fitness' => array( 'accent' => '#10b981', 'secondary' => '#06b6d4', 'bg' => '#081412', 'font_title' => "'Montserrat', sans-serif" ),
			'travel'  => array( 'accent' => '#38bdf8', 'secondary' => '#0ea5e9', 'bg' => '#071524', 'font_title' => "'Plus Jakarta Sans', sans-serif" ),
		);
		$p = isset( $palette_map[ $target_cat ] ) ? $palette_map[ $target_cat ] : $palette_map['tech'];

		$generated_story = array(
			'id'                  => 'auto-post-' . ( $post_id ? $post_id : uniqid() ),
			'template_id'         => 'custom-auto-story',
			'name'                => $post_title,
			'title'               => $post_title,
			'tagline'             => mb_substr( $slide1_body, 0, 70 ),
			'category'            => $target_cat,
			'story_category'      => $target_cat,
			'category_label'      => ucfirst( $target_cat ),
			'publisher'           => $default_site_name,
			'publisher_logo'      => $default_site_logo,
			'poster_image'        => $img1,
			'accent_color'        => $p['accent'],
			'secondary_color'     => $p['secondary'],
			'bg_color'            => $p['bg'],
			'headline_color'      => '#ffffff',
			'badge_bg'            => $p['accent'],
			'badge_color'         => '#080811',
			'font_family_title'   => $p['font_title'],
			'font_family_body'    => "'Inter', sans-serif",
			'headline_size'       => 32,
			'body_size'           => 15,
			'stat_size'           => 40,
			'headline_tracking'   => '-0.01em',
			'headline_line_height'=> '1.18',
			'global_scrim'        => 'linear-gradient(180deg, rgba(8,8,17,0.3) 0%, rgba(8,8,17,0.92) 80%)',
			'seo_title'           => mb_substr( $post_title, 0, 70 ),
			'seo_desc'            => mb_substr( $slide1_body, 0, 155 ),
			'canonical_url'       => $post_permalink,
			'schema_type'         => 'NewsArticle',
			'og_image'            => $img1,
			'background_audio'    => '',
			'adsense_client'      => '',
			'adsense_slot'        => '',
			'ga4_id'              => '',
			'pages'               => array(
				array(
					'id'            => 'page-1',
					'page_type'     => 'cover',
					'badge'         => __( 'FEATURED ARTICLE', 'web-stories-templates-studio' ),
					'badge_bg'      => '#00f2fe',
					'badge_color'   => '#080811',
					'headline'      => $slide1_head,
					'headline_color'=> '#ffffff',
					'body'          => $slide1_body,
					'body_color'    => 'rgba(255,255,255,0.92)',
					'stat_number'   => '01',
					'stat_color'    => '#00f2fe',
					'stat_label'    => __( 'INTRODUCTION', 'web-stories-templates-studio' ),
					'bg_image'      => $img1,
					'animation'     => 'fly-in-bottom',
					'anim_duration' => 0.7,
					'anim_delay'    => 0.1,
				),
				array(
					'id'            => 'page-2',
					'page_type'     => 'content',
					'badge'         => __( 'KEY INSIGHT', 'web-stories-templates-studio' ),
					'badge_bg'      => '#3b82f6',
					'badge_color'   => '#ffffff',
					'headline'      => $slide2_head,
					'headline_color'=> '#ffffff',
					'body'          => $slide2_body,
					'body_color'    => 'rgba(255,255,255,0.92)',
					'stat_number'   => '02',
					'stat_color'    => '#3b82f6',
					'stat_label'    => __( 'ANALYSIS', 'web-stories-templates-studio' ),
					'bg_image'      => $img2,
					'animation'     => 'fly-in-bottom',
					'anim_duration' => 0.7,
					'anim_delay'    => 0.1,
				),
				array(
					'id'            => 'page-3',
					'page_type'     => 'content',
					'badge'         => __( 'STRATEGY', 'web-stories-templates-studio' ),
					'badge_bg'      => '#00f2fe',
					'badge_color'   => '#080811',
					'headline'      => $slide3_head,
					'headline_color'=> '#ffffff',
					'body'          => $slide3_body,
					'body_color'    => 'rgba(255,255,255,0.92)',
					'stat_number'   => '03',
					'stat_color'    => '#00f2fe',
					'stat_label'    => __( 'APPLICATION', 'web-stories-templates-studio' ),
					'bg_image'      => $img3,
					'animation'     => 'fly-in-bottom',
					'anim_duration' => 0.7,
					'anim_delay'    => 0.1,
				),
				array(
					'id'            => 'page-4',
					'page_type'     => 'verdict',
					'badge'         => __( 'READ MORE', 'web-stories-templates-studio' ),
					'badge_bg'      => '#3b82f6',
					'badge_color'   => '#ffffff',
					'headline'      => $slide4_head,
					'headline_color'=> '#ffffff',
					'body'          => $slide4_body,
					'body_color'    => 'rgba(255,255,255,0.92)',
					'stat_number'   => '04',
					'stat_color'    => '#3b82f6',
					'stat_label'    => __( 'CONCLUSION', 'web-stories-templates-studio' ),
					'bg_image'      => $img4,
					'animation'     => 'fly-in-bottom',
					'anim_duration' => 0.7,
					'anim_delay'    => 0.1,
					'cta_text'      => __( 'Read Full Article', 'web-stories-templates-studio' ),
					'cta_url'       => $post_permalink,
				),
			),
		);

		wp_send_json_success( array(
			'story'   => $generated_story,
			'message' => __( 'Web Story successfully generated from article!', 'web-stories-templates-studio' ),
		) );
	}
}
