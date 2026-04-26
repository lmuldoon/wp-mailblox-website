=== WP Mailblox ===
Contributors: TODO-wporg-username
Tags: email, email builder, email template, html email, gutenberg
Requires at least: 6.0
Tested up to: 6.7
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Build and export HTML emails from WordPress using a Gutenberg-based email editor.

== Description ==

WP Mailblox turns the WordPress block editor into a fully-featured HTML email builder. Design email templates using familiar Gutenberg blocks, then export pixel-perfect HTML or push directly to your email platform.

**Core Features (Free)**

* Gutenberg-based email editor with email-safe block rendering
* Blocks: Header, Subheader, Text, Image, Button, Divider, Spacer, Logo, Menu, Social, Footer, Columns, Section
* Multi-column layouts (1–4 columns) with full Outlook MSO compatibility
* Mobile-responsive CSS with per-block mobile padding, alignment, and font size overrides
* Hide-on-mobile toggle per block
* Mobile preview in the editor canvas
* Preset system for brand settings (typography, colours, logo, dark mode)
* Web-safe and Google Fonts support
* Reusable saved templates with tagging and category filtering
* Starter template library (newsletter, promotional, two-column, transactional, blank)
* One-click HTML export with copy and download
* Export validation warnings
* Subject line and preheader fields with live character counter
* Dark mode support (`prefers-color-scheme` and Outlook-safe `[data-ogsc]`)
* Per-section dark background colour overrides
* UTM parameter tracking per template and per block
* Background images on Section and Columns blocks
* Direct template push to Mailchimp, Klaviyo, Brevo, Campaign Monitor, and OneSignal
* HTML export with platform-compatible merge tags for ActiveCampaign, HubSpot, EmailOctopus, GetResponse and ConvertKit

**Pro Features**

* WooCommerce Product block
* WooCommerce Order Table block (with dynamic line-item loops for Klaviyo and Brevo)
* WooCommerce Coupon block
* Reusable Section modules
* Custom HTML block
* Remove footer branding

== Installation ==

1. Upload the `wp-mailblox` folder to the `/wp-content/plugins/` directory, or install directly through the WordPress Plugins screen.
2. Activate the plugin through the **Plugins** screen in WordPress.
3. Navigate to **WP Mailblox → Settings** to complete onboarding and configure your first preset.
4. Go to **WP Mailblox → Email Templates** and click **Add New** to start building.

== Frequently Asked Questions ==

= Which email clients are supported? =

WP Mailblox generates email HTML tested against Gmail, Apple Mail, Outlook (2016–2021 and Outlook.com), Yahoo Mail, and major mobile clients. The column layout uses the MSO ghost-table technique for full Outlook compatibility.

= Which email platforms can I push to? =

WP Mailblox Pro includes direct push to Mailchimp, Klaviyo, Brevo, Campaign Monitor, and OneSignal. ActiveCampaign, HubSpot, EmailOctopus, GetResponse and ConvertKit are supported via HTML export — the exported HTML includes platform-compatible merge tags so dynamic fields work correctly when you paste it into the platform's template editor.

= Do I need WooCommerce for the WooCommerce blocks? =

Yes. The WooCommerce blocks (Product, Order Table, Coupon) require WooCommerce to be active. These are Pro-only features.

= Are translations supported? =

Translation support (i18n) is planned for a future release. Contributions are welcome via translate.wordpress.org once the plugin is listed.

= Is the source code on GitHub? =

The plugin is commercial software. The free version is available on WordPress.org.

== External Services ==

This plugin connects to the following external services. All connections are initiated by the site administrator and only when explicitly triggered.

**Google Fonts** (https://fonts.google.com)
Used to load custom web fonts in email previews and exports when a Google Font is selected in a preset. No personal data is transmitted. See Google's privacy policy: https://policies.google.com/privacy

**Mailchimp** (https://mailchimp.com)
Used to push email templates to a Mailchimp account. Requires the user to provide their own Mailchimp API key. Only called when the user clicks "Push to Mailchimp". Data sent: the rendered HTML email template. See Mailchimp's privacy policy: https://mailchimp.com/legal/privacy/

**Brevo** (https://brevo.com)
Used to push email templates to a Brevo account. Requires the user to provide their own Brevo API key. Only called when the user clicks "Push to Brevo". Data sent: the rendered HTML email template. See Brevo's privacy policy: https://www.brevo.com/legal/privacypolicy/

**Klaviyo** (https://klaviyo.com)
Used to push email templates to a Klaviyo account. Requires the user to provide their own Klaviyo API key. Only called when the user clicks "Push to Klaviyo". Data sent: the rendered HTML email template. See Klaviyo's privacy policy: https://www.klaviyo.com/legal/privacy-notice

**ActiveCampaign** (https://activecampaign.com)
ActiveCampaign merge tags are generated in exported HTML for use in ActiveCampaign campaigns. No API connection is made — the plugin does not transmit data to ActiveCampaign. See ActiveCampaign's privacy policy: https://www.activecampaign.com/legal/privacy-policy

**Campaign Monitor** (https://campaignmonitor.com)
Used to push email templates to a Campaign Monitor account. Requires the user to provide their own Campaign Monitor API key. Only called when the user clicks "Push to Campaign Monitor". Data sent: the rendered HTML email template. See Campaign Monitor's privacy policy: https://www.campaignmonitor.com/policies/privacy/

**OneSignal** (https://onesignal.com)
Used to push email templates to a OneSignal account. Requires the user to provide their own OneSignal REST API key. Only called when the user clicks "Push to OneSignal". Data sent: the rendered HTML email template. See OneSignal's privacy policy: https://onesignal.com/privacy_policy

**Vimeo oEmbed API** (https://vimeo.com)
Used to fetch video thumbnails when a Vimeo URL is inserted into the Video block. Only called when a Vimeo URL is entered. Data sent: the Vimeo video URL. See Vimeo's privacy policy: https://vimeo.com/privacy

**Freemius** (https://freemius.com)
Used for plugin licensing and update delivery. Collects anonymised usage data on plugin activation (opt-in). The plugin is configured with `is_org_compliant = true`, meaning Freemius operates in WordPress.org-compliant mode. See Freemius's privacy policy: https://freemius.com/privacy/

== Screenshots ==

1. The email builder editor
2. Preset settings
3. Template chooser
4. Export modal

== Upgrade Notice ==

= 1.0.0 =
Initial release.

== Changelog ==

= 1.0.0 =
* Initial release.
* Gutenberg-based email editor with email-safe block rendering.
* Blocks: Header, Subheader, Text, Image, Button, Divider, Spacer, Logo, Menu, Social, Footer, HTML, Columns, Section.
* Preset system for brand settings (typography, colours, logo, dark mode).
* Starter template library and saved templates.
* One-click HTML export with copy and download.
* Direct push to Mailchimp, Klaviyo, Brevo, Campaign Monitor, and OneSignal.
* HTML export with merge tags for ActiveCampaign, HubSpot, EmailOctopus, GetResponse and ConvertKit.
* Dark mode support.
* UTM parameter tracking.
* WooCommerce blocks (Pro): Product, Order Table, Coupon.
* Reusable Section modules (Pro).
