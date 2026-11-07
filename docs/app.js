/**
 * Web Stories Templates Studio | Apple-Inspired Interactive Engine
 * Seamless Archetype Switching, Microinteractions, Real-Time Safe Zone Calipers
 */

document.addEventListener('DOMContentLoaded', () => {
  // 1. Curated Archetypes & Story Slides Database
  const ARCHETYPES = {
    tech: {
      title: "Sub-Atomic Computing Horizons",
      subtitle: "SPECIFICATION // TECH & SILICON",
      desc: "Engineered with high-contrast obsidian backgrounds, electric blue accents, and zero text crowding. Built specifically for technology publishers breaking complex hardware narratives.",
      color: "#0071e3",
      fontName: "32px Space Grotesk",
      tag: "DEEP TECH // 2026",
      publisher: "Silicon Frontier Media",
      monogram: "SF",
      font: "'Space Grotesk', sans-serif",
      slides: [
        {
          bg: "https://images.unsplash.com/photo-1629654297299-c8506221ca97?w=600&h=1000&fit=crop&q=80",
          headline: "Sub-1nm Quantum Processors Now Reality",
          body: "Researchers have achieved room-temperature quantum coherence on silicon lattices, unlocking 100x efficiency gains.",
          cta: "Explore Architecture"
        },
        {
          bg: "https://images.unsplash.com/photo-1518770660439-4636190af475?w=600&h=1000&fit=crop&q=80",
          headline: "Cryogenic Vacuum Chambers at 0.05 Kelvin",
          body: "Zero phonon scattering eliminates thermal dissipation, delivering lossless logic routing for neural networks.",
          cta: "Inspect Thermal Spec"
        },
        {
          bg: "https://images.unsplash.com/photo-1526374965328-7f61d4dc18c5?w=600&h=1000&fit=crop&q=80",
          headline: "Lattice Encryption Deployed Globally",
          body: "Post-quantum cryptographic defense matrices successfully protect global inter-bank payment nodes.",
          cta: "View Security Audit"
        },
        {
          bg: "https://images.unsplash.com/photo-1504384308090-c894fdcc538d?w=600&h=1000&fit=crop&q=80",
          headline: "Silicon Roadmaps: 2026 to 2030",
          body: "Next-generation optical photon interconnects replace copper traces for exascale computing clusters.",
          cta: "Download Roadmap &rarr;"
        }
      ]
    },
    fashion: {
      title: "Paris Haute Couture Atelier",
      subtitle: "COLLECTION // HAUTE COUTURE",
      desc: "Luxury editorial layout featuring sculptured typography, warm gold tones, and generous negative space crafted for high-end fashion and lifestyle publications.",
      color: "#d4af37",
      fontName: "34px Playfair Display",
      tag: "HAUTE COUTURE // PARIS",
      publisher: "Vogue Gazette",
      monogram: "VG",
      font: "'Playfair Display', serif",
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
          body: "Severe architectural cuts meet fluid silk movement in nocturnal palettes of obsidian and chalk ivory.",
          cta: "Explore Silhouettes"
        },
        {
          bg: "https://images.unsplash.com/photo-1490481651871-ab68de25d43d?w=600&h=1000&fit=crop&q=80",
          headline: "Sustainable Velvet & Heirlooms",
          body: "Artisanal European weaving houses revitalize botanical dyeing practices for carbon-neutral fashion.",
          cta: "Read Craft Profile"
        },
        {
          bg: "https://images.unsplash.com/photo-1469334031218-e382a71b716b?w=600&h=1000&fit=crop&q=80",
          headline: "The Private Atelier Lookbook",
          body: "Exclusive preview invitations now open for Autumn/Winter VIP collectors and editorial press.",
          cta: "Request Invitation &rarr;"
        }
      ]
    },
    viral: {
      title: "High-Retention Creator Pulse",
      subtitle: "ENGAGEMENT // VIRAL HOOKS",
      desc: "Rapid-fire visual pacing, electric neon accents, and bold typographic hooks engineered to stop thumb-scrolling and maximize complete story reads.",
      color: "#ff2d55",
      fontName: "36px Plus Jakarta Sans",
      tag: "VIRAL PULSE // 2026",
      publisher: "Creator Pulse",
      monogram: "CP",
      font: "'Plus Jakarta Sans', sans-serif",
      slides: [
        {
          bg: "https://images.unsplash.com/photo-1542751371-adc38448a05e?w=600&h=1000&fit=crop&q=80",
          headline: "The 3-Second Rule That Breaks Feeds",
          body: "Stop wasting intro slides. Here is why the first 12 words dictate 88% of full story retention rates.",
          cta: "See The Breakdown"
        },
        {
          bg: "https://images.unsplash.com/photo-1511512578047-dfb367046420?w=600&h=1000&fit=crop&q=80",
          headline: "Color Contrast Science in Short Video",
          body: "High-saturation foreground badges paired with dark background scrims boost tap-through clicks by 42%.",
          cta: "View Heatmaps"
        },
        {
          bg: "https://images.unsplash.com/photo-1516251193007-45ef944ab0c6?w=600&h=1000&fit=crop&q=80",
          headline: "The Kinetic Micro-Copy Blueprint",
          body: "Use active verbs and open-loop questions to compel users to swipe through all 4 slides.",
          cta: "Download Blueprint"
        },
        {
          bg: "https://images.unsplash.com/photo-1518709268805-4e9042af9f23?w=600&h=1000&fit=crop&q=80",
          headline: "Distill Blog Posts In 1 Click",
          body: "Turn your standard WordPress articles into high-converting Web Stories instantly with our Pennion engine.",
          cta: "Try Free Plugin &rarr;"
        }
      ]
    },
    food: {
      title: "Artisanal Botanical Bakery",
      subtitle: "CULINARY // GASTRONOMY",
      desc: "Warm earth tones, organic typography, and appetizing macro food photography tailored for chefs, food bloggers, and recipe publishers.",
      color: "#ff9500",
      fontName: "32px Space Grotesk",
      tag: "ARTISAN // BOTANICAL",
      publisher: "Botanical Bistro",
      monogram: "BB",
      font: "'Space Grotesk', sans-serif",
      slides: [
        {
          bg: "https://images.unsplash.com/photo-1540420773420-3366772f4999?w=600&h=1000&fit=crop&q=80",
          headline: "The Sourdough Fermentation Formula",
          body: "Mastering wild yeast hydration kinetics and heirloom stone-milled wheat crumb aerations.",
          cta: "Get Full Recipe"
        },
        {
          bg: "https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=600&h=1000&fit=crop&q=80",
          headline: "85% High Hydration Dough Folding",
          body: "Coil folds spaced every 30 minutes build strong gluten elasticity without degassing the starter.",
          cta: "Watch Folding Technique"
        },
        {
          bg: "https://images.unsplash.com/photo-1509440159596-0249088772ff?w=600&h=1000&fit=crop&q=80",
          headline: "Dutch Oven Steam Expansion Magic",
          body: "Trapped water vapor superheats crust starch gelatinization, creating delicate blistered pockets.",
          cta: "Bake Instructions"
        },
        {
          bg: "https://images.unsplash.com/photo-1514933651103-005eec06c04b?w=600&h=1000&fit=crop&q=80",
          headline: "Pairing Slices With Cultured Butter",
          body: "Flaky sea salt crystals sprinkled over double-churned grass-fed Jersey cream butter.",
          cta: "Explore Menu &rarr;"
        }
      ]
    },
    fitness: {
      title: "BioFit Metabolic Conditioning",
      subtitle: "ATHLETICS // BIO-OPTIMIZATION",
      desc: "Clean performance styling with electric emerald accents and crisp typography built for trainers, wellness coaches, and athletics.",
      color: "#34c759",
      fontName: "32px Inter Crisp",
      tag: "BIOFIT // METABOLIC",
      publisher: "BioFit Athletic",
      monogram: "BF",
      font: "'Inter', sans-serif",
      slides: [
        {
          bg: "https://images.unsplash.com/photo-1517838277536-f5f99be501cd?w=600&h=1000&fit=crop&q=80",
          headline: "Zone 2 Metabolic Conditioning Protocol",
          body: "Optimizing mitochondrial density and endurance thresholds with low-lactate steady-state pacing.",
          cta: "Start Workout"
        },
        {
          bg: "https://images.unsplash.com/photo-1534438327276-14e5300c3a48?w=600&h=1000&fit=crop&q=80",
          headline: "Heart Rate Variability & Recovery",
          body: "Maintain strict 65-75% maximum heart rate to stimulate aerobic fat oxidation pathways.",
          cta: "View HR Zones"
        },
        {
          bg: "https://images.unsplash.com/photo-1541534741688-6078c6bfb5c5?w=600&h=1000&fit=crop&q=80",
          headline: "Electrolyte Replenishment Ratios",
          body: "Sodium-potassium-magnesium hydration stacks calibrated for 90-minute sustained athletic output.",
          cta: "Nutrition Stack"
        },
        {
          bg: "https://images.unsplash.com/photo-1518611012118-696072aa579a?w=600&h=1000&fit=crop&q=80",
          headline: "Weekly Progressive Overload Log",
          body: "Track cardiac drift trends and lactate clearance week-over-week across your training mesocycle.",
          cta: "Log Session &rarr;"
        }
      ]
    },
    travel: {
      title: "Patagonia Glacial Expeditions",
      subtitle: "EXPEDITION // PATAGONIA",
      desc: "Crisp alpine sky blues, majestic panoramic framing, and rugged itinerary formatting for travel journals, explorers, and expeditions.",
      color: "#0071e3",
      fontName: "32px Space Grotesk",
      tag: "EXPEDITION // PATAGONIA",
      publisher: "Wanderlust Atlas",
      monogram: "WA",
      font: "'Space Grotesk', sans-serif",
      slides: [
        {
          bg: "https://images.unsplash.com/photo-1506744038136-46273834b3fb?w=600&h=1000&fit=crop&q=80",
          headline: "The Hidden Glacial Fjords of Patagonia",
          body: "48 hours traversing uncharted southern ice sheets and sub-polar archipelago coastlines.",
          cta: "Explore Itinerary"
        },
        {
          bg: "https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?w=600&h=1000&fit=crop&q=80",
          headline: "Glacier Grey Kayak Navigation",
          body: "Threading sea kayaks through electric blue calved icebergs in 35-knot katabatic winds.",
          cta: "View Route Map"
        },
        {
          bg: "https://images.unsplash.com/photo-1470071459604-3b5ec3a7fe05?w=600&h=1000&fit=crop&q=80",
          headline: "Wilderness Bivouac Under The Stars",
          body: "Pristine sub-Antarctic night skies with zero artificial light pollution above granite spires.",
          cta: "Camp Guide"
        },
        {
          bg: "https://images.unsplash.com/photo-1426604966848-d7adac402bff?w=600&h=1000&fit=crop&q=80",
          headline: "Complete Expedition Equipment Checklist",
          body: "Crampons, four-season alpine bivvy shelters, and iridium satellite communication beacons.",
          cta: "Download Checklist &rarr;"
        }
      ]
    }
  };

  // State
  let currentKey = 'tech';
  let currentSlideIndex = 0;
  let customFont = null;

  // Studio Elements
  const studioPhoneScreen = document.getElementById('studioPhoneScreen');
  const studioStoryBadge = document.getElementById('studioStoryBadge');
  const studioStoryHeading = document.getElementById('studioStoryHeading');
  const studioStoryBody = document.getElementById('studioStoryBody');
  const studioStoryCta = document.getElementById('studioStoryCta');
  const studioPubTitle = document.getElementById('studioPubTitle');
  const studioPubMonogram = document.getElementById('studioPubMonogram');
  const studioLaserGuide = document.getElementById('studioLaserGuide');
  const studioLaserSwitch = document.getElementById('studioLaserSwitch');

  const studioMetaSubtitle = document.getElementById('studioMetaSubtitle');
  const studioArchetypeTitle = document.getElementById('studioArchetypeTitle');
  const studioArchetypeDesc = document.getElementById('studioArchetypeDesc');
  const studioColorHex = document.getElementById('studioColorHex');
  const studioFontScale = document.getElementById('studioFontScale');

  const fontCapsules = document.querySelectorAll('.font-capsule');
  const btnNextStudioSlide = document.getElementById('btnNextStudioSlide');

  // ==========================================================================
  // Gooey Spring Tab Navigation Engine (from Gooey-Tab-Navigation)
  // Physics Spring Interpolation + Directional Canvas Pixel Burst
  // ==========================================================================
  const studioTabShell = document.getElementById('studioTabShell');
  const gooeyTabs = studioTabShell ? [...studioTabShell.querySelectorAll('.gooey-tab')] : [];
  const gooeyIndicator = document.getElementById('studioIndicator');
  const gooeyAmbient = document.getElementById('studioAmbient');
  const gooeyCanvas = document.getElementById('studioPixelCanvas');
  const gooeyCtx = gooeyCanvas ? gooeyCanvas.getContext('2d') : null;
  const reducedMotion = matchMedia('(prefers-reduced-motion: reduce)');

  let gooeyActive = 0;
  let gooeyRaf = 0;
  let gooeyX = 0;
  let gooeyVelocity = 0;
  let gooeyTargetX = 0;
  let gooeyTargetW = 96;
  let gooeyCurrentW = 96;
  let gooeyLastTime = 0;
  let gooeyStretch = 1;
  let gooeyParticles = [];
  let gooeyPixelFrame = 0;

  const measureGooeyTab = i => {
    if (!studioTabShell || !gooeyTabs[i]) return { x: 0, w: 96, center: 48 };
    const s = studioTabShell.getBoundingClientRect();
    const r = gooeyTabs[i].getBoundingClientRect();
    return {
      x: r.left - s.left - 5,
      w: r.width,
      center: r.left - s.left - 5 + r.width / 2
    };
  };

  function resizeGooeyCanvas() {
    if (!studioTabShell || !gooeyCanvas || !gooeyCtx) return;
    const dpr = Math.min(window.devicePixelRatio || 1, 2);
    const r = studioTabShell.getBoundingClientRect();
    gooeyCanvas.width = Math.round(r.width * dpr);
    gooeyCanvas.height = Math.round(r.height * dpr);
    gooeyCanvas.style.width = r.width + 'px';
    gooeyCanvas.style.height = r.height + 'px';
    gooeyCtx.setTransform(dpr, 0, 0, dpr, 0, 0);
  }

  function pixelBurst(rect, direction) {
    if (reducedMotion.matches || !gooeyCtx) return;
    const size = 4.2;
    const gap = 5.8;
    const rows = Math.max(4, Math.floor(rect.h / gap));
    for (let row = 0; row < rows; row++) {
      for (let col = 0; col < Math.ceil(rect.w / gap); col++) {
        const px = rect.x + col * gap + (Math.random() - 0.5) * 1.8;
        const py = rect.y + row * gap + (Math.random() - 0.5) * 1.8;
        const nx = (px - rect.x) / Math.max(rect.w, 1) - 0.5;
        const ny = (py - rect.y) / Math.max(rect.h, 1) - 0.5;
        const speed = 38 + Math.random() * 100;
        gooeyParticles.push({
          x: px,
          y: py,
          vx: direction * (24 + Math.random() * speed) + nx * 28,
          vy: ny * 42 + (Math.random() - 0.5) * 76,
          size: size * (0.65 + Math.random() * 0.65),
          life: 0.48 + Math.random() * 0.28,
          age: 0,
          delay: Math.random() * 0.075,
          rotation: Math.random() * 0.5,
          spin: (Math.random() - 0.5) * 5,
          alpha: 0.75 + Math.random() * 0.25
        });
      }
    }
    if (!gooeyPixelFrame) gooeyPixelFrame = requestAnimationFrame(drawGooeyPixels);
  }

  function drawGooeyPixels(now) {
    if (!gooeyCanvas || !gooeyCtx) return;
    const dt = Math.min((now - (drawGooeyPixels.last || now)) / 1000, 0.032) || 0.016;
    drawGooeyPixels.last = now;
    const w = gooeyCanvas.clientWidth;
    const h = gooeyCanvas.clientHeight;
    gooeyCtx.clearRect(0, 0, w, h);
    gooeyParticles = gooeyParticles.filter(p => p.age < p.life + p.delay);
    for (const p of gooeyParticles) {
      p.age += dt;
      if (p.age < p.delay) continue;
      const t = (p.age - p.delay) / p.life;
      p.x += p.vx * dt;
      p.y += p.vy * dt;
      p.vx *= Math.pow(0.985, dt * 60);
      p.vy += 85 * dt;
      p.rotation += p.spin * dt;
      const alpha = p.alpha * Math.pow(1 - t, 1.35);
      const s = p.size * (1 - 0.18 * t);
      gooeyCtx.save();
      gooeyCtx.translate(p.x, p.y);
      gooeyCtx.rotate(p.rotation);
      gooeyCtx.globalAlpha = alpha;
      gooeyCtx.fillStyle = t < 0.25 ? '#555555' : (t < 0.65 ? '#333333' : '#111111');
      gooeyCtx.shadowColor = 'rgba(0, 0, 0, 0.2)';
      gooeyCtx.shadowBlur = 5;
      gooeyCtx.fillRect(-s / 2, -s / 2, s, s);
      gooeyCtx.restore();
    }
    if (gooeyParticles.length) {
      gooeyPixelFrame = requestAnimationFrame(drawGooeyPixels);
    } else {
      gooeyPixelFrame = 0;
      gooeyCtx.clearRect(0, 0, w, h);
      drawGooeyPixels.last = 0;
    }
  }

  function gooeySpringFrame(now) {
    const dt = Math.min((now - gooeyLastTime) / 1000, 0.032) || 0.016;
    gooeyLastTime = now;
    const dx = gooeyTargetX - gooeyX;
    gooeyVelocity += (dx * 230 - gooeyVelocity * 29) * dt;
    gooeyX += gooeyVelocity * dt;
    gooeyCurrentW += (gooeyTargetW - gooeyCurrentW) * (1 - Math.exp(-dt * 15));
    const desiredStretch = 1 + Math.min(Math.abs(gooeyVelocity) * 0.00065, 0.13);
    gooeyStretch += (desiredStretch - gooeyStretch) * (1 - Math.exp(-dt * 18));

    if (Math.abs(dx) < 0.08 && Math.abs(gooeyVelocity) < 0.5) {
      gooeyX = gooeyTargetX;
      gooeyVelocity = 0;
      gooeyCurrentW = gooeyTargetW;
      gooeyStretch = 1;
      if (gooeyIndicator) {
        gooeyIndicator.style.width = gooeyTargetW + 'px';
        gooeyIndicator.style.transform = `translate3d(${gooeyX}px,0,0) scaleX(1)`;
      }
      gooeyRaf = 0;
      return;
    }

    if (gooeyIndicator) {
      gooeyIndicator.style.width = gooeyTargetW + 'px';
      gooeyIndicator.style.transform = `translate3d(${gooeyX}px,0,0) scaleX(${(gooeyCurrentW / gooeyTargetW) * gooeyStretch})`;
    }
    gooeyRaf = requestAnimationFrame(gooeySpringFrame);
  }

  function moveToGooeyTab(i, focus = false) {
    if (!gooeyTabs.length) return;
    i = (i + gooeyTabs.length) % gooeyTabs.length;
    if (focus) gooeyTabs[i].focus();
    if (i === gooeyActive) return;

    const old = measureGooeyTab(gooeyActive);
    const next = measureGooeyTab(i);
    const direction = Math.sign(next.center - old.center) || 1;
    pixelBurst({ x: old.x + 2, y: 5, w: Math.max(0, old.w - 4), h: Math.max(0, old.h - 10) }, direction);

    const visibleCenter = gooeyX + gooeyCurrentW / 2;
    gooeyActive = i;
    gooeyTargetW = next.w;
    gooeyTargetX = next.center - gooeyTargetW / 2;

    gooeyTabs.forEach((t, n) => {
      t.classList.toggle('active', n === i);
      t.setAttribute('aria-selected', String(n === i));
      t.tabIndex = n === i ? 0 : -1;
    });

    if (gooeyTabs[i]) {
      try {
        gooeyTabs[i].scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
      } catch (err) {}
    }

    // Switch Studio Archetype Data
    const arch = gooeyTabs[i].getAttribute('data-archetype');
    if (arch && ARCHETYPES[arch]) {
      currentKey = arch;
      currentSlideIndex = 0;
      customFont = null;
      studioProgress = 0;
      studioLastTimestamp = null;
      renderStudioSlide();
    }

    if (reducedMotion.matches) {
      if (gooeyRaf) cancelAnimationFrame(gooeyRaf);
      gooeyX = gooeyTargetX;
      gooeyCurrentW = gooeyTargetW;
      gooeyVelocity = 0;
      gooeyStretch = 1;
      if (gooeyIndicator) {
        gooeyIndicator.style.width = gooeyTargetW + 'px';
        gooeyIndicator.style.transform = `translate3d(${gooeyX}px,0,0) scaleX(1)`;
      }
      return;
    }

    gooeyX = visibleCenter - gooeyTargetW / 2;
    gooeyCurrentW = gooeyTargetW;
    gooeyVelocity = Math.max(-900, Math.min(900, gooeyVelocity));
    if (!gooeyRaf) {
      gooeyLastTime = performance.now();
      gooeyRaf = requestAnimationFrame(gooeySpringFrame);
    }

    if (gooeyAmbient) {
      gooeyAmbient.animate(
        [
          { transform: 'scale(.98)', opacity: .72 },
          { transform: 'scale(1.04)', opacity: .95 },
          { transform: 'scale(1)', opacity: .82 }
        ],
        { duration: 520, easing: 'cubic-bezier(.2,.7,.25,1)' }
      );
    }
  }

  // Hook up Gooey Tabs Click & Keyboard
  gooeyTabs.forEach((tab, i) => {
    tab.addEventListener('click', () => moveToGooeyTab(i));
    tab.addEventListener('keydown', e => {
      let n = null;
      if (e.key === 'ArrowRight') n = i + 1;
      if (e.key === 'ArrowLeft') n = i - 1;
      if (e.key === 'Home') n = 0;
      if (e.key === 'End') n = gooeyTabs.length - 1;
      if (n !== null) {
        e.preventDefault();
        moveToGooeyTab(n, true);
      }
    });
  });

  function syncGooey() {
    if (!studioTabShell || !gooeyTabs.length) return;
    if (gooeyRaf) cancelAnimationFrame(gooeyRaf);
    const r = measureGooeyTab(gooeyActive);
    gooeyX = gooeyTargetX = r.x;
    gooeyTargetW = gooeyCurrentW = r.w;
    gooeyVelocity = 0;
    gooeyStretch = 1;
    gooeyRaf = 0;
    if (gooeyIndicator) {
      gooeyIndicator.style.width = r.w + 'px';
      gooeyIndicator.style.transform = `translate3d(${r.x}px,0,0) scaleX(1)`;
    }
    resizeGooeyCanvas();
  }

  window.addEventListener('resize', syncGooey);
  requestAnimationFrame(syncGooey);

  function renderStudioSlide() {
    const data = ARCHETYPES[currentKey];
    if (!data) return;
    const slide = data.slides[currentSlideIndex];
    const activeFont = customFont || data.font;

    // Studio Screen
    if (studioPhoneScreen) {
      studioPhoneScreen.style.backgroundImage = `url('${slide.bg}')`;
    }
    if (studioStoryBadge) {
      studioStoryBadge.textContent = data.tag;
      studioStoryBadge.style.backgroundColor = data.color;
    }
    if (studioStoryHeading) {
      studioStoryHeading.textContent = slide.headline;
      studioStoryHeading.style.fontFamily = activeFont;
    }
    if (studioStoryBody) {
      studioStoryBody.textContent = slide.body;
    }
    if (studioStoryCta) {
      studioStoryCta.innerHTML = `<span>${slide.cta}</span><span>&rarr;</span>`;
      studioStoryCta.style.borderColor = 'rgba(255, 255, 255, 0.4)';
    }
    if (studioPubTitle) {
      studioPubTitle.textContent = data.publisher;
    }
    if (studioPubMonogram) {
      studioPubMonogram.textContent = data.monogram;
      studioPubMonogram.style.backgroundColor = data.color;
    }

    // Studio Side Info
    if (studioMetaSubtitle) studioMetaSubtitle.textContent = data.subtitle;
    if (studioArchetypeTitle) studioArchetypeTitle.textContent = data.title;
    if (studioArchetypeDesc) studioArchetypeDesc.textContent = data.desc;
    if (studioColorHex) {
      studioColorHex.textContent = data.color.toUpperCase();
      studioColorHex.style.color = data.color;
    }
    if (studioFontScale) {
      studioFontScale.textContent = data.fontName;
    }

    // Update Progress Bars on Studio Phone
    updateStudioBars();
  }

  // ── Studio Live Auto-Advance & Ticking Progress Bar ──
  const SLIDE_DURATION = 4500; // 4.5 seconds per slide
  let studioProgress = 0; // 0 to 1
  let studioLastTimestamp = null;
  let isStudioPaused = false;
  let studioRafId = null;

  function updateStudioBars() {
    if (!studioPhoneScreen) return;
    const segs = studioPhoneScreen.querySelectorAll('.story-bar-segment');
    segs.forEach((seg, idx) => {
      seg.classList.remove('active', 'completed');
      const fill = seg.querySelector('.story-bar-fill');
      if (idx < currentSlideIndex) {
        seg.classList.add('completed');
        if (fill) fill.style.width = '100%';
      } else if (idx === currentSlideIndex) {
        seg.classList.add('active');
        if (fill) fill.style.width = `${Math.min(100, Math.max(0, studioProgress * 100))}%`;
      } else {
        if (fill) fill.style.width = '0%';
      }
    });
  }

  function goToStudioSlide(targetIndex) {
    const data = ARCHETYPES[currentKey];
    if (!data) return;
    const total = data.slides.length;
    currentSlideIndex = (targetIndex + total) % total;
    studioProgress = 0;
    studioLastTimestamp = null;
    renderStudioSlide();
  }

  function studioLoop(timestamp) {
    if (!studioLastTimestamp) studioLastTimestamp = timestamp;
    const delta = timestamp - studioLastTimestamp;
    studioLastTimestamp = timestamp;

    if (!isStudioPaused && delta > 0 && delta < 500) {
      studioProgress += delta / SLIDE_DURATION;
      if (studioProgress >= 1) {
        goToStudioSlide(currentSlideIndex + 1);
      } else {
        updateStudioBars();
      }
    }

    studioRafId = requestAnimationFrame(studioLoop);
  }
  studioRafId = requestAnimationFrame(studioLoop);

  // Hold to Pause Studio Simulator
  const studioPauseIndicator = document.getElementById('studioPauseIndicator');
  function pauseStudio() {
    isStudioPaused = true;
    if (studioPauseIndicator) studioPauseIndicator.classList.add('is-visible');
  }
  function resumeStudio() {
    isStudioPaused = false;
    studioLastTimestamp = null;
    if (studioPauseIndicator) studioPauseIndicator.classList.remove('is-visible');
  }

  if (studioPhoneScreen) {
    studioPhoneScreen.addEventListener('pointerdown', pauseStudio);
    window.addEventListener('pointerup', resumeStudio);
    studioPhoneScreen.addEventListener('pointerleave', resumeStudio);
  }

  // Interactive Left / Right Tap Zones inside Studio Phone
  const studioTapLeft = document.getElementById('studioTapLeft');
  const studioTapRight = document.getElementById('studioTapRight');

  if (studioTapLeft) {
    studioTapLeft.addEventListener('click', (e) => {
      e.stopPropagation();
      if (studioProgress > 0.25) {
        studioProgress = 0;
        updateStudioBars();
      } else {
        goToStudioSlide(currentSlideIndex - 1);
      }
    });
  }

  if (studioTapRight) {
    studioTapRight.addEventListener('click', (e) => {
      e.stopPropagation();
      goToStudioSlide(currentSlideIndex + 1);
    });
  }

  if (btnNextStudioSlide) {
    btnNextStudioSlide.addEventListener('click', (e) => {
      e.stopPropagation();
      goToStudioSlide(currentSlideIndex + 1);
    });
  }

  // ── 3D Perspective Holographic Tilt & Liquid Glare ──
  const studioPhoneWrap = document.getElementById('studioPhoneWrap');
  const studioIPhone = document.getElementById('studioIPhone') || document.querySelector('.iphone-titanium');
  const liquidGlare = document.querySelector('.liquid-phone-glare');

  if (studioPhoneWrap && studioIPhone) {
    studioPhoneWrap.addEventListener('mousemove', (e) => {
      const rect = studioPhoneWrap.getBoundingClientRect();
      const cx = rect.left + rect.width / 2;
      const cy = rect.top + rect.height / 2;
      const nx = (e.clientX - cx) / (rect.width / 2);
      const ny = (e.clientY - cy) / (rect.height / 2);
      const rotX = -ny * 11;
      const rotY = nx * 11;

      studioIPhone.style.transform = `perspective(1000px) rotateX(${rotX.toFixed(2)}deg) rotateY(${rotY.toFixed(2)}deg) scale3d(1.025, 1.025, 1.025)`;

      if (liquidGlare) {
        const gx = ((nx + 1) / 2) * 100;
        const gy = ((ny + 1) / 2) * 100;
        liquidGlare.style.opacity = '1';
        liquidGlare.style.background = `radial-gradient(circle at ${gx.toFixed(1)}% ${gy.toFixed(1)}%, rgba(255, 255, 255, 0.35) 0%, rgba(255, 255, 255, 0.08) 35%, transparent 65%)`;
      }
    });

    studioPhoneWrap.addEventListener('mouseleave', () => {
      studioIPhone.style.transform = 'perspective(1000px) rotateX(0deg) rotateY(0deg) scale3d(1, 1, 1)';
      if (liquidGlare) {
        liquidGlare.style.opacity = '0';
      }
    });
  }

  // Font Capsules Click
  fontCapsules.forEach(cap => {
    cap.addEventListener('click', () => {
      fontCapsules.forEach(c => c.classList.remove('is-active'));
      cap.classList.add('is-active');
      customFont = cap.getAttribute('data-font');
      renderStudioSlide();
    });
  });

  // Safe Zone Laser Switch
  if (studioLaserSwitch) {
    studioLaserSwitch.addEventListener('change', () => {
      const isChecked = studioLaserSwitch.checked;
      if (studioLaserGuide) {
        if (isChecked) studioLaserGuide.classList.add('is-active');
        else studioLaserGuide.classList.remove('is-active');
      }
    });
  }

  // 2. Lookbook Search & Category Filtering
  const lookbookSearch = document.getElementById('lookbookSearch');
  const catCapsules = document.querySelectorAll('.cat-capsule-btn');
  const lookbookCards = document.querySelectorAll('.lookbook-item-card');

  function filterLookbook() {
    const query = (lookbookSearch ? lookbookSearch.value : '').toLowerCase().trim();
    const activeBtn = document.querySelector('.cat-capsule-btn.is-active');
    const selectedCat = activeBtn ? activeBtn.getAttribute('data-cat') : 'all';

    lookbookCards.forEach(card => {
      const title = (card.querySelector('.card-main-title') || {}).textContent.toLowerCase();
      const sub = (card.querySelector('.card-sub-info') || {}).textContent.toLowerCase();
      const tag = (card.querySelector('.card-category-tag') || {}).textContent.toLowerCase();
      const cat = card.getAttribute('data-cat') || '';

      const matchesQuery = !query || title.includes(query) || sub.includes(query) || tag.includes(query);
      const matchesCategory = (selectedCat === 'all') || (cat === selectedCat);

      if (matchesQuery && matchesCategory) {
        card.style.display = 'flex';
        card.style.opacity = '1';
        card.style.transform = 'none';
      } else {
        card.style.display = 'none';
      }
    });
  }

  if (lookbookSearch) {
    lookbookSearch.addEventListener('input', filterLookbook);
  }

  catCapsules.forEach(btn => {
    btn.addEventListener('click', () => {
      catCapsules.forEach(b => b.classList.remove('is-active'));
      btn.classList.add('is-active');
      filterLookbook();
    });
  });

  // Star Favorite Toggle
  const starButtons = document.querySelectorAll('.card-star-button');
  starButtons.forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.stopPropagation();
      btn.classList.toggle('is-fav');
      const svg = btn.querySelector('svg');
      if (btn.classList.contains('is-fav')) {
        svg.setAttribute('fill', '#f59e0b');
        svg.setAttribute('stroke', '#f59e0b');
      } else {
        svg.setAttribute('fill', 'none');
        svg.setAttribute('stroke', 'currentColor');
      }
    });
  });

  // "Load in Studio" Buttons on Lookbook Cards
  const loadButtons = document.querySelectorAll('.btn-card-load');
  loadButtons.forEach(btn => {
    btn.addEventListener('click', () => {
      const targetArch = btn.getAttribute('data-target-archetype');
      if (targetArch && ARCHETYPES[targetArch]) {
        const tabIdx = gooeyTabs.findIndex(t => t.getAttribute('data-archetype') === targetArch);
        if (tabIdx !== -1) {
          moveToGooeyTab(tabIdx);
        } else {
          currentKey = targetArch;
          currentSlideIndex = 0;
          customFont = null;
          renderStudioSlide();
        }

        // Smooth scroll to studio
        const studioEl = document.getElementById('studio');
        if (studioEl) {
          studioEl.scrollIntoView({ behavior: 'smooth' });
        }
      }
    });
  });

  // 3. macOS Terminal Installation Tabs & 1-Click Copy
  const terminalTabs = document.querySelectorAll('.terminal-tab');
  const cliCodeText = document.getElementById('cliCodeText');
  const btnCopyTerminal = document.getElementById('btnCopyTerminal');

  const COMMANDS = {
    git: "git clone https://github.com/ursishant/web-stories-templates-studio.git wp-content/plugins/web-stories-templates",
    wp: "wp plugin install https://github.com/ursishant/web-stories-templates-studio/releases/download/v1.3.6/web-stories-templates.zip --activate",
    zip: "curl -LO https://github.com/ursishant/web-stories-templates-studio/releases/download/v1.3.6/web-stories-templates.zip && unzip web-stories-templates.zip -d wp-content/plugins/"
  };

  terminalTabs.forEach(tab => {
    tab.addEventListener('click', () => {
      terminalTabs.forEach(t => t.classList.remove('active'));
      tab.classList.add('active');
      const cmdKey = tab.getAttribute('data-cmd');
      if (cliCodeText && COMMANDS[cmdKey]) {
        cliCodeText.textContent = COMMANDS[cmdKey];
      }
    });
  });

  if (btnCopyTerminal && cliCodeText) {
    btnCopyTerminal.addEventListener('click', () => {
      const textToCopy = cliCodeText.textContent.trim();
      navigator.clipboard.writeText(textToCopy).then(() => {
        const originalText = btnCopyTerminal.textContent;
        btnCopyTerminal.textContent = "✓ Copied!";
        btnCopyTerminal.style.background = "#34c759";
        btnCopyTerminal.style.borderColor = "#34c759";
        btnCopyTerminal.style.color = "#ffffff";
        setTimeout(() => {
          btnCopyTerminal.textContent = originalText;
          btnCopyTerminal.style.background = "";
          btnCopyTerminal.style.borderColor = "";
          btnCopyTerminal.style.color = "";
        }, 2200);
      });
    });
  }

  // 4. Interactive Apple FAQ Accordion
  const faqHeaders = document.querySelectorAll('.faq-header-btn');
  faqHeaders.forEach(btn => {
    btn.addEventListener('click', () => {
      const item = btn.closest('.faq-item');
      const isOpen = item.classList.contains('is-open');

      // Close other open items with smooth spring accordion animation
      document.querySelectorAll('.faq-item.is-open').forEach(other => {
        if (other !== item) {
          other.classList.remove('is-open');
          const otherBtn = other.querySelector('.faq-header-btn');
          if (otherBtn) otherBtn.setAttribute('aria-expanded', 'false');
        }
      });

      if (isOpen) {
        item.classList.remove('is-open');
        btn.setAttribute('aria-expanded', 'false');
      } else {
        item.classList.add('is-open');
        btn.setAttribute('aria-expanded', 'true');
      }
    });
  });

  // FAQ Category Filter Pills
  const faqPills = document.querySelectorAll('.faq-filter-pill');
  const faqItems = document.querySelectorAll('.faq-item');
  faqPills.forEach(pill => {
    pill.addEventListener('click', () => {
      faqPills.forEach(p => p.classList.remove('is-active'));
      pill.classList.add('is-active');
      const filter = pill.getAttribute('data-faq-filter');

      faqItems.forEach(item => {
        const cat = item.getAttribute('data-faq-cat');
        if (filter === 'all' || cat === filter) {
          item.style.display = 'block';
        } else {
          item.style.display = 'none';
        }
      });
    });
  });

  // 5. Hero Phone 3D Parallax Tilt Microinteraction
  const heroStage = document.querySelector('.hero-stage');
  const heroPhone = document.querySelector('.hero-stage .iphone-titanium');
  if (heroStage && heroPhone) {
    heroStage.addEventListener('mousemove', (e) => {
      const rect = heroStage.getBoundingClientRect();
      const x = e.clientX - rect.left - rect.width / 2;
      const y = e.clientY - rect.top - rect.height / 2;
      const rotateY = (x / (rect.width / 2)) * 7;
      const rotateX = -(y / (rect.height / 2)) * 7;
      heroPhone.style.transform = `perspective(1200px) rotateX(${rotateX.toFixed(2)}deg) rotateY(${rotateY.toFixed(2)}deg) scale3d(1.02, 1.02, 1.02)`;
    });
    heroStage.addEventListener('mouseleave', () => {
      heroPhone.style.transform = 'perspective(1200px) rotateX(0deg) rotateY(0deg) scale3d(1, 1, 1)';
    });
  }

  // 6. Tactile Click Ripple on Phone Screens
  function createRipple(e, element) {
    if (!element) return;
    const ripple = document.createElement('span');
    ripple.classList.add('click-ripple');
    const rect = element.getBoundingClientRect();
    const size = Math.max(rect.width, rect.height) * 0.45;
    ripple.style.width = ripple.style.height = `${size}px`;
    ripple.style.left = `${e.clientX - rect.left - size / 2}px`;
    ripple.style.top = `${e.clientY - rect.top - size / 2}px`;
    element.appendChild(ripple);
    setTimeout(() => ripple.remove(), 600);
  }

  const heroPhoneScreen = document.getElementById('heroPhoneScreen');
  if (heroPhoneScreen) {
    heroPhoneScreen.addEventListener('click', (e) => {
      createRipple(e, heroPhoneScreen);
    });
  }
  if (studioPhoneScreen) {
    studioPhoneScreen.addEventListener('click', (e) => {
      createRipple(e, studioPhoneScreen);
    });
  }

  // Vidyst-Style Hero Platform Rotator
  const rotatorItems = document.querySelectorAll('.vidyst-rotator-item');
  if (rotatorItems.length > 1) {
    let rotatorIdx = 0;
    setInterval(() => {
      const prevEl = rotatorItems[rotatorIdx];
      prevEl.classList.remove('active');
      prevEl.classList.add('prev');

      rotatorIdx = (rotatorIdx + 1) % rotatorItems.length;
      const nextEl = rotatorItems[rotatorIdx];
      nextEl.classList.remove('prev');
      nextEl.classList.add('active');

      setTimeout(() => {
        prevEl.classList.remove('prev');
      }, 550);
    }, 2400);
  }

  // 7. Interactive Cursor Spotlight on Cards
  const spotlightCards = document.querySelectorAll('.bento-surface, .studio-card-box, .faq-item, .lookbook-item-card');
  spotlightCards.forEach(card => {
    card.addEventListener('mousemove', (e) => {
      const rect = card.getBoundingClientRect();
      const x = e.clientX - rect.left;
      const y = e.clientY - rect.top;
      card.style.setProperty('--mouse-x', `${x}px`);
      card.style.setProperty('--mouse-y', `${y}px`);
    });
  });

  // 8. Navbar Dynamic Frosted Elevation on Scroll
  const siteNav = document.querySelector('.site-nav');

  // Logo Click: Scroll smoothly back to top (Home)
  const brandLogoLink = document.getElementById('brandLogoLink');
  if (brandLogoLink) {
    brandLogoLink.addEventListener('click', (e) => {
      e.preventDefault();
      window.scrollTo({ top: 0, behavior: 'smooth' });
      if (window.history && window.history.pushState) {
        window.history.pushState(null, '', window.location.pathname);
      }
    });
  }

  // Mobile Nav Toggle & Sheet Handling
  const mobileNavToggle = document.getElementById('mobileNavToggle');
  const mobileNavMenu = document.getElementById('mobileNavMenu');
  const mobileNavBackdrop = document.getElementById('mobileNavBackdrop');
  if (mobileNavToggle && mobileNavMenu) {
    const toggleMenu = (open) => {
      const isOpen = open !== undefined ? open : !mobileNavMenu.classList.contains('is-open');
      mobileNavMenu.classList.toggle('is-open', isOpen);
      if (mobileNavBackdrop) mobileNavBackdrop.classList.toggle('is-open', isOpen);
      mobileNavToggle.setAttribute('aria-expanded', String(isOpen));
      document.body.style.overflow = isOpen ? 'hidden' : '';
    };

    mobileNavToggle.addEventListener('click', (e) => {
      e.stopPropagation();
      toggleMenu();
    });

    if (mobileNavBackdrop) {
      mobileNavBackdrop.addEventListener('click', () => toggleMenu(false));
    }

    document.querySelectorAll('.mobile-nav-link, .mobile-menu-cta').forEach(link => {
      link.addEventListener('click', () => toggleMenu(false));
    });

    document.addEventListener('click', (e) => {
      if (!mobileNavMenu.contains(e.target) && !mobileNavToggle.contains(e.target)) {
        toggleMenu(false);
      }
    });
  }

  if (siteNav) {
    window.addEventListener('scroll', () => {
      if (window.scrollY > 30) {
        siteNav.style.boxShadow = '0 4px 20px rgba(0, 0, 0, 0.05)';
        siteNav.style.borderColor = 'rgba(0, 0, 0, 0.1)';
      } else {
        siteNav.style.boxShadow = 'none';
        siteNav.style.borderColor = 'var(--border-hairline)';
      }
    }, { passive: true });
  }

  // 9. macOS Traffic Lights Micro-Interactions
  const redDot = document.querySelector('.light-red');
  const yellowDot = document.querySelector('.light-yellow');
  const greenDot = document.querySelector('.light-green');
  const terminalBody = document.querySelector('.macos-body');

  if (redDot) {
    redDot.addEventListener('click', () => {
      redDot.style.transform = 'scale(0.85) rotate(-15deg)';
      setTimeout(() => redDot.style.transform = '', 200);
    });
  }
  if (yellowDot && terminalBody) {
    yellowDot.addEventListener('click', () => {
      terminalBody.style.opacity = terminalBody.style.opacity === '0.25' ? '1' : '0.25';
    });
  }
  if (greenDot && terminalBody) {
    greenDot.addEventListener('click', () => {
      terminalBody.style.fontSize = terminalBody.style.fontSize === '15px' ? '13.5px' : '15px';
    });
  }


  // 11. Ambient Liquid Canvas Mouse Inertia
  const orbBlue = document.getElementById('orbBlue');
  const orbPurple = document.getElementById('orbPurple');
  const orbCyan = document.getElementById('orbCyan');

  let targetOrbX = 0, targetOrbY = 0;
  let curOrbX = 0, curOrbY = 0;

  window.addEventListener('mousemove', (e) => {
    const nx = (e.clientX / window.innerWidth) - 0.5;
    const ny = (e.clientY / window.innerHeight) - 0.5;
    targetOrbX = nx * 50;
    targetOrbY = ny * 50;
  }, { passive: true });

  function animateOrbs() {
    curOrbX += (targetOrbX - curOrbX) * 0.04;
    curOrbY += (targetOrbY - curOrbY) * 0.04;

    if (orbBlue) orbBlue.style.transform = `translate(${curOrbX * 1.2}px, ${curOrbY * 1.2}px)`;
    if (orbPurple) orbPurple.style.transform = `translate(${-curOrbX * 0.9}px, ${-curOrbY * 0.9}px)`;
    if (orbCyan) orbCyan.style.transform = `translate(${curOrbX * 0.8}px, ${-curOrbY * 0.7}px)`;

    requestAnimationFrame(animateOrbs);
  }
  requestAnimationFrame(animateOrbs);

  // 12. GSAP & ScrollTrigger: Morphing Navbar, Immersive Scroll & Staggers
  if (typeof gsap !== 'undefined') {
    if (typeof ScrollTrigger !== 'undefined') {
      gsap.registerPlugin(ScrollTrigger);
    }

    // A. Apple Dynamic Island Morphing Navbar on Scroll
    const siteNav = document.getElementById('siteNav');
    if (siteNav && typeof ScrollTrigger !== 'undefined') {
      ScrollTrigger.create({
        start: 'top -30px',
        onEnter: () => siteNav.classList.add('nav-morphed'),
        onLeaveBack: () => siteNav.classList.remove('nav-morphed')
      });
    }

    // C. Ambient Liquid Mesh Morphing & Velocity Reactivity
    if (orbBlue && orbPurple && orbCyan) {
      gsap.to(orbBlue, {
        x: '+=50',
        y: '-=35',
        scale: 1.12,
        duration: 7,
        repeat: -1,
        yoyo: true,
        ease: 'sine.inOut'
      });
      gsap.to(orbPurple, {
        x: '-=45',
        y: '+=45',
        scale: 0.92,
        duration: 9,
        repeat: -1,
        yoyo: true,
        ease: 'sine.inOut'
      });
      gsap.to(orbCyan, {
        x: '+=35',
        y: '+=30',
        scale: 1.08,
        duration: 8,
        repeat: -1,
        yoyo: true,
        ease: 'sine.inOut'
      });

      if (typeof ScrollTrigger !== 'undefined') {
        ScrollTrigger.create({
          onUpdate: (self) => {
            const vel = Math.min(Math.abs(self.getVelocity()) / 1200, 0.35);
            gsap.to('.liquid-orb', {
              scaleY: 1 + vel * 0.7,
              scaleX: 1 - vel * 0.15,
              duration: 0.2,
              overwrite: 'auto'
            });
          }
        });
      }
    }

    // D. Progressive Scroll Availability (Silky Dreamy Unblur on Desktop; Instantly Sharp on Mobile)
    const isMobileViewport = window.innerWidth <= 768;
    if (isMobileViewport) {
      // Mobile-First: Ensure 100% razor sharp visibility immediately without blur penalties
      document.querySelectorAll('.section-head, .studio-card-box, .bento-surface, .lookbook-item-card, .faq-item, .search-capsule-wrap, .category-capsules, .cta-banner-card').forEach(el => {
        el.style.opacity = '1';
        el.style.filter = 'none';
        el.style.transform = 'none';
      });
    } else if (typeof ScrollTrigger !== 'undefined' && typeof gsap !== 'undefined') {
      gsap.registerPlugin(ScrollTrigger);

      // Slow progressive reveal for section headers
      const sectionHeads = document.querySelectorAll('.section-head');
      sectionHeads.forEach(head => {
        const rect = head.getBoundingClientRect();
        if (rect.top > window.innerHeight * 0.88) {
          gsap.fromTo(head,
            { opacity: 0.2, filter: 'blur(10px)', y: 30 },
            {
              opacity: 1,
              filter: 'blur(0px)',
              y: 0,
              duration: 1.0,
              ease: 'power3.out',
              clearProps: 'filter,transform',
              scrollTrigger: {
                trigger: head,
                start: 'top 88%',
                once: true
              }
            }
          );
        } else {
          head.style.opacity = '1';
        }
      });

      // Progressive component reveal targets
      const progressiveTargets = [
        { sel: '.studio-card-box', y: 36, dur: 1.1, stagger: 0 },
        { sel: '.bento-surface', y: 32, dur: 0.95, stagger: 0.1 },
        { sel: '.search-capsule-wrap', y: 20, dur: 0.85, stagger: 0 },
        { sel: '.category-capsules', y: 20, dur: 0.85, stagger: 0 },
        { sel: '.lookbook-item-card', y: 34, dur: 1.0, stagger: 0.08 },
        { sel: '.faq-item', y: 22, dur: 0.85, stagger: 0.06 },
        { sel: '.cta-banner-card, .install-guide-box', y: 30, dur: 1.0, stagger: 0.08 }
      ];

      progressiveTargets.forEach(({ sel, y, dur, stagger }) => {
        const els = document.querySelectorAll(sel);
        if (!els.length) return;

        const belowFold = Array.from(els).filter(el => {
          const r = el.getBoundingClientRect();
          return r.top > window.innerHeight * 0.88;
        });

        if (!belowFold.length) {
          els.forEach(el => { el.style.opacity = '1'; });
          return;
        }

        // Set initial gentle blur and shift for below-fold items
        gsap.set(belowFold, {
          opacity: 0.2,
          filter: 'blur(10px)',
          y: y
        });

        ScrollTrigger.batch(belowFold, {
          start: 'top 90%',
          once: true,
          onEnter: (batch) => {
            gsap.to(batch, {
              opacity: 1,
              filter: 'blur(0px)',
              y: 0,
              stagger: stagger,
              duration: dur,
              ease: 'power3.out',
              clearProps: 'filter,transform'
            });
          }
        });
      });

      // Fallback safeguard: after 2.5s or on first user scroll, guarantee visibility
      const ensureVisible = () => {
        document.querySelectorAll('.studio-card-box, .bento-surface, .lookbook-item-card, .faq-item').forEach(el => {
          if (el.style.opacity && parseFloat(el.style.opacity) < 0.5) {
            el.style.opacity = '1';
            el.style.filter = 'none';
            el.style.transform = 'none';
          }
        });
      };
      setTimeout(ensureVisible, 2500);

      // Refresh triggers once all webfonts and images complete loading
      window.addEventListener('load', () => {
        ScrollTrigger.refresh();
      });
    }
  }

  // 14. Bento Distiller Simulation Interaction
  const btnDistillerSim = document.getElementById('btnDistillerSim');
  const distillerFill = document.getElementById('distillerFill');
  const distillerStatus = document.getElementById('distillerStatus');
  const distillerIcon = document.getElementById('distillerIcon');

  if (btnDistillerSim) {
    btnDistillerSim.addEventListener('click', () => {
      if (distillerFill) distillerFill.style.width = '0%';
      if (distillerStatus) {
        distillerStatus.textContent = 'Parsing WordPress H2 headings & media...';
        distillerStatus.style.color = 'var(--text-secondary)';
      }
      if (distillerIcon) distillerIcon.style.transform = 'rotate(180deg)';

      setTimeout(() => {
        if (distillerFill) distillerFill.style.width = '48%';
        if (distillerStatus) distillerStatus.textContent = 'Distilling 2,450 words into 4 AMP slides...';
      }, 400);

      setTimeout(() => {
        if (distillerFill) distillerFill.style.width = '100%';
        if (distillerStatus) {
          distillerStatus.textContent = '✓ 4-Slide Story Distilled in 1.1s (100% AMP Valid)';
          distillerStatus.style.color = '#34c759';
        }
        if (distillerIcon) distillerIcon.style.transform = 'rotate(360deg)';
      }, 1000);
    });
  }

  // 15. Bento Ambient Audio Equalizer Interaction
  const btnAudioToggle = document.getElementById('btnAudioToggle');
  const audioWaves = document.getElementById('audioWaves');
  const audioPlayIcon = document.getElementById('audioPlayIcon');
  const audioTrackName = document.getElementById('audioTrackName');
  let isAudioPlaying = false;

  if (btnAudioToggle) {
    btnAudioToggle.addEventListener('click', () => {
      isAudioPlaying = !isAudioPlaying;
      if (audioWaves) audioWaves.classList.toggle('is-playing', isAudioPlaying);
      if (audioPlayIcon) audioPlayIcon.textContent = isAudioPlaying ? '❚❚' : '▶';
      if (audioTrackName) {
        audioTrackName.textContent = isAudioPlaying 
          ? 'Now Playing: Lo-Fi Discover Chill (Loop)' 
          : 'Lo-Fi Discover Chill • 120 BPM';
        audioTrackName.style.color = isAudioPlaying ? '#ff2d55' : 'var(--text-primary)';
      }
    });
  }

  // 16. Real-Time GitHub Star Score from GitHub API
  function initGitHubStarCounter() {
    const desktopBadge = document.getElementById('ghStarCountDesktop') || document.querySelector('.star-badge-count');
    const mobileBadge = document.getElementById('ghStarCountMobile') || document.querySelector('.mobile-star-badge');
    const REPO = 'ursishant/web-stories-templates-studio';
    const CACHE_KEY = 'wst_stars_count';
    const CACHE_TIME_KEY = 'wst_stars_ts';
    const CACHE_TTL = 10 * 60 * 1000; // 10 minutes cache

    function updateBadges(count) {
      if (typeof count !== 'number' || isNaN(count)) return;
      let label = '★ ' + count;
      if (count >= 1000) {
        label = '★ ' + (count / 1000).toFixed(1).replace(/\.0$/, '') + 'k';
      }
      if (desktopBadge) desktopBadge.textContent = label;
      if (mobileBadge) mobileBadge.textContent = label;
      document.querySelectorAll('.js-gh-stars').forEach(el => el.textContent = label);
    }

    try {
      const cached = localStorage.getItem(CACHE_KEY);
      const cachedTs = localStorage.getItem(CACHE_TIME_KEY);
      if (cached && cachedTs && (Date.now() - parseInt(cachedTs, 10) < CACHE_TTL)) {
        updateBadges(parseInt(cached, 10));
      }
    } catch (e) {}

    fetch(`https://api.github.com/repos/${REPO}`, {
      headers: { 'Accept': 'application/vnd.github.v3+json' }
    })
      .then(res => {
        if (!res.ok) throw new Error('HTTP ' + res.status);
        return res.json();
      })
      .then(data => {
        if (typeof data.stargazers_count === 'number') {
          updateBadges(data.stargazers_count);
          try {
            localStorage.setItem(CACHE_KEY, String(data.stargazers_count));
            localStorage.setItem(CACHE_TIME_KEY, String(Date.now()));
          } catch (e) {}
        }
      })
      .catch(err => {
        console.debug('GitHub Stars API rate limit or offline fallback:', err);
      });
  }

  initGitHubStarCounter();

  // ───────────────────────────────────────────────────────────────────────────
  // 17. Fullscreen 9:16 Immersive Story Viewer Modal System
  // ───────────────────────────────────────────────────────────────────────────
  const immersiveModal = document.getElementById('immersiveStoryModal');
  const btnLaunchImmersive = document.getElementById('btnLaunchImmersive');
  const btnCloseImmersive = document.getElementById('btnCloseImmersive');
  const immPhoneScreen = document.getElementById('immPhoneScreen');
  const immPubTitle = document.getElementById('immPubTitle');
  const immSlideCounter = document.getElementById('immSlideCounter');
  const immStoryBadge = document.getElementById('immStoryBadge');
  const immStoryHeading = document.getElementById('immStoryHeading');
  const immStoryBody = document.getElementById('immStoryBody');
  const immCtaText = document.getElementById('immCtaText');
  const immPauseIndicator = document.getElementById('immPauseIndicator');
  const immTapLeft = document.getElementById('immTapLeft');
  const immTapRight = document.getElementById('immTapRight');
  const immBtnPrev = document.getElementById('immBtnPrev');
  const immBtnNext = document.getElementById('immBtnNext');
  const immLaserToggle = document.getElementById('immLaserToggle');
  const immLaserGuide = document.getElementById('immLaserGuide');

  let immActiveKey = 'tech';
  let immCurrentSlide = 0;
  let immProgress = 0;
  let immLastTimestamp = null;
  let isImmPaused = false;
  let immRafId = null;

  function renderImmSlide() {
    const data = ARCHETYPES[immActiveKey];
    if (!data) return;
    const slide = data.slides[immCurrentSlide];
    if (!slide) return;

    if (immPhoneScreen) immPhoneScreen.style.backgroundImage = `url('${slide.bg}')`;
    if (immPubTitle) immPubTitle.textContent = data.publisher;
    if (immSlideCounter) immSlideCounter.textContent = `${immCurrentSlide + 1} / ${data.slides.length}`;
    if (immStoryBadge) {
      immStoryBadge.textContent = data.tag;
      immStoryBadge.style.backgroundColor = data.color;
    }
    if (immStoryHeading) {
      immStoryHeading.textContent = slide.headline;
      immStoryHeading.style.fontFamily = data.font;
    }
    if (immStoryBody) immStoryBody.textContent = slide.body;
    if (immCtaText) immCtaText.textContent = slide.cta;

    updateImmBars();
  }

  function updateImmBars() {
    if (!immersiveModal) return;
    const segs = immersiveModal.querySelectorAll('#immStoryBars .story-bar-segment');
    segs.forEach((seg, idx) => {
      seg.classList.remove('active', 'completed');
      const fill = seg.querySelector('.story-bar-fill');
      if (idx < immCurrentSlide) {
        seg.classList.add('completed');
        if (fill) fill.style.width = '100%';
      } else if (idx === immCurrentSlide) {
        seg.classList.add('active');
        if (fill) fill.style.width = `${Math.min(100, Math.max(0, immProgress * 100))}%`;
      } else {
        if (fill) fill.style.width = '0%';
      }
    });
  }

  function goToImmSlide(targetIndex) {
    const data = ARCHETYPES[immActiveKey];
    if (!data) return;
    const total = data.slides.length;
    immCurrentSlide = (targetIndex + total) % total;
    immProgress = 0;
    immLastTimestamp = null;
    renderImmSlide();
  }

  function immLoop(timestamp) {
    if (!immLastTimestamp) immLastTimestamp = timestamp;
    const delta = timestamp - immLastTimestamp;
    immLastTimestamp = timestamp;

    if (!isImmPaused && delta > 0 && delta < 500) {
      immProgress += delta / SLIDE_DURATION;
      if (immProgress >= 1) {
        goToImmSlide(immCurrentSlide + 1);
      } else {
        updateImmBars();
      }
    }

    if (immersiveModal && immersiveModal.classList.contains('is-open')) {
      immRafId = requestAnimationFrame(immLoop);
    }
  }

  function openImmersiveModal(archetypeKey, slideIndex = 0) {
    if (!immersiveModal) return;
    immActiveKey = archetypeKey || currentKey || 'tech';
    immCurrentSlide = slideIndex;
    immProgress = 0;
    immLastTimestamp = null;
    isImmPaused = false;

    renderImmSlide();
    immersiveModal.classList.add('is-open');
    document.body.classList.add('modal-open');

    if (immRafId) cancelAnimationFrame(immRafId);
    immRafId = requestAnimationFrame(immLoop);
  }

  function closeImmersiveModal() {
    if (!immersiveModal) return;
    immersiveModal.classList.remove('is-open');
    document.body.classList.remove('modal-open');
    if (immRafId) {
      cancelAnimationFrame(immRafId);
      immRafId = null;
    }
    isImmPaused = false;
    if (immPauseIndicator) immPauseIndicator.classList.remove('is-visible');
  }

  if (btnLaunchImmersive) {
    btnLaunchImmersive.addEventListener('click', () => {
      openImmersiveModal(currentKey, currentSlideIndex);
    });
  }

  if (btnCloseImmersive) {
    btnCloseImmersive.addEventListener('click', closeImmersiveModal);
  }

  if (immersiveModal) {
    immersiveModal.addEventListener('click', (e) => {
      if (e.target === immersiveModal) closeImmersiveModal();
    });
  }

  // Tap navigation inside modal
  if (immTapLeft) {
    immTapLeft.addEventListener('click', (e) => {
      e.stopPropagation();
      if (immProgress > 0.25) {
        immProgress = 0;
        updateImmBars();
      } else {
        goToImmSlide(immCurrentSlide - 1);
      }
    });
  }
  if (immBtnPrev) {
    immBtnPrev.addEventListener('click', (e) => {
      e.stopPropagation();
      goToImmSlide(immCurrentSlide - 1);
    });
  }
  if (immTapRight) {
    immTapRight.addEventListener('click', (e) => {
      e.stopPropagation();
      goToImmSlide(immCurrentSlide + 1);
    });
  }
  if (immBtnNext) {
    immBtnNext.addEventListener('click', (e) => {
      e.stopPropagation();
      goToImmSlide(immCurrentSlide + 1);
    });
  }

  // Hold to pause modal
  if (immPhoneScreen) {
    immPhoneScreen.addEventListener('pointerdown', () => {
      isImmPaused = true;
      if (immPauseIndicator) immPauseIndicator.classList.add('is-visible');
    });
    window.addEventListener('pointerup', () => {
      isImmPaused = false;
      immLastTimestamp = null;
      if (immPauseIndicator) immPauseIndicator.classList.remove('is-visible');
    });
    immPhoneScreen.addEventListener('pointerleave', () => {
      isImmPaused = false;
      immLastTimestamp = null;
      if (immPauseIndicator) immPauseIndicator.classList.remove('is-visible');
    });
  }

  // Laser toggle inside modal
  if (immLaserToggle && immLaserGuide) {
    immLaserToggle.addEventListener('change', () => {
      immLaserGuide.classList.toggle('is-active', immLaserToggle.checked);
    });
  }

  // Keyboard navigation
  window.addEventListener('keydown', (e) => {
    if (!immersiveModal || !immersiveModal.classList.contains('is-open')) return;
    if (e.key === 'Escape') {
      closeImmersiveModal();
    } else if (e.key === 'ArrowLeft') {
      goToImmSlide(immCurrentSlide - 1);
    } else if (e.key === 'ArrowRight') {
      goToImmSlide(immCurrentSlide + 1);
    } else if (e.key === ' ') {
      e.preventDefault();
      isImmPaused = !isImmPaused;
      if (immPauseIndicator) {
        immPauseIndicator.classList.toggle('is-visible', isImmPaused);
      }
    }
  });

  // Wire Lookbook Cards to Immersive Modal
  document.querySelectorAll('.lookbook-item-card').forEach(card => {
    const archKey = card.getAttribute('data-archetype') || 'tech';
    const previewBtn = card.querySelector('.btn-card-preview-hover');
    if (previewBtn) {
      previewBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        openImmersiveModal(archKey, 0);
      });
    }
    const mediaCover = card.querySelector('.card-cover-media');
    if (mediaCover) {
      mediaCover.addEventListener('click', (e) => {
        // Only trigger if click wasn't on another button/link
        if (!e.target.closest('button') && !e.target.closest('a')) {
          openImmersiveModal(archKey, 0);
        }
      });
    }
  });

  // Initial Render
  renderStudioSlide();
});
