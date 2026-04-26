<?php
if (!defined('ABSPATH')) exit;

$preset_id = get_post_meta($post->ID, 'eb_preset', true);

$defaults = [
  'container_width' => 640,
  'heading_font'           => 'Arial',
  'heading_font_stack'     => 'Arial, Helvetica, sans-serif',
  'heading_font_type'      => 'websafe',
  'heading_font_weight'    => 700,
  'heading_size'           => 28,
  'heading_size_mobile'    => 24,
  'heading_line_height'    => 1.3,
  'subheading_font'        => 'Arial',
  'subheading_font_stack'  => 'Arial, Helvetica, sans-serif',
  'subheading_font_type'   => 'websafe',
  'subheading_font_weight' => 400,
  'subheading_size'        => 24,
  'subheading_size_mobile' => 20,
  'subheading_line_height' => 1.3,
  'body_font'              => 'Helvetica',
  'body_font_stack'        => 'Helvetica, Arial, sans-serif',
  'body_font_type'         => 'websafe',
  'body_font_weight'       => 400,
  'body_size'              => 18,
  'body_size_mobile'       => 16,
  'body_line_height'       => 1.5,
  'button_font'            => 'Helvetica',
  'button_font_stack'      => 'Helvetica, Arial, sans-serif',
  'button_font_type'       => 'websafe',
  'button_font_weight'     => 700,
  'button_size'            => 16,
  'button_size_mobile'     => 14,
  'button_color'           => '#000000',
  'button_text_color'      => '',
  'bg_color'               => '#ffffff',
  'platform'               => 'mailchimp',
  'dark_bg_color'          => '#121212',
  'dark_text_color'        => '#ffffff',
  'dark_button_color'      => '#ffffff',
  'dark_button_text_color' => '#000000',
  'dark_link_color'        => '#ffffff',
];

$preset_settings = eb_get_preset_settings($preset_id);
$styles          = array_merge($defaults, $preset_settings);
// Guard — if no preset is selected, bail out gracefully
if (!$preset_id) {
  echo '<p style="font-family: sans-serif; padding: 20px; color: #cc0000;">No preset selected for this email. Please select a preset before previewing or exporting.</p>';
  exit;
}

// Check for per-email dark mode background override
$dark_bg_override = get_post_meta($post->ID, 'eb_dark_bg_color_override', true);
if (!empty($dark_bg_override)) {
  $styles['dark_bg_color'] = $dark_bg_override;
}

// Extract for easy use in template
extract($styles);

// --- Load platform JSON tags ---
$platform      = $platform_override
              ?? get_post_meta($post->ID, 'eb_platform', true)
              ?: get_post_meta($preset_id, 'eb_platform', true)
              ?: 'mailchimp';
$platform_file = EB_PLUGIN_PATH . 'platforms/' . sanitize_file_name($platform) . '.json';

$tags = [];

if (file_exists($platform_file)) {
  $json = file_get_contents($platform_file);
  $data = json_decode($json, true);

  if ($data) {
    $flatten_tags = function ($array) use (&$flatten_tags) {
      $flat = [];
      foreach ($array as $key => $value) {
        if (is_array($value) && isset($value['tag'])) {
          // New format — extract just the tag value
          $flat[$key] = $value['tag'];
        } elseif (is_array($value)) {
          // Group level — recurse into it
          $flat = array_merge($flat, $flatten_tags($value));
        } else {
          // Old format fallback
          $flat[$key] = $value;
        }
      }
      return $flat;
    };

    $tags = $flatten_tags($data);
  }
}

// Render blocks FIRST so section colour registry gets populated
$blocks       = parse_blocks($post->post_content);
$content_html = eb_render_blocks($blocks, $post->ID, $tags, $platform_override ?? null);

// Replace placeholders in content
if (!empty($tags)) {
  foreach ($tags as $key => $tag) {
    $content_html = str_replace('[[' . $key . ']]', $tag, $content_html);
  }
}

$subject_tag = get_post_meta($post->ID, 'eb_subject', true) ?: ($tags['SUBJECT'] ?? '');

// Build per-section dark mode CSS from registry
$dark_section_css      = '';
$ogsb_dark_section_css = '';

$dark_sections = eb_get_dark_section_colors();

if (!empty($dark_sections)) {
  foreach ($dark_sections as $class => $color) {
    $dark_section_css .= "\n      td." . $class . " { background-color: " . esc_attr($color) . " !important; }";
    $ogsb_dark_section_css .= "\n     [data-ogsb] td." . $class . " { background-color: " . esc_attr($color) . " !important; }";
  }
}

// --- Build Google Fonts import (with weights) ---
// Collect each Google font and the weights needed for it.
$google_font_weights = []; // [ 'FontName' => [400, 700, ...] ]

$font_groups = [
  'heading'    => ['type' => $styles['heading_font_type'] ?? 'websafe',    'font' => $styles['heading_font'] ?? '',    'weight' => intval($styles['heading_font_weight'] ?? 400)],
  'subheading' => ['type' => $styles['subheading_font_type'] ?? 'websafe', 'font' => $styles['subheading_font'] ?? '', 'weight' => intval($styles['subheading_font_weight'] ?? 400)],
  'body'       => ['type' => $styles['body_font_type'] ?? 'websafe',       'font' => $styles['body_font'] ?? '',       'weight' => intval($styles['body_font_weight'] ?? 400)],
  'button'     => ['type' => $styles['button_font_type'] ?? 'websafe',     'font' => $styles['button_font'] ?? '',     'weight' => intval($styles['button_font_weight'] ?? 700)],
];

foreach ($font_groups as $group) {
  if ($group['type'] === 'google' && !empty($group['font'])) {
    $name = $group['font'];
    if (!isset($google_font_weights[$name])) {
      $google_font_weights[$name] = [];
    }
    $google_font_weights[$name][] = $group['weight'];
    // Always include regular (400) alongside the specified weight so body text renders correctly
    $google_font_weights[$name][] = 400;
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <meta name="viewport" content="width=device-width">
  <!--[if !mso]><!-->
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <!--<![endif]-->
  <meta name="x-apple-disable-message-reformatting">
  <meta content="date=no" name="format-detection">
  <meta content="address=no" name="format-detection">
  <meta content="email=no" name="format-detection">
  <meta content="telephone=no" name="format-detection">
  <meta name="color-scheme" content="light dark">
  <meta name="supported-color-schemes" content="light dark only">
  <title><?php echo esc_html($subject_tag); ?></title>

  <?php if (!empty($google_font_weights)) :
    $query_parts = [];
    foreach ($google_font_weights as $font_name => $weights) {
      $weights = array_unique($weights);
      sort($weights);
      $encoded = str_replace(' ', '+', $font_name);
      $query_parts[] = $encoded . ':wght@' . implode(';', $weights);
    }
    $query = implode('&family=', $query_parts);
  ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=<?php echo esc_attr($query); ?>&display=swap" rel="stylesheet">
  <?php endif; ?>

  <style>
    <?php echo file_get_contents(plugin_dir_path(__FILE__) . '../assets/css/default.css'); ?>
  </style>

  <style>
    @media screen and (max-width: <?php echo esc_attr($container_width); ?>px) {
      .eb_menu,.eb_menu table {
        width:100% !important;
      }

      p.heading {
        font-size: <?php echo esc_attr($heading_size_mobile); ?>px !important;
      }

      p.subheading {
        font-size: <?php echo esc_attr($subheading_size_mobile); ?>px !important;
      }

      p.text {
        font-size: <?php echo esc_attr($body_size_mobile); ?>px !important;
      }

      a.button {
        font-size: <?php echo esc_attr($button_size_mobile); ?>px !important;
      }

      <?php
      $mob_dirs = ['pt' => 'top', 'pb' => 'bottom', 'pl' => 'left', 'pr' => 'right'];
      for ($i = 0; $i <= 50; $i += 5) {
          foreach ($mob_dirs as $abbr => $side) {
              $px = $i === 0 ? '0' : $i . 'px';
              echo "      .eb-mob-{$abbr}-{$i} { padding-{$side}: {$px} !important; }\n";
          }
      }
      echo "      .eb-mob-al-left, .eb-mob-al-left p  { text-align: left !important; }\n";
      echo "      .eb-mob-al-center,.eb-mob-al-center p { text-align: center !important; }\n";
      echo "      .eb-mob-al-right, .eb-mob-al-right p  { text-align: right !important; }\n";
      echo "      .eb-mob-hide      { display: none !important; }\n";
      echo "      .eb-menu-inner    { width: 100% !important; float: none !important; }\n";
      for ($i = 8; $i <= 40; $i++) {
          echo "      .eb-mob-fs-{$i} { font-size: {$i}px !important; }\n";
          echo "      .eb-mob-fs-{$i} a, .eb-mob-fs-{$i} td { font-size: {$i}px !important; }\n";
      }
      foreach (eb_get_mobile_border_radii() as $class => $v) {
          echo "      .{$class} { border-radius:{$v[0]}px {$v[1]}px {$v[2]}px {$v[3]}px !important; }\n";
      }
      for ($i = 0; $i <= 10; $i++) {
          $px = $i === 0 ? '0' : $i . 'px';
          echo "      .eb-mob-bw-{$i} { border-width: {$px} !important; border-style: solid !important; }\n";
      }
      foreach (eb_get_mobile_border_colors() as $class => $color) {
          echo "      .{$class} { border-color: " . esc_attr($color) . " !important; }\n";
      }
      ?>
    }

    @media screen and (max-width: <?php echo esc_attr($container_width); ?>px) {
        .eb_img_adapt {
          width: 100% !important;
          height: auto !important;
        }
        .eb-column-wrap,.eb-column-wrap table {
            width: 100% !important;
            max-width: 100% !important;
        }
        .eb-column-wrap__td {
          width: 100% !important;
        }
        .eb-hidden,.eb-column-spacer,.eb-column-gap {
            display: none !important;
        }

        .pb20 {
          padding-bottom:20px !important;
        }
    }

    @media (prefers-color-scheme: dark) {

      /* Body and container backgrounds */
      body,
      #body,
      .email-container,
      .eb-wrapper-color,
      td.email-bg {
        background-color: <?php echo esc_attr($styles['dark_bg_color']); ?> !important;
      }

      /* All text elements */
      .eb-dark-text-colour-enabled .dark-mode-text,
      h1,
      h2,
      h3,
      h4,
      h5,
      h6,
      .eb-dark-text-colour-enabled td,
      .eb-dark-text-colour-enabled span,
      .eb-dark-text-colour-enabled li {
        color: <?php echo esc_attr($styles['dark_text_color']); ?> !important;
      }

      svg path {
        fill: <?php echo esc_attr($styles['dark_text_color']); ?> !important;
      }

      /* Links */
      a,
      a.eb-link {
        color: <?php echo esc_attr($styles['dark_link_color']); ?> !important;
      }

      /* Buttons */
      a.button {
        background-color: <?php echo esc_attr($styles['dark_button_color']); ?> !important;
        color: <?php echo esc_attr($styles['dark_button_text_color']); ?> !important;
      }

      .eb-logo-light {
        display: none !important;
      }
      .eb-logo-dark {
          display: inline-block !important;
      }
      .eb-img-light {
        display: none !important;
      }
      .eb-img-dark {
        display: block !important;
      }

      /* Section backgrounds — preset dark colour is the default,
         individual section overrides are generated below */
      td.section-td {
        background-color: <?php echo esc_attr($styles['dark_bg_color']); ?> !important;
      }

      /* Order table row backgrounds */
      td.eb-order-row,
      td.eb-order-totals-row {
        background-color: transparent !important;
      }
      td.eb-order-row-alt {
        background-color: rgba(255,255,255,0.05) !important;
      }

      <?php echo $dark_section_css; ?>
    }

    [data-ogsc] body,
    [data-ogsc] #body,
    [data-ogsc] .email-container,
    [data-ogsc] .eb-wrapper-color,
    [data-ogsc] td.email-bg {
        background-color: <?php echo esc_attr($styles['dark_bg_color']); ?> !important;
      }

      /* All text elements */
    [data-ogsc] .dark-mode-text,
    [data-ogsc] h1,
    [data-ogsc] h2,
    [data-ogsc] h3,
    [data-ogsc]h4,
    [data-ogsc] h5,
    [data-ogsc] h6,
    [data-ogsc] td,
    [data-ogsc] span,
    [data-ogsc] li {
        color: <?php echo esc_attr($styles['dark_text_color']); ?> !important;
      }

    [data-ogsc] svg path {
        fill: <?php echo esc_attr($styles['dark_text_color']); ?> !important;
      }

      /* Links */
    [data-ogsc] a,
    [data-ogsc] a.eb-link {
        color: <?php echo esc_attr($styles['dark_link_color']); ?> !important;
      }

      /* Buttons */
    [data-ogsc] a.button {
        background-color: <?php echo esc_attr($styles['dark_button_color']); ?> !important;
        color: <?php echo esc_attr($styles['dark_button_text_color']); ?> !important;
      }

    [data-ogsc] .eb-logo-light {
        display: none !important;
      }
    [data-ogsc] .eb-logo-dark {
          display: inline-block !important;
      }
    [data-ogsc] .eb-img-light {
        display: none !important;
      }
    [data-ogsc] .eb-img-dark {
        display: block !important;
      }

      /* Section backgrounds — preset dark colour is the default,
         individual section overrides are generated below */
    [data-ogsc] td.section-td {
        background-color: <?php echo esc_attr($styles['dark_bg_color']); ?> !important;
      }

      /* Order table row backgrounds */
    [data-ogsc] td.eb-order-row,
    [data-ogsc] td.eb-order-totals-row {
        background-color: transparent !important;
      }
    [data-ogsc] td.eb-order-row-alt {
        background-color: rgba(255,255,255,0.05) !important;
      }

    <?php echo $ogsb_dark_section_css; ?>
  </style>

</head>

<body id="body" width="100%" bgcolor="<?php echo esc_attr($bg_color); ?>" style="mso-line-height-rule: exactly; width:100% !important; margin:0; padding:0; background-color:<?php echo esc_attr($bg_color); ?>;"><?php
$preheader_text = get_post_meta($post->ID, 'eb_preheader', true);
if ($platform === 'emailoctopus') :
    // EmailOctopus requires {{PreviewText}} as a placeholder — they populate it from campaign settings
?><div style="display:none;max-height:0;overflow:hidden;mso-hide:all;font-size:1px;color:#ffffff;line-height:1px;">{{PreviewText}}</div><?php elseif (!empty($preheader_text)) : ?><div style="display:none;max-height:0;overflow:hidden;mso-hide:all;font-size:1px;color:#ffffff;line-height:1px;"><?php echo esc_html($preheader_text); ?></div><?php endif; ?>
  <div dir="ltr" class="eb-wrapper-color" lang="und" style="background-color:<?php echo esc_attr($bg_color); ?>"><!--[if gte mso 9]>
			<v:background xmlns:v="urn:schemas-microsoft-com:vml" fill="t">
				<v:fill type="tile" color="<?php echo esc_attr($bg_color); ?>"></v:fill>
			</v:background>
		<![endif]-->
  <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr>
      <?php
        $body_bg_image_style = '';
        if ( !empty($body_bg_image_enabled) && !empty($body_bg_image_url) ) {
            $bg_pos  = esc_attr($body_bg_image_pos_x) . ' ' . esc_attr($body_bg_image_pos_y);
            $bg_size = esc_attr($body_bg_image_size_w);
            $body_bg_image_style = sprintf(
                ' background-image:url(\'%s\'); background-repeat:%s; background-position:%s; background-size:%s;',
                esc_url($body_bg_image_url),
                esc_attr($body_bg_image_repeat),
                $bg_pos,
                $bg_size
            );
        }
      ?>
      <td valign="top" align="center" style="padding:0; background-color: <?php echo esc_attr($bg_color); ?>;<?php echo $body_bg_image_style; ?>" class="email-bg">
          <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
            <tr>
              <td>