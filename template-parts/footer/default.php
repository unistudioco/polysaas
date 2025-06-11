<?php
/**
 * Default Footer Template
 */

use Polysaas\Core\Config;

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

?>
<div class="uc-section py-4 text-center overflow-hidden bg-light dark:bg-gray-900 dark:text-white">
    <div class="container">
        <p><?php echo sprintf(__('2025 © %s all rights reserved.', Config::get('text_domain')), get_bloginfo('name')); ?></p>
    </div>
</div>