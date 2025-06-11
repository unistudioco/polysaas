<?php
/**
 * The template for displaying the footer
 */
use Polysaas\Core\Hooks;
?>
    <footer id="colophon" class="site-footer">
        <?php     
            // Before Footer Content
            Hooks::do_action('before_footer_content');

            // Display Footer Content
            Hooks::do_action('footer_content_display');

            // After Footer Content
            Hooks::do_action('after_footer_content');
        ?>
    </footer>
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>