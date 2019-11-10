<?php
/**
 * Page Title addon - Dynamic CSS
 * 
 * @package Kemet Addons
 */

add_filter( 'kemet_dynamic_css', 'kemet_ext_page_title_dynamic_css');

/**
 * Dynamic CSS
 *
 * @param  string $dynamic_css
 * @return string
 */
function kemet_ext_page_title_dynamic_css( $dynamic_css ) {
           // $page_title_layout        = kemet_get_option( 'page-title-layouts' );
           // $page_title_alignmrent        = kemet_get_option( 'page-title-alignmrent' );
            $page_title_bg        = kemet_get_option( 'page-title-bg-obj' );
            $page_title_space        = kemet_get_option( 'page-title-space' );
            $page_title_color        = kemet_get_option( 'page-title-color' );
            $page_title_font_size        = kemet_get_option( 'page-title-font-size' );
            $page_title_font_family        = kemet_get_option( 'page-title-font-family' );
            $page_title_font_weight        = kemet_get_option( 'page-title-font-weight' );
            $page_title_font_transform        = kemet_get_option( 'pagetitle-text-transform' );
            $page_title_line_height        = kemet_get_option( 'pagetitle-line-height' );
            $Page_title_bottomline_width         = kemet_get_option( 'page-title-bottomline-width' );
            $Page_title_bottomline_width         = kemet_get_option( 'page-title-bottom-line-color' );
            
            $css_output = array(
               '.kmt-page-title-addon-content, .kemet-merged-header-title' => kemet_get_background_obj( $page_title_bg ),
               '.kmt-page-title-addon-content' => array(
                    'padding-top'    => kemet_responsive_spacing( $page_title_space, 'top', 'desktop' ),
                    'padding-right'  => kemet_responsive_spacing( $page_title_space, 'right', 'desktop' ),
                    'padding-bottom' => kemet_responsive_spacing( $page_title_space, 'bottom', 'desktop' ),
                    'padding-left'   => kemet_responsive_spacing( $page_title_space, 'left', 'desktop' ), 
               ),
               '.kemet-page-title'  => array(
                   'color'  => esc_attr( $page_title_color ),
                   'font-family'    => kemet_get_css_value( $page_title_font_family, 'font' ),
                    'font-weight'    => kemet_get_css_value( $page_title_font_weight, 'font' ),
                    'font-size'      => kemet_responsive_font( $page_title_font_size, 'desktop' ),
                    'text-transform' => esc_attr( $page_title_font_transform ),
                    'line-height'     => kemet_get_css_value( $page_title_line_height, 'px' ),
               ),
 
            );

           $parse_css = kemet_parse_css( $css_output );
            
            $tablet_styles = array(
                '.kmt-page-title-addon-content' => array(
                    'margin-top'    => kemet_responsive_spacing( $page_title_space, 'top', 'tablet' ),
                    'margin-right'  => kemet_responsive_spacing( $page_title_space, 'right', 'tablet' ),
                    'margin-bottom' => kemet_responsive_spacing( $page_title_space, 'bottom', 'tablet' ),
                    'margin-left'   => kemet_responsive_spacing( $page_title_space, 'left', 'tablet' ),              
                ),
             );
            $parse_css .= kemet_parse_css( $tablet_styles, '', '768' );
            
            $mobile_styles = array(
                '.kmt-page-title-addon-content' => array(
                    'margin-top'    => kemet_responsive_spacing( $page_title_space, 'top', 'mobile' ),
                    'margin-right'  => kemet_responsive_spacing( $page_title_space, 'right', 'mobile' ),
                    'margin-bottom' => kemet_responsive_spacing( $page_title_space, 'bottom', 'mobile' ),
                    'margin-left'   => kemet_responsive_spacing( $page_title_space, 'left', 'mobile' ),              
                ),
             );
            $parse_css .= kemet_parse_css( $mobile_styles, '', '544' );
            
            return $dynamic_css . $parse_css;
}