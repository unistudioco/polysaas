<?php
/**
 * Template part for displaying page headers
 *
 * @package Polysaas
 */

use Polysaas\Core\Template_Helper;
use Polysaas\Core\Hooks;

// Get header layout and options
$header_layout = isset($args['header_layout']) ? $args['header_layout'] : 'default';
$show_breadcrumbs = isset($args['show_breadcrumbs']) ? $args['show_breadcrumbs'] : true;
$title = isset($args['title']) ? $args['title'] : get_the_title();
$subtitle = isset($args['subtitle']) ? $args['subtitle'] : '';
$alignment = isset($args['alignment']) ? $args['alignment'] : 'left';

// Classes for different alignments
$alignment_class = 'text-' . $alignment;

if ($header_layout !== 'disabled') :
?>
<div class="page-header<?php echo (is_singular() ? ' page-header-single' : ''); ?><?php echo $header_layout === 'boxed' ? ' header-boxed' : ''; ?>">
    <div class="<?php echo $header_layout === 'boxed' ? 'container' : 'container'; ?>">
        <div class="page-header-inner <?php echo $header_layout === 'boxed' ? 'hstack justify-between' : 'vstack justify-center items-center'; ?> gap-4">
            <?php if ($show_breadcrumbs) : 
                ob_start();
                Template_Helper::breadcrumbs();
                $breadcrumbs = ob_get_clean();
                
                if (!empty(trim($breadcrumbs))) : ?>
                    <div class="breadcrumbs-wrap order-2">
                        <?php echo $breadcrumbs; ?>
                    </div>
                <?php endif;
            endif; ?>

            <?php if (!is_singular()) : ?>
            <header class="page-header-content order-1<?php echo esc_attr(' ' . $alignment_class); ?>">
                <?php if (!empty($subtitle)) : ?>
                    <div class="page-subtitle fs-7 text-primary mb-1"><?php echo wp_kses_post($subtitle); ?></div>
                <?php endif; ?>
                
                <h1 class="page-title"><?php echo wp_kses_post($title); ?></h1>
                
                <?php if (is_singular('post') && Template_Helper::should_show('show_post_meta')) : ?>
                    <div class="entry-meta mt-3 d-flex flex-wrap gap-3 justify-content-<?php echo $alignment; ?>">
                        <?php Template_Helper::render_post_meta(); ?>
                    </div>
                <?php endif; ?>
            </header>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php
endif;