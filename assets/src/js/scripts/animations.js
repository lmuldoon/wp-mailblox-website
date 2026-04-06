import {
	throttle,
	debounce
} from "./__event-utilities";
import {
	gsap,
	random
} from "gsap";
import {
	ScrollTrigger
} from "gsap/ScrollTrigger";



export function initAnimations() {

	init();
	setUpHeader();
	setUpFooter();

	animateElements();

	$('html').removeClass('no-gsap').addClass('gsap');

}

function init() {
	gsap.registerPlugin(ScrollTrigger);

	gsap.config({
		nullTargetWarn: false,
		force3D: false
	});
	gsap.defaults({
		ease: "ease",
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

	// Initial calculation
	storeHeaderHeight();

	// Recalculate on resize
	$(window).on('resize', debounce(storeHeaderHeight, 150));

	// Recalculate on scroll, throttled
	$(window).on('scroll', throttle(storeHeaderHeight, 100));
}

function setUpFooter() {

	const $siteFooter = $('.site-footer');

	function storeFooterHeight() {
		$('html').css('--footer-height', `${Math.ceil($siteFooter.outerHeight(true))}px`);
	}

	storeFooterHeight();

}


function animateElements() {
	const $heroImg = $('.parallax-up img');

	$heroImg.each(function () {
		const $img = $(this);

		gsap.set($img, {
			scale: 1.3
		});

		gsap.to($img, {
			scale: 1,
			duration: 1.2,
			ease: "power2.out"
		});
	});

	

	gsap.utils.toArray(".parallax-up").forEach(item => {

		const content = $(item).find('.parallax-up--element');
		const overlay = $(item).find('.hero--overlay');
		const img = $(item).find('img');

		gsap.set(img, {
					scale: 1
				});

		// Move content
		// gsap.to(content, {
		// 	// opacity: 0,
		// 	yPercent: -50,
		// 	ease: "none",
		// 	scrollTrigger: {
		// 		trigger: item,
		// 		scrub: 0.2,
		// 		start: "top top",
		// 		end: "bottom 20%"
		// 	}
		// });

		// Fade overlay to opacity: 1
		gsap.to(overlay, {
			opacity: 1,
			ease: "none",
			scrollTrigger: {
				trigger: item,
				scrub: 0.2,
				start: "top top",
				end: "bottom 20%" // opacity reaches 1 when hero fully scrolled out
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
				end: "bottom 10%" // opacity reaches 1 when hero fully scrolled out
			}
		});

	});

	gsap.utils.toArray(".animated").forEach((elem, index) => {
		const imgs = $(elem).find("img");

		gsap.set(elem.children, {
			opacity: 0
		});

		imgs.each(function () {
			const $img = $(this);
			const isCarouselImage = $img.closest('.js-card-carousel').length > 0;
			const isHeroImage = $img.closest('.parallax-up').length > 0;

			// Only set initial scale if NOT inside a carousel AND NOT a hero image
			if (!isCarouselImage && !isHeroImage) {
				gsap.set($img, {
					scale: 1.1
				});
			}
		});

		ScrollTrigger.create({
			trigger: elem,
			start: "top 90%",
			once: true,

			onEnter: () => {
				gsap.fromTo(
					elem.children, {
						x: 50,
						opacity: 0
					}, {
						x: 0,
						opacity: 1,
						stagger: 0.1,
						clearProps: "transform, opacity"
					}
				);

				imgs.each(function () {
					const $img = $(this);
					const isCarouselImage = $img.closest('.js-card-carousel').length > 0;

					if (!isCarouselImage) {
						gsap.to($img, {
							scale: 1,
							duration: 1,
							ease: "power2.out",
							clearProps: "transform"
						});
					}
				});
			},

			onEnterBack: () => {
				gsap.to(elem.children, {
					duration: 0.3,
					opacity: 1,
					stagger: 0.05
				});

				imgs.each(function () {
					const $img = $(this);
					const isCarouselImage = $img.closest('.js-card-carousel').length > 0;

					if (!isCarouselImage) {
						gsap.to($img, {
							scale: 1,
							duration: 0.5
						});
					}
				});
			}
		});
	});

	gsap.utils.toArray(".animated-up").forEach((elem) => {
	const children = elem.children;

	ScrollTrigger.create({
		trigger: elem,
		start: "top 90%",
		once: true,
		immediateRender: false, // prevent initial transform before ScrollTrigger decides
		onEnter: () => {
			gsap.fromTo(children, 
				{ y: 50, opacity: 0 }, 
				{ y: 0, opacity: 1, stagger: 0.15, duration: 0.8, ease: "power2.out", clearProps: "transform, opacity" }
			);
		},
		onEnterBack: () => {
			gsap.to(children, { opacity: 1, duration: 0.3, stagger: 0.05 });
		}
	});
});

	const $header = $('.js-site-header');

	// Only animate if header exists
	if (!$header.length) return;

	const headerInner = $header.find('.site-header__inner');

	// Set initial state: move up and hidden
	gsap.set(headerInner, {
		y: -50, // move 50px above
		opacity: 0
	});

	// Animate down into view on page load
	gsap.to(headerInner, {
		y: 0,
		opacity: 1,
		duration: 1,
		ease: "power2.out"
	});


}

export function refreshAnimations() {
	ScrollTrigger.refresh();
}