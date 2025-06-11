<?php
/**
 * Custom Elementor Widget.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Polysaas_Elementor_Custom_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'custom_widget';
	}

	public function get_title() {
		return __( 'Custom Widget', 'polysaas' );
	}

	public function get_icon() {
		return 'eicon-code';
	}

	public function get_categories() {
		return [ 'general' ];
	}

	protected function register_controls() {
		$this->start_controls_section(
			'content_section',
			[
				'label' => __( 'Content', 'polysaas' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'title',
			[
				'label' => __( 'Title', 'polysaas' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Default title', 'polysaas' ),
				'placeholder' => __( 'Type your title here', 'polysaas' ),
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		?>
		<div class="custom-widget">
			<h2><?php echo esc_html( $settings['title'] ); ?></h2>
		</div>
		<?php
	}

	protected function _content_template() {
		?>
		<div class="custom-widget">
			<h2>{{{ settings.title }}}</h2>
		</div>
		<?php
	}
}

?>
