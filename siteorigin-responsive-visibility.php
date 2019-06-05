<?php

/**
 *
 * @link              http://grell.es
 * @since             1.0.0
 * @package           SiteoriginResponsiveVisibility
 *
 * @wordpress-plugin
 * Plugin Name:       SiteOrigin Resposive Visibility
 * Plugin URI:        http://plugins.grell.es
 * GitHub Plugin URI: boquiabierto/siteorigin-responsive-visibility
 * Description:       Add options to SiteOrigin page builder rows and widgets (under attributes) to hide them depending on SiteOrigin breakpoint settings.
 * Version:           1.1
 * Author:            Adrián Ortiz Arandes
 * Author URI:        http://grell.es
 * License:           GPL-3.0+
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:       so-responsive-visibility
 * Domain Path:       /languages
 */


class SO_Responsive_Visibility {
	
	private $text_domain;
	
	function __construct() {
		
		$this->text_domain = 'so-responsive-visibility';
		
		add_action( 'init', array( $this, 'set_breakpoints') );
		add_action( 'init', array( $this, 'load_textdomain' ) );
		add_filter( 'body_class', array( $this, 'body_class' ) );
		add_action( 'wp_head', array( $this, 'wp_head' ) );
		
		add_filter( 'siteorigin_panels_row_style_fields', array( $this, 'row_style_fields' ) );
  	add_filter( 'siteorigin_panels_widget_style_fields', array( $this, 'row_style_fields' ) );
		
		add_filter( 'siteorigin_panels_row_style_attributes', array( $this, 'row_style_attributes' ), 10, 2);
		add_filter( 'siteorigin_panels_widget_style_attributes', array( $this, 'row_style_attributes' ), 10, 2);
		
	}
	
	public function set_breakpoints() {
		
		if ( empty( $this->so_panel_settings ) ) {
			$this->so_panel_settings = get_option('siteorigin_panels_settings');
		}
		
		$this->breakpoints = array(
			'mobile-width' => ( array_key_exists('mobile-width', $this->so_panel_settings ) ? $this->so_panel_settings['mobile-width'] : '768' ),
			'tablet-width' => ( array_key_exists('tablet-width', $this->so_panel_settings ) ? $this->so_panel_settings['tablet-width'] : '992' ),
			'desktop-width' => '1200'
		);
		
	}
	
	private function get_breakpoint( $key ) {
		error_log( $this->breakpoints[$key] );
		return $this->breakpoints[$key];
	}
	
	private function get_breakpoint_mobile() {
		return $this->get_breakpoint( 'mobile-width' );
	}
	
	private function get_breakpoint_tablet() {
		return $this->get_breakpoint( 'tablet-width' );
	}
	
	private function get_breakpoint_desktop() {
		return $this->get_breakpoint( 'desktop-width' );
	}
	
	private function get_field_name( $s ) {
		return '<span class="dashicons dashicons-hidden"></span> ' . $s;
	}
	
	public function load_textdomain() {
		load_plugin_textdomain(
			$this->text_domain,
			false,
			dirname( plugin_basename(__FILE__) ) . '/languages/'
		);
	}
	
	public function row_style_fields( $fields ) {

		$fields['hidden-xs'] = array(
			'name'        => $this->get_field_name( sprintf( __( '0 to %spx <small>screen width</small>', $this->text_domain ), $this->get_breakpoint_mobile() ) ),
			'type'        => 'checkbox',
			'group'       => 'attributes',
			'priority'    => 11,
		);
		
		$fields['hidden-sm'] = array(
			'name'        => $this->get_field_name( sprintf( __( 'larger than %spx <small>screen width</small>', $this->text_domain ), $this->get_breakpoint_mobile() ) ),
			'type'        => 'checkbox',
			'group'       => 'attributes',
			'priority'    => 12,
		);
		
		$fields['hidden-md'] = array(
			'name'        => $this->get_field_name( sprintf( __( 'larger than %spx <small>screen width</small>', $this->text_domain ), $this->get_breakpoint_tablet() ) ),
			'type'        => 'checkbox',
			'group'       => 'attributes',
			'priority'    => 13,
		);
		
		$fields['hidden-lg'] = array(
			'name'        => $this->get_field_name( sprintf( __( 'larger than %spx <small>screen width</small>', $this->text_domain ), $this->get_breakpoint_desktop() ) ),
			'type'        => 'checkbox',
			'group'       => 'attributes',
			'priority'    => 14,
		);
		
		return $fields;
	}

	public function row_style_attributes( $attributes, $args ) {
		
		$classes = array( 'hidden-xs', 'hidden-sm', 'hidden-md', 'hidden-lg' );
		
		foreach( $classes as $class ) {
			
			if( !empty( $args[$class] ) ) {
			
				array_push( $attributes['class'], $class );
			
			}
				
		}

		return $attributes;
	
	}
	
	public function body_class( $classes ) {
		
		$classes[] = $this->text_domain;
		
		return $classes;
	}
	
	public function wp_head() {
		
		?>
		<style id="<?php echo $this->text_domain; ?>" type="text/css">
		/* Styles by SiteOrigin Resposive Visibility */
		@media (max-width:<?php echo ( int )$this->get_breakpoint_mobile() -1; ?>px){
			.<?php echo $this->text_domain; ?> .hidden-xs {
				display: none !important;
			}
		}
		@media (min-width:<?php echo $this->get_breakpoint_mobile(); ?>px) and (max-width:<?php echo ( int )$this->get_breakpoint_tablet()-1; ?>px){
			.<?php echo $this->text_domain; ?> .hidden-sm {
				display: none !important;
			}
		}
		@media (min-width:<?php echo $this->get_breakpoint_tablet(); ?>px) and (max-width:<?php echo ( int )$this->get_breakpoint_desktop()-1; ?>px){
			.<?php echo $this->text_domain; ?> .hidden-md {
				display: none !important;
			}
		}
		@media (min-width:<?php echo $this->get_breakpoint_desktop(); ?>px){
			.<?php echo $this->text_domain; ?> .hidden-lg {
				display: none !important;
			}
		}
		</style>
		<?php
		
	}


	
	
	
}

new SO_Responsive_Visibility();
