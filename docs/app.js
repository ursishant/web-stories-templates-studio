/**
 * Web Stories Templates Studio - Interactive Showcase Engine
 */

document.addEventListener('DOMContentLoaded', () => {
  // 1. Template Presets Database
  const TEMPLATES = {
    tech: {
      name: "Quantum Leap Silicon",
      tag: "DEEP TECH // 2026",
      accent: "#00f2fe",
      secondary: "#3b82f6",
      font: "'Space Grotesk', sans-serif",
      publisher: "Silicon Frontier Media",
      slides: [
        {
          bg: "https://images.unsplash.com/photo-1629654297299-c8506221ca97?w=600&h=1000&fit=crop&q=80",
          headline: "Sub-1nm Quantum Processors Now Reality",
          body: "Researchers have achieved room-temperature quantum coherence on silicon lattices, paving the way for 100x efficiency gains.",
          cta: "Explore Architecture"
        },
        {
          bg: "https://images.unsplash.com/photo-1518770660439-4636190af475?w=600&h=1000&fit=crop&q=80",
          headline: "Thermal Dissipation at 0.05 Kelvin",
          body: "Cryogenic vacuum chambers eliminate phonon scattering, achieving zero-resistance logic operations.",
          cta: "View Thermal Data"
        },
        {
          bg: "https://images.unsplash.com/photo-1526374965328-7f61d4dc18c5?w=600&h=1000&fit=crop&q=80",
          headline: "Quantum Key Cryptography Standardized",
          body: "New post-quantum lattice encryption algorithms deployed globally across financial networks.",
          cta: "Inspect Security Spec"
        },
        {
          bg: "https://images.unsplash.com/photo-1504384308090-c894fdcc538d?w=600&h=1000&fit=crop&q=80",
          headline: "The Commercial Roadmap Ahead",
          body: "Early access hardware devkits shipping to enterprise research institutions next quarter.",
          cta: "Join Waitlist &rarr;"
        }
      ]
    },
    fashion: {
      name: "Haute Couture Atelier",
      tag: "HAUTE COUTURE // PARIS",
      accent: "#d4af37",
      secondary: "#fef3c7",
      font: "'Playfair Display', serif",
      publisher: "Vogue Gazette",
      slides: [
        {
          bg: "https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?w=600&h=1000&fit=crop&q=80",
          headline: "Paris Fashion Week Runway Highlights",
          body: "Architectural draping, ethereal organza, and sustainable raw silk dominate this season's silhouettes.",
          cta: "View Collection"
        },
        {
          bg: "https://images.unsplash.com/photo-1509631179647-0177331693ae?w=600&h=1000&fit=crop&q=80",
          headline: "Monochrome Tailoring & Sculpted Capes",
          body: "Severe tailoring meets fluid movement in nocturnal palettes of obsidian and chalk ivory.",
          cta: "Explore Silhouettes"
        },
        {
          bg: "https://images.unsplash.com/photo-1490481651871-ab68de25d43d?w=600&h=1000&fit=crop&q=80",
          headline: "Hand-Stitched Micro-Bead Embellishments",
          body: "Over 800 hours of artisanal hand-embroidery on sheer metallic mesh panels.",
          cta: "Watch Atelier Film"
        },
        {
          bg: "https://images.unsplash.com/photo-1469334031218-e382a71b716b?w=600&h=1000&fit=crop&q=80",
          headline: "The Autumn Runway Full Showcase",
          body: "Discover the complete lookbook and preorder exclusive limited runway editions.",
          cta: "Order Runway Edition &rarr;"
        }
      ]
    },
    viral: {
      name: "Creator Pulse Radar",
      tag: "VIRAL HOOK // RETENTION",
      accent: "#ec4899",
      secondary: "#8b5cf6",
      font: "'Montserrat', sans-serif",
      publisher: "Creator Lab",
      slides: [
        {
          bg: "https://images.unsplash.com/photo-1542751371-adc38448a05e?w=600&h=1000&fit=crop&q=80",
          headline: "How 2026 Algorithms Rank Stories",
          body: "Data from 100M views reveals retention curves skyrocket after 3-second visual shifts.",
          cta: "Read Case Study"
        },
        {
          bg: "https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=600&h=1000&fit=crop&q=80",
          headline: "The High-Retention 3-Second Hook",
          body: "Lead with an open-loop premise and dynamic foreground motion to arrest viewer scroll.",
          cta: "See Hook Formulas"
        },
        {
          bg: "https://images.unsplash.com/photo-1517841905240-472988babdf9?w=600&h=1000&fit=crop&q=80",
          headline: "Optical Pacing & Kinetic Badges",
          body: "Short, punchy captions under 280 characters prevent cognitive fatigue.",
          cta: "Inspect Retention Stats"
        },
        {
          bg: "https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?w=600&h=1000&fit=crop&q=80",
          headline: "Unlock The Full Story Blueprint",
          body: "Download the complete 2026 Viral Story System cheat sheet and start publishing.",
          cta: "Download Cheat Sheet &rarr;"
        }
      ]
    },
    food: {
      name: "Artisanal Botanical Bistro",
      tag: "BAKING MASTERCLASS",
      accent: "#f59e0b",
      secondary: "#10b981",
      font: "'Plus Jakarta Sans', sans-serif",
      publisher: "Gourmet Table",
      slides: [
        {
          bg: "https://images.unsplash.com/photo-1540420773420-3366772f4999?w=600&h=1000&fit=crop&q=80",
          headline: "The Sourdough Fermentation Formula",
          body: "Mastering wild hydration kinetics and stone-milled heirloom wheat crumb structures.",
          cta: "Get Recipe"
        },
        {
          bg: "https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=600&h=1000&fit=crop&q=80",
          headline: "85% High Hydration Dough Folding",
          body: "Coil folds every 30 minutes build gluten elasticity without degasification.",
          cta: "View Technique"
        },
        {
          bg: "https://images.unsplash.com/photo-1509440159596-0249088772ff?w=600&h=1000&fit=crop&q=80",
          headline: "Dutch Oven Steam Expansion",
          body: "Trapped water vapor superheats crust gelatinization, creating blistering blisters.",
          cta: "Bake Instructions"
        },
        {
          bg: "https://images.unsplash.com/photo-1514933651103-005eec06c04b?w=600&h=1000&fit=crop&q=80",
          headline: "Artisan Loaf Slice & Butter Pairing",
          body: "Pair with sea salt flakes and churned grass-fed cultured butter.",
          cta: "Explore Menu &rarr;"
        }
      ]
    },
    fitness: {
      name: "BioFit Peak Horizon",
      tag: "BIOFIT ROUTINE // METABOLIC",
      accent: "#10b981",
      secondary: "#06b6d4",
      font: "'Inter', sans-serif",
      publisher: "BioFit Athletic",
      slides: [
        {
          bg: "https://images.unsplash.com/photo-1517838277536-f5f99be501cd?w=600&h=1000&fit=crop&q=80",
          headline: "Zone 2 Metabolic Conditioning Protocol",
          body: "Optimizing mitochondrial density and endurance thresholds with low-lactate pacing.",
          cta: "Start Workout"
        },
        {
          bg: "https://images.unsplash.com/photo-1534438327276-14e5300c3a48?w=600&h=1000&fit=crop&q=80",
          headline: "Heart Rate Variability & Recovery",
          body: "Maintain strict 65-75% max HR to stimulate aerobic fat oxidation pathways.",
          cta: "View HR Zones"
        },
        {
          bg: "https://images.unsplash.com/photo-1541534741688-6078c6bfb5c5?w=600&h=1000&fit=crop&q=80",
          headline: "Electrolyte Replenishment Ratios",
          body: "Sodium-potassium-magnesium hydration stacks calibrated for 90-minute sustained output.",
          cta: "Nutrition Stack"
        },
        {
          bg: "https://images.unsplash.com/photo-1518611012118-696072aa579a?w=600&h=1000&fit=crop&q=80",
          headline: "Weekly Progressive Overload Log",
          body: "Track cardiac drift trends and lactate clearance week-over-week.",
          cta: "Log Session &rarr;"
        }
      ]
    },
    travel: {
      name: "Wanderlust Expedition",
      tag: "EXPEDITION // PATAGONIA",
      accent: "#38bdf8",
      secondary: "#818cf8",
      font: "'Space Grotesk', sans-serif",
      publisher: "Wanderer Mag",
      slides: [
        {
          bg: "https://images.unsplash.com/photo-1506744038136-46273834b3fb?w=600&h=1000&fit=crop&q=80",
          headline: "The Hidden Glacial Fjords of Patagonia",
          body: "48 hours traversing uncharted ice sheets and sub-polar archipelago coastlines.",
          cta: "Explore Itinerary"
        },
        {
          bg: "https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?w=600&h=1000&fit=crop&q=80",
          headline: "Glacier Grey Kayak Navigation",
          body: "Paddle among icebergs calved from the Southern Patagonian Ice Field.",
          cta: "Route Coordinates"
        },
        {
          bg: "https://images.unsplash.com/photo-1470071459604-3b5ec3a7fe05?w=600&h=1000&fit=crop&q=80",
          headline: "Wilderness Bivouac Under The Stars",
          body: "Night skies with zero light pollution above jagged granite spires.",
          cta: "Camp Guide"
        },
        {
          bg: "https://images.unsplash.com/photo-1426604966848-d7adac402bff?w=600&h=1000&fit=crop&q=80",
          headline: "Complete Expedition Equipment Checklist",
          body: "Crampons, four-season alpine bivvy, and satellite GPS beacons.",
          cta: "Download Checklist &rarr;"
        }
      ]
    }
  };

  // State
  let currentKey = 'tech';
  let currentSlideIndex = 0;
  let currentFont = "'Space Grotesk', sans-serif";
  let currentAccent = "#00f2fe";

  // Elements
  const phoneScreen = document.getElementById('phoneScreen');
  const storyBadge = document.getElementById('storyBadge');
  const storyHeading = document.getElementById('storyHeading');
  const storyBody = document.getElementById('storyBody');
  const storyCta = document.getElementById('storyCta');
  const pubName = document.getElementById('pubName');
  const pubAvatar = document.getElementById('pubAvatar');
  const safeZoneOverlay = document.getElementById('safeZoneOverlay');
  const safeZoneToggle = document.getElementById('safeZoneToggle');
  const progressBars = document.querySelectorAll('.progress-segment');

  function updateStorySlide() {
    const tpl = TEMPLATES[currentKey];
    const slide = tpl.slides[currentSlideIndex];

    // Background Image
    phoneScreen.style.backgroundImage = `url('${slide.bg}')`;

    // Content
    storyBadge.textContent = tpl.tag;
    storyBadge.style.backgroundColor = currentAccent;
    storyHeading.textContent = slide.headline;
    storyHeading.style.fontFamily = currentFont;
    storyBody.textContent = slide.body;
    storyCta.innerHTML = `<span>${slide.cta}</span><span>&rarr;</span>`;
    pubName.textContent = tpl.publisher;
    pubAvatar.textContent = tpl.publisher.slice(0, 2).toUpperCase();
    pubAvatar.style.backgroundColor = currentAccent;

    // Progress Bars
    progressBars.forEach((seg, idx) => {
      seg.classList.remove('completed', 'active');
      const fill = seg.querySelector('.progress-fill');
      if (idx < currentSlideIndex) {
        seg.classList.add('completed');
        fill.style.width = '100%';
      } else if (idx === currentSlideIndex) {
        seg.classList.add('active');
        fill.style.width = '100%';
      } else {
        fill.style.width = '0%';
      }
    });
  }

  // Next / Prev Slide Handlers
  function nextSlide() {
    const tpl = TEMPLATES[currentKey];
    currentSlideIndex = (currentSlideIndex + 1) % tpl.slides.length;
    updateStorySlide();
  }

  function prevSlide() {
    const tpl = TEMPLATES[currentKey];
    currentSlideIndex = (currentSlideIndex - 1 + tpl.slides.length) % tpl.slides.length;
    updateStorySlide();
  }

  // Tap on phone to navigate
  phoneScreen.addEventListener('click', (e) => {
    const rect = phoneScreen.getBoundingClientRect();
    const clickX = e.clientX - rect.left;
    if (clickX > rect.width * 0.4) {
      nextSlide();
    } else {
      prevSlide();
    }
  });

  // Safe Zone Toggle
  if (safeZoneToggle) {
    safeZoneToggle.addEventListener('change', (e) => {
      if (e.target.checked) {
        safeZoneOverlay.classList.add('is-visible');
      } else {
        safeZoneOverlay.classList.remove('is-visible');
      }
    });
  }

  // Template Buttons
  const tplButtons = document.querySelectorAll('.tpl-btn');
  tplButtons.forEach(btn => {
    btn.addEventListener('click', () => {
      tplButtons.forEach(b => b.classList.remove('is-active'));
      btn.classList.add('is-active');

      const tplId = btn.getAttribute('data-tpl');
      if (TEMPLATES[tplId]) {
        currentKey = tplId;
        currentSlideIndex = 0;
        currentAccent = TEMPLATES[tplId].accent;
        currentFont = TEMPLATES[tplId].font;
        updateStorySlide();
      }
    });
  });

  // Font Buttons
  const fontButtons = document.querySelectorAll('.font-btn');
  fontButtons.forEach(btn => {
    btn.addEventListener('click', () => {
      fontButtons.forEach(b => b.classList.remove('is-active'));
      btn.classList.add('is-active');
      currentFont = btn.getAttribute('data-font');
      storyHeading.style.fontFamily = currentFont;
    });
  });

  // Color Swatch Buttons
  const colorButtons = document.querySelectorAll('.color-dot-btn');
  colorButtons.forEach(btn => {
    btn.addEventListener('click', () => {
      colorButtons.forEach(b => b.classList.remove('is-active'));
      btn.classList.add('is-active');
      currentAccent = btn.getAttribute('data-color');
      storyBadge.style.backgroundColor = currentAccent;
      pubAvatar.style.backgroundColor = currentAccent;
    });
  });

  // 2. Catalog Search & Filter
  const catalogSearch = document.getElementById('catalogSearch');
  const filterChips = document.querySelectorAll('.filter-chip');
  const templateCards = document.querySelectorAll('.template-card');

  function filterCatalog() {
    const searchTerm = (catalogSearch ? catalogSearch.value : '').toLowerCase().trim();
    const activeChip = document.querySelector('.filter-chip.is-active');
    const selectedCategory = activeChip ? activeChip.getAttribute('data-cat') : 'all';

    templateCards.forEach(card => {
      const title = card.querySelector('.card-title').textContent.toLowerCase();
      const tag = card.querySelector('.card-tag').textContent.toLowerCase();
      const cat = card.getAttribute('data-cat') || '';

      const matchesSearch = !searchTerm || title.includes(searchTerm) || tag.includes(searchTerm);
      const matchesCategory = (selectedCategory === 'all') || (cat === selectedCategory);

      if (matchesSearch && matchesCategory) {
        card.style.display = 'flex';
      } else {
        card.style.display = 'none';
      }
    });
  }

  if (catalogSearch) {
    catalogSearch.addEventListener('input', filterCatalog);
  }

  filterChips.forEach(chip => {
    chip.addEventListener('click', () => {
      filterChips.forEach(c => c.classList.remove('is-active'));
      chip.classList.add('is-active');
      filterCatalog();
    });
  });

  // Favorite Star Toggles
  const favStars = document.querySelectorAll('.card-fav-star');
  favStars.forEach(star => {
    star.addEventListener('click', (e) => {
      e.stopPropagation();
      star.classList.toggle('is-fav');
      const svg = star.querySelector('svg');
      if (star.classList.contains('is-fav')) {
        svg.setAttribute('fill', '#f59e0b');
        svg.setAttribute('stroke', '#f59e0b');
      } else {
        svg.setAttribute('fill', 'none');
        svg.setAttribute('stroke', 'currentColor');
      }
    });
  });

  // "Use in Simulator" Card Action Buttons
  const cardActions = document.querySelectorAll('.card-action-btn');
  cardActions.forEach(btn => {
    btn.addEventListener('click', () => {
      const card = btn.closest('.template-card');
      const targetTpl = card.getAttribute('data-target-tpl');
      if (targetTpl && TEMPLATES[targetTpl]) {
        currentKey = targetTpl;
        currentSlideIndex = 0;
        currentAccent = TEMPLATES[targetTpl].accent;
        currentFont = TEMPLATES[targetTpl].font;

        // Sync buttons
        tplButtons.forEach(b => {
          if (b.getAttribute('data-tpl') === targetTpl) {
            b.classList.add('is-active');
          } else {
            b.classList.remove('is-active');
          }
        });

        updateStorySlide();

        // Smooth scroll to simulator
        const simSection = document.getElementById('live-simulator');
        if (simSection) {
          simSection.scrollIntoView({ behavior: 'smooth' });
        }
      }
    });
  });

  // 3. Copy CLI Command Button
  const btnCopyCli = document.getElementById('btnCopyCli');
  if (btnCopyCli) {
    btnCopyCli.addEventListener('click', () => {
      const code = document.getElementById('cliCommandText').textContent.trim();
      navigator.clipboard.writeText(code).then(() => {
        const originalText = btnCopyCli.textContent;
        btnCopyCli.textContent = "✓ Copied!";
        btnCopyCli.style.background = "#22c55e";
        btnCopyCli.style.color = "#ffffff";
        setTimeout(() => {
          btnCopyCli.textContent = originalText;
          btnCopyCli.style.background = "";
          btnCopyCli.style.color = "";
        }, 2000);
      });
    });
  }

  // Initial render
  updateStorySlide();
});
