<?php
/**
 * Admin Studio Controller
 *
 * Renders the modern WordPress Admin UI:
 * - Curated Template Gallery
 * - Real-time Phone Simulator & Interactive Customizer
 * - Safe-Zone Visual Inspector
 * - Google Web Stories Sizes & Compliance Guide
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WSTS_Admin_Studio {

	private static $instance = null;
	private $registered_hooks = array();

	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'admin_menu', array( $this, 'register_admin_menus' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
	}

	/**
	 * Register menu items
	 */
	public function register_admin_menus() {
		$hooks = array();

		// 1. If Google's official Web Stories plugin or web-story CPT is installed, nest directly inside Stories menu
		if ( post_type_exists( 'web-story' ) ) {
			$official_sub = add_submenu_page(
				'edit.php?post_type=web-story',
				__( 'Web Stories Template Studio', 'web-stories-templates-studio' ),
				__( '✨ Template Studio', 'web-stories-templates-studio' ),
				'edit_posts',
				'web-stories-templates',
				array( $this, 'render_studio_page' ),
				3
			);
			if ( $official_sub ) {
				$hooks[] = $official_sub;
			}
		}

		// 2. Standalone Dedicated Menu
		$main_page = add_menu_page(
			__( 'Web Stories Studio', 'web-stories-templates-studio' ),
			__( 'Story Studio', 'web-stories-templates-studio' ),
			'edit_posts',
			'web-stories-templates',
			array( $this, 'render_studio_page' ),
			'dashicons-slides',
			26
		);
		$hooks[] = $main_page;

		$tpl_sub = add_submenu_page(
			'web-stories-templates',
			__( 'Templates', 'web-stories-templates-studio' ),
			__( 'Template Library', 'web-stories-templates-studio' ),
			'edit_posts',
			'web-stories-templates',
			array( $this, 'render_studio_page' )
		);
		$hooks[] = $tpl_sub;

		$guide_sub = add_submenu_page(
			'web-stories-templates',
			__( 'Sizes & Specs Guide', 'web-stories-templates-studio' ),
			__( 'Sizes & Specs Guide', 'web-stories-templates-studio' ),
			'edit_posts',
			'web-stories-sizes-guide',
			array( $this, 'render_sizes_guide_page' )
		);
		$hooks[] = $guide_sub;

		$this->registered_hooks = $hooks;
	}

	/**
	 * Enqueue admin CSS and JS strictly on our plugin pages
	 */
	public function enqueue_admin_assets( $hook ) {
		// Strict page-level scoping: never enqueue on other admin screens
		if ( ! in_array( $hook, $this->registered_hooks, true ) && false === strpos( $hook, 'web-stories-templates' ) && false === strpos( $hook, 'web-stories-sizes-guide' ) ) {
			return;
		}

		wp_enqueue_media();

		// Enqueue Google Fonts for Admin Studio live typography preview
		wp_enqueue_style(
			'wsts-google-fonts',
			'https://fonts.googleapis.com/css2?family=Cinzel:wght@600;800&family=Inter:wght@400;500;600;700&family=Montserrat:wght@600;700;800&family=Oswald:wght@600;700&family=Playfair+Display:ital,wght@0,600;0,800;1,400&family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Space+Grotesk:wght@500;700;800&family=Syne:wght@700;800&display=swap',
			array(),
			null
		);

		$css_file = WSTS_PLUGIN_DIR . 'assets/css/admin-studio.css';
		$js_file  = WSTS_PLUGIN_DIR . 'assets/js/admin-studio.js';
		$css_ver  = file_exists( $css_file ) ? filemtime( $css_file ) : WSTS_VERSION;
		$js_ver   = file_exists( $js_file ) ? filemtime( $js_file ) : WSTS_VERSION;

		wp_enqueue_style(
			'wsts-admin-css',
			WSTS_PLUGIN_URL . 'assets/css/admin-studio.css',
			array( 'wsts-google-fonts' ),
			$css_ver
		);

		wp_enqueue_script(
			'wsts-admin-js',
			WSTS_PLUGIN_URL . 'assets/js/admin-studio.js',
			array( 'jquery' ),
			$js_ver,
			true
		);

		$registry  = WSTS_Template_Registry::instance();
		$templates = $registry->get_all();

		wp_localize_script(
			'wsts-admin-js',
			'wstsStudio',
			array(
				'ajaxUrl'             => admin_url( 'admin-ajax.php' ),
				'nonce'               => wp_create_nonce( 'wsts_studio_nonce' ),
				'templates'           => $templates,
				'hasWebStories'       => class_exists( '\Google\Web_Stories\Plugin' ) || post_type_exists( 'web-story' ),
				'webStoriesEditorUrl' => admin_url( 'post.php?action=edit&post=' ),
				'specs'               => WSTS_Spec_Validator::get_full_specs(),
				'brandDesignSystem'   => get_option( 'wsts_brand_design_system', null ),
				'siteLogo'            => self::get_website_logo(),
				'siteName'            => self::get_website_name(),
				'pluginUrl'           => WSTS_PLUGIN_URL,
				'defaultLogo'         => WSTS_PLUGIN_URL . 'assets/images/default-logo.png',
				'hasUnsplashKey'      => ! empty( get_option( 'wsts_unsplash_access_key', '' ) ),
				'hasPexelsKey'        => ! empty( get_option( 'wsts_pexels_api_key', '' ) ),
				'favorites'           => is_array( get_user_meta( get_current_user_id(), 'wsts_favorite_templates', true ) ) ? array_values( get_user_meta( get_current_user_id(), 'wsts_favorite_templates', true ) ) : array(),
				'defaultTemplate'     => (string) get_user_meta( get_current_user_id(), 'wsts_default_template', true ),
				'homeUrl'             => home_url( '/' ),
			)
		);
	}

	/**
	 * Automatically fetch website logo from WordPress site icon or theme custom logo
	 */
	public static function get_website_logo() {
		// 1. WordPress Site Icon (Google recommended size 512x512)
		if ( function_exists( 'get_site_icon_url' ) ) {
			$site_icon = get_site_icon_url( 512 );
			if ( ! empty( $site_icon ) ) {
				return $site_icon;
			}
		}

		// 2. Custom Logo set in Theme Customizer
		if ( function_exists( 'get_theme_mod' ) && function_exists( 'wp_get_attachment_image_url' ) ) {
			$custom_logo_id = get_theme_mod( 'custom_logo' );
			if ( $custom_logo_id ) {
				$logo_url = wp_get_attachment_image_url( $custom_logo_id, 'full' );
				if ( ! empty( $logo_url ) ) {
					return $logo_url;
				}
			}
		}

		// 3. Bundled high-res vector/PNG fallback
		return WSTS_PLUGIN_URL . 'assets/images/default-logo.png';
	}

	/**
	 * Get website / publisher name
	 */
	public static function get_website_name() {
		$name = get_bloginfo( 'name' );
		return ! empty( $name ) ? $name : 'Web Stories';
	}

	/**
	 * Render main Template Studio page
	 */
	public function render_studio_page() {
		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'web-stories-templates-studio' ) );
		}

		$registry        = WSTS_Template_Registry::instance();
		$templates       = $registry->get_all();
		$categories      = $registry->get_categories();
		$has_web_stories = class_exists( '\Google\Web_Stories\Plugin' );
		$recent_posts    = get_posts(
			array(
				'numberposts' => 30,
				'post_status' => 'publish',
				'post_type'   => 'post',
			)
		);
		?>
		<div class="wrap wsts-studio-wrap">
			<!-- Header -->
			<header class="wsts-header">
				<div class="wsts-header-content">
					<div class="wsts-brand-badge"><?php esc_html_e( 'WEB STORIES STUDIO • BY PENNION', 'web-stories-templates-studio' ); ?></div>
					<h1 class="wsts-title">Web Stories Template Studio</h1>
					<p class="wsts-subtitle">
						Curated, high-retention 9:16 templates engineered with strict safe zones and 1-click adjustment.
					</p>
				</div>
				<div class="wsts-header-actions">
					<button type="button" class="button button-primary wsts-btn-autopost" id="btnOpenAutoPostModal">
						✨ <?php esc_html_e( 'Auto-Create from Post', 'web-stories-templates-studio' ); ?>
					</button>
					<div class="wsts-status-pill <?php echo $has_web_stories ? 'is-active' : 'is-standby'; ?>">
						<span class="wsts-dot"></span>
						<?php echo $has_web_stories ? esc_html__( 'Official Web Stories Engine: Active', 'web-stories-templates-studio' ) : esc_html__( 'Standalone Mode Ready', 'web-stories-templates-studio' ); ?>
					</div>
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=web-stories-sizes-guide' ) ); ?>" class="button button-secondary wsts-guide-btn">
						<?php esc_html_e( 'Sizes & Specs', 'web-stories-templates-studio' ); ?>
					</a>
				</div>
			</header>

			<!-- Search & Category Filter Bar (1-Line Sleek Responsive Scroller) -->
			<div class="wsts-filter-bar">
				<div class="wsts-search-filter-wrap">
					<div class="wsts-search-box">
						<span class="wsts-search-icon">
							<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
								<circle cx="11" cy="11" r="8"></circle>
								<line x1="21" y1="21" x2="16.65" y2="16.65"></line>
							</svg>
						</span>
						<input type="text" id="wstsTemplateSearchInput" class="wsts-search-input" placeholder="<?php esc_attr_e( 'Search 500+ templates & 100+ categories (e.g. crypto, fitness, coffee, luxury, cars, tech)...', 'web-stories-templates-studio' ); ?>">
						<button type="button" class="wsts-search-clear-btn" id="btnSearchClear" style="display:none;">&times;</button>
					</div>
					<div class="wsts-category-select-wrap">
						<select id="wstsCategoryQuickSelect" class="wsts-category-select" aria-label="<?php esc_attr_e( 'Jump to Category', 'web-stories-templates-studio' ); ?>">
							<optgroup label="<?php esc_attr_e( 'Quick Filters', 'web-stories-templates-studio' ); ?>">
								<option value="all"><?php esc_html_e( 'All Templates', 'web-stories-templates-studio' ); ?> (<?php echo count( $templates ); ?>)</option>
								<option value="favorites"><?php esc_html_e( '★ Favorites', 'web-stories-templates-studio' ); ?></option>
							</optgroup>
							<optgroup label="<?php esc_attr_e( 'Popular Niches', 'web-stories-templates-studio' ); ?>">
								<?php
								$primary_slugs = array( 'tech', 'fashion', 'viral', 'food', 'fitness', 'travel', 'crypto', 'luxury', 'health', 'coffee' );
								foreach ( $primary_slugs as $pslug ) :
									if ( isset( $categories[ $pslug ] ) ) :
										?>
										<option value="<?php echo esc_attr( $pslug ); ?>">
											<?php echo esc_html( $categories[ $pslug ]['label'] ); ?> (<?php echo esc_html( $categories[ $pslug ]['count'] ); ?>)
										</option>
										<?php
									endif;
								endforeach;
								?>
							</optgroup>
						</select>
					</div>
					<div class="wsts-catalog-meta-pill">
						<span class="wsts-pulse-dot"></span>
						<strong>500+ Templates</strong> &bull; 5 Per Category
					</div>
				</div>

				<div class="wsts-category-scroll-container">
					<button type="button" class="wsts-scroll-arrow wsts-scroll-arrow-left" id="btnScrollCatLeft" aria-label="<?php esc_attr_e( 'Scroll categories left', 'web-stories-templates-studio' ); ?>">
						<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
					</button>
					<div class="wsts-scroll-fade wsts-scroll-fade-left"></div>

					<div class="wsts-filter-pills" id="wstsCategoryPillsBar">
						<button type="button" class="wsts-filter-pill is-active" data-category="all">
							<span class="wsts-pill-text"><?php esc_html_e( 'All Templates', 'web-stories-templates-studio' ); ?></span>
							<span class="wsts-pill-count"><?php echo count( $templates ); ?></span>
						</button>
						<button type="button" class="wsts-filter-pill wsts-fav-pill" data-category="favorites" id="pillFavorites">
							<span class="wsts-pill-text">★ <?php esc_html_e( 'Favorites', 'web-stories-templates-studio' ); ?></span>
							<span class="wsts-pill-count" id="favPillCount">0</span>
						</button>
						<?php foreach ( $categories as $slug => $cat ) : ?>
							<?php if ( 'all' === $slug ) { continue; } ?>
							<button type="button" class="wsts-filter-pill" data-category="<?php echo esc_attr( $slug ); ?>">
								<span class="wsts-pill-text"><?php echo esc_html( $cat['label'] ); ?></span>
								<span class="wsts-pill-count"><?php echo esc_html( $cat['count'] ); ?></span>
							</button>
						<?php endforeach; ?>
					</div>

					<div class="wsts-scroll-fade wsts-scroll-fade-right"></div>
					<button type="button" class="wsts-scroll-arrow wsts-scroll-arrow-right" id="btnScrollCatRight" aria-label="<?php esc_attr_e( 'Scroll categories right', 'web-stories-templates-studio' ); ?>">
						<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
					</button>
				</div>
			</div>

			<!-- Template Cards Grid -->
			<div class="wsts-grid" id="wstsTemplateGrid">
				<?php foreach ( $templates as $tpl ) : ?>
					<div class="wsts-card" data-id="<?php echo esc_attr( $tpl['id'] ); ?>" data-category="<?php echo esc_attr( $tpl['category'] ); ?>">
						<div class="wsts-card-preview-wrap">
							<img src="<?php echo esc_url( $tpl['poster_image'] ); ?>" alt="<?php echo esc_attr( $tpl['name'] ); ?>" class="wsts-card-img" loading="lazy">
							<div class="wsts-card-overlay">
								<div class="wsts-card-badges">
									<div class="wsts-card-badge-left">
										<span class="wsts-tag"><?php echo esc_html( $tpl['category_label'] ); ?></span>
										<span class="wsts-default-chip" title="<?php esc_attr_e( 'Active Default Template', 'web-stories-templates-studio' ); ?>">★ <?php esc_html_e( 'Default', 'web-stories-templates-studio' ); ?></span>
									</div>
									<div class="wsts-card-badge-right">
										<span class="wsts-page-count"><?php echo count( $tpl['pages'] ); ?> <?php esc_html_e( 'Slides', 'web-stories-templates-studio' ); ?></span>
										<button type="button" class="wsts-card-fav-btn" data-id="<?php echo esc_attr( $tpl['id'] ); ?>" title="<?php esc_attr_e( 'Add to Favorites', 'web-stories-templates-studio' ); ?>" aria-label="<?php esc_attr_e( 'Toggle Favorite', 'web-stories-templates-studio' ); ?>">
											<svg class="wsts-star-svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
												<polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
											</svg>
										</button>
									</div>
								</div>
								<div class="wsts-card-overlay-actions">
									<button type="button" class="wsts-btn wsts-btn-primary wsts-open-customizer" data-id="<?php echo esc_attr( $tpl['id'] ); ?>">
										<?php esc_html_e( 'Preview & Customize', 'web-stories-templates-studio' ); ?>
									</button>
								</div>
							</div>
						</div>
						<div class="wsts-card-body">
							<div class="wsts-color-dots">
								<span class="wsts-dot-color" style="background-color: <?php echo esc_attr( $tpl['accent_color'] ); ?>" title="Accent"></span>
								<span class="wsts-dot-color" style="background-color: <?php echo esc_attr( $tpl['secondary_color'] ); ?>" title="Secondary"></span>
								<span class="wsts-dot-color" style="background-color: <?php echo esc_attr( $tpl['bg_color'] ); ?>" title="Background"></span>
							</div>
							<h3 class="wsts-card-title"><?php echo esc_html( $tpl['name'] ); ?></h3>
							<p class="wsts-card-tagline"><?php echo esc_html( $tpl['tagline'] ); ?></p>
							<div class="wsts-card-footer">
								<button type="button" class="wsts-quick-import-btn" data-id="<?php echo esc_attr( $tpl['id'] ); ?>">
									<?php esc_html_e( 'Use Template', 'web-stories-templates-studio' ); ?> &rarr;
								</button>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
			</div>

			<!-- Live Studio / Customizer Modal -->
			<div class="wsts-modal" id="wstsCustomizerModal" style="display:none;">
				<div class="wsts-modal-backdrop"></div>
				<div class="wsts-modal-container">
					<div class="wsts-modal-header">
						<div class="wsts-modal-title-box">
							<span class="wsts-modal-badge" id="modalTemplateBadge">TEMPLATE</span>
							<h2 id="modalTemplateTitle">Customizer</h2>
						</div>
						<div class="wsts-modal-header-controls">
							<button type="button" class="wsts-btn-make-default" id="btnMakeDefaultTemplate" title="<?php esc_attr_e( 'Set this template as your global default when opening the Studio', 'web-stories-templates-studio' ); ?>">
								⭐ <?php esc_html_e( 'Set Default', 'web-stories-templates-studio' ); ?>
							</button>
							<button type="button" class="wsts-toggle-safe-zone" id="btnToggleSafeZone" title="Show/Hide Safe Zones">
								<?php esc_html_e( 'Safe Zone', 'web-stories-templates-studio' ); ?>: <span id="safeZoneStateText"><?php esc_html_e( 'OFF', 'web-stories-templates-studio' ); ?></span>
							</button>
							<button type="button" class="wsts-modal-close" id="btnModalClose">&times;</button>
						</div>
					</div>

					<div class="wsts-modal-body">
						<!-- Left: Adjustment Controls -->
						<div class="wsts-adjust-panel">

							<div class="wsts-adjust-tabs">
								<button type="button" class="wsts-adjust-tab is-active" data-tab="tab-meta"><?php esc_html_e( 'Story & Branding', 'web-stories-templates-studio' ); ?></button>
								<button type="button" class="wsts-adjust-tab" data-tab="tab-typography"><?php esc_html_e( 'Typography', 'web-stories-templates-studio' ); ?></button>
								<button type="button" class="wsts-adjust-tab" data-tab="tab-slides"><?php esc_html_e( 'Slides & Content', 'web-stories-templates-studio' ); ?></button>
								<button type="button" class="wsts-adjust-tab" data-tab="tab-seo"><?php esc_html_e( 'SEO & Discover', 'web-stories-templates-studio' ); ?></button>
								<button type="button" class="wsts-adjust-tab" data-tab="tab-monetize"><?php esc_html_e( 'Monetization & Audio', 'web-stories-templates-studio' ); ?></button>
								<button type="button" class="wsts-adjust-tab" data-tab="tab-specs"><?php esc_html_e( 'Specifications Audit', 'web-stories-templates-studio' ); ?></button>
							</div>

							<!-- TAB 1: STORY & BRANDING -->
							<div class="wsts-tab-content is-active" id="tab-meta">
								<div class="wsts-form-group">
									<label class="wsts-label" for="customStoryTitle"><?php esc_html_e( 'Story title', 'web-stories-templates-studio' ); ?></label>
									<input type="text" id="customStoryTitle" class="wsts-input" placeholder="Enter punchy title (under 10 words)...">
									<small class="wsts-help"><?php esc_html_e( 'Recommended < 10 words. Displays on Google previews & story header.', 'web-stories-templates-studio' ); ?></small>
								</div>

								<div class="wsts-form-group">
									<div class="wsts-label-inline-bar">
										<label class="wsts-label" for="customStoryCategory" style="margin-bottom:0;"><?php esc_html_e( 'Story Category / WordPress Taxonomy', 'web-stories-templates-studio' ); ?></label>
										<button type="button" class="wsts-text-btn" id="btnToggleCustomCat" style="background:none;border:none;color:#2563eb;font-size:11.5px;font-weight:600;cursor:pointer;padding:0;">
											+ <?php esc_html_e( 'Custom Category', 'web-stories-templates-studio' ); ?>
										</button>
									</div>
									<select id="customStoryCategory" class="wsts-input">
										<optgroup label="<?php esc_attr_e( 'Your WordPress Categories', 'web-stories-templates-studio' ); ?>">
											<?php
											$wp_cats = get_categories( array( 'hide_empty' => false ) );
											if ( ! empty( $wp_cats ) && ! is_wp_error( $wp_cats ) ) {
												foreach ( $wp_cats as $wc ) {
													echo '<option value="' . esc_attr( $wc->slug ) . '">' . esc_html( $wc->name ) . ' (WordPress)</option>';
												}
											}
											?>
										</optgroup>
										<optgroup label="<?php esc_attr_e( 'Official Web Story Categories', 'web-stories-templates-studio' ); ?>">
											<?php
											if ( taxonomy_exists( 'web_story_category' ) ) {
												$story_terms = get_terms( array( 'taxonomy' => 'web_story_category', 'hide_empty' => false ) );
												if ( ! empty( $story_terms ) && ! is_wp_error( $story_terms ) ) {
													foreach ( $story_terms as $st ) {
														echo '<option value="' . esc_attr( $st->slug ) . '">' . esc_html( $st->name ) . '</option>';
													}
												}
											}
											?>
										</optgroup>
										<optgroup label="<?php esc_attr_e( 'Template Niches (Auto-Created in WordPress)', 'web-stories-templates-studio' ); ?>">
											<?php foreach ( $categories as $cslug => $cdata ) : ?>
												<?php if ( 'all' === $cslug || 'favorites' === $cslug ) continue; ?>
												<option value="<?php echo esc_attr( $cslug ); ?>"><?php echo esc_html( $cdata['label'] ); ?></option>
											<?php endforeach; ?>
										</optgroup>
									</select>
									<input type="text" id="customStoryCategoryCustom" class="wsts-input" placeholder="<?php esc_attr_e( 'Type new category name...', 'web-stories-templates-studio' ); ?>" style="display:none; margin-top:6px;">
									<small class="wsts-help"><?php esc_html_e( 'Publishes your story directly under this category in WordPress & Google Web Stories.', 'web-stories-templates-studio' ); ?></small>
								</div>

								<!-- Publisher Brand & Logo Identity Section (100% Inline Apple/Linear Control) -->
								<div class="wsts-brand-card">
									<div class="wsts-form-group" style="margin-bottom: 14px;">
										<label class="wsts-label" for="customPublisher"><?php esc_html_e( 'Publisher Brand Name', 'web-stories-templates-studio' ); ?></label>
										<input type="text" id="customPublisher" class="wsts-input" placeholder="Your Site / Brand Name">
									</div>

									<div class="wsts-form-group" style="margin-bottom: 0;">
										<div class="wsts-label-inline-bar">
											<label class="wsts-label" for="customLogoUrl" style="margin-bottom:0;">
												<?php esc_html_e( 'Publisher Logo (min 96×96)', 'web-stories-templates-studio' ); ?>
											</label>
											<span class="wsts-auto-badge" id="logoAutoBadge"><?php esc_html_e( 'Site Icon', 'web-stories-templates-studio' ); ?></span>
										</div>
										<div class="wsts-logo-box">
											<div class="wsts-logo-avatar" id="wstsLogoAvatar" title="<?php esc_attr_e( 'Publisher Logo Preview', 'web-stories-templates-studio' ); ?>">
												<img src="" id="wstsLogoThumbImg" alt="Logo" class="wsts-logo-thumb-img" style="display:none;">
												<span class="wsts-logo-avatar-fallback" id="wstsLogoAvatarFallback">WP</span>
											</div>
											<div class="wsts-logo-controls">
												<input type="text" id="customLogoUrl" class="wsts-input wsts-logo-url-input" placeholder="https://... (Logo image URL)">
												<div class="wsts-logo-btn-group">
													<button type="button" class="button wsts-btn-fetch-site-logo" id="btnFetchSiteLogo" title="<?php esc_attr_e( 'Fetch Site Icon from WordPress', 'web-stories-templates-studio' ); ?>">
														⚡ <?php esc_html_e( 'Use Site Icon', 'web-stories-templates-studio' ); ?>
													</button>
													<button type="button" class="button wsts-media-picker-btn" data-target="#customLogoUrl">
														📁 <?php esc_html_e( 'Upload', 'web-stories-templates-studio' ); ?>
													</button>
												</div>
											</div>
										</div>
										<small class="wsts-help"><?php esc_html_e( 'Auto-detected from your WordPress Site Icon (512×512 recommended for Google Discover).', 'web-stories-templates-studio' ); ?></small>
									</div>
								</div>

								<div class="wsts-form-row wsts-color-row">
									<div class="wsts-form-group">
										<label class="wsts-label" for="customAccentHex"><?php esc_html_e( 'Accent color', 'web-stories-templates-studio' ); ?></label>
										<div class="wsts-color-control" id="ctrlAccentColor">
											<div class="wsts-color-swatch-btn" id="swatchAccentColor" style="background-color: #00f2fe;">
												<input type="color" id="customAccentColor" class="wsts-color-picker wsts-native-color-picker" value="#00f2fe">
											</div>
											<input type="text" id="customAccentHex" class="wsts-color-hex-input" value="#00F2FE" maxlength="7" spellcheck="false">
										</div>
									</div>
									<div class="wsts-form-group">
										<label class="wsts-label" for="customSecondaryHex"><?php esc_html_e( 'Secondary accent', 'web-stories-templates-studio' ); ?></label>
										<div class="wsts-color-control" id="ctrlSecondaryColor">
											<div class="wsts-color-swatch-btn" id="swatchSecondaryColor" style="background-color: #fe0979;">
												<input type="color" id="customSecondaryColor" class="wsts-color-picker wsts-native-color-picker" value="#fe0979">
											</div>
											<input type="text" id="customSecondaryHex" class="wsts-color-hex-input" value="#FE0979" maxlength="7" spellcheck="false">
										</div>
									</div>
									<div class="wsts-form-group">
										<label class="wsts-label" for="customBgHex"><?php esc_html_e( 'Canvas tone', 'web-stories-templates-studio' ); ?></label>
										<div class="wsts-color-control" id="ctrlBgColor">
											<div class="wsts-color-swatch-btn" id="swatchBgColor" style="background-color: #080811;">
												<input type="color" id="customBgColor" class="wsts-color-picker wsts-native-color-picker" value="#080811">
											</div>
											<input type="text" id="customBgHex" class="wsts-color-hex-input" value="#080811" maxlength="7" spellcheck="false">
										</div>
									</div>
								</div>

								<div class="wsts-form-group">
									<label class="wsts-label" for="customPosterImage"><?php esc_html_e( 'Story portrait cover / poster (3:4 or 9:16)', 'web-stories-templates-studio' ); ?></label>
									<div class="wsts-input-btn-group">
										<input type="text" id="customPosterImage" class="wsts-input" placeholder="Cover image URL...">
										<button type="button" class="button wsts-smart-image-btn" data-target="poster"><?php esc_html_e( '✨ Unsplash / Pexels', 'web-stories-templates-studio' ); ?></button>
										<button type="button" class="button wsts-media-picker-btn" data-target="#customPosterImage"><?php esc_html_e( 'Upload / Media', 'web-stories-templates-studio' ); ?></button>
									</div>
								</div>

								<!-- Scrim Gradient Preset (Global Story Contrast Overlay) -->
								<div class="wsts-form-group">
									<label class="wsts-label" for="customGlobalScrim"><?php esc_html_e( 'Scrim Gradient Preset', 'web-stories-templates-studio' ); ?></label>
									<select id="customGlobalScrim" class="wsts-input">
										<option value="linear-gradient(180deg, rgba(8,8,17,0.3) 0%, rgba(8,8,17,0.92) 80%)"><?php esc_html_e( 'High Contrast Dark (Recommended for Readability)', 'web-stories-templates-studio' ); ?></option>
										<option value="linear-gradient(180deg, rgba(0,0,0,0.1) 0%, rgba(0,0,0,0.65) 85%)"><?php esc_html_e( 'Subtle Dark Scrim', 'web-stories-templates-studio' ); ?></option>
										<option value="linear-gradient(180deg, rgba(255,255,255,0.08) 0%, rgba(15,23,42,0.9) 75%)"><?php esc_html_e( 'Cinematic Duo Gradient', 'web-stories-templates-studio' ); ?></option>
										<option value="linear-gradient(180deg, rgba(0,0,0,0.4) 0%, rgba(0,0,0,0.4) 100%)"><?php esc_html_e( 'Flat Dim 40%', 'web-stories-templates-studio' ); ?></option>
										<option value="none"><?php esc_html_e( 'None (Transparent Background)', 'web-stories-templates-studio' ); ?></option>
									</select>
									<div style="margin-top: 8px;">
										<button type="button" class="button wsts-btn-sync-action" id="btnApplyGlobalScrimToAllSlides" title="<?php esc_attr_e( 'Apply this scrim gradient preset across all slides in this story', 'web-stories-templates-studio' ); ?>">
											<?php esc_html_e( '⚡ Apply Scrim to All Slides', 'web-stories-templates-studio' ); ?>
										</button>
									</div>
									<small class="wsts-help"><?php esc_html_e( 'Ensures headline & narrative text strictly meets Google Web Stories contrast readability standards over background imagery.', 'web-stories-templates-studio' ); ?></small>
								</div>

								<div class="wsts-design-system-box">
									<div class="wsts-ds-header">
										<strong><?php esc_html_e( 'Brand Design System', 'web-stories-templates-studio' ); ?></strong>
										<span class="wsts-ds-status" id="brandDsStatusLabel"><?php esc_html_e( 'Ready', 'web-stories-templates-studio' ); ?></span>
									</div>
									<p class="wsts-ds-hint"><?php esc_html_e( 'Save your colors, typography, and publisher identity as your reusable Brand Design System.', 'web-stories-templates-studio' ); ?></p>
									<div class="wsts-ds-actions">
										<button type="button" class="button button-secondary" id="btnApplyBrandDS">
											<?php esc_html_e( 'Apply Brand Preset', 'web-stories-templates-studio' ); ?>
										</button>
										<button type="button" class="button button-secondary" id="btnSaveBrandDS">
											<?php esc_html_e( 'Save as Brand Preset', 'web-stories-templates-studio' ); ?>
										</button>
									</div>
								</div>
							</div>

							<!-- TAB 2: UNIFIED TYPOGRAPHY & SIZING SYSTEM -->
							<div class="wsts-tab-content" id="tab-typography">
								<div class="wsts-section-intro">
									<h4 class="wsts-section-title"><?php esc_html_e( 'Unified Story Typography', 'web-stories-templates-studio' ); ?></h4>
									<p class="wsts-section-desc"><?php esc_html_e( 'Control headline fonts, body typography, and optical sizing scale uniformly across every slide.', 'web-stories-templates-studio' ); ?></p>
								</div>

								<!-- Unified Story Colors & Badge Coordination Card -->
								<div class="wsts-typo-card">
									<div class="wsts-typo-card-header">
										<span class="wsts-typo-badge">COLORS & BADGES</span>
										<strong><?php esc_html_e( 'Unified Story Colors & Badge Styling', 'web-stories-templates-studio' ); ?></strong>
									</div>

									<div class="wsts-form-row wsts-color-row">
										<div class="wsts-form-group">
											<label class="wsts-label" for="unifiedHeadingColorHex"><?php esc_html_e( 'Unified heading color', 'web-stories-templates-studio' ); ?></label>
											<div class="wsts-color-control" id="ctrlUnifiedHeadingColor">
												<div class="wsts-color-swatch-btn" id="swatchUnifiedHeadingColor" style="background-color: #ffffff;">
													<input type="color" id="unifiedHeadingColor" class="wsts-color-picker wsts-native-color-picker" value="#ffffff">
												</div>
												<input type="text" id="unifiedHeadingColorHex" class="wsts-color-hex-input" value="#FFFFFF" maxlength="7" spellcheck="false">
											</div>
											<small class="wsts-help"><?php esc_html_e( 'Syncs headline color across all slides.', 'web-stories-templates-studio' ); ?></small>
										</div>

										<div class="wsts-form-group">
											<label class="wsts-label" for="unifiedBadgeBgHex"><?php esc_html_e( 'Unified badge background', 'web-stories-templates-studio' ); ?></label>
											<div class="wsts-color-control" id="ctrlUnifiedBadgeBg">
												<div class="wsts-color-swatch-btn" id="swatchUnifiedBadgeBg" style="background-color: #00f2fe;">
													<input type="color" id="unifiedBadgeBg" class="wsts-color-picker wsts-native-color-picker" value="#00f2fe">
												</div>
												<input type="text" id="unifiedBadgeBgHex" class="wsts-color-hex-input" value="#00F2FE" maxlength="7" spellcheck="false">
											</div>
											<small class="wsts-help"><?php esc_html_e( 'Category pills & top tags across slides.', 'web-stories-templates-studio' ); ?></small>
										</div>

										<div class="wsts-form-group">
											<label class="wsts-label" for="unifiedBadgeColorHex"><?php esc_html_e( 'Unified badge text', 'web-stories-templates-studio' ); ?></label>
											<div class="wsts-color-control" id="ctrlUnifiedBadgeColor">
												<div class="wsts-color-swatch-btn" id="swatchUnifiedBadgeColor" style="background-color: #080811;">
													<input type="color" id="unifiedBadgeColor" class="wsts-color-picker wsts-native-color-picker" value="#080811">
												</div>
												<input type="text" id="unifiedBadgeColorHex" class="wsts-color-hex-input" value="#080811" maxlength="7" spellcheck="false">
											</div>
											<small class="wsts-help"><?php esc_html_e( 'Contrast label text inside badge.', 'web-stories-templates-studio' ); ?></small>
										</div>
									</div>

									<div class="wsts-unified-actions-row">
										<button type="button" class="button wsts-btn-sync-action" id="btnSyncHeadingWithBadge" title="<?php esc_attr_e( 'Match heading color with the badge color for bold unified impact', 'web-stories-templates-studio' ); ?>">
											🎨 <?php esc_html_e( 'Sync Heading Color with Badge', 'web-stories-templates-studio' ); ?>
										</button>
										<button type="button" class="button wsts-btn-sync-action" id="btnSyncBadgeWithAccent" title="<?php esc_attr_e( 'Inherit brand accent color for badge background', 'web-stories-templates-studio' ); ?>">
											⚡ <?php esc_html_e( 'Sync Badge with Brand Accent', 'web-stories-templates-studio' ); ?>
										</button>
										<button type="button" class="button wsts-btn-sync-action" id="btnApplyColorsToAllSlides" title="<?php esc_attr_e( 'Apply unified colors across every slide', 'web-stories-templates-studio' ); ?>">
											✨ <?php esc_html_e( 'Apply to All Slides', 'web-stories-templates-studio' ); ?>
										</button>
									</div>
								</div>

								<!-- Heading Typography Card -->
								<div class="wsts-typo-card">
									<div class="wsts-typo-card-header">
										<span class="wsts-typo-badge">H1 HEADLINES</span>
										<strong><?php esc_html_e( 'Heading Typography & Sizing', 'web-stories-templates-studio' ); ?></strong>
									</div>

									<div class="wsts-form-group">
										<!-- Unified Story Heading Font (customTitleFontGlobal) -->
										<label class="wsts-label" for="customTitleFontUnified"><?php esc_html_e( 'Heading Font Family (Full Google Library & Custom)', 'web-stories-templates-studio' ); ?></label>
										<select id="customTitleFontUnified" data-id="customTitleFontGlobal" class="wsts-input wsts-select-font">
											<optgroup label="<?php esc_attr_e( 'Popular Modern Sans-Serif', 'web-stories-templates-studio' ); ?>">
												<option value="'Space Grotesk', sans-serif">Space Grotesk (Modern Tech & Futuristic)</option>
												<option value="'Inter', sans-serif">Inter (Universal Crisp & Neutral)</option>
												<option value="'Plus Jakarta Sans', sans-serif">Plus Jakarta Sans (Contemporary Clean)</option>
												<option value="'Roboto', sans-serif">Roboto (Clean Android Modern)</option>
												<option value="'Montserrat', sans-serif">Montserrat (Geometric Punchy Bold)</option>
												<option value="'Poppins', sans-serif">Poppins (Friendly Clean Geometric)</option>
												<option value="'Open Sans', sans-serif">Open Sans (High Legibility)</option>
												<option value="'Lato', sans-serif">Lato (Warm Editorial Sans)</option>
												<option value="'DM Sans', sans-serif">DM Sans (Sophisticated Minimal)</option>
												<option value="'Work Sans', sans-serif">Work Sans (Sharp Graphic Grotesque)</option>
												<option value="'Raleway', sans-serif">Raleway (Elegant Modernist Sans)</option>
												<option value="'Nunito', sans-serif">Nunito (Rounded Friendly Sans)</option>
												<option value="'Outfit', sans-serif">Outfit (Brand Tech Minimal)</option>
												<option value="'Manrope', sans-serif">Manrope (Geometric Modern)</option>
												<option value="'Sora', sans-serif">Sora (Neo-Grotesque Display)</option>
												<option value="'Lexend', sans-serif">Lexend (Optimized Reading Flow)</option>
												<option value="'Urbanist', sans-serif">Urbanist (Sleek Clean Geometric)</option>
												<option value="'Syne', sans-serif">Syne (Avant-Garde Architectural)</option>
												<option value="'Archivo', sans-serif">Archivo (Industrial Punchy)</option>
												<option value="'Cabinet Grotesk', sans-serif">Cabinet Grotesk (Neo-Brutalist Bold)</option>
											</optgroup>
											<optgroup label="<?php esc_attr_e( 'Luxury & Editorial Serif', 'web-stories-templates-studio' ); ?>">
												<option value="'Playfair Display', serif">Playfair Display (Luxury Editorial Serif)</option>
												<option value="'Cinzel', serif">Cinzel (Classical High Fashion Roman)</option>
												<option value="'Merriweather', serif">Merriweather (Stately Editorial Serif)</option>
												<option value="'Lora', serif">Lora (Contemporary Calligraphic Serif)</option>
												<option value="'Bodoni Moda', serif">Bodoni Moda (High-Contrast Vogue Luxury)</option>
												<option value="'Cormorant Garamond', serif">Cormorant Garamond (Graceful Traditional)</option>
												<option value="'Fraunces', serif">Fraunces (Warm Wonky Editorial)</option>
												<option value="'Prata', serif">Prata (Elegant High-Fashion Tear)</option>
												<option value="'Newsreader', serif">Newsreader (Longform Journal Serif)</option>
												<option value="'Libre Baskerville', serif">Libre Baskerville (Refined Classic Book)</option>
												<option value="'EB Garamond', serif">EB Garamond (Renaissance Classical)</option>
												<option value="'Marcellus', serif">Marcellus (Sculpted Roman Classic)</option>
											</optgroup>
											<optgroup label="<?php esc_attr_e( 'Display & High-Impact Bold', 'web-stories-templates-studio' ); ?>">
												<option value="'Oswald', sans-serif">Oswald (Punchy Condensed Headlines)</option>
												<option value="'Bebas Neue', sans-serif">Bebas Neue (Heavy All-Caps Impact)</option>
												<option value="'Anton', sans-serif">Anton (Ultra-Heavy Modern Gothic)</option>
												<option value="'Bungee', cursive">Bungee (Urban Billboard Headline)</option>
												<option value="'Righteous', cursive">Righteous (Futuristic Bold Display)</option>
												<option value="'Clash Display', sans-serif">Clash Display (High-Impact Contemporary)</option>
												<option value="'Abril Fatface', serif">Abril Fatface (Bold Dramatic Titling)</option>
												<option value="'Alfa Slab One', cursive">Alfa Slab One (Heavy Slab Serif)</option>
												<option value="'Bangers', cursive">Bangers (Comic Pop Impact)</option>
												<option value="'Shrikhand', cursive">Shrikhand (Retro Voluptuous Display)</option>
												<option value="'Russo One', sans-serif">Russo One (Speed & Bold Power)</option>
												<option value="'Ultra', serif">Ultra (Massive Woodtype Slab)</option>
											</optgroup>
											<optgroup label="<?php esc_attr_e( 'Monospace & Modern Tech', 'web-stories-templates-studio' ); ?>">
												<option value="'Space Mono', monospace">Space Mono (Geometric Monospace)</option>
												<option value="'Fira Code', monospace">Fira Code (Developer Code Mono)</option>
												<option value="'JetBrains Mono', monospace">JetBrains Mono (Contemporary Terminal)</option>
												<option value="'Source Code Pro', monospace">Source Code Pro (Clean Monospaced)</option>
												<option value="'IBM Plex Mono', monospace">IBM Plex Mono (Industrial Tech Monospace)</option>
												<option value="'Roboto Mono', monospace">Roboto Mono (Crisp Structured Mono)</option>
											</optgroup>
											<optgroup label="<?php esc_attr_e( 'Handwriting & Organic Script', 'web-stories-templates-studio' ); ?>">
												<option value="'Caveat', cursive">Caveat (Spontaneous Hand-Lettered)</option>
												<option value="'Dancing Script', cursive">Dancing Script (Casual Lively Cursive)</option>
												<option value="'Pacifico', cursive">Pacifico (Laidback 50s Surf Script)</option>
												<option value="'Great Vibes', cursive">Great Vibes (Flowing Elegant Script)</option>
												<option value="'Satisfy', cursive">Satisfy (Smooth Brush Lettering)</option>
												<option value="'Kalam', cursive">Kalam (Organic Ballpoint Script)</option>
												<option value="'Permanent Marker', cursive">Permanent Marker (Bold Felt-Tip Marker)</option>
											</optgroup>
											<optgroup label="<?php esc_attr_e( 'Custom Brand Font', 'web-stories-templates-studio' ); ?>">
												<option value="__custom__"><?php esc_html_e( '✨ + Add Custom Brand Font...', 'web-stories-templates-studio' ); ?></option>
											</optgroup>
										</select>
									</div>

									<!-- Custom Heading Font Drawer -->
									<div class="wsts-custom-font-drawer" id="titleCustomFontDrawer" style="display:none;">
										<div class="wsts-custom-font-header">
											<span class="wsts-custom-badge"><?php esc_html_e( 'CUSTOM WEBFONT', 'web-stories-templates-studio' ); ?></span>
											<strong><?php esc_html_e( 'Connect Custom Heading Webfont', 'web-stories-templates-studio' ); ?></strong>
										</div>
										<div class="wsts-form-group">
											<label class="wsts-label" for="customHeadingFontName"><?php esc_html_e( 'Custom font family name', 'web-stories-templates-studio' ); ?></label>
											<input type="text" id="customHeadingFontName" class="wsts-input" placeholder="e.g. Canela, Neue Haas Grotesk, Futura PT">
											<small class="wsts-help"><?php esc_html_e( 'Exact name defined in your CSS or brand kit.', 'web-stories-templates-studio' ); ?></small>
										</div>
										<div class="wsts-form-group">
											<label class="wsts-label" for="customHeadingFontUrl"><?php esc_html_e( 'Webfont stylesheet URL (Optional: Typekit, Google Fonts, @font-face)', 'web-stories-templates-studio' ); ?></label>
											<input type="text" id="customHeadingFontUrl" class="wsts-input" placeholder="https://use.typekit.net/... or https://fonts.googleapis.com/...">
											<small class="wsts-help"><?php esc_html_e( 'Leave empty if font is already bundled with your theme or operating system.', 'web-stories-templates-studio' ); ?></small>
										</div>
										<div class="wsts-form-row">
											<div class="wsts-form-group">
												<label class="wsts-label" for="customHeadingFontFallback"><?php esc_html_e( 'Fallback font stack', 'web-stories-templates-studio' ); ?></label>
												<select id="customHeadingFontFallback" class="wsts-input">
													<option value="sans-serif">Sans-serif (-apple-system, sans-serif)</option>
													<option value="serif">Serif (Georgia, serif)</option>
													<option value="monospace">Monospace (ui-monospace, monospace)</option>
												</select>
											</div>
											<div class="wsts-form-group" style="align-self: flex-end;">
												<button type="button" class="button button-primary wsts-btn-apply-custom-font" id="btnApplyCustomTitleFont">
													<?php esc_html_e( 'Apply Custom Font', 'web-stories-templates-studio' ); ?>
												</button>
											</div>
										</div>
									</div>

									<div class="wsts-form-group">
										<div class="wsts-label-with-value">
											<label class="wsts-label" for="customHeadlineSizeRange"><?php esc_html_e( 'Headline Font Size (Unified across slides)', 'web-stories-templates-studio' ); ?></label>
											<span class="wsts-value-tag" id="headlineSizeTag">32px</span>
										</div>
										<div class="wsts-range-input-row">
											<input type="range" id="customHeadlineSizeRange" min="22" max="56" step="2" value="32" class="wsts-range-slider">
											<div class="wsts-quick-size-pills">
												<button type="button" class="wsts-size-pill" data-size="28">28px</button>
												<button type="button" class="wsts-size-pill is-active" data-size="32">32px</button>
												<button type="button" class="wsts-size-pill" data-size="38">38px</button>
												<button type="button" class="wsts-size-pill" data-size="44">44px</button>
												<button type="button" class="wsts-size-pill" data-size="50">50px</button>
											</div>
										</div>
									</div>

									<div class="wsts-form-row wsts-color-row">
										<div class="wsts-form-group">
											<label class="wsts-label" for="customHeadlineTracking"><?php esc_html_e( 'Letter-Spacing', 'web-stories-templates-studio' ); ?></label>
											<select id="customHeadlineTracking" class="wsts-input">
												<option value="-0.03em">Tight (-0.03em)</option>
												<option value="-0.01em" selected>Clean (-0.01em)</option>
												<option value="0em">Normal (0em)</option>
												<option value="0.04em">Spaced (+0.04em)</option>
											</select>
										</div>
										<div class="wsts-form-group">
											<label class="wsts-label" for="customHeadlineLineHeight"><?php esc_html_e( 'Line-Height', 'web-stories-templates-studio' ); ?></label>
											<select id="customHeadlineLineHeight" class="wsts-input">
												<option value="1.12">Snug (1.12)</option>
												<option value="1.18" selected>Editorial (1.18)</option>
												<option value="1.28">Relaxed (1.28)</option>
											</select>
										</div>
									</div>
								</div>

								<!-- Body Typography Card -->
								<div class="wsts-typo-card">
									<div class="wsts-typo-card-header">
										<span class="wsts-typo-badge">PARAGRAPH / BODY</span>
										<strong><?php esc_html_e( 'Body Typography & Sizing', 'web-stories-templates-studio' ); ?></strong>
									</div>

									<div class="wsts-form-group">
										<label class="wsts-label" for="customBodyFontUnified"><?php esc_html_e( 'Body Font Family (Full Google Library & Custom)', 'web-stories-templates-studio' ); ?></label>
										<select id="customBodyFontUnified" class="wsts-input wsts-select-font">
											<optgroup label="<?php esc_attr_e( 'Popular Clean Sans-Serif', 'web-stories-templates-studio' ); ?>">
												<option value="'Inter', sans-serif">Inter (Universal Neutral & Crisp)</option>
												<option value="'Plus Jakarta Sans', sans-serif">Plus Jakarta Sans (Contemporary Neutral)</option>
												<option value="'Roboto', sans-serif">Roboto (Clean Android Modern)</option>
												<option value="'Open Sans', sans-serif">Open Sans (High Legibility)</option>
												<option value="'Lato', sans-serif">Lato (Warm Neutral Sans)</option>
												<option value="'Poppins', sans-serif">Poppins (Clean Geometric)</option>
												<option value="'DM Sans', sans-serif">DM Sans (Sophisticated Minimal)</option>
												<option value="'Space Grotesk', sans-serif">Space Grotesk (Modern Tech)</option>
											</optgroup>
											<optgroup label="<?php esc_attr_e( 'Editorial Serif', 'web-stories-templates-studio' ); ?>">
												<option value="'Playfair Display', serif">Playfair Display (Editorial Serif)</option>
												<option value="'Merriweather', serif">Merriweather (Stately Book Serif)</option>
												<option value="'Lora', serif">Lora (Contemporary Calligraphic Serif)</option>
												<option value="'Cormorant Garamond', serif">Cormorant Garamond (Traditional Grace)</option>
											</optgroup>
											<optgroup label="<?php esc_attr_e( 'Monospace & Modern Tech', 'web-stories-templates-studio' ); ?>">
												<option value="'Space Mono', monospace">Space Mono (Geometric Monospace)</option>
												<option value="'Fira Code', monospace">Fira Code (Developer Code Mono)</option>
												<option value="'JetBrains Mono', monospace">JetBrains Mono (Modern Terminal)</option>
											</optgroup>
											<optgroup label="<?php esc_attr_e( 'Custom Brand Font', 'web-stories-templates-studio' ); ?>">
												<option value="__custom__"><?php esc_html_e( '✨ + Add Custom Brand Font...', 'web-stories-templates-studio' ); ?></option>
											</optgroup>
										</select>
									</div>

									<!-- Custom Body Font Drawer -->
									<div class="wsts-custom-font-drawer" id="bodyCustomFontDrawer" style="display:none;">
										<div class="wsts-custom-font-header">
											<span class="wsts-custom-badge"><?php esc_html_e( 'CUSTOM WEBFONT', 'web-stories-templates-studio' ); ?></span>
											<strong><?php esc_html_e( 'Connect Custom Body Webfont', 'web-stories-templates-studio' ); ?></strong>
										</div>
										<div class="wsts-form-group">
											<label class="wsts-label" for="customBodyFontName"><?php esc_html_e( 'Custom font family name', 'web-stories-templates-studio' ); ?></label>
											<input type="text" id="customBodyFontName" class="wsts-input" placeholder="e.g. Söhne, Circular, Graphik">
										</div>
										<div class="wsts-form-group">
											<label class="wsts-label" for="customBodyFontUrl"><?php esc_html_e( 'Webfont stylesheet URL (Optional)', 'web-stories-templates-studio' ); ?></label>
											<input type="text" id="customBodyFontUrl" class="wsts-input" placeholder="https://use.typekit.net/... or https://...">
										</div>
										<div class="wsts-form-row">
											<div class="wsts-form-group">
												<label class="wsts-label" for="customBodyFontFallback"><?php esc_html_e( 'Fallback font stack', 'web-stories-templates-studio' ); ?></label>
												<select id="customBodyFontFallback" class="wsts-input">
													<option value="sans-serif">Sans-serif (-apple-system, sans-serif)</option>
													<option value="serif">Serif (Georgia, serif)</option>
													<option value="monospace">Monospace (ui-monospace, monospace)</option>
												</select>
											</div>
											<div class="wsts-form-group" style="align-self: flex-end;">
												<button type="button" class="button button-primary wsts-btn-apply-custom-font" id="btnApplyCustomBodyFont">
													<?php esc_html_e( 'Apply Custom Font', 'web-stories-templates-studio' ); ?>
												</button>
											</div>
										</div>
									</div>

									<div class="wsts-form-row wsts-color-row">
										<div class="wsts-form-group">
											<div class="wsts-label-with-value">
												<label class="wsts-label" for="customBodySizeRange"><?php esc_html_e( 'Body Font Size', 'web-stories-templates-studio' ); ?></label>
												<span class="wsts-value-tag" id="bodySizeTag">15px</span>
											</div>
											<input type="range" id="customBodySizeRange" min="12" max="20" step="1" value="15" class="wsts-range-slider">
										</div>
										<div class="wsts-form-group">
											<div class="wsts-label-with-value">
												<label class="wsts-label" for="customStatSizeRange"><?php esc_html_e( 'Stat Number Size', 'web-stories-templates-studio' ); ?></label>
												<span class="wsts-value-tag" id="statSizeTag">40px</span>
											</div>
											<input type="range" id="customStatSizeRange" min="28" max="56" step="2" value="40" class="wsts-range-slider">
										</div>
									</div>
								</div>

								<!-- Live Type Specimen Preview Box -->
								<div class="wsts-specimen-card" id="wstsTypoSpecimenBox">
									<span class="wsts-specimen-badge" id="specimenBadge">PREVIEW SPECIMEN</span>
									<div class="wsts-specimen-stat" id="specimenStat">100% <span>EFFICIENCY</span></div>
									<h2 class="wsts-specimen-headline" id="specimenHeadline">Typography That Feels Liquidy & Premium</h2>
									<p class="wsts-specimen-body" id="specimenBody">Unified font families and fluid optical sizing make every slide feel consistently designed across Google Discover and desktop browsers.</p>
								</div>
							</div>

							<!-- TAB 3: SLIDES COPY & IMAGERY -->
							<div class="wsts-tab-content" id="tab-slides">
								<div class="wsts-slide-accordion" id="wstsSlideAccordion">
									<!-- Injected dynamically via JS based on selected template -->
								</div>
								<div class="wsts-slide-toolbar">
									<button type="button" class="button button-secondary wsts-btn-add-slide" id="btnAddSlide">
										+ <?php esc_html_e( 'Add New Slide', 'web-stories-templates-studio' ); ?>
									</button>
									<span class="wsts-slide-count-badge" id="slideCountBadge">4 Slides</span>
								</div>
							</div>

							<!-- TAB 4: SEO & GOOGLE DISCOVER -->
							<div class="wsts-tab-content" id="tab-seo">
								<div class="wsts-section-intro">
									<h4 class="wsts-section-title"><?php esc_html_e( 'Google Discover & Search Optimization', 'web-stories-templates-studio' ); ?></h4>
									<p class="wsts-section-desc"><?php esc_html_e( 'Optimize your snippet, meta tags, and structured schema data to maximize impressions on Google Discover and mobile search results.', 'web-stories-templates-studio' ); ?></p>
								</div>

								<!-- Live Google Discover Card Simulator -->
								<div class="wsts-seo-preview-card">
									<div class="wsts-seo-preview-header">
										<span class="wsts-seo-badge"><?php esc_html_e( 'LIVE GOOGLE DISCOVER PREVIEW', 'web-stories-templates-studio' ); ?></span>
										<button type="button" class="wsts-btn-auto-seo" id="btnAutoGenerateSeo" title="<?php esc_attr_e( 'Auto-fill SEO title and description from story slides', 'web-stories-templates-studio' ); ?>">
											⚡ <?php esc_html_e( 'Auto-Fill from Story Content', 'web-stories-templates-studio' ); ?>
										</button>
									</div>
									<div class="wsts-discover-mockup">
										<div class="wsts-discover-img-wrap">
											<img src="" id="discoverPreviewImg" alt="Cover" class="wsts-discover-img">
											<span class="wsts-discover-stories-icon" title="Web Story">
												<svg width="14" height="14" viewBox="0 0 24 24" fill="#ffffff"><rect x="3" y="2" width="14" height="20" rx="3" fill="#ffffff" fill-opacity="0.9"/><rect x="8" y="2" width="13" height="20" rx="3" stroke="#000" stroke-width="1.5" fill="#ffffff"/><path d="M12 7h5M12 11h5M12 15h3" stroke="#2563eb" stroke-width="2" stroke-linecap="round"/></svg>
											</span>
										</div>
										<div class="wsts-discover-content">
											<div class="wsts-discover-source">
												<img src="" id="discoverPreviewFavicon" alt="Favicon" class="wsts-discover-favicon">
												<span class="wsts-discover-publisher" id="discoverPreviewPublisher">Publisher Name</span>
												<span class="wsts-discover-dot">&bull;</span>
												<span class="wsts-discover-time">1 hr ago</span>
											</div>
											<h4 class="wsts-discover-headline" id="discoverPreviewHeadline">Your Punchy Web Story Headline Here</h4>
											<p class="wsts-discover-desc" id="discoverPreviewDesc">Compelling snippet summarizing the story hook for Google Discover readers.</p>
										</div>
									</div>
								</div>

								<!-- SEO Fields Form -->
								<div class="wsts-seo-form">
									<div class="wsts-form-group">
										<div class="wsts-label-with-value">
											<label class="wsts-label" for="customSeoTitle"><?php esc_html_e( 'SEO Headline (Google Discover & SERP)', 'web-stories-templates-studio' ); ?></label>
											<span class="wsts-char-count" id="seoTitleCharCount">0 / 70</span>
										</div>
										<input type="text" id="customSeoTitle" class="wsts-input" placeholder="Punchy headline under 70 characters...">
										<small class="wsts-help"><?php esc_html_e( 'Google Discover truncates headlines over 70 characters. Keep it compelling and clickable.', 'web-stories-templates-studio' ); ?></small>
									</div>

									<div class="wsts-form-group">
										<div class="wsts-label-with-value">
											<label class="wsts-label" for="customSeoDesc"><?php esc_html_e( 'Meta Description / Discover Snippet', 'web-stories-templates-studio' ); ?></label>
											<span class="wsts-char-count" id="seoDescCharCount">0 / 160</span>
										</div>
										<textarea id="customSeoDesc" class="wsts-input wsts-textarea" rows="3" placeholder="Engaging 1-2 sentence overview summarizing what viewers will discover in this story..."></textarea>
										<small class="wsts-help"><?php esc_html_e( 'Target 120-155 characters for optimal Google search snippet readability.', 'web-stories-templates-studio' ); ?></small>
									</div>

									<div class="wsts-form-row wsts-color-row">
										<div class="wsts-form-group">
											<label class="wsts-label" for="customSchemaType"><?php esc_html_e( 'Schema.org Structured Data Type', 'web-stories-templates-studio' ); ?></label>
											<select id="customSchemaType" class="wsts-input">
												<option value="NewsArticle" selected>NewsArticle (Recommended for Discover)</option>
												<option value="Article">Article (General Editorial & Lifestyle)</option>
												<option value="BlogPosting">BlogPosting (Blogs & Personal)</option>
											</select>
											<small class="wsts-help"><?php esc_html_e( 'Injected as Schema.org JSON-LD structured data with pennion author credits.', 'web-stories-templates-studio' ); ?></small>
										</div>
										<div class="wsts-form-group">
											<label class="wsts-label" for="customCanonicalUrl"><?php esc_html_e( 'Canonical URL (Optional)', 'web-stories-templates-studio' ); ?></label>
											<input type="text" id="customCanonicalUrl" class="wsts-input" placeholder="https://yourdomain.com/story-permalink">
											<small class="wsts-help"><?php esc_html_e( 'Defaults automatically to WordPress story permalink on publish.', 'web-stories-templates-studio' ); ?></small>
										</div>
									</div>

									<div class="wsts-form-group">
										<label class="wsts-label" for="customOgImage"><?php esc_html_e( 'OpenGraph & Social Share Poster (1200×630 or 9:16)', 'web-stories-templates-studio' ); ?></label>
										<div class="wsts-input-btn-group">
											<input type="text" id="customOgImage" class="wsts-input" placeholder="Social poster image URL...">
											<button type="button" class="button wsts-smart-image-btn" data-target="og"><?php esc_html_e( '✨ Unsplash / Pexels', 'web-stories-templates-studio' ); ?></button>
											<button type="button" class="button wsts-media-picker-btn" data-target="#customOgImage"><?php esc_html_e( 'Upload', 'web-stories-templates-studio' ); ?></button>
										</div>
										<small class="wsts-help"><?php esc_html_e( 'Image shown when story is shared on WhatsApp, Twitter, Facebook, and Slack.', 'web-stories-templates-studio' ); ?></small>
									</div>
								</div>
							</div>

							<!-- TAB 5: MONETIZATION & AMBIENT AUDIO -->
							<div class="wsts-tab-content" id="tab-monetize">
								<div class="wsts-section-intro">
									<h4 class="wsts-section-title"><?php esc_html_e( 'Monetization, Analytics & Audio', 'web-stories-templates-studio' ); ?></h4>
									<p class="wsts-section-desc"><?php esc_html_e( 'Configure Google AdSense auto-ads, GA4 telemetry, and royalty-free ambient soundtrack playback.', 'web-stories-templates-studio' ); ?></p>
								</div>

								<!-- Google AdSense for Web Stories Card -->
								<div class="wsts-typo-card">
									<div class="wsts-typo-card-header">
										<span class="wsts-typo-badge">ADSENSE</span>
										<strong><?php esc_html_e( 'Google AdSense Auto-Ads (AMP Stories)', 'web-stories-templates-studio' ); ?></strong>
									</div>
									<div class="wsts-form-row wsts-color-row">
										<div class="wsts-form-group">
											<label class="wsts-label" for="customAdSenseClient"><?php esc_html_e( 'AdSense Publisher ID', 'web-stories-templates-studio' ); ?></label>
											<input type="text" id="customAdSenseClient" class="wsts-input" placeholder="ca-pub-XXXXXXXXXXXXXXXX">
											<small class="wsts-help"><?php esc_html_e( 'Your Google AdSense client ID (starts with ca-pub-).', 'web-stories-templates-studio' ); ?></small>
										</div>
										<div class="wsts-form-group">
											<label class="wsts-label" for="customAdSenseSlot"><?php esc_html_e( 'Story Ad Slot ID', 'web-stories-templates-studio' ); ?></label>
											<input type="text" id="customAdSenseSlot" class="wsts-input" placeholder="e.g. 1234567890">
											<small class="wsts-help"><?php esc_html_e( 'Responsive ad unit slot ID created in AdSense.', 'web-stories-templates-studio' ); ?></small>
										</div>
									</div>
									<small class="wsts-help" style="margin-top: 8px;">
										<?php esc_html_e( 'AMP auto-ads automatically injects native interstitial ads between slides according to Google retention density guidelines.', 'web-stories-templates-studio' ); ?>
									</small>
								</div>

								<!-- Google Analytics 4 (GA4) Card -->
								<div class="wsts-typo-card">
									<div class="wsts-typo-card-header">
										<span class="wsts-typo-badge">GA4</span>
										<strong><?php esc_html_e( 'Google Analytics 4 Telemetry', 'web-stories-templates-studio' ); ?></strong>
									</div>
									<div class="wsts-form-group">
										<label class="wsts-label" for="customGa4Id"><?php esc_html_e( 'GA4 Measurement ID', 'web-stories-templates-studio' ); ?></label>
										<input type="text" id="customGa4Id" class="wsts-input" placeholder="G-XXXXXXXXXX">
										<small class="wsts-help"><?php esc_html_e( 'Tracks story page views, completion rates, and user drop-off directly inside your GA4 reports.', 'web-stories-templates-studio' ); ?></small>
									</div>
								</div>

								<!-- Ambient Audio Studio Card -->
								<div class="wsts-typo-card">
									<div class="wsts-typo-card-header">
										<span class="wsts-typo-badge">AUDIO</span>
										<strong><?php esc_html_e( 'Background Audio Studio', 'web-stories-templates-studio' ); ?></strong>
									</div>
									<div class="wsts-form-group">
										<label class="wsts-label" for="customAudioPreset"><?php esc_html_e( 'Curated Royalty-Free Loop Presets', 'web-stories-templates-studio' ); ?></label>
										<select id="customAudioPreset" class="wsts-input">
											<option value=""><?php esc_html_e( 'None / Muted (Default)', 'web-stories-templates-studio' ); ?></option>
											<option value="https://assets.mixkit.co/music/preview/mixkit-chill-bro-494.mp3"><?php esc_html_e( 'Lo-Fi Chill Hop (Relaxing & Casual)', 'web-stories-templates-studio' ); ?></option>
											<option value="https://assets.mixkit.co/music/preview/mixkit-tech-house-vibes-130.mp3"><?php esc_html_e( 'Cinematic Tech Pulse (Modern & Deep)', 'web-stories-templates-studio' ); ?></option>
											<option value="https://assets.mixkit.co/music/preview/mixkit-serene-view-443.mp3"><?php esc_html_e( 'Acoustic Morning Breeze (Lifestyle & Travel)', 'web-stories-templates-studio' ); ?></option>
											<option value="https://assets.mixkit.co/music/preview/mixkit-raising-me-higher-34.mp3"><?php esc_html_e( 'Upbeat Electronic Anthem (Fitness & Energy)', 'web-stories-templates-studio' ); ?></option>
											<option value="custom"><?php esc_html_e( 'Custom Audio MP3 URL...', 'web-stories-templates-studio' ); ?></option>
										</select>
									</div>
									<div class="wsts-form-group">
										<label class="wsts-label" for="customBackgroundAudio"><?php esc_html_e( 'Background Audio URL (.mp3)', 'web-stories-templates-studio' ); ?></label>
										<div class="wsts-input-btn-group">
											<input type="text" id="customBackgroundAudio" class="wsts-input" placeholder="https://.../music.mp3">
											<button type="button" class="button wsts-btn-preview-audio" id="btnPreviewAudio">
												▶ <?php esc_html_e( 'Test Play', 'web-stories-templates-studio' ); ?>
											</button>
											<button type="button" class="button wsts-media-picker-btn" id="btnUploadAudio" data-target="#customBackgroundAudio">
												📁 <?php esc_html_e( 'Upload MP3', 'web-stories-templates-studio' ); ?>
											</button>
										</div>
										<small class="wsts-help"><?php esc_html_e( 'Background audio smoothly loops across every slide with standard mute/unmute controls in the story runtime.', 'web-stories-templates-studio' ); ?></small>
									</div>
								</div>
							</div>

							<!-- TAB 6: SIZING AUDIT -->
							<div class="wsts-tab-content" id="tab-specs">
								<div class="wsts-audit-card">
									<h4 class="wsts-audit-title"><?php esc_html_e( 'Compliance & Specifications Checklist', 'web-stories-templates-studio' ); ?></h4>
									<div class="wsts-audit-item is-pass">
										<span class="wsts-audit-icon"><svg width="14" height="14" viewBox="0 0 16 16" fill="#34d399"><path d="M13.78 4.22a.75.75 0 0 1 0 1.06l-7.25 7.25a.75.75 0 0 1-1.06 0L2.22 9.28a.751.751 0 0 1 .018-1.042.751.751 0 0 1 1.042-.018L6 10.94l6.72-6.72a.75.75 0 0 1 1.06 0Z"/></svg></span>
										<div>
											<strong>Canvas Dimensions:</strong> 1080 &times; 1920 pixels (9:16 portrait).
										</div>
									</div>
									<div class="wsts-audit-item is-pass">
										<span class="wsts-audit-icon"><svg width="14" height="14" viewBox="0 0 16 16" fill="#34d399"><path d="M13.78 4.22a.75.75 0 0 1 0 1.06l-7.25 7.25a.75.75 0 0 1-1.06 0L2.22 9.28a.751.751 0 0 1 .018-1.042.751.751 0 0 1 1.042-.018L6 10.94l6.72-6.72a.75.75 0 0 1 1.06 0Z"/></svg></span>
										<div>
											<strong>Safe Zone Cleared:</strong> 220px top & 250px bottom margins protected.
										</div>
									</div>
									<div class="wsts-audit-item" id="auditTitleLength">
										<span class="wsts-audit-icon"><svg width="14" height="14" viewBox="0 0 16 16" fill="#a78bfa"><circle cx="8" cy="8" r="6" stroke="currentColor" stroke-width="1.5" fill="none"/><line x1="8" y1="7" x2="8" y2="11" stroke="currentColor" stroke-width="1.5"/><circle cx="8" cy="4.5" r="0.75" fill="currentColor"/></svg></span>
										<div>
											<strong>Title Word Count:</strong> <span id="auditTitleCount">Optimal</span>
										</div>
									</div>
									<div class="wsts-audit-item" id="auditTextCheck">
										<span class="wsts-audit-icon"><svg width="14" height="14" viewBox="0 0 16 16" fill="#a78bfa"><circle cx="8" cy="8" r="6" stroke="currentColor" stroke-width="1.5" fill="none"/><line x1="8" y1="7" x2="8" y2="11" stroke="currentColor" stroke-width="1.5"/><circle cx="8" cy="4.5" r="0.75" fill="currentColor"/></svg></span>
										<div>
											<strong>Slide Copy Density:</strong> Under 280 characters per slide.
										</div>
									</div>
									<div class="wsts-audit-item" id="auditSlideCount">
										<span class="wsts-audit-icon"><svg width="14" height="14" viewBox="0 0 16 16" fill="#34d399"><path d="M13.78 4.22a.75.75 0 0 1 0 1.06l-7.25 7.25a.75.75 0 0 1-1.06 0L2.22 9.28a.751.751 0 0 1 .018-1.042.751.751 0 0 1 1.042-.018L6 10.94l6.72-6.72a.75.75 0 0 1 1.06 0Z"/></svg></span>
										<div>
											<strong>Story Length & Retention:</strong> <span id="auditSlideCountText">Optimal</span>
										</div>
									</div>
								</div>
							</div>
						</div>

						<!-- Right: Live Interactive Phone Simulator -->
						<div class="wsts-preview-panel">
							<div class="wsts-phone-mockup">
								<div class="wsts-dynamic-island">
									<span class="wsts-island-sensor"></span>
									<span class="wsts-island-lens"></span>
								</div>
								<div class="wsts-phone-screen" id="wstsPhoneScreen">
									<!-- Safe Zone Guide Overlay -->
									<div class="wsts-safezone-overlay" id="safeZoneOverlay">
										<div class="wsts-safe-top-zone">
											<span>TOP CLEARANCE &bull; 220px</span>
										</div>
										<div class="wsts-safe-center-box">
											<span class="wsts-safe-label">SAFE CONTENT AREA (920 &times; 1450)</span>
										</div>
										<div class="wsts-safe-bottom-zone">
											<span>GESTURE CLEARANCE &bull; 250px</span>
										</div>
									</div>

									<!-- Story Progress Bars -->
									<div class="wsts-sim-progress" id="simProgressBars">
										<!-- Injected dynamically -->
									</div>

									<!-- Story Header Bar -->
									<div class="wsts-sim-header">
										<div class="wsts-sim-publisher">
											<img src="" id="simPublisherLogo" alt="Logo" class="wsts-sim-logo">
											<span id="simPublisherName">Publisher</span>
										</div>
										<div class="wsts-sim-icons">
											<span class="wsts-sim-icon-btn">
												<svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="6" y="4" width="4" height="16"></rect><rect x="14" y="4" width="4" height="16"></rect></svg>
											</span>
											<span class="wsts-sim-icon-btn">
												<svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
											</span>
										</div>
									</div>

									<!-- Active Slide Display -->
									<div class="wsts-sim-slide" id="simActiveSlide">
										<div class="wsts-sim-bg" id="simBg"></div>
										<div class="wsts-sim-scrim" id="simScrim"></div>
										<div class="wsts-sim-content">
											<div class="wsts-sim-badge" id="simBadge">TAG</div>
											<div class="wsts-sim-stat-box" id="simStatBox">
												<span class="wsts-sim-stat-num" id="simStatNum">01</span>
												<span class="wsts-sim-stat-lbl" id="simStatLbl">LABEL</span>
											</div>
											<h2 class="wsts-sim-headline" id="simHeadline">Headline Text</h2>
											<p class="wsts-sim-body" id="simBody">Body copy text goes here.</p>
											<div class="wsts-sim-cta" id="simCtaBox">
												<a href="#" class="wsts-sim-cta-btn" id="simCtaBtn">Swipe Up &rarr;</a>
											</div>
										</div>
									</div>

									<!-- Simulator Nav Tap Zones (strictly zero native WP button styling) -->
									<button type="button" class="wsts-sim-tap wsts-sim-tap-prev" id="simPrevBtn" aria-label="<?php esc_attr_e( 'Previous Page', 'web-stories-templates-studio' ); ?>"></button>
									<button type="button" class="wsts-sim-tap wsts-sim-tap-next" id="simNextBtn" aria-label="<?php esc_attr_e( 'Next Page', 'web-stories-templates-studio' ); ?>"></button>
								</div>
								<div class="wsts-phone-home-indicator"></div>

								<!-- Floating Desktop Nav Chevrons (Flanking Phone Mockup) -->
								<button type="button" class="wsts-sim-chevron wsts-sim-chevron-prev" id="simChevronPrev" aria-label="<?php esc_attr_e( 'Previous Slide', 'web-stories-templates-studio' ); ?>">&lsaquo;</button>
								<button type="button" class="wsts-sim-chevron wsts-sim-chevron-next" id="simChevronNext" aria-label="<?php esc_attr_e( 'Next Slide', 'web-stories-templates-studio' ); ?>">&rsaquo;</button>
							</div>

							<!-- Slide Index Indicator -->
							<div class="wsts-sim-page-indicator">
								<button type="button" class="wsts-nav-pill-btn" id="btnSimPrev">&larr; <?php esc_html_e( 'Prev', 'web-stories-templates-studio' ); ?></button>
								<span id="simCurrentPageLabel">Page 1 / 4</span>
								<button type="button" class="wsts-nav-pill-btn" id="btnSimNext"><?php esc_html_e( 'Next', 'web-stories-templates-studio' ); ?> &rarr;</button>
							</div>
						</div>
					</div>

					<!-- Modal Footer Actions -->
					<div class="wsts-modal-footer">
						<div class="wsts-footer-left">
							<button type="button" class="button wsts-btn-code" id="btnExportAmpHtml">
								&lt;/&gt; <?php esc_html_e( 'Export Standalone AMP', 'web-stories-templates-studio' ); ?>
							</button>
							<button type="button" class="button wsts-btn-social-export" id="btnExportSocialSlices" title="<?php esc_attr_e( 'Export all slides as 1080x1920 PNG images for Instagram, TikTok & WhatsApp', 'web-stories-templates-studio' ); ?>">
								📱 <?php esc_html_e( 'Export Social Slices (PNG)', 'web-stories-templates-studio' ); ?>
							</button>
							<span class="wsts-footer-credit"><?php esc_html_e( 'Crafted by pennion', 'web-stories-templates-studio' ); ?></span>
						</div>
						<div class="wsts-footer-right">
							<button type="button" class="button wsts-btn-apple-preview" id="btnLivePreviewStory">
								👁 <?php esc_html_e( 'Preview Story', 'web-stories-templates-studio' ); ?>
							</button>
							<button type="button" class="button button-secondary" id="btnSaveDraft">
								<?php esc_html_e( 'Save Draft', 'web-stories-templates-studio' ); ?>
							</button>
							<button type="button" class="button button-primary wsts-btn-publish" id="btnPublishStory">
								<?php esc_html_e( 'Create & Publish Story', 'web-stories-templates-studio' ); ?>
							</button>
						</div>
					</div>
				</div>
			</div>

			<!-- Live Interactive Story Preview Modal (Before Publishing) -->
			<div class="wsts-modal" id="wstsLivePreviewModal" style="display:none;">
				<div class="wsts-modal-backdrop"></div>
				<div class="wsts-modal-container wsts-preview-modal-container">
					<div class="wsts-modal-header wsts-preview-modal-header">
						<div class="wsts-modal-title-box">
							<span class="wsts-modal-badge"><?php esc_html_e( 'FULL AMP RUNTIME PREVIEW', 'web-stories-templates-studio' ); ?></span>
							<h3 class="wsts-preview-modal-title" id="livePreviewTitle"><?php esc_html_e( 'Story Preview', 'web-stories-templates-studio' ); ?></h3>
						</div>
						<div class="wsts-preview-device-switch">
							<button type="button" class="wsts-device-pill is-active" data-mode="mobile" id="btnPreviewMobile">
								📱 <?php esc_html_e( 'Mobile 9:16', 'web-stories-templates-studio' ); ?>
							</button>
							<button type="button" class="wsts-device-pill" data-mode="desktop" id="btnPreviewDesktop">
								💻 <?php esc_html_e( 'Full Stage', 'web-stories-templates-studio' ); ?>
							</button>
							<button type="button" class="wsts-device-pill" id="btnOpenStandalonePreview" title="<?php esc_attr_e( 'Open standalone in new browser tab', 'web-stories-templates-studio' ); ?>">
								↗ <?php esc_html_e( 'New Tab', 'web-stories-templates-studio' ); ?>
							</button>
						</div>
						<button type="button" class="wsts-modal-close" id="btnLivePreviewClose">&times;</button>
					</div>
					<div class="wsts-modal-body wsts-preview-modal-body">
						<div class="wsts-preview-frame-wrap is-mobile" id="livePreviewFrameWrap">
							<iframe id="wstsLivePreviewIframe" class="wsts-live-preview-iframe" title="<?php esc_attr_e( 'Live Story Preview', 'web-stories-templates-studio' ); ?>"></iframe>
						</div>
					</div>
					<div class="wsts-modal-footer">
						<div class="wsts-footer-left">
							<button type="button" class="button wsts-btn-social-export" id="btnPreviewExportSocialSlices" title="<?php esc_attr_e( 'Export all slides as 1080x1920 PNG images for Instagram, TikTok & WhatsApp', 'web-stories-templates-studio' ); ?>">
								📱 <?php esc_html_e( 'Export Social Slices (PNG)', 'web-stories-templates-studio' ); ?>
							</button>
							<span class="wsts-footer-credit"><?php esc_html_e( 'Google AMP Story Runtime • Live Safe Zones & In-Page Logo Bar', 'web-stories-templates-studio' ); ?></span>
						</div>
						<div class="wsts-footer-right">
							<button type="button" class="button button-secondary" id="btnClosePreviewModal">
								<?php esc_html_e( 'Back to Customizer', 'web-stories-templates-studio' ); ?>
							</button>
							<button type="button" class="button button-primary wsts-btn-publish" id="btnPreviewPublishStory">
								<?php esc_html_e( 'Publish Story Now', 'web-stories-templates-studio' ); ?> &rarr;
							</button>
						</div>
					</div>
				</div>
			</div>

			<!-- AMP Export Modal -->
			<div class="wsts-modal" id="wstsExportModal" style="display:none;">
				<div class="wsts-modal-backdrop"></div>
				<div class="wsts-modal-container wsts-modal-small">
					<div class="wsts-modal-header">
						<h3><?php esc_html_e( 'Valid Standalone Google AMP Story Code', 'web-stories-templates-studio' ); ?></h3>
						<button type="button" class="wsts-modal-close" id="btnExportModalClose">&times;</button>
					</div>
					<div class="wsts-modal-body">
						<p><?php esc_html_e( 'This is 100% compliant AMP Story code adhering to Google Web Stories guidelines. You can embed it, host it, or test it via Google AMP Validator.', 'web-stories-templates-studio' ); ?></p>
						<textarea id="wstsExportCodeTextarea" class="wsts-code-textarea" readonly rows="12"></textarea>
					</div>
					<div class="wsts-modal-footer">
						<button type="button" class="button button-primary" id="btnCopyAmpCode">
							<?php esc_html_e( 'Copy to Clipboard', 'web-stories-templates-studio' ); ?>
						</button>
					</div>
				</div>
			</div>

			<!-- Smart Visual Media Studio (Unsplash, Pexels & Custom Uploads) -->
			<div class="wsts-modal" id="wstsImageFinderModal" style="display:none;">
				<div class="wsts-modal-backdrop"></div>
				<div class="wsts-modal-container wsts-modal-medium">
					<div class="wsts-modal-header wsts-media-modal-header">
						<div class="wsts-modal-title-box">
							<h3 class="wsts-media-modal-title"><?php esc_html_e( 'Visual Media Studio', 'web-stories-templates-studio' ); ?></h3>
							<p class="wsts-media-modal-sub"><?php esc_html_e( 'Curated 1080×1920 vertical photography from Unsplash, Pexels & Media Library', 'web-stories-templates-studio' ); ?></p>
						</div>
						<button type="button" class="wsts-modal-close" id="btnImageFinderClose" aria-label="<?php esc_attr_e( 'Close', 'web-stories-templates-studio' ); ?>">&times;</button>
					</div>
					<div class="wsts-modal-body wsts-finder-body">
						<!-- Top Action Bar: Search & Action Buttons -->
						<div class="wsts-finder-topbar">
							<div class="wsts-finder-search-wrap">
								<span class="wsts-finder-search-icon">🔍</span>
								<input type="text" id="wstsImageSearchInput" class="wsts-input wsts-finder-search-input" placeholder="<?php esc_attr_e( 'Search vertical photos (e.g. fashion, tech, coffee, fitness, luxury)...', 'web-stories-templates-studio' ); ?>">
								<button type="button" class="button button-primary wsts-finder-search-btn" id="btnSearchImages"><?php esc_html_e( 'Search', 'web-stories-templates-studio' ); ?></button>
							</div>
							<div class="wsts-finder-actions-wrap">
								<button type="button" class="button wsts-btn-autofind" id="btnAutoFindForSlide" title="<?php esc_attr_e( 'Auto-detect keywords from current slide and find matching photos', 'web-stories-templates-studio' ); ?>">
									✨ <?php esc_html_e( 'Auto-Match Slide', 'web-stories-templates-studio' ); ?>
								</button>
								<button type="button" class="button wsts-btn-upload-custom" id="btnUploadCustomFromFinder" title="<?php esc_attr_e( 'Upload your custom image from computer or pick from WordPress Media Library', 'web-stories-templates-studio' ); ?>">
									📁 <?php esc_html_e( 'Upload Custom Image', 'web-stories-templates-studio' ); ?>
								</button>
							</div>
						</div>

						<!-- Source Filter Tabs (Clean Segmented Bar) -->
						<div class="wsts-source-tabs" id="wstsSourceTabs">
							<button type="button" class="wsts-source-tab is-active" data-source="all">
								<?php esc_html_e( 'All Photos', 'web-stories-templates-studio' ); ?>
							</button>
							<button type="button" class="wsts-source-tab" data-source="unsplash">
								<?php esc_html_e( 'Unsplash', 'web-stories-templates-studio' ); ?>
							</button>
							<button type="button" class="wsts-source-tab" data-source="pexels">
								<?php esc_html_e( 'Pexels', 'web-stories-templates-studio' ); ?>
							</button>
							<button type="button" class="wsts-source-tab" data-source="custom">
								<?php esc_html_e( 'Media Library', 'web-stories-templates-studio' ); ?>
							</button>
						</div>

						<!-- Topic Pills (1-Line Horizontal Scroller) -->
						<div class="wsts-finder-pills-wrap">
							<div class="wsts-finder-pills" id="wstsFinderPills">
								<button type="button" class="wsts-finder-pill is-active" data-query="all"><?php esc_html_e( 'All Topics', 'web-stories-templates-studio' ); ?></button>
								<button type="button" class="wsts-finder-pill" data-query="fashion"><?php esc_html_e( 'Fashion & Luxury', 'web-stories-templates-studio' ); ?></button>
								<button type="button" class="wsts-finder-pill" data-query="tech"><?php esc_html_e( 'Tech & AI', 'web-stories-templates-studio' ); ?></button>
								<button type="button" class="wsts-finder-pill" data-query="food"><?php esc_html_e( 'Food & Cafe', 'web-stories-templates-studio' ); ?></button>
								<button type="button" class="wsts-finder-pill" data-query="fitness"><?php esc_html_e( 'Fitness & Workout', 'web-stories-templates-studio' ); ?></button>
								<button type="button" class="wsts-finder-pill" data-query="travel"><?php esc_html_e( 'Travel & Nature', 'web-stories-templates-studio' ); ?></button>
								<button type="button" class="wsts-finder-pill" data-query="cars"><?php esc_html_e( 'Cars & Speed', 'web-stories-templates-studio' ); ?></button>
								<button type="button" class="wsts-finder-pill" data-query="business"><?php esc_html_e( 'Business & Architecture', 'web-stories-templates-studio' ); ?></button>
							</div>
						</div>

						<!-- Image Grid -->
						<div class="wsts-finder-grid" id="wstsImageResultsGrid">
							<!-- Injected dynamically via JS -->
						</div>
					</div>
					<div class="wsts-modal-footer">
						<div class="wsts-footer-left">
							<span class="wsts-footer-credit"><?php esc_html_e( '100% Google Web Stories 1080×1920 (9:16 Portrait) Optimized • Free Commercial License', 'web-stories-templates-studio' ); ?></span>
						</div>
						<div class="wsts-footer-right">
							<button type="button" class="button button-secondary" id="btnImageFinderCancel">
								<?php esc_html_e( 'Close', 'web-stories-templates-studio' ); ?>
							</button>
						</div>
					</div>
				</div>
			</div>

			<!-- 1-Click Blog Post Auto-Story Generator Modal -->
			<div class="wsts-modal" id="wstsAutoPostModal" style="display:none;">
				<div class="wsts-modal-backdrop"></div>
				<div class="wsts-modal-container wsts-modal-small">
					<div class="wsts-modal-header">
						<div class="wsts-modal-title-box">
							<span class="wsts-modal-badge"><?php esc_html_e( 'POWERHOUSE FEATURE', 'web-stories-templates-studio' ); ?></span>
							<h3 class="wsts-modal-title"><?php esc_html_e( 'Auto-Create Story from Blog Post', 'web-stories-templates-studio' ); ?></h3>
						</div>
						<button type="button" class="wsts-modal-close" id="btnAutoPostModalClose">&times;</button>
					</div>
					<div class="wsts-modal-body wsts-autopost-body">
						<p class="wsts-modal-desc">
							<?php esc_html_e( 'Select any published WordPress blog post to instantly distill it into a high-retention 4-slide Google Web Story with matched photography, key takeaways, and an outlink swipe-up button.', 'web-stories-templates-studio' ); ?>
						</p>

						<div class="wsts-form-group">
							<label class="wsts-label" for="autoPostSelect"><?php esc_html_e( 'Select Recent WordPress Post', 'web-stories-templates-studio' ); ?></label>
							<select id="autoPostSelect" class="wsts-input">
								<option value=""><?php esc_html_e( '-- Choose a published post --', 'web-stories-templates-studio' ); ?></option>
								<?php if ( ! empty( $recent_posts ) ) : ?>
									<?php foreach ( $recent_posts as $p ) : ?>
										<option value="<?php echo esc_attr( $p->ID ); ?>">
											<?php echo esc_html( wp_trim_words( $p->post_title, 10, '...' ) ); ?> (<?php echo esc_html( get_the_date( 'M j', $p->ID ) ); ?>)
										</option>
									<?php endforeach; ?>
								<?php endif; ?>
							</select>
						</div>

						<div class="wsts-form-group">
							<label class="wsts-label" for="autoPostCategory"><?php esc_html_e( 'Select Story Style & Theme Niche', 'web-stories-templates-studio' ); ?></label>
							<select id="autoPostCategory" class="wsts-input">
								<?php foreach ( $categories as $c_slug => $c_data ) : ?>
									<?php if ( 'all' === $c_slug || 'favorites' === $c_slug ) continue; ?>
									<option value="<?php echo esc_attr( $c_slug ); ?>"><?php echo esc_html( $c_data['label'] ); ?></option>
								<?php endforeach; ?>
							</select>
						</div>

						<div class="wsts-autopost-status" id="autoPostStatus" style="display:none; margin-top:14px; padding:12px 16px; border-radius:8px; background:#f0fdf4; border:1px solid #bbf7d0; color:#15803d; font-size:12.5px;">
							<span class="wsts-spinner-mini" style="display:inline-block; margin-right:6px;">⚡</span>
							<span id="autoPostStatusText"><?php esc_html_e( 'Analyzing post, generating slides & matching photography...', 'web-stories-templates-studio' ); ?></span>
						</div>
					</div>
					<div class="wsts-modal-footer">
						<div class="wsts-footer-left">
							<span class="wsts-footer-credit"><?php esc_html_e( '1-Click Conversion • Pennion Engine', 'web-stories-templates-studio' ); ?></span>
						</div>
						<div class="wsts-footer-right">
							<button type="button" class="button button-secondary" id="btnAutoPostCancel"><?php esc_html_e( 'Cancel', 'web-stories-templates-studio' ); ?></button>
							<button type="button" class="button button-primary" id="btnAutoPostGenerate">
								✨ <?php esc_html_e( 'Generate 4-Slide Story', 'web-stories-templates-studio' ); ?>
							</button>
						</div>
					</div>
				</div>
			</div>

			<!-- In-App Notification Toast / Modal (Apple-Grade Liquid Dialog) -->
			<div class="wsts-modal wsts-notify-backdrop-wrap" id="wstsNotifyModal" style="display:none;">
				<div class="wsts-modal-backdrop"></div>
				<div class="wsts-notify-card">
					<button type="button" class="wsts-modal-close wsts-notify-close-btn" id="btnNotifyClose">&times;</button>
					<div class="wsts-notify-icon-circle">
						<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" id="wstsNotifyIconSvg">
							<path d="M20 6L9 17l-5-5"/>
						</svg>
					</div>
					<h3 class="wsts-notify-title" id="wstsNotifyTitle"><?php esc_html_e( 'Notification', 'web-stories-templates-studio' ); ?></h3>
					<p class="wsts-notify-body-text" id="wstsNotifyMessage"></p>
					<div id="wstsNotifyExtra" class="wsts-notify-extra-content"></div>
					<div class="wsts-notify-buttons" id="wstsNotifyActions">
						<button type="button" class="button wsts-btn-apple-pill-primary" id="btnNotifyOk"><?php esc_html_e( 'OK', 'web-stories-templates-studio' ); ?></button>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Render Sizes & Specs Guide page
	 */
	public function render_sizes_guide_page() {
		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'web-stories-templates-studio' ) );
		}
		?>
		<div class="wrap wsts-studio-wrap">
			<header class="wsts-header">
				<div class="wsts-header-content">
					<div class="wsts-brand-badge">DEVELOPER & CREATOR REFERENCE</div>
					<h1 class="wsts-title">Google Web Stories: Sizes, Safe Zones & Specifications</h1>
					<p class="wsts-subtitle">
						The definitive cheat sheet for building high-performing, Google Discover-eligible Web Stories.
					</p>
				</div>
				<div>
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=web-stories-templates' ) ); ?>" class="button button-primary">
						&larr; <?php esc_html_e( 'Back to Template Studio', 'web-stories-templates-studio' ); ?>
					</a>
				</div>
			</header>

			<div class="wsts-specs-container">
				<!-- Visual Canvas Diagram -->
				<div class="wsts-spec-diagram-card">
					<h3>Canvas & Safe Zone Architecture</h3>
					<div class="wsts-diagram-box">
						<div class="wsts-diagram-top">
							<span>TOP UNSAFE ZONE (160px – 220px)</span>
							<small>Story Progress Bar, Publisher Icon, Story Title, Audio Mute, Close Button</small>
						</div>
						<div class="wsts-diagram-safe">
							<strong>CENTRAL GOOGLE SAFE ZONE (~920px × 1450px)</strong>
							<p>All Headlines, Subtitles, Badges, and Core Body Copy must be anchored here.</p>
						</div>
						<div class="wsts-diagram-bottom">
							<span>BOTTOM UNSAFE ZONE (180px – 250px)</span>
							<small>Swipe-Up Call to Action, Page Outlinks, Device Gesture Indicators</small>
						</div>
					</div>
				</div>

				<!-- Specifications Grid -->
				<div class="wsts-specs-grid">
					<div class="wsts-spec-item-card">
						<h4>Canvas & Dimensions</h4>
						<ul>
							<li><strong>Fullbleed Canvas:</strong> 1080 × 1920 px (9:16 Portrait)</li>
							<li><strong>Poster Cover Image:</strong> 640 × 853 px (3:4 ratio minimum, recommended 960×1280 px)</li>
							<li><strong>Square Publisher Logo:</strong> 96 × 96 px minimum (recommended 512×512 px)</li>
							<li><strong>Resolution:</strong> High density 1080p for retina display clarity</li>
						</ul>
					</div>

					<div class="wsts-spec-item-card">
						<h4>Safe Zone Clearances</h4>
						<ul>
							<li><strong>Top Clearance:</strong> Minimum 220 px padding</li>
							<li><strong>Bottom Clearance:</strong> Minimum 250 px padding</li>
							<li><strong>Side Clearance:</strong> Minimum 80 px left & right</li>
							<li><strong>Usable Core Area:</strong> 920 × 1450 px centered</li>
						</ul>
					</div>

					<div class="wsts-spec-item-card">
						<h4>Typography & Copy Limits</h4>
						<ul>
							<li><strong>Text per Slide:</strong> Max 280 characters (Google strict limit)</li>
							<li><strong>Optimal Slide Text:</strong> Under 180 characters for fast retention</li>
							<li><strong>Headline Length:</strong> Under 10 words</li>
							<li><strong>Color Contrast:</strong> Minimum 4.5:1 ratio (always use gradient scrim overlays)</li>
						</ul>
					</div>

					<div class="wsts-spec-item-card">
						<h4>Media & Video Guidelines</h4>
						<ul>
							<li><strong>Video Duration:</strong> 4 to 15 seconds per slide (max 60s)</li>
							<li><strong>Video Format:</strong> MP4 (H.264 video codec, AAC audio)</li>
							<li><strong>Video Bitrate:</strong> 1.5 – 2.5 Mbps target (max 4 Mbps)</li>
							<li><strong>Image Size:</strong> Under 300 KB per image (WebP or compressed JPEG)</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
}
