<?php
/**
 * Frontend Story Player & Embed Shortcode
 *
 * Provides responsive story card embeds, interactive lightboxes, and frontend single story template rendering with strict request guards.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WSTS_Story_Player {

	private static $instance = null;

	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_shortcode( 'sick_web_story', array( $this, 'render_shortcode' ) );
		add_shortcode( 'web_story_player', array( $this, 'render_shortcode' ) );
		add_filter( 'template_include', array( $this, 'filter_single_story_template' ), 99 );
	}

	/**
	 * Render single story frontend if standalone AMP is needed
	 * Guarantees zero interference with feeds, REST API, sitemaps, or the official Google plugin.
	 */
	public function filter_single_story_template( $template ) {
		// Guard against admin, feeds, embeds, and REST requests
		if ( is_admin() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) || is_feed() || is_embed() ) {
			return $template;
		}

		if ( function_exists( 'wp_is_json_request' ) && wp_is_json_request() ) {
			return $template;
		}

		if ( is_singular( 'web-story' ) || ( is_singular( 'post' ) && get_post_meta( get_the_ID(), '_wsts_is_web_story', true ) ) ) {
			global $post;

			if ( ! is_a( $post, 'WP_Post' ) ) {
				return $template;
			}

			// If the official web-stories plugin is active, let it take precedence
			if ( class_exists( '\Google\Web_Stories\Plugin' ) ) {
				return $template;
			}

			// Only render if post contains valid AMP story markup
			if ( ! empty( $post->post_content ) && false !== strpos( $post->post_content, '<amp-story' ) ) {
				if ( ! headers_sent() ) {
					header( 'Content-Type: text/html; charset=utf-8' );
				}
				echo $post->post_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				exit;
			}
		}

		return $template;
	}

	/**
	 * Shortcode callback: [sick_web_story id="123" width="360px"]
	 */
	public function render_shortcode( $atts ) {
		$atts = shortcode_atts(
			array(
				'id'     => 0,
				'width'  => '360px',
				'align'  => 'center',
			),
			$atts,
			'sick_web_story'
		);

		$post_id = absint( $atts['id'] );
		if ( ! $post_id ) {
			return '';
		}

		$post = get_post( $post_id );
		if ( ! is_a( $post, 'WP_Post' ) ) {
			return '';
		}

		$permalink = get_permalink( $post_id );
		$poster    = get_post_meta( $post_id, '_wsts_poster_image', true );
		if ( ! $poster && has_post_thumbnail( $post_id ) ) {
			$poster = get_the_post_thumbnail_url( $post_id, 'full' );
		}

		// Sanitize width style to prevent CSS injection
		$clean_width = preg_replace( '/[^0-9pxrem%]/', '', $atts['width'] );
		if ( empty( $clean_width ) ) {
			$clean_width = '360px';
		}

		ob_start();
		?>
		<div class="wsts-story-embed-wrapper" style="max-width: <?php echo esc_attr( $clean_width ); ?>; margin: 20px auto; text-align: center;">
			<div class="wsts-story-embed-card" style="position: relative; width: 100%; aspect-ratio: 9/16; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
				<?php if ( $poster ) : ?>
					<img src="<?php echo esc_url( $poster ); ?>" alt="<?php echo esc_attr( wp_strip_all_tags( $post->post_title ) ); ?>" style="width: 100%; height: 100%; object-fit: cover; display: block;">
				<?php endif; ?>
				<div class="wsts-story-embed-overlay" style="position: absolute; bottom: 0; left: 0; width: 100%; padding: 24px; box-sizing: border-box; background: linear-gradient(180deg, transparent 0%, rgba(0,0,0,0.85) 100%); color: #ffffff; text-align: left;">
					<h3 style="margin: 0 0 10px 0; font-size: 20px; font-weight: 700; color: #ffffff;"><?php echo esc_html( wp_strip_all_tags( $post->post_title ) ); ?></h3>
					<a href="<?php echo esc_url( $permalink ); ?>" target="_blank" rel="noopener" style="display: inline-block; padding: 10px 20px; border-radius: 50px; background: #00f2fe; color: #080811; font-weight: 700; font-size: 13px; text-decoration: none;">
						▶ <?php esc_html_e( 'View Web Story', 'web-stories-templates-studio' ); ?>
					</a>
				</div>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}
}
