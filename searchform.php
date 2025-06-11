<?php
/**
 * Custom search form template
 *
 * @package Polysaas
 */
?>

<form role="search" method="get" class="search-form position-relative" action="<?php echo esc_url(home_url('/')); ?>">
    <label class="screen-reader-text"><?php _e('Search for:', 'polysaas'); ?></label>
    <div class="input-group">
        <input type="search" class="search-field form-control rounded-start py-2 ps-3" 
            placeholder="<?php echo esc_attr_x('Search...', 'placeholder', 'polysaas'); ?>" 
            value="<?php echo get_search_query(); ?>" name="s" 
            aria-label="<?php echo esc_attr_x('Search', 'aria-label', 'polysaas'); ?>" />
        <button type="submit" class="search-submit btn btn-primary w-56px">
            <i class="icon icon-2 unicon-search"></i>
            <span class="d-none"><?php echo esc_attr_x('Search', 'submit button', 'polysaas'); ?></span>
        </button>
    </div>
</form>