# Changelog

All notable changes to WP Mailblox are documented here.

---

## [1.0.0] — 2026-04-07

### Initial release

#### Core builder
- Gutenberg-based email template editor with email-safe block rendering
- Blocks: Header, Subheader, Text, Image, Button, Divider, Spacer, Logo, Menu, Social, Footer, HTML, Columns (1–4 column layouts with gap control), Section (full-width background colour + image support)
- MSO-safe column layout using ghost table technique for Outlook compatibility
- Mobile-responsive CSS with per-block mobile padding and alignment overrides
- Hide on mobile toggle per block
- Mobile preview — editor canvas reflects mobile-specific attribute values (padding, alignment, font size, hide-on-mobile) when the Gutenberg device preview is set to Mobile; mobile preview iframe width is 410 px; tablet option removed
- Image block display width is auto-derived from column context — no manual input required

#### Presets
- Preset system for storing brand settings (typography, colours, logo, dark mode)
- Web-safe and Google Fonts support with per-role font stacks (heading, subheading, body, button)
- Default button background and text colour stored per preset; all button blocks inherit preset colour unless overridden at block level
- Per-preset platform selection
- Per-template platform override
- Preset import / export as JSON (for moving presets between sites)

#### Templates
- Template chooser modal on new email — starter templates and saved templates
- Save any email as a reusable template
- Template tagging with tag filter in the chooser modal
- Starter template library (newsletter, promotional, two-column, transactional, blank)

#### Modules (Pro)
- Save any Section block as a reusable module via the block toolbar
- Insert saved modules from the block inserter
- Modules stored in a dedicated hidden CPT (`eb_module`)
- Delete modules from the picker UI

#### Export & platform push
- One-click HTML export with copy and download actions
- Export validation warnings (missing subject, preheader, preset, localhost image URLs)
- Subject line and preheader meta fields with live character counter
- Direct template push to Mailchimp, Klaviyo, and Brevo
- Campaign Monitor push via temporary public URL
- HubSpot: export-only (API does not support raw HTML template upload)

#### WooCommerce
- Product block — renders product image, title, price (with sale price support), and CTA button
- Order table block — transactional order summary with alternating row styles, configurable totals rows, and View Order button; dynamic line item loop for Klaviyo and Brevo
- Product recommendations block — grid of products filtered by category, sortable by newest / best selling / top rated / price / random, configurable count and columns
- Coupon block
- All WooCommerce blocks inherit preset button colour by default, overridable per block

#### Dark mode
- Full dark mode support via `prefers-color-scheme` and Outlook-safe `[data-ogsc]` selectors
- Per-section dark background colour overrides
- Per-template dark mode background override
- Dark mode logo swap (light/dark logo per preset and per logo block)
- Outlook fix: dark logo wrapped in `<!--[if !mso]><!-->`conditional so Outlook never renders both logos simultaneously

#### UTM tracking
- Per-template UTM defaults (source, medium, campaign, content, term)
- Per-block UTM override
- UTM parameters appended only to real HTTP/HTTPS URLs — merge tags skipped

#### Background images
- Section and Columns blocks support background images with repeat, horizontal position, vertical position, width, and height controls
- Background size width options: Cover, Contain, Auto, 100%
- Background size height options (shown when width is Auto or 100%): Auto, 100%
- Background image preview shown in inspector panel beneath the Replace Image button

#### Editor UI
- All boolean settings use the WordPress `ToggleControl` toggle switch style throughout
- All small mutually-exclusive option sets (alignment, badge shape, background repeat, background position, font type, background size) use a consistent button-group toggle style (`EBButtonGroup`) matching the Desktop / Mobile responsive toggle
- Desktop / Mobile responsive toggle (`EBResponsiveToggle`) and button groups carry CSS classes (`eb-toggle-group`, `eb-responsive-toggle`, `eb-button-group-field`) for styling
- `EBAlignControl` shared component updated to use button-group style
- Preset admin: font type selector (Web Safe / Google Font) converted from radio buttons to button-group toggle
- Preset admin: Enable Dark Mode Logo uses native WordPress toggle switch (via `wp-components` stylesheet)
- Reactive auto-contrast text colours in editor — heading, subheader, text, and WooCommerce blocks update immediately when parent section or columns background colour changes, without requiring a page refresh
- All column-level blocks declare `parent` constraints, preventing them from appearing in the root-level block inserter

#### Admin
- Onboarding wizard on first activation (preset name → brand → done)
- Settings page with licence status, usage counters, and platform API key management
- Free plan limits: 5 email templates, 1 preset (enforced on save and Add New)
- Export preset / import preset JSON buttons on preset edit screen
- Live preheader character counter
- Google Fonts preview in block editor
- Footer branding on free-plan exports ("Sent with WP Mailblox" badge); removed automatically on Pro

#### Email compatibility fixes
- Gmail / web.de: fixed two-column layouts stacking vertically — outer table uses fixed pixel width and containing TD uses `align="left"` to preserve float context
- Outlook: fixed column MSO table widths incorrectly using container width instead of available width after padding
