<?php
/**
 * Plugin Name:		  Text Case Converter
 * Description:		  Editor Custom button
 * Version: 		  1.3
 * Author: 			  Coder426
 * Author URI:		  https://www.hirewebxperts.com
 * Donate link: 	  https://hirewebxperts.com/donate/
 * Text Domain:       txtcc
 * Domain Path:		  /languages
 * License:           GPLv2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * License: GPL2
*/

/**
 * Define plugin url path
 */
define('TXTCC_CART_PLUGIN_URL',plugin_dir_url( __FILE__ ));
define('TXTCC_CART_PLUGIN_DIR',dirname( __FILE__ ));
define('TXTCC_CART_JS',TXTCC_CART_PLUGIN_URL. 'assets/js/');
define('TXTCC_CART_CSS',TXTCC_CART_PLUGIN_URL. 'assets/css/');
define('TXTCC_CART_IMG',TXTCC_CART_PLUGIN_URL. 'assets/img/');
define('TXTCC_CART_INC',TXTCC_CART_PLUGIN_DIR. '/inc/');

if ( ! defined( 'ABSPATH' ) ) { 
    exit; // exit if accessed directly    
}

/*
* Add languages files
*/
add_action('init','txtcc_language_translate');
function txtcc_language_translate(){
	load_plugin_textdomain( 'txtcc', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}

add_action('wp_enqueue_scripts','load_tooltips'); //loads on wordpress init
function load_tooltips() {
    if (!is_admin()) {
        wp_enqueue_style( 'txtcc-tooltip', TXTCC_CART_CSS . 'zebra_tooltips.css'); 
        wp_enqueue_script( 'txtcc_tooltip_js', TXTCC_CART_JS . 'zebra_tooltips.js', array( 'jquery' ), false,true);
       wp_enqueue_script( 'txtccfront_js', TXTCC_CART_JS . 'txtfront.js', array( 'jquery' ), false,true);

       wp_enqueue_style( 'txtcc-tooltip', TXTCC_CART_CSS . 'fontawesome-iconpicker.css'); 
       wp_enqueue_style( 'txtcc-tooltip', TXTCC_CART_CSS . 'fontawesome-iconpicker1.css'); 

       wp_enqueue_script( 'txtcc_tooltip_js', TXTCC_CART_JS . 'fontawesome-iconpicker1.js', array( 'jquery' ), false,true);
       wp_enqueue_script( 'txtccfront_js', TXTCC_CART_JS . 'txtfontfront.js', array( 'jquery' ), false,true);
    }
} 


/******
 * Include file in admin panel
 */
if (is_admin()) {
	if(!function_exists('txtcc_admin_scripts')){
        function txtcc_admin_scripts() {
            wp_enqueue_style( 'chnge-edit-admin', TXTCC_CART_CSS . 'chnge-edit-admin.css');     
            wp_enqueue_script( 'txtcc_mce_js', TXTCC_CART_JS . 'txtcc-mce.js', array( 'jquery' ), 1.0,true);
            wp_enqueue_script( 'txtcc_mce_js' );
            wp_enqueue_script( 'wp-tinymce' );
            wp_localize_script( 'txtcc_mce_js', 'txtcc_vars', array(
                'ajaxUrl'              => admin_url('admin-ajax.php'),
                'uppercase' 		   => __( 'Uppercase', 'txtcc' ),
                'lowercase' 		   => __( 'lowercase', 'txtcc' ),
                'capitalize' 	       => __( 'Capitalize', 'txtcc' ),
                'sentence' 		       => __( 'Sentence', 'txtcc' ),
                'invert_case' 		   => __( 'Invert Case', 'txtcc' ),
                'alternate_case' 	   => __( 'Alternate Case', 'txtcc' ),
                'insert_dummy_text'    => __( 'Insert dummy text', 'txtcc' ),
                'subscript' 		   => __( 'Subscript', 'txtcc' ),
                'superscript' 		   => __( 'Superscript', 'txtcc' ),
                'download_text' 	   => __( 'Download Text', 'txtcc' ),
                'clean' 		       => __( 'Clean', 'txtcc' ),
                'copy_clipboard' 	   => __( 'Copy To clipboard', 'txtcc' ),
                'break_line' 		   => __( 'Add break in content', 'txtcc' ),
                'calculator' 		   => __( 'Calculator', 'txtcc' ),
                'address' 		       => __( 'Address', 'txtcc' ),
                'short_quotation' 	   => __( 'Short Quotation', 'txtcc' ),
                'abbr' 		           => __( 'Abbreviation', 'txtcc' ),
                'highlight' 		   => __( 'Highlight text', 'txtcc' ),
                'abbr_title' 		   => __( 'Enter the title attribute', 'txtcc' ),
                'abbr_title_lbl' 	   => __( 'Title', 'txtcc' ),
                'drop_down'            => __( 'Synonyms','txtcc'),
                'drop_down_title_lbl'  => __( 'Add synonyms','txtcc'),
                'audio'                => __( 'Audio', 'txtcc'),
                'audio_title'          => __( 'Add audio', 'txtcc'),
                'audio_src'            => __( 'Audio src', 'txtcc'),
                'tooltip'              => __( 'Tool Tip', 'txtcc'),
                'tooltip_title_lbl'    => __( 'Tool Tip Text', 'txtcc'),
                'video'                => __( 'video', 'txtcc'),
                'video_title'          => __( 'Add video', 'txtcc'),
                'video_src'            => __( 'video src', 'txtcc'),
            ) );
        }
        add_action( 'admin_enqueue_scripts', 'txtcc_admin_scripts' );
	}
}
// hooks your functions into the correct filters
function txtcc_add_mce() {
    // check user permissions
    if ( !current_user_can( 'edit_posts' ) &&  !current_user_can( 'edit_pages' ) ) {
            return;
    }
    // check if WYSIWYG is enabled
    if ( 'true' == get_user_option( 'rich_editing' ) ) {
        add_filter( 'mce_external_plugins', 'txtcc_add_tinymce_plugin' );
        add_filter( 'mce_buttons_3', 'txtcc_register_mce_button' );
    }
}
add_action('init', 'txtcc_add_mce');

// register new button in the editor
function txtcc_register_mce_button( $buttons ) {

    $btn_text_arrs = array('withcaps','withoutcaps','firstcaps','frstwcaps','invertcase','address','short_quotation','abbr','highlight','insert_text','sup','sub','alt','download','ctoc','drop_down','audio','video','tooltip','brk_line','clean','calculator');

    foreach($btn_text_arrs as $btn_text_arr){
        array_push( $buttons, $btn_text_arr );
    }
    return $buttons;
}
// declare a script for the new button
// the script will insert the shortcode on the click event
function txtcc_add_tinymce_plugin( $plugin_array ) {
    $plugin_array['Editorchangecase'] = TXTCC_CART_JS .'txtcc-mce.js';
    return $plugin_array;
}
?>