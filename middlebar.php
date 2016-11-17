<?php

/*
Plugin Name: Middlebar
Plugin URI: http://gresak.net
Description: Include a widget sidebar in your content.
Author: Gregor Grešak
Version: 0.1
Author URI: http://gresak.net
*/

new GG_Midlebar();

class GG_Midlebar {

	public $paragraph_offset = -99;

	public $stop_categories = array('pr_novica');

	public function __construct(){
		add_action( 'widgets_init', array($this, "widgets_init"),11 );
		add_filter( 'the_content', array($this, 'inject_sidebar'),10);
        add_action('customize_register', array($this, 'customizer'));
	}

	public function inject_sidebar($content) {

		if(!is_single() || in_category($this->stop_categories)) return $content;

		$middlebar = $this->get_middlebar();

		$content = str_replace('</p>', '</p>{{GG}}', $content);
		$paragraphs = explode('{{GG}}', $content);
		$n = ceil(count($paragraphs)/2) + $this->get_paragraph_offset();
		if($n <= 0) {
			$n = 1;
		}
		//var_dump(count($paragraphs),$n);
		$paragraphs[$n-1] = $paragraphs[$n-1].$middlebar;

		$content = implode("", $paragraphs);

		return $content;
	}

	protected function get_paragraph_offset() {
		return get_theme_mod( "middlebar_position", -99 );
	}

	 public function customizer($wp_customize) {
        $wp_customize->add_section('middlebar', array(
            'title' => 'Middlebar',
            'priority' => 10,
            ));
        
        $wp_customize->add_setting('middlebar_position', array( "default" => -99));
		$wp_customize->add_control(
			new WP_Customize_Control(
				$wp_customize,
				'middlebar_position',
				array(
					'label' => 'Middlebar position',
					'section' => 'middlebar',
					'settings' => 'middlebar_position'
					)
				)
			);
    }

	public function widgets_init()
	{
		register_sidebar( array(
			'name'          => 'Middlebar',
			'id'            => 'middlebar_sidebar',
			'description'	=> 'Middlebar appears in the middle of article body.',
			'before_widget' => '<div>',
			'after_widget'  => '</div>',
			'before_title'  => '<h2 class="rounded">',
			'after_title'   => '</h2>',
		) );
	}

	protected function get_middlebar() {
		if (!is_active_sidebar( 'middlebar_sidebar' )) return;
	 	ob_start();

		dynamic_sidebar( 'middlebar_sidebar' );

		return ob_get_clean();
	}
}