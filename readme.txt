=== Web Stories Templates Studio ===
Contributors: pennion
Tags: web stories, google web stories, stories, amp story, amp, templates
Requires at least: 5.8
Tested up to: 6.7
Requires PHP: 7.4
Stable tag: 1.3.6
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Sick, modern, high-converting templates for Google Web Stories on WordPress. 100% compliant with Google Web Stories dimensions, safe zones, and AMP specifications. Over 500+ templates across 100+ categories.

== Description ==

**Web Stories Templates Studio** provides a massive catalog of 500+ "sick", high-retention visual templates engineered specifically for **Google Web Stories for WordPress**, with 5 unique templates for every category across 100+ niches.

Every template is meticulously built according to official Google Web Stories design guidelines, ensuring that your text, headings, badges, and calls-to-action are never obscured by device notches, mobile status bars, or story UI overlays.

### Google Web Stories Specification Compliance Built-In
* **Fullbleed Canvas:** 1080 × 1920 px (Strict 9:16 vertical aspect ratio).
* **Safe Zone Guard:** Visual guides enforcing 220px top padding (clears progress bar, publisher logo, story title, audio toggle) and 250px bottom padding (clears swipe-up outlink CTAs and gesture bars).
* **Poster Cover Image Standards:** Optimized for 3:4 portrait (640 × 853 px minimum, 960 × 1280 px recommended).
* **Publisher Logo:** 1:1 Square monogram icon (512 × 512 px).
* **Typography Density Control:** Real-time character count check to stay strictly under Google's 280-character per slide guideline.

### 500+ Unique Templates Across 100+ Categories
* 5 completely bespoke, hand-crafted templates for every niche: Crypto, Fitness, Luxury Lifestyle, Haute Cuisine, Architecture, Streetwear, AI & Robotics, Coffee, Real Estate, Gaming, Mindfulness, Travel Expeditions, and 90+ more categories!
* Instant live catalog search to quickly locate any category or aesthetic.
* Unified Heading Color & Badge Coordination with 1-click sync actions.
* Full Google Fonts Library (60+ curated fonts in categorized optgroups) + Custom Brand Font upload/URL option.
* Native browser tooltip removal on simulator navigation for zero visual obstruction.
1. **Cyber Tech Radar:** Futuristic dark aesthetic, violet/cyan accents, monospaced stat callouts, tech verdict card.
2. **Luxe Editorial:** High-fashion luxury lifestyle layout with serif editorial typography, warm ivory/champagne tones, and lookbook product cards.
3. **Viral Pulse:** High-energy editorial listicle with punchy numbered badges and high-retention hooks.
4. **Gourmet Kitchen:** Artisanal culinary journal with cooking timers, ingredient checklist card, and step-by-step technique slides.
5. **BioFit Peak:** High-intensity athletic routine with emerald gradients, exercise set/rep counters, and hydration reminders.
6. **Wanderlust Expedition:** Immersive travelogue featuring polaroid frames, passport badges, and 48-hour itinerary highlights.

### Easy To Adjust & Customize
* **Live Interactive Phone Simulator:** Preview how your story flips and renders in a realistic 9:16 mobile mockup.
* **Safe Zone Overlay Switch:** Turn on the visual safe zone overlay at any time to confirm that headlines and text are in safe bounds.
* **1-Click Create & Edit:** Creates a WordPress Web Story draft pre-populated with valid AMP HTML and visual JSON state, redirecting you straight to the Google Web Stories editor.
* **Direct Publish:** Publish stories directly without opening third-party tools.
* **Standalone AMP HTML Export:** Export 100% compliant AMP Story code for any external hosting or validator.
* **Embed Shortcode:** Use `[sick_web_story id="123"]` to embed responsive story cards in any post or page.

== Installation ==

1. Upload the `web-stories-templates` folder to the `/wp-content/plugins/` directory, or upload `web-stories-templates.zip` via WordPress Admin -> Plugins -> Add New -> Upload Plugin.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Navigate to **Story Studio** in your WordPress admin menu.
4. Browse the templates, click **Preview & Customize**, adjust your colors, text, and images, and click **Create & Publish Story**!

== Frequently Asked Questions ==

= Do I need the official Google Web Stories plugin installed? =
No! Web Stories Templates Studio works completely standalone and can generate valid AMP Stories directly. However, if the official **Web Stories** plugin by Google is active, our plugin seamlessly integrates with its visual React editor for even deeper customization!

= Are the templates responsive across all mobile screens? =
Yes! All templates follow Google's full-bleed background and centered safe-zone architecture. They adapt cleanly to standard mobile screens, tall aspect ratios (e.g. 19.5:9, 20:9), and tablets.

= What image sizes should I use when replacing backgrounds? =
We recommend 1080 × 1920 pixels (9:16 aspect ratio) portrait images in WebP or JPEG format, compressed to under 300 KB each for lightning-fast page loading.

== Changelog ==

= 1.3.4 =
* Complete Live Preview & Publishing Engine: Integrated dedicated `wsts_live_preview` AJAX endpoint ensuring Google AMP runtime (`v0.js`) bootstraps cleanly with full HTTP origin and headers without iframe sandbox blocks; wired standalone preview tab and 1-click publishing with capability mapping.
* Pixel-Perfect Design System Polish: Eradicated double-border ghosting on color inputs, eliminated WP core blue focus ring on customizer tabs, and removed stray CSS artifacts.
* Clean Responsive Top Bar & Search: Replaced emoji search icon with crisp SVG icon, added clearance padding to prevent text overlap, and streamlined category selector.
* Inline Publisher Brand & Logo Identity: Re-architected publisher branding controls with 44×44 square preview avatar, clear full-width logo URL input, and dedicated action buttons for site icon detection and media upload.
* Template Card Badge & Favorite Alignment: Fixed slide count badge clipping ("4 Slides"), prevented star button overlap, and repositioned default template badge inside preview canvas.
* Custom WordPress Taxonomy Support: Enabled instant assignment of any existing WordPress category or custom category during story customization.

= 1.2.8 =
* Unified Form Controls & Design System Cohesion: Eradicated default browser color inputs and harsh WordPress core blue focus rings; replaced with silky Apple/Linear soft indigo focus states and custom SVG chevron dropdown selects.
* AMP Desktop Navigation & Top Safe Zone Clearance: Enforced 160px top padding and 85px bottom padding in AMP story pages, guaranteeing zero collision between slide badges/headlines and Google AMP desktop chevron navigation paddles or publisher header bars.
* 1-Click Blog Post Auto-Story Generator: Instant modal allowing creators to convert any published WordPress blog post into a high-retention 4-slide AMP Web Story with matched vertical photography and swipe-up CTA outlink.
* Background Audio Studio: Built-in ambient soundtrack studio with curated royalty-free loops (Lo-Fi Chill, Cinematic Pulse, Acoustic Morning, Electronic Anthem), custom MP3 URL support, and live audio preview test player.
* Google AdSense & GA4 Monetization Tab: Native controls for AMP auto-ads (`ca-pub-XXX` and ad slot) and GA4 measurement ID telemetry.
* Social Story Slices Exporter: 1-click exporter rendering each slide to offscreen 1080×1920 `<canvas>` and downloading high-res PNG images for Instagram Stories, TikTok, and WhatsApp Status.

= 1.2.7 =
* Zoom-In Animation Elimination: Replaced all legacy jarring `zoom-in` animations across the 505 templates with smooth, elegant `fly-in-bottom` motion.
* Smooth Microinteractions: Replaced abrupt button scaling with subtle Apple-grade micro-presses (`scale(0.985)`) and smooth spring curves.
* Favorites & Default Template System: Added star favorites with persistent WordPress user meta and quick default template loader.
* Keyboard Arrow Navigation: Press left (`←`) and right (`→`) arrows on desktop to smoothly switch slides in the studio simulator.

= 1.2.5 =
* Minimal & Classy Design Overhaul: Entire Studio refreshed with Apple/Linear-grade quiet luxury aesthetics and refined whitespace.
* Custom Color Controls: Replaced raw browser color inputs with elegant, rounded swatch beads and uppercase monospace hex text fields.
* Bi-Directional Color Sync: Changing colors via system picker instantly updates the hex code and live story preview; editing hex text updates swatch and story canvas in real-time.
* Minimal Typography: Form labels converted from loud uppercase shouting to clean, minimal sentence-case slate typography.
* Apple Segmented Control Tabs: Modern macOS-inspired capsule track with floating active capsule for intuitive tab switching.
* Minimalist Slide Cards: Refined accordion rows with sleek micro-interactions and subtle elevations.

= 1.2.4 =
* Visual Media Studio: Direct integration with Unsplash and Pexels for instant 1080×1920 (9:16 portrait) stock photos.
* Source Filter Tabs: Filter effortlessly between "All Stock", "Unsplash", "Pexels", and "My Media Library".
* Custom Image Upload: Prominent "Upload Custom Image" action to drag & drop files from your computer or pick from WordPress Media Library directly inside the modal.
* Dual API Key Support: Optional setting to connect personal Unsplash Access Key and Pexels API Key for real-time live API catalog querying.

= 1.2.3 =
* Dedicated unified Typography menu: single-source control for Google font families, fluid headline sizing slider (22-56px), quick size pills, optical letter-spacing, line-height, body size, and stat size.
* Interactive Live Type Specimen Box with real-time phone simulator synchronization.
* Auto-fetch WordPress site icon and site title with 1-click restore.
* Strict vertical bottom alignment ensuring non-obtrusive, high-converting layout.
* Apple Pro titanium obsidian visual styling and fluid notification modal.

= 1.0.0 =
* Initial release with 6 sick templates, interactive phone simulator, live safe-zone overlay, and 1-click Google Web Stories creator.

