/**
 * Functionality related to Featherlight library.
 */

(function( $ ) {

	$('[data-featherlight-tab]').click(function(event) {
		let target = $(this).data('featherlight-tab');


		let $image = $.featherlight.current().$content.find('#'+target);

		if ( $image.length ) {
			$('.featherlight [data-featherlight-tab]').removeClass('is-active');
			$(this).addClass('is-active');

			$image.siblings('div').addClass('hidden');
			$image.removeClass('hidden');
		} 
	});	


	const FLNavController = (function() {
		let isNavigating = false;

		const navHtml = `
			<div class="featherlight-nav">
				<button class="featherlight-prev" aria-label="Previous slide">
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" aria-hidden="true" focusable="false"><path d="M13.891 17.418c0.268 0.272 0.268 0.709 0 0.979s-0.701 0.271-0.969 0l-7.83-7.908c-0.268-0.27-0.268-0.707 0-0.979l7.83-7.908c0.268-0.27 0.701-0.27 0.969 0s0.268 0.709 0 0.979l-7.141 7.419 7.141 7.418z"></path></svg>
				</button>
				<button class="featherlight-next" aria-label="Next slide">
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" aria-hidden="true" focusable="false"><path d="M13.25 10l-7.141-7.42c-0.268-0.27-0.268-0.707 0-0.979 0.268-0.27 0.701-0.27 0.969 0l7.83 7.908c0.268 0.271 0.268 0.709 0 0.979l-7.83 7.908c-0.268 0.271-0.701 0.27-0.969 0s-0.268-0.707 0-0.979l7.141-7.417z"></path></svg>
				</button>
			</div> <!-- /.featherlight-nav -->`;

		function getContent( $group, index ) {
			
			let slideCount = $group.find('[data-slick-index]:not(.slick-cloned)').length - 1;

			// wrap around to end
			if ( index < 0 ) {
				index = slideCount;
			}

			// wrap around to start
			if ( index > slideCount ) {
				index = 0;
			}

			let $content = $group.find(`[data-slick-index="${index}"] .featherlight-target`);

			if ( !$content.length ) {
				$content = $group.find(`[data-slick-index="${index}"] [data-featherlight]`).attr('href');
			}

			return $content;
		}

		function appendNav( $target, featherlight ) {
			let $main = $.featherlight.current().$content.find('.featherlight-main');

			if ( !$main.length ) {
				$main = $.featherlight.current().$content.closest('.featherlight-content');
			}

			let $nav = $(navHtml);
			let $prev = $nav.find('.featherlight-prev');
			let $next = $nav.find('.featherlight-next');

			let $group = $target.closest('[data-featherlight-group]');
			let index = $target.closest('[data-slick-index]').data('slickIndex');

			$prev.click(function(event) {
				isNavigating = true;
				let variant = featherlight.current().variant;

				featherlight.current().close();
				featherlight(getContent($group, index - 1), {
					variant
				});

				isNavigating = false;
			});

			$next.click(function(event) {
				isNavigating = true;
				let variant = featherlight.current().variant;

				featherlight.current().close();
				featherlight(getContent($group, index + 1), {
					variant
				});

				isNavigating = false;
			});

			$main.append($nav);
		}

		return {
			appendNav,
			isNavigating: function() {
				return isNavigating;
			}
		};
	})();

	// extend featherlight
	$.featherlight.defaults.beforeOpen = function( event ) {
		if ( !$('.featherlight-shade').length ) {
			$('body').append('<div class="featherlight-shade"></div>');
		}
	};

	$.featherlight.defaults.beforeClose = function( event ) {
		if ( FLNavController.isNavigating() ) {
			return;
		}

		$('.featherlight-shade').remove();
	};

	$.featherlight.defaults.afterOpen = function( event ) {
		let $target = $.featherlight.current().target || $.featherlight.current().$currentTarget;

		console.log($target);

		if ( typeof $target === 'string' ) {
			$target = $(`[data-featherlight][href="${$target}"]`);
		}

		// check if targeted featherlight is part of group
		if ( !$target || !$target.length || !$target.closest('[data-featherlight-group]').length ) {
			return;
		}

		FLNavController.appendNav($target, $.featherlight);
	};

})( jQuery );
