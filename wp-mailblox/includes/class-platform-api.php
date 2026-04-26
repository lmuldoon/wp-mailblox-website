<?php
// /includes/class-platform-api.php

if (!defined('ABSPATH')) exit;

/**
 * Handles pushing email templates directly to external email platforms.
 *
 * Supported platforms:
 *   - Mailchimp
 *   - Brevo
 *   - Klaviyo
 *   - ActiveCampaign
 *   - Campaign Monitor (served via signed public URL)
 *
 * Each push() call resolves the effective platform for the template, generates
 * the HTML via EB_Admin::export_email(), and sends it to the appropriate API.
 * On success, the returned platform template ID is stored as post meta so
 * subsequent pushes update rather than duplicate the template.
 */
class EB_Platform_API
{
    /**
     * Push an email template to its configured platform.
     *
     * @param  int   $post_id  email_template post ID
     * @return array { success: bool, message: string, url: string|null }
     */
    public function push($post_id)
    {
        // Resolve platform: template override → preset default → mailchimp
        $platform  = get_post_meta($post_id, 'eb_platform', true);
        if (!$platform) {
            $preset_id = get_post_meta($post_id, 'eb_preset', true);
            $platform  = get_post_meta($preset_id, 'eb_platform', true) ?: 'mailchimp';
        }

        // Generate HTML
        $admin = new EB_Admin();
        $html  = $admin->export_email($post_id);

        if (empty($html)) {
            return ['success' => false, 'message' => 'Could not generate email HTML. Ensure a preset is selected.', 'url' => null];
        }

        // Check for localhost/private image URLs that won't resolve from the platform
        $warnings = $this->check_image_urls($html);

        switch ($platform) {
            case 'mailchimp':
                $result = $this->push_mailchimp($post_id, $html);
                break;
            case 'brevo':
                $result = $this->push_brevo($post_id, $html);
                break;
            case 'klaviyo':
                $result = $this->push_klaviyo($post_id, $html);
                break;
            case 'campaign_monitor':
                $result = $this->push_campaign_monitor($post_id);
                break;
            case 'onesignal':
                $result = $this->push_onesignal($post_id, $html);
                break;
            default:
                return [
                    'success' => false,
                    'message' => '"' . esc_html(ucwords(str_replace('_', ' ', $platform))) . '" does not support direct push yet.',
                    'url'     => null,
                ];
        }

        if ($result['success'] && !empty($warnings)) {
            $result['warnings'] = $warnings;
        }

        return $result;
    }

    // -------------------------------------------------------------------------
    // Mailchimp
    // -------------------------------------------------------------------------

    private function push_mailchimp($post_id, $html)
    {
        $api_key = get_option('eb_api_mailchimp_key', '');

        if (empty($api_key)) {
            return ['success' => false, 'message' => 'Mailchimp API key is not configured. Go to WP Mailblox → Settings → Platform Connections.', 'url' => null];
        }

        // Mailchimp API key format: {key}-{datacenter}  e.g. abc123def-us12
        $parts = explode('-', $api_key);
        $dc    = end($parts);

        if (count($parts) < 2 || empty($dc)) {
            return ['success' => false, 'message' => 'Invalid Mailchimp API key format. Expected: key-datacenter (e.g. abc123-us12).', 'url' => null];
        }

        $name        = get_the_title($post_id) ?: 'Email Template ' . $post_id;
        $template_id = get_post_meta($post_id, 'eb_mailchimp_template_id', true);

        $endpoint = "https://{$dc}.api.mailchimp.com/3.0/templates";
        $method   = 'POST';

        if ($template_id) {
            $endpoint .= '/' . intval($template_id);
            $method    = 'PATCH';
        }

        $response = wp_remote_request($endpoint, [
            'method'  => $method,
            'headers' => [
                'Authorization' => 'Basic ' . base64_encode('anystring:' . $api_key),
                'Content-Type'  => 'application/json',
            ],
            'body'    => wp_json_encode(['name' => $name, 'html' => $html]),
            'timeout' => 30,
        ]);

        return $this->handle_response($response, $post_id, 'mailchimp', $template_id, function($body) {
            return $body['id'] ?? null;
        }, "https://{$dc}.admin.mailchimp.com/templates/");
    }

    // -------------------------------------------------------------------------
    // Brevo
    // -------------------------------------------------------------------------

    private function push_brevo($post_id, $html)
    {
        $api_key      = get_option('eb_api_brevo_key', '');
        $sender_name  = get_option('eb_api_brevo_sender_name', '');
        $sender_email = get_option('eb_api_brevo_sender_email', '');

        if (empty($api_key)) {
            return ['success' => false, 'message' => 'Brevo API key is not configured. Go to WP Mailblox → Settings → Platform Connections.', 'url' => null];
        }

        if (empty($sender_name) || empty($sender_email)) {
            return ['success' => false, 'message' => 'Brevo requires a sender name and email. Configure them in WP Mailblox → Settings → Platform Connections.', 'url' => null];
        }

        $name        = get_the_title($post_id) ?: 'Email Template ' . $post_id;
        $subject     = get_post_meta($post_id, 'eb_subject', true) ?: $name;
        $template_id = get_post_meta($post_id, 'eb_brevo_template_id', true);

        $endpoint = 'https://api.brevo.com/v3/smtp/templates';
        $method   = 'POST';

        if ($template_id) {
            $endpoint .= '/' . intval($template_id);
            $method    = 'PUT';
        }

        $response = wp_remote_request($endpoint, [
            'method'  => $method,
            'headers' => [
                'api-key'      => $api_key,
                'Content-Type' => 'application/json',
            ],
            'body'    => wp_json_encode([
                'templateName' => $name,
                'subject'      => $subject,
                'htmlContent'  => $html,
                'sender'       => ['name' => $sender_name, 'email' => $sender_email],
            ]),
            'timeout' => 30,
        ]);

        return $this->handle_response($response, $post_id, 'brevo', $template_id, function($body) {
            return $body['id'] ?? null;
        }, 'https://app.brevo.com/email-templates/list');
    }

    // -------------------------------------------------------------------------
    // Klaviyo
    // -------------------------------------------------------------------------

    private function push_klaviyo($post_id, $html)
    {
        $api_key = get_option('eb_api_klaviyo_key', '');

        if (empty($api_key)) {
            return ['success' => false, 'message' => 'Klaviyo API key is not configured. Go to WP Mailblox → Settings → Platform Connections.', 'url' => null];
        }

        $name        = get_the_title($post_id) ?: 'Email Template ' . $post_id;
        $template_id = get_post_meta($post_id, 'eb_klaviyo_template_id', true);

        $headers = [
            'Authorization' => 'Klaviyo-API-Key ' . $api_key,
            'revision'      => '2023-12-15',
            'Content-Type'  => 'application/json',
        ];

        if ($template_id) {
            // Update existing template
            $response = wp_remote_request("https://a.klaviyo.com/api/templates/{$template_id}/", [
                'method'  => 'PATCH',
                'headers' => $headers,
                'body'    => wp_json_encode([
                    'data' => [
                        'type'       => 'template',
                        'id'         => $template_id,
                        'attributes' => ['name' => $name, 'html' => $html],
                    ],
                ]),
                'timeout' => 30,
            ]);
        } else {
            // Create new template
            $response = wp_remote_post('https://a.klaviyo.com/api/templates/', [
                'headers' => $headers,
                'body'    => wp_json_encode([
                    'data' => [
                        'type'       => 'template',
                        'attributes' => [
                            'name'        => $name,
                            'editor_type' => 'CODE',
                            'html'        => $html,
                        ],
                    ],
                ]),
                'timeout' => 30,
            ]);
        }

        return $this->handle_response($response, $post_id, 'klaviyo', $template_id, function($body) {
            return $body['data']['id'] ?? null;
        }, 'https://www.klaviyo.com/email-template/list');
    }

    // -------------------------------------------------------------------------
    // OneSignal
    // -------------------------------------------------------------------------

    private function push_onesignal($post_id, $html)
    {
        $api_key = get_option('eb_api_onesignal_key', '');
        $app_id  = get_option('eb_api_onesignal_app_id', '');

        if (empty($api_key) || empty($app_id)) {
            return ['success' => false, 'message' => 'OneSignal REST API key and App ID are required. Go to WP Mailblox → Settings → Platform Connections.', 'url' => null];
        }

        $name        = get_the_title($post_id) ?: 'Email Template ' . $post_id;
        $subject     = get_post_meta($post_id, 'eb_subject', true) ?: $name;
        $template_id = get_post_meta($post_id, 'eb_onesignal_template_id', true);

        $body = wp_json_encode([
            'app_id'        => $app_id,
            'name'          => $name,
            'isEmail'       => true,
            'email_subject' => $subject,
            'email_body'    => $html,
        ]);

        $headers = [
            'Authorization' => 'Key ' . $api_key,
            'Content-Type'  => 'application/json',
        ];

        if ($template_id) {
            $response = wp_remote_request("https://onesignal.com/api/v1/templates/{$template_id}", [
                'method'  => 'PATCH',
                'headers' => $headers,
                'body'    => $body,
                'timeout' => 30,
            ]);
        } else {
            $response = wp_remote_post('https://onesignal.com/api/v1/templates', [
                'headers' => $headers,
                'body'    => $body,
                'timeout' => 30,
            ]);
        }

        return $this->handle_response($response, $post_id, 'onesignal', $template_id, function($body) {
            return $body['id'] ?? null;
        }, 'https://app.onesignal.com/apps/' . $app_id . '/templates');
    }

    // -------------------------------------------------------------------------
    // Campaign Monitor
    // -------------------------------------------------------------------------

    /**
     * Campaign Monitor does not accept HTML directly — it fetches from a URL.
     * We generate a signed one-time token, expose a temporary public endpoint
     * that serves the rendered HTML, and pass that URL to the CM API.
     * The token expires in 5 minutes and is deleted on first use.
     */
    private function push_campaign_monitor($post_id)
    {
        $api_key   = get_option('eb_api_campaign_monitor_key', '');
        $client_id = get_option('eb_api_campaign_monitor_client_id', '');

        if (empty($api_key)) {
            return ['success' => false, 'message' => 'Campaign Monitor API key is not configured. Go to WP Mailblox → Settings → Platform Connections.', 'url' => null];
        }

        if (empty($client_id)) {
            return ['success' => false, 'message' => 'Campaign Monitor client ID is not configured. Go to WP Mailblox → Settings → Platform Connections.', 'url' => null];
        }

        // Reject localhost / private network — CM servers cannot reach these
        $site_host = parse_url(home_url(), PHP_URL_HOST);
        if ($site_host === 'localhost' || preg_match('/^(127\.|192\.168\.|10\.|172\.(1[6-9]|2\d|3[01])\.)/', $site_host)) {
            return ['success' => false, 'message' => 'Campaign Monitor requires a publicly accessible site URL. Push is not available on localhost or private network addresses.', 'url' => null];
        }

        $name        = get_the_title($post_id) ?: 'Email Template ' . $post_id;
        $template_id = get_post_meta($post_id, 'eb_campaign_monitor_template_id', true);

        // Generate a signed token — Campaign Monitor will fetch the URL within seconds
        $token = wp_generate_password(32, false);
        set_transient('eb_cm_token_' . $post_id, $token, 5 * MINUTE_IN_SECONDS);

        $html_url = add_query_arg(
            ['eb_cm_html' => $post_id, 'token' => $token],
            home_url('/')
        );

        if ($template_id) {
            $endpoint = 'https://api.createsend.com/api/v3.3/templates/' . $template_id . '.json';
            $method   = 'PUT';
        } else {
            $endpoint = 'https://api.createsend.com/api/v3.3/templates/' . $client_id . '.json';
            $method   = 'POST';
        }

        $response = wp_remote_request($endpoint, [
            'method'  => $method,
            'headers' => [
                // Campaign Monitor uses HTTP Basic Auth: API key as username, 'x' as password
                'Authorization' => 'Basic ' . base64_encode($api_key . ':x'),
                'Content-Type'  => 'application/json',
            ],
            'body'    => wp_json_encode([
                'Name'        => $name,
                'HtmlPageURL' => $html_url,
            ]),
            'timeout' => 30,
        ]);

        return $this->handle_response($response, $post_id, 'campaign_monitor', $template_id, function($body) {
            // Create returns a plain JSON string (the template ID); update returns nothing
            return is_string($body) ? $body : null;
        }, 'https://app.createsend.com/templates');
    }

    // -------------------------------------------------------------------------
    // Shared response handler
    // -------------------------------------------------------------------------

    /**
     * @param WP_Error|array $response       wp_remote_* response
     * @param int            $post_id
     * @param string         $platform_slug
     * @param string|null    $existing_id    already-stored template ID (null = create)
     * @param callable       $id_extractor   extracts the template ID from response body
     * @param string         $view_url       URL to the template list in the platform
     */
    private function handle_response($response, $post_id, $platform_slug, $existing_id, $id_extractor, $view_url)
    {
        if (is_wp_error($response)) {
            return ['success' => false, 'message' => $response->get_error_message(), 'url' => null];
        }

        $code = wp_remote_retrieve_response_code($response);
        $body = json_decode(wp_remote_retrieve_body($response), true);

        if ($code >= 200 && $code < 300) {
            $returned_id = call_user_func($id_extractor, $body ?? []);

            if ($returned_id && !$existing_id) {
                update_post_meta($post_id, "eb_{$platform_slug}_template_id", $returned_id);
            }

            update_post_meta($post_id, 'eb_last_pushed', current_time('mysql'));
            update_post_meta($post_id, 'eb_last_pushed_platform', $platform_slug);

            $verb  = $existing_id ? 'updated in' : 'created in';
            $label = ucwords(str_replace('_', ' ', $platform_slug));

            return ['success' => true, 'message' => "Template {$verb} {$label}.", 'url' => $view_url];
        }

        // Extract a readable error from common API response shapes
        // Campaign Monitor uses PascalCase keys; others use lowercase
        $error = $body['detail'] ?? $body['title'] ?? $body['message'] ?? $body['Message']
              ?? ($body['errors'][0]['detail'] ?? ($body['errors'][0] ?? null))
              ?? 'Unknown error (HTTP ' . $code . ')';

        $label = ucwords(str_replace('_', ' ', $platform_slug));
        return ['success' => false, 'message' => "{$label} error: {$error}", 'url' => null];
    }

    // -------------------------------------------------------------------------
    // Image URL checker
    // -------------------------------------------------------------------------

    private function check_image_urls($html)
    {
        $warnings = [];
        preg_match_all('/<img[^>]+src=["\']([^"\']+)["\']/', $html, $matches);

        foreach ($matches[1] as $src) {
            $host = parse_url($src, PHP_URL_HOST);
            if (!$host) continue;
            if ($host === 'localhost' || preg_match('/^(127\.|192\.168\.|10\.|172\.(1[6-9]|2\d|3[01])\.)/', $host)) {
                $warnings[] = 'One or more images use a local URL (' . esc_html($host) . ') that will not resolve from the platform servers. Upload images to a public URL before pushing.';
                break; // One warning is enough
            }
        }

        return $warnings;
    }
}
