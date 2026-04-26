<?php
if (!defined('ABSPATH')) exit;

$view_online_tag = $tags['WEBVERSION'] ?? '#';
$unsubscribe_tag = $tags['UNSUB'] ?? '#';

$is_preview     = isset($_GET['eb_preview']) || isset($_GET['eb_share']);
$is_preview_bar = $is_preview && !isset($_GET['eb_raw']);

// Read dark mode values from $styles directly — extracted variables may be
// overwritten by block templates included during eb_render_blocks().
$dark_bg_color          = $styles['dark_bg_color']          ?? '#121212';
$dark_text_color        = $styles['dark_text_color']        ?? '#ffffff';
$dark_button_color      = $styles['dark_button_color']      ?? '#ffffff';
$dark_button_text_color = $styles['dark_button_text_color'] ?? '#000000';
$dark_link_color        = $styles['dark_link_color']        ?? '#ffffff';
?>

                </td>
              </tr>
            </table>
        </td>
      </tr>
    </table>
  </div>

<?php if ($is_preview) : ?>
<style>
    /* Dark mode simulation class — present in all preview modes including iframes */
    body.eb-simulated-dark,
    body.eb-simulated-dark #body,
    body.eb-simulated-dark .email-container,
    body.eb-simulated-dark .eb-wrapper-color,
    body.eb-simulated-dark td.email-bg {
        background-color: <?php echo esc_attr($dark_bg_color ?? '#121212'); ?> !important;
    }

    body.eb-simulated-dark .eb-dark-text-colour-enabled .dark-mode-text,
    body.eb-simulated-dark h1,
    body.eb-simulated-dark h2,
    body.eb-simulated-dark h3,
    body.eb-simulated-dark h4,
    body.eb-simulated-dark h5,
    body.eb-simulated-dark h6,
    body.eb-simulated-dark .eb-dark-text-colour-enabled td,
    body.eb-simulated-dark .eb-dark-text-colour-enabled .dark-mode-text span,
    body.eb-simulated-dark li {
        color: <?php echo esc_attr($dark_text_color ?? '#ffffff'); ?> !important;
    }

    body.eb-simulated-dark svg path {
        fill: <?php echo esc_attr($dark_text_color ?? '#ffffff'); ?> !important;
    }

    body.eb-simulated-dark a,
    body.eb-simulated-dark a.eb-link {
        color: <?php echo esc_attr($dark_link_color ?? '#ffffff'); ?> !important;
    }

    body.eb-simulated-dark a.button {
        background-color: <?php echo esc_attr($dark_button_color ?? '#ffffff'); ?> !important;
        color: <?php echo esc_attr($dark_button_text_color ?? '#000000'); ?> !important;
    }

    body.eb-simulated-dark td.section-td {
        background-color: <?php echo esc_attr($dark_bg_color ?? '#121212'); ?> !important;
    }

    /* Order table row backgrounds */
    body.eb-simulated-dark td.eb-order-row,
    body.eb-simulated-dark td.eb-order-totals-row {
        background-color: transparent !important;
    }
    body.eb-simulated-dark td.eb-order-row-alt {
        background-color: rgba(255,255,255,0.05) !important;
    }

    body.eb-simulated-dark .eb-logo-light,
    body.eb-simulated-dark .eb-img-light {
        display: none !important;
    }

    body.eb-simulated-dark .eb-logo-dark {
        display: inline-block !important;
    }

    body.eb-simulated-dark .eb-img-dark {
        display: block !important;
    }

    <?php
    foreach (eb_get_dark_section_colors() as $class => $color) {
        echo 'body.eb-simulated-dark td.' . $class . ' { background-color: ' . esc_attr($color) . ' !important; }' . "\n";
    }
    ?>
</style>
<?php endif; ?>

<?php if ($is_preview_bar) : ?>
<style>
    #eb-preview-bar {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background: #1e1e1e;
        color: #ffffff;
        padding: 12px 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        font-size: 13px;
        z-index: 9999;
        box-shadow: 0 -2px 10px rgba(0,0,0,0.3);
    }

    #eb-preview-bar span {
        color: #aaaaaa;
    }

    #eb-dark-toggle {
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        background: #333333;
        border: 1px solid #555555;
        border-radius: 6px;
        padding: 6px 12px;
        color: #ffffff;
        font-size: 13px;
        transition: background 0.2s;
    }

    #eb-dark-toggle:hover {
        background: #444444;
    }

    #eb-dark-toggle .toggle-icon {
        font-size: 16px;
    }

    #eb-dark-toggle.active {
        background: #2271b1;
        border-color: #2271b1;
    }
</style>

<div id="eb-preview-bar">
    <span>📧 Email Preview</span>

    <button id="eb-dark-toggle" onclick="toggleDarkMode()">
        <span class="toggle-icon">🌙</span>
        <span class="toggle-label">Dark Mode Off</span>
    </button>

    <span>Dark mode is simulated — actual rendering varies by email client.</span>
</div>

<div style="height: 60px;"></div>

<script>
    function toggleDarkMode() {
        var body    = document.body;
        var btn     = document.getElementById('eb-dark-toggle');
        var label   = btn.querySelector('.toggle-label');
        var icon    = btn.querySelector('.toggle-icon');
        var isDark  = body.classList.toggle('eb-simulated-dark');

        if (isDark) {
            label.textContent = 'Dark Mode On';
            icon.textContent  = '☀️';
            btn.classList.add('active');
        } else {
            label.textContent = 'Dark Mode Off';
            icon.textContent  = '🌙';
            btn.classList.remove('active');
        }
    }
</script>
<?php endif; ?>

<?php if ( ! function_exists('eb_is_pro') || ! eb_is_pro() ) : ?>
<table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr>
        <td align="center" style="padding: 20px 0 20px;">
            <a href="https://wpmailblox.com" target="_blank" style="display:inline-block; border:none; text-decoration:none;">
                <img src="<?php echo esc_url( EB_PLUGIN_URL . 'assets/images/branding.png' ); ?>"
                     alt="Sent with WP Mailblox"
                     width="120"
                     style="display:block; border:none; outline:none; max-width:120px; height:auto;">
            </a>
        </td>
    </tr>
</table>
<?php endif; ?>

</body>
</html>
