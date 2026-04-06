function lazyLoadFrame($target) {
	let $frame = jQuery($target.data('lazy-frame'));

	$target.find('img').remove();
	$target.append($frame);

	$target.removeAttr('data-lazy-frame');
	$target.find('button').remove();
}

(function ($) {

    let $lazyFrames = jQuery('[data-lazy-frame]');

	if ($lazyFrames.length) {
		$lazyFrames.click(function (event) {
			let $target = jQuery(event.target).closest('[data-lazy-frame]');

			lazyLoadFrame($target);

			$lazyFrames = $lazyFrames.not($target);
		});
	}

})(jQuery);