<?php

/**
 * The home page template.
 */

global $meta;

$meta->title = 'WP Mailblox — Email Builder for WordPress';
$meta->description = 'Build beautiful, email-safe HTML emails inside WordPress. Design with Gutenberg, export clean HTML, or push directly to Mailchimp, Brevo, Klaviyo and more.';

get_header();

?>

<!-- =============================================
     HERO
============================================= -->
<div class="hero-wrapper">
	<div class="hero hero--home parallax-up">
		<div id="main-title" class="headline hero__content">
			<div class="animated-up">
				<p class="hero__eyebrow">WordPress Email Builder</p>
				<h1>Build emails in&nbsp;WordPress.<br>Send them&nbsp;anywhere.</h1>
				<p class="hero__sub">Design professional, email-safe HTML templates using Gutenberg blocks. Define your brand once with Presets. Export clean code or push directly to your email platform — no third-party builder required.</p>
				<div class="hero__actions flex flex-wrap gap-4 items-center">
					<a class="button button--primary" href="https://wordpress.org/plugins/wp-mailblox/" target="_blank" rel="noopener">
						Download Free
						<svg aria-hidden="true" focusable="false">
							<use href="#arrow" />
						</svg>
					</a>
					<a class="button button--ghost" href="/docs">
						Read the Docs
						<svg aria-hidden="true" focusable="false">
							<use href="#arrow" />
						</svg>
					</a>
				</div>
			</div>
		</div>
		<div class="hero-img">
			<div class="img-placeholder" aria-hidden="true">
				<span>Plugin screenshot</span>
			</div>
		</div>
	</div>
</div>

<article>

	<!-- =============================================
	     INTRODUCTION
	============================================= -->
	<div class="section section--intro">
		<div class="container">
			<div class="text-container mx-auto text-center animated-up">
				<h2>Your email workflow, inside WordPress</h2>
				<div class="line mx-auto"></div>
				<p>WP Mailblox is a native WordPress email builder that lets you design professional HTML emails using familiar Gutenberg blocks. Set up brand Presets once — fonts, colours, logo — and every email you build inherits them automatically. When you're ready to send, export clean, email-client-safe HTML or push your template directly to your email platform with one click.</p>
			</div>
		</div>
	</div>

	<!-- =============================================
	     FEATURES GRID
	============================================= -->
	<div class="section bg-grey" id="features">
		<div class="container">
			<div class="text-center mb-12 animated-up">
				<h2>Everything you need to build great emails</h2>
				<div class="line mx-auto"></div>
			</div>
			<div class="features-grid animated-up">

				<div class="feature-card">
					<div class="feature-card__icon" aria-hidden="true">
						<iconify-icon icon="material-symbols:edit-square-outline-rounded"></iconify-icon>
					</div>
					<h3>Gutenberg-native builder</h3>
					<p>Design emails using the block editor you already know. No new interface to learn — just drag, drop, and customise inside WordPress.</p>
				</div>

				<div class="feature-card">
					<div class="feature-card__icon" aria-hidden="true">
						<iconify-icon icon="material-symbols:tune-rounded"></iconify-icon>
					</div>
					<h3>Brand Presets</h3>
					<p>Define your logo, colours, fonts and default platform once. Every email you create inherits your brand automatically — change the Preset and every template updates.</p>
				</div>

				<div class="feature-card">
					<div class="feature-card__icon" aria-hidden="true">
						<iconify-icon icon="material-symbols:download-rounded"></iconify-icon>
					</div>
					<h3>One-click HTML export</h3>
					<p>Export perfectly structured, email-client-safe HTML at any time. Download the file or copy it to clipboard — ready to paste into any platform.</p>
				</div>

				<div class="feature-card">
					<div class="feature-card__icon" aria-hidden="true">
						<iconify-icon icon="material-symbols:send-rounded"></iconify-icon>
					</div>
					<h3>Direct platform push <span class="badge badge--pro">Pro</span></h3>
					<p>Send your finished template straight to Mailchimp, Brevo, Klaviyo, Campaign Monitor, ActiveCampaign or HubSpot — without leaving WordPress.</p>
				</div>

				<div class="feature-card">
					<div class="feature-card__icon" aria-hidden="true">
						<iconify-icon icon="material-symbols:dark-mode-outline-rounded"></iconify-icon>
					</div>
					<h3>Dark mode support</h3>
					<p>Full <code>prefers-color-scheme</code> support with Outlook-safe selectors. Set dark backgrounds per-section, override at template level, and swap in alternate images — including a separate dark mode logo — automatically.</p>
				</div>

				<div class="feature-card">
					<div class="feature-card__icon" aria-hidden="true">
						<iconify-icon icon="material-symbols:storefront-outline-rounded"></iconify-icon>
					</div>
					<h3>WooCommerce blocks <span class="badge badge--pro">Pro</span></h3>
					<p>Include live product data, order summaries, product recommendations and coupon codes directly in your email templates — powered by your store.</p>
				</div>

			</div>
		</div>
	</div>

	<!-- =============================================
	     HOW IT WORKS
	============================================= -->
	<div class="section" id="how-it-works">
		<div class="container">
			<div class="text-center mb-12 animated-up">
				<h2>Up and running in minutes</h2>
				<div class="line mx-auto"></div>
			</div>
			<div class="steps animated-up">

				<div class="step">
					<div class="step__number" aria-hidden="true">1</div>
					<div class="step__content flow">
						<h3>Install &amp; activate</h3>
						<p>Search for <strong>WP Mailblox</strong> in your WordPress plugin directory, install and activate. The onboarding wizard walks you through your first setup in under two minutes.</p>
					</div>
				</div>

				<div class="step__connector" aria-hidden="true"></div>

				<div class="step">
					<div class="step__number" aria-hidden="true">2</div>
					<div class="step__content flow">
						<h3>Configure your Preset</h3>
						<p>Upload your logo, set your brand colours and fonts, choose your email platform and enter your API credentials. Your Preset becomes the foundation for every email you build.</p>
					</div>
				</div>

				<div class="step__connector" aria-hidden="true"></div>

				<div class="step">
					<div class="step__number" aria-hidden="true">3</div>
					<div class="step__content flow">
						<h3>Build &amp; export</h3>
						<p>Create a new Email Template, pick a starter layout or start from blank, add your blocks, set the subject line and preheader, then export clean HTML or push straight to your platform.</p>
					</div>
				</div>

			</div>
		</div>
	</div>

	<!-- =============================================
	     PLATFORM INTEGRATIONS
	============================================= -->
	<div class="section section--platforms bg-darkblue" id="platforms">
		<div class="container">
			<div class="text-center mb-10 animated-up flow">
				<h2 class="text-white">Works with your email platform</h2>
				<p class="text-white" style="opacity:0.75;">Export clean HTML for any platform, or use direct push (Pro) to send your template straight to your tool of choice.</p>
			</div>
			<div class="platforms-grid animated-up">
				<div class="platform-badge">Mailchimp</div>
				<div class="platform-badge">Brevo</div>
				<div class="platform-badge">Klaviyo</div>
				<div class="platform-badge">Campaign Monitor</div>
				<div class="platform-badge">ActiveCampaign</div>
				<div class="platform-badge">HubSpot</div>
			</div>
			<div class="animated-up">
				<p class="text-center mt-6 animated-up" style="color: rgba(255,255,255,0.5); font-size: var(--size-300);">Direct platform push requires WP Mailblox Pro. HTML export works with any platform.</p>
			</div>
		</div>
	</div>

	<!-- =============================================
	     FREE VS PRO COMPARISON
	============================================= -->
	<div class="section" id="pricing">
		<div class="container">
			<div class="text-center mb-12 animated-up">
				<h2>Free to start. Pro when you're ready.</h2>
				<div class="line mx-auto"></div>
				<p class="text-container mx-auto">WP Mailblox is free to download and use. Upgrade to Pro for unlimited templates, multiple presets and direct platform push.</p>
			</div>

			<div class="pricing-cards animated-up">

				<div class="pricing-card flow">
					<div class="pricing-card__header">
						<h3>Free</h3>
						<div class="pricing-card__price">
							<span class="pricing-card__amount">$0</span>
							<span class="pricing-card__period">forever</span>
						</div>
					</div>
					<ul class="pricing-card__features">
						<li class="feature--yes">Gutenberg email builder</li>
						<li class="feature--yes">Up to 5 email templates</li>
						<li class="feature--yes">1 brand Preset</li>
						<li class="feature--yes">All standard blocks</li>
						<li class="feature--yes">HTML export (download &amp; copy)</li>
						<li class="feature--yes">Dark mode support</li>
						<li class="feature--yes">Mobile responsive controls</li>
						<li class="feature--yes">UTM tracking</li>
						<li class="feature--yes">Starter template library</li>
						<li class="feature--no">Direct platform push</li>
						<li class="feature--no">Multiple Presets</li>
						<li class="feature--no">WooCommerce blocks</li>
						<li class="feature--no">HTML / custom code block</li>
						<li class="feature--no">Remove WP Mailblox branding</li>
					</ul>
					<a class="button button--outline w-full text-center justify-center" href="https://wordpress.org/plugins/wp-mailblox/" target="_blank" rel="noopener">Download Free</a>
				</div>

				<div class="pricing-card pricing-card--pro flow">
					<div class="pricing-card__badge">Most Popular</div>
					<div class="pricing-card__header mt-0">
						<h3>Pro</h3>
						<div class="pricing-card__price">
							<span class="pricing-card__amount">From $9.99</span>
							<span class="pricing-card__period">/ month</span>
						</div>
						<div class="pricing-tiers">
							<div class="pricing-tier">
								<span class="pricing-tier__label">1 site</span>
								<span class="pricing-tier__prices">
									<span class="pricing-tier__monthly">$9.99<small>/mo</small></span>
									<span class="pricing-tier__sep">·</span>
									<span class="pricing-tier__annual">$79.99<small>/yr</small></span>
								</span>
							</div>
							<div class="pricing-tier">
								<span class="pricing-tier__label">5 sites</span>
								<span class="pricing-tier__prices">
									<span class="pricing-tier__monthly">$16.99<small>/mo</small></span>
									<span class="pricing-tier__sep">·</span>
									<span class="pricing-tier__annual">$159.99<small>/yr</small></span>
								</span>
							</div>
							<div class="pricing-tier">
								<span class="pricing-tier__label">Unlimited</span>
								<span class="pricing-tier__prices">
									<span class="pricing-tier__monthly">$24.99<small>/mo</small></span>
									<span class="pricing-tier__sep">·</span>
									<span class="pricing-tier__annual">$249.99<small>/yr</small></span>
								</span>
							</div>
						</div>
					</div>
					<ul class="pricing-card__features">
						<li class="feature--yes">Everything in Free</li>
						<li class="feature--yes">Unlimited email templates</li>
						<li class="feature--yes">Unlimited brand Presets</li>
						<li class="feature--yes">All standard blocks</li>
						<li class="feature--yes">HTML export (download &amp; copy)</li>
						<li class="feature--yes">Dark mode support</li>
						<li class="feature--yes">Mobile responsive controls</li>
						<li class="feature--yes">UTM tracking</li>
						<li class="feature--yes">Starter template library</li>
						<li class="feature--yes">Direct push to 6 platforms</li>
						<li class="feature--yes">Multiple Presets</li>
						<li class="feature--yes">WooCommerce blocks</li>
						<li class="feature--yes">HTML / custom code block</li>
						<li class="feature--yes">Remove WP Mailblox branding</li>
					</ul>
					<a class="button button--primary w-full text-center justify-center js-get-pro" href="#">Get Pro</a>
				</div>

			</div>

		</div>
	</div>

	<!-- =============================================
	     FAQ
	============================================= -->
	<div class="section bg-grey" id="faq">
		<div class="container">
			<div class="text-center mb-12 animated-up">
				<h2>Frequently asked questions</h2>
				<div class="line mx-auto"></div>
				<p class="text-container mx-auto">Everything you need to know before getting started. If you can't find what you're looking for, reach out via the <a href="https://wordpress.org/support/plugin/wp-mailblox/" target="_blank" rel="noopener">support forum on WordPress.org</a>.</p>
			</div>
			<div class="faq-layout animated-up">
				<div class="accordions-wrapper">

					<div class="accordion">
						<div class="js-accordion-trigger">
							<h3>Do I need to know HTML or code to use WP Mailblox?</h3>
						</div>
						<div class="accordion-content flow">
							<p>Not at all. WP Mailblox is built entirely around the WordPress block editor — the same interface you use to write posts and pages. If you can build a page in WordPress, you can build an email. There's no code to write and no new tool to learn.</p>
							<p>The plugin handles all the email-safe HTML behind the scenes — table-based layouts, Outlook fallbacks, responsive styles — so the output works properly in email clients without you having to think about it.</p>
						</div>
					</div>

					<div class="accordion">
						<div class="js-accordion-trigger">
							<h3>Who is WP Mailblox for?</h3>
						</div>
						<div class="accordion-content flow">
							<p>WP Mailblox is designed for anyone who manages a WordPress site and sends emails — whether you're a freelancer, a small business, a developer building for clients, or a marketing team that wants to own its email workflow.</p>
							<p>It's particularly useful if you work across multiple clients or brands (the Preset system keeps each brand's design separate and consistent), if you're already paying for an email platform but dislike its design tools, or if you want to produce polished emails without a monthly subscription to a dedicated builder.</p>
						</div>
					</div>

					<div class="accordion">
						<div class="js-accordion-trigger">
							<h3>I already use Mailchimp — why would I use WP Mailblox instead of its built-in editor?</h3>
						</div>
						<div class="accordion-content flow">
							<p>The Mailchimp editor is capable, but it's a separate tool with its own learning curve, its own asset library, and its own constraints. If your brand assets and content already live in WordPress, switching contexts every time you build an email creates unnecessary friction.</p>
							<p>WP Mailblox lets you design and manage email templates in the same place as everything else. Your Preset stores your brand settings so you're never hunting for a hex code or uploading a logo again. When you're done, you push the finished template directly to Mailchimp (Pro) or export the HTML — your choice. The email platform just handles delivery.</p>
						</div>
					</div>

					<div class="accordion">
						<div class="js-accordion-trigger">
							<h3>Does WP Mailblox send the emails?</h3>
						</div>
						<div class="accordion-content flow">
							<p>No — WP Mailblox is a design and export tool, not a sending platform. It produces the HTML template; your email platform (Mailchimp, Brevo, Klaviyo, etc.) handles subscriber lists, scheduling, delivery, and analytics.</p>
							<p>This is intentional. Email delivery is a complex problem that dedicated platforms solve well. WP Mailblox focuses on what WordPress does well — content creation and design — and leaves sending to the tools built for it.</p>
						</div>
					</div>

					<div class="accordion">
						<div class="js-accordion-trigger">
							<h3>Will my emails work in Outlook, Gmail, and Apple Mail?</h3>
						</div>
						<div class="accordion-content flow">
							<p>Yes. WP Mailblox generates email-safe HTML using table-based layouts and includes MSO conditional comments specifically for Outlook on Windows — historically the most challenging email client to support. Responsive styles are applied via media queries for clients that support them.</p>
							<p>Dark mode is handled with both <code>prefers-color-scheme</code> media queries and <code>[data-ogsc]</code> attribute selectors for Outlook's dark mode. We recommend testing in a tool like Litmus or Email on Acid before sending to a large list, but the output is built to the same standards used by professional email developers.</p>
						</div>
					</div>

					<div class="accordion">
						<div class="js-accordion-trigger">
							<h3>What happens to my templates if I cancel my Pro licence?</h3>
						</div>
						<div class="accordion-content flow">
							<p>Your templates are stored as standard WordPress posts and are never deleted when a licence changes. If you cancel Pro and return to the free plan, you retain access to your first 5 templates and 1 Preset. Templates beyond those limits remain in your database — they're just not editable until you either upgrade again or reduce your active count.</p>
							<p>You can also export any template as HTML at any time, so your work is never locked in.</p>
						</div>
					</div>

					<div class="accordion">
						<div class="js-accordion-trigger">
							<h3>Do I need WooCommerce to use WP Mailblox?</h3>
						</div>
						<div class="accordion-content flow">
							<p>No. WooCommerce is only required for the WooCommerce-specific blocks (Product, Order Table, Product Recommendations, Coupon) which are Pro features. All other blocks and the full core plugin work independently of WooCommerce.</p>
							<p>If WooCommerce isn't active, the WooCommerce blocks simply won't appear in the block inserter.</p>
						</div>
					</div>

					<div class="accordion">
						<div class="js-accordion-trigger">
							<h3>Is my data safe? Does WP Mailblox send anything to external servers?</h3>
						</div>
						<div class="accordion-content flow">
							<p>WP Mailblox does not collect or transmit any data from your website visitors. All email templates are stored locally in your WordPress database.</p>
							<p>When you use the direct platform push feature (Pro), your template HTML is sent to the relevant email platform's API — the same as if you'd copy-pasted the code in yourself. No subscriber or customer data is ever transmitted. The plugin uses <a href="https://freemius.com" target="_blank" rel="noopener">Freemius</a> for licence management, which may collect anonymised diagnostic data on activation (opt-out available). See our <a href="/terms-privacy">Privacy Policy</a> for full details.</p>
						</div>
					</div>

				</div>
			</div>
		</div>
	</div>

	<!-- =============================================
	     BOTTOM CTA
	============================================= -->
	<div class="section section--cta bg-midblue">
		<div class="container">
			<div class="text-center animated-up flow">
				<h2 class="text-white">Start building better emails today</h2>
				<p style="color: rgba(255,255,255,0.75);" class="text-container mx-auto">WP Mailblox is free to download from the WordPress plugin directory. No account required, no strings attached.</p>
				<div class="flex flex-wrap gap-4 justify-center mt-8">
					<a class="button button--ghost" href="https://wordpress.org/plugins/wp-mailblox/" target="_blank" rel="noopener">
						Download Free
						<svg aria-hidden="true" focusable="false">
							<use href="#arrow" />
						</svg>
					</a>
					<a class="button button--ghost-light" href="/docs">
						Read the Docs
						<svg aria-hidden="true" focusable="false">
							<use href="#arrow" />
						</svg>
					</a>
				</div>
			</div>
		</div>
	</div>

</article>

<?php get_footer(); ?>