<?php
/**
 * Default Header Template
 */

use Polysaas\Core\Config;
use Polysaas\Helpers\Navbar_Walker;

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

?>
<div class="default-header uc-navbar-main" style="--uc-nav-height: 80px">
    <div class="container">
        <div class="uc-navbar min-h-64px lg:min-h-80px" data-uc-navbar="animation: uc-animation-slide-top-small; duration: 250; delay-hide: 250;">
            <div class="uc-navbar-left">
                <div class="site-branding">
                    <?php if (get_custom_logo()) {
                        the_custom_logo();
                    } else {
                        if(is_front_page()) {
                            echo '<h1 class="uc-logo m-0 text-primary">' . get_bloginfo('title') . '</h1>';
                        } else {
                            echo '<h2 class="uc-logo m-0"><a class="text-none" href="' . get_bloginfo('wpurl') . '">' . get_bloginfo('title') . '</a></h2>';
                        }
                    } ?>
                </div>
            </div>
            <div class="uc-navbar-right">
                <?php
                if( has_nav_menu(Config::prefix('primary'))) {
                    wp_nav_menu(array(
                        'theme_location' => Config::prefix('primary'),
                        'container'      => false,
                        'menu_class' => 'uc-navbar-nav gap-3 xl:gap-4 d-none lg:d-flex',
                        'fallback_cb' => '__return_false',
                        'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>',
                        'depth' => 4,
                        'walker' => new Navbar_Walker()
                    ));
                } else {
                    echo sprintf(__('<p>Please <a href="%s" class="uc-link text-none border-bottom border-black border-opacity-15">create a menu and define it</a> as "Primary Menu" to appear here.</p>', Config::get('text_domain')), get_admin_url(path:'nav-menus.php'));
                }
                ?>
                <a class="d-block lg:d-none" href="#uc-menu-panel" data-uc-navbar-toggle-icon data-uc-toggle></a>
            </div>
        </div>
    </div>
</div>