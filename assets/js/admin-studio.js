/**
 * Web Stories Templates Studio - Admin JavaScript
 *
 * Implements interactive 9:16 phone simulator, real-time live preview,
 * safe-zone overlay toggles, media picker integration, and in-app modal dialogs.
 */

(function ($) {
	'use strict';

	// State
	let activeStoryData = null;
	let currentSlideIndex = 0;
	let isSafeZoneOn = false;
	let favorites = [];
	let defaultTemplate = '';
	let currentPreviewAmpHtml = '';
	let currentPreviewUrl = '';

	$(document).ready(function () {
		initFavoritesAndDefaults();
		initCategoryFilter();
		initCustomizerModal();
		initLivePreview();
		initSeoTab();
		initStoryLivePreviewModal();
		initSlideNavigation();
		initSafeZoneToggle();
		initMediaUploader();
		initImageFinder();
		initActions();
		initNotificationModal();
		initAutoPostModal();
		initAudioStudio();
		initSocialSlicesExporter();
	});

	/**
	 * Favorites & Default Template System
	 */
	function initFavoritesAndDefaults() {
		// Load from localStorage or server localized data
		try {
			const storedFavs = localStorage.getItem('wsts_favorites');
			if (storedFavs) {
				favorites = JSON.parse(storedFavs);
			} else if (wstsStudio && Array.isArray(wstsStudio.favorites)) {
				favorites = wstsStudio.favorites.slice();
			}
		} catch (e) {
			favorites = (wstsStudio && Array.isArray(wstsStudio.favorites)) ? wstsStudio.favorites.slice() : [];
		}

		try {
			const storedDef = localStorage.getItem('wsts_default_template');
			if (storedDef) {
				defaultTemplate = storedDef;
			} else if (wstsStudio && wstsStudio.defaultTemplate) {
				defaultTemplate = wstsStudio.defaultTemplate;
			}
		} catch (e) {
			defaultTemplate = (wstsStudio && wstsStudio.defaultTemplate) ? wstsStudio.defaultTemplate : '';
		}

		renderFavoritesState();

		// Card Star Favorite click
		$(document).on('click', '.wsts-card-fav-btn', function (e) {
			e.preventDefault();
			e.stopPropagation();

			const $btn = $(this);
			const tplId = $btn.data('id');
			if (!tplId) return;

			const idx = favorites.indexOf(tplId);
			const isAdding = (idx === -1);

			if (isAdding) {
				favorites.push(tplId);
			} else {
				favorites.splice(idx, 1);
			}

			// Save to localStorage
			try {
				localStorage.setItem('wsts_favorites', JSON.stringify(favorites));
			} catch (err) {}

			// Star spring pop animation
			$btn.addClass('wsts-star-pop');
			setTimeout(function () {
				$btn.removeClass('wsts-star-pop');
			}, 400);

			renderFavoritesState();

			// If current category filter is favorites, refresh view
			if ($('.wsts-filter-pill.is-active').data('category') === 'favorites') {
				$('#wstsCategoryQuickSelect').trigger('change');
			}

			// Sync with server asynchronously
			$.ajax({
				url: wstsStudio.ajaxUrl,
				type: 'POST',
				data: {
					action: 'wsts_toggle_favorite',
					nonce: wstsStudio.nonce,
					template_id: tplId
				}
			});
		});

		// Customizer Header: "Set Default Template"
		$('#btnMakeDefaultTemplate').on('click', function (e) {
			e.preventDefault();
			if (!activeStoryData || !activeStoryData.id) return;
			const tplId = activeStoryData.id;

			defaultTemplate = tplId;
			if (favorites.indexOf(tplId) === -1) {
				favorites.push(tplId);
			}

			try {
				localStorage.setItem('wsts_default_template', defaultTemplate);
				localStorage.setItem('wsts_favorites', JSON.stringify(favorites));
			} catch (err) {}

			renderFavoritesState();

			const $btn = $(this);
			$btn.addClass('wsts-star-pop');
			setTimeout(function () {
				$btn.removeClass('wsts-star-pop');
			}, 400);

			showNotification(
				'Default Template Saved',
				'“' + activeStoryData.name + '” is now set as your default template. It is pinned at the top of your Favorites.'
			);

			$.ajax({
				url: wstsStudio.ajaxUrl,
				type: 'POST',
				data: {
					action: 'wsts_set_default_template',
					nonce: wstsStudio.nonce,
					template_id: tplId
				}
			});
		});
	}

	function renderFavoritesState() {
		$('#wstsTemplateGrid .wsts-card').each(function () {
			const $card = $(this);
			const tplId = $card.data('id');
			const isFav = favorites.indexOf(tplId) !== -1;
			const isDef = defaultTemplate === tplId;

			$card.toggleClass('is-favorite', isFav);
			$card.toggleClass('is-default-template', isDef);
			$card.find('.wsts-card-fav-btn').toggleClass('is-active', isFav);
		});

		$('#favPillCount').text(favorites.length);
		$('#favSelectCount').text(favorites.length);

		if (activeStoryData && activeStoryData.id) {
			const isDef = (defaultTemplate === activeStoryData.id);
			$('#btnMakeDefaultTemplate')
				.toggleClass('is-active', isDef)
				.text(isDef ? '⭐ Default (Active)' : '⭐ Set Default');
		}
	}

	/**
	 * Category Filtering & Live Catalog Search across 500+ Templates
	 */
	function initCategoryFilter() {
		let activeCat = 'all';

		function filterCatalog() {
			const query = ($('#wstsTemplateSearchInput').val() || '').trim().toLowerCase();
			const hasQuery = query.length > 0;

			if (hasQuery) {
				$('#btnSearchClear').show();
			} else {
				$('#btnSearchClear').hide();
			}

			let visibleCount = 0;
			$('#wstsTemplateGrid .wsts-card').each(function () {
				const $card = $(this);
				const cardCat = ($card.data('category') || '').toString().toLowerCase();
				const cardId = ($card.data('id') || '').toString();
				const cardName = ($card.find('.wsts-card-title').text() || '').toLowerCase();
				const cardCatLabel = ($card.find('.wsts-tag').text() || '').toLowerCase();
				const cardTagline = ($card.find('.wsts-card-tagline').text() || '').toLowerCase();

				const matchesCat = (activeCat === 'all')
					|| (activeCat === 'favorites' && favorites.indexOf(cardId) !== -1)
					|| (cardCat === activeCat);
				const matchesQuery = !hasQuery || (
					cardName.indexOf(query) !== -1 ||
					cardCatLabel.indexOf(query) !== -1 ||
					cardTagline.indexOf(query) !== -1 ||
					cardCat.indexOf(query) !== -1
				);

				if (matchesCat && matchesQuery) {
					$card.show();
					visibleCount++;
				} else {
					$card.hide();
				}
			});

			if (visibleCount === 0) {
				if (!$('#wstsNoCatalogResults').length) {
					$('#wstsTemplateGrid').after(
						'<div id="wstsNoCatalogResults" class="wsts-no-results-banner" style="text-align: center; padding: 48px 20px; background: #ffffff; border-radius: 18px; border: 1px dashed #cbd5e1; margin: 24px 0;">' +
						'<div id="wstsNoResultsIcon" style="font-size: 36px; margin-bottom: 12px;">🔍</div>' +
						'<h3 id="wstsNoResultsTitle" style="font-size: 18px; font-weight: 700; color: #0f172a; margin-bottom: 6px;">No templates found</h3>' +
						'<p id="wstsNoResultsDesc" style="font-size: 14px; color: #64748b; margin: 0;">Try a different keyword or click "All Categories" to browse all 500+ templates.</p>' +
						'</div>'
					);
				}
				if (activeCat === 'favorites') {
					$('#wstsNoResultsIcon').text('⭐');
					$('#wstsNoResultsTitle').text('No favorite templates yet');
					$('#wstsNoResultsDesc').text('Click the star ★ on any template card to save your favorites or default template for quick 1-click access!');
				} else {
					$('#wstsNoResultsIcon').text('🔍');
					$('#wstsNoResultsTitle').text('No templates found');
					$('#wstsNoResultsDesc').text('Try a different keyword or click "All Categories" to browse all 500+ templates.');
				}
				$('#wstsNoCatalogResults').show();
			} else {
				$('#wstsNoCatalogResults').hide();
			}
		}

		// Category pill click (Smooth 1-Line Scroller)
		$('.wsts-filter-pill').on('click', function () {
			activeCat = ($(this).data('category') || 'all').toString().toLowerCase();
			$('.wsts-filter-pill').removeClass('is-active');
			$(this).addClass('is-active');
			$('#wstsCategoryQuickSelect').val(activeCat);

			// Center clicked pill smoothly in the horizontal scroller
			const bar = document.getElementById('wstsCategoryPillsBar');
			if (bar && this) {
				bar.scrollTo({
					left: this.offsetLeft - (bar.clientWidth / 2) + (this.clientWidth / 2),
					behavior: 'smooth'
				});
			}

			filterCatalog();
		});

		// Category quick jump dropdown
		$('#wstsCategoryQuickSelect').on('change', function () {
			const cat = ($(this).val() || 'all').toString().toLowerCase();
			activeCat = cat;
			$('.wsts-filter-pill').removeClass('is-active');
			const $targetPill = $('.wsts-filter-pill[data-category="' + cat + '"]');
			if ($targetPill.length) {
				$targetPill.addClass('is-active');
				const bar = document.getElementById('wstsCategoryPillsBar');
				const pill = $targetPill[0];
				if (bar && pill) {
					bar.scrollTo({
						left: pill.offsetLeft - (bar.clientWidth / 2) + (pill.clientWidth / 2),
						behavior: 'smooth'
					});
				}
			}
			filterCatalog();
		});

		// Smooth horizontal scroll arrows
		$('#btnScrollCatLeft').on('click', function () {
			const $bar = $('#wstsCategoryPillsBar');
			$bar.animate({ scrollLeft: $bar.scrollLeft() - 320 }, 220);
		});

		$('#btnScrollCatRight').on('click', function () {
			const $bar = $('#wstsCategoryPillsBar');
			$bar.animate({ scrollLeft: $bar.scrollLeft() + 320 }, 220);
		});

		// Live Search input
		$('#wstsTemplateSearchInput').on('input keyup', function () {
			filterCatalog();
		});

		// Clear search button
		$('#btnSearchClear').on('click', function () {
			$('#wstsTemplateSearchInput').val('');
			filterCatalog();
			$('#wstsTemplateSearchInput').focus();
		});
	}

	/**
	 * Modal Initialization & Opening
	 */
	function initCustomizerModal() {
		// Open from Card
		$(document).on('click', '.wsts-card, .wsts-card-preview-wrap, .wsts-open-customizer, .wsts-quick-import-btn', function (e) {
			if ($(e.target).closest('.wsts-card-fav-btn').length) {
				return;
			}
			e.preventDefault();
			const templateId = $(this).data('id') || $(this).closest('.wsts-card').data('id');
			if (templateId) {
				openCustomizer(templateId);
			}
		});

		// Close Modal
		$('#btnModalClose, #wstsCustomizerModal .wsts-modal-backdrop').on('click', function () {
			$('#wstsCustomizerModal').fadeOut(200);
		});

		// Tabs Switching
		$('.wsts-adjust-tab').on('click', function () {
			const targetTab = $(this).data('tab');
			$('.wsts-adjust-tab').removeClass('is-active');
			$(this).addClass('is-active');

			$('.wsts-tab-content').removeClass('is-active');
			$('#' + targetTab).addClass('is-active');

			if (targetTab === 'tab-specs') {
				updateSizingAudit();
			}
		});

		// Accordion Collapse
		$(document).on('click', '.wsts-accordion-head', function (e) {
			if ($(e.target).closest('.wsts-acc-btn').length) {
				return; // Don't collapse when clicking action buttons
			}
			const $item = $(this).closest('.wsts-accordion-item');
			$item.toggleClass('is-open');
			$item.siblings().removeClass('is-open');

			const index = parseInt($item.data('slide-index'), 10);
			if (!isNaN(index)) {
				goToSlide(index);
			}
		});

		// Add New Slide Button
		$('#btnAddSlide').on('click', function (e) {
			e.preventDefault();
			addNewSlide();
		});

		// Slide Accordion Actions: Up, Down, Duplicate, Delete
		$(document).on('click', '.wsts-slide-up', function (e) {
			e.stopPropagation();
			const idx = parseInt($(this).data('index'), 10);
			moveSlide(idx, -1);
		});

		$(document).on('click', '.wsts-slide-down', function (e) {
			e.stopPropagation();
			const idx = parseInt($(this).data('index'), 10);
			moveSlide(idx, 1);
		});

		$(document).on('click', '.wsts-slide-dup', function (e) {
			e.stopPropagation();
			const idx = parseInt($(this).data('index'), 10);
			duplicateSlide(idx);
		});

		$(document).on('click', '.wsts-slide-del', function (e) {
			e.stopPropagation();
			const idx = parseInt($(this).data('index'), 10);
			deleteSlide(idx);
		});

		// Design System Preset Actions
		$('#btnApplyBrandDS').on('click', function (e) {
			e.preventDefault();
			applyBrandDesignSystem();
		});

		$('#btnSaveBrandDS').on('click', function (e) {
			e.preventDefault();
			saveBrandDesignSystem();
		});
	}

	/**
	 * Synchronize Publisher Logo Avatar in UI
	 */
	function syncLogoAvatar(url) {
		const effUrl = url || (wstsStudio && wstsStudio.siteLogo ? wstsStudio.siteLogo : (wstsStudio && wstsStudio.defaultLogo ? wstsStudio.defaultLogo : ''));
		if (effUrl) {
			$('#wstsLogoThumbImg').attr('src', effUrl).show();
			$('#wstsLogoAvatarFallback').hide();
		} else {
			$('#wstsLogoThumbImg').hide();
			$('#wstsLogoAvatarFallback').show();
		}
	}

	/**
	 * Open Customizer with Template Data
	 */
	function openCustomizer(templateId) {
		let rawTemplate = null;
		if (wstsStudio && wstsStudio.templates) {
			if (wstsStudio.templates[templateId]) {
				rawTemplate = wstsStudio.templates[templateId];
			} else if (Array.isArray(wstsStudio.templates)) {
				rawTemplate = wstsStudio.templates.find(function (t) { return t && t.id === templateId; });
			} else {
				for (const k in wstsStudio.templates) {
					if (wstsStudio.templates[k] && wstsStudio.templates[k].id === templateId) {
						rawTemplate = wstsStudio.templates[k];
						break;
					}
				}
			}
		}
		if (!rawTemplate) return;

		// Deep clone template
		openCustomizerWithData(JSON.parse(JSON.stringify(rawTemplate)));
	}

	function openCustomizerWithData(storyData) {
		activeStoryData = storyData;

		// Set Modal Titles
		$('#modalTemplateBadge').text(activeStoryData.category_label || 'TEMPLATE');
		$('#modalTemplateTitle').text(activeStoryData.name || activeStoryData.title);

		// Safe headline fallback
		const firstHeadline = (activeStoryData.pages && activeStoryData.pages[0] && activeStoryData.pages[0].headline)
			? activeStoryData.pages[0].headline
			: (activeStoryData.name || activeStoryData.title);

		// Populate Story Meta Inputs (Auto-resolving Website Logo and Publisher Name)
		const siteLogo = (wstsStudio && wstsStudio.siteLogo) ? wstsStudio.siteLogo : '';
		const siteName = (wstsStudio && wstsStudio.siteName) ? wstsStudio.siteName : 'Web Stories';

		const effPublisher = (activeStoryData.publisher && !['TechPulse Studio', 'Atelier Vogue', 'Web Stories Publisher'].includes(activeStoryData.publisher))
			? activeStoryData.publisher
			: siteName;

		const effLogo = (activeStoryData.publisher_logo && !activeStoryData.publisher_logo.includes('unsplash.com'))
			? activeStoryData.publisher_logo
			: siteLogo;

		activeStoryData.publisher = effPublisher;
		activeStoryData.publisher_logo = effLogo;

		$('#customStoryTitle').val(activeStoryData.title || firstHeadline);
		$('#customStoryCategory').val(activeStoryData.category || activeStoryData.story_category || 'general');
		$('#customPublisher').val(effPublisher);
		$('#customLogoUrl').val(effLogo);
		syncLogoAvatar(effLogo);
		const accColor = activeStoryData.accent_color || '#00f2fe';
		$('#customAccentColor').val(accColor);
		$('#swatchAccentColor').css('background-color', accColor);
		$('#customAccentHex').val(accColor.toUpperCase());

		const secColor = activeStoryData.secondary_color || '#fe0979';
		$('#customSecondaryColor').val(secColor);
		$('#swatchSecondaryColor').css('background-color', secColor);
		$('#customSecondaryHex').val(secColor.toUpperCase());

		const bgColor = activeStoryData.bg_color || '#080811';
		$('#customBgColor').val(bgColor);
		$('#swatchBgColor').css('background-color', bgColor);
		$('#customBgHex').val(bgColor.toUpperCase());
		$('#customPosterImage').val(activeStoryData.poster_image || '');

		// Scrim Gradient Preset in Story & Branding Initialization
		const defaultScrim = 'linear-gradient(180deg, rgba(8,8,17,0.3) 0%, rgba(8,8,17,0.92) 80%)';
		const storyScrim = activeStoryData.global_scrim || (activeStoryData.pages && activeStoryData.pages.length && activeStoryData.pages[0].scrim ? activeStoryData.pages[0].scrim : defaultScrim);
		activeStoryData.global_scrim = storyScrim;
		$('#customGlobalScrim').val(storyScrim);

		// Unified Colors & Badge Coordination Initialization
		const headColor = activeStoryData.headline_color || (activeStoryData.pages && activeStoryData.pages.length && activeStoryData.pages[0].headline_color ? activeStoryData.pages[0].headline_color : '#ffffff');
		activeStoryData.headline_color = headColor;
		$('#unifiedHeadingColor').val(headColor);
		$('#swatchUnifiedHeadingColor').css('background-color', headColor);
		$('#unifiedHeadingColorHex').val(headColor.toUpperCase());

		const badgeBg = activeStoryData.badge_bg || (activeStoryData.pages && activeStoryData.pages.length && activeStoryData.pages[0].badge_bg ? activeStoryData.pages[0].badge_bg : accColor);
		activeStoryData.badge_bg = badgeBg;
		$('#unifiedBadgeBg').val(badgeBg);
		$('#swatchUnifiedBadgeBg').css('background-color', badgeBg);
		$('#unifiedBadgeBgHex').val(badgeBg.toUpperCase());

		const badgeTxt = activeStoryData.badge_color || (activeStoryData.pages && activeStoryData.pages.length && activeStoryData.pages[0].badge_color ? activeStoryData.pages[0].badge_color : '#080811');
		activeStoryData.badge_color = badgeTxt;
		$('#unifiedBadgeColor').val(badgeTxt);
		$('#swatchUnifiedBadgeColor').css('background-color', badgeTxt);
		$('#unifiedBadgeColorHex').val(badgeTxt.toUpperCase());

		// Unified Typography Initialization
		const titleFont = activeStoryData.font_family_title || "'Space Grotesk', sans-serif";
		const bodyFont = activeStoryData.font_family_body || "'Inter', sans-serif";
		$('#customTitleFontUnified, #customTitleFont, #customTitleFontGlobal').val(titleFont);
		$('#customBodyFontUnified, #customBodyFont').val(bodyFont);

		if (activeStoryData.custom_font_url) {
			loadCustomFontStylesheet(activeStoryData.custom_font_url);
		}

		// Resolve unified headline size (from story property or first slide or 32)
		let headSize = activeStoryData.headline_size;
		if (!headSize && activeStoryData.pages && activeStoryData.pages.length && activeStoryData.pages[0].headline_size) {
			headSize = parseInt(activeStoryData.pages[0].headline_size, 10);
		}
		if (!headSize) {
			headSize = 32;
		}
		activeStoryData.headline_size = headSize;
		$('#customHeadlineSizeRange').val(headSize);
		$('#headlineSizeTag').text(headSize + 'px');
		$('.wsts-size-pill').removeClass('is-active').filter('[data-size="' + headSize + '"]').addClass('is-active');

		const tracking = activeStoryData.headline_tracking || '-0.01em';
		const lineHeight = activeStoryData.headline_line_height || '1.18';
		activeStoryData.headline_tracking = tracking;
		activeStoryData.headline_line_height = lineHeight;
		$('#customHeadlineTracking').val(tracking);
		$('#customHeadlineLineHeight').val(lineHeight);

		const bodySize = activeStoryData.body_size || 15;
		activeStoryData.body_size = bodySize;
		$('#customBodySizeRange').val(bodySize);
		$('#bodySizeTag').text(bodySize + 'px');

		const statSize = activeStoryData.stat_size || 40;
		activeStoryData.stat_size = statSize;
		$('#customStatSizeRange').val(statSize);
		$('#statSizeTag').text(statSize + 'px');

		// Monetization & Analytics Fields
		$('#customAdSenseClient').val(activeStoryData.adsense_client || '');
		$('#customAdSenseSlot').val(activeStoryData.adsense_slot || '');
		$('#customGa4Id').val(activeStoryData.ga4_id || '');

		// Audio Studio Fields
		const audioUrl = activeStoryData.background_audio || '';
		$('#customBackgroundAudio').val(audioUrl);
		if (audioUrl) {
			const $matchingOpt = $('#customAudioPreset option[value="' + audioUrl + '"]');
			if ($matchingOpt.length) {
				$('#customAudioPreset').val(audioUrl);
			} else {
				$('#customAudioPreset').val('custom');
			}
		} else {
			$('#customAudioPreset').val('');
		}

		updateTypoSpecimen();

		if (wstsStudio && wstsStudio.brandDesignSystem) {
			$('#brandDsStatusLabel').text('Brand Preset Saved');
		} else {
			$('#brandDsStatusLabel').text('Ready');
		}

		// Initialize SEO & Google Discover fields
		initSeoFieldsForStory();

		// Update Default Template button state
		const isDef = (defaultTemplate === activeStoryData.id);
		$('#btnMakeDefaultTemplate')
			.toggleClass('is-active', isDef)
			.text(isDef ? '⭐ Default (Active)' : '⭐ Set Default');

		// Render Slide Accordion safely
		renderSlideAccordion();

		// Default to Slides tab so slide editor immediately matches simulator
		$('.wsts-adjust-tab[data-tab="tab-slides"]').click();

		// Initialize Simulator
		currentSlideIndex = 0;
		renderSimulatorProgress();
		updateSimulatorDisplay();
		updateSizingAudit();

		$('#wstsCustomizerModal').fadeIn(250);
	}

	/**
	 * Render Slide Accordion Items safely using DOM construction
	 * Exposes complete controls to edit every single thing on every slide
	 */
	function renderSlideAccordion() {
		const $container = $('#wstsSlideAccordion');
		$container.empty();

		if (!activeStoryData.pages || !activeStoryData.pages.length) return;

		activeStoryData.pages.forEach(function (page, idx) {
			const $item = $('<div>', {
				class: 'wsts-accordion-item' + (idx === currentSlideIndex ? ' is-open' : ''),
				'data-slide-index': idx
			});

			const $head = $('<div>', { class: 'wsts-accordion-head' });
			const $titleSpan = $('<span>', { class: 'wsts-acc-title' }).text('Slide ' + (idx + 1) + ' — ' + (page.headline || 'Page'));

			const $actions = $('<div>', { class: 'wsts-acc-actions' });
			const $btnUp = $('<button>', {
				type: 'button',
				class: 'wsts-acc-btn wsts-slide-up',
				'data-index': idx,
				title: 'Move Slide Up'
			}).text('↑').prop('disabled', idx === 0);

			const $btnDown = $('<button>', {
				type: 'button',
				class: 'wsts-acc-btn wsts-slide-down',
				'data-index': idx,
				title: 'Move Slide Down'
			}).text('↓').prop('disabled', idx === activeStoryData.pages.length - 1);

			const $btnDup = $('<button>', {
				type: 'button',
				class: 'wsts-acc-btn wsts-slide-dup',
				'data-index': idx,
				title: 'Duplicate Slide'
			}).text('+Copy');

			const $btnDel = $('<button>', {
				type: 'button',
				class: 'wsts-acc-btn wsts-slide-del',
				'data-index': idx,
				title: 'Delete Slide'
			}).text('✕').prop('disabled', activeStoryData.pages.length <= 1);

			const $arrowSpan = $('<span>', { class: 'wsts-acc-arrow' }).text('▾');
			$actions.append($btnUp, $btnDown, $btnDup, $btnDel, $arrowSpan);

			$head.append($titleSpan, $actions);

			const $body = $('<div>', { class: 'wsts-accordion-body' });

			// Helper to build Apple/Linear minimal color control with swatch + hex text
			function createSlideColorControl(field, slideIdx, colorVal) {
				const val = colorVal || '#ffffff';
				const hexFormatted = (val.indexOf('#') === 0 ? val : '#' + val).toUpperCase();
				const $ctrl = $('<div>', { class: 'wsts-color-control' });
				const $swatch = $('<div>', {
					class: 'wsts-color-swatch-btn',
					style: 'background-color: ' + val + ';'
				});
				const $native = $('<input>', {
					type: 'color',
					class: 'wsts-color-picker wsts-native-color-picker slide-field',
					'data-field': field,
					'data-index': slideIdx,
					val: val
				});
				$swatch.append($native);

				const $hex = $('<input>', {
					type: 'text',
					class: 'wsts-color-hex-input slide-field',
					'data-field': field,
					'data-index': slideIdx,
					val: hexFormatted,
					maxlength: 7,
					spellcheck: 'false',
					style: 'border:none!important;background:transparent!important;box-shadow:none!important;outline:none!important;border-radius:0!important;padding:0 4px!important;'
				});

				$ctrl.append($swatch, $hex);
				return $ctrl;
			}

			// 1. BADGE & TAG SECTION
			const $secBadge = $('<div>', { class: 'wsts-field-section' });
			$secBadge.append($('<div>', { class: 'wsts-field-section-title' }).text('Badge & category tag'));
			const $rowBadge = $('<div>', { class: 'wsts-form-row' });

			const $gBadgeText = $('<div>', { class: 'wsts-form-group', style: 'flex: 2;' });
			$gBadgeText.append($('<label>', { class: 'wsts-label' }).text('Badge text'));
			$gBadgeText.append($('<input>', {
				type: 'text',
				class: 'wsts-input slide-field',
				'data-field': 'badge',
				'data-index': idx,
				placeholder: 'e.g. THE ESSENTIALS',
				val: page.badge || ''
			}));

			const $gBadgeBg = $('<div>', { class: 'wsts-form-group', style: 'flex: 1.2;' });
			$gBadgeBg.append($('<label>', { class: 'wsts-label' }).text('Badge background'));
			$gBadgeBg.append(createSlideColorControl('badge_bg', idx, page.badge_bg || activeStoryData.badge_bg || activeStoryData.accent_color || '#00f2fe'));

			const $gBadgeColor = $('<div>', { class: 'wsts-form-group', style: 'flex: 1.2;' });
			$gBadgeColor.append($('<label>', { class: 'wsts-label' }).text('Badge text'));
			$gBadgeColor.append(createSlideColorControl('badge_color', idx, page.badge_color || activeStoryData.badge_color || '#ffffff'));
			$rowBadge.append($gBadgeText, $gBadgeBg, $gBadgeColor);
			$secBadge.append($rowBadge);

			// 2. HEADLINE & TYPOGRAPHY SECTION
			const $secHead = $('<div>', { class: 'wsts-field-section' });
			$secHead.append($('<div>', { class: 'wsts-field-section-title' }).text('Headline'));

			const $gHead = $('<div>', { class: 'wsts-form-group' });
			$gHead.append($('<label>', { class: 'wsts-label' }).text('Slide headline'));
			$gHead.append($('<input>', {
				type: 'text',
				class: 'wsts-input slide-field',
				'data-field': 'headline',
				'data-index': idx,
				placeholder: 'Enter punchy headline...',
				val: page.headline || ''
			}));
			$secHead.append($gHead);

			const $rowHeadMeta = $('<div>', { class: 'wsts-form-row' });
			const $gHeadColor = $('<div>', { class: 'wsts-form-group', style: 'flex: 1.2;' });
			$gHeadColor.append($('<label>', { class: 'wsts-label' }).text('Headline color'));
			$gHeadColor.append(createSlideColorControl('headline_color', idx, page.headline_color || activeStoryData.headline_color || '#ffffff'));

			const $gAlign = $('<div>', { class: 'wsts-form-group', style: 'flex: 1;' });
			$gAlign.append($('<label>', { class: 'wsts-label' }).text('Text alignment'));
			const $selAlign = $('<select>', {
				class: 'wsts-input slide-field',
				'data-field': 'text_align',
				'data-index': idx
			});
			[
				{ val: 'left', label: 'Left Align' },
				{ val: 'center', label: 'Center Align' },
				{ val: 'right', label: 'Right Align' }
			].forEach(function (al) {
				const $opt = $('<option>', { value: al.val }).text(al.label);
				if ((page.text_align || 'left') === al.val) {
					$opt.prop('selected', true);
				}
				$selAlign.append($opt);
			});
			$gAlign.append($selAlign);
			$rowHeadMeta.append($gHeadColor, $gAlign);
			$secHead.append($rowHeadMeta);

			// 3. BODY TEXT SECTION
			const $secBody = $('<div>', { class: 'wsts-field-section' });
			$secBody.append($('<div>', { class: 'wsts-field-section-title' }).text('Body text & narrative'));

			const $rowBody = $('<div>', { class: 'wsts-form-row' });
			const $gBody = $('<div>', { class: 'wsts-form-group', style: 'flex: 2.8;' });
			$gBody.append($('<label>', { class: 'wsts-label' }).text('Slide body copy (Google max: 280 characters)'));
			$gBody.append($('<textarea>', {
				class: 'wsts-textarea slide-field',
				'data-field': 'body',
				'data-index': idx,
				rows: 3,
				placeholder: 'Write compelling narrative copy...',
				val: page.body || ''
			}));
			const $charHelp = $('<small>', { class: 'wsts-help' });
			const $charCount = $('<span>', { class: 'char-counter', 'data-index': idx }).text('0');
			$charHelp.append($charCount, document.createTextNode(' / 280 characters'));
			$gBody.append($charHelp);

			const $gBodyColor = $('<div>', { class: 'wsts-form-group', style: 'flex: 1.2;' });
			$gBodyColor.append($('<label>', { class: 'wsts-label' }).text('Body text color'));
			$gBodyColor.append(createSlideColorControl('body_color', idx, page.body_color || '#ffffff'));
			$rowBody.append($gBody, $gBodyColor);
			$secBody.append($rowBody);

			// 4. STAT / HIGHLIGHT SECTION
			const $secStat = $('<div>', { class: 'wsts-field-section' });
			$secStat.append($('<div>', { class: 'wsts-field-section-title' }).text('Callout number / metric'));

			const $rowStat = $('<div>', { class: 'wsts-form-row' });
			const $gStatNum = $('<div>', { class: 'wsts-form-group', style: 'flex: 1;' });
			$gStatNum.append($('<label>', { class: 'wsts-label' }).text('Stat / number'));
			$gStatNum.append($('<input>', {
				type: 'text',
				class: 'wsts-input slide-field',
				'data-field': 'stat_number',
				'data-index': idx,
				placeholder: 'e.g. 100%, 01, 4.2x',
				val: page.stat_number || ''
			}));

			const $gStatColor = $('<div>', { class: 'wsts-form-group', style: 'flex: 1.2;' });
			$gStatColor.append($('<label>', { class: 'wsts-label' }).text('Stat color'));
			$gStatColor.append(createSlideColorControl('stat_color', idx, page.stat_color || activeStoryData.accent_color || '#d4af37'));

			const $gStatLbl = $('<div>', { class: 'wsts-form-group', style: 'flex: 1.8;' });
			$gStatLbl.append($('<label>', { class: 'wsts-label' }).text('Stat label'));
			$gStatLbl.append($('<input>', {
				type: 'text',
				class: 'wsts-input slide-field',
				'data-field': 'stat_label',
				'data-index': idx,
				placeholder: 'e.g. ORGANIC ITALIAN CASHMERE',
				val: page.stat_label || ''
			}));
			$rowStat.append($gStatNum, $gStatColor, $gStatLbl);
			$secStat.append($rowStat);

			// 5. BACKGROUND MEDIA & SCRIM
			const $secMedia = $('<div>', { class: 'wsts-field-section' });
			$secMedia.append($('<div>', { class: 'wsts-field-section-title' }).text('Background Image & Scrim Overlay'));

			const $gMedia = $('<div>', { class: 'wsts-form-group' });
			$gMedia.append($('<label>', { class: 'wsts-label' }).text('Background Image (1080x1920 9:16 portrait)'));
			const $grpInput = $('<div>', { class: 'wsts-input-btn-group' });
			$grpInput.append($('<input>', {
				type: 'text',
				class: 'wsts-input slide-field slide-bg-input',
				'data-field': 'bg_image',
				'data-index': idx,
				val: page.bg_image || ''
			}));
			$grpInput.append($('<button>', {
				type: 'button',
				class: 'button wsts-smart-image-btn',
				'data-index': idx,
				title: 'Search & insert curated 9:16 vertical photos'
			}).text('✨ Find Images'));
			$grpInput.append($('<button>', {
				type: 'button',
				class: 'button wsts-slide-media-btn',
				'data-index': idx,
				title: 'Select from WordPress Media Library'
			}).text('Media'));
			$gMedia.append($grpInput);

			const $rowAutoMatch = $('<div>', { class: 'wsts-automatch-bar', style: 'margin-top: 6px; display: flex; gap: 8px;' });
			$rowAutoMatch.append($('<button>', {
				type: 'button',
				class: 'button button-secondary wsts-btn-quick-suggest',
				'data-index': idx
			}).html('⚡ Auto-Match Best Photo to "' + (page.headline ? page.headline.substring(0, 20) + '...' : 'Slide') + '"'));
			$gMedia.append($rowAutoMatch);
			$secMedia.append($gMedia);

			// 6. CALL TO ACTION (OUTLINK)
			const $secCta = $('<div>', { class: 'wsts-field-section' });
			$secCta.append($('<div>', { class: 'wsts-field-section-title' }).text('Call To Action & Swipe Up Button'));

			const $rowCta = $('<div>', { class: 'wsts-form-row' });
			const $gCtaText = $('<div>', { class: 'wsts-form-group' });
			$gCtaText.append($('<label>', { class: 'wsts-label' }).text('Outlink / CTA Button Text'));
			$gCtaText.append($('<input>', {
				type: 'text',
				class: 'wsts-input slide-field',
				'data-field': 'cta_text',
				'data-index': idx,
				placeholder: 'e.g. Swipe Up, Explore Collection',
				val: page.cta_text || ''
			}));

			const $gCtaUrl = $('<div>', { class: 'wsts-form-group' });
			$gCtaUrl.append($('<label>', { class: 'wsts-label' }).text('Outlink URL'));
			$gCtaUrl.append($('<input>', {
				type: 'url',
				class: 'wsts-input slide-field',
				'data-field': 'cta_url',
				'data-index': idx,
				placeholder: 'https://...',
				val: page.cta_url || ''
			}));
			$rowCta.append($gCtaText, $gCtaUrl);
			$secCta.append($rowCta);

			// 7. ANIMATION & TIMING
			const $secAnim = $('<div>', { class: 'wsts-field-section' });
			$secAnim.append($('<div>', { class: 'wsts-field-section-title' }).text('Slide Motion & Animation'));

			const $rowAnim = $('<div>', { class: 'wsts-form-row' });
			const $gAnim = $('<div>', { class: 'wsts-form-group', style: 'flex: 1.5;' });
			$gAnim.append($('<label>', { class: 'wsts-label' }).text('Entry Animation'));
			const $selAnim = $('<select>', {
				class: 'wsts-input slide-field',
				'data-field': 'animation',
				'data-index': idx
			});
			let currentAnim = page.animation || 'fly-in-bottom';
			if (currentAnim === 'zoom-in' || currentAnim === 'twirl-in') {
				currentAnim = 'fly-in-bottom';
				page.animation = 'fly-in-bottom';
			}

			[
				{ val: 'fly-in-bottom', label: 'Fly In From Bottom (Recommended)' },
				{ val: 'fade-in', label: 'Fade In (Smooth & Minimal)' },
				{ val: 'fly-in-left', label: 'Fly In From Left' },
				{ val: 'fly-in-right', label: 'Fly In From Right' },
				{ val: 'drop-in', label: 'Drop In From Top' },
				{ val: 'pulse', label: 'Soft Ambient Fade' }
			].forEach(function (an) {
				const $opt = $('<option>', { value: an.val }).text(an.label);
				if (currentAnim === an.val) {
					$opt.prop('selected', true);
				}
				$selAnim.append($opt);
			});
			$gAnim.append($selAnim);

			const $gAnimDur = $('<div>', { class: 'wsts-form-group', style: 'flex: 1;' });
			$gAnimDur.append($('<label>', { class: 'wsts-label' }).text('Duration'));
			const $selAnimDur = $('<select>', {
				class: 'wsts-input slide-field',
				'data-field': 'anim_duration',
				'data-index': idx
			});
			[
				{ val: 0.4, label: '0.4s Fast' },
				{ val: 0.6, label: '0.6s Normal' },
				{ val: 0.8, label: '0.8s Cinematic' },
				{ val: 1.2, label: '1.2s Dramatic' }
			].forEach(function (d) {
				const $opt = $('<option>', { value: d.val }).text(d.label);
				if (parseFloat(page.anim_duration || 0.8) === d.val) {
					$opt.prop('selected', true);
				}
				$selAnimDur.append($opt);
			});
			$gAnimDur.append($selAnimDur);

			const $gDur = $('<div>', { class: 'wsts-form-group', style: 'flex: 1;' });
			$gDur.append($('<label>', { class: 'wsts-label' }).text('Auto-Advance'));
			const $selDur = $('<select>', {
				class: 'wsts-input slide-field',
				'data-field': 'duration',
				'data-index': idx
			});
			[
				{ val: 5, label: '5s' },
				{ val: 7, label: '7s' },
				{ val: 8, label: '8s' },
				{ val: 10, label: '10s' },
				{ val: 12, label: '12s' }
			].forEach(function (du) {
				const $opt = $('<option>', { value: du.val }).text(du.label);
				if (parseInt(page.duration, 10) === du.val || (!page.duration && du.val === 8)) {
					$opt.prop('selected', true);
				}
				$selDur.append($opt);
			});
			$gDur.append($selDur);
			$rowAnim.append($gAnim, $gAnimDur, $gDur);

			const $rowPreviewAnim = $('<div>', { style: 'margin-top: 8px; display: flex; gap: 8px;' });
			$rowPreviewAnim.append($('<button>', {
				type: 'button',
				class: 'button button-secondary wsts-btn-preview-anim',
				'data-index': idx
			}).html('▶ <strong>Replay Live Animation</strong>'));
			$secAnim.append($rowAnim, $rowPreviewAnim);

			$body.append($secBadge, $secHead, $secBody, $secStat, $secMedia, $secCta, $secAnim);
			$item.append($head, $body);
			$container.append($item);
		});

		updateCharCounters();
		$('#slideCountBadge').text(activeStoryData.pages.length + (activeStoryData.pages.length === 1 ? ' Slide' : ' Slides'));
	}

	/**
	 * Real-time Preview Bindings
	 */
	function initLivePreview() {
		// Story meta fields
		$('#customStoryTitle').on('input', function () {
			if (activeStoryData) {
				activeStoryData.title = $(this).val();
			}
			updateSizingAudit();
		});

		$('#customPublisher').on('input', function () {
			if (activeStoryData) {
				activeStoryData.publisher = $(this).val();
			}
			$('#simPublisherName').text(activeStoryData ? activeStoryData.publisher : 'Publisher');
		});

		$('#btnToggleCustomCat').on('click', function (e) {
			e.preventDefault();
			$('#customStoryCategoryCustom').slideToggle(150, function () {
				if ($(this).is(':visible')) {
					$(this).focus();
				}
			});
		});

		$('#customStoryCategoryCustom').on('input change', function () {
			const val = $(this).val().trim();
			if (activeStoryData) {
				activeStoryData.story_category = val || $('#customStoryCategory').val();
				activeStoryData.category = activeStoryData.story_category;
			}
		});

		$('#customStoryCategory').on('change', function () {
			if (!$('#customStoryCategoryCustom').val().trim() && activeStoryData) {
				activeStoryData.story_category = $(this).val();
				activeStoryData.category = $(this).val();
			}
		});

		$('#wstsLogoThumbImg').on('error', function () {
			$(this).hide();
			$('#wstsLogoAvatarFallback').show();
		});

		$('#simPublisherLogo').on('error', function () {
			const fallbackLogo = (wstsStudio && wstsStudio.defaultLogo) ? wstsStudio.defaultLogo : '';
			if (fallbackLogo && $(this).attr('src') !== fallbackLogo) {
				$(this).attr('src', fallbackLogo);
			} else {
				$(this).hide();
			}
		});

		$('#customLogoUrl').on('input change', function () {
			const val = $(this).val();
			syncLogoAvatar(val);
			if (activeStoryData) {
				activeStoryData.publisher_logo = val;
				if (activeStoryData.publisher_logo) {
					$('#simPublisherLogo').attr('src', activeStoryData.publisher_logo).show();
				} else {
					$('#simPublisherLogo').hide();
				}
			}
		});

		$('#customAccentColor').on('input change', function () {
			const val = $(this).val();
			$('#swatchAccentColor').css('background-color', val);
			$('#customAccentHex').val(val.toUpperCase());
			if (activeStoryData) {
				activeStoryData.accent_color = val;
			}
			applyColorStyles();
		});

		$('#customAccentHex').on('input change', function () {
			let val = $(this).val().trim();
			if (val && !val.startsWith('#')) val = '#' + val;
			if (/^#[0-9A-Fa-f]{6}$/.test(val)) {
				$('#customAccentColor').val(val);
				$('#swatchAccentColor').css('background-color', val);
				if (activeStoryData) {
					activeStoryData.accent_color = val;
				}
				applyColorStyles();
			}
		});

		$('#customSecondaryColor').on('input change', function () {
			const val = $(this).val();
			$('#swatchSecondaryColor').css('background-color', val);
			$('#customSecondaryHex').val(val.toUpperCase());
			if (activeStoryData) {
				activeStoryData.secondary_color = val;
			}
			applyColorStyles();
		});

		$('#customSecondaryHex').on('input change', function () {
			let val = $(this).val().trim();
			if (val && !val.startsWith('#')) val = '#' + val;
			if (/^#[0-9A-Fa-f]{6}$/.test(val)) {
				$('#customSecondaryColor').val(val);
				$('#swatchSecondaryColor').css('background-color', val);
				if (activeStoryData) {
					activeStoryData.secondary_color = val;
				}
				applyColorStyles();
			}
		});

		$('#customBgColor').on('input change', function () {
			const val = $(this).val();
			$('#swatchBgColor').css('background-color', val);
			$('#customBgHex').val(val.toUpperCase());
			if (activeStoryData) {
				activeStoryData.bg_color = val;
			}
			$('#wstsPhoneScreen').css('background-color', activeStoryData.bg_color);
		});

		$('#customBgHex').on('input change', function () {
			let val = $(this).val().trim();
			if (val && !val.startsWith('#')) val = '#' + val;
			if (/^#[0-9A-Fa-f]{6}$/.test(val)) {
				$('#customBgColor').val(val);
				$('#swatchBgColor').css('background-color', val);
				if (activeStoryData) {
					activeStoryData.bg_color = val;
				}
				$('#wstsPhoneScreen').css('background-color', activeStoryData.bg_color);
			}
		});

		$('#customPosterImage').on('input', function () {
			if (activeStoryData) {
				activeStoryData.poster_image = $(this).val();
			}
		});

		// Scrim Gradient Preset in Story & Branding (Universal Control)
		$('#customGlobalScrim').on('change', function () {
			const scrimVal = $(this).val();
			if (activeStoryData) {
				activeStoryData.global_scrim = scrimVal;
				if (activeStoryData.pages) {
					activeStoryData.pages.forEach(function (p) {
						p.scrim = scrimVal;
					});
				}
			}
			updateSimulatorDisplay();
		});

		$(document).on('click', '#btnApplyGlobalScrimToAllSlides', function (e) {
			e.preventDefault();
			const scrimVal = $('#customGlobalScrim').val() || 'linear-gradient(180deg, rgba(8,8,17,0.3) 0%, rgba(8,8,17,0.92) 80%)';
			if (activeStoryData) {
				activeStoryData.global_scrim = scrimVal;
				if (activeStoryData.pages) {
					activeStoryData.pages.forEach(function (p) {
						p.scrim = scrimVal;
					});
				}
			}
			updateSimulatorDisplay();
			showNotification(
				'Scrim Applied to All Slides',
				'All slides now uniformly inherit your selected background contrast scrim gradient.'
			);
		});

		// ─── UNIFIED COLOR & BADGE COORDINATION LISTENERS ──────
		function updateUnifiedHeadingColor(hex) {
			activeStoryData.headline_color = hex;
			if (activeStoryData.pages) {
				activeStoryData.pages.forEach(function (p) {
					p.headline_color = hex;
				});
			}
			$('.slide-field[data-field="headline_color"]').each(function () {
				const $ctrl = $(this).closest('.wsts-color-control');
				if ($ctrl.length) {
					$ctrl.find('.wsts-native-color-picker').val(hex);
					$ctrl.find('.wsts-color-swatch-btn').css('background-color', hex);
					$ctrl.find('.wsts-color-hex-input').val(hex.toUpperCase());
				} else {
					$(this).val(hex);
				}
			});
			updateSimulatorDisplay();
			updateTypoSpecimen();
		}

		$('#unifiedHeadingColor').on('input change', function () {
			const val = $(this).val();
			$('#swatchUnifiedHeadingColor').css('background-color', val);
			$('#unifiedHeadingColorHex').val(val.toUpperCase());
			updateUnifiedHeadingColor(val);
		});

		$('#unifiedHeadingColorHex').on('input change', function () {
			let val = $(this).val().trim();
			if (val && !val.startsWith('#')) val = '#' + val;
			if (/^#[0-9A-Fa-f]{6}$/.test(val)) {
				$('#unifiedHeadingColor').val(val);
				$('#swatchUnifiedHeadingColor').css('background-color', val);
				updateUnifiedHeadingColor(val);
			}
		});

		function updateUnifiedBadgeBg(hex) {
			activeStoryData.badge_bg = hex;
			if (activeStoryData.pages) {
				activeStoryData.pages.forEach(function (p) {
					p.badge_bg = hex;
				});
			}
			$('.slide-field[data-field="badge_bg"]').each(function () {
				const $ctrl = $(this).closest('.wsts-color-control');
				if ($ctrl.length) {
					$ctrl.find('.wsts-native-color-picker').val(hex);
					$ctrl.find('.wsts-color-swatch-btn').css('background-color', hex);
					$ctrl.find('.wsts-color-hex-input').val(hex.toUpperCase());
				} else {
					$(this).val(hex);
				}
			});
			updateSimulatorDisplay();
		}

		$('#unifiedBadgeBg').on('input change', function () {
			const val = $(this).val();
			$('#swatchUnifiedBadgeBg').css('background-color', val);
			$('#unifiedBadgeBgHex').val(val.toUpperCase());
			updateUnifiedBadgeBg(val);
		});

		$('#unifiedBadgeBgHex').on('input change', function () {
			let val = $(this).val().trim();
			if (val && !val.startsWith('#')) val = '#' + val;
			if (/^#[0-9A-Fa-f]{6}$/.test(val)) {
				$('#unifiedBadgeBg').val(val);
				$('#swatchUnifiedBadgeBg').css('background-color', val);
				updateUnifiedBadgeBg(val);
			}
		});

		function updateUnifiedBadgeColor(hex) {
			activeStoryData.badge_color = hex;
			if (activeStoryData.pages) {
				activeStoryData.pages.forEach(function (p) {
					p.badge_color = hex;
				});
			}
			$('.slide-field[data-field="badge_color"]').each(function () {
				const $ctrl = $(this).closest('.wsts-color-control');
				if ($ctrl.length) {
					$ctrl.find('.wsts-native-color-picker').val(hex);
					$ctrl.find('.wsts-color-swatch-btn').css('background-color', hex);
					$ctrl.find('.wsts-color-hex-input').val(hex.toUpperCase());
				} else {
					$(this).val(hex);
				}
			});
			updateSimulatorDisplay();
		}

		$('#unifiedBadgeColor').on('input change', function () {
			const val = $(this).val();
			$('#swatchUnifiedBadgeColor').css('background-color', val);
			$('#unifiedBadgeColorHex').val(val.toUpperCase());
			updateUnifiedBadgeColor(val);
		});

		$('#unifiedBadgeColorHex').on('input change', function () {
			let val = $(this).val().trim();
			if (val && !val.startsWith('#')) val = '#' + val;
			if (/^#[0-9A-Fa-f]{6}$/.test(val)) {
				$('#unifiedBadgeColor').val(val);
				$('#swatchUnifiedBadgeColor').css('background-color', val);
				updateUnifiedBadgeColor(val);
			}
		});

		// 🎨 Sync Heading Color with Badge
		$(document).on('click', '#btnSyncHeadingWithBadge', function (e) {
			e.preventDefault();
			const badgeBgHex = $('#unifiedBadgeBgHex').val() || activeStoryData.badge_bg || '#00f2fe';
			$('#unifiedHeadingColor').val(badgeBgHex);
			$('#swatchUnifiedHeadingColor').css('background-color', badgeBgHex);
			$('#unifiedHeadingColorHex').val(badgeBgHex.toUpperCase());
			updateUnifiedHeadingColor(badgeBgHex);
			showNotification('Heading Synced with Badge', 'Slide headlines now match your badge background color.');
		});

		// ⚡ Sync Badge with Brand Accent
		$(document).on('click', '#btnSyncBadgeWithAccent', function (e) {
			e.preventDefault();
			const accHex = $('#customAccentHex').val() || activeStoryData.accent_color || '#00f2fe';
			$('#unifiedBadgeBg').val(accHex);
			$('#swatchUnifiedBadgeBg').css('background-color', accHex);
			$('#unifiedBadgeBgHex').val(accHex.toUpperCase());
			updateUnifiedBadgeBg(accHex);
			showNotification('Badge Synced with Accent', 'Badges now inherit your brand accent color.');
		});

		// ✨ Apply Colors to All Slides
		$(document).on('click', '#btnApplyColorsToAllSlides', function (e) {
			e.preventDefault();
			const hColor = $('#unifiedHeadingColorHex').val() || '#ffffff';
			const bBg = $('#unifiedBadgeBgHex').val() || activeStoryData.accent_color || '#00f2fe';
			const bTxt = $('#unifiedBadgeColorHex').val() || '#080811';
			updateUnifiedHeadingColor(hColor);
			updateUnifiedBadgeBg(bBg);
			updateUnifiedBadgeColor(bTxt);
			renderSlideAccordion();
			showNotification('Applied to All Slides', 'All slides now strictly share your unified heading & badge colors.');
		});

		// ─── UNIFIED TYPOGRAPHY MENU LISTENERS ───────────────────
		// Unified Heading Font Change (Google Font or Custom)
		$(document).on('change', '#customTitleFontUnified, #customTitleFontGlobal, #customTitleFont', function () {
			const selectedFont = $(this).val();
			if (selectedFont === '__custom__') {
				$('#titleCustomFontDrawer').slideDown(200);
				return;
			}
			$('#titleCustomFontDrawer').slideUp(200);
			$('#customTitleFontUnified, #customTitleFontGlobal, #customTitleFont').val(selectedFont);
			if (activeStoryData) {
				activeStoryData.font_family_title = selectedFont;
			}
			loadGoogleFontDynamically(selectedFont);
			updateTypoSpecimen();
		});

		// Apply Custom Heading Font
		$(document).on('click', '#btnApplyCustomTitleFont', function (e) {
			e.preventDefault();
			const fontName = $('#customHeadingFontName').val().trim();
			if (!fontName) {
				showNotification('Missing Font Name', 'Please enter your custom font family name.');
				return;
			}
			const fontUrl = $('#customHeadingFontUrl').val().trim();
			const fallback = $('#customHeadingFontFallback').val() || 'sans-serif';
			const fontVal = "'" + fontName + "', " + fallback;

			if (fontUrl) {
				loadCustomFontStylesheet(fontUrl);
				if (activeStoryData) {
					activeStoryData.custom_font_url = fontUrl;
				}
			}
			if (activeStoryData) {
				activeStoryData.font_family_title = fontVal;
			}

			let $opt = $('#customTitleFontUnified').find('option[data-custom="true"]');
			if (!$opt.length) {
				$opt = $('<option>', { value: fontVal, 'data-custom': 'true' });
				$('#customTitleFontUnified').prepend($opt);
			}
			$opt.val(fontVal).text('✨ Custom: ' + fontName).prop('selected', true);
			$('#titleCustomFontDrawer').slideUp(200);
			updateSimulatorDisplay();
			updateTypoSpecimen();
			showNotification('Custom Font Applied', 'Your custom font "' + fontName + '" has been applied to story headings.');
		});

		// Unified Body Font Change (Google Font or Custom)
		$(document).on('change', '#customBodyFontUnified, #customBodyFont', function () {
			const selectedFont = $(this).val();
			if (selectedFont === '__custom__') {
				$('#bodyCustomFontDrawer').slideDown(200);
				return;
			}
			$('#bodyCustomFontDrawer').slideUp(200);
			$('#customBodyFontUnified, #customBodyFont').val(selectedFont);
			if (activeStoryData) {
				activeStoryData.font_family_body = selectedFont;
			}
			loadGoogleFontDynamically(selectedFont);
			updateTypoSpecimen();
		});

		// Apply Custom Body Font
		$(document).on('click', '#btnApplyCustomBodyFont', function (e) {
			e.preventDefault();
			const fontName = $('#customBodyFontName').val().trim();
			if (!fontName) {
				showNotification('Missing Font Name', 'Please enter your custom font family name.');
				return;
			}
			const fontUrl = $('#customBodyFontUrl').val().trim();
			const fallback = $('#customBodyFontFallback').val() || 'sans-serif';
			const fontVal = "'" + fontName + "', " + fallback;

			if (fontUrl) {
				loadCustomFontStylesheet(fontUrl);
			}
			if (activeStoryData) {
				activeStoryData.font_family_body = fontVal;
			}

			let $opt = $('#customBodyFontUnified').find('option[data-custom="true"]');
			if (!$opt.length) {
				$opt = $('<option>', { value: fontVal, 'data-custom': 'true' });
				$('#customBodyFontUnified').prepend($opt);
			}
			$opt.val(fontVal).text('✨ Custom: ' + fontName).prop('selected', true);
			$('#bodyCustomFontDrawer').slideUp(200);
			updateSimulatorDisplay();
			updateTypoSpecimen();
			showNotification('Custom Font Applied', 'Your custom font "' + fontName + '" has been applied to body typography.');
		});

		// Unified Headline Size Range Slider (Unified across story)
		$(document).on('input change', '#customHeadlineSizeRange', function () {
			const size = parseInt($(this).val(), 10);
			$('#headlineSizeTag').text(size + 'px');
			$('.wsts-size-pill').removeClass('is-active').filter('[data-size="' + size + '"]').addClass('is-active');
			if (activeStoryData) {
				activeStoryData.headline_size = size;
				if (activeStoryData.pages) {
					activeStoryData.pages.forEach(function (p) {
						p.headline_size = size;
					});
				}
			}
			updateTypoSpecimen();
			updateSizingAudit();
		});

		// Headline Size Quick Pills (28px, 32px, 38px, 44px, 50px)
		$(document).on('click', '.wsts-size-pill', function (e) {
			e.preventDefault();
			const size = parseInt($(this).data('size'), 10);
			$('.wsts-size-pill').removeClass('is-active');
			$(this).addClass('is-active');
			$('#customHeadlineSizeRange').val(size).trigger('input');
		});

		// Headline Tracking (Letter-Spacing)
		$(document).on('change', '#customHeadlineTracking', function () {
			const tracking = $(this).val();
			if (activeStoryData) {
				activeStoryData.headline_tracking = tracking;
			}
			updateTypoSpecimen();
		});

		// Headline Line-Height
		$(document).on('change', '#customHeadlineLineHeight', function () {
			const lh = $(this).val();
			if (activeStoryData) {
				activeStoryData.headline_line_height = lh;
			}
			updateTypoSpecimen();
		});

		// Body Font Size Slider (12-20px)
		$(document).on('input change', '#customBodySizeRange', function () {
			const size = parseInt($(this).val(), 10);
			$('#bodySizeTag').text(size + 'px');
			if (activeStoryData) {
				activeStoryData.body_size = size;
			}
			updateTypoSpecimen();
		});

		// Stat Number Size Slider (28-56px)
		$(document).on('input change', '#customStatSizeRange', function () {
			const size = parseInt($(this).val(), 10);
			$('#statSizeTag').text(size + 'px');
			if (activeStoryData) {
				activeStoryData.stat_size = size;
			}
			updateTypoSpecimen();
		});

		// 1-Click Restore WordPress Site Icon as Logo
		$(document).on('click', '#btnFetchSiteLogo', function (e) {
			e.preventDefault();
			const siteLogo = (wstsStudio && wstsStudio.siteLogo) ? wstsStudio.siteLogo : '';
			if (siteLogo) {
				$('#customLogoUrl').val(siteLogo);
				syncLogoAvatar(siteLogo);
				if (activeStoryData) {
					activeStoryData.publisher_logo = siteLogo;
				}
				$('#simPublisherLogo').attr('src', siteLogo).show();
				showNotification(
					'Website Logo Restored',
					'Publisher logo has been reset to your WordPress Site Icon.'
				);
			}
		});

		// Two-way sync for Apple/Linear color controls
		$(document).on('input change', '.wsts-native-color-picker', function () {
			const val = $(this).val();
			const $ctrl = $(this).closest('.wsts-color-control');
			$ctrl.find('.wsts-color-swatch-btn').css('background-color', val);
			$ctrl.find('.wsts-color-hex-input').val(val.toUpperCase());
		});

		$(document).on('input change', '.wsts-color-hex-input', function () {
			let val = $(this).val().trim();
			if (val && !val.startsWith('#')) {
				val = '#' + val;
			}
			if (/^#[0-9A-Fa-f]{6}$/.test(val)) {
				const $ctrl = $(this).closest('.wsts-color-control');
				$ctrl.find('.wsts-color-swatch-btn').css('background-color', val);
				$ctrl.find('.wsts-native-color-picker').val(val);
			}
		});

		// Slide fields live updating
		$(document).on('input change', '.slide-field', function () {
			const field = $(this).data('field');
			const idx = parseInt($(this).data('index'), 10);
			let val = $(this).val();

			if ($(this).hasClass('wsts-color-hex-input')) {
				let hex = (val || '').trim();
				if (hex && !hex.startsWith('#')) hex = '#' + hex;
				if (!/^#[0-9A-Fa-f]{6}$/.test(hex)) return;
				val = hex;
			}

			if (!activeStoryData || !activeStoryData.pages || !activeStoryData.pages[idx]) return;
			activeStoryData.pages[idx][field] = val;

			if (field === 'headline') {
				// Update accordion header safely
				const $headTitle = $(`.wsts-accordion-item[data-slide-index="${idx}"] .wsts-accordion-head span:first-child`);
				$headTitle.text('Slide ' + (idx + 1) + ' — ' + (val || 'Page'));
			}

			if (idx === currentSlideIndex) {
				updateSimulatorDisplay();
				if (field === 'animation' || field === 'anim_duration' || field === 'anim_delay') {
					playSlideAnimation(idx);
				}
			}

			updateCharCounters();
			updateSizingAudit();
		});

		// Preview animation button
		$(document).on('click', '.wsts-btn-preview-anim', function (e) {
			e.preventDefault();
			const idx = parseInt($(this).data('index'), 10);
			if (idx !== currentSlideIndex) {
				goToSlide(idx);
			} else {
				playSlideAnimation(idx);
			}
		});
	}

	/**
	 * Update phone simulator display for active slide
	 */
	function updateSimulatorDisplay() {
		if (!activeStoryData || !activeStoryData.pages || !activeStoryData.pages[currentSlideIndex]) return;

		const slide = activeStoryData.pages[currentSlideIndex];

		// Background & Scrim
		const bgImg = slide.bg_image || activeStoryData.poster_image;
		$('#simBg').css('background-image', 'url(' + encodeURI(bgImg) + ')');
		const currentScrim = slide.scrim || activeStoryData.global_scrim || 'linear-gradient(180deg, rgba(8,8,17,0.3) 0%, rgba(8,8,17,0.92) 80%)';
		$('#simScrim').css('background', currentScrim !== 'none' ? currentScrim : 'transparent');

		// Publisher Header
		$('#simPublisherName').text(activeStoryData.publisher || 'Web Stories');
		if (activeStoryData.publisher_logo) {
			$('#simPublisherLogo').attr('src', activeStoryData.publisher_logo).show();
		} else {
			$('#simPublisherLogo').hide();
		}

		// Text alignment
		const align = slide.text_align || 'left';
		const $content = $('.wsts-sim-content');
		$content.css('text-align', align);
		if (align === 'center') {
			$content.css('align-items', 'center');
		} else if (align === 'right') {
			$content.css('align-items', 'flex-end');
		} else {
			$content.css('align-items', 'flex-start');
		}

		// Badge
		if (slide.badge) {
			$('#simBadge').text(slide.badge).show();
		} else {
			$('#simBadge').hide();
		}
		const badgeBg = slide.badge_bg || activeStoryData.badge_bg || activeStoryData.accent_color || '#00f2fe';
		const badgeColor = slide.badge_color || activeStoryData.badge_color || '#ffffff';
		$('#simBadge').css({
			'background-color': badgeBg,
			'color': badgeColor
		});

		// Stat box
		if (slide.stat_number) {
			$('#simStatNum').text(slide.stat_number);
			$('#simStatLbl').text(slide.stat_label || '');
			const statSize = activeStoryData.stat_size ? activeStoryData.stat_size + 'px' : '40px';
			const statColor = slide.stat_color || activeStoryData.accent_color || '#d4af37';
			$('#simStatNum').css({
				'font-size': statSize,
				'color': statColor
			});
			$('#simStatBox').show();
		} else {
			$('#simStatBox').hide();
		}

		// Headline
		$('#simHeadline').text(slide.headline || '');
		const headSize = slide.headline_size ? slide.headline_size + 'px' : (activeStoryData.headline_size ? activeStoryData.headline_size + 'px' : '32px');
		const headColor = slide.headline_color || activeStoryData.headline_color || '#ffffff';
		const tracking = activeStoryData.headline_tracking || '-0.01em';
		const lineHeight = activeStoryData.headline_line_height || '1.18';
		$('#simHeadline').css({
			'font-size': headSize,
			'color': headColor,
			'text-align': align,
			'letter-spacing': tracking,
			'line-height': lineHeight
		});

		// Body
		$('#simBody').text(slide.body || '');
		const bodySize = activeStoryData.body_size ? activeStoryData.body_size + 'px' : '15px';
		const bodyColor = slide.body_color || 'rgba(255,255,255,0.92)';
		$('#simBody').css({
			'font-size': bodySize,
			'color': bodyColor,
			'text-align': align
		});

		// CTA Button
		if (slide.cta_text && slide.cta_url && slide.cta_url !== '#') {
			$('#simCtaBtn').text(slide.cta_text + ' \u2192').attr('href', slide.cta_url);
			$('#simCtaBox').show();
		} else {
			$('#simCtaBox').hide();
		}

		// Typography & font
		const titleFont = activeStoryData.font_family_title || "'Space Grotesk', sans-serif";
		const bodyFont = activeStoryData.font_family_body || "'Inter', sans-serif";
		$('#simHeadline, #simStatNum').css('font-family', titleFont);
		$('#simBody').css('font-family', bodyFont);

		// Colors & Ambient Lighting
		applyColorStyles();

		// Play Entrance Animation in simulator
		playSlideAnimation(currentSlideIndex);

		// Progress bars
		updateSimulatorProgressState();

		// Page indicator label
		$('#simCurrentPageLabel').text('Page ' + (currentSlideIndex + 1) + ' / ' + activeStoryData.pages.length);
	}

	/**
	 * Dynamic Google Fonts & Custom Webfont Loaders
	 */
	function loadGoogleFontDynamically(fontFamily) {
		if (!fontFamily) return;
		const match = fontFamily.match(/^['"]?([^,'"]+)['"]?/);
		if (!match || !match[1]) return;
		const fontName = match[1].trim();

		const systemFonts = ['system-ui', '-apple-system', 'arial', 'helvetica', 'georgia', 'times new roman', 'sans-serif', 'serif', 'monospace', '__custom__'];
		if (systemFonts.includes(fontName.toLowerCase())) return;

		const fontSlug = fontName.replace(/\s+/g, '+');
		const linkId = 'wsts-dyn-font-' + fontName.toLowerCase().replace(/[^a-z0-9]/g, '-');
		if (document.getElementById(linkId)) return;

		const link = document.createElement('link');
		link.id = linkId;
		link.rel = 'stylesheet';
		link.href = 'https://fonts.googleapis.com/css2?family=' + fontSlug + ':wght@300;400;500;600;700;800;900&display=swap';
		document.head.appendChild(link);
	}

	function loadCustomFontStylesheet(url) {
		if (!url || !/^https?:\/\//i.test(url)) return;
		const linkId = 'wsts-custom-webfont-link';
		let link = document.getElementById(linkId);
		if (!link) {
			link = document.createElement('link');
			link.id = linkId;
			link.rel = 'stylesheet';
			document.head.appendChild(link);
		}
		link.href = url;
	}

	/**
	 * Live Type Specimen Preview Updater
	 * Keeps specimen box and simulator preview synchronized
	 */
	function updateTypoSpecimen() {
		if (!activeStoryData) return;

		const titleFont = $('#customTitleFontUnified').val() || activeStoryData.font_family_title || "'Space Grotesk', sans-serif";
		const bodyFont = $('#customBodyFontUnified').val() || activeStoryData.font_family_body || "'Inter', sans-serif";
		const headSize = parseInt($('#customHeadlineSizeRange').val(), 10) || activeStoryData.headline_size || 32;
		const tracking = $('#customHeadlineTracking').val() || activeStoryData.headline_tracking || '-0.01em';
		const lineHeight = $('#customHeadlineLineHeight').val() || activeStoryData.headline_line_height || '1.18';
		const bodySize = parseInt($('#customBodySizeRange').val(), 10) || activeStoryData.body_size || 15;
		const statSize = parseInt($('#customStatSizeRange').val(), 10) || activeStoryData.stat_size || 40;

		const headColor = $('#unifiedHeadingColorHex').val() || (activeStoryData ? activeStoryData.headline_color : '#ffffff') || '#ffffff';
		const statColor = $('#unifiedBadgeBgHex').val() || (activeStoryData ? activeStoryData.badge_bg : '#00f2fe') || '#00f2fe';

		// Update Type Specimen Card in Typography Tab
		$('#specimenHeadline').css({
			'font-family': titleFont,
			'font-size': headSize + 'px',
			'letter-spacing': tracking,
			'line-height': lineHeight,
			'color': headColor
		});
		$('#specimenBody').css({
			'font-family': bodyFont,
			'font-size': bodySize + 'px'
		});
		$('#specimenStat').css({
			'font-family': titleFont,
			'font-size': statSize + 'px',
			'color': statColor
		});

		// Live Sync to Simulator Phone Screen
		$('#simHeadline').css({
			'font-family': titleFont,
			'font-size': headSize + 'px',
			'letter-spacing': tracking,
			'line-height': lineHeight
		});
		$('#simBody').css({
			'font-family': bodyFont,
			'font-size': bodySize + 'px'
		});
		$('#simStatNum').css({
			'font-family': titleFont,
			'font-size': statSize + 'px'
		});
	}

	/**
	 * Apply dynamic styling colors
	 */
	function applyColorStyles() {
		if (!activeStoryData) return;
		const accent = activeStoryData.accent_color || '#d4af37';
		const secondary = activeStoryData.secondary_color || '#e6ded4';

		const slide = activeStoryData.pages && activeStoryData.pages[currentSlideIndex] ? activeStoryData.pages[currentSlideIndex] : null;
		const badgeBg = (slide && slide.badge_bg) ? slide.badge_bg : accent;
		const statColor = (slide && slide.stat_color) ? slide.stat_color : accent;

		$('#simBadge').css('background-color', badgeBg);
		$('#simStatNum').css('color', statColor);
		$('#simCtaBtn').css('background', 'linear-gradient(135deg, ' + accent + ', ' + secondary + ')');

		// Soft ambient backlight on phone mockup
		$('.wsts-phone-mockup').css('--sim-glow', accent);
	}

	/**
	 * Progress Bars in Phone Simulator
	 */
	function renderSimulatorProgress() {
		const $bars = $('#simProgressBars');
		$bars.empty();

		if (!activeStoryData || !activeStoryData.pages) return;

		const count = activeStoryData.pages.length;
		if (count > 20) {
			$bars.css('gap', '1.5px');
		} else if (count > 10) {
			$bars.css('gap', '2px');
		} else {
			$bars.css('gap', '3px');
		}

		for (let i = 0; i < count; i++) {
			$bars.append('<div class="wsts-progress-bar"><div class="wsts-progress-fill"></div></div>');
		}
	}

	function updateSimulatorProgressState() {
		const $bars = $('#simProgressBars .wsts-progress-bar');
		$bars.each(function (idx) {
			if (idx <= currentSlideIndex) {
				$(this).addClass('is-active');
			} else {
				$(this).removeClass('is-active');
			}
		});
	}

	/**
	 * Navigation (Tapping & Buttons)
	 */
	function initSlideNavigation() {
		// Taps on simulator screen & floating glass chevrons
		$('#simPrevBtn, #btnSimPrev, #simChevronPrev').on('click', function (e) {
			e.preventDefault();
			if (currentSlideIndex > 0) {
				goToSlide(currentSlideIndex - 1);
			}
		});

		$('#simNextBtn, #btnSimNext, #simChevronNext').on('click', function (e) {
			e.preventDefault();
			if (activeStoryData && activeStoryData.pages && currentSlideIndex < activeStoryData.pages.length - 1) {
				goToSlide(currentSlideIndex + 1);
			}
		});

		// Keyboard arrow navigation for buttery-smooth slide switching
		$(document).on('keydown', function (e) {
			const tag = (e.target && e.target.tagName ? e.target.tagName.toLowerCase() : '');
			if (tag === 'input' || tag === 'textarea' || tag === 'select') {
				return;
			}

			if ($('#wstsCustomizerModal').is(':visible') && !$('#wstsLivePreviewModal').is(':visible')) {
				if (e.key === 'ArrowRight') {
					if (activeStoryData && activeStoryData.pages && currentSlideIndex < activeStoryData.pages.length - 1) {
						goToSlide(currentSlideIndex + 1);
					}
				} else if (e.key === 'ArrowLeft') {
					if (currentSlideIndex > 0) {
						goToSlide(currentSlideIndex - 1);
					}
				}
			}
		});
	}

	function goToSlide(index) {
		currentSlideIndex = index;
		updateSimulatorDisplay();

		// Sync accordion open state
		const $item = $('.wsts-accordion-item[data-slide-index="' + index + '"]');
		$item.addClass('is-open').siblings().removeClass('is-open');

		// Ensure slides tab is active so user sees this slide's inputs
		if (!$('#tab-slides').hasClass('is-active')) {
			$('.wsts-adjust-tab[data-tab="tab-slides"]').click();
		}

		// Smooth scroll accordion item into view if needed
		if ($item.length && $item[0].scrollIntoView) {
			$item[0].scrollIntoView({ behavior: 'smooth', block: 'nearest' });
		}
	}

	/**
	 * Safe Zone Overlay Toggle
	 */
	function initSafeZoneToggle() {
		$('#btnToggleSafeZone').on('click', function () {
			isSafeZoneOn = !isSafeZoneOn;
			if (isSafeZoneOn) {
				$(this).addClass('is-active');
				$('#safeZoneStateText').text('ON');
				$('#safeZoneOverlay').addClass('is-visible');
			} else {
				$(this).removeClass('is-active');
				$('#safeZoneStateText').text('OFF');
				$('#safeZoneOverlay').removeClass('is-visible');
			}
		});
	}

	/**
	 * SEO & Google Discover Management
	 */
	function initSeoTab() {
		$('#customSeoTitle').on('input', function () {
			if (activeStoryData) {
				activeStoryData.seo_title = $(this).val();
			}
			updateSeoDisplay();
		});

		$('#customSeoDesc').on('input', function () {
			if (activeStoryData) {
				activeStoryData.seo_desc = $(this).val();
			}
			updateSeoDisplay();
		});

		$('#customSchemaType').on('change', function () {
			if (activeStoryData) {
				activeStoryData.schema_type = $(this).val();
			}
		});

		$('#customCanonicalUrl').on('input', function () {
			if (activeStoryData) {
				activeStoryData.canonical_url = $(this).val();
			}
		});

		$('#customOgImage').on('input change', function () {
			if (activeStoryData) {
				activeStoryData.og_image = $(this).val();
			}
			updateSeoDisplay();
		});

		// 1-Click Auto Generate SEO
		$('#btnAutoGenerateSeo').on('click', function () {
			if (!activeStoryData) return;

			const s1 = (activeStoryData.pages && activeStoryData.pages[0]) ? activeStoryData.pages[0] : null;
			const s2 = (activeStoryData.pages && activeStoryData.pages[1]) ? activeStoryData.pages[1] : null;

			let autoTitle = (s1 && s1.headline) ? s1.headline : (activeStoryData.title || activeStoryData.name);
			if (autoTitle.length > 68) {
				autoTitle = autoTitle.substring(0, 65) + '...';
			}

			let autoDesc = '';
			if (s1 && s1.body) {
				autoDesc = s1.body;
			} else if (s2 && s2.body) {
				autoDesc = s2.body;
			} else {
				autoDesc = activeStoryData.tagline || 'Experience this visual Web Story featuring curated interactive highlights.';
			}

			if (autoDesc.length > 155) {
				autoDesc = autoDesc.substring(0, 152) + '...';
			}

			$('#customSeoTitle').val(autoTitle).trigger('input');
			$('#customSeoDesc').val(autoDesc).trigger('input');

			// Microinteraction pulse
			$('.wsts-discover-mockup').addClass('wsts-card-pulse');
			setTimeout(function () {
				$('.wsts-discover-mockup').removeClass('wsts-card-pulse');
			}, 500);
		});
	}

	function initSeoFieldsForStory() {
		if (!activeStoryData) return;

		const s1 = (activeStoryData.pages && activeStoryData.pages[0]) ? activeStoryData.pages[0] : null;
		const defaultTitle = activeStoryData.seo_title || (s1 && s1.headline ? s1.headline : (activeStoryData.title || activeStoryData.name));
		const defaultDesc = activeStoryData.seo_desc || (s1 && s1.body ? s1.body.substring(0, 150) : (activeStoryData.tagline || ''));
		const defaultSchema = activeStoryData.schema_type || 'NewsArticle';
		const defaultCanonical = activeStoryData.canonical_url || (wstsStudio && wstsStudio.homeUrl ? wstsStudio.homeUrl : '');
		const defaultOg = activeStoryData.og_image || activeStoryData.poster_image || '';

		activeStoryData.seo_title = defaultTitle;
		activeStoryData.seo_desc = defaultDesc;
		activeStoryData.schema_type = defaultSchema;
		activeStoryData.canonical_url = defaultCanonical;
		activeStoryData.og_image = defaultOg;

		$('#customSeoTitle').val(defaultTitle);
		$('#customSeoDesc').val(defaultDesc);
		$('#customSchemaType').val(defaultSchema);
		$('#customCanonicalUrl').val(defaultCanonical);
		$('#customOgImage').val(defaultOg);

		updateSeoDisplay();
	}

	function updateSeoDisplay() {
		if (!activeStoryData) return;

		const title = $('#customSeoTitle').val() || activeStoryData.title || 'Web Story Title';
		const desc = $('#customSeoDesc').val() || 'Compelling snippet summarizing the story hook for Google Discover readers.';
		const poster = $('#customOgImage').val() || $('#customPosterImage').val() || activeStoryData.poster_image || '';
		const publisher = $('#customPublisher').val() || activeStoryData.publisher || 'Publisher';
		const logo = $('#customLogoUrl').val() || activeStoryData.publisher_logo || '';

		$('#discoverPreviewHeadline').text(title);
		$('#discoverPreviewDesc').text(desc);
		$('#discoverPreviewPublisher').text(publisher);

		if (poster) {
			$('#discoverPreviewImg').attr('src', poster).show();
		} else {
			$('#discoverPreviewImg').hide();
		}

		if (logo) {
			$('#discoverPreviewFavicon').attr('src', logo).show();
		} else {
			$('#discoverPreviewFavicon').hide();
		}

		// Character counters
		const titleLen = (title || '').length;
		$('#seoTitleCharCount')
			.text(titleLen + ' / 70')
			.toggleClass('is-warning', titleLen > 70);

		const descLen = (desc || '').length;
		$('#seoDescCharCount')
			.text(descLen + ' / 160')
			.toggleClass('is-warning', descLen > 160);
	}

	/**
	 * Live Interactive Story Preview Modal (Before Publishing)
	 */
	function initStoryLivePreviewModal() {
		// Open preview modal
		$('#btnLivePreviewStory').on('click', function () {
			openLivePreviewModal();
		});

		// Close preview modal
		$('#btnLivePreviewClose, #btnClosePreviewModal, #wstsLivePreviewModal .wsts-modal-backdrop').on('click', function () {
			$('#wstsLivePreviewModal').fadeOut(200);
			const iframe = document.getElementById('wstsLivePreviewIframe');
			if (iframe) {
				iframe.src = 'about:blank';
			}
		});

		// Device switches
		$('#btnPreviewMobile').on('click', function () {
			$('.wsts-preview-device-switch .wsts-device-pill').removeClass('is-active');
			$(this).addClass('is-active');
			$('#livePreviewFrameWrap').removeClass('is-desktop').addClass('is-mobile');
		});

		$('#btnPreviewDesktop').on('click', function () {
			$('.wsts-preview-device-switch .wsts-device-pill').removeClass('is-active');
			$(this).addClass('is-active');
			$('#livePreviewFrameWrap').removeClass('is-mobile').addClass('is-desktop');
		});

		// Open in new tab (Real HTTP endpoint gives complete AMP engine runtime & fonts)
		$('#btnOpenStandalonePreview').on('click', function () {
			if (currentPreviewUrl) {
				window.open(currentPreviewUrl, '_blank');
			} else if (currentPreviewAmpHtml) {
				const win = window.open('', '_blank');
				if (win) {
					win.document.open();
					win.document.write(currentPreviewAmpHtml);
					win.document.close();
				}
			}
		});

		// Publish directly from preview modal
		$('#btnPreviewPublishStory').on('click', function () {
			$('#wstsLivePreviewModal').fadeOut(150);
			createStory('publish');
		});
	}

	function openLivePreviewModal() {
		if (!activeStoryData) return;

		syncAllFormInputs();

		const $btn = $('#btnLivePreviewStory');
		const originalText = $btn.text();
		$btn.text('Compiling AMP...').prop('disabled', true);

		$.ajax({
			url: wstsStudio.ajaxUrl,
			type: 'POST',
			data: {
				action: 'wsts_export_amp',
				nonce: wstsStudio.nonce,
				story_data: JSON.stringify(activeStoryData)
			},
			success: function (res) {
				$btn.text(originalText).prop('disabled', false);
				if (res.success && (res.data.preview_url || res.data.amp_html)) {
					currentPreviewAmpHtml = res.data.amp_html || '';
					currentPreviewUrl = res.data.preview_url || '';
					$('#livePreviewTitle').text(activeStoryData.title || 'Story Preview');
					$('#wstsLivePreviewModal').fadeIn(250);

					const iframe = document.getElementById('wstsLivePreviewIframe');
					if (iframe) {
						if (currentPreviewUrl) {
							iframe.src = currentPreviewUrl;
						} else {
							iframe.srcdoc = currentPreviewAmpHtml;
						}
					}
				} else {
					showNotification('Preview Error', (res.data && res.data.message) ? res.data.message : 'Could not generate story preview.');
				}
			},
			error: function () {
				$btn.text(originalText).prop('disabled', false);
				showNotification('Connection Error', 'Could not compile story preview. Please check your network connection.');
			}
		});
	}

	function syncAllFormInputs() {
		if (!activeStoryData) return;
		activeStoryData.title = $('#customStoryTitle').val() || activeStoryData.title;
		const customCat = $('#customStoryCategoryCustom').val().trim();
		const selCat = $('#customStoryCategory').val();
		const finalCat = customCat ? customCat : selCat;
		activeStoryData.story_category = finalCat || activeStoryData.story_category || activeStoryData.category;
		activeStoryData.category = activeStoryData.story_category || activeStoryData.category;
		activeStoryData.publisher = $('#customPublisher').val() || activeStoryData.publisher;
		activeStoryData.publisher_logo = $('#customLogoUrl').val() || activeStoryData.publisher_logo;
		activeStoryData.poster_image = $('#customPosterImage').val() || activeStoryData.poster_image;
		activeStoryData.accent_color = $('#customAccentHex').val() || activeStoryData.accent_color;
		activeStoryData.secondary_color = $('#customSecondaryHex').val() || activeStoryData.secondary_color;
		activeStoryData.bg_color = $('#customBgHex').val() || activeStoryData.bg_color;
		activeStoryData.headline_color = $('#unifiedHeadingColorHex').val() || activeStoryData.headline_color;
		activeStoryData.badge_bg = $('#unifiedBadgeBgHex').val() || activeStoryData.badge_bg;
		activeStoryData.badge_color = $('#unifiedBadgeColorHex').val() || activeStoryData.badge_color;
		activeStoryData.font_family_title = $('#customTitleFontUnified').val() || activeStoryData.font_family_title;
		activeStoryData.font_family_body = $('#customBodyFontUnified').val() || activeStoryData.font_family_body;
		activeStoryData.headline_size = parseInt($('#customHeadlineSizeRange').val(), 10) || activeStoryData.headline_size || 32;
		activeStoryData.body_size = parseInt($('#customBodySizeRange').val(), 10) || activeStoryData.body_size || 15;
		activeStoryData.stat_size = parseInt($('#customStatSizeRange').val(), 10) || activeStoryData.stat_size || 40;
		activeStoryData.headline_tracking = $('#customHeadlineTracking').val() || activeStoryData.headline_tracking || '-0.01em';
		activeStoryData.headline_line_height = $('#customHeadlineLineHeight').val() || activeStoryData.headline_line_height || '1.18';
		activeStoryData.global_scrim = $('#customGlobalScrim').val() || activeStoryData.global_scrim;
		activeStoryData.seo_title = $('#customSeoTitle').val() || activeStoryData.seo_title || activeStoryData.title;
		activeStoryData.seo_desc = $('#customSeoDesc').val() || activeStoryData.seo_desc || '';
		activeStoryData.schema_type = $('#customSchemaType').val() || 'NewsArticle';
		activeStoryData.canonical_url = $('#customCanonicalUrl').val() || '';
		activeStoryData.og_image = $('#customOgImage').val() || activeStoryData.poster_image || '';
		activeStoryData.adsense_client = $('#customAdSenseClient').val() || '';
		activeStoryData.adsense_slot = $('#customAdSenseSlot').val() || '';
		activeStoryData.ga4_id = $('#customGa4Id').val() || '';
		activeStoryData.background_audio = $('#customBackgroundAudio').val() || '';

		// Synchronize all slide DOM input fields into activeStoryData.pages
		if (activeStoryData.pages && activeStoryData.pages.length) {
			$('.slide-field').each(function () {
				const field = $(this).data('field');
				const idx = parseInt($(this).data('index'), 10);
				if (field && !isNaN(idx) && activeStoryData.pages[idx]) {
					let val = $(this).val();
					if ($(this).hasClass('wsts-color-hex-input')) {
						let hex = (val || '').trim();
						if (hex && !hex.startsWith('#')) hex = '#' + hex;
						if (/^#[0-9A-Fa-f]{6}$/.test(hex)) {
							val = hex;
						}
					}
					activeStoryData.pages[idx][field] = val;
				}
			});
		}
	}

	/**
	 * Character Counter & Sizing Validator
	 */
	function updateCharCounters() {
		$('.char-counter').each(function () {
			const idx = $(this).data('index');
			const $textarea = $('.slide-field[data-field="body"][data-index="' + idx + '"]');
			const len = ($textarea.val() || '').length;
			$(this).text(len);

			if (len > 280) {
				$(this).css('color', '#ef4444');
			} else {
				$(this).css('color', '#10b981');
			}
		});
	}

	function updateSizingAudit() {
		if (!activeStoryData) return;

		const iconPassSvg = '<svg width="14" height="14" viewBox="0 0 16 16" fill="#34d399"><path d="M13.78 4.22a.75.75 0 0 1 0 1.06l-7.25 7.25a.75.75 0 0 1-1.06 0L2.22 9.28a.751.751 0 0 1 .018-1.042.751.751 0 0 1 1.042-.018L6 10.94l6.72-6.72a.75.75 0 0 1 1.06 0Z"/></svg>';
		const iconWarnSvg = '<svg width="14" height="14" viewBox="0 0 16 16" fill="#f59e0b"><path d="M6.457 1.047c.659-1.234 2.427-1.234 3.086 0l6.082 11.378A1.75 1.75 0 0 1 14.082 15H1.918a1.75 1.75 0 0 1-1.543-2.575Zm1.763.707a.25.25 0 0 0-.44 0L1.698 13.132a.25.25 0 0 0 .22.368h12.164a.25.25 0 0 0 .22-.368Zm.53 3.996v2.5a.75.75 0 0 1-1.5 0v-2.5a.75.75 0 0 1 1.5 0ZM9 11a1 1 0 1 1-2 0 1 1 0 0 1 2 0Z"/></svg>';

		// Title word count check
		const title = $('#customStoryTitle').val() || '';
		const words = title.trim().split(/\s+/).filter(Boolean).length;
		if (words > 10) {
			$('#auditTitleLength')
				.empty()
				.append($('<span>', { class: 'wsts-audit-icon' }).html(iconWarnSvg))
				.append($('<div>').html('<strong>Title Warning:</strong> ' + words + ' words. Google recommends under 10 words to prevent truncation on mobile screens.'))
				.css('color', '#f59e0b');
		} else {
			$('#auditTitleLength')
				.empty()
				.append($('<span>', { class: 'wsts-audit-icon' }).html(iconPassSvg))
				.append($('<div>').html('<strong>Title Optimal:</strong> ' + words + ' words (Within Google\'s recommended 10-word limit).'))
				.css('color', '#34d399');
		}

		// Text density check
		let hasOverflow = false;
		if (activeStoryData.pages) {
			activeStoryData.pages.forEach(function (p) {
				if ((p.body || '').length > 280) {
					hasOverflow = true;
				}
			});
		}

		if (hasOverflow) {
			$('#auditTextCheck')
				.empty()
				.append($('<span>', { class: 'wsts-audit-icon' }).html(iconWarnSvg))
				.append($('<div>').html('<strong>Slide Copy Density:</strong> One or more slides exceed Google\'s 280-character maximum. Shorten text for better engagement.'))
				.css('color', '#f59e0b');
		} else {
			$('#auditTextCheck')
				.empty()
				.append($('<span>', { class: 'wsts-audit-icon' }).html(iconPassSvg))
				.append($('<div>').html('<strong>Slide Copy Density:</strong> All slides are within Google\'s 280-character limit.'))
				.css('color', '#34d399');
		}

		// Story length & retention check (supporting 20+ slides)
		const totalPages = activeStoryData.pages ? activeStoryData.pages.length : 0;
		if (totalPages < 5) {
			$('#auditSlideCount')
				.empty()
				.append($('<span>', { class: 'wsts-audit-icon' }).html(iconWarnSvg))
				.append($('<div>').html('<strong>Story Length:</strong> ' + totalPages + ' slides (Short format. Google recommends 10–20 slides for peak narrative depth).'))
				.css('color', '#f59e0b');
		} else if (totalPages <= 20) {
			$('#auditSlideCount')
				.empty()
				.append($('<span>', { class: 'wsts-audit-icon' }).html(iconPassSvg))
				.append($('<div>').html('<strong>Story Length:</strong> ' + totalPages + ' slides (Optimal! 10–20 slides achieves Google\'s highest completion rate).'))
				.css('color', '#34d399');
		} else if (totalPages <= 30) {
			$('#auditSlideCount')
				.empty()
				.append($('<span>', { class: 'wsts-audit-icon' }).html(iconPassSvg))
				.append($('<div>').html('<strong>Story Length:</strong> ' + totalPages + ' slides (Long-form story. Supported by Google; ensure media assets stay under 300KB each).'))
				.css('color', '#34d399');
		} else {
			$('#auditSlideCount')
				.empty()
				.append($('<span>', { class: 'wsts-audit-icon' }).html(iconWarnSvg))
				.append($('<div>').html('<strong>Story Length:</strong> ' + totalPages + ' slides (Notice: Over 30 slides risks user drop-off; keep media ultra-fast loading).'))
				.css('color', '#f59e0b');
		}
	}

	/**
	 * WordPress Media Uploader
	 */
	function initMediaUploader() {
		// Story meta media button
		$('.wsts-media-picker-btn').on('click', function (e) {
			e.preventDefault();
			const targetInput = $(this).data('target');

			const frame = wp.media({
				title: 'Select or Upload Web Story Asset',
				button: { text: 'Use Asset' },
				multiple: false
			});

			frame.on('select', function () {
				const attachment = frame.state().get('selection').first().toJSON();
				$(targetInput).val(attachment.url).trigger('input');
			});

			frame.open();
		});

		// Slide background media button
		$(document).on('click', '.wsts-slide-media-btn', function (e) {
			e.preventDefault();
			const idx = $(this).data('index');
			const $input = $('.slide-bg-input[data-index="' + idx + '"]');

			const frame = wp.media({
				title: 'Select Slide 9:16 Portrait Image',
				button: { text: 'Use Slide Background' },
				multiple: false
			});

			frame.on('select', function () {
				const attachment = frame.state().get('selection').first().toJSON();
				$input.val(attachment.url).trigger('input');
			});

			frame.open();
		});
	}

	/**
	 * Notification Modal System (Replaces native alert/confirm)
	 */
	function initNotificationModal() {
		$('#btnNotifyClose, #wstsNotifyModal .wsts-modal-backdrop, #btnNotifyOk').on('click', function () {
			$('#wstsNotifyModal').fadeOut(200);
		});
	}

	function showNotification(title, message, extraHtml, actions) {
		$('#wstsNotifyTitle').text(title || 'Notification');
		$('#wstsNotifyMessage').text(message || '');
		const $extra = $('#wstsNotifyExtra');
		$extra.empty();
		if (extraHtml) {
			$extra.append(extraHtml);
		}

		const $actionsBox = $('#wstsNotifyActions');
		$actionsBox.empty();

		if (actions && actions.length) {
			actions.forEach(function (act) {
				const $btn = $('<button>', {
					type: 'button',
					class: 'button ' + (act.primary ? 'wsts-btn-apple-pill-primary' : 'wsts-btn-apple-pill-secondary')
				}).text(act.text);

				$btn.on('click', function () {
					$('#wstsNotifyModal').fadeOut(150);
					if (typeof act.callback === 'function') {
						act.callback();
					}
				});
				$actionsBox.append($btn);
			});
		} else {
			const $okBtn = $('<button>', {
				type: 'button',
				class: 'button wsts-btn-apple-pill-primary'
			}).text('OK');
			$okBtn.on('click', function () {
				$('#wstsNotifyModal').fadeOut(150);
			});
			$actionsBox.append($okBtn);
		}

		$('#wstsNotifyModal').fadeIn(200);
	}

	/**
	 * Story Creation & Actions
	 */
	function initActions() {
		// Save Draft
		$('#btnSaveDraft').on('click', function () {
			createStory('draft');
		});

		// 1-Click Publish
		$('#btnPublishStory').on('click', function () {
			createStory('publish');
		});

		// Export AMP HTML
		$('#btnExportAmpHtml').on('click', function () {
			exportAmpHtml();
		});

		$('#btnExportModalClose, #wstsExportModal .wsts-modal-backdrop').on('click', function () {
			$('#wstsExportModal').fadeOut(200);
		});

		$('#btnCopyAmpCode').on('click', function () {
			const codeText = $('#wstsExportCodeTextarea').val();
			const $btn = $(this);

			if (navigator.clipboard && navigator.clipboard.writeText) {
				navigator.clipboard.writeText(codeText).then(function () {
					$btn.text('Copied to Clipboard');
					setTimeout(function () {
						$btn.text('Copy to Clipboard');
					}, 2000);
				});
			} else {
				const $code = $('#wstsExportCodeTextarea');
				$code.select();
				document.execCommand('copy');
				$btn.text('Copied to Clipboard');
				setTimeout(function () {
					$btn.text('Copy to Clipboard');
				}, 2000);
			}
		});
	}

	function createStory(status) {
		const $btn = status === 'publish' ? $('#btnPublishStory') : $('#btnSaveDraft');
		const originalText = $btn.text();
		$btn.text(status === 'publish' ? 'Publishing Story...' : 'Saving Draft...').prop('disabled', true);

		// Synchronize all form inputs into activeStoryData
		syncAllFormInputs();

		$.ajax({
			url: wstsStudio.ajaxUrl,
			type: 'POST',
			data: {
				action: 'wsts_create_story',
				nonce: wstsStudio.nonce,
				status: status,
				story_data: JSON.stringify(activeStoryData)
			},
			success: function (res) {
				$btn.text(originalText).prop('disabled', false);
				if (res.success) {
					if (status === 'draft') {
						showNotification(
							'Story Draft Saved',
							res.data.message || 'Your Web Story draft has been saved successfully.',
							null,
							[
								{
									text: 'Continue in Studio',
									primary: false
								},
								{
									text: 'Open in Editor',
									primary: true,
									callback: function () {
										window.location.href = res.data.edit_url;
									}
								}
							]
						);
					} else {
						const $link = $('<a>', {
							href: res.data.view_url,
							target: '_blank',
							rel: 'noopener',
							style: 'display: inline-block; padding: 8px 18px; background: #a78bfa; color: #09090b; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 13px;'
						}).text('View Live Story \u2192');

						showNotification(
							'Web Story Published',
							res.data.message || 'Your story is live and compliant with Google Web Stories specifications.',
							$link,
							[
								{
									text: 'Done',
									primary: false
								}
							]
						);
					}
				} else {
					showNotification('Creation Error', (res.data && res.data.message) ? res.data.message : 'Failed to create story.');
				}
			},
			error: function () {
				$btn.text(originalText).prop('disabled', false);
				showNotification('Connection Error', 'Could not communicate with the WordPress server. Please refresh and check your login status.');
			}
		});
	}

	function exportAmpHtml() {
		const $btn = $('#btnExportAmpHtml');
		$btn.text('Generating Standalone AMP...').prop('disabled', true);

		syncAllFormInputs();

		$.ajax({
			url: wstsStudio.ajaxUrl,
			type: 'POST',
			data: {
				action: 'wsts_export_amp',
				nonce: wstsStudio.nonce,
				story_data: JSON.stringify(activeStoryData)
			},
			success: function (res) {
				$btn.text('</> Export Standalone AMP').prop('disabled', false);
				if (res.success) {
					$('#wstsExportCodeTextarea').val(res.data.amp_html);
					$('#wstsExportModal').fadeIn(200);
				} else {
					showNotification('Export Error', (res.data && res.data.message) ? res.data.message : 'Failed to generate AMP HTML.');
				}
			},
			error: function () {
				$btn.text('</> Export Standalone AMP').prop('disabled', false);
				showNotification('Connection Error', 'Server communication error.');
			}
		});
	}

	/**
	 * Dynamic Slide CRUD Operations (Supports 20+ slides)
	 */
	function addNewSlide() {
		if (!activeStoryData) return;
		if (!activeStoryData.pages) {
			activeStoryData.pages = [];
		}

		const slideNum = activeStoryData.pages.length + 1;
		const newSlide = {
			id: 'page-' + slideNum,
			page_type: 'content',
			badge: 'PAGE ' + slideNum,
			badge_bg: activeStoryData.accent_color || '#4f46e5',
			badge_color: '#ffffff',
			headline: 'New Slide ' + slideNum,
			headline_size: 32,
			headline_color: '#ffffff',
			text_align: 'left',
			body: 'Add your story narrative or listicle content here.',
			body_color: 'rgba(255,255,255,0.92)',
			stat_number: '',
			stat_label: '',
			stat_color: activeStoryData.accent_color || '#4f46e5',
			bg_image: activeStoryData.poster_image || '',
			scrim: 'linear-gradient(180deg, rgba(8,8,17,0.3) 0%, rgba(8,8,17,0.92) 80%)',
			cta_text: '',
			cta_url: '#',
			animation: 'fade-in',
			duration: 8
		};

		activeStoryData.pages.push(newSlide);
		currentSlideIndex = activeStoryData.pages.length - 1;
		renderSlideAccordion();
		renderSimulatorProgress();
		updateSimulatorDisplay();
		updateSizingAudit();
	}

	function duplicateSlide(idx) {
		if (!activeStoryData || !activeStoryData.pages || !activeStoryData.pages[idx]) return;

		const cloned = JSON.parse(JSON.stringify(activeStoryData.pages[idx]));
		cloned.id = 'page-' + (activeStoryData.pages.length + 1);
		cloned.headline = cloned.headline ? cloned.headline + ' (Copy)' : 'Copy of Slide';

		activeStoryData.pages.splice(idx + 1, 0, cloned);
		currentSlideIndex = idx + 1;
		renderSlideAccordion();
		renderSimulatorProgress();
		updateSimulatorDisplay();
		updateSizingAudit();
	}

	function deleteSlide(idx) {
		if (!activeStoryData || !activeStoryData.pages || activeStoryData.pages.length <= 1) {
			showNotification('Cannot Delete', 'A Web Story must contain at least 1 slide.');
			return;
		}

		activeStoryData.pages.splice(idx, 1);
		if (currentSlideIndex >= activeStoryData.pages.length) {
			currentSlideIndex = activeStoryData.pages.length - 1;
		}
		renderSlideAccordion();
		renderSimulatorProgress();
		updateSimulatorDisplay();
		updateSizingAudit();
	}

	function moveSlide(idx, direction) {
		if (!activeStoryData || !activeStoryData.pages) return;

		const targetIdx = idx + direction;
		if (targetIdx < 0 || targetIdx >= activeStoryData.pages.length) return;

		const temp = activeStoryData.pages[idx];
		activeStoryData.pages[idx] = activeStoryData.pages[targetIdx];
		activeStoryData.pages[targetIdx] = temp;

		currentSlideIndex = targetIdx;
		renderSlideAccordion();
		renderSimulatorProgress();
		updateSimulatorDisplay();
	}

	/**
	 * Brand Design System Presets (Global Brand Tokens)
	 */
	function applyBrandDesignSystem() {
		if (!wstsStudio || !wstsStudio.brandDesignSystem) {
			showNotification(
				'No Brand Preset Saved',
				'You have not saved a Brand Design System yet. Customize your brand colors, fonts, and publisher logo, then click "Save as Brand Preset".'
			);
			return;
		}

		const ds = wstsStudio.brandDesignSystem;
		if (ds.publisher) {
			$('#customPublisher').val(ds.publisher).trigger('input');
		}
		if (ds.publisher_logo) {
			$('#customLogoUrl').val(ds.publisher_logo).trigger('input');
		}
		if (ds.accent_color) {
			$('#customAccentColor').val(ds.accent_color).trigger('input');
			$('#swatchAccentColor').css('background-color', ds.accent_color);
			$('#customAccentHex').val(ds.accent_color.toUpperCase());
		}
		if (ds.secondary_color) {
			$('#customSecondaryColor').val(ds.secondary_color).trigger('input');
			$('#swatchSecondaryColor').css('background-color', ds.secondary_color);
			$('#customSecondaryHex').val(ds.secondary_color.toUpperCase());
		}
		if (ds.bg_color) {
			$('#customBgColor').val(ds.bg_color).trigger('input');
			$('#swatchBgColor').css('background-color', ds.bg_color);
			$('#customBgHex').val(ds.bg_color.toUpperCase());
		}
		if (ds.font_family_title) {
			$('#customTitleFontUnified, #customTitleFont').val(ds.font_family_title).trigger('change');
		}
		if (ds.font_family_body) {
			$('#customBodyFontUnified, #customBodyFont').val(ds.font_family_body).trigger('change');
		}
		if (ds.headline_size) {
			$('#customHeadlineSizeRange').val(ds.headline_size).trigger('input');
		}
		if (ds.body_size) {
			$('#customBodySizeRange').val(ds.body_size).trigger('input');
		}
		if (ds.stat_size) {
			$('#customStatSizeRange').val(ds.stat_size).trigger('input');
		}
		if (ds.headline_color) {
			$('#unifiedHeadingColor').val(ds.headline_color);
			$('#swatchUnifiedHeadingColor').css('background-color', ds.headline_color);
			$('#unifiedHeadingColorHex').val(ds.headline_color.toUpperCase());
			updateUnifiedHeadingColor(ds.headline_color);
		}
		if (ds.badge_bg) {
			$('#unifiedBadgeBg').val(ds.badge_bg);
			$('#swatchUnifiedBadgeBg').css('background-color', ds.badge_bg);
			$('#unifiedBadgeBgHex').val(ds.badge_bg.toUpperCase());
			updateUnifiedBadgeBg(ds.badge_bg);
		}
		if (ds.badge_color) {
			$('#unifiedBadgeColor').val(ds.badge_color);
			$('#swatchUnifiedBadgeColor').css('background-color', ds.badge_color);
			$('#unifiedBadgeColorHex').val(ds.badge_color.toUpperCase());
			updateUnifiedBadgeColor(ds.badge_color);
		}
		if (ds.custom_font_url) {
			loadCustomFontStylesheet(ds.custom_font_url);
			if (activeStoryData) {
				activeStoryData.custom_font_url = ds.custom_font_url;
			}
		}
		if (ds.headline_tracking) {
			$('#customHeadlineTracking').val(ds.headline_tracking).trigger('change');
		}
		if (ds.headline_line_height) {
			$('#customHeadlineLineHeight').val(ds.headline_line_height).trigger('change');
		}
		if (ds.global_scrim) {
			$('#customGlobalScrim').val(ds.global_scrim).trigger('change');
		}

		showNotification(
			'Brand Design System Applied',
			'Your saved typography, optical sizing, brand palette, and publisher identity have been applied to this story.'
		);
	}

	function saveBrandDesignSystem() {
		const $btn = $('#btnSaveBrandDS');
		const originalText = $btn.text();
		$btn.text('Saving...').prop('disabled', true);

		const dsPayload = {
			publisher: $('#customPublisher').val(),
			publisher_logo: $('#customLogoUrl').val(),
			accent_color: $('#customAccentColor').val(),
			secondary_color: $('#customSecondaryColor').val(),
			bg_color: $('#customBgColor').val(),
			headline_color: $('#unifiedHeadingColorHex').val() || (activeStoryData ? activeStoryData.headline_color : '#ffffff'),
			badge_bg: $('#unifiedBadgeBgHex').val() || (activeStoryData ? activeStoryData.badge_bg : '#00f2fe'),
			badge_color: $('#unifiedBadgeColorHex').val() || (activeStoryData ? activeStoryData.badge_color : '#080811'),
			global_scrim: $('#customGlobalScrim').val() || (activeStoryData ? activeStoryData.global_scrim : '') || 'linear-gradient(180deg, rgba(8,8,17,0.3) 0%, rgba(8,8,17,0.92) 80%)',
			custom_font_url: (activeStoryData ? activeStoryData.custom_font_url : '') || $('#customHeadingFontUrl').val() || '',
			font_family_title: $('#customTitleFontUnified').val() || (activeStoryData ? activeStoryData.font_family_title : ''),
			font_family_body: $('#customBodyFontUnified').val() || (activeStoryData ? activeStoryData.font_family_body : ''),
			headline_size: parseInt($('#customHeadlineSizeRange').val(), 10) || (activeStoryData ? activeStoryData.headline_size : 32),
			body_size: parseInt($('#customBodySizeRange').val(), 10) || (activeStoryData ? activeStoryData.body_size : 15),
			stat_size: parseInt($('#customStatSizeRange').val(), 10) || (activeStoryData ? activeStoryData.stat_size : 40),
			headline_tracking: $('#customHeadlineTracking').val() || (activeStoryData ? activeStoryData.headline_tracking : '-0.01em'),
			headline_line_height: $('#customHeadlineLineHeight').val() || (activeStoryData ? activeStoryData.headline_line_height : '1.18')
		};

		$.ajax({
			url: wstsStudio.ajaxUrl,
			type: 'POST',
			data: {
				action: 'wsts_save_design_system',
				nonce: wstsStudio.nonce,
				design_system: JSON.stringify(dsPayload)
			},
			success: function (res) {
				$btn.text(originalText).prop('disabled', false);
				if (res.success) {
					wstsStudio.brandDesignSystem = res.data.design_system;
					$('#brandDsStatusLabel').text('Brand Preset Saved');
					showNotification(
						'Brand Design System Saved',
						res.data.message || 'Brand preset saved. All future stories can inherit these brand tokens with 1 click.'
					);
				} else {
					showNotification('Save Failed', (res.data && res.data.message) ? res.data.message : 'Could not save brand preset.');
				}
			},
			error: function () {
				$btn.text(originalText).prop('disabled', false);
				showNotification('Connection Error', 'Could not communicate with WordPress to save brand design system.');
			}
		});
	}

	/**
	 * Play Live Entrance Animation on Simulator
	 */
	function playSlideAnimation(idx) {
		if (!activeStoryData || !activeStoryData.pages || !activeStoryData.pages[idx]) return;
		const page = activeStoryData.pages[idx];
		let anim = page.animation || 'fly-in-bottom';
		if (anim === 'zoom-in' || anim === 'twirl-in') {
			anim = 'fly-in-bottom';
		}
		const dur = parseFloat(page.anim_duration || 0.7);

		const $content = $('.wsts-sim-content');
		const animClasses = 'wsts-anim-fade-in wsts-anim-fly-in-bottom wsts-anim-fly-in-left wsts-anim-fly-in-right wsts-anim-zoom-in wsts-anim-twirl-in wsts-anim-drop-in wsts-anim-pulse';

		const $targets = $content.find('#simBadge, #simStatBox, #simHeadline, #simBody, #simCtaBox');
		$targets.removeClass(animClasses);

		// Force reflow
		$targets.each(function () {
			void this.offsetWidth;
		});

		$targets.css({
			'--wsts-anim-dur': dur + 's'
		});

		const currentAnimClass = 'wsts-anim-' + anim;
		$('#simBadge').addClass(currentAnimClass).css('animation-delay', '0.02s');
		$('#simStatBox').addClass(currentAnimClass).css('animation-delay', '0.08s');
		$('#simHeadline').addClass(currentAnimClass).css('animation-delay', '0.14s');
		$('#simBody').addClass(currentAnimClass).css('animation-delay', '0.20s');
		$('#simCtaBox').addClass(currentAnimClass).css('animation-delay', '0.26s');
	}

	/**
	 * Smart Stock Image Finder (Unsplash, Pexels & Custom Media Library)
	 */
	let targetImageMode = 'slide'; // 'slide' or 'poster'
	let targetImageSlideIndex = 0;
	let currentImageSource = 'all'; // 'all', 'unsplash', 'pexels', 'custom'
	let currentImageQuery = '';

	function initImageFinder() {
		// Open modal from slide button or poster button
		$(document).on('click', '.wsts-smart-image-btn', function (e) {
			e.preventDefault();
			const target = $(this).data('target');
			if (target === 'poster') {
				targetImageMode = 'poster';
			} else {
				targetImageMode = 'slide';
				targetImageSlideIndex = parseInt($(this).data('index'), 10);
			}
			openImageFinderModal();
		});

		// Instant Auto-Match from slide button
		$(document).on('click', '.wsts-btn-quick-suggest', function (e) {
			e.preventDefault();
			const idx = parseInt($(this).data('index'), 10);
			autoMatchImageForSlide(idx);
		});

		// Close modal
		$('#btnImageFinderClose, #btnImageFinderCancel, #wstsImageFinderModal .wsts-modal-backdrop').on('click', function () {
			$('#wstsImageFinderModal').fadeOut(200);
		});

		// Search button click
		$('#btnSearchImages').on('click', function () {
			currentImageQuery = $('#wstsImageSearchInput').val();
			loadStockImages(currentImageQuery, currentImageSource);
		});

		// Search on enter key
		$('#wstsImageSearchInput').on('keypress', function (e) {
			if (e.which === 13) {
				e.preventDefault();
				$('#btnSearchImages').click();
			}
		});

		// Source filter tabs (All, Unsplash, Pexels, Media)
		$(document).on('click', '.wsts-source-tab', function () {
			$('.wsts-source-tab').removeClass('is-active');
			$(this).addClass('is-active');
			currentImageSource = $(this).data('source') || 'all';
			loadStockImages(currentImageQuery, currentImageSource);
		});

		// Topic pills
		$('.wsts-finder-pill').on('click', function () {
			$(this).addClass('is-active').siblings().removeClass('is-active');
			const query = $(this).data('query');
			currentImageQuery = query === 'all' ? '' : query;
			$('#wstsImageSearchInput').val(currentImageQuery);
			loadStockImages(currentImageQuery, currentImageSource);
		});

		// Auto-find from slide headline button in modal
		$('#btnAutoFindForSlide').on('click', function () {
			let query = 'fashion';
			if (targetImageMode === 'poster') {
				query = activeStoryData.name || activeStoryData.category || 'fashion';
			} else if (activeStoryData && activeStoryData.pages && activeStoryData.pages[targetImageSlideIndex]) {
				const page = activeStoryData.pages[targetImageSlideIndex];
				query = (page.headline || page.badge || activeStoryData.category || 'fashion').toLowerCase();
			}
			currentImageQuery = query;
			$('#wstsImageSearchInput').val(query);
			loadStockImages(query, currentImageSource);
		});

		// Upload Custom Image button in top bar or upload tile
		$(document).on('click', '#btnUploadCustomFromFinder, .wsts-upload-tile', function (e) {
			e.preventDefault();
			openCustomMediaUploader();
		});

		// Select image from grid
		$(document).on('click', '.wsts-finder-card:not(.wsts-upload-tile), .wsts-btn-use-stock', function (e) {
			e.preventDefault();
			const $card = $(this).closest('.wsts-finder-card');
			const url = $card.data('url');
			if (targetImageMode === 'poster') {
				applyImageToPoster(url);
			} else {
				applyImageToSlide(targetImageSlideIndex, url);
			}
			$('#wstsImageFinderModal').fadeOut(200);
		});
	}

	function openCustomMediaUploader() {
		if (typeof wp === 'undefined' || !wp.media) {
			showNotification('Media Library Unavailable', 'WordPress Media Library is not accessible on this screen.');
			return;
		}

		const frame = wp.media({
			title: 'Upload or Select 9:16 Portrait Image',
			button: { text: 'Use Image in Story' },
			multiple: false
		});

		frame.on('select', function () {
			const attachment = frame.state().get('selection').first().toJSON();
			const url = attachment.url;
			if (targetImageMode === 'poster') {
				applyImageToPoster(url);
			} else {
				applyImageToSlide(targetImageSlideIndex, url);
			}
			$('#wstsImageFinderModal').fadeOut(200);
		});

		frame.open();
	}

	function openImageFinderModal() {
		let suggested = '';
		if (targetImageMode === 'poster') {
			suggested = activeStoryData.name || activeStoryData.category || 'all';
		} else if (activeStoryData && activeStoryData.pages && activeStoryData.pages[targetImageSlideIndex]) {
			const page = activeStoryData.pages[targetImageSlideIndex];
			suggested = page.headline ? page.headline : (page.badge || activeStoryData.category);
		}
		currentImageQuery = (suggested && suggested !== 'all') ? suggested : '';
		$('#wstsImageSearchInput').val(currentImageQuery);
		loadStockImages(currentImageQuery, currentImageSource);
		$('#wstsImageFinderModal').fadeIn(200);
	}

	function loadStockImages(query, source) {
		const $grid = $('#wstsImageResultsGrid');
		source = source || currentImageSource || 'all';
		const sourceLabel = source === 'custom' ? 'your media library' : (source === 'all' ? 'Unsplash & Pexels' : source.toUpperCase());
		$grid.html('<div class="wsts-finder-loading">Loading 9:16 high-res photos from ' + sourceLabel + '...</div>');

		$.ajax({
			url: wstsStudio.ajaxUrl,
			type: 'POST',
			data: {
				action: 'wsts_search_images',
				nonce: wstsStudio.nonce,
				query: query || '',
				source: source
			},
			success: function (res) {
				if (res.success && res.data.images && res.data.images.length) {
					renderImageGrid(res.data.images, source);
				} else {
					if (source === 'custom') {
						renderImageGrid([], 'custom');
					} else {
						$grid.html('<div class="wsts-finder-empty">No photos found for "' + (query || '') + '" in ' + sourceLabel + '. Try searching "fashion", "luxury", "tech", or "travel", or click "Upload Custom Image".</div>');
					}
				}
			},
			error: function () {
				$grid.html('<div class="wsts-finder-empty">Could not load images. Please check connection.</div>');
			}
		});
	}

	function renderImageGrid(images, currentSource) {
		const $grid = $('#wstsImageResultsGrid');
		$grid.empty();

		// If viewing My Media Library, always prepend the "Upload New Custom Image" tile
		if (currentSource === 'custom') {
			const $uploadTile = $('<div>', {
				class: 'wsts-finder-card wsts-upload-tile',
				title: 'Click to upload a custom image from your device'
			});
			$uploadTile.append($('<div>', { class: 'wsts-upload-tile-icon' }).text('+'));
			$uploadTile.append($('<strong>').text('Upload New Image'));
			$uploadTile.append($('<span>').text('JPEG, PNG, WebP (9:16 portrait)'));
			$grid.append($uploadTile);
		}

		if (!images || !images.length) {
			if (currentSource === 'custom') {
				// We already appended the upload tile
				return;
			}
			$grid.html('<div class="wsts-finder-empty">No photos found. Try another search or click "Upload Custom Image".</div>');
			return;
		}

		images.forEach(function (img) {
			const $card = $('<div>', {
				class: 'wsts-finder-card',
				'data-url': img.url,
				title: 'Click to select ' + (img.title || 'photo')
			});

			$card.append($('<img>', {
				src: img.thumb || img.url,
				alt: img.title || 'Story photo',
				class: 'wsts-finder-img',
				loading: 'lazy'
			}));

			const $overlay = $('<div>', { class: 'wsts-finder-overlay' });

			// Top: Source badge (Unsplash / Pexels / Media)
			const srcType = img.source || 'unsplash';
			const srcLabel = img.source_label || (srcType === 'pexels' ? 'Pexels' : (srcType === 'custom' ? 'Media' : 'Unsplash'));
			$overlay.append($('<span>', {
				class: 'wsts-source-badge is-' + srcType
			}).text(srcLabel));

			// Bottom: Meta Box & Button
			const $metaBox = $('<div>', { class: 'wsts-finder-card-meta' });
			if (img.title) {
				$metaBox.append($('<strong>', { class: 'wsts-finder-title' }).text(img.title));
			}
			if (img.author) {
				$metaBox.append($('<small>', { class: 'wsts-finder-author' }).text('Photo by ' + img.author));
			}

			const btnText = targetImageMode === 'poster' ? 'Use as Story Cover' : 'Use as Background';
			$metaBox.append($('<button>', {
				type: 'button',
				class: 'button button-primary wsts-btn-use-stock'
			}).text(btnText));

			$overlay.append($metaBox);
			$card.append($overlay);
			$grid.append($card);
		});
	}

	function applyImageToPoster(imgUrl) {
		if (!activeStoryData) return;
		activeStoryData.poster_image = imgUrl;
		$('#customPosterImage').val(imgUrl).trigger('input');
		updateSimulatorDisplay();
		showNotification(
			'Story Cover Updated',
			'New high-resolution portrait cover image applied to your Web Story.'
		);
	}

	function applyImageToSlide(slideIdx, imgUrl) {
		if (!activeStoryData || !activeStoryData.pages || !activeStoryData.pages[slideIdx]) return;
		activeStoryData.pages[slideIdx].bg_image = imgUrl;

		// Update input
		$('.slide-bg-input[data-index="' + slideIdx + '"]').val(imgUrl);

		if (slideIdx === currentSlideIndex) {
			updateSimulatorDisplay();
		}

		showNotification(
			'Background Image Updated',
			'New 9:16 high-resolution portrait image applied to Slide ' + (slideIdx + 1) + '.'
		);
	}

	function autoMatchImageForSlide(slideIdx) {
		if (!activeStoryData || !activeStoryData.pages || !activeStoryData.pages[slideIdx]) return;
		const page = activeStoryData.pages[slideIdx];
		const query = page.headline || page.badge || activeStoryData.category;

		$.ajax({
			url: wstsStudio.ajaxUrl,
			type: 'POST',
			data: {
				action: 'wsts_search_images',
				nonce: wstsStudio.nonce,
				query: query
			},
			success: function (res) {
				if (res.success && res.data.images && res.data.images.length) {
					const bestImg = res.data.images[0];
					applyImageToSlide(slideIdx, bestImg.url);
				}
			}
		});
	}

	/**
	 * 1-Click Blog Post Auto-Story Generator
	 */
	function initAutoPostModal() {
		$('#btnOpenAutoPostModal').on('click', function (e) {
			e.preventDefault();
			$('#autoPostStatus').hide();
			$('#btnAutoPostGenerate').prop('disabled', false).text('✨ Generate 4-Slide Story');
			$('#wstsAutoPostModal').fadeIn(200);
		});

		$('#btnAutoPostModalClose, #btnAutoPostCancel, #wstsAutoPostModal .wsts-modal-backdrop').on('click', function () {
			$('#wstsAutoPostModal').fadeOut(180);
		});

		$('#btnAutoPostGenerate').on('click', function (e) {
			e.preventDefault();
			const postId = $('#autoPostSelect').val();
			if (!postId) {
				showNotification('Select a Blog Post', 'Please choose a published WordPress blog post from the dropdown list to convert.');
				return;
			}

			const categorySlug = $('#autoPostCategory').val() || 'tech';
			const $btn = $(this);
			$btn.prop('disabled', true).text('Analyzing Post...');
			$('#autoPostStatus').show();
			$('#autoPostStatusText').text('Analyzing post, extracting takeaways & matching photography...');

			$.ajax({
				url: wstsStudio.ajaxUrl,
				type: 'POST',
				data: {
					action: 'wsts_auto_generate_from_post',
					nonce: wstsStudio.nonce,
					post_id: postId,
					category_slug: categorySlug
				},
				success: function (res) {
					$btn.prop('disabled', false).text('✨ Generate 4-Slide Story');
					$('#autoPostStatus').hide();
					if (res.success && res.data.story) {
						$('#wstsAutoPostModal').fadeOut(180);
						openCustomizerWithData(res.data.story);
						showNotification(
							'4-Slide Story Generated',
							'Your blog post has been successfully distilled into an AMP Web Story with matched photography, key takeaways, and outlinks!'
						);
					} else {
						showNotification('Auto-Generation Error', (res.data && res.data.message) ? res.data.message : 'Could not generate story from selected post.');
					}
				},
				error: function () {
					$btn.prop('disabled', false).text('✨ Generate 4-Slide Story');
					$('#autoPostStatus').hide();
					showNotification('Connection Error', 'Could not reach WordPress AJAX endpoint. Please try again.');
				}
			});
		});
	}

	/**
	 * Background Audio Studio
	 */
	let audioStudioPlayer = null;

	function initAudioStudio() {
		// Preset select listener
		$('#customAudioPreset').on('change', function () {
			const val = $(this).val();
			if (val === 'custom') {
				$('#customBackgroundAudio').focus();
			} else {
				$('#customBackgroundAudio').val(val).trigger('input');
			}
			if (audioStudioPlayer) {
				audioStudioPlayer.pause();
				audioStudioPlayer = null;
				$('#btnPreviewAudio').removeClass('is-playing').text('▶ Test Play');
			}
		});

		// Preview play / pause button
		$('#btnPreviewAudio').on('click', function (e) {
			e.preventDefault();
			const audioUrl = $('#customBackgroundAudio').val();
			if (!audioUrl) {
				showNotification('No Audio URL', 'Please select a royalty-free music preset or enter a direct MP3 URL first.');
				return;
			}

			if (audioStudioPlayer && !audioStudioPlayer.paused) {
				audioStudioPlayer.pause();
				audioStudioPlayer = null;
				$(this).removeClass('is-playing').text('▶ Test Play');
			} else {
				try {
					if (audioStudioPlayer) {
						audioStudioPlayer.pause();
					}
					audioStudioPlayer = new Audio(audioUrl);
					const $btn = $(this);
					audioStudioPlayer.play().then(function () {
						$btn.addClass('is-playing').text('⏹ Pause Test');
					}).catch(function () {
						showNotification('Playback Notice', 'Could not stream preview track directly in browser. The URL will still be compiled into the Web Story.');
						$btn.removeClass('is-playing').text('▶ Test Play');
					});
					audioStudioPlayer.onended = function () {
						$('#btnPreviewAudio').removeClass('is-playing').text('▶ Test Play');
					};
				} catch (err) {
					$('#btnPreviewAudio').removeClass('is-playing').text('▶ Test Play');
				}
			}
		});

		// Stop audio when customizer modal closes
		$('#btnModalClose, #wstsCustomizerModal .wsts-modal-backdrop').on('click', function () {
			if (audioStudioPlayer) {
				audioStudioPlayer.pause();
				audioStudioPlayer = null;
				$('#btnPreviewAudio').removeClass('is-playing').text('▶ Test Play');
			}
		});
	}

	/**
	 * Social Story Slices Exporter (1080x1920 PNG Downloads)
	 */
	function initSocialSlicesExporter() {
		$('#btnExportSocialSlices, #btnPreviewExportSocialSlices').on('click', function (e) {
			e.preventDefault();
			if (!activeStoryData || !activeStoryData.pages || !activeStoryData.pages.length) {
				showNotification('No Slides', 'Please open a story template with slides before exporting.');
				return;
			}

			syncAllFormInputs();
			exportAllSocialSlices();
		});
	}

	function exportAllSocialSlices() {
		const pages = activeStoryData.pages;
		const total = pages.length;
		const baseSlug = (activeStoryData.title || 'story')
			.toLowerCase()
			.replace(/[^a-z0-9]+/g, '-')
			.replace(/(^-|-$)/g, '');

		showNotification(
			'Exporting Social Slices',
			'Generating ' + total + ' high-resolution 1080×1920 portrait PNG images for Instagram Stories, TikTok & WhatsApp...'
		);

		let exportIdx = 0;

		function processNextSlide() {
			if (exportIdx >= total) {
				showNotification(
					'Social Slices Downloaded',
					'All ' + total + ' slides have been rendered and downloaded as high-resolution 1080×1920 PNGs!'
				);
				return;
			}

			const slide = pages[exportIdx];
			const currentIdx = exportIdx;
			exportIdx++;

			renderSlideToCanvas(slide, function (blob) {
				if (blob) {
					const url = URL.createObjectURL(blob);
					const a = document.createElement('a');
					a.href = url;
					a.download = baseSlug + '-slide-' + (currentIdx + 1) + '.png';
					document.body.appendChild(a);
					a.click();
					setTimeout(function () {
						document.body.removeChild(a);
						URL.revokeObjectURL(url);
					}, 100);
				}
				setTimeout(processNextSlide, 350);
			});
		}

		processNextSlide();
	}

	function renderSlideToCanvas(slide, callback) {
		const canvas = document.createElement('canvas');
		canvas.width = 1080;
		canvas.height = 1920;
		const ctx = canvas.getContext('2d');

		// Background fill
		ctx.fillStyle = activeStoryData.bg_color || '#080811';
		ctx.fillRect(0, 0, 1080, 1920);

		const bgImg = new Image();
		bgImg.crossOrigin = 'anonymous';

		function drawSlideContent() {
			// Scrim Overlay
			const scrimGrad = ctx.createLinearGradient(0, 0, 0, 1920);
			scrimGrad.addColorStop(0, 'rgba(8, 8, 17, 0.2)');
			scrimGrad.addColorStop(0.35, 'rgba(8, 8, 17, 0.4)');
			scrimGrad.addColorStop(0.7, 'rgba(8, 8, 17, 0.88)');
			scrimGrad.addColorStop(1, 'rgba(8, 8, 17, 0.98)');
			ctx.fillStyle = scrimGrad;
			ctx.fillRect(0, 0, 1080, 1920);

			// Top Publisher Monogram & Name
			const pubName = activeStoryData.publisher || 'Web Stories';
			ctx.fillStyle = '#ffffff';
			ctx.font = '700 28px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
			ctx.fillText(pubName, 80, 130);

			// Bottom Safe Area Calculations (Google 250px bottom clearance)
			let curY = 1180;

			// Badge Pill
			if (slide.badge) {
				const badgeBg = slide.badge_bg || activeStoryData.badge_bg || '#00f2fe';
				const badgeColor = slide.badge_color || '#ffffff';
				ctx.font = '700 24px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
				const bText = (slide.badge || '').toUpperCase();
				const bWidth = ctx.measureText(bText).width + 36;
				ctx.fillStyle = badgeBg;
				roundRect(ctx, 80, curY, bWidth, 48, 24);
				ctx.fill();

				ctx.fillStyle = badgeColor;
				ctx.fillText(bText, 98, curY + 33);
				curY += 75;
			}

			// Stat Metric (if present)
			if (slide.stat_number) {
				const statColor = slide.stat_color || activeStoryData.accent_color || '#00f2fe';
				ctx.font = '900 86px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
				ctx.fillStyle = statColor;
				ctx.fillText(slide.stat_number, 80, curY + 70);

				if (slide.stat_label) {
					ctx.font = '600 26px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
					ctx.fillStyle = 'rgba(255, 255, 255, 0.8)';
					ctx.fillText(slide.stat_label.toUpperCase(), 80, curY + 110);
					curY += 130;
				} else {
					curY += 95;
				}
			}

			// Headline
			if (slide.headline) {
				const headColor = slide.headline_color || activeStoryData.headline_color || '#ffffff';
				ctx.font = '800 68px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
				ctx.fillStyle = headColor;
				curY = drawWrappedText(ctx, slide.headline, 80, curY + 50, 920, 82);
				curY += 24;
			}

			// Body Copy
			if (slide.body) {
				const bodyColor = slide.body_color || 'rgba(255, 255, 255, 0.9)';
				ctx.font = '400 36px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
				ctx.fillStyle = bodyColor;
				curY = drawWrappedText(ctx, slide.body, 80, curY + 30, 920, 48);
			}

			// CTA Outlink Pill
			if (slide.cta_text) {
				const btnY = 1730;
				const ctaBg = activeStoryData.accent_color || '#00f2fe';
				ctx.font = '700 32px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
				const cText = slide.cta_text + ' \u2192';
				const cWidth = ctx.measureText(cText).width + 60;
				ctx.fillStyle = ctaBg;
				roundRect(ctx, 80, btnY, cWidth, 72, 36);
				ctx.fill();

				ctx.fillStyle = '#080811';
				ctx.fillText(cText, 110, btnY + 48);
			}

			canvas.toBlob(function (blob) {
				callback(blob);
			}, 'image/png');
		}

		if (slide.bg_image) {
			bgImg.onload = function () {
				drawCoverImage(ctx, bgImg, 0, 0, 1080, 1920);
				drawSlideContent();
			};
			bgImg.onerror = function () {
				drawSlideContent();
			};
			bgImg.src = slide.bg_image;
		} else {
			drawSlideContent();
		}
	}

	function drawCoverImage(ctx, img, x, y, w, h) {
		const imgRatio = img.width / img.height;
		const canvasRatio = w / h;
		let sWidth, sHeight, sx, sy;

		if (imgRatio > canvasRatio) {
			sHeight = img.height;
			sWidth = img.height * canvasRatio;
			sx = (img.width - sWidth) / 2;
			sy = 0;
		} else {
			sWidth = img.width;
			sHeight = img.width / canvasRatio;
			sx = 0;
			sy = (img.height - sHeight) / 2;
		}

		ctx.drawImage(img, sx, sy, sWidth, sHeight, x, y, w, h);
	}

	function drawWrappedText(ctx, text, x, y, maxWidth, lineHeight) {
		const words = (text || '').split(' ');
		let line = '';
		let curY = y;

		for (let n = 0; n < words.length; n++) {
			const testLine = line + words[n] + ' ';
			const metrics = ctx.measureText(testLine);
			const testWidth = metrics.width;
			if (testWidth > maxWidth && n > 0) {
				ctx.fillText(line.trim(), x, curY);
				line = words[n] + ' ';
				curY += lineHeight;
			} else {
				line = testLine;
			}
		}
		ctx.fillText(line.trim(), x, curY);
		return curY + lineHeight;
	}

	function roundRect(ctx, x, y, width, height, radius) {
		ctx.beginPath();
		ctx.moveTo(x + radius, y);
		ctx.lineTo(x + width - radius, y);
		ctx.quadraticCurveTo(x + width, y, x + width, y + radius);
		ctx.lineTo(x + width, y + height - radius);
		ctx.quadraticCurveTo(x + width, y + height, x + width - radius, y + height);
		ctx.lineTo(x + radius, y + height);
		ctx.quadraticCurveTo(x, y + height, x, y + height - radius);
		ctx.lineTo(x, y + radius);
		ctx.quadraticCurveTo(x, y, x + radius, y);
		ctx.closePath();
	}

})(jQuery);

