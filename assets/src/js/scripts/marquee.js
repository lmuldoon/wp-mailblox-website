import { gsap } from 'gsap';

/**
 * Two-row auto-scrolling email examples marquee.
 * Row 1 scrolls left, Row 2 scrolls right.
 * Hover on either row decelerates both smoothly.
 */
export function initMarquee() {
	const rows = document.querySelectorAll('.marquee-track');
	if (!rows.length) return;

	const speed = 40; // px per second
	const tweens = [];

	rows.forEach((track, i) => {
		// Each track already has items duplicated in the HTML for seamless loop.
		// Measure the width of one set (half the total).
		const totalWidth = track.scrollWidth / 2;
		const direction = i % 2 === 0 ? -1 : 1; // row 0 → left, row 1 → right
		const duration = totalWidth / speed;

		const startX = direction === -1 ? 0 : -totalWidth;
		const endX   = direction === -1 ? -totalWidth : 0;

		gsap.set(track, { x: startX });

		const tween = gsap.to(track, {
			x: endX,
			duration,
			ease: 'none',
			repeat: -1,
			modifiers: {
				x: gsap.utils.unitize(x => parseFloat(x) % totalWidth),
			},
		});

		tweens.push(tween);
	});

	// Hover: decelerate to 10 % speed, release: return to full speed
	const wrapper = document.querySelector('.section--marquee');
	if (!wrapper) return;

	wrapper.addEventListener('mouseenter', () => {
		tweens.forEach(t => gsap.to(t, { timeScale: 0.1, duration: 0.6, ease: 'power2.out' }));
	});

	wrapper.addEventListener('mouseleave', () => {
		tweens.forEach(t => gsap.to(t, { timeScale: 1, duration: 0.8, ease: 'power2.inOut' }));
	});
}
