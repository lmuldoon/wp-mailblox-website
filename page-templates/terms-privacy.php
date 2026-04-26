<?php

/**
 * Terms & Privacy Policy page.
 */

global $meta;

$meta->title = 'Terms & Privacy Policy — WP Mailblox';
$meta->description = 'Terms of use and privacy policy for WP Mailblox and wpmailblox.com.';

get_header();

?>

<article>

	<div class="sections-header section bg-darkblue">
		<div class="container">
			<div class="animated-up">
				<h1 class="text-white">Terms &amp; Privacy Policy</h1>
				<p style="color: rgba(255,255,255,0.75);">Last updated: April 2025</p>
			</div>
		</div>
	</div>

	<div class="section">
		<div class="container animated-up">
			<div class="text-container content">

				<h2>Terms of Use</h2>

				<h3>Use of the plugin</h3>
				<p>WP Mailblox is a WordPress plugin distributed under the <a href="https://www.gnu.org/licenses/gpl-2.0.html" target="_blank" rel="noopener">GNU General Public License v2.0 or later (GPL-2.0+)</a>. You are free to use, modify and distribute the plugin subject to the terms of that licence.</p>

				<h3>Pro licence</h3>
				<p>WP Mailblox Pro licences are sold through Freemius. By purchasing a licence you agree to the Freemius <a href="https://freemius.com/terms/" target="_blank" rel="noopener">Terms of Service</a>. Licences are per-site and non-transferable unless otherwise stated at the time of purchase.</p>
				<p>Refunds are available within 30 days of purchase if the plugin does not work as described and the issue cannot be resolved through support. Refund requests should be made via the support contact on this site.</p>

				<h3>Limitation of liability</h3>
				<p>WP Mailblox is provided "as is", without warranty of any kind. We are not liable for any direct, indirect, incidental or consequential damages arising from your use of the plugin, including but not limited to email delivery failures, data loss or API service interruptions from third-party platforms.</p>

				<h3>Third-party services</h3>
				<p>WP Mailblox integrates with third-party email platforms (Mailchimp, Brevo, Klaviyo, Campaign Monitor, ActiveCampaign, HubSpot). Use of these services is subject to their own terms and privacy policies. WP Mailblox passes your API credentials and template HTML to these services as required to perform the integration. We do not store or transmit your API credentials outside your WordPress installation.</p>

				<hr>

				<h2>Privacy Policy</h2>

				<h3>This website (wpmailblox.com)</h3>
				<p>This site uses cookies and similar technologies. Some are strictly necessary for the site to function; others are used for analytics purposes to help us understand how visitors use the site and how we can improve it.</p>

				<h4>Analytics</h4>
				<p>We use <strong>Google Analytics</strong> to collect anonymised data about site usage — such as pages visited, time on site, and general geographic region. This data is aggregated and does not identify you personally. Google Analytics cookies are only set if you have consented to analytics cookies. For more information on how Google handles data collected via Analytics, see <a href="https://policies.google.com/privacy" target="_blank" rel="noopener">Google's Privacy Policy</a>.</p>

				<h4>Cookie consent</h4>
				<p>We use <strong>CookieHub</strong> to manage cookie consent on this site. When you first visit, a banner will invite you to accept or decline non-essential cookies. You can review or change your preferences at any time using the button below.</p>

				<p><button type="button" class="button button--outline button--sm" onclick="CookieHub.openSettings(); return false;">Manage Cookie Preferences</button></p>

				<h4>Contact and enquiries</h4>
				<p>If you contact us via a form or email, we retain your message and contact details solely for the purpose of responding to your enquiry. We do not sell or share your personal information with third parties.</p>

				<h3>The WP Mailblox plugin</h3>
				<p>The WP Mailblox plugin itself does not collect, transmit or store any personal data from your website visitors. It operates entirely within your WordPress installation.</p>

				<h4>Data sent to third-party email platforms</h4>
				<p>When you use the direct platform push feature (Pro), WP Mailblox sends your email template HTML to the selected platform's API. No visitor or subscriber data is transmitted — only the template structure and content you have built.</p>

				<h4>Freemius licensing</h4>
				<p>WP Mailblox uses <a href="https://freemius.com" target="_blank" rel="noopener">Freemius</a> for licence management. Freemius may collect diagnostic and usage data from your WordPress installation as part of the licence activation process. You can opt out during activation. Please refer to the <a href="https://freemius.com/privacy/" target="_blank" rel="noopener">Freemius Privacy Policy</a> for full details of what data is collected and how it is used.</p>

				<h3>Contact</h3>
				<p>If you have questions about this policy or wish to request deletion of any data we hold, please contact us via the support link in the WordPress plugin directory listing.</p>

			</div>
		</div>
	</div>

</article>

<?php get_footer(); ?>
