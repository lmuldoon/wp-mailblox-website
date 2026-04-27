<?php

/**
 * Documentation page.
 */

global $meta;

$meta->title = 'Docs — WP Mailblox';
$meta->description = 'Learn how to install, configure, and use WP Mailblox to build and send email templates from WordPress.';

get_header();

?>

<article class="docs-page">

	<div class="sections-header section bg-darkblue">
		<div class="container">
			<div class="animated-up">
				<h1 class="text-white">Documentation</h1>
				<p style="color: rgba(255,255,255,0.75);">Everything you need to install, configure and get the most out of WP Mailblox.</p>
			</div>
		</div>
	</div>

	<div class="docs-layout container">

		<!-- Sidebar Nav -->
		<nav class="docs-sidebar animated-up" aria-label="Documentation navigation">
			<ul class="docs-nav">
				<li><a href="#getting-started" class="docs-nav__link">Getting Started</a></li>
				<li><a href="#installation" class="docs-nav__link">Installation</a></li>
				<li><a href="#presets" class="docs-nav__link">Creating a Preset</a></li>
				<li><a href="#building-email" class="docs-nav__link">Building an Email</a></li>
				<li><a href="#blocks" class="docs-nav__link">Available Blocks</a></li>
				<li><a href="#mobile-controls" class="docs-nav__link">Mobile Controls</a></li>
				<li><a href="#utm-tracking" class="docs-nav__link">UTM Tracking</a></li>
				<li><a href="#dark-mode" class="docs-nav__link">Dark Mode</a></li>
				<li><a href="#editor-toolbar" class="docs-nav__link">Editor Toolbar</a></li>
				<li><a href="#exporting" class="docs-nav__link">Exporting Your Email</a></li>
				<li><a href="#platform-integrations" class="docs-nav__link">Platform Integrations</a>
					<ul class="docs-nav docs-nav--sub">
						<li><a href="#platform-mailchimp" class="docs-nav__link">Mailchimp</a></li>
						<li><a href="#platform-brevo" class="docs-nav__link">Brevo</a></li>
						<li><a href="#platform-klaviyo" class="docs-nav__link">Klaviyo</a></li>
						<li><a href="#platform-campaignmonitor" class="docs-nav__link">Campaign Monitor</a></li>
						<li><a href="#platform-onesignal" class="docs-nav__link">OneSignal</a></li>
						<li><a href="#platform-activecampaign" class="docs-nav__link">ActiveCampaign</a></li>
						<li><a href="#platform-hubspot" class="docs-nav__link">HubSpot</a></li>
						<li><a href="#platform-emailoctopus" class="docs-nav__link">EmailOctopus</a></li>
						<li><a href="#platform-getresponse" class="docs-nav__link">GetResponse</a></li>
						<li><a href="#platform-convertkit" class="docs-nav__link">ConvertKit</a></li>
					</ul>
				</li>
				<li><a href="#woocommerce" class="docs-nav__link">WooCommerce Blocks <span class="badge badge--pro">Pro</span></a></li>
				<li><a href="#free-vs-pro" class="docs-nav__link">Free vs Pro</a></li>
			</ul>
		</nav>

		<!-- Main Content -->
		<main class="docs-content animated-up" id="maincontent-docs">

			<!-- =============================================
			     GETTING STARTED
			============================================= -->
			<section class="docs-section" id="getting-started">
				<div class="stack">
					<h2>Getting Started</h2>
					<p>WP Mailblox is a WordPress plugin that lets you design professional HTML email templates directly inside the WordPress block editor (Gutenberg). Instead of building emails in a separate tool or writing code by hand, you design them where you already manage your content.</p>
					<p>Once your template is ready, you can:</p>
					<ul>
						<li>Download the rendered HTML file</li>
						<li>Copy the HTML to your clipboard</li>
						<li>Push the template directly to a connected email platform (Pro)</li>
					</ul>
					<p>WP Mailblox uses a <strong>Preset</strong> system to store your brand settings — logo, colours, fonts, and platform choice. Assign a Preset to any email template and it automatically inherits your brand. Update the Preset and all associated templates update with it.</p>

					<div class="docs-callout docs-callout--info">
						<strong>Who is this for?</strong> WP Mailblox is built for WordPress users who want full control over their email design workflow. Create emails using your own hosted assets, keep branding consistent with your website, and export clean HTML that works with any email platform — without relying on third-party builders.
					</div>
				</div>
			</section>

			<hr>

			<!-- =============================================
			     INSTALLATION
			============================================= -->
			<section class="docs-section" id="installation">
				<div class="stack">
					<h2>Installation</h2>

					<h3>From the WordPress plugin directory</h3>
					<ol>
						<li>In your WordPress admin, go to <strong>Plugins → Add New</strong>.</li>
						<li>Search for <strong>WP Mailblox</strong>.</li>
						<li>Click <strong>Install Now</strong>, then <strong>Activate</strong>.</li>
					</ol>

					<h3>Manual installation</h3>
					<ol>
						<li>Download the plugin zip from <a href="https://wordpress.org/plugins/wp-mailblox/" target="_blank" rel="noopener">wordpress.org/plugins/wp-mailblox</a>.</li>
						<li>In your WordPress admin, go to <strong>Plugins → Add New → Upload Plugin</strong>.</li>
						<li>Upload the zip file and click <strong>Install Now</strong>, then <strong>Activate</strong>.</li>
					</ol>

					<h3>Onboarding wizard</h3>
					<p>After activation, WP Mailblox launches a three-step onboarding wizard:</p>
					<ol>
						<li><strong>Name your Preset</strong> — Give your first brand Preset a name (e.g. "Main Brand") and select your default email platform.</li>
						<li><strong>Brand setup</strong> — Upload your logo, and set your primary background and text colours.</li>
						<li><strong>Done</strong> — Your Preset is ready. You can refine all settings at any time via <strong>WP Mailblox → Email Presets</strong>.</li>
					</ol>

					<div class="docs-callout docs-callout--tip">
						<strong>Tip:</strong> You can skip the onboarding wizard and set up your Preset manually via <strong>WP Mailblox → Email Presets → Add New</strong>.
					</div>
				</div>
			</section>

			<hr>

			<!-- =============================================
			     PRESETS
			============================================= -->
			<section class="docs-section" id="presets">
				<div class="stack">
					<h2>Creating a Preset</h2>
					<p>Presets are the foundation of WP Mailblox. A Preset stores your brand configuration and is assigned to one or more email templates. The free plan supports one Preset; Pro supports unlimited.</p>

					<p>To create or edit a Preset, go to <strong>WP Mailblox → Email Presets → Add New</strong> (or click an existing Preset to edit it).</p>

					<h3>Preset settings</h3>

					<h4>Logo</h4>
					<ul>
						<li><strong>Logo image</strong> — Upload your primary logo. This appears in the Logo block within your email templates.</li>
						<li><strong>Dark mode logo</strong> — Optional alternative logo used when dark mode is active.</li>
						<li><strong>Logo alt text</strong> — Descriptive alt text for screen readers and email clients that don't display images.</li>
					</ul>

					<h4>Colours</h4>
					<ul>
						<li><strong>Body background</strong> — The outer background colour of the email.</li>
						<li><strong>Text colour (light background)</strong> — Default body text colour.</li>
						<li><strong>Text colour (dark background)</strong> — Text colour used on dark-background sections.</li>
						<li><strong>Button background</strong> — Default background for Button blocks.</li>
						<li><strong>Button text colour</strong> — Default text colour for Button blocks.</li>
						<li><strong>Link colour</strong> — Colour for hyperlinks in text content.</li>
					</ul>

					<h4>Typography</h4>
					<p>Set font family, weight, size (desktop and mobile), and line height for three text roles:</p>
					<ul>
						<li><strong>Heading</strong> — Used by Header blocks.</li>
						<li><strong>Subheading</strong> — Used by Subheader blocks.</li>
						<li><strong>Body</strong> — Used by Text blocks and general content.</li>
					</ul>
					<p>You can choose from web-safe fonts (Arial, Helvetica, Georgia, etc.) or specify a <strong>Google Font</strong> by entering the font family name. Google Fonts are loaded via a <code>&lt;link&gt;</code> tag injected into the exported HTML's <code>&lt;head&gt;</code>.</p>

					<h4>Platform</h4>
					<p>Select the default email platform for this Preset. This controls which merge tags are available in your templates (e.g. <code>*|FNAME|*</code> for Mailchimp vs <code>{{ contact.firstname }}</code> for Brevo). Individual templates can override the Preset's platform if needed.</p>

					<h4>Container width</h4>
					<p>Sets the maximum width of the email content area. Default is 600px, which is the standard for most email clients.</p>

					<h4>Import / Export</h4>
					<p>Presets can be exported as JSON and imported on another WordPress site — useful for agencies managing multiple client sites with the same brand.</p>
				</div>
			</section>

			<hr>

			<!-- =============================================
			     BUILDING AN EMAIL
			============================================= -->
			<section class="docs-section" id="building-email">
				<div class="stack">
					<h2>Building an Email Template</h2>

					<h3>Creating a new template</h3>
					<ol>
						<li>Go to <strong>WP Mailblox → Email Templates → Add New</strong>.</li>
						<li>The template chooser opens. Select a <strong>starter template</strong> (Blank, Newsletter, Promotional, Two-column, Transactional) or one of your saved templates.</li>
						<li>The Gutenberg editor loads with your chosen layout pre-filled.</li>
					</ol>

					<h3>Email settings (sidebar metaboxes)</h3>
					<p>The right-hand sidebar contains several metaboxes specific to email templates:</p>

					<ul>
						<li><strong>Set Email Preset</strong> — Choose which Preset this template uses. Required before export.</li>
						<li><strong>Platform Override</strong> — Optionally override the Preset's platform for this template only.</li>
						<li><strong>Subject line</strong> — The email subject (up to 200 characters). Required before export.</li>
						<li><strong>Preheader text</strong> — The preview text shown next to the subject in the inbox (40–130 characters recommended). Required before export.</li>
						<li><strong>Dark Mode Override</strong> — Set a specific dark mode background colour for this template, overriding the Preset's default.</li>
						<li><strong>UTM Defaults</strong> — Set default UTM parameters (source, medium, campaign, content, term) that are appended to all links in the template.</li>
					</ul>

					<div class="docs-callout docs-callout--info">
						<strong>Note:</strong> Preview, Send Test Email, Save as Template, and Export have moved to the top toolbar. See <a href="#editor-toolbar">Editor Toolbar</a> for details.
					</div>

					<h3>Adding blocks</h3>
					<p>Click the <strong>+</strong> icon in the editor to add WP Mailblox blocks. All blocks specific to the plugin are grouped under the <strong>WP Mailblox</strong> category in the block inserter.</p>

					<div class="docs-callout docs-callout--tip">
						<strong>Tip:</strong> Email HTML is fundamentally different from web HTML — it uses tables for layout and has strict limitations on CSS support. WP Mailblox handles all of this for you. Don't use standard WordPress blocks like Paragraph or Heading inside email templates; use the dedicated WP Mailblox blocks instead.
					</div>
				</div>
			</section>

			<hr>

			<!-- =============================================
			     AVAILABLE BLOCKS
			============================================= -->
			<section class="docs-section" id="blocks">
				<div class="stack">
					<h2>Available Blocks</h2>
					<p>WP Mailblox provides a set of blocks specifically designed to produce valid, email-client-safe HTML. All blocks respect your Preset's typography and colour settings by default.</p>

					<h3>Layout blocks</h3>

					<div class="docs-block-table-wrap"><div class="docs-block-table">
						<div class="docs-block-row docs-block-row--header">
							<div>Block</div>
							<div>Description</div>
							<div>Key settings</div>
						</div>

						<div class="docs-block-row">
							<div><strong>Section</strong></div>
							<div>Full-width container block. All other blocks must sit inside a Section. Controls the background — solid colour or image.</div>
							<div>Background colour (solid) or background image (repeat, position, size); dark mode background colour override (4 preset swatches); padding top/bottom; mobile padding overrides, hide on mobile; Save as Reusable Module button <span class="badge badge--pro">Pro</span></div>
						</div>

						<div class="docs-block-row">
							<div><strong>Columns</strong></div>
							<div>Splits the layout into 1–4 columns. Uses MSO ghost-table technique for full Outlook compatibility.</div>
							<div>Number of columns (1–4); gap between columns (0–60 px); column width distribution (drag bar, reset to equal); reverse column order (2-column only); background colour or image; dark mode background override; padding all 4 sides; per-corner border radius TL/TR/BL/BR; desktop and mobile overrides; hide on mobile</div>
						</div>

						<div class="docs-block-row">
							<div><strong>Column</strong></div>
							<div>Individual column inside a Columns block. Styled independently from its siblings.</div>
							<div>Background colour or background image; dark mode background override; padding all 4 sides; per-corner border radius TL/TR/BL/BR; border width and colour; desktop and mobile overrides</div>
						</div>
					</div></div>

					<h3>Content blocks</h3>

					<div class="docs-callout docs-callout--tip">
						<strong>Merge tags in Header, Subheader, and Text blocks.</strong> The Header, Subheader, and Text blocks support personalisation merge tags from your assigned platform — for example, inserting a subscriber's first name or a dynamic coupon code. While editing one of these blocks, click the <strong>merge tag</strong> button in the block toolbar to browse and insert the available tags for your platform. The tags that appear are determined by the platform set in your Preset (or the template's Platform Override).
					</div>

					<div class="docs-block-table-wrap"><div class="docs-block-table">
						<div class="docs-block-row docs-block-row--header">
							<div>Block</div>
							<div>Description</div>
							<div>Key settings</div>
						</div>

						<div class="docs-block-row">
							<div><strong>Header</strong></div>
							<div>Large heading text. Inherits heading typography from your Preset.</div>
							<div>Text colour override; disable dark mode text colour adjustment; alignment (left / centre / right); padding all 4 sides; mobile alignment override, mobile padding overrides, hide on mobile; merge tag insertion (toolbar)</div>
						</div>

						<div class="docs-block-row">
							<div><strong>Subheader</strong></div>
							<div>Secondary heading. Inherits subheading typography from your Preset.</div>
							<div>Text colour override; disable dark mode text colour adjustment; alignment (left / centre / right); padding all 4 sides; mobile alignment override, mobile padding overrides, hide on mobile; merge tag insertion (toolbar)</div>
						</div>

						<div class="docs-block-row">
							<div><strong>Text</strong></div>
							<div>Body paragraph text with rich-text formatting (bold, italic, links).</div>
							<div>Text colour override; disable dark mode text colour adjustment; alignment (left / centre / right); padding all 4 sides; mobile alignment override, mobile padding overrides, hide on mobile; merge tag insertion (toolbar)</div>
						</div>

						<div class="docs-block-row">
							<div><strong>Image</strong></div>
							<div>Responsive email image with full alt-text and link support.</div>
							<div>Upload / select image; alt text (warning if missing); display width (px); dark mode image override; link URL; alignment; padding all 4 sides; per-corner border radius TL/TR/BL/BR; border width and colour; mobile alignment, padding, border radius/width/colour, hide on mobile; UTM parameters (source, medium, campaign, content, term)</div>
						</div>

						<div class="docs-block-row">
							<div><strong>Button</strong></div>
							<div>CTA button using a table-based structure for Outlook compatibility.</div>
							<div>Label text; URL (text input, post/page search, or merge tag); background colour; text colour; alignment (left / centre / right); padding all 4 sides; border radius (0–50 px); border width (0–10 px) and border colour; mobile — all styling properties independently overridable, hide on mobile; UTM parameters (source, medium, campaign, content, term)</div>
						</div>

						<div class="docs-block-row">
							<div><strong>Divider</strong></div>
							<div>Horizontal rule for visual separation between content sections.</div>
							<div>Line colour; thickness (1–10 px); spacing (desktop); mobile spacing override, hide on mobile</div>
						</div>

						<div class="docs-block-row">
							<div><strong>Spacer</strong></div>
							<div>Empty vertical space block for fine-tuning layout spacing.</div>
							<div>Spacing height (desktop); background colour; dark mode background colour; mobile spacing override, hide on mobile</div>
						</div>

						<div class="docs-block-row">
							<div><strong>Logo</strong></div>
							<div>Renders your Preset's logo. Switches to dark logo automatically when dark mode is active.</div>
							<div>Logo image override (overrides Preset logo for this instance); alt text; link URL; dark mode logo override; width (50–600 px); alignment; padding all 4 sides; mobile alignment, padding overrides, hide on mobile</div>
						</div>

						<div class="docs-block-row">
							<div><strong>Menu</strong></div>
							<div>Horizontal navigation links — typically used in the email header.</div>
							<div>Menu items (label + URL, add/remove); alignment; font size (10–24 px); text colour; separator width and colour; padding all 4 sides; mobile alignment, font size, padding overrides, hide on mobile; UTM parameters (source, medium, campaign, content, term)</div>
						</div>

						<div class="docs-block-row">
							<div><strong>Social</strong></div>
							<div>Row of social media icon links. Supports Facebook, Instagram, X, LinkedIn, YouTube, TikTok, Pinterest, and Threads.</div>
							<div>Platform URLs (Facebook, Instagram, X, LinkedIn, YouTube, TikTok, Pinterest, Threads); drag to reorder; alignment; icon size (16–64 px); icon spacing (0–32 px); badge shape (circle / rounded square / square); badge padding (0–12 px); badge colour; mobile alignment, icon size override, hide on mobile</div>
						</div>

						<div class="docs-block-row">
							<div><strong>Footer</strong></div>
							<div>Pre-structured email footer with unsubscribe link, address, and platform-specific merge tags injected automatically.</div>
							<div>Show/hide unsubscribe link + link text; show/hide "View this email online" link + link text; physical address; footer text; text colour</div>
						</div>

						<div class="docs-block-row">
							<div><strong>Video</strong></div>
							<div>Email-safe video block — embeds a linked thumbnail image since video cannot play inline in most email clients.</div>
							<div>YouTube or Vimeo URL (thumbnail fetched automatically); custom thumbnail override; alignment; border radius (0–40 px); show play button toggle + play button colour; show caption toggle + caption text; padding all 4 sides; mobile padding overrides, hide on mobile; UTM parameters (source, medium, campaign, content, term)</div>
						</div>

						<div class="docs-block-row">
							<div><strong>Conditional</strong></div>
							<div>Wraps content in a platform-specific conditional merge tag so it only renders for certain subscribers. Only available when the template is set to a compatible platform.</div>
							<div>Condition field (platform merge field)</div>
						</div>

						<div class="docs-block-row">
							<div><strong>HTML <span class="badge badge--pro">Pro</span></strong></div>
							<div>Insert raw HTML directly into the email. Useful for custom components, external snippets or platform-specific code.</div>
							<div>Raw HTML textarea</div>
						</div>
					</div></div>

					<h3>WooCommerce blocks <span class="badge badge--pro">Pro</span></h3>

					<div class="docs-block-table-wrap"><div class="docs-block-table">
						<div class="docs-block-row docs-block-row--header">
							<div>Block</div>
							<div>Description</div>
							<div>Key settings</div>
						</div>

						<div class="docs-block-row">
							<div><strong>Product</strong></div>
							<div>Displays a single product with image, title, price and CTA button. Search and select from your WooCommerce store.</div>
							<div>Product search; product URL override; button label text; show price toggle; show description toggle; image alignment; button colour and text colour; button padding all 4 sides; button border radius, width, and colour; mobile image alignment, padding, button border radius/width/colour; disable dark mode text colour</div>
						</div>

						<div class="docs-block-row">
							<div><strong>Order Table</strong></div>
							<div>Renders a transactional order summary table with item rows, subtotal, shipping and total. Uses platform merge tags for dynamic order data.</div>
							<div>Show/hide subtotal, discount, tax, shipping, total rows; show view order button + button text; header background and text colour; border colour; alternate row colour; text colour; font size (desktop and mobile); button colour, text colour, border radius, padding, border width/colour; padding all 4 sides (desktop + mobile); disable dark mode text colour</div>
						</div>

						<div class="docs-block-row">
							<div><strong>Coupon</strong></div>
							<div>Displays a styled coupon code block with a prominent code and optional expiry/description text.</div>
							<div>Title, description, coupon code, expiry text; box background and text colour; code box background and code text colour; dark mode box and code box background; alignment (desktop + mobile); padding all 4 sides (desktop + mobile); border colour, style (solid/dashed/dotted), width, radius; mobile border radius, width, and colour overrides</div>
						</div>
					</div></div>
				</div>
			</section>

			<hr>

			<!-- =============================================
			     MOBILE CONTROLS
			============================================= -->
			<section class="docs-section" id="mobile-controls">
				<div class="stack">
					<h2>Mobile Controls</h2>
					<p>Most WP Mailblox blocks include a <strong>Mobile</strong> tab in their block settings panel. This lets you override the desktop layout on screens below your Preset's container width.</p>
					<ul>
						<li><strong>Mobile padding</strong> — Set separate top, bottom, left and right padding for mobile. Values snap to a 5px grid (0–50px).</li>
						<li><strong>Mobile alignment</strong> — Override text and content alignment on mobile independently of the desktop setting.</li>
						<li><strong>Hide on mobile</strong> — Some blocks support a toggle to hide the block entirely on mobile screens.</li>
					</ul>
					<div class="docs-callout docs-callout--info">
						Mobile styles are rendered using a <code>&lt;style&gt;</code> block with a <code>max-width: &lt;container width&gt;</code> media query. Email clients that don't support media queries (e.g. Outlook on Windows) will always display the desktop layout.
					</div>
				</div>
			</section>

			<hr>

			<!-- =============================================
			     UTM TRACKING
			============================================= -->
			<section class="docs-section" id="utm-tracking">
				<div class="stack">
					<h2>UTM Tracking</h2>
					<p>WP Mailblox can automatically append UTM parameters to all links in your exported email HTML. This makes it easy to track email campaign performance in Google Analytics or any UTM-aware analytics tool.</p>

					<h3>Template-level UTM defaults</h3>
					<p>Set default values for your UTM parameters in the <strong>UTM Defaults</strong> metabox in the template sidebar:</p>
					<ul>
						<li><code>utm_source</code> — e.g. <code>newsletter</code></li>
						<li><code>utm_medium</code> — e.g. <code>email</code></li>
						<li><code>utm_campaign</code> — e.g. <code>may-2025-promotion</code></li>
						<li><code>utm_content</code> — e.g. <code>hero-button</code></li>
						<li><code>utm_term</code> — optional keyword tracking</li>
					</ul>

					<h3>Per-block overrides</h3>
					<p>Individual Button and link blocks can override the template's UTM defaults. This is useful when different CTAs in the same email should be tracked separately (e.g. different <code>utm_content</code> values per button).</p>

					<div class="docs-callout docs-callout--info">
						UTM parameters are only appended to real <code>http://</code> or <code>https://</code> URLs. Platform merge tags (e.g. <code>*|UNSUB|*</code>) are automatically excluded.
					</div>
				</div>
			</section>

			<hr>

			<!-- =============================================
			     DARK MODE
			============================================= -->
			<section class="docs-section" id="dark-mode">
				<div class="stack">
					<h2>Dark Mode</h2>
					<p>WP Mailblox includes full dark mode support, using <code>prefers-color-scheme: dark</code> media queries alongside <code>[data-ogsc]</code> attribute selectors for Outlook compatibility.</p>

					<h3>How it works</h3>
					<p>Dark mode colours are set at two levels:</p>
					<ul>
						<li><strong>Preset level</strong> — Set dark mode text colours in your Preset settings. These apply globally to all templates using that Preset.</li>
						<li><strong>Template level</strong> — The <strong>Dark Mode Override</strong> metabox lets you set a specific dark background colour for the entire template, overriding the Preset default.</li>
						<li><strong>Block level</strong> — Individual Section blocks have a <strong>Dark mode background</strong> colour setting, letting you set a different background per section.</li>
					</ul>

					<h3>Dark logo</h3>
					<p>If you've uploaded a dark mode logo in your Preset, the Logo block will automatically swap to it when dark mode is active. This is handled via <code>display: none</code> / <code>display: block</code> swapping with appropriate selectors for each email client.</p>

					<div class="docs-callout docs-callout--tip">
						<strong>Always pair light backgrounds with a dark mode override.</strong> If a Section or Columns block has a custom background colour set for light mode — particularly a white or light grey — be sure to set its dark mode background colour as well. Without an override, those blocks will retain their light background in dark mode, which can result in light-on-light text and make your content unreadable. As a rule of thumb: any block where you have explicitly set a background colour should also have a dark mode background colour defined.
					</div>

					<div class="docs-callout docs-callout--info">
						Not all email clients support dark mode. Gmail on Android and iOS, Apple Mail, and Outlook on Mac are among those that do. Always test your dark mode designs across clients before sending.
					</div>
				</div>
			</section>

			<hr>

			<!-- =============================================
			     EDITOR TOOLBAR
			============================================= -->
			<section class="docs-section" id="editor-toolbar">
				<div class="stack">
					<h2>Editor Toolbar</h2>
					<p>The top toolbar of the email editor contains four action buttons specific to WP Mailblox. These apply to the whole template and are always accessible regardless of which block is selected.</p>

					<div class="docs-block-table-wrap"><div class="docs-block-table docs-block-table--two-col">
						<div class="docs-block-row docs-block-row--header">
							<div>Button</div>
							<div>Description</div>
						</div>

						<div class="docs-block-row">
							<div><strong>Preview</strong></div>
							<div>Opens a full-width preview of the rendered email in the editor canvas. Toggles between desktop and mobile view. Requires a Preset to be assigned before it can render.</div>
						</div>

						<div class="docs-block-row">
							<div><strong>Send Test Email</strong></div>
							<div>Sends a rendered copy of the current template to a specified email address. Use this to test how your email looks across different clients and devices before sending to your list.</div>
						</div>

						<div class="docs-block-row">
							<div><strong>Save as Template</strong></div>
							<div>Saves the current email design as a reusable saved template. Enter a name and optional tag in the modal that appears. Saved templates appear in the template chooser when creating a new email.</div>
						</div>

						<div class="docs-block-row">
							<div><strong>Export</strong></div>
							<div>Opens the export modal — download HTML, copy to clipboard, or push directly to your connected platform <span class="badge badge--pro">Pro</span>. WP Mailblox runs validation checks (missing subject, preheader, preset, localhost image URLs) and shows warnings before export.</div>
						</div>
					</div></div>
				</div>
			</section>

			<hr>

			<!-- =============================================
			     EXPORTING
			============================================= -->
			<section class="docs-section" id="exporting">
				<div class="stack">
					<h2>Exporting Your Email</h2>
					<p>When your template is ready, click the <strong>Export</strong> button in the top toolbar of the editor.</p>

					<h3>Export options</h3>
					<ul>
						<li><strong>Download HTML</strong> — Saves the rendered email as an <code>.html</code> file to your computer.</li>
						<li><strong>Copy HTML</strong> — Copies the rendered HTML to your clipboard, ready to paste into your email platform.</li>
						<li><strong>Push to platform (Pro)</strong> — Sends the template directly to your connected email platform via API. See <a href="#platform-integrations">Platform Integrations</a> for setup.</li>
					</ul>

					<h3>Export validation</h3>
					<p>Before exporting, WP Mailblox checks for common issues and shows a warning if any are found:</p>
					<ul>
						<li><strong>Missing subject line</strong> — A subject is required for export.</li>
						<li><strong>Missing preheader</strong> — A preheader is required for export.</li>
						<li><strong>No Preset assigned</strong> — A Preset must be assigned before exporting.</li>
						<li><strong>Localhost/private image URLs</strong> — Images hosted on <code>localhost</code> or a private IP will not load for recipients. Replace them with publicly accessible URLs before sending.</li>
					</ul>

					<div class="docs-callout docs-callout--tip">
						<strong>Tip:</strong> Use the <strong>Preview</strong> button in the toolbar to check how your template renders before exporting. You can also send a test to an inbox using <strong>Send Test Email</strong>.
					</div>
				</div>
			</section>

			<hr>

			<!-- =============================================
			     PLATFORM INTEGRATIONS
			============================================= -->
			<section class="docs-section" id="platform-integrations">
				<div class="stack">
					<h2>Platform Integrations</h2>
					<p>Direct platform push is a <strong>Pro</strong> feature. It lets you send your finished template directly to your email platform without leaving WordPress. API credentials are stored in <strong>WP Mailblox → Settings → Platform Connections</strong>.</p>

					<div class="docs-callout docs-callout--info">
						HTML export (download / copy) is available on all plans and works with any email platform — even those not listed here.
					</div>

					<!-- Mailchimp -->
					<div class="docs-platform stack" id="platform-mailchimp">
						<h3>Mailchimp</h3>
						<p><strong>Required:</strong> Mailchimp API key</p>
						<ol>
							<li>In Mailchimp, go to <strong>Account → Extras → API keys → Create A Key</strong>.</li>
							<li>Copy the key — it follows the format <code>xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx-us1</code> (your data centre is the suffix after the dash).</li>
							<li>Paste it into <strong>WP Mailblox → Settings → Platform Connections → Mailchimp API Key</strong>.</li>
						</ol>
						<p>When you push a template, WP Mailblox creates (or updates) a <strong>Template</strong> in your Mailchimp account under <strong>Content → Email templates</strong>.</p>
					</div>

					<!-- Brevo -->
					<div class="docs-platform stack" id="platform-brevo">
						<h3>Brevo (formerly Sendinblue)</h3>
						<p><strong>Required:</strong> Brevo API key, verified sender name and email address</p>
						<ol>
							<li>In Brevo, go to <strong>My account → SMTP &amp; API → API Keys → Generate a new API key</strong>.</li>
							<li>Copy the key and paste it into <strong>WP Mailblox → Settings → Platform Connections → Brevo API Key</strong>.</li>
							<li>Enter your verified sender <strong>name</strong> and <strong>email address</strong> — these must match a verified sender in your Brevo account.</li>
						</ol>
					</div>

					<!-- Klaviyo -->
					<div class="docs-platform stack" id="platform-klaviyo">
						<h3>Klaviyo</h3>
						<p><strong>Required:</strong> Klaviyo Private API key</p>
						<ol>
							<li>In Klaviyo, go to <strong>Account → Settings → API Keys → Create Private API Key</strong>.</li>
							<li>Give it a name (e.g. "WP Mailblox") and ensure it has <strong>Full Access</strong> or at minimum <strong>Templates: Read/Write</strong>.</li>
							<li>Copy the key and paste it into <strong>WP Mailblox → Settings → Platform Connections → Klaviyo Private API Key</strong>.</li>
						</ol>
					</div>

					<!-- Campaign Monitor -->
					<div class="docs-platform stack" id="platform-campaignmonitor">
						<h3>Campaign Monitor</h3>
						<p><strong>Required:</strong> Campaign Monitor API key, Client ID</p>
						<ol>
							<li>In Campaign Monitor, click your name → <strong>Account Settings → API keys → Generate API key</strong>.</li>
							<li>Your Client ID is in the URL when you're viewing a client: <code>app.createsend.com/clients/<strong>CLIENT_ID</strong>/</code>.</li>
							<li>Enter both values in <strong>WP Mailblox → Settings → Platform Connections</strong>.</li>
						</ol>
						<div class="docs-callout docs-callout--warning">
							<strong>Note:</strong> Campaign Monitor's API requires the template HTML to be served from a publicly accessible URL. WP Mailblox creates a temporary signed URL to serve the template. This means your WordPress site must be accessible from the internet — it will not work on localhost.
						</div>
					</div>

					<!-- OneSignal -->
					<div class="docs-platform stack" id="platform-onesignal">
						<h3>OneSignal</h3>
						<p><strong>Required:</strong> OneSignal REST API key</p>
						<ol>
							<li>In your OneSignal dashboard, go to <strong>Settings → Keys &amp; IDs</strong>.</li>
							<li>Copy the <strong>REST API Key</strong>.</li>
							<li>Paste it into <strong>WP Mailblox → Settings → Platform Connections → OneSignal REST API Key</strong>.</li>
						</ol>
						<p>When you push a template, WP Mailblox creates (or updates) an email template in your OneSignal account.</p>
					</div>

					<!-- ActiveCampaign -->
					<div class="docs-platform stack" id="platform-activecampaign">
						<h3>ActiveCampaign</h3>
						<p><strong>Note:</strong> ActiveCampaign's API does not currently support uploading raw HTML email templates. WP Mailblox generates ActiveCampaign-compatible merge tags and provides HTML export for manual upload — direct push is not available for ActiveCampaign.</p>
						<p>To use your template in ActiveCampaign:</p>
						<ol>
							<li>Export the HTML from WP Mailblox (download or copy).</li>
							<li>In ActiveCampaign, go to <strong>Campaigns → Templates</strong>.</li>
							<li>Create a new template using the <strong>HTML</strong> option and paste in the exported code.</li>
						</ol>
					</div>

					<!-- HubSpot -->
					<div class="docs-platform stack" id="platform-hubspot">
						<h3>HubSpot</h3>
						<p><strong>Note:</strong> HubSpot's API does not currently support uploading raw HTML email templates. WP Mailblox generates HubSpot-compatible merge tags and provides HTML export for manual upload — direct push is not available for HubSpot.</p>
						<p>To use your template in HubSpot:</p>
						<ol>
							<li>Export the HTML from WP Mailblox (download or copy).</li>
							<li>In HubSpot, go to <strong>Marketing → Files and Templates → Design Tools</strong>.</li>
							<li>Create a new email template and paste in the HTML code.</li>
						</ol>
					</div>

					<!-- EmailOctopus -->
					<div class="docs-platform stack" id="platform-emailoctopus">
						<h3>EmailOctopus</h3>
						<p><strong>Note:</strong> EmailOctopus supports custom HTML templates via manual import — direct push is not available. WP Mailblox generates EmailOctopus-compatible merge tags so dynamic fields work correctly when you import the template.</p>
						<p>To use your template in EmailOctopus:</p>
						<ol>
							<li>Export the HTML from WP Mailblox (download or copy).</li>
							<li>In EmailOctopus, go to <strong>Templates</strong> and click <strong>New template → Import HTML</strong>.</li>
							<li>Paste in the exported HTML and save.</li>
						</ol>
					</div>

					<!-- GetResponse -->
					<div class="docs-platform stack" id="platform-getresponse">
						<h3>GetResponse</h3>
						<p><strong>Note:</strong> GetResponse supports custom HTML templates — direct push is not available. WP Mailblox generates GetResponse-compatible merge tags so dynamic fields work correctly when you import the template.</p>
						<p>To use your template in GetResponse:</p>
						<ol>
							<li>Export the HTML from WP Mailblox (download or copy).</li>
							<li>In GetResponse, go to <strong>Email Marketing → Templates → Create template</strong>.</li>
							<li>Choose the <strong>HTML editor</strong> option and paste in the exported code.</li>
						</ol>
					</div>

					<!-- ConvertKit -->
					<div class="docs-platform stack" id="platform-convertkit">
						<h3>ConvertKit (Kit)</h3>
						<p><strong>Note:</strong> ConvertKit supports custom HTML emails — direct push is not available. WP Mailblox generates ConvertKit-compatible merge tags so personalisation fields work correctly when you use the template.</p>
						<p>To use your template in ConvertKit:</p>
						<ol>
							<li>Export the HTML from WP Mailblox (download or copy).</li>
							<li>In ConvertKit, create a new broadcast or sequence email.</li>
							<li>Select <strong>HTML</strong> as the email type and paste in the exported code.</li>
						</ol>
					</div>

				</div>
			</section>

			<hr>

			<!-- =============================================
			     WOOCOMMERCE
			============================================= -->
			<section class="docs-section" id="woocommerce">
				<div class="stack">
					<h2>WooCommerce Blocks <span class="badge badge--pro">Pro</span></h2>
					<p>WP Mailblox Pro includes three blocks that integrate directly with your WooCommerce store, letting you build transactional and promotional emails with live product data.</p>

					<p><strong>Requirement:</strong> WooCommerce must be installed and active to use these blocks.</p>

					<h3>Product block</h3>
					<p>Search for and display a product from your store. The block renders the product image, name, price and an optional CTA button. Useful for promotional emails highlighting a specific product.</p>

					<h3>Order Table block</h3>
					<p>Renders a structured order summary for transactional emails (order confirmations, shipping notifications). The table is populated with platform-specific merge tags so the order data is dynamically injected at send time. Supports alternating row colours and customisable column labels.</p>

					<h3>Coupon block</h3>
					<p>Displays a styled coupon code with optional description and expiry text. The coupon code itself is static text — you enter the code you want to promote.</p>

					<div class="docs-callout docs-callout--tip">
						<strong>Tip:</strong> The Order Table and Product blocks pull live data from your store at the time you edit the template (for preview purposes). The merge tags inserted into the exported HTML are what your email platform will replace with real order data at send time. Check your platform's documentation to ensure the merge tags WP Mailblox inserts are compatible with your chosen platform.
					</div>
				</div>
			</section>

			<hr>

			<!-- =============================================
			     FREE VS PRO
			============================================= -->
			<section class="docs-section" id="free-vs-pro">
				<div class="stack">
					<h2>Free vs Pro</h2>

					<table class="docs-comparison-table">
						<thead>
							<tr>
								<th>Feature</th>
								<th>Free</th>
								<th>Pro</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>Email templates</td>
								<td>Up to 5</td>
								<td>Unlimited</td>
							</tr>
							<tr>
								<td>Brand Presets</td>
								<td>1</td>
								<td>Unlimited</td>
							</tr>
							<tr>
								<td>All standard blocks</td>
								<td>✓</td>
								<td>✓</td>
							</tr>
							<tr>
								<td>HTML export (download &amp; copy)</td>
								<td>✓</td>
								<td>✓</td>
							</tr>
							<tr>
								<td>Dark mode support</td>
								<td>✓</td>
								<td>✓</td>
							</tr>
							<tr>
								<td>Mobile responsive controls</td>
								<td>✓</td>
								<td>✓</td>
							</tr>
							<tr>
								<td>UTM tracking</td>
								<td>✓</td>
								<td>✓</td>
							</tr>
							<tr>
								<td>Starter template library</td>
								<td>✓</td>
								<td>✓</td>
							</tr>
							<tr>
								<td>Preset import / export</td>
								<td>✓</td>
								<td>✓</td>
							</tr>
							<tr>
								<td>Direct platform push (5 platforms)</td>
								<td>—</td>
								<td>✓</td>
							</tr>
							<tr>
								<td>Multiple Presets</td>
								<td>—</td>
								<td>✓</td>
							</tr>
							<tr>
								<td>Reusable section modules</td>
								<td>—</td>
								<td>✓</td>
							</tr>
							<tr>
								<td>HTML / custom code block</td>
								<td>—</td>
								<td>✓</td>
							</tr>
							<tr>
								<td>WooCommerce blocks</td>
								<td>—</td>
								<td>✓</td>
							</tr>
							<tr>
								<td>Remove WP Mailblox branding</td>
								<td>—</td>
								<td>✓</td>
							</tr>
							<tr>
								<td>Priority support</td>
								<td>—</td>
								<td>✓</td>
							</tr>
						</tbody>
					</table>

					<div class="flex flex-wrap gap-4 mt-8">
						<a class="button button--primary js-get-pro" href="#">Upgrade to Pro</a>
						<a class="button button--outline" href="https://wordpress.org/plugins/wp-mailblox/" target="_blank" rel="noopener">Download Free</a>
					</div>
				</div>
			</section>

		</main>
	</div>

</article>

<?php get_footer(); ?>
