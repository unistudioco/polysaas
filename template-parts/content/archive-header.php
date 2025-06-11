<?php
/**
 * Template part for displaying archive headers
 *
 * @package Polysaas
 */

use Polysaas\Core\Template_Helper;

// Get header settings
$settings = Template_Helper::get_page_header_settings();

// If using a global section, don't render anything here
if ($settings['template_type'] === '_gs' && !empty($settings['template_id'])) {
    return;
}

// Get the appropriate title and description
$title = '';
$description = '';
$subtitle = '';

if (is_home() && !is_front_page()) {
    $title = get_the_title(get_option('page_for_posts'));
} elseif (is_category()) {
    $subtitle = __('Category', 'polysaas');
    $title = single_cat_title('', false);
    $description = category_description();
} elseif (is_tag()) {
    $subtitle = __('Tag', 'polysaas');
    $title = single_tag_title('', false);
    $description = tag_description();
} elseif (is_author()) {
    $subtitle = __('Author', 'polysaas');
    $title = get_the_author();
} elseif (is_date()) {
    if (is_day()) {
        $subtitle = __('Daily Archives', 'polysaas');
        $title = get_the_date();
    } elseif (is_month()) {
        $subtitle = __('Monthly Archives', 'polysaas');
        $title = get_the_date('F Y');
    } elseif (is_year()) {
        $subtitle = __('Yearly Archives', 'polysaas');
        $title = get_the_date('Y');
    } else {
        $title = __('Archives', 'polysaas');
    }
} elseif (is_search()) {
    $title = sprintf(__('Search Results for: %s', 'polysaas'), get_search_query());
} else {
    $title = __('Archives', 'polysaas');
}

// Args for page header
$args = [
    'header_layout' => $settings['layout'],
    'show_breadcrumbs' => $settings['show_breadcrumbs'],
    'title' => $title,
    'subtitle' => $subtitle,
    'alignment' => 'center',
];

get_template_part('template-parts/content/page-header', null, $args);

// Display description if available
if (!empty($description) && $settings['layout'] !== 'disabled') :
?>
<div class="archive-description container my-4 text-center">
    <?php echo wp_kses_post($description); ?>
</div>
<?php endif; ?>