import { gsap } from 'gsap';
import {
	disableBodyScroll,
	enableBodyScroll
} from 'body-scroll-lock';

const targetElement = document.querySelector('#js-site-header');
let isOpen = false;
let isAnimating = false;

function init() {
	const $hamburger = $('#js-hamburger');

	$hamburger.on('click', function () {
		if (isAnimating) return;
		isOpen ? closeMenu() : openMenu();
	});

	// Close on Escape key
	$(document).on('keydown', function (e) {
		if (e.key === 'Escape' && isOpen) closeMenu();
	});
}

function openMenu() {
	isAnimating = true;
	isOpen = true;

	const $hamburger = $('#js-hamburger');
	const $nav = $('#js-mobile-nav');
	const $items = $('#js-mobile-nav .site-menu li');

	$hamburger.addClass('is-active').attr('aria-expanded', 'true');
	$nav.attr('aria-hidden', 'false');
	disableBodyScroll(targetElement, { reserveScrollBarGap: true });

	// Make visible before animating
	gsap.set($nav[0], { visibility: 'visible', pointerEvents: 'auto' });

	// Reset items to starting position
	gsap.set($items, { opacity: 0, x: -24 });

	const tl = gsap.timeline({
		onComplete: () => { isAnimating = false; }
	});

	// Fade panel in
	tl.to($nav[0], {
		opacity: 1,
		duration: 0.3,
		ease: 'power2.out'
	});

	// Stagger items in from the left
	tl.to($items, {
		opacity: 1,
		x: 0,
		stagger: 0.07,
		duration: 0.45,
		ease: 'power3.out',
		clearProps: 'x'
	}, '-=0.15');
}

function closeMenu() {
	isAnimating = true;
	isOpen = false;

	const $hamburger = $('#js-hamburger');
	const $nav = $('#js-mobile-nav');
	const $items = $('#js-mobile-nav .site-menu li');

	$hamburger.removeClass('is-active').attr('aria-expanded', 'false');
	$nav.attr('aria-hidden', 'true');
	enableBodyScroll(targetElement);

	const tl = gsap.timeline({
		onComplete: () => {
			isAnimating = false;
			gsap.set($nav[0], { visibility: 'hidden', pointerEvents: 'none' });
		}
	});

	// Stagger items out in reverse order
	tl.to($items, {
		opacity: 0,
		x: -16,
		stagger: { each: 0.04, from: 'end' },
		duration: 0.25,
		ease: 'power2.in'
	});

	// Fade panel out
	tl.to($nav[0], {
		opacity: 0,
		duration: 0.2,
		ease: 'power2.in'
	}, '-=0.1');
}

export const MenuController = {
	init,
	openMenu,
	closeMenu
};
