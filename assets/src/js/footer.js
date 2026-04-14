import {
	debounce,
	throttle
} from "./scripts/__event-utilities";
import 'iconify-icon';

import {
	slick
} from "slick-carousel";
import Iconify from '@iconify/iconify';

import {
	HeaderStateController
} from './scripts/header-collapse';
HeaderStateController.init();

import {
	MenuController
} from './scripts/nav-toggle';
MenuController.init();

import {
	initAnimations,
	scrollToBottom
} from './scripts/animations';
initAnimations();

import { initEmailClient } from './scripts/email-client';
initEmailClient();


require('./scripts/init.tabs')
require('./scripts/accordian-controller')
import './scripts/lazyload-frame';

import GLightbox from 'glightbox';

// GLightbox
//------------------------------------------------------------------------------------------------------------------------------------------------

const galleryLightbox = GLightbox({
	selector: '.glightbox-gallery',
	openEffect: 'fade',
	closeEffect: 'fade',
	slideEffect: 'fade',
	dragable: false,
	loop: true,
	touchNavigation: false,
	zoomable: true,
});

const locationLightbox = GLightbox({
	selector: '.gl-single-image',
	loop: false,
	zoomable: true,
});


const inlineLightbox = GLightbox({
	selector: '.glightbox-inline',
	loop: false, // prevents looping to the next/previous modal
	closeButton: true, // show close button
	touchNavigation: false,
	dragable: false,
	openEffect: 'fade',
	closeEffect: 'fade',
	slideEffect: 'fade', // ensures no sliding effect that implies carousel
	moreText: false, // optional, disables any "more" links
	width: '1000px',
	onSlideChange: function () {
		// this will only run if multiple slides exist; with separate modals, nothing happens
	}
});

// AOS Animations
//------------------------------------------------------------------------------------------------------------------------------------------------

import AOS from 'aos';

let $siteHeader = $('.site-header');
let $mainTitle = $('#main-title');

jQuery(document).ready(() => {
	AOS.init({
		offset: 100,
		delay: 75,
		duration: 900,
		once: true,
	})



	storeHeaderAndFooterHeight();
});


function storeHeaderAndFooterHeight() {
	$('body').css('--header-height', $siteHeader.outerHeight(false));
	$('body').css('--main-title-height', $mainTitle.outerHeight(false));
}



$(window).on('resize', debounce(function (event) {
	storeHeaderAndFooterHeight();
}, 150));

$(window).on('scroll', debounce(function (event) {
	storeHeaderAndFooterHeight();
}, 100));




// Uncomment to use SVG icon buttons
const prevArrowHTML = `
<button type="button" class="slick-prev" aria-label="Previous slide">
  <svg aria-hidden="true" focusable="false">
    <use href="#arrow"></use>
  </svg>
</button>`;

const nextArrowHTML = `
<button type="button" class="slick-next" aria-label="Next slide">
  <svg aria-hidden="true" focusable="false">
    <use href="#arrow"></use>
  </svg>
</button>`;
const defaultArgs = {
	autoplay: false,
	dots: false,
	arrows: true,
	prevArrow: prevArrowHTML,
	nextArrow: nextArrowHTML,
	infinite: true,
	adaptiveHeight: false,
	draggable: false
};

const $imageCarousel = $('.js-image-carousel');

if ($imageCarousel.length) {

	$imageCarousel.slick({
		autoplay: false,
		dots: true,
		arrows: true,
		prevArrow: prevArrowHTML,
		nextArrow: nextArrowHTML,
		infinite: true,
		adaptiveHeight: false,
		draggable: true,
		slidesToShow: 1,
		centerMode: true,
		centerPadding: '17%',
		responsive: [{
			breakpoint: 768,
			settings: {
				centerMode: false,
				centerPadding: 0,
				arrows: false,
				adaptiveHeight: true
			}
		}]
	});

} // /$imageCarousel.length

const $cardCarousel = $('.js-card-carousel');

if ($cardCarousel.length) {

	$cardCarousel.slick({
		dots: false,
		arrows: true,
		prevArrow: prevArrowHTML,
		nextArrow: nextArrowHTML,
		infinite: false,
		slidesToShow: 2,
		slidesToScroll: 2,
		accessibility: true,
		variableWidth: false,
		focusOnSelect: false,
		centerMode: false,
		appendArrows: $('.feature-block__title-text .js-card-carousel-arrows'),
		responsive: [
			{
				breakpoint: 600,
				settings: {
					slidesToShow: 1,
					slidesToScroll: 1
				}
			},
		]
	});

} // /$cardCarousel.length


$(function () {
	$('.js-carousel').slick({
		dots: false,
		arrows: true,
		prevArrow: prevArrowHTML,
		nextArrow: nextArrowHTML,
		appendArrows: '.gallery-arrows',
		infinite: false,
		slidesToShow: 3,
		accessibility: true,
		variableWidth: false,
		focusOnSelect: false,
		centerMode: false,
		responsive: [{
				breakpoint: 992,
				settings: {
					slidesToShow: 2
				}
			},
			{
				breakpoint: 600,
				settings: {
					slidesToShow: 1
				}
			}
		]
	});
	$('.js-summary-items').slick({
		dots: false,
		arrows: true,
		prevArrow: prevArrowHTML,
		nextArrow: nextArrowHTML,
		appendArrows: '.summary-items-arrows',
		infinite: false,
		slidesToShow: 2,
		accessibility: true,
		variableWidth: false,
		focusOnSelect: false,
		centerMode: false,
		responsive: [{
			breakpoint: 1024,
			settings: {
				slidesToShow: 1
			}
		}]
	});
});

(function ($) {

	$('a[href*="#"]')
		.not('[href="#"]')
		.not('[href="#0"]')
		.not('[role="tab"]')
		.not('.glightbox-inline')
		.on('click', function (event) {

			if (
				location.pathname.replace(/^\//, '') === this.pathname.replace(/^\//, '') &&
				location.hostname === this.hostname
			) {

				let target = $(this.hash);
				target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');

				if (!target.length) return;

				event.preventDefault();

				const $header = $('.js-site-header');

				// Disable transitions temporarily
				$header.addClass('is-measuring');

				// Force collapsed state
				$header.attr('data-state', 'collapsed');

				// FORCE reflow (this is the key part)
				void $header[0].offsetHeight;

				// Now height is correct and final
				const headerHeight = Math.ceil($header.outerHeight(true));
				console.log(headerHeight);
				// Re-enable transitions
				$header.removeClass('is-measuring');

				$('html, body').animate({
					scrollTop: target.offset().top - headerHeight + 1
				}, 400, function () {

					const heading = $(target[0]).find('h2').get(0);
					if (!heading) return;

					heading.focus({ preventScroll: true });

					if (heading !== document.activeElement) {
						heading.setAttribute('tabindex', '-1');
						heading.focus({ preventScroll: true });
					}
				});
			}
		});

})(jQuery);

(function ($) {

	const $sections = $('.menu-section');
	const $header = $('.site-header');
	const $nav = $('.site-menu li a');

	function updateActiveNav() {

		const headerHeight = $header.outerHeight();
		const scrollPosition = $(window).scrollTop() + headerHeight + 5;

		let currentSection = null;

		$sections.each(function () {

			if ($(this).offset().top <= scrollPosition) {
				currentSection = $(this);
			}
		});

		// If no section matched (we're above the first one)
		if (!currentSection) {
			$nav.removeClass('is-current');
			return;
		}

		const sectionId = currentSection.attr('id');

		$nav.removeClass('is-current');
		$('.site-menu li a.' + sectionId).addClass('is-current');
	}

	$(window).on('load scroll', updateActiveNav);

})(jQuery);