<?php
if (!defined('ABSPATH')) exit;

// Load header template
include EB_PLUGIN_PATH . 'templates/header.php';
?>

<table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" class="responsive">
<tr>
<td style="font-family:<?php echo esc_attr($body_font); ?>;">

<?php

// Output final content
echo $content_html;
?>

</td>
</tr>
</table>

<?php
// Load footer template
include EB_PLUGIN_PATH . 'templates/footer.php';