// Toggle accordion states

(function ($) {

	const ANIMATION_SPEED = 300;
	const BUTTON_HTML = '<button class="accordion-button" type="button" aria-label="Toggle accordion"><svg xmlns="http://www.w3.org/2000/svg" width="40.898" height="40.898" viewBox="0 0 40.898 40.898"><g transform="translate(-1314.5 -2098.5)"><path d="M1,40.9H-1V0H1Z" transform="translate(1334.949 2098.5)" fill="#96ae23"/><path d="M1,40.9H-1V0H1Z" transform="translate(1355.398 2118.949) rotate(90)" fill="#96ae23"/></g></svg></button>';
	let $wrappers = $('.accordions-wrapper');

	if (!$wrappers.length) {
		return;
	}


	/**
	 * Set up a11y attributes and add interactive elements, e.g. button
	 */
	function init() {
		$wrappers.each((index, wrapper) => {

			$triggers = $(wrapper).find('.js-accordion-trigger');

			$($triggers).each((index, trigger) => {

				$(trigger).append(BUTTON_HTML);
				$(trigger).next().hide();
			});

			open($($triggers[0]), $($triggers[0]).next());

		});

	}

	function open($trigger, $content) {
		let $button = $trigger.find('button');

		if (!isOpen($trigger)) {
			// Close currently open accordion content within the same group
			$trigger.siblings('.is-open').each(function () {
				close($(this), $(this).next());
			});

			$content.slideDown(ANIMATION_SPEED);
			$button.attr('aria-expanded', 'true');
			$content.attr('aria-hidden', 'false');

			$button.find('use').attr('href', '#minus');
			$trigger.addClass('is-open');

			$content.on('keyup.accordion', function (event) {
				if ('Escape' === event.key) {
					$button.focus();
					close($trigger, $content);
				}
			});
		}
	}

	function close($trigger, $content) {
		let $button = $trigger.find('button');

		$content.slideUp(ANIMATION_SPEED);
		$button.attr('aria-expanded', 'false');
		$content.attr('aria-hidden', 'true');

		$button.find('use').attr('href', '#plus');
		$trigger.removeClass('is-open');

		$content.off('keyup.accordion');
	}

	function isOpen($trigger) {
		let $button = $trigger.find('button');
		return $button.is('[aria-expanded="true"]');
	}

	function toggle($trigger, $content) {
		if (isOpen($trigger)) {
			close($trigger, $content);
		} else {
			open($trigger, $content);
		}
	}


	init();

	$wrappers.each((index, wrapper) => {


		$triggers = $(wrapper).find('.js-accordion-trigger');

		$triggers.on('click', function (event) {
			let $target = $(event.target);
			if (!$target.is('.js-accordion-trigger')) {
				$target = $target.closest('.js-accordion-trigger');
			}

			toggle($target, $target.next());
		});



	});

})(jQuery);