<?php
/**
 * Template Registry
 *
 * Houses and registers the curated "Sick Templates" library with full design metadata,
 * safe-zone adherence, typography sets, and customizable page blocks.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WSTS_Template_Registry {

	private static $instance = null;
	private $templates       = array();

	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		$this->register_default_templates();
	}

	/**
	 * Register the 6 Sick Templates
	 */
	private function register_default_templates() {

		// 1. CYBER TECH RADAR
		$this->templates['cyber-tech-radar'] = array(
			'id'                => 'cyber-tech-radar',
			'name'              => 'Cyber Tech Radar',
			'tagline'           => 'Apple Pro Dark Silicon & Glassmorphic Hardware',
			'category'          => 'tech',
			'category_label'    => 'Tech & Gaming',
			'description'       => 'High-tech Apple Pro aesthetic featuring deep space-black obsidian tones, frosted neon cyan badges, Space Grotesk typography, and titanium hardware.',
			'accent_color'      => '#00f2fe',
			'secondary_color'   => '#fe0979',
			'bg_color'          => '#080811',
			'font_family_title' => "'Space Grotesk', sans-serif",
			'font_family_body'  => "'Inter', sans-serif",
			'google_fonts_url'  => 'https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Space+Grotesk:wght@700;800&display=swap',
			'poster_image'      => 'https://images.unsplash.com/photo-1629654297299-c8506221ca97?w=1080&h=1920&fit=crop&q=80',
			'publisher'         => 'TechPulse Studio',
			'publisher_logo'    => 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?w=200&h=200&fit=crop&q=80',
			'pages'             => array(
				array(
					'id'          => 'page-1',
					'page_type'   => 'cover',
					'badge'       => 'EXCLUSIVE RADAR // 2026',
					'headline'    => 'Next-Gen Neural Hardware Unleashed',
					'body'        => 'Everything you must know about the quantum leap in portable computing architecture.',
					'stat_number' => '4.2x',
					'stat_label'  => 'Faster Processing Bandwidth',
					'bg_image'    => 'https://images.unsplash.com/photo-1629654297299-c8506221ca97?w=1080&h=1920&fit=crop&q=80',
					'scrim'       => 'linear-gradient(180deg, rgba(8,8,17,0.2) 0%, rgba(8,8,17,0.55) 45%, rgba(8,8,17,0.96) 85%)',
					'cta_text'    => 'Explore Radar',
					'cta_url'     => '#',
					'animation'   => 'fade-in',
				),
				array(
					'id'          => 'page-2',
					'page_type'   => 'feature',
					'badge'       => 'SPEC BREAKDOWN',
					'headline'    => 'Sub-1nm Architecture Changes Everything',
					'body'        => 'Zero thermal throttling under 8K compute loads while maintaining battery longevity all day.',
					'stat_number' => '0.8nm',
					'stat_label'  => 'Node Lithography Standard',
					'bg_image'    => 'https://images.unsplash.com/photo-1550745165-9bc0b252726f?w=1080&h=1920&fit=crop&q=80',
					'scrim'       => 'linear-gradient(180deg, rgba(8,8,17,0.2) 0%, rgba(8,8,17,0.55) 45%, rgba(8,8,17,0.96) 85%)',
					'cta_text'    => 'View Benchmarks',
					'cta_url'     => '#',
					'animation'   => 'fly-in-bottom',
				),
				array(
					'id'          => 'page-3',
					'page_type'   => 'highlight',
					'badge'       => 'BENCHMARK REPORT // 2026',
					'headline'    => 'Crushing All Desktop Rivals in Raw Efficiency',
					'body'        => 'Achieves sustained 120 FPS photorealism under 35 watts of peak system draw.',
					'stat_number' => '120 FPS',
					'stat_label'  => 'Ultra Settings Ray-Tracing',
					'bg_image'    => 'https://images.unsplash.com/photo-1508739773434-c26b3d09e071?w=1080&h=1920&fit=crop&q=80',
					'scrim'       => 'linear-gradient(180deg, rgba(8,8,17,0.2) 0%, rgba(8,8,17,0.55) 45%, rgba(8,8,17,0.96) 85%)',
					'cta_text'    => 'Full Breakdown',
					'cta_url'     => '#',
					'animation'   => 'fade-in',
				),
				array(
					'id'          => 'page-4',
					'page_type'   => 'cta',
					'badge'       => 'THE FINAL VERDICT',
					'headline'    => 'Is The Upgrade Worth It in 2026?',
					'body'        => 'Tap below to read our comprehensive 24-page teardown and benchmark comparison guide.',
					'stat_number' => '9.8 / 10',
					'stat_label'  => 'Editor Rating Score',
					'bg_image'    => 'https://images.unsplash.com/photo-1531297484001-80022131f5a1?w=1080&h=1920&fit=crop&q=80',
					'scrim'       => 'linear-gradient(180deg, rgba(8,8,17,0.2) 0%, rgba(8,8,17,0.55) 45%, rgba(8,8,17,0.96) 85%)',
					'cta_text'    => 'Read Full Review',
					'cta_url'     => '#',
					'animation'   => 'fly-in-bottom',
				),
			),
		);

		// 2. LUXE MINIMAL / HAUTE EDITORIAL
		$this->templates['luxe-minimal-haute'] = array(
			'id'                => 'luxe-minimal-haute',
			'name'              => 'Luxe Editorial',
			'tagline'           => 'High-Fashion Minimalist Serif & Chic Ivory Tones',
			'category'          => 'fashion',
			'category_label'    => 'Fashion & Lifestyle',
			'description'       => 'Editorial elegance crafted for luxury fashion, jewelry, and interior design. Features serif typography, warm champagne tones, and framed lookbook layouts.',
			'accent_color'      => '#d4af37',
			'secondary_color'   => '#e6ded4',
			'bg_color'          => '#141311',
			'font_family_title' => "'Playfair Display', serif",
			'font_family_body'  => "'Plus Jakarta Sans', sans-serif",
			'google_fonts_url'  => 'https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,800;1,400&family=Plus+Jakarta+Sans:wght@400;500;700&display=swap',
			'poster_image'      => 'https://images.unsplash.com/photo-1469334031218-e382a71b716b?w=1080&h=1920&fit=crop&q=80',
			'publisher'         => 'Atelier Vogue',
			'publisher_logo'    => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=200&h=200&fit=crop&q=80',
			'pages'             => array(
				array(
					'id'          => 'page-1',
					'page_type'   => 'cover',
					'badge'       => 'THE ESSENTIALS',
					'headline'    => 'Tailoring Without Compromise',
					'body'        => 'Every piece reflects forty hours of bespoke craftsmanship using ethically sourced raw merino wool.',
					'stat_number' => '100%',
					'stat_label'  => 'ORGANIC ITALIAN CASHMERE',
					'bg_image'    => 'https://images.unsplash.com/photo-1469334031218-e382a71b716b?w=1080&h=1920&fit=crop&q=80',
					'scrim'       => 'linear-gradient(180deg, rgba(20,19,17,0.15) 0%, rgba(20,19,17,0.88) 80%)',
					'cta_text'    => 'Discover The Lookbook',
					'cta_url'     => '#',
					'animation'   => 'fade-in',
				),
				array(
					'id'          => 'page-2',
					'page_type'   => 'editorial',
					'badge'       => 'AUTUMN / WINTER CAPSULE',
					'headline'    => 'The Poetry of Modern Silence',
					'body'        => 'Sculptural silhouettes and unblemished cashmere defining this season’s quiet luxury.',
					'stat_number' => 'COLLECTION 04',
					'stat_label'  => 'Handcrafted in Milan',
					'bg_image'    => 'https://images.unsplash.com/photo-1490481651871-ab68de25d43d?w=1080&h=1920&fit=crop&q=80',
					'scrim'       => 'linear-gradient(180deg, rgba(20,19,17,0.2) 0%, rgba(20,19,17,0.9) 82%)',
					'cta_text'    => 'View Fabric Details',
					'cta_url'     => '#',
					'animation'   => 'fade-in',
				),
				array(
					'id'          => 'page-3',
					'page_type'   => 'gallery',
					'badge'       => 'WARDROBE ARCHIVE',
					'headline'    => 'Understated Palettes for Timeless Living',
					'body'        => 'Neutral ivory, warm taupe, and smoked charcoal marry effortlessly across all daily occasions.',
					'stat_number' => '03 PIECES',
					'stat_label'  => 'Universal Capsule Formula',
					'bg_image'    => 'https://images.unsplash.com/photo-1445205170230-053b83016050?w=1080&h=1920&fit=crop&q=80',
					'scrim'       => 'linear-gradient(180deg, rgba(20,19,17,0.3) 0%, rgba(20,19,17,0.9) 80%)',
					'cta_text'    => 'Style Guide',
					'cta_url'     => '#',
					'animation'   => 'fly-in-bottom',
				),
				array(
					'id'          => 'page-4',
					'page_type'   => 'cta',
					'badge'       => 'PRIVATE TRUNK SHOW',
					'headline'    => 'Reserve Your Bespoke Fitting',
					'body'        => 'Limited edition batch available exclusively for verified atelier members worldwide.',
					'stat_number' => 'LIMITED',
					'stat_label'  => 'Only 50 Numbered Garments',
					'bg_image'    => 'https://images.unsplash.com/photo-1485230895905-ec40ba36b9bc?w=1080&h=1920&fit=crop&q=80',
					'scrim'       => 'linear-gradient(180deg, rgba(20,19,17,0.3) 0%, rgba(20,19,17,0.95) 85%)',
					'cta_text'    => 'Shop The Capsule',
					'cta_url'     => '#',
					'animation'   => 'fade-in',
				),
			),
		);

		// 3. VIRAL PULSE / TOP 5 LISTICLE
		$this->templates['viral-pulse-listicle'] = array(
			'id'                => 'viral-pulse-listicle',
			'name'              => 'Viral Pulse',
			'tagline'           => 'High-Energy Creator Listicle & Countdown Badges',
			'category'          => 'viral',
			'category_label'    => 'Viral & Creators',
			'description'       => 'Built specifically for high audience retention. Punchy numbered badges, vivid gradient accents, and digestible cards engineered for social-first consumption.',
			'accent_color'      => '#ff0055',
			'secondary_color'   => '#7928ca',
			'bg_color'          => '#0a0a0e',
			'font_family_title' => "'Syne', sans-serif",
			'font_family_body'  => "'Inter', sans-serif",
			'google_fonts_url'  => 'https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Syne:wght@700;800&display=swap',
			'poster_image'      => 'https://images.unsplash.com/photo-1511512578047-dfb367046420?w=1080&h=1920&fit=crop&q=80',
			'publisher'         => 'ViralPulse Daily',
			'publisher_logo'    => 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?w=200&h=200&fit=crop&q=80',
			'pages'             => array(
				array(
					'id'          => 'page-1',
					'page_type'   => 'cover',
					'badge'       => 'COUNTDOWN // TOP 5',
					'headline'    => '5 Hidden Habits of Ultra-Producers',
					'body'        => 'Number 4 will completely flip how you think about everyday deep work.',
					'stat_number' => '#05 TO #01',
					'stat_label'  => 'Tested on 500+ Founders',
					'bg_image'    => 'https://images.unsplash.com/photo-1511512578047-dfb367046420?w=1080&h=1920&fit=crop&q=80',
					'scrim'       => 'linear-gradient(180deg, rgba(10,10,14,0.3) 0%, rgba(10,10,14,0.92) 80%)',
					'cta_text'    => 'Start Countdown',
					'cta_url'     => '#',
					'animation'   => 'fade-in',
				),
				array(
					'id'          => 'page-2',
					'page_type'   => 'list_item',
					'badge'       => 'SECRET HABIT #05',
					'headline'    => 'The Zero-Inbox Friction Lock',
					'body'        => 'Never check notifications before completing your primary 90-minute focus boulder.',
					'stat_number' => '01',
					'stat_label'  => 'Priority Boulder First',
					'bg_image'    => 'https://images.unsplash.com/photo-1499750310107-5fef28a66643?w=1080&h=1920&fit=crop&q=80',
					'scrim'       => 'linear-gradient(180deg, rgba(10,10,14,0.35) 0%, rgba(10,10,14,0.94) 82%)',
					'cta_text'    => 'Next Secret',
					'cta_url'     => '#',
					'animation'   => 'fly-in-bottom',
				),
				array(
					'id'          => 'page-3',
					'page_type'   => 'list_item',
					'badge'       => 'SECRET HABIT #01',
					'headline'    => 'The 3-Item Daily Victory Protocol',
					'body'        => 'Limit your daily commitments to just 3 meaningful outcomes. Everything else is secondary noise.',
					'stat_number' => '3X',
					'stat_label'  => 'Output Multiplier',
					'bg_image'    => 'https://images.unsplash.com/photo-1484480974693-6ca0a78fb36b?w=1080&h=1920&fit=crop&q=80',
					'scrim'       => 'linear-gradient(180deg, rgba(10,10,14,0.3) 0%, rgba(10,10,14,0.93) 80%)',
					'cta_text'    => 'Get The Checklist',
					'cta_url'     => '#',
					'animation'   => 'fade-in',
				),
				array(
					'id'          => 'page-4',
					'page_type'   => 'cta',
					'badge'       => 'FREE BLUEPRINT',
					'headline'    => 'Download The Full Productivity Engine',
					'body'        => 'Swipe up right now to get the complete Notion template and weekly habit scorecard.',
					'stat_number' => 'FREE',
					'stat_label'  => 'Instant Notion Download',
					'bg_image'    => 'https://images.unsplash.com/photo-1507679799987-c73779587ccf?w=1080&h=1920&fit=crop&q=80',
					'scrim'       => 'linear-gradient(180deg, rgba(10,10,14,0.35) 0%, rgba(10,10,14,0.96) 82%)',
					'cta_text'    => 'Download Notion Template',
					'cta_url'     => '#',
					'animation'   => 'fly-in-bottom',
				),
			),
		);

		// 4. GOURMET CRUNCH / RECIPE & FOOD
		$this->templates['gourmet-crunch-recipe'] = array(
			'id'                => 'gourmet-crunch-recipe',
			'name'              => 'Gourmet Kitchen',
			'tagline'           => 'Artisanal Culinary Journal & Step-by-Step Cards',
			'category'          => 'food',
			'category_label'    => 'Food & Recipes',
			'description'       => 'Warm and mouthwatering food aesthetic. Includes cooking timer badges, ingredient checklist cards, and numbered preparation steps.',
			'accent_color'      => '#f59e0b',
			'secondary_color'   => '#ef4444',
			'bg_color'          => '#15120e',
			'font_family_title' => "'Cinzel', serif",
			'font_family_body'  => "'Plus Jakarta Sans', sans-serif",
			'google_fonts_url'  => 'https://fonts.googleapis.com/css2?family=Cinzel:wght@700;800&family=Plus+Jakarta+Sans:wght@400;600;700&display=swap',
			'poster_image'      => 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=1080&h=1920&fit=crop&q=80',
			'publisher'         => 'Chef Craft Stories',
			'publisher_logo'    => 'https://images.unsplash.com/photo-1577219491135-ce391730fb2c?w=200&h=200&fit=crop&q=80',
			'pages'             => array(
				array(
					'id'          => 'page-1',
					'page_type'   => 'cover',
					'badge'       => 'ARTISAN RECIPE // 20 MIN',
					'headline'    => 'Crispy Garlic Truffle Gnocchi',
					'body'        => 'Pan-seared potato dumplings bathed in rich garlic herb butter and fresh shaved black truffles.',
					'stat_number' => '15 MIN',
					'stat_label'  => 'Total Cooking Time',
					'bg_image'    => 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=1080&h=1920&fit=crop&q=80',
					'scrim'       => 'linear-gradient(180deg, rgba(21,18,14,0.2) 0%, rgba(21,18,14,0.92) 80%)',
					'cta_text'    => 'View Ingredients',
					'cta_url'     => '#',
					'animation'   => 'fade-in',
				),
				array(
					'id'          => 'page-2',
					'page_type'   => 'ingredients',
					'badge'       => 'KEY INGREDIENTS',
					'headline'    => 'Fresh Pantry Essentials',
					'body'        => '500g Fresh Gnocchi, 4 cloves crushed garlic, fresh thyme, salted French butter, and 20g pecorino.',
					'stat_number' => '5 MAIN',
					'stat_label'  => 'Pure Quality Ingredients',
					'bg_image'    => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=1080&h=1920&fit=crop&q=80',
					'scrim'       => 'linear-gradient(180deg, rgba(21,18,14,0.3) 0%, rgba(21,18,14,0.94) 82%)',
					'cta_text'    => 'See Method',
					'cta_url'     => '#',
					'animation'   => 'fly-in-bottom',
				),
				array(
					'id'          => 'page-3',
					'page_type'   => 'step',
					'badge'       => 'THE GOLDEN SECRET',
					'headline'    => 'The Crisp-Sear Pan Technique',
					'body'        => 'Do not boil first! Sauté straight in foaming hot butter on medium heat until golden crust forms.',
					'stat_number' => 'STEP 02',
					'stat_label'  => 'Sear Without Boiling',
					'bg_image'    => 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=1080&h=1920&fit=crop&q=80',
					'scrim'       => 'linear-gradient(180deg, rgba(21,18,14,0.25) 0%, rgba(21,18,14,0.92) 80%)',
					'cta_text'    => 'Finishing Touches',
					'cta_url'     => '#',
					'animation'   => 'fade-in',
				),
				array(
					'id'          => 'page-4',
					'page_type'   => 'cta',
					'badge'       => 'RECIPE CARD',
					'headline'    => 'Save & Print The Printable Card',
					'body'        => 'Swipe up to print the one-page grocery checklist and chef temperature reference sheet.',
					'stat_number' => 'SAVE',
					'stat_label'  => 'Bookmark To Cookbook',
					'bg_image'    => 'https://images.unsplash.com/photo-1540420773420-3366772f4999?w=1080&h=1920&fit=crop&q=80',
					'scrim'       => 'linear-gradient(180deg, rgba(21,18,14,0.3) 0%, rgba(21,18,14,0.95) 85%)',
					'cta_text'    => 'Print Full Recipe',
					'cta_url'     => '#',
					'animation'   => 'fly-in-bottom',
				),
			),
		);

		// 5. BIOFIT & PEAK / HEALTH & FITNESS
		$this->templates['biofit-peak-routine'] = array(
			'id'                => 'biofit-peak-routine',
			'name'              => 'BioFit Peak',
			'tagline'           => 'Electric Emerald & High-Intensity Workout Layout',
			'category'          => 'fitness',
			'category_label'    => 'Health & Fitness',
			'description'       => 'High-intensity athletic aesthetic featuring vivid neon emerald accents, set and rep trackers, hydration indicators, and trainer CTA blocks.',
			'accent_color'      => '#10b981',
			'secondary_color'   => '#06b6d4',
			'bg_color'          => '#08120c',
			'font_family_title' => "'Space Grotesk', sans-serif",
			'font_family_body'  => "'Plus Jakarta Sans', sans-serif",
			'google_fonts_url'  => 'https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&family=Space+Grotesk:wght@700;800&display=swap',
			'poster_image'      => 'https://images.unsplash.com/photo-1517838277536-f5f99be501cd?w=1080&h=1920&fit=crop&q=80',
			'publisher'         => 'Peak Athletic Club',
			'publisher_logo'    => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=200&h=200&fit=crop&q=80',
			'pages'             => array(
				array(
					'id'          => 'page-1',
					'page_type'   => 'cover',
					'badge'       => 'HIIT PROTOCOL // 12 MIN',
					'headline'    => 'Metabolic Torch Burn Circuit',
					'body'        => 'Zero equipment required. Ignite cardiovascular endurance and elevate post-exercise oxygen consumption.',
					'stat_number' => '450 KCAL',
					'stat_label'  => 'Estimated Active Burn',
					'bg_image'    => 'https://images.unsplash.com/photo-1517838277536-f5f99be501cd?w=1080&h=1920&fit=crop&q=80',
					'scrim'       => 'linear-gradient(180deg, rgba(8,18,12,0.2) 0%, rgba(8,18,12,0.92) 80%)',
					'cta_text'    => 'Start Circuit',
					'cta_url'     => '#',
					'animation'   => 'fade-in',
				),
				array(
					'id'          => 'page-2',
					'page_type'   => 'workout_block',
					'badge'       => 'CIRCUIT BLOCK A',
					'headline'    => 'Explosive Bulgarian Split Jumps',
					'body'        => 'Perform 45 seconds of explosive single-leg power followed by 15 seconds of active recovery.',
					'stat_number' => '4 SETS',
					'stat_label'  => '45s Work / 15s Rest',
					'bg_image'    => 'https://images.unsplash.com/photo-1534438327276-14e5300c3a48?w=1080&h=1920&fit=crop&q=80',
					'scrim'       => 'linear-gradient(180deg, rgba(8,18,12,0.3) 0%, rgba(8,18,12,0.93) 82%)',
					'cta_text'    => 'Next Movement',
					'cta_url'     => '#',
					'animation'   => 'fly-in-bottom',
				),
				array(
					'id'          => 'page-3',
					'page_type'   => 'recovery',
					'badge'       => 'HYDRATION & RECOVERY',
					'headline'    => 'Replenish Electrolyte Balance',
					'body'        => 'Target 500ml cold water with magnesium and sodium citrate within 20 minutes post workout.',
					'stat_number' => '500 ML',
					'stat_label'  => 'Optimal Post Rehydration',
					'bg_image'    => 'https://images.unsplash.com/photo-1549060279-7e168fcee0c2?w=1080&h=1920&fit=crop&q=80',
					'scrim'       => 'linear-gradient(180deg, rgba(8,18,12,0.25) 0%, rgba(8,18,12,0.9) 80%)',
					'cta_text'    => 'Get Routine',
					'cta_url'     => '#',
					'animation'   => 'fade-in',
				),
				array(
					'id'          => 'page-4',
					'page_type'   => 'cta',
					'badge'       => 'FREE 30-DAY PLAN',
					'headline'    => 'Join The 30-Day Peak Challenge',
					'body'        => 'Swipe up to download our complete printable training log and weekly nutrition macros chart.',
					'stat_number' => 'JOIN',
					'stat_label'  => 'Over 12,000 Athletes Active',
					'bg_image'    => 'https://images.unsplash.com/photo-1571019614242-c5c5dee9f50b?w=1080&h=1920&fit=crop&q=80',
					'scrim'       => 'linear-gradient(180deg, rgba(8,18,12,0.3) 0%, rgba(8,18,12,0.95) 85%)',
					'cta_text'    => 'Claim Free Challenge',
					'cta_url'     => '#',
					'animation'   => 'fly-in-bottom',
				),
			),
		);

		// 6. WANDERLUST / TRAVEL EXPEDITION
		$this->templates['wanderlust-expedition'] = array(
			'id'                => 'wanderlust-expedition',
			'name'              => 'Wanderlust Expedition',
			'tagline'           => 'Immersive Editorial Travel & Destination Diary',
			'category'          => 'travel',
			'category_label'    => 'Travel & Adventure',
			'description'       => 'Visual travelogue crafted for destinations and road trips. Includes stamp badges, day-by-day itinerary highlights, and hidden gem tips.',
			'accent_color'      => '#0ea5e9',
			'secondary_color'   => '#f97316',
			'bg_color'          => '#09151f',
			'font_family_title' => "'Cinzel', serif",
			'font_family_body'  => "'Inter', sans-serif",
			'google_fonts_url'  => 'https://fonts.googleapis.com/css2?family=Cinzel:wght@700;800&family=Inter:wght@400;500;700&display=swap',
			'poster_image'      => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=1080&h=1920&fit=crop&q=80',
			'publisher'         => 'Wanderlust Atlas',
			'publisher_logo'    => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=200&h=200&fit=crop&q=80',
			'pages'             => array(
				array(
					'id'          => 'page-1',
					'page_type'   => 'cover',
					'badge'       => 'SECRET GUIDE // 48 HOURS',
					'headline'    => 'Hidden Fjords & Untamed Coastlines',
					'body'        => 'Explore the emerald waters and dramatic cliffside villages untouched by mainstream cruise tours.',
					'stat_number' => '48 HRS',
					'stat_label'  => 'Ultimate Expedition Route',
					'bg_image'    => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=1080&h=1920&fit=crop&q=80',
					'scrim'       => 'linear-gradient(180deg, rgba(9,21,31,0.2) 0%, rgba(9,21,31,0.92) 80%)',
					'cta_text'    => 'Explore Itinerary',
					'cta_url'     => '#',
					'animation'   => 'fade-in',
				),
				array(
					'id'          => 'page-2',
					'page_type'   => 'itinerary',
					'badge'       => 'DAY 01 // DAWN HIKE',
					'headline'    => 'Sunrise Over The Sapphire Ridge',
					'body'        => 'Begin the ascent at 05:00 AM before mist clears for unmatched panoramic vistas of the outer bay.',
					'stat_number' => '1,420M',
					'stat_label'  => 'Peak Ridge Elevation',
					'bg_image'    => 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?w=1080&h=1920&fit=crop&q=80',
					'scrim'       => 'linear-gradient(180deg, rgba(9,21,31,0.3) 0%, rgba(9,21,31,0.94) 82%)',
					'cta_text'    => 'Next Spot',
					'cta_url'     => '#',
					'animation'   => 'fly-in-bottom',
				),
				array(
					'id'          => 'page-3',
					'page_type'   => 'secret_spot',
					'badge'       => 'LOCAL SECRET SPOT',
					'headline'    => 'The Hidden Sea Cave Grotto',
					'body'        => 'Accessible only at low tide via sea kayak. Water shines turquoise under natural daylight openings.',
					'stat_number' => 'KAYAK ONLY',
					'stat_label'  => 'Zero Crowds Guaranteed',
					'bg_image'    => 'https://images.unsplash.com/photo-1506744038136-46273834b3fb?w=1080&h=1920&fit=crop&q=80',
					'scrim'       => 'linear-gradient(180deg, rgba(9,21,31,0.25) 0%, rgba(9,21,31,0.92) 80%)',
					'cta_text'    => 'Coordinates & Map',
					'cta_url'     => '#',
					'animation'   => 'fade-in',
				),
				array(
					'id'          => 'page-4',
					'page_type'   => 'cta',
					'badge'       => 'DOWNLOAD MAPS',
					'headline'    => 'Get Offline GPS Trails & Guidebook',
					'body'        => 'Swipe up for complete offline GPX trail pins, local food recommendations, and boat rentals.',
					'stat_number' => 'MAPS',
					'stat_label'  => 'Full Offline GPX Pack',
					'bg_image'    => 'https://images.unsplash.com/photo-1476514525535-07fb3b4ae5f1?w=1080&h=1920&fit=crop&q=80',
					'scrim'       => 'linear-gradient(180deg, rgba(9,21,31,0.3) 0%, rgba(9,21,31,0.95) 85%)',
					'cta_text'    => 'Get Offline GPS Maps',
					'cta_url'     => '#',
					'animation'   => 'fly-in-bottom',
				),
			),
		);

		// Load massive 500+ templates catalog (5 unique templates for each category)
		$catalog_file = dirname( __FILE__ ) . '/templates-catalog.php';
		if ( file_exists( $catalog_file ) ) {
			require_once $catalog_file;
			if ( class_exists( 'WSTS_Templates_Catalog' ) ) {
				$catalog = WSTS_Templates_Catalog::get_all();
				if ( is_array( $catalog ) ) {
					$this->templates = array_merge( $this->templates, $catalog );
				}
			}
		}
	}

	/**
	 * Get all registered templates
	 *
	 * @return array
	 */
	public function get_all() {
		return apply_filters( 'wsts_registered_templates', $this->templates );
	}

	/**
	 * Get template by ID
	 *
	 * @param string $id Template identifier
	 * @return array|null
	 */
	public function get( $id ) {
		$all = $this->get_all();
		return isset( $all[ $id ] ) ? $all[ $id ] : null;
	}

	/**
	 * Get categories with count
	 *
	 * @return array
	 */
	public function get_categories() {
		$categories = array(
			'all' => array(
				'label' => __( 'All Templates', 'web-stories-templates-studio' ),
				'count' => count( $this->templates ),
			),
		);

		foreach ( $this->templates as $tpl ) {
			$cat_id = $tpl['category'];
			if ( ! isset( $categories[ $cat_id ] ) ) {
				$categories[ $cat_id ] = array(
					'label' => $tpl['category_label'],
					'count' => 0,
				);
			}
			$categories[ $cat_id ]['count']++;
		}

		return $categories;
	}

	/**
	 * Get Stock Images from Unsplash, Pexels, or WordPress Media Library
	 *
	 * High-resolution 1080x1920 (9:16 portrait) photography for Google Web Stories
	 *
	 * @param string $query  Optional search keyword or tag
	 * @param string $source Source filter: 'all', 'unsplash', 'pexels', or 'custom'
	 * @return array List of image objects with urls, source, and tags
	 */
	public static function get_stock_images( $query = '', $source = 'all' ) {
		$source = sanitize_key( $source ?: 'all' );

		// 1. If Custom / Media Library requested, fetch directly from WordPress uploads
		if ( 'custom' === $source || 'media' === $source ) {
			return self::get_media_library_images( $query );
		}

		$clean_query = trim( strtolower( $query ) );
		$live_results = array();

		// 2. Query live Unsplash API if Access Key is configured
		if ( ( 'all' === $source || 'unsplash' === $source ) && ! empty( $clean_query ) && 'all' !== $clean_query ) {
			$un_live = self::fetch_unsplash_live( $clean_query );
			if ( ! empty( $un_live ) ) {
				$live_results = array_merge( $live_results, $un_live );
			}
		}

		// 3. Query live Pexels API if API Key is configured
		if ( ( 'all' === $source || 'pexels' === $source ) && ! empty( $clean_query ) && 'all' !== $clean_query ) {
			$pex_live = self::fetch_pexels_live( $clean_query );
			if ( ! empty( $pex_live ) ) {
				$live_results = array_merge( $live_results, $pex_live );
			}
		}

		if ( ! empty( $live_results ) ) {
			return $live_results;
		}

		// 4. Curated High-Resolution 9:16 Portrait Catalog (Unsplash & Pexels)
		$images = array(
			// ─── UNSPLASH: FASHION & LUXURY ───
			array(
				'id'           => 'un-fashion-1',
				'source'       => 'unsplash',
				'source_label' => 'Unsplash',
				'title'        => 'Italian Cashmere & Sunglasses Editorial',
				'category'     => 'fashion',
				'tags'         => array( 'fashion', 'luxury', 'cashmere', 'sunglasses', 'model', 'editorial', 'essentials', 'style' ),
				'thumb'        => 'https://images.unsplash.com/photo-1469334031218-e382a71b716b?w=360&h=640&fit=crop&q=80',
				'url'          => 'https://images.unsplash.com/photo-1469334031218-e382a71b716b?w=1080&h=1920&fit=crop&q=80',
				'author'       => 'Tamara Bellis',
				'author_url'   => 'https://unsplash.com/@tamarabellis',
			),
			array(
				'id'           => 'un-fashion-2',
				'source'       => 'unsplash',
				'source_label' => 'Unsplash',
				'title'        => 'Minimalist Capsule Wardrobe Lookbook',
				'category'     => 'fashion',
				'tags'         => array( 'fashion', 'minimalist', 'capsule', 'wardrobe', 'hanger', 'linen', 'editorial' ),
				'thumb'        => 'https://images.unsplash.com/photo-1490481651871-ab68de25d43d?w=360&h=640&fit=crop&q=80',
				'url'          => 'https://images.unsplash.com/photo-1490481651871-ab68de25d43d?w=1080&h=1920&fit=crop&q=80',
				'author'       => 'Victoria Sonora',
				'author_url'   => 'https://unsplash.com/@victoriasonora',
			),
			array(
				'id'           => 'un-fashion-3',
				'source'       => 'unsplash',
				'source_label' => 'Unsplash',
				'title'        => 'Vibrant Yellow High-Fashion Streetwear',
				'category'     => 'fashion',
				'tags'         => array( 'fashion', 'streetwear', 'vibrant', 'yellow', 'sunglasses', 'editorial' ),
				'thumb'        => 'https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?w=360&h=640&fit=crop&q=80',
				'url'          => 'https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?w=1080&h=1920&fit=crop&q=80',
				'author'       => 'Dom Hill',
				'author_url'   => 'https://unsplash.com/@domhill',
			),
			array(
				'id'           => 'un-fashion-4',
				'source'       => 'unsplash',
				'source_label' => 'Unsplash',
				'title'        => 'Haute Couture Runway Elegance',
				'category'     => 'fashion',
				'tags'         => array( 'fashion', 'couture', 'luxury', 'dress', 'editorial', 'glamour' ),
				'thumb'        => 'https://images.unsplash.com/photo-1509631179647-0177331693ae?w=360&h=640&fit=crop&q=80',
				'url'          => 'https://images.unsplash.com/photo-1509631179647-0177331693ae?w=1080&h=1920&fit=crop&q=80',
				'author'       => 'Alina Kovac',
				'author_url'   => 'https://unsplash.com/@alinakovac',
			),

			// ─── PEXELS: FASHION & LIFESTYLE ───
			array(
				'id'           => 'pex-fashion-1',
				'source'       => 'pexels',
				'source_label' => 'Pexels',
				'title'        => 'Chic Milan Street Style Sunglasses',
				'category'     => 'fashion',
				'tags'         => array( 'fashion', 'luxury', 'style', 'streetwear', 'sunglasses', 'model' ),
				'thumb'        => 'https://images.pexels.com/photos/7679720/pexels-photo-7679720.jpeg?auto=compress&cs=tinysrgb&fit=crop&w=360&h=640',
				'url'          => 'https://images.pexels.com/photos/7679720/pexels-photo-7679720.jpeg?auto=compress&cs=tinysrgb&fit=crop&w=1080&h=1920',
				'author'       => 'Cottonbro Studio',
				'author_url'   => 'https://www.pexels.com/@cottonbro',
			),
			array(
				'id'           => 'pex-fashion-2',
				'source'       => 'pexels',
				'source_label' => 'Pexels',
				'title'        => 'Modern Tailored Blazer & Accessories',
				'category'     => 'fashion',
				'tags'         => array( 'fashion', 'suit', 'blazer', 'editorial', 'model', 'luxury' ),
				'thumb'        => 'https://images.pexels.com/photos/3756766/pexels-photo-3756766.jpeg?auto=compress&cs=tinysrgb&fit=crop&w=360&h=640',
				'url'          => 'https://images.pexels.com/photos/3756766/pexels-photo-3756766.jpeg?auto=compress&cs=tinysrgb&fit=crop&w=1080&h=1920',
				'author'       => 'Andrea Piacquadio',
				'author_url'   => 'https://www.pexels.com/@andreapiacquadio',
			),

			// ─── UNSPLASH: TECH & COMPUTING ───
			array(
				'id'           => 'un-tech-1',
				'source'       => 'unsplash',
				'source_label' => 'Unsplash',
				'title'        => 'Retro Cyberpunk Neon Workspace',
				'category'     => 'tech',
				'tags'         => array( 'tech', 'cyberpunk', 'neon', 'gaming', 'futuristic', 'cyan', 'vintage' ),
				'thumb'        => 'https://images.unsplash.com/photo-1550745165-9bc0b252726f?w=360&h=640&fit=crop&q=80',
				'url'          => 'https://images.unsplash.com/photo-1550745165-9bc0b252726f?w=1080&h=1920&fit=crop&q=80',
				'author'       => 'Lorenzo Herrera',
				'author_url'   => 'https://unsplash.com/@lorenzoherrera',
			),
			array(
				'id'           => 'un-tech-2',
				'source'       => 'unsplash',
				'source_label' => 'Unsplash',
				'title'        => 'Sub-1nm Microprocessor Motherboard Macro',
				'category'     => 'tech',
				'tags'         => array( 'tech', 'hardware', 'processor', 'chip', 'computer', 'circuit', 'semiconductor' ),
				'thumb'        => 'https://images.unsplash.com/photo-1518770660439-4636190af475?w=360&h=640&fit=crop&q=80',
				'url'          => 'https://images.unsplash.com/photo-1518770660439-4636190af475?w=1080&h=1920&fit=crop&q=80',
				'author'       => 'Alexandre Debiève',
				'author_url'   => 'https://unsplash.com/@alexandre_debieve',
			),
			array(
				'id'           => 'un-tech-3',
				'source'       => 'unsplash',
				'source_label' => 'Unsplash',
				'title'        => 'Next-Gen Workspace & Ultrawide Display',
				'category'     => 'tech',
				'tags'         => array( 'tech', 'workspace', 'monitor', 'laptop', 'developer', 'desk', 'apple' ),
				'thumb'        => 'https://images.unsplash.com/photo-1531297484001-80022131f5a1?w=360&h=640&fit=crop&q=80',
				'url'          => 'https://images.unsplash.com/photo-1531297484001-80022131f5a1?w=1080&h=1920&fit=crop&q=80',
				'author'       => 'Luca Bravo',
				'author_url'   => 'https://unsplash.com/@lucabravo',
			),

			// ─── PEXELS: TECH & AI ───
			array(
				'id'           => 'pex-tech-1',
				'source'       => 'pexels',
				'source_label' => 'Pexels',
				'title'        => 'Robotics & High-Tech Neural Lab',
				'category'     => 'tech',
				'tags'         => array( 'tech', 'ai', 'robotics', 'engineering', 'code', 'future' ),
				'thumb'        => 'https://images.pexels.com/photos/3861969/pexels-photo-3861969.jpeg?auto=compress&cs=tinysrgb&fit=crop&w=360&h=640',
				'url'          => 'https://images.pexels.com/photos/3861969/pexels-photo-3861969.jpeg?auto=compress&cs=tinysrgb&fit=crop&w=1080&h=1920',
				'author'       => 'ThisIsEngineering',
				'author_url'   => 'https://www.pexels.com/@thisisengineering',
			),
			array(
				'id'           => 'pex-tech-2',
				'source'       => 'pexels',
				'source_label' => 'Pexels',
				'title'        => 'Neon Cyber Matrix Code Display',
				'category'     => 'tech',
				'tags'         => array( 'tech', 'code', 'matrix', 'terminal', 'cyber', 'developer' ),
				'thumb'        => 'https://images.pexels.com/photos/546819/pexels-photo-546819.jpeg?auto=compress&cs=tinysrgb&fit=crop&w=360&h=640',
				'url'          => 'https://images.pexels.com/photos/546819/pexels-photo-546819.jpeg?auto=compress&cs=tinysrgb&fit=crop&w=1080&h=1920',
				'author'       => 'Luis Gomes',
				'author_url'   => 'https://www.pexels.com/@luisgomes',
			),

			// ─── UNSPLASH: FOOD & CULINARY ───
			array(
				'id'           => 'un-food-1',
				'source'       => 'unsplash',
				'source_label' => 'Unsplash',
				'title'        => 'Wood-Fired Neapolitan Pizza Crust',
				'category'     => 'food',
				'tags'         => array( 'food', 'pizza', 'italian', 'crust', 'cooking', 'recipe' ),
				'thumb'        => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=360&h=640&fit=crop&q=80',
				'url'          => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=1080&h=1920&fit=crop&q=80',
				'author'       => 'Aurélien Lemasson',
				'author_url'   => 'https://unsplash.com/@aurelienlemasson',
			),
			array(
				'id'           => 'un-food-2',
				'source'       => 'unsplash',
				'source_label' => 'Unsplash',
				'title'        => 'Specialty Coffee Espresso Pour & Latte Art',
				'category'     => 'food',
				'tags'         => array( 'food', 'coffee', 'espresso', 'latte', 'cafe', 'barista' ),
				'thumb'        => 'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?w=360&h=640&fit=crop&q=80',
				'url'          => 'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?w=1080&h=1920&fit=crop&q=80',
				'author'       => 'Nathan Dumlao',
				'author_url'   => 'https://unsplash.com/@nate_dumlao',
			),

			// ─── PEXELS: FOOD & CAFE ───
			array(
				'id'           => 'pex-food-1',
				'source'       => 'pexels',
				'source_label' => 'Pexels',
				'title'        => 'Artisanal Cafe Fresh Roasted Beans & Pour',
				'category'     => 'food',
				'tags'         => array( 'food', 'coffee', 'cafe', 'roast', 'espresso', 'beans' ),
				'thumb'        => 'https://images.pexels.com/photos/302899/pexels-photo-302899.jpeg?auto=compress&cs=tinysrgb&fit=crop&w=360&h=640',
				'url'          => 'https://images.pexels.com/photos/302899/pexels-photo-302899.jpeg?auto=compress&cs=tinysrgb&fit=crop&w=1080&h=1920',
				'author'       => 'Chevanon Photography',
				'author_url'   => 'https://www.pexels.com/@chevanon',
			),
			array(
				'id'           => 'pex-food-2',
				'source'       => 'pexels',
				'source_label' => 'Pexels',
				'title'        => 'Gourmet Organic Brunch Feast',
				'category'     => 'food',
				'tags'         => array( 'food', 'brunch', 'healthy', 'organic', 'dining', 'recipe' ),
				'thumb'        => 'https://images.pexels.com/photos/1640777/pexels-photo-1640777.jpeg?auto=compress&cs=tinysrgb&fit=crop&w=360&h=640',
				'url'          => 'https://images.pexels.com/photos/1640777/pexels-photo-1640777.jpeg?auto=compress&cs=tinysrgb&fit=crop&w=1080&h=1920',
				'author'       => 'Ella Olsson',
				'author_url'   => 'https://www.pexels.com/@ella-olsson',
			),

			// ─── UNSPLASH: FITNESS & WELLNESS ───
			array(
				'id'           => 'un-fit-1',
				'source'       => 'unsplash',
				'source_label' => 'Unsplash',
				'title'        => 'Intense Functional Kettlebell Workout',
				'category'     => 'fitness',
				'tags'         => array( 'fitness', 'workout', 'gym', 'training', 'kettlebell', 'crossfit' ),
				'thumb'        => 'https://images.unsplash.com/photo-1517838277536-f5f99be501cd?w=360&h=640&fit=crop&q=80',
				'url'          => 'https://images.unsplash.com/photo-1517838277536-f5f99be501cd?w=1080&h=1920&fit=crop&q=80',
				'author'       => 'Sven Mieke',
				'author_url'   => 'https://unsplash.com/@theshphotovideo',
			),
			array(
				'id'           => 'un-fit-2',
				'source'       => 'unsplash',
				'source_label' => 'Unsplash',
				'title'        => 'Morning Sunrise Yoga & Mindful Meditation',
				'category'     => 'fitness',
				'tags'         => array( 'fitness', 'yoga', 'meditation', 'wellness', 'mindfulness', 'sunrise' ),
				'thumb'        => 'https://images.unsplash.com/photo-1506126613408-eca07ce68773?w=360&h=640&fit=crop&q=80',
				'url'          => 'https://images.unsplash.com/photo-1506126613408-eca07ce68773?w=1080&h=1920&fit=crop&q=80',
				'author'       => 'Dave Hoefler',
				'author_url'   => 'https://unsplash.com/@davehoefler',
			),

			// ─── PEXELS: FITNESS & ATHLETICS ───
			array(
				'id'           => 'pex-fit-1',
				'source'       => 'pexels',
				'source_label' => 'Pexels',
				'title'        => 'Athletic Strength Training & Dumbbells',
				'category'     => 'fitness',
				'tags'         => array( 'fitness', 'gym', 'workout', 'bodybuilding', 'muscles', 'training' ),
				'thumb'        => 'https://images.pexels.com/photos/4164761/pexels-photo-4164761.jpeg?auto=compress&cs=tinysrgb&fit=crop&w=360&h=640',
				'url'          => 'https://images.pexels.com/photos/4164761/pexels-photo-4164761.jpeg?auto=compress&cs=tinysrgb&fit=crop&w=1080&h=1920',
				'author'       => 'César Saldívar',
				'author_url'   => 'https://www.pexels.com/@cesarsaldivar',
			),

			// ─── UNSPLASH: TRAVEL & EXPEDITION ───
			array(
				'id'           => 'un-travel-1',
				'source'       => 'unsplash',
				'source_label' => 'Unsplash',
				'title'        => 'Desert Highway Camper Van Road Trip',
				'category'     => 'travel',
				'tags'         => array( 'travel', 'roadtrip', 'desert', 'adventure', 'vanlife', 'highway' ),
				'thumb'        => 'https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?w=360&h=640&fit=crop&q=80',
				'url'          => 'https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?w=1080&h=1920&fit=crop&q=80',
				'author'       => 'Dino Reichmuth',
				'author_url'   => 'https://unsplash.com/@dinoreichmuth',
			),
			array(
				'id'           => 'un-travel-2',
				'source'       => 'unsplash',
				'source_label' => 'Unsplash',
				'title'        => 'Crystal Turquoise Tropical Island Beach',
				'category'     => 'travel',
				'tags'         => array( 'travel', 'beach', 'ocean', 'tropical', 'summer', 'island', 'vacation' ),
				'thumb'        => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=360&h=640&fit=crop&q=80',
				'url'          => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=1080&h=1920&fit=crop&q=80',
				'author'       => 'Sean Oulashin',
				'author_url'   => 'https://unsplash.com/@oulashin',
			),

			// ─── PEXELS: TRAVEL & NATURE ───
			array(
				'id'           => 'pex-travel-1',
				'source'       => 'pexels',
				'source_label' => 'Pexels',
				'title'        => 'Dramatic Mountain Range Hike & Sunset',
				'category'     => 'travel',
				'tags'         => array( 'travel', 'mountains', 'hiking', 'sunset', 'nature', 'landscape' ),
				'thumb'        => 'https://images.pexels.com/photos/2440021/pexels-photo-2440021.jpeg?auto=compress&cs=tinysrgb&fit=crop&w=360&h=640',
				'url'          => 'https://images.pexels.com/photos/2440021/pexels-photo-2440021.jpeg?auto=compress&cs=tinysrgb&fit=crop&w=1080&h=1920',
				'author'       => 'Lucas Pezeta',
				'author_url'   => 'https://www.pexels.com/@lucaspezeta',
			),

			// ─── PEXELS: CARS & AUTOMOTIVE ───
			array(
				'id'           => 'pex-car-1',
				'source'       => 'pexels',
				'source_label' => 'Pexels',
				'title'        => 'Luxury Exotic Supercar Neon Motion',
				'category'     => 'business',
				'tags'         => array( 'cars', 'supercar', 'auto', 'speed', 'luxury', 'vehicle' ),
				'thumb'        => 'https://images.pexels.com/photos/3802510/pexels-photo-3802510.jpeg?auto=compress&cs=tinysrgb&fit=crop&w=360&h=640',
				'url'          => 'https://images.pexels.com/photos/3802510/pexels-photo-3802510.jpeg?auto=compress&cs=tinysrgb&fit=crop&w=1080&h=1920',
				'author'       => 'Adrian Dorobantu',
				'author_url'   => 'https://www.pexels.com/@adriandorobantu',
			),

			// ─── UNSPLASH: BUSINESS & ARCHITECTURE ───
			array(
				'id'           => 'un-biz-1',
				'source'       => 'unsplash',
				'source_label' => 'Unsplash',
				'title'        => 'Curved Glass Skyscraper Modern Facade',
				'category'     => 'business',
				'tags'         => array( 'business', 'architecture', 'skyscraper', 'building', 'modern', 'glass' ),
				'thumb'        => 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?w=360&h=640&fit=crop&q=80',
				'url'          => 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?w=1080&h=1920&fit=crop&q=80',
				'author'       => 'Sean Pollock',
				'author_url'   => 'https://unsplash.com/@seanpollock',
			),
			array(
				'id'           => 'un-biz-2',
				'source'       => 'unsplash',
				'source_label' => 'Unsplash',
				'title'        => 'Minimalist Scandinavian Clean Studio Desk',
				'category'     => 'business',
				'tags'         => array( 'business', 'minimalist', 'interior', 'desk', 'workspace', 'homeoffice' ),
				'thumb'        => 'https://images.unsplash.com/photo-1497215728101-856f4ea42174?w=360&h=640&fit=crop&q=80',
				'url'          => 'https://images.unsplash.com/photo-1497215728101-856f4ea42174?w=1080&h=1920&fit=crop&q=80',
				'author'       => 'Bench Accounting',
				'author_url'   => 'https://unsplash.com/@benchaccounting',
			),
		);

		// Filter curated list by source if specified
		if ( 'all' !== $source ) {
			$images = array_filter( $images, function( $img ) use ( $source ) {
				return isset( $img['source'] ) && $img['source'] === $source;
			} );
			$images = array_values( $images );
		}

		if ( empty( $clean_query ) || 'all' === $clean_query ) {
			return $images;
		}

		$stop_words = array( 'in', 'on', 'at', 'of', 'to', 'for', 'with', 'a', 'an', 'the', 'and', 'or', 'is', 'are', 'was', 'were', 'by', 'from', 'it', 'its', 'this', 'that', 'these', 'those', 'how', 'what', 'why', 'when', 'best', 'top', '10', '2026', 'guide' );
		$raw_tokens = preg_split( '/[\s\-_,.]+/', $clean_query );
		$words      = array();
		foreach ( $raw_tokens as $rt ) {
			$w = trim( $rt );
			if ( strlen( $w ) >= 3 && ! in_array( $w, $stop_words, true ) ) {
				$words[] = $w;
			}
		}

		// If query had only short words/stopwords, fallback to raw tokens
		if ( empty( $words ) ) {
			$words = array_filter( array_map( 'trim', $raw_tokens ) );
		}

		$matched = array();

		foreach ( $images as $img ) {
			$score    = 0;
			$haystack = strtolower( $img['title'] . ' ' . $img['category'] . ' ' . implode( ' ', $img['tags'] ) );

			// Exact category match gets high weight
			if ( ! empty( $img['category'] ) && false !== strpos( $clean_query, strtolower( $img['category'] ) ) ) {
				$score += 50;
			}

			foreach ( $words as $word ) {
				// Whole word boundary matching prevents "in" from matching "linen" or "fashion"
				if ( preg_match( '/\b' . preg_quote( $word, '/' ) . '\b/i', $haystack ) ) {
					$score += 20;
				} elseif ( false !== strpos( $haystack, $word ) ) {
					$score += 5;
				}
			}

			if ( $score > 0 ) {
				$img['score'] = $score;
				$matched[]    = $img;
			}
		}

		if ( ! empty( $matched ) ) {
			usort( $matched, function( $a, $b ) {
				return $b['score'] - $a['score'];
			} );
			return $matched;
		}

		// Fallback for custom search query: Dual Unsplash & Pexels high-resolution portrait cards
		$fallbacks = array();
		if ( 'pexels' !== $source ) {
			$fallbacks[] = array(
				'id'           => 'un-custom-' . md5( $clean_query ),
				'source'       => 'unsplash',
				'source_label' => 'Unsplash',
				'title'        => ucfirst( $clean_query ) . ' (High-Res Vertical Unsplash Photo)',
				'category'     => 'custom',
				'tags'         => array( $clean_query ),
				'thumb'        => 'https://images.unsplash.com/photo-1469334031218-e382a71b716b?w=360&h=640&fit=crop&q=80',
				'url'          => 'https://images.unsplash.com/photo-1469334031218-e382a71b716b?w=1080&h=1920&fit=crop&q=80',
				'author'       => 'Unsplash Creator',
				'author_url'   => 'https://unsplash.com',
			);
		}
		if ( 'unsplash' !== $source ) {
			$fallbacks[] = array(
				'id'           => 'pex-custom-' . md5( $clean_query ),
				'source'       => 'pexels',
				'source_label' => 'Pexels',
				'title'        => ucfirst( $clean_query ) . ' (High-Res Vertical Pexels Photo)',
				'category'     => 'custom',
				'tags'         => array( $clean_query ),
				'thumb'        => 'https://images.pexels.com/photos/7679720/pexels-photo-7679720.jpeg?auto=compress&cs=tinysrgb&fit=crop&w=360&h=640',
				'url'          => 'https://images.pexels.com/photos/7679720/pexels-photo-7679720.jpeg?auto=compress&cs=tinysrgb&fit=crop&w=1080&h=1920',
				'author'       => 'Pexels Creator',
				'author_url'   => 'https://www.pexels.com',
			);
		}

		return $fallbacks;
	}

	/**
	 * Fetch WordPress Media Library uploaded images
	 *
	 * @param string $query Optional search keyword
	 * @return array
	 */
	public static function get_media_library_images( $query = '' ) {
		if ( ! function_exists( 'get_posts' ) ) {
			return array();
		}

		$args = array(
			'post_type'      => 'attachment',
			'post_mime_type' => 'image',
			'post_status'    => 'inherit',
			'posts_per_page' => 40,
			'orderby'        => 'date',
			'order'          => 'DESC',
		);

		if ( ! empty( $query ) && 'all' !== $query ) {
			$args['s'] = $query;
		}

		$attachments = get_posts( $args );
		$results     = array();

		foreach ( $attachments as $att ) {
			$thumb = function_exists( 'wp_get_attachment_image_url' ) ? wp_get_attachment_image_url( $att->ID, 'medium' ) : '';
			$full  = function_exists( 'wp_get_attachment_image_url' ) ? wp_get_attachment_image_url( $att->ID, 'full' ) : '';

			if ( ! $full && function_exists( 'wp_get_attachment_url' ) ) {
				$full = wp_get_attachment_url( $att->ID );
			}

			if ( empty( $full ) ) {
				continue;
			}

			$results[] = array(
				'id'           => 'media-' . $att->ID,
				'source'       => 'custom',
				'source_label' => 'Media Library',
				'title'        => get_the_title( $att->ID ) ? get_the_title( $att->ID ) : 'Custom Uploaded Image',
				'category'     => 'custom',
				'tags'         => array( 'upload', 'custom', 'media' ),
				'thumb'        => $thumb ? $thumb : $full,
				'url'          => $full,
				'author'       => 'Your WordPress Uploads',
				'author_url'   => function_exists( 'admin_url' ) ? admin_url( 'upload.php?item=' . $att->ID ) : '',
			);
		}

		return $results;
	}

	/**
	 * Live API Fetcher: Unsplash
	 *
	 * @param string $query
	 * @return array
	 */
	public static function fetch_unsplash_live( $query ) {
		if ( ! function_exists( 'get_option' ) || ! function_exists( 'wp_remote_get' ) ) {
			return array();
		}

		$key = trim( get_option( 'wsts_unsplash_access_key', '' ) );
		if ( empty( $key ) ) {
			return array();
		}

		$transient_key = 'wsts_un_' . md5( $query );
		$cached = function_exists( 'get_transient' ) ? get_transient( $transient_key ) : false;
		if ( false !== $cached && is_array( $cached ) ) {
			return $cached;
		}

		$endpoint = 'https://api.unsplash.com/search/photos?query=' . rawurlencode( $query ) . '&orientation=portrait&per_page=20';
		$res      = wp_remote_get( $endpoint, array(
			'timeout' => 8,
			'headers' => array(
				'Authorization'  => 'Client-ID ' . $key,
				'Accept-Version' => 'v1',
			),
		) );

		if ( is_wp_error( $res ) || 200 !== wp_remote_retrieve_response_code( $res ) ) {
			return array();
		}

		$body = json_decode( wp_remote_retrieve_body( $res ), true );
		if ( empty( $body['results'] ) || ! is_array( $body['results'] ) ) {
			return array();
		}

		$photos = array();
		foreach ( $body['results'] as $item ) {
			$title   = ! empty( $item['description'] ) ? $item['description'] : ( ! empty( $item['alt_description'] ) ? $item['alt_description'] : 'Unsplash Editorial Portrait' );
			$raw_url = ! empty( $item['urls']['raw'] ) ? $item['urls']['raw'] : ( ! empty( $item['urls']['regular'] ) ? $item['urls']['regular'] : '' );
			if ( empty( $raw_url ) ) {
				continue;
			}
			$opt_url    = $raw_url . ( false !== strpos( $raw_url, '?' ) ? '&' : '?' ) . 'w=1080&h=1920&fit=crop&q=80';
			$thumb      = ! empty( $item['urls']['small'] ) ? $item['urls']['small'] : $opt_url;
			$author     = ! empty( $item['user']['name'] ) ? $item['user']['name'] : 'Unsplash Creator';
			$author_url = ! empty( $item['user']['links']['html'] ) ? $item['user']['links']['html'] : 'https://unsplash.com';

			$photos[] = array(
				'id'           => 'un-' . $item['id'],
				'source'       => 'unsplash',
				'source_label' => 'Unsplash',
				'title'        => function_exists( 'wp_strip_all_tags' ) ? wp_strip_all_tags( $title ) : strip_tags( $title ),
				'category'     => 'unsplash',
				'tags'         => array( $query, 'unsplash', 'portrait' ),
				'thumb'        => $thumb,
				'url'          => $opt_url,
				'author'       => $author,
				'author_url'   => $author_url,
			);
		}

		if ( function_exists( 'set_transient' ) && ! empty( $photos ) ) {
			set_transient( $transient_key, $photos, ( defined( 'HOUR_IN_SECONDS' ) ? HOUR_IN_SECONDS : 3600 ) * 6 );
		}

		return $photos;
	}

	/**
	 * Live API Fetcher: Pexels
	 *
	 * @param string $query
	 * @return array
	 */
	public static function fetch_pexels_live( $query ) {
		if ( ! function_exists( 'get_option' ) || ! function_exists( 'wp_remote_get' ) ) {
			return array();
		}

		$key = trim( get_option( 'wsts_pexels_api_key', '' ) );
		if ( empty( $key ) ) {
			return array();
		}

		$transient_key = 'wsts_pex_' . md5( $query );
		$cached = function_exists( 'get_transient' ) ? get_transient( $transient_key ) : false;
		if ( false !== $cached && is_array( $cached ) ) {
			return $cached;
		}

		$endpoint = 'https://api.pexels.com/v1/search?query=' . rawurlencode( $query ) . '&orientation=portrait&per_page=20';
		$res      = wp_remote_get( $endpoint, array(
			'timeout' => 8,
			'headers' => array(
				'Authorization' => $key,
			),
		) );

		if ( is_wp_error( $res ) || 200 !== wp_remote_retrieve_response_code( $res ) ) {
			return array();
		}

		$body = json_decode( wp_remote_retrieve_body( $res ), true );
		if ( empty( $body['photos'] ) || ! is_array( $body['photos'] ) ) {
			return array();
		}

		$photos = array();
		foreach ( $body['photos'] as $item ) {
			$title = ! empty( $item['alt'] ) ? $item['alt'] : 'Pexels High-Resolution Portrait';
			$src   = isset( $item['src'] ) ? $item['src'] : array();
			$url   = ! empty( $src['portrait'] ) ? $src['portrait'] : ( ! empty( $src['large2x'] ) ? $src['large2x'] : '' );
			if ( empty( $url ) ) {
				continue;
			}
			$thumb      = ! empty( $src['medium'] ) ? $src['medium'] : $url;
			$author     = ! empty( $item['photographer'] ) ? $item['photographer'] : 'Pexels Photographer';
			$author_url = ! empty( $item['photographer_url'] ) ? $item['photographer_url'] : 'https://www.pexels.com';

			$photos[] = array(
				'id'           => 'pex-' . $item['id'],
				'source'       => 'pexels',
				'source_label' => 'Pexels',
				'title'        => function_exists( 'wp_strip_all_tags' ) ? wp_strip_all_tags( $title ) : strip_tags( $title ),
				'category'     => 'pexels',
				'tags'         => array( $query, 'pexels', 'portrait' ),
				'thumb'        => $thumb,
				'url'          => $url,
				'author'       => $author,
				'author_url'   => $author_url,
			);
		}

		if ( function_exists( 'set_transient' ) && ! empty( $photos ) ) {
			set_transient( $transient_key, $photos, ( defined( 'HOUR_IN_SECONDS' ) ? HOUR_IN_SECONDS : 3600 ) * 6 );
		}

		return $photos;
	}
}

