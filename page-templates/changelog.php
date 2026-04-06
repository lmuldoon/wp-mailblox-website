<?php

/**
 * Changelog page.
 */

global $meta;

$meta->title = 'Changelog — WP Mailblox';
$meta->description = 'Version history and release notes for WP Mailblox. See what\'s new, what\'s changed and what\'s been fixed.';

get_header();

?>

<article>

	<div class="section bg-darkblue">
		<div class="container">
			<div class="animated-up">
				<h1 class="text-white">Changelog</h1>
				<p style="color: rgba(255,255,255,0.75);">Version history and release notes for WP Mailblox.</p>
			</div>
		</div>
	</div>

	<div class="section">
		<div class="container">
			<div class="small-text-container">

				<div class="changelog-entry animated-up">
					<div class="changelog-entry__header">
						<h2 class="changelog-entry__version">1.0.0</h2>
						<span class="changelog-entry__date">April 2025</span>
						<span class="badge badge--release">Initial Release</span>
					</div>
					<div class="flow changelog-entry__body">
						<p>First public release of WP Mailblox.</p>

						<h3>Features</h3>
						<ul>
							<li>Gutenberg-native email builder using custom WP Mailblox blocks</li>
							<li>Brand Preset system — store logo, colours, typography and platform settings</li>
							<li>Section block with solid colour, gradient and image backgrounds</li>
							<li>Columns block (2–4 columns) with MSO ghost table Outlook support</li>
							<li>Header, Subheader, Text, Image, Button, Divider and Spacer blocks</li>
							<li>Logo block with automatic dark mode logo swap</li>
							<li>Menu block for email navigation links</li>
							<li>Social block with built-in platform icons</li>
							<li>Footer block with platform-specific unsubscribe merge tag injection</li>
							<li>HTML block for custom code (Pro)</li>
							<li>WooCommerce Product, Order Table, Product Recommendations and Coupon blocks (Pro)</li>
							<li>Per-block mobile padding and alignment overrides</li>
							<li>Full dark mode support with <code>prefers-color-scheme</code> and Outlook <code>[data-ogsc]</code> selectors</li>
							<li>UTM parameter defaults at template level with per-block overrides</li>
							<li>Subject line and preheader fields with character counters</li>
							<li>HTML export — download and copy to clipboard</li>
							<li>Direct platform push to Mailchimp, Brevo, Klaviyo, Campaign Monitor and ActiveCampaign (Pro)</li>
							<li>HubSpot merge tag support with HTML export workflow</li>
							<li>Template preview page</li>
							<li>Starter template library (Blank, Newsletter, Promotional, Two-column, Transactional)</li>
							<li>Save-as-template with tag support</li>
							<li>Preset import/export as JSON</li>
							<li>Google Fonts support in Presets</li>
							<li>Three-step onboarding wizard on first activation</li>
							<li>Export validation with warnings for missing fields and localhost image URLs</li>
							<li>Free plan: up to 5 templates and 1 Preset</li>
							<li>Pro plan: unlimited templates and Presets via Freemius licensing</li>
						</ul>
					</div>
				</div>

			</div>
		</div>
	</div>

</article>

<?php get_footer(); ?>
