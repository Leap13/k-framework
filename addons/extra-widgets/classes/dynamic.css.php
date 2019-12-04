<?php
/**
 * Go Top - Dynamic CSS
 * 
 * @package Kemet Addons
 */

add_filter( 'kemet_dynamic_css', 'kemet_ext_go_top_dynamic_css');

/**
 * Dynamic CSS
 *
 * @param  string $dynamic_css
 * @return string
 */
function kemet_ext_go_top_dynamic_css( $dynamic_css ) {
			// Go Top Link
			$go_top_icon_color             = kemet_get_option('extra-widgets-icon-color');
			$go_top_icon_h_color           = kemet_get_option('extra-widgets-icon-h-color');
			$go_top_icon_size              = kemet_get_option('extra-widgets-icon-size');
			$go_top_bg_color               = kemet_get_option('extra-widgets-bg-color');
			$go_top_bg_h_color             = kemet_get_option('extra-widgets-bg-h-color');
			$go_top_border_radius          = kemet_get_option('extra-widgets-border-radius');
			$go_top_button_size            = kemet_get_option('extra-widgets-button-size');
            
            $css_content = array(
                '.kmt-extra-widgets-link' => array(
					'background-color' => esc_attr( $go_top_bg_color ),
					'border-radius'    => kemet_get_css_value( $go_top_border_radius, 'px' ),
					'width'            => kemet_get_css_value( $go_top_button_size,'px' ),
					'height'           => kemet_get_css_value( $go_top_button_size,'px' ),
					'line-height'      => kemet_get_css_value( $go_top_button_size,'px'),
					'color'            => esc_attr($go_top_icon_color),
				),
				'.kmt-extra-widgets-link:before' => array(
					'font-size'      => kemet_responsive_font( $go_top_icon_size, 'desktop' ),
				),
				'.kmt-extra-widgets-link:hover' => array(
					'color'            => esc_attr($go_top_icon_h_color),
					'background-color' => esc_attr($go_top_bg_h_color)
				),
 
            );

           $parse_css = kemet_parse_css( $css_content );
            
            $css_tablet = array(
                '.kmt-extra-widgets-link:before' => array(
                    'font-size'    => kemet_responsive_font( $go_top_icon_size, 'tablet' ),
                ),

             );
            $parse_css .= kemet_parse_css( $css_tablet, '', '768' );
            
            $css_mobile = array(
                '.kmt-extra-widgets-link:before' => array(
                    'font-size'    => kemet_responsive_font( $go_top_icon_size, 'mobile' ),
                ),

             );
            $parse_css .= kemet_parse_css( $css_mobile, '', '544' );
            
            return $dynamic_css . $parse_css;
}