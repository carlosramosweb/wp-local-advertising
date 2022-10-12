<?php
/*---------------------------------------------------------
Plugin Name: WP Local Advertising
Author: carlosramosweb
Author URI: https://criacaocriativa.com
Donate link: https://donate.criacaocriativa.com
Description: Esse plugin é uma versão BETA. Exibie anuncios nas página ou posts do Wordpress usando o Shortcode [wp_advertising_shortcode]
Text Domain: wp-local-advertising
Domain Path: /languages/
Version: 1.0.0
Requires at least: 3.5.0
Tested up to: 5.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html 
------------------------------------------------------------*/

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'WP_Local_Advertising' ) ) {   

    class WP_Local_Advertising {

        public function __construct() {
            add_action( 'plugins_loaded', array( $this, 'init_functions' ) );
        }
        //=>

        public function init_functions() {
            add_action( 'init', array( $this, 'wp_register_posttype' ) );
            add_action( 'save_post', array( $this, 'wp_save_meta_box' ) );
            add_action( 'add_meta_boxes', array( $this, 'wp_register_meta_boxes' ) ); 
            add_shortcode( 'wp_local_advertising', array( $this, 'wp_get_local_advertising_shortcode' ) );  
        }
        //=>

        public function wp_register_posttype() {
            $args = array(
                'public'                => true,
                'label'                 => 'Publicidade Local',
                'public_queryable'      => true,
                'exclude_from_search'   => true,
                'show_ui'               => true,
                'show_in_menu'          => true,
                'show_in_nav_menus'     => true,
                'show_in_admin_bar'     => true,
                'capability_type'       => 'post',
                'query_var'             => true,
                'menu_icon'             => 'dashicons-megaphone',
                'supports'              => array( 'title', 'thumbnail' ), 
                'rewrite'               => array(
                    'slug'          => 'wp-local-advertising',
                    'with_front'    => false
                ),
                // 'title', 'editor', 'comments', 'revisions', 'trackbacks', 'author', 'excerpt', 'page-attributes', 'thumbnail', 'custom-fields', and 'post-formats'
            );
            register_post_type( 'wp-local-advertising', $args );
        }
        //=>

        public function wp_register_meta_boxes() {
            add_meta_box( 
                'meta-box-id', 
                __( 'Configuração', 'wp-local-advertising' ), 
                array( $this, 'wp_advertising_shortcode_display_callback' ),
                'wp-local-advertising',
                'advanced',
                'high'
            );
        }
        //=>

        public function wp_advertising_shortcode_display_callback( $post ) { 
            $link_ads = get_post_meta( get_the_ID(), '_link_ads', true );
            if ( empty( $link_ads ) ) {
                $link_ads = "";
            }
            ?>
            <p class="form-field _link_ads_field ">
                <label for="_link_ads"><strong>Link do Ads:</strong></label><br/>
                <input type="text" class="wp_input_link" name="_link_ads" value="<?php echo $link_ads; ?>" placeholder="https://">
            </p>
            <p class="form-field _link_ads_field ">
                <label for="_link_ads"><strong>Shortcode:</strong></label><br/>
                [wp_local_advertising id='<?php echo get_the_ID(); ?>']
            </p>
            <?php
        }
        //=>

        public function wp_save_meta_box( $post_id ) {
            if ( isset( $_POST ) ) {
                if ( isset( $_POST['post_type'] ) && $_POST['post_type'] == "wp-local-advertising" ) {
                    update_post_meta(
                        $post_id,
                        '_link_ads',
                        $_POST['_link_ads']
                    );
                    $shorcode_ads = "[wp_local_advertising id='" . $post_id . "']";
                    update_post_meta(
                        $post_id,
                        '_shorcode_ads',
                        $shorcode_ads
                    );
                }
            }
        }
        //=>

        public function wp_get_local_advertising_shortcode( $atts ) {
            global $post;
            $post_id = $atts['id'];
            if( ! isset( $post_id ) ) {
                return;
            }
            $ads = get_post( $post_id );

            if ( $ads ) {
                $the_title = $ads->post_title;
                $link_ads = get_post_meta( $post_id, '_link_ads', true );

                $thumbnail_ads = get_the_post_thumbnail_url( $post_id, 'full' ); 
                if ( ! empty( $thumbnail_ads ) ) {
                    $thumbnail = '<div style="display:block; width:100%; margin:0 auto;" class="thumbnail-local-ads">';
                    $thumbnail .= '<a href="' . $link_ads . '" target="_blank">';
                    $thumbnail .= '<img src="' . $thumbnail_ads . '" alt="' . $the_title . '" style="width:100%;">';
                    $thumbnail .= '</a>';
                    $thumbnail .= '</div>';
                }
                echo $thumbnail;
            }
            ?>
            <?php
        }
        //=>

        public function wp_get_modal_advertising_shortcode() { ?>
            <div class="modal fade exampleModal" id="exampleModal" tabindex="-1" role="dialog" aria-hidden="true" style="padding-right: 0;" onClick="close_play()">
              <div class="modal-dialog" role="document" style="max-width: 98%;">
                <div class="modal-content" style="background-color: #000;">
                  <div class="modal-header" style="border-bottom: 1px solid #212529;">
                    <h5 class="modal-title" style="color: aliceblue;">Nome da Música</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body">
                    <iframe class="iframe-youtube" id="iframe-youtube" src="" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                  </div>
                </div>
              </div>
            </div>
            <?php
        }
        //=>

        public function wp_get_script_advertising_shortcode() { ?>
            <style type="text/css"></style>
            <?php
        }
        //=>

    }
    //=>

    new WP_Local_Advertising();
}