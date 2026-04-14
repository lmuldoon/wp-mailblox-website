<?php

/**
 * Header for the site.
 */

global $meta;

?>
<!DOCTYPE html>
<html class="no-js" lang="en-gb">

<head>
	<!-- Swap no-js → js synchronously so CSS can pre-hide animated elements before first paint -->
	<script>
		document.documentElement.classList.replace('no-js', 'js');
	</script>
	<?php $DOMAIN = 'wp-mailblox.com';
	$re = "/^(?:www\.)?" . str_replace('.', "\.", $DOMAIN) . "$/"; // escape dots
	$IS_LIVE = preg_match($re, $_SERVER['SERVER_NAME']);
	?>
	<?php if ($IS_LIVE) { ?>
		<!-- <script src="https://cdn.cookiehub.eu/c2/eb0a2c04.js"></script>
		<script type="text/javascript">
			window.dataLayer = window.dataLayer || [];

			function gtag() {
				dataLayer.push(arguments);
			}
			gtag('consent', 'default', {
				'security_storage': 'granted',
				'functionality_storage': 'denied',
				'personalization_storage': 'denied',
				'ad_storage': 'denied',
				'ad_user_data': 'denied',
				'ad_personalization': 'denied',
				'analytics_storage': 'denied',
				'wait_for_update': 500
			});
			document.addEventListener("DOMContentLoaded", function(event) {
				var cpm = {};
				window.cookiehub.load(cpm);
			});
		</script> -->
	<?php  } ?>

	<?php if (isset($meta->noindex) && $meta->noindex) : ?>
		<meta name="robots" content="noindex">
	<?php endif; ?>

	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<title><?php echo $meta->title; ?></title>
	<meta name="description" content="<?php echo $meta->description; ?>">

	<!-- Start Favicons -->
	<link rel="icon" type="image/png" href="/favicon-96x96.png" sizes="96x96" />
	<link rel="icon" type="image/svg+xml" href="/favicon.svg" />
	<link rel="shortcut icon" href="/favicon.ico" />
	<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png" />
	<meta name="apple-mobile-web-app-title" content="WP Mailblox" />
	<link rel="manifest" href="/site.webmanifest" />
	<!-- End Favicons -->

	<link rel="stylesheet" type="text/css" href="/<?php echo get_revision('screen.css'); ?>">
	<link rel="stylesheet" type="text/css" href="/<?php echo get_revision('print.css'); ?>">

	<script type="text/javascript" src="/<?php echo get_revision('header.js'); ?>"></script>
	<?php if ($IS_LIVE) { ?>
		<!-- Google tag (gtag.js)
		<script async src="https://www.googletagmanager.com/gtag/js?id=G-LQVT7QDPPS"></script>
		<script>
			window.dataLayer = window.dataLayer || [];

			function gtag() {
				dataLayer.push(arguments);
			}
			gtag('js', new Date());

			gtag('config', 'G-LQVT7QDPPS');
		</script> -->
	<?php } ?>
</head>
<?php
$is_single_page = strpos($meta->slug, 'news/') === 0;
?>

<body class="page page-<?php echo $meta->slug; ?> <?php echo $is_single_page ? 'page-news-single' : ''; ?>" id="top">
	<a href="#maincontent" class="skip-link">Skip to main content</a>

	<?php // Load all SVG icons as hidden spritesheet 
	?>
	<div hidden>
		<?php include_asset('static/icons.svg'); ?>
	</div>
	<header class="site-header js-site-header" role="banner" id="js-site-header">
		<div class="site-header__inner">
			<div class="container">

				<div class="site-header__bar">
					<a href="/" class="site-logo" aria-label="WP Mailblox home">
						<?php include_asset('static/logo.svg'); ?>
					</a>

					<!-- Desktop nav — hidden on mobile -->
					<nav class="site-nav--desktop" aria-label="Main navigation">
						<ul class="site-menu">
							<li><a class="site-menu__link" href="/#features">Features</a></li>
							<li><a class="site-menu__link" href="/#pricing">Pricing</a></li>
							<li><a class="site-menu__link<?php echo $meta->slug === 'docs' ? ' is-active' : ''; ?>" href="/docs">Docs</a></li>
							<li><a class="site-menu__link<?php echo $meta->slug === 'changelog' ? ' is-active' : ''; ?>" href="/changelog">Changelog</a></li>
							<li><a class="button button--nav js-get-pro" href="#">Get Pro</a></li>
						</ul>
					</nav>

					<button class="hamburger" aria-label="Toggle navigation" aria-expanded="false" id="js-hamburger">
						<span class="hamburger__line"></span>
						<span class="hamburger__line"></span>
						<span class="hamburger__line"></span>
					</button>
				</div>

			</div>
		</div>
	</header> <!-- /.site-header -->

	<!-- Mobile nav overlay — outside the header so it has its own stacking context -->
	<nav class="site-nav--mobile" id="js-mobile-nav" aria-label="Mobile navigation" aria-hidden="true">
		<ul class="site-menu">
			<li><a class="site-menu__link" href="/#features">Features</a></li>
			<li><a class="site-menu__link" href="/#pricing">Pricing</a></li>
			<li><a class="site-menu__link<?php echo $meta->slug === 'docs' ? ' is-active' : ''; ?>" href="/docs">Docs</a></li>
			<li><a class="site-menu__link<?php echo $meta->slug === 'changelog' ? ' is-active' : ''; ?>" href="/changelog">Changelog</a></li>
		</ul>
		<a class="button button--nav js-get-pro" href="#">Get Pro</a>
	</nav>

	<main id="maincontent" class="site-main" role="main">