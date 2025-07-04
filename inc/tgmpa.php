<?php
/**
 * Include the TGM_Plugin_Activation class.
 */
require_once get_template_directory() . '/inc/class-tgm-plugin-activation.php';

add_action( 'tgmpa_register', 'polysaas_register_required_plugins' );

/**
 * Register the required plugins for this theme.
 *
 * This function is hooked into tgmpa_register, which is fired on the WP `init` action on priority 10.
 */
function polysaas_register_required_plugins() {
	$plugins = array(
		array(
			'name'      => esc_html__( 'Elementor Page Builder', 'polysaas' ),
			'slug'      => 'elementor',
			'required'  => true,
		),
        [
            'name'         => esc_html__( 'UniCore', 'polysaas' ),
            'slug'         => 'unicore',
            'source'       => get_stylesheet_directory_uri() . '/data-import/plugins/unicore.zip',
            'required'     => true,
        ],
        [
            'name'         => esc_html__( 'Advanced Custom Fields', 'polysaas' ),
            'slug'         => 'advanced-custom-fields',
            'required'     => true,
        ],
        [
            'name'     =>  esc_html__('Kirki Customizer Framework','polysaas'),
            'slug'     => 'kirki',
            'required' => true,
        ],
        [
            'name'     => esc_html__( 'WP Classic Editor', 'polysaas' ),
            'slug'     => 'classic-editor',
            'required' => false,
        ],
		array(
			'name'      => esc_html__( 'Contact Form 7', 'polysaas' ),
			'slug'      => 'contact-form-7',
			'required'  => false,
		),
        [
            'name'     => esc_html__( 'One Click Demo Import', 'polysaas' ),
            'slug'     => 'one-click-demo-import',
            'required' => false,
        ],
        [
            'name'     => esc_html__( 'Mailchimp For WP', 'polysaas' ),
            'slug'     => 'mailchimp-for-wp',
            'required' => false,
        ],
		array(
			'name'      => 'WooCommerce',
			'slug'      => 'woocommerce',
			'required'  => false,
		),
	);

	$config = array(
		'id'           => 'polysaas',                 	   // Unique ID for hashing notices for multiple instances of TGMPA.
		'default_path' => '',                              // Default absolute path to bundled plugins.
		'menu'         => 'tgmpa-install-plugins',         // Menu slug.
		'has_notices'  => true,                            // Show admin notices or not.
		'dismissable'  => true,                            // If false, a user cannot dismiss the nag message.
		'dismiss_msg'  => '',                              // If 'dismissable' is false, this message will be output at top of nag.
		'is_automatic' => false,                           // Automatically activate plugins after installation or not.
		'message'      => '',                              // Message to output right before the plugins table.

        'strings'      => [
            'page_title'                      => esc_html__( 'Install Required Plugins', 'polysaas' ),
            'menu_title'                      => esc_html__( 'Install Plugins', 'polysaas' ),
            'installing'                      => esc_html__( 'Installing Plugin: %s', 'polysaas' ),
            'updating'                        => esc_html__( 'Updating Plugin: %s', 'polysaas' ),
            'oops'                            => esc_html__( 'Something went wrong with the plugin API.', 'polysaas' ),
            'notice_can_install_required'     => _n_noop(
                'This theme requires the following plugin: %1$s.',
                'This theme requires the following plugins: %1$s.',
                'polysaas'
            ),
            'notice_can_install_recommended'  => _n_noop(
                'This theme recommends the following plugin: %1$s.',
                'This theme recommends the following plugins: %1$s.',
                'polysaas'
            ),
            'notice_ask_to_update'            => _n_noop(
                'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.',
                'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.',
                'polysaas'
            ),
            'notice_ask_to_update_maybe'      => _n_noop(
                'There is an update available for: %1$s.',
                'There are updates available for the following plugins: %1$s.',
                'polysaas'
            ),
            'notice_can_activate_required'    => _n_noop(
                'The following required plugin is currently inactive: %1$s.',
                'The following required plugins are currently inactive: %1$s.',
                'polysaas'
            ),
            'notice_can_activate_recommended' => _n_noop(
                'The following recommended plugin is currently inactive: %1$s.',
                'The following recommended plugins are currently inactive: %1$s.',
                'polysaas'
            ),
            'install_link'                    => _n_noop(
                'Begin installing plugin',
                'Begin installing plugins',
                'polysaas'
            ),
            'update_link'                     => _n_noop(
                'Begin updating plugin',
                'Begin updating plugins',
                'polysaas'
            ),
            'activate_link'                   => _n_noop(
                'Begin activating plugin',
                'Begin activating plugins',
                'polysaas'
            ),
            'return'                          => esc_html__( 'Return to Required Plugins Installer', 'polysaas' ),
            'plugin_activated'                => esc_html__( 'Plugin activated successfully.', 'polysaas' ),
            'activated_successfully'          => esc_html__( 'The following plugin was activated successfully:', 'polysaas' ),
            'plugin_already_active'           => esc_html__( 'No action taken. Plugin %1$s was already active.', 'polysaas' ),
            'plugin_needs_higher_version'     => esc_html__( 'Plugin not activated. A higher version of %s is needed for this theme. Please update the plugin.', 'polysaas' ),
            'complete'                        => esc_html__( 'All plugins installed and activated successfully. %1$s', 'polysaas' ),
            'dismiss'                         => esc_html__( 'Dismiss this notice', 'polysaas' ),
            'notice_cannot_install_activate'  => esc_html__( 'There are one or more required or recommended plugins to install, update or activate.', 'polysaas' ),
            'contact_admin'                   => esc_html__( 'Please contact the administrator of this site for help.', 'polysaas' ),
            'nag_type'                        => '',
        ],
	);

	tgmpa( $plugins, $config );
}
?>