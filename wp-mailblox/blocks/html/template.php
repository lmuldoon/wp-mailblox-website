<?php
if (!defined('ABSPATH')) exit;

$content = $template_attributes['content'] ?? '';

if (empty(trim($content))) return;
?>
<?php echo $content; ?>
