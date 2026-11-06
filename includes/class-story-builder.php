<?php
/**
 * Story Builder
 *
 * Compiles template specifications and customized parameters into:
 * 1. Fully compliant, standalone Google AMP Story HTML document (post_content).
 * 2. Visual editor JSON state (post_content_filtered) compatible with Google Web Stories for WordPress.
 * 3. Handles WordPress post creation, meta assignment, and schema generation with strict security guards.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WSTS_Story_Builder {

	/**
	 * Validate and sanitize URLs to allow only safe web protocols (http, https)
	 *
	 * @param string $url Target URL
	 * @param string $fallback Fallback if invalid
	 * @return string Safe URL
	 */
	public static function sanitize_safe_url( $url, $fallback = '#' ) {
		if ( empty( $url ) ) {
			return $fallback;
		}

		$trimmed = trim( (string) $url );

		// Strict protocol allow-list (reject javascript:, data:, vbscript:)
		if ( preg_match( '/^(https?:\/\/|\/|#)/i', $trimmed ) ) {
			return function_exists( 'esc_url' ) ? esc_url( $trimmed ) : filter_var( $trimmed, FILTER_SANITIZE_URL );
		}

		return $fallback;
	}

	/**
	 * Build complete standalone AMP Story HTML
	 *
	 * @param array $story Custom story configuration
	 * @return string Full valid AMP HTML string
	 */
	public static function generate_amp_html( $story ) {
		$title           = ! empty( $story['title'] ) ? esc_attr( wp_strip_all_tags( $story['title'] ) ) : 'Web Story';

		// Resolve Publisher Name & Logo automatically from WordPress website if empty or generic dummy
		$fallback_name = function_exists( 'get_bloginfo' ) ? get_bloginfo( 'name' ) : 'Web Stories';
		$default_site_name = class_exists( 'WSTS_Admin_Studio' ) ? WSTS_Admin_Studio::get_website_name() : $fallback_name;
		$default_site_logo = class_exists( 'WSTS_Admin_Studio' ) ? WSTS_Admin_Studio::get_website_logo() : WSTS_PLUGIN_URL . 'assets/images/default-logo.png';

		$raw_publisher = ! empty( $story['publisher'] ) ? wp_strip_all_tags( $story['publisher'] ) : '';
		if ( empty( $raw_publisher ) || in_array( $raw_publisher, array( 'TechPulse Studio', 'Atelier Vogue', 'Web Stories Publisher' ), true ) ) {
			$raw_publisher = $default_site_name;
		}
		$publisher = esc_attr( $raw_publisher ? $raw_publisher : $fallback_name );

		$raw_logo = ! empty( $story['publisher_logo'] ) ? $story['publisher_logo'] : '';
		if ( empty( $raw_logo ) || false !== strpos( $raw_logo, 'unsplash.com' ) ) {
			$raw_logo = $default_site_logo;
		}
		$publisher_logo = self::sanitize_safe_url( $raw_logo, $default_site_logo );

		$poster_portrait = ! empty( $story['poster_image'] ) ? self::sanitize_safe_url( $story['poster_image'], 'https://images.unsplash.com/photo-1550745165-9bc0b252726f?w=1080&h=1920&fit=crop&q=80' ) : 'https://images.unsplash.com/photo-1550745165-9bc0b252726f?w=1080&h=1920&fit=crop&q=80';
		$accent_color    = ! empty( $story['accent_color'] ) ? sanitize_hex_color( $story['accent_color'] ) : '#00f2fe';
		$secondary_color = ! empty( $story['secondary_color'] ) ? sanitize_hex_color( $story['secondary_color'] ) : '#fe0979';
		$bg_color        = ! empty( $story['bg_color'] ) ? sanitize_hex_color( $story['bg_color'] ) : '#080811';

		// Clean font declarations & construct Apple-grade typography stacks
		$raw_font_title = ! empty( $story['font_family_title'] ) ? $story['font_family_title'] : "'Space Grotesk', sans-serif";
		$raw_font_body  = ! empty( $story['font_family_body'] ) ? $story['font_family_body'] : "'Inter', sans-serif";

		$primary_title_font = trim( str_replace( array( '"', "'", 'sans-serif', 'serif', 'monospace', ',' ), '', $raw_font_title ) );
		$primary_body_font  = trim( str_replace( array( '"', "'", 'sans-serif', 'serif', 'monospace', ',' ), '', $raw_font_body ) );

		if ( empty( $primary_title_font ) ) {
			$primary_title_font = 'Space Grotesk';
		}
		if ( empty( $primary_body_font ) ) {
			$primary_body_font = 'Inter';
		}

		// CSS font stacks with Apple SF Pro and system fallbacks (clean CSS tokens, NO esc_attr inside <style>)
		$font_title_css = '"' . esc_html( $primary_title_font ) . '", -apple-system, BlinkMacSystemFont, "SF Pro Display", "Segoe UI", Roboto, sans-serif';
		$font_body_css  = '"' . esc_html( $primary_body_font ) . '", -apple-system, BlinkMacSystemFont, "SF Pro Text", "Segoe UI", Roboto, sans-serif';

		// Unified sizing & typography metrics
		$headline_size_global = ! empty( $story['headline_size'] ) ? absint( $story['headline_size'] ) : 32;
		$body_size_global     = ! empty( $story['body_size'] ) ? absint( $story['body_size'] ) : 15;
		$stat_size_global     = ! empty( $story['stat_size'] ) ? absint( $story['stat_size'] ) : 40;
		$headline_tracking    = ! empty( $story['headline_tracking'] ) ? sanitize_text_field( $story['headline_tracking'] ) : '-0.01em';
		$headline_line_height = ! empty( $story['headline_line_height'] ) ? sanitize_text_field( $story['headline_line_height'] ) : '1.18';

		// Dynamic Google Fonts: Always ensure required Google Fonts are bundled and linked
		$fonts_to_load = array( 'Space Grotesk', 'Inter', 'Playfair Display', 'Plus Jakarta Sans', 'Syne', 'Cinzel' );
		if ( ! in_array( $primary_title_font, $fonts_to_load, true ) ) {
			$fonts_to_load[] = $primary_title_font;
		}
		if ( ! in_array( $primary_body_font, $fonts_to_load, true ) ) {
			$fonts_to_load[] = $primary_body_font;
		}

		$gf_families = array();
		foreach ( $fonts_to_load as $fam ) {
			$gf_families[] = 'family=' . str_replace( ' ', '+', $fam ) . ':ital,wght@0,400;0,600;0,700;0,800;1,400;1,600';
		}
		$google_fonts = 'https://fonts.googleapis.com/css2?' . implode( '&', $gf_families ) . '&display=swap';

		ob_start();
		?>
<!doctype html>
<html ⚡ lang="<?php echo esc_attr( get_locale() ); ?>">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width,minimum-scale=1,initial-scale=1">
	<title><?php echo esc_html( ! empty( $story['seo_title'] ) ? $story['seo_title'] : $title ); ?></title>
	<meta name="description" content="<?php echo esc_attr( ! empty( $story['seo_desc'] ) ? $story['seo_desc'] : wp_strip_all_tags( $title ) ); ?>">
	<link rel="canonical" href="<?php echo esc_url( ! empty( $story['canonical_url'] ) ? $story['canonical_url'] : home_url( '/' ) ); ?>">

	<!-- OpenGraph & Social Cards -->
	<meta property="og:type" content="article">
	<meta property="og:title" content="<?php echo esc_attr( ! empty( $story['seo_title'] ) ? $story['seo_title'] : $title ); ?>">
	<meta property="og:description" content="<?php echo esc_attr( ! empty( $story['seo_desc'] ) ? $story['seo_desc'] : wp_strip_all_tags( $title ) ); ?>">
	<meta property="og:image" content="<?php echo esc_url( ! empty( $story['og_image'] ) ? $story['og_image'] : $poster_portrait ); ?>">
	<meta property="og:url" content="<?php echo esc_url( ! empty( $story['canonical_url'] ) ? $story['canonical_url'] : home_url( '/' ) ); ?>">
	<meta name="twitter:card" content="summary_large_image">
	<meta name="twitter:title" content="<?php echo esc_attr( ! empty( $story['seo_title'] ) ? $story['seo_title'] : $title ); ?>">
	<meta name="twitter:description" content="<?php echo esc_attr( ! empty( $story['seo_desc'] ) ? $story['seo_desc'] : wp_strip_all_tags( $title ) ); ?>">
	<meta name="twitter:image" content="<?php echo esc_url( ! empty( $story['og_image'] ) ? $story['og_image'] : $poster_portrait ); ?>">

	<!-- Google Discover & Schema.org JSON-LD Structured Data -->
	<script type="application/ld+json">
	{
		"@context": "https://schema.org",
		"@type": "<?php echo esc_attr( ! empty( $story['schema_type'] ) ? $story['schema_type'] : 'NewsArticle' ); ?>",
		"mainEntityOfPage": {
			"@type": "WebPage",
			"@id": "<?php echo esc_url( ! empty( $story['canonical_url'] ) ? $story['canonical_url'] : home_url( '/' ) ); ?>"
		},
		"headline": "<?php echo esc_attr( ! empty( $story['seo_title'] ) ? $story['seo_title'] : $title ); ?>",
		"description": "<?php echo esc_attr( ! empty( $story['seo_desc'] ) ? $story['seo_desc'] : wp_strip_all_tags( $title ) ); ?>",
		"image": [
			"<?php echo esc_url( $poster_portrait ); ?>"
		],
		"datePublished": "<?php echo esc_attr( gmdate( 'c' ) ); ?>",
		"dateModified": "<?php echo esc_attr( gmdate( 'c' ) ); ?>",
		"author": {
			"@type": "Organization",
			"name": "pennion",
			"url": "https://pennion.com"
		},
		"publisher": {
			"@type": "Organization",
			"name": "<?php echo esc_attr( $publisher ); ?>",
			"logo": {
				"@type": "ImageObject",
				"url": "<?php echo esc_url( $publisher_logo ); ?>"
			}
		}
	}
	</script>

	<?php if ( $google_fonts ) : ?>
		<link rel="stylesheet" href="<?php echo esc_url( $google_fonts ); ?>">
	<?php endif; ?>
	<?php if ( ! empty( $story['custom_font_url'] ) ) : ?>
		<link rel="stylesheet" href="<?php echo esc_url( $story['custom_font_url'] ); ?>">
	<?php endif; ?>
	<script async src="https://cdn.ampproject.org/v0.js"></script>
	<script async custom-element="amp-story" src="https://cdn.ampproject.org/v0/amp-story-1.0.js"></script>
	<?php if ( ! empty( $story['adsense_client'] ) && ! empty( $story['adsense_slot'] ) ) : ?>
		<script async custom-element="amp-story-auto-ads" src="https://cdn.ampproject.org/v0/amp-story-auto-ads-0.1.js"></script>
	<?php endif; ?>
	<?php if ( ! empty( $story['ga4_id'] ) ) : ?>
		<script async custom-element="amp-analytics" src="https://cdn.ampproject.org/v0/amp-analytics-0.1.js"></script>
	<?php endif; ?>
	<style amp-boilerplate>body{-webkit-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-moz-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-ms-animation:-amp-start 8s steps(1,end) 0s 1 normal both;animation:-amp-start 8s steps(1,end) 0s 1 normal both}@-webkit-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-moz-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-ms-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-o-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}</style><noscript><style amp-boilerplate>body{-webkit-animation:none;-moz-animation:none;-ms-animation:none;animation:none}</style></noscript>
	<style amp-custom>
		/* Guarantee story body and container are visible in preview */
		body {
			visibility: visible !important;
			-webkit-animation: none !important;
			animation: none !important;
		}
		amp-story {
			font-family: <?php echo $font_body_css; ?>;
			color: #ffffff;
			background-color: <?php echo esc_attr( $bg_color ); ?>;
			-webkit-font-smoothing: antialiased;
			-moz-osx-font-smoothing: grayscale;
		}
		/* Desktop Navigation Controls in AMP Story (Strictly vertically centered at 50%, completely safe from top badges and notch) */
		.i-amphtml-story-button-move-prev,
		.i-amphtml-story-button-move-next {
			position: absolute !important;
			top: 50% !important;
			bottom: auto !important;
			transform: translateY(-50%) !important;
			z-index: 9999 !important;
			opacity: 0.9 !important;
			width: 44px !important;
			height: 44px !important;
			border-radius: 50% !important;
			box-shadow: 0 4px 16px rgba(0, 0, 0, 0.4) !important;
			transition: opacity 0.18s ease, transform 0.18s ease !important;
		}
		.i-amphtml-story-button-move-prev {
			left: 18px !important;
		}
		.i-amphtml-story-button-move-next {
			right: 18px !important;
		}
		.i-amphtml-story-button-move-prev:hover,
		.i-amphtml-story-button-move-next:hover {
			opacity: 1 !important;
			transform: translateY(-50%) scale(1.1) !important;
		}
		amp-story-grid-layer[template="vertical"],
		amp-story-grid-layer.story-layer-bottom {
			align-content: end !important;
			justify-content: flex-end !important;
			padding: 0 !important;
		}
		.story-safe-container {
			box-sizing: border-box;
			width: 100%;
			margin-top: auto !important; /* Strict vertical bottom alignment */
			padding: 180px 28px 85px 28px; /* Enforces Safe Zone boundaries (180px top, 85px bottom clearance) */
			display: flex;
			flex-direction: column;
			justify-content: flex-end;
			position: relative;
			z-index: 2;
		}
		.scrim-overlay {
			position: absolute;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			pointer-events: none;
			z-index: 1;
		}
		.story-badge {
			display: inline-flex;
			align-items: center;
			align-self: flex-start;
			padding: 5px 13px;
			border-radius: 999px;
			font-size: 10.5px;
			font-weight: 700;
			letter-spacing: 1.2px;
			text-transform: uppercase;
			color: #ffffff;
			background: rgba(255, 255, 255, 0.12);
			border: 1px solid rgba(255, 255, 255, 0.2);
			backdrop-filter: blur(14px);
			-webkit-backdrop-filter: blur(14px);
			margin-bottom: 12px;
			box-shadow: 0 4px 16px rgba(0,0,0,0.25);
		}
		.story-stat-box {
			display: flex;
			align-items: baseline;
			gap: 10px;
			margin-bottom: 10px;
		}
		.story-stat-number {
			font-family: <?php echo $font_title_css; ?>;
			font-size: <?php echo esc_attr( $stat_size_global ); ?>px;
			font-weight: 800;
			line-height: 1;
			letter-spacing: -0.03em;
			color: <?php echo esc_attr( $accent_color ); ?>;
			text-shadow: 0 2px 14px rgba(0,0,0,0.6);
		}
		.story-stat-label {
			font-size: 12.5px;
			font-weight: 600;
			color: rgba(255,255,255,0.88);
			text-transform: uppercase;
			letter-spacing: 0.8px;
		}
		.story-headline {
			font-family: <?php echo $font_title_css; ?>;
			font-size: <?php echo esc_attr( $headline_size_global ); ?>px;
			font-weight: 800;
			line-height: <?php echo esc_attr( $headline_line_height ); ?>;
			letter-spacing: <?php echo esc_attr( $headline_tracking ); ?>;
			margin: 0 0 10px 0;
			color: #ffffff;
			text-shadow: 0 2px 16px rgba(0,0,0,0.75);
			word-wrap: break-word;
		}
		.story-body {
			font-family: <?php echo $font_body_css; ?>;
			font-size: <?php echo esc_attr( $body_size_global ); ?>px;
			line-height: 1.48;
			letter-spacing: -0.01em;
			color: rgba(255,255,255,0.92);
			margin: 0;
			text-shadow: 0 1px 8px rgba(0,0,0,0.7);
		}
		.outlink-button {
			display: inline-flex;
			align-items: center;
			justify-content: center;
			padding: 12px 24px;
			border-radius: 50px;
			background: linear-gradient(135deg, <?php echo esc_attr( $accent_color ); ?>, <?php echo esc_attr( $secondary_color ); ?>);
			color: #080811;
			text-decoration: none;
			font-weight: 700;
			font-size: 13.5px;
			letter-spacing: 0.3px;
			margin-top: 16px;
			box-shadow: 0 8px 24px rgba(0,0,0,0.35);
		}
		@media (max-width: 900px) {
			.i-amphtml-story-button-container {
				display: none !important;
			}
		}
	</style>
</head>
<body>
	<amp-story standalone=""
		title="<?php echo esc_attr( $title ); ?>"
		publisher="<?php echo esc_attr( $publisher ); ?>"
		publisher-logo-src="<?php echo esc_url( $publisher_logo ); ?>"
		poster-portrait-src="<?php echo esc_url( $poster_portrait ); ?>"
		<?php if ( ! empty( $story['background_audio'] ) ) : ?>
		background-audio="<?php echo esc_url( $story['background_audio'] ); ?>"
		<?php endif; ?>>

		<?php if ( ! empty( $story['adsense_client'] ) && ! empty( $story['adsense_slot'] ) ) : ?>
		<amp-story-auto-ads>
			<script type="application/json">
			{
				"ad-attributes": {
					"type": "adsense",
					"data-ad-client": "<?php echo esc_attr( $story['adsense_client'] ); ?>",
					"data-ad-slot": "<?php echo esc_attr( $story['adsense_slot'] ); ?>"
				}
			}
			</script>
		</amp-story-auto-ads>
		<?php endif; ?>

		<?php if ( ! empty( $story['ga4_id'] ) ) : ?>
		<amp-analytics type="googleanalytics" id="analytics-ga4">
			<script type="application/json">
			{
				"vars": {
					"account": "<?php echo esc_attr( $story['ga4_id'] ); ?>"
				},
				"triggers": {
					"trackPageview": {
						"on": "story-page-visible",
						"request": "pageview"
					}
				}
			}
			</script>
		</amp-analytics>
		<?php endif; ?>

		<?php
		if ( ! empty( $story['pages'] ) && is_array( $story['pages'] ) ) :
			foreach ( $story['pages'] as $index => $page ) :
				$page_id       = ! empty( $page['id'] ) ? esc_attr( sanitize_key( $page['id'] ) ) : 'page-' . ( $index + 1 );
				$bg_image      = ! empty( $page['bg_image'] ) ? self::sanitize_safe_url( $page['bg_image'], $poster_portrait ) : $poster_portrait;
				$raw_scrim     = ! empty( $page['scrim'] ) ? $page['scrim'] : ( ! empty( $story['global_scrim'] ) ? $story['global_scrim'] : 'linear-gradient(180deg, rgba(8,8,17,0.3) 0%, rgba(8,8,17,0.92) 80%)' );
				$scrim         = preg_replace( '/[^a-zA-Z0-9,()_ %.-]/', '', $raw_scrim );
				$raw_animation = ! empty( $page['animation'] ) ? sanitize_key( $page['animation'] ) : 'fly-in-bottom';
				$animation     = ( 'zoom-in' === $raw_animation || 'twirl-in' === $raw_animation ) ? 'fly-in-bottom' : $raw_animation;
				$animation     = esc_attr( $animation );
				$anim_duration = ! empty( $page['anim_duration'] ) ? floatval( $page['anim_duration'] ) : 0.8;
				$anim_delay    = ! empty( $page['anim_delay'] ) ? floatval( $page['anim_delay'] ) : 0.1;
				$duration      = ! empty( $page['duration'] ) ? absint( $page['duration'] ) : 8;
				$badge         = ! empty( $page['badge'] ) ? esc_html( wp_strip_all_tags( $page['badge'] ) ) : '';
				$badge_bg      = ! empty( $page['badge_bg'] ) ? sanitize_hex_color( $page['badge_bg'] ) : ( ! empty( $story['badge_bg'] ) ? sanitize_hex_color( $story['badge_bg'] ) : $accent_color );
				$badge_color   = ! empty( $page['badge_color'] ) ? sanitize_hex_color( $page['badge_color'] ) : ( ! empty( $story['badge_color'] ) ? sanitize_hex_color( $story['badge_color'] ) : '#ffffff' );
				$headline      = ! empty( $page['headline'] ) ? esc_html( wp_strip_all_tags( $page['headline'] ) ) : '';
				$headline_size = ! empty( $page['headline_size'] ) ? absint( $page['headline_size'] ) : $headline_size_global;
				$headline_color= ! empty( $page['headline_color'] ) ? sanitize_hex_color( $page['headline_color'] ) : ( ! empty( $story['headline_color'] ) ? sanitize_hex_color( $story['headline_color'] ) : '#ffffff' );
				$text_align    = ! empty( $page['text_align'] ) && in_array( $page['text_align'], array( 'left', 'center', 'right' ), true ) ? $page['text_align'] : 'left';
				$body          = ! empty( $page['body'] ) ? esc_html( wp_strip_all_tags( $page['body'] ) ) : '';
				$body_color    = ! empty( $page['body_color'] ) ? sanitize_hex_color( $page['body_color'] ) : 'rgba(255,255,255,0.92)';
				$stat_num      = ! empty( $page['stat_number'] ) ? esc_html( wp_strip_all_tags( $page['stat_number'] ) ) : '';
				$stat_color    = ! empty( $page['stat_color'] ) ? sanitize_hex_color( $page['stat_color'] ) : $accent_color;
				$stat_lbl      = ! empty( $page['stat_label'] ) ? esc_html( wp_strip_all_tags( $page['stat_label'] ) ) : '';
				$cta_text      = ! empty( $page['cta_text'] ) ? esc_html( wp_strip_all_tags( $page['cta_text'] ) ) : '';
				$cta_url       = ! empty( $page['cta_url'] ) ? self::sanitize_safe_url( $page['cta_url'], '#' ) : '';
				?>
				<amp-story-page id="<?php echo esc_attr( $page_id ); ?>" auto-advance-after="<?php echo esc_attr( $duration ); ?>s">
					<!-- Fullbleed Media Layer (1080x1920) -->
					<amp-story-grid-layer template="fill">
						<amp-img src="<?php echo esc_url( $bg_image ); ?>"
							width="1080" height="1920"
							layout="responsive"
							alt="<?php echo esc_attr( $headline ); ?>">
						</amp-img>
						<div class="scrim-overlay" style="background: <?php echo esc_attr( $scrim ); ?>;"></div>
					</amp-story-grid-layer>

					<!-- Safe Zone Content Layer (Strict Bottom Alignment) -->
					<amp-story-grid-layer template="vertical" class="story-layer-bottom">
						<div class="story-safe-container" style="text-align: <?php echo esc_attr( $text_align ); ?>; <?php echo 'center' === $text_align ? 'align-items: center;' : ''; ?>">
							<?php if ( $badge ) : ?>
								<div class="story-badge" animate-in="<?php echo esc_attr( $animation ); ?>" animate-in-duration="<?php echo esc_attr( $anim_duration ); ?>s" animate-in-delay="<?php echo esc_attr( $anim_delay ); ?>s" style="background-color: <?php echo esc_attr( $badge_bg ); ?>; color: <?php echo esc_attr( $badge_color ); ?>;">
									<?php echo $badge; ?>
								</div>
							<?php endif; ?>

							<?php if ( $stat_num ) : ?>
								<div class="story-stat-box" animate-in="<?php echo esc_attr( $animation ); ?>" animate-in-duration="<?php echo esc_attr( $anim_duration ); ?>s" animate-in-delay="<?php echo esc_attr( $anim_delay + 0.1 ); ?>s">
									<div class="story-stat-number" style="font-size: <?php echo esc_attr( $stat_size_global ); ?>px; color: <?php echo esc_attr( $stat_color ); ?>;"><?php echo $stat_num; ?></div>
									<?php if ( $stat_lbl ) : ?>
										<div class="story-stat-label"><?php echo $stat_lbl; ?></div>
									<?php endif; ?>
								</div>
							<?php endif; ?>

							<?php if ( $headline ) : ?>
								<h1 class="story-headline" animate-in="<?php echo esc_attr( $animation ); ?>" animate-in-duration="<?php echo esc_attr( $anim_duration ); ?>s" animate-in-delay="<?php echo esc_attr( $anim_delay + 0.15 ); ?>s" style="font-size: <?php echo esc_attr( $headline_size ); ?>px; color: <?php echo esc_attr( $headline_color ); ?>; text-align: <?php echo esc_attr( $text_align ); ?>; letter-spacing: <?php echo esc_attr( $headline_tracking ); ?>; line-height: <?php echo esc_attr( $headline_line_height ); ?>;">
									<?php echo $headline; ?>
								</h1>
							<?php endif; ?>

							<?php if ( $body ) : ?>
								<p class="story-body" animate-in="<?php echo esc_attr( $animation ); ?>" animate-in-duration="<?php echo esc_attr( $anim_duration ); ?>s" animate-in-delay="<?php echo esc_attr( $anim_delay + 0.25 ); ?>s" style="font-size: <?php echo esc_attr( $body_size_global ); ?>px; color: <?php echo esc_attr( $body_color ); ?>; text-align: <?php echo esc_attr( $text_align ); ?>;">
									<?php echo $body; ?>
								</p>
							<?php endif; ?>

							<?php if ( $cta_text && $cta_url && '#' !== $cta_url ) : ?>
								<div animate-in="fly-in-bottom" animate-in-duration="<?php echo esc_attr( $anim_duration ); ?>s" animate-in-delay="<?php echo esc_attr( $anim_delay + 0.35 ); ?>s">
									<a href="<?php echo esc_url( $cta_url ); ?>" class="outlink-button" target="_blank" rel="noopener">
										<?php echo $cta_text; ?> &rarr;
									</a>
								</div>
							<?php endif; ?>
						</div>
					</amp-story-grid-layer>

					<?php if ( $cta_url && '#' !== $cta_url ) : ?>
						<amp-story-page-outlink layout="nodisplay">
							<a href="<?php echo esc_url( $cta_url ); ?>"><?php echo $cta_text ? $cta_text : 'Learn More'; ?></a>
						</amp-story-page-outlink>
					<?php endif; ?>
				</amp-story-page>
			<?php endforeach; ?>
		<?php endif; ?>
	</amp-story>
</body>
</html>
		<?php
		return ob_get_clean();
	}

	/**
	 * Build JSON structure for post_content_filtered (Google Web Stories Editor source)
	 *
	 * @param array $story Story configuration
	 * @return string JSON serialized data
	 */
	public static function generate_editor_json( $story ) {
		$pages_data = array();

		if ( ! empty( $story['pages'] ) && is_array( $story['pages'] ) ) {
			foreach ( $story['pages'] as $idx => $p ) {
				$page_id = ! empty( $p['id'] ) ? sanitize_key( $p['id'] ) : 'page-' . uniqid();
				$bg_img  = ! empty( $p['bg_image'] ) ? self::sanitize_safe_url( $p['bg_image'], '' ) : self::sanitize_safe_url( $story['poster_image'], '' );

				$elements = array(
					// Background Image Element
					array(
						'id'             => 'elem-bg-' . $idx,
						'type'           => 'image',
						'x'              => 0,
						'y'              => 0,
						'width'          => 412,
						'height'         => 732,
						'rotationAngle'  => 0,
						'isBackground'   => true,
						'resource'       => array(
							'type'    => 'image',
							'mimeType'=> 'image/jpeg',
							'src'     => $bg_img,
							'width'   => 1080,
							'height'  => 1920,
							'alt'     => ! empty( $p['headline'] ) ? sanitize_text_field( $p['headline'] ) : 'Background',
						),
					),
					// Headline Element
					array(
						'id'             => 'elem-text-' . $idx,
						'type'           => 'text',
						'x'              => 32,
						'y'              => 380,
						'width'          => 348,
						'height'         => 140,
						'rotationAngle'  => 0,
						'content'        => '<p style="color:#ffffff;font-weight:800;font-size:26px;">' . esc_html( wp_strip_all_tags( $p['headline'] ) ) . '</p>',
						'font'           => array(
							'family'  => ! empty( $story['font_family_title'] ) ? sanitize_text_field( $story['font_family_title'] ) : 'Space Grotesk',
							'service' => 'fonts.google.com',
						),
						'style'          => array(
							'color'      => '#ffffff',
							'fontWeight' => 800,
						),
					),
					// Body Element
					array(
						'id'             => 'elem-body-' . $idx,
						'type'           => 'text',
						'x'              => 32,
						'y'              => 530,
						'width'          => 348,
						'height'         => 100,
						'rotationAngle'  => 0,
						'content'        => '<p style="color:#f3f4f6;font-size:14px;line-height:1.4;">' . esc_html( wp_strip_all_tags( $p['body'] ) ) . '</p>',
						'font'           => array(
							'family'  => ! empty( $story['font_family_body'] ) ? sanitize_text_field( $story['font_family_body'] ) : 'Inter',
							'service' => 'fonts.google.com',
						),
						'style'          => array(
							'color'      => '#f3f4f6',
							'fontWeight' => 400,
						),
					),
				);

				$pages_data[] = array(
					'id'         => $page_id,
					'elements'   => $elements,
					'animations' => array(
						array(
							'id'      => 'anim-' . $idx,
							'target'  => 'elem-text-' . $idx,
							'type'    => 'fly-in-bottom',
							'duration'=> 600,
							'delay'   => 150,
						),
					),
					'background' => array(
						'color' => array(
							'r' => 8,
							'g' => 8,
							'b' => 17,
						),
					),
				);
			}
		}

		$payload = array(
			'version'   => 38,
			'pages'     => $pages_data,
			'story'     => array(
				'title'          => ! empty( $story['title'] ) ? sanitize_text_field( $story['title'] ) : 'Web Story',
				'publisher'      => ! empty( $story['publisher'] ) ? sanitize_text_field( $story['publisher'] ) : sanitize_text_field( get_bloginfo( 'name' ) ),
				'publisherLogo'  => ! empty( $story['publisher_logo'] ) ? self::sanitize_safe_url( $story['publisher_logo'], '' ) : '',
				'poster'         => ! empty( $story['poster_image'] ) ? self::sanitize_safe_url( $story['poster_image'], '' ) : '',
			),
		);

		return wp_json_encode( $payload );
	}

	/**
	 * Create or update a WordPress Web Story post
	 *
	 * @param array $story Story configuration
	 * @param string $status 'publish' or 'draft'
	 * @return int|WP_Error Post ID on success, WP_Error on failure
	 */
	public static function create_web_story_post( $story, $status = 'draft' ) {
		$post_type = post_type_exists( 'web-story' ) ? 'web-story' : 'post';

		$amp_html     = self::generate_amp_html( $story );
		$editor_json  = self::generate_editor_json( $story );
		$post_title   = ! empty( $story['title'] ) ? sanitize_text_field( $story['title'] ) : 'Sick Web Story';

		// Properly slash post content and JSON payload for wp_insert_post
		$post_arr = array(
			'post_title'            => wp_slash( $post_title ),
			'post_type'             => $post_type,
			'post_status'           => 'publish' === $status ? 'publish' : 'draft',
			'post_content'          => wp_slash( $amp_html ),
			'post_content_filtered' => wp_slash( $editor_json ),
			'post_author'           => get_current_user_id() ? get_current_user_id() : 1,
		);

		// Temporarily unhook KSES filters so valid Google AMP components (<amp-story>) are not stripped
		if ( function_exists( 'kses_remove_filters' ) ) {
			kses_remove_filters();
		}

		$post_id = wp_insert_post( $post_arr, true );

		// If inserting into web-story failed (e.g. capability or taxonomy restriction), automatically retry with standard post
		if ( is_wp_error( $post_id ) && 'web-story' === $post_type ) {
			$post_arr['post_type'] = 'post';
			$post_id = wp_insert_post( $post_arr, true );
		}

		if ( function_exists( 'kses_init_filters' ) ) {
			kses_init_filters();
		}

		if ( ! $post_id || is_wp_error( $post_id ) ) {
			return is_wp_error( $post_id ) ? $post_id : new WP_Error( 'post_creation_failed', __( 'Could not create story post in database.', 'web-stories-templates-studio' ) );
		}

		// Mark post as a Web Story so the player renders it regardless of post type
		update_post_meta( $post_id, '_wsts_is_web_story', 1 );

		// Assign Category / Taxonomy to Story (Compatible with Google Web Stories & Standard WP)
		$cat_to_set = ! empty( $story['story_category'] ) ? $story['story_category'] : ( ! empty( $story['category_label'] ) ? $story['category_label'] : ( ! empty( $story['category'] ) ? $story['category'] : '' ) );
		if ( ! empty( $cat_to_set ) ) {
			if ( taxonomy_exists( 'web_story_category' ) ) {
				wp_set_object_terms( $post_id, sanitize_text_field( $cat_to_set ), 'web_story_category' );
			}
			if ( taxonomy_exists( 'category' ) ) {
				$term = term_exists( $cat_to_set, 'category' );
				$term_id = 0;
				if ( is_array( $term ) && ! empty( $term['term_id'] ) ) {
					$term_id = (int) $term['term_id'];
				} elseif ( is_numeric( $term ) ) {
					$term_id = (int) $term;
				} else {
					$inserted = wp_insert_term( $cat_to_set, 'category' );
					if ( is_array( $inserted ) && ! empty( $inserted['term_id'] ) ) {
						$term_id = (int) $inserted['term_id'];
					} elseif ( is_wp_error( $inserted ) && isset( $inserted->error_data['term_exists'] ) ) {
						$term_id = (int) $inserted->error_data['term_exists'];
					}
				}
				if ( $term_id > 0 ) {
					wp_set_object_terms( $post_id, $term_id, 'category' );
				}
			}
		}

		// Store custom story meta safely
		update_post_meta( $post_id, '_wsts_template_id', ! empty( $story['template_id'] ) ? sanitize_key( $story['template_id'] ) : '' );
		update_post_meta( $post_id, '_wsts_publisher_name', ! empty( $story['publisher'] ) ? sanitize_text_field( $story['publisher'] ) : '' );
		update_post_meta( $post_id, '_wsts_publisher_logo', ! empty( $story['publisher_logo'] ) ? self::sanitize_safe_url( $story['publisher_logo'], '' ) : '' );
		update_post_meta( $post_id, '_wsts_poster_image', ! empty( $story['poster_image'] ) ? self::sanitize_safe_url( $story['poster_image'], '' ) : '' );
		update_post_meta( $post_id, '_wsts_custom_config', $story );

		// Web Stories for WordPress official compatibility meta
		update_post_meta( $post_id, 'web_stories_publisher_logo_url', ! empty( $story['publisher_logo'] ) ? self::sanitize_safe_url( $story['publisher_logo'], '' ) : '' );

		// SEO & Google Discover Metadata
		update_post_meta( $post_id, '_wsts_seo_title', ! empty( $story['seo_title'] ) ? sanitize_text_field( $story['seo_title'] ) : $post_title );
		update_post_meta( $post_id, '_wsts_seo_desc', ! empty( $story['seo_desc'] ) ? sanitize_textarea_field( $story['seo_desc'] ) : '' );
		update_post_meta( $post_id, '_wsts_canonical_url', ! empty( $story['canonical_url'] ) ? self::sanitize_safe_url( $story['canonical_url'], '' ) : '' );
		update_post_meta( $post_id, '_wsts_schema_type', ! empty( $story['schema_type'] ) ? sanitize_text_field( $story['schema_type'] ) : 'NewsArticle' );
		update_post_meta( $post_id, '_wsts_og_image', ! empty( $story['og_image'] ) ? self::sanitize_safe_url( $story['og_image'], '' ) : '' );

		// Monetization, Analytics & Ambient Audio Metadata
		update_post_meta( $post_id, '_wsts_adsense_client', ! empty( $story['adsense_client'] ) ? sanitize_text_field( $story['adsense_client'] ) : '' );
		update_post_meta( $post_id, '_wsts_adsense_slot', ! empty( $story['adsense_slot'] ) ? sanitize_text_field( $story['adsense_slot'] ) : '' );
		update_post_meta( $post_id, '_wsts_ga4_id', ! empty( $story['ga4_id'] ) ? sanitize_text_field( $story['ga4_id'] ) : '' );
		update_post_meta( $post_id, '_wsts_background_audio', ! empty( $story['background_audio'] ) ? esc_url_raw( $story['background_audio'] ) : '' );

		return $post_id;
	}
}
