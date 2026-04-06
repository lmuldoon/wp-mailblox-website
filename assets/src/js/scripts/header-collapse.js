import {throttle} from "./__event-utilities";

const ANIMATION_DURATION = 200;

let $siteHeader = $('.js-site-header');
let isPaused = false;
let state = 'expanded';

function init() {
	updateState();
	$(window).scroll(throttle(function(event) {
		updateState();
	}, 25));
}

function updateState() {
	
	if ( isPaused ) {
		return;
	}

	let newState = $(window).scrollTop() > 70 ? 'collapsed' : 'expanded';

	if ( state != newState ) {
		$siteHeader.attr('data-state', newState);
		$siteHeader.trigger('state-change-start', [newState]);
		setTimeout(function() {
			$siteHeader.trigger('state-change-end', [newState]);
		}, ANIMATION_DURATION);

		state = newState;
	}
}

function pause() {
	isPaused = true;
}

function play() {
	isPaused = false;
}

export const HeaderStateController = {
	init,
	pause,
	play,
	isPaused: function() {
		return isPaused;
	}
};
