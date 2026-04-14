import { gsap } from 'gsap';

/**
 * Interactive mock email client section.
 * Auto-advances through inbox items on a timer; click to jump to any item.
 * Hover pauses the timer and progress bar; mouse-leave resumes.
 */
export function initEmailClient() {
	const client = document.querySelector('.email-client');
	if (!client) return;

	const items = Array.from(client.querySelectorAll('.inbox-item'));
	const previewPane = client.querySelector('.email-client__preview');
	const previewImg  = client.querySelector('.email-preview__img');
	if (!previewPane || !previewImg) return;

	const images = [
		'/static/images/email-examples/appy.jpg',
		'/static/images/email-examples/automotive.jpg',
		'/static/images/email-examples/b-fashion.jpg',
		'/static/images/email-examples/chic.jpg',
		'/static/images/email-examples/church.jpg',
		'/static/images/email-examples/honey.jpg',
		'/static/images/email-examples/itsuki.jpg',
		'/static/images/email-examples/ladies-fitness.jpg',
		'/static/images/email-examples/real-estate.jpg',
		'/static/images/email-examples/salt.jpg',
	];

	const DURATION = 7000; // ms per email
	let current = 0;
	let timer = null;
	let progressTween = null;

	function switchTo(index) {
		if (index === current) return;

		// Update sidebar active state
		items[current].classList.remove('is-active');
		items[current].setAttribute('aria-selected', 'false');
		items[index].classList.add('is-active');
		items[index].setAttribute('aria-selected', 'true');

		// Fade out → swap src + reset scroll → fade in
		gsap.to(previewImg, {
			opacity: 0,
			duration: 0.25,
			ease: 'power2.in',
			onComplete: () => {
				previewImg.src = images[index];
				previewPane.scrollTop = 0;
				gsap.to(previewImg, { opacity: 1, duration: 0.35, ease: 'power2.out' });
			},
		});

		current = index;
		startProgress();
		restartTimer();
	}

	function startProgress() {
		if (progressTween) progressTween.kill();

		// Reset all bars to zero
		items.forEach(item => {
			const bar = item.querySelector('.inbox-item__progress');
			if (bar) gsap.set(bar, { scaleX: 0 });
		});

		const activeBar = items[current].querySelector('.inbox-item__progress');
		if (!activeBar) return;

		progressTween = gsap.fromTo(activeBar,
			{ scaleX: 0 },
			{
				scaleX: 1,
				duration: DURATION / 1000,
				ease: 'none',
				transformOrigin: 'left center',
			}
		);
	}

	function restartTimer() {
		clearInterval(timer);
		timer = setInterval(() => {
			switchTo((current + 1) % items.length);
		}, DURATION);
	}

	// Click / keyboard handlers
	items.forEach((item, i) => {
		item.addEventListener('click', () => switchTo(i));
		item.addEventListener('keydown', e => {
			if (e.key === 'Enter' || e.key === ' ') {
				e.preventDefault();
				switchTo(i);
			}
		});
	});

	// Pause on hover, resume on leave
	client.addEventListener('mouseenter', () => {
		clearInterval(timer);
		if (progressTween) progressTween.pause();
	});

	client.addEventListener('mouseleave', () => {
		if (progressTween) progressTween.resume();
		restartTimer();
	});

	// Kick off
	startProgress();
	restartTimer();
}
