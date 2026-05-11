export function initCheckout() {
	window.addEventListener('load', () => {
		if (!window.FS || !FS.Checkout) return;

		const checkout = new FS.Checkout({
			product_id: 27104,
			plan_id:    44844,
		});

		document.querySelectorAll('.js-get-pro').forEach(btn => {
			btn.addEventListener('click', (e) => {
				e.preventDefault();
				checkout.open({ name: 'WP Mailblox Pro' });
			});
		});
	});
}
