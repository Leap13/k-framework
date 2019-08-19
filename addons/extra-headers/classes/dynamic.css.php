<?php
/**
 * Footer Widgets - Dynamic CSS
 *
 * @package Astra Addon
 */

add_filter( 'kemet_addons_dynamic_css', 'kemet_ext_headers_dynamic_css' );

/**
 * Dynamic CSS
 *
 * @param  string $dynamic_css          Astra Dynamic CSS.
 * @param  string $dynamic_css_filtered Astra Dynamic CSS Filters.
 * @return string
 */
function kemet_ext_headers_dynamic_css() {

            $header_5_icon_color         = kemet_get_option( 'header-5-icon-color' );
            $header_5_icon_h_color       = kemet_get_option( 'header-5-icon-h-color' );
            $header_5_icon_bg_color      = kemet_get_option( 'header-5-icon-bg-color' );
            $header_5_icon_bg_h_color    = kemet_get_option( 'header-5-icon-bg-h-color' );
            $header_5_icon_border_radius =  kemet_get_option( 'header-5-icon-border-radius' );

            $header6_width               = kemet_get_option( 'header6-width' );
            $header6_border_width        = kemet_get_option( 'header6-border-width' );
            $header6_border_style        = kemet_get_option( 'header6-border-style' );
            $header6_border_color        = kemet_get_option( 'header6-border-color' );

            $css_output = array();
            
            $css_output = array(     
                '.icon-bars-btn span' => array(
					'background-color' => esc_attr($header_5_icon_color),
                ),
                '.kemet-addons-header5 .animated-icon:hover span,.kemet-addons-header7 .animated-icon:hover span' => array(
					'background-color' => esc_attr($header_5_icon_h_color),
                ),
                '.kemet-addons-header5 .animated-icon,.kemet-addons-header7 .animated-icon' => array(
                    'background-color' => esc_attr($header_5_icon_bg_color),
                    'border-radius'    => kemet_get_css_value( $header_5_icon_border_radius, 'px' ),
                ),
                '.kemet-addons-header5 .animated-icon:hover,.kemet-addons-header7 .animated-icon:hover' => array(
					'background-color' => esc_attr($header_5_icon_bg_h_color),
                ),
                '#sitehead.header-main-layout-6,#sitehead.header-main-layout-7' => array(
                    'width' => kemet_get_css_value( $header6_width, 'px' ),
                    'border-color' => esc_attr( $header6_border_color ),
                ),
                '.kemet-addons-header6-right #sitehead.header-main-layout-6,.kemet-addons-header7-right #sitehead.header-main-layout-7' => array(
                    'border-left-style' => esc_attr( $header6_border_style ),
                    'border-left-width' => kemet_get_css_value( $header6_border_width , 'px' ),
                ),
                '.kemet-addons-header6-left #sitehead.header-main-layout-6,.kemet-addons-header7-left #sitehead.header-main-layout-7' => array(
                    'border-right-style' => esc_attr( $header6_border_style ),
                    'border-right-width' => kemet_get_css_value( $header6_border_width , 'px' ),
                ),
                'body.kemet-addons-header6-right' => array(
                    'padding-right' => kemet_get_css_value( $header6_width , 'px'),
                ),
                'body.kemet-addons-header6-left' => array(
                    'padding-left' => kemet_get_css_value( $header6_width , 'px'),
                ),
            );
//            $css_output = array();   
            
//            $css_output = array(     
//                '.kemet-addons-header5 .animated-icon span,.kemet-addons-header7 .animated-icon span, body' => array(
//					'background-color' => esc_attr($header_5_icon_color),
//                 ),
//              );
			/* Parse CSS from array() */

            $parse_css = kemet_parse_css( $css_output );
            return $parse_css;

};