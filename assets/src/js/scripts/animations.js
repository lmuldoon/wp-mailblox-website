import {
	throttle,
	debounce
} from "./__event-utilities";
import {
	gsap
} from "gsap";
import {
	ScrollTrigger
} from "gsap/ScrollTrigger";



export function initAnimations() {

	init();
	setUpHeader();
	setUpFooter();

	animateHero();
	animateElements();
	animatePricingCards();
	initMagneticButtons();

	$('html').removeClass('no-gsap').addClass('gsap');

}

function init() {
	gsap.registerPlugin(ScrollTrigger);

	gsap.config({
		nullTargetWarn: false,
		force3D: false
	});
	gsap.defaults({
		ease: "power2.out",
		duration: 0.75
	});

	ScrollTrigger.config({
		limitCallbacks: true,
	});

	// refresh trigger points for Safari to calculate correct height of footer
	setTimeout(function () {
		ScrollTrigger.refresh();
	}, 1000);
}

function setUpHeader() {
	const $siteHeader = $('#js-site-header');

	function storeHeaderHeight() {
		$('html').css('--header-height', `${Math.ceil($siteHeader.outerHeight(true))}px`);
	}

	storeHeaderHeight();
	$(window).on('resize', debounce(storeHeaderHeight, 150));
	$(window).on('scroll', throttle(storeHeaderHeight, 100));
}

function setUpFooter() {
	const $siteFooter = $('.site-footer');

	function storeFooterHeight() {
		$('html').css('--footer-height', `${Math.ceil($siteFooter.outerHeight(true))}px`);
	}

	storeFooterHeight();
}


// ─── HERO ──────────────────────────────────────────────────────────────────────
// Word-by-word title reveal + eyebrow + subtitle + CTA + image

function animateHero() {
	const h1 = document.querySelector('.hero--home h1');
	const eyebrow = document.querySelector('.hero__eyebrow');
	const sub = document.querySelector('.hero__sub');
	const actions = document.querySelector('.hero__actions');
	const heroImg = document.querySelector('.hero-img img, .hero-img .img-placeholder');

	// ── Split H1 into word spans ──────────────────────────────────────────────
	if (h1) {
		// Preserve <br> tags: split on them first, wrap words in each segment
		const rawHtml = h1.innerHTML;
		const lines = rawHtml.split(/<br\s*\/?>/i);
		const wrappedLines = lines.map(line =>
			line.replace(/(\S+)/g, (word) =>
				`<span class="hero-word"><span class="hero-word__inner">${word}</span></span>`
			)
		);
		h1.innerHTML = wrappedLines.join('<br>');

		const wordInners = h1.querySelectorAll('.hero-word__inner');
		gsap.set(wordInners, { y: '115%' });

		gsap.to(wordInners, {
			y: '0%',
			stagger: 0.055,
			duration: 0.75,
			ease: 'power3.out',
			delay: 0.55
		});
	}

	// ── Eyebrow ───────────────────────────────────────────────────────────────
	if (eyebrow) {
		gsap.from(eyebrow, {
			opacity: 0,
			y: 18,
			duration: 0.6,
			ease: 'power2.out',
			delay: 0.3
		});
	}

	// ── Sub-heading ───────────────────────────────────────────────────────────
	if (sub) {
		gsap.from(sub, {
			opacity: 0,
			y: 22,
			duration: 0.7,
			ease: 'power2.out',
			delay: 0.9
		});
	}

	// ── CTA buttons ───────────────────────────────────────────────────────────
	if (actions) {
		gsap.from(actions.children, {
			opacity: 0,
			y: 16,
			stagger: 0.1,
			duration: 0.6,
			ease: 'power2.out',
			delay: 1.05
		});
	}

	// ── Hero image ────────────────────────────────────────────────────────────
	if (heroImg) {
		gsap.fromTo(heroImg,
			{ opacity: 0, y: 36, scale: 0.97 },
			{ opacity: 1, y: 0, scale: 1, duration: 1.1, ease: 'power3.out', delay: 0.65 }
		);
	}

	// ── Header drop-in ────────────────────────────────────────────────────────
	const headerInner = document.querySelector('.js-site-header .site-header__inner');
	if (headerInner) {
		gsap.from(headerInner, {
			y: -40,
			opacity: 0,
			duration: 0.9,
			ease: 'power2.out',
			delay: 0.1
		});
	}
}


// ─── SCROLL REVEAL ─────────────────────────────────────────────────────────────

function animateElements() {

	// Parallax hero image scroll-out
	gsap.utils.toArray(".parallax-up").forEach(item => {
		const overlay = $(item).find('.hero--overlay');
		const img = $(item).find('img');

		gsap.to(overlay, {
			opacity: 1,
			ease: "none",
			scrollTrigger: {
				trigger: item,
				scrub: 0.2,
				start: "top top",
				end: "bottom 20%"
			}
		});

		gsap.to(img, {
			opacity: 0,
			scale: 1.2,
			ease: "none",
			scrollTrigger: {
				trigger: item,
				scrub: 0.2,
				start: "top top",
				end: "bottom 10%"
			}
		});
	});

	// ── .animated — horizontal slide-in ─────────────────────────────────────
	gsap.utils.toArray(".animated").forEach((elem) => {
		const imgs = $(elem).find("img");

		gsap.set(elem.children, { opacity: 0 });

		imgs.each(function () {
			const $img = $(this);
			const isCarousel = $img.closest('.js-card-carousel').length > 0;
			const isHero = $img.closest('.parallax-up').length > 0;
			if (!isCarousel && !isHero) {
				gsap.set($img, { scale: 1.08 });
			}
		});

		ScrollTrigger.create({
			trigger: elem,
			start: "top 90%",
			once: true,
			onEnter: () => {
				gsap.fromTo(
					elem.children,
					{ x: 40, opacity: 0 },
					{ x: 0, opacity: 1, stagger: 0.1, duration: 0.7, ease: 'power2.out', clearProps: "transform, opacity" }
				);

				imgs.each(function () {
					const $img = $(this);
					if (!$img.closest('.js-card-carousel').length) {
						gsap.to($img, { scale: 1, duration: 1, ease: "power2.out", clearProps: "transform" });
					}
				});
			},
			onEnterBack: () => {
				gsap.to(elem.children, { duration: 0.3, opacity: 1, stagger: 0.05 });
			}
		});
	});

	// ── .animated-up — upward fade-in, slightly staggered ───────────────────
	gsap.utils.toArray(".animated-up").forEach((elem) => {
		const children = elem.children;

		ScrollTrigger.create({
			trigger: elem,
			start: "top 88%",
			once: true,
			immediateRender: false,
			onEnter: () => {
				gsap.fromTo(children,
					{ y: 40, opacity: 0 },
					{
						y: 0, opacity: 1,
						stagger: 0.12,
						duration: 0.75,
						ease: "power3.out",
						clearProps: "transform, opacity"
					}
				);
			},
			onEnterBack: () => {
				gsap.to(children, { opacity: 1, duration: 0.3, stagger: 0.05 });
			}
		});
	});
}


// ─── PRICING CARDS ─────────────────────────────────────────────────────────────
// Custom entrance: Free card slides from left, Pro card from right with a glow pulse

function animatePricingCards() {
	const pricingSection = document.querySelector('.pricing-cards');
	if (!pricingSection) return;

	const [freeCard, proCard] = pricingSection.querySelectorAll('.pricing-card');

	if (!freeCard || !proCard) return;

	gsap.set(freeCard, { opacity: 0, x: -30 });
	gsap.set(proCard, { opacity: 0, x: 30 });

	ScrollTrigger.create({
		trigger: pricingSection,
		start: 'top 78%',
		once: true,
		onEnter: () => {
			const tl = gsap.timeline();

			tl.to(freeCard, {
				opacity: 1, x: 0,
				duration: 0.75, ease: 'power3.out'
			})
			.to(proCard, {
				opacity: 1, x: 0,
				duration: 0.75, ease: 'power3.out'
			}, '-=0.5')
			// Glow pulse on Pro card after it lands
			.fromTo(proCard, {
				boxShadow: '0 0 0 1px rgba(2,172,233,0.08), 0 32px 80px rgba(2,116,165,0.28), 0 8px 24px rgba(0,0,0,0.3)'
			}, {
				boxShadow: '0 0 0 1px rgba(2,172,233,0.25), 0 32px 100px rgba(2,116,165,0.5), 0 8px 24px rgba(0,0,0,0.3)',
				duration: 0.5, ease: 'power2.out', yoyo: true, repeat: 1
			}, '-=0.1');
		}
	});
}


// ─── MAGNETIC BUTTONS ──────────────────────────────────────────────────────────
// Subtle cursor-tracking pull on primary CTA buttons

function initMagneticButtons() {
	const buttons = document.querySelectorAll('.button--primary, .button--nav');

	buttons.forEach(btn => {
		btn.addEventListener('mousemove', (e) => {
			const rect = btn.getBoundingClientRect();
			const cx = rect.left + rect.width / 2;
			const cy = rect.top + rect.height / 2;
			const dx = e.clientX - cx;
			const dy = e.clientY - cy;

			gsap.to(btn, {
				x: dx * 0.18,
				y: dy * 0.18,
				duration: 0.35,
				ease: 'power2.out'
			});
		});

		btn.addEventListener('mouseleave', () => {
			gsap.to(btn, {
				x: 0, y: 0,
				duration: 0.6,
				ease: 'elastic.out(1, 0.55)'
			});
		});
	});
}


export function refreshAnimations() {
	ScrollTrigger.refresh();
}
