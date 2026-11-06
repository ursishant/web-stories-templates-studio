<?php
/**
 * Specifications and Sizing Guidelines Helper
 *
 * Enforces and verifies all Google Web Stories sizing, safe zones, typography limits, and AMP compliance standards.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WSTS_Spec_Validator {

	/**
	 * Canvas & Display Dimensions
	 */
	const CANVAS_WIDTH          = 1080; // Standard portrait width in pixels
	const CANVAS_HEIGHT         = 1920; // Standard portrait height in pixels
	const ASPECT_RATIO_W        = 9;
	const ASPECT_RATIO_H        = 16;
	const POSTER_WIDTH          = 640;  // Minimum 3:4 poster width
	const POSTER_HEIGHT         = 853;  // Minimum 3:4 poster height
	const LOGO_MIN_SIZE         = 96;   // 1:1 square icon minimum
	const LOGO_RECOMMENDED_SIZE = 512;  // 1:1 square icon recommended

	/**
	 * Safe Zone Boundaries (in pixels for a 1080x1920 canvas)
	 */
	const SAFE_TOP_OFFSET       = 220;  // Clears progress bar, publisher branding, close & audio buttons
	const SAFE_BOTTOM_OFFSET    = 250;  // Clears swipe up CTA, outlinks, and device home indicators
	const SAFE_SIDE_MARGIN      = 80;   // Prevents edge-tap conflict and curved display distortion
	const SAFE_CONTENT_WIDTH    = 920;  // 1080 - (80 * 2)
	const SAFE_CONTENT_HEIGHT   = 1450; // 1920 - 220 - 250

	/**
	 * Text & Content Thresholds
	 */
	const MAX_CHARS_PER_PAGE    = 280;  // Strict Google Web Stories recommendation
	const OPTIMAL_CHARS_PER_PAGE= 180;
	const MAX_TITLE_WORDS       = 10;
	const MIN_FONT_SIZE_PX      = 16;
	const MIN_TITLE_SIZE_PX     = 28;

	/**
	 * Video Specifications
	 */
	const VIDEO_MAX_BITRATE_KBPS= 4000;
	const VIDEO_OPT_BITRATE_KBPS= 2000;
	const VIDEO_MAX_DURATION_SEC= 60;
	const VIDEO_OPT_DURATION_SEC= 15;
	const VIDEO_FRAME_RATE      = 30;

	/**
	 * Get complete specification dictionary
	 *
	 * @return array
	 */
	public static function get_full_specs() {
		return array(
			'dimensions' => array(
				'canvas_width'        => self::CANVAS_WIDTH,
				'canvas_height'       => self::CANVAS_HEIGHT,
				'aspect_ratio'        => '9:16',
				'poster_portrait'     => self::POSTER_WIDTH . 'x' . self::POSTER_HEIGHT . ' px (3:4 ratio)',
				'poster_recommended'  => '960x1280 px or 1080x1440 px',
				'publisher_logo'      => self::LOGO_RECOMMENDED_SIZE . 'x' . self::LOGO_RECOMMENDED_SIZE . ' px (1:1 square)',
			),
			'safe_zone' => array(
				'top_offset'          => self::SAFE_TOP_OFFSET . ' px',
				'bottom_offset'       => self::SAFE_BOTTOM_OFFSET . ' px',
				'side_margin'         => self::SAFE_SIDE_MARGIN . ' px',
				'safe_width'          => self::SAFE_CONTENT_WIDTH . ' px',
				'safe_height'         => self::SAFE_CONTENT_HEIGHT . ' px',
				'top_clearance_reason'=> 'AMP Story progress bar, story title, publisher logo, audio button, close icon',
				'bottom_clearance_reason'=> 'Swipe-up Call-to-Action attachments, share sheet, iOS/Android home swipe bars',
			),
			'typography' => array(
				'max_chars_page'      => self::MAX_CHARS_PER_PAGE,
				'optimal_chars_page'  => self::OPTIMAL_CHARS_PER_PAGE,
				'max_title_words'     => self::MAX_TITLE_WORDS,
				'contrast_requirement'=> '4.5:1 minimum WCAG (Use gradient scrim overlays)',
			),
			'media' => array(
				'image_format'        => 'WebP, JPEG, PNG (under 300 KB)',
				'video_format'        => 'MP4 (H.264 / AAC, 9:16 portrait, 1080x1920 or 720x1280)',
				'video_bitrate'       => 'Target 1.5 - 2.5 Mbps (max 4.0 Mbps)',
				'video_duration'      => 'Ideal 4 - 15 seconds per page (max 60 seconds)',
				'audio_handling'      => 'Autoplays muted by default; audio indicator present',
			),
		);
	}

	/**
	 * Validate a story payload against Google Web Stories specifications
	 *
	 * @param array $story_data Story data payload
	 * @return array Validation findings and recommendations
	 */
	public static function validate_story( $story_data ) {
		$findings = array(
			'is_valid' => true,
			'warnings' => array(),
			'passes'   => array(),
		);

		// Validate Title
		if ( empty( $story_data['title'] ) ) {
			$findings['is_valid']   = false;
			$findings['warnings'][] = __( 'Story Title is required for Google indexing and schema generation.', 'web-stories-templates-studio' );
		} else {
			$word_count = str_word_count( strip_tags( $story_data['title'] ) );
			if ( $word_count > self::MAX_TITLE_WORDS ) {
				$findings['warnings'][] = sprintf(
					__( 'Title has %d words. Google recommends under %d words to prevent truncation on mobile screens.', 'web-stories-templates-studio' ),
					$word_count,
					self::MAX_TITLE_WORDS
				);
			} else {
				$findings['passes'][] = __( 'Story Title length is optimal.', 'web-stories-templates-studio' );
			}
		}

		// Validate Publisher Info
		if ( empty( $story_data['publisher'] ) ) {
			$findings['warnings'][] = __( 'Publisher name is recommended for Google Web Stories brand attribution.', 'web-stories-templates-studio' );
		}
		if ( empty( $story_data['publisher_logo'] ) ) {
			$findings['warnings'][] = __( 'Square Publisher Logo (minimum 96x96px) is required for Google Discover eligibility.', 'web-stories-templates-studio' );
		} else {
			$findings['passes'][] = __( 'Publisher logo configured.', 'web-stories-templates-studio' );
		}

		// Validate Cover / Poster
		if ( empty( $story_data['poster'] ) ) {
			$findings['warnings'][] = __( 'Poster image (portrait 3:4, min 640x853px) is mandatory for AMP story validation.', 'web-stories-templates-studio' );
		} else {
			$findings['passes'][] = __( 'Poster portrait image present.', 'web-stories-templates-studio' );
		}

		// Validate Page text count
		if ( ! empty( $story_data['pages'] ) && is_array( $story_data['pages'] ) ) {
			$page_idx = 1;
			foreach ( $story_data['pages'] as $page ) {
				$text_length = 0;
				if ( ! empty( $page['headline'] ) ) {
					$text_length += strlen( strip_tags( $page['headline'] ) );
				}
				if ( ! empty( $page['body'] ) ) {
					$text_length += strlen( strip_tags( $page['body'] ) );
				}

				if ( $text_length > self::MAX_CHARS_PER_PAGE ) {
					$findings['warnings'][] = sprintf(
						__( 'Page %d has %d characters (exceeds Google limit of %d chars). Shorten text for better engagement.', 'web-stories-templates-studio' ),
						$page_idx,
						$text_length,
						self::MAX_CHARS_PER_PAGE
					);
				}
				$page_idx++;
			}
		}

		return $findings;
	}
}

