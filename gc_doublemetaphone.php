<?php

/**
  Plugin Name: WooCommerce Metaphone Product Search 
  Plugin URI:
  Description: This plugin performs ajax search over WooCommerce Products using famous double metaphone algorithm by Lawrence Philips. No more worries about spelling mistakes while users search your store. Please be informed that it may take some time while activating the plugin depending upon the number of products on your store.
  Version:     1.0
  Author:      Manoj Roka
  Author URI:
  License:     GPL2
  License URI: https://www.gnu.org/licenses/gpl-2.0.html
  Text Domain: wporg
  Domain Path: /search
 */
defined('ABSPATH') or die('No script kiddies please!');

//Only for testing
//error_reporting(E_ALL);
//ini_set('display_errors', 1);
ini_set('max_execution_time', 300);
set_time_limit(300);

/**
 * Adding Menu to the wordpress backend
 */
/** Step 2 (from text above). */
add_action('admin_menu', 'gc_doublemetaphone_menu');

/** Step 1. */
function gc_doublemetaphone_menu() {
    add_menu_page('Metaphone Search', 'Metaphone Search', 'manage_options', 'gc-double-metaphones', 'gc_doublemetaphone_create', '', '12');
}

/** Step 3. */
function gc_doublemetaphone_create() {
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }
    require_once 'admin/settings.php';
}

/**
 * End of Adding Menu to the wordpress backend
 */
/**
 * Creating database table to hold metaphone values
 */
add_option("gc_doublemetaphone_db_version", "1.0");

function gc_doublemetaphone_install() {
    global $wpdb;

    $table_name = $wpdb->prefix . "gc_doublemetaphone";
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE " . $table_name . " (
  id bigint(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  woo_product_id varchar(255),
  name_metaphone text,
  short_des_metaphone text,
  des_metaphone text,name_txt text,short_des_txt text,des_txt text,product_image_url varchar(255)
) " . $charset_collate;

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta($sql);

    //adding options
    add_option("gc_doublemetaphone_db_version", "1.0");
    add_option("gc_load_jquery", "no");
    add_option("gc_load_datatable", "yes");
    add_option("gc_num_results", 30);
    add_option("gc_display_black_shadow", "yes");
    add_option("gc_num_chars", 3);
    add_option("gc_ajax_search_for_wpbox", 'no');
}

function gc_update_fulltext() {
    global $wpdb;

    $table_name = $wpdb->prefix . "gc_doublemetaphone";

    $fquery = "ALTER TABLE " . $table_name . " ADD FULLTEXT (`name_metaphone`)";
    $wpdb->query($fquery);

    $wpdb->query("ALTER TABLE " . $table_name . " ADD FULLTEXT (short_des_metaphone)");

    $wpdb->query("ALTER TABLE " . $table_name . " ADD FULLTEXT (des_metaphone)");

    $wpdb->query("ALTER TABLE " . $table_name . " ADD FULLTEXT (name_txt)");

    $wpdb->query("ALTER TABLE " . $table_name . " ADD FULLTEXT (`name_metaphone`,short_des_metaphone)");

    $wpdb->query("ALTER TABLE " . $table_name . " ADD FULLTEXT (`name_metaphone`,des_metaphone)");

    $wpdb->query("ALTER TABLE " . $table_name . " ADD FULLTEXT (short_des_metaphone,des_metaphone)");

    $wpdb->query("ALTER TABLE " . $table_name . " ADD FULLTEXT (`name_metaphone`,short_des_metaphone,des_metaphone)");

    $wpdb->query("ALTER TABLE " . $table_name . " ADD FULLTEXT (name_txt,short_des_txt,des_txt)");
}

//Automatic update of the product data
require_once 'lib/GCDoubleMetaPhone.php';
require_once 'lib/double_metaphone_lib.php';
register_activation_hook(__FILE__, 'gc_doublemetaphone_install');
register_activation_hook(__FILE__, 'gc_update_fulltext');
register_activation_hook(__FILE__, array('GCDoubleMetaPhone', 'updateMetaPhones'));

/**
 * End of Creating database table to hold metaphone values
 */
/**
 * Delete database table on plugin deactivation
 */
register_deactivation_hook(__FILE__, 'gc_doublemetaphone_uninstall');

function gc_doublemetaphone_uninstall() {
    delete_option("gc_doublemetaphone_db_version");
    delete_option("gc_load_jquery");
    delete_option("gc_load_datatable");
    delete_option("gc_num_results");
    delete_option("gc_display_black_shadow");
    delete_option("gc_num_chars");
    delete_option("gc_ajax_search_for_wpbox");
    global $wpdb;

    $table_name = $wpdb->prefix . "gc_doublemetaphone";

    $sql = "drop table if exists " . $table_name;
//execute the query deleting the table
    $wpdb->query($sql);
}

/**
 * End of Delete database table on plugin deactivation
 */
/**
 * Core Class Loading
 */
add_action('init', 'process_gc_doublemetaphone');
global $process;

function process_gc_doublemetaphone() {
    global $process;
    require_once 'lib/GCDoubleMetaPhone.php';
    $process = new GCDoubleMetaPhone();
    if (isset($_POST['gc_updatemetavalues'])) {
        $process::updateMetaPhones();
    }
    //$process->getOutputmessage();
}

add_action('admin_notices', 'gc_doublemetaphone_admin_notice__success');

function gc_doublemetaphone_admin_notice__success() {
    global $process;
    if (strlen($process->getOutputmessage()) > 0) {
        echo '<div class="notice notice-success is-dismissible"><p>' . $process->getOutputmessage() . '</p></div>';
    }
}

/**
 * Appending our unique id to woocommerce product search field
 */
add_filter('get_product_search_form', 'gc_doublemetaphone_product_searchform');
if(get_option('gc_ajax_search_for_wpbox') == 'yes'){
    add_filter( 'get_search_form', 'gc_doublemetaphone_product_searchform', 100 );
}
function gc_doublemetaphone_product_searchform($form) {
    $form = '<form role="search" method="get" id="searchform" class="woocommerce-product-search gc_dmetaphone_ajax" action="' . esc_url(home_url('/')) . '">
		<div id="gc_woo_searchbox">
			<label class="screen-reader-text" for="gc_doublem_input">' . __('Search for:', 'woocommerce') . '</label>
			<input type="search" value="' . get_search_query() . '" name="s" id="gc_doublem_input" placeholder="' . __('Search Products..', 'woocommerce') . '" />
			<input type="submit" id="searchsubmit" value="' . esc_attr__('Search', 'woocommerce') . '" />
			<input type="hidden" name="post_type" value="product" />
                        <input type="hidden" id="gc_siteurl" value="' . admin_url('admin-ajax.php') . '">
                            <input type="hidden" id="gc_num_chars" value="' . get_option("gc_num_chars") . '">
		</div>
                <div class="suggestionsBox container" id="suggestions" style="display: none;"><div class="loading"></div><div class="suggestionList" id="autoSuggestionsList">&nbsp;</div></div><div id="search_shadow"></div>
	</form>';
    if (get_option('gc_display_black_shadow') == 'yes') {
        $form .= '<style>.gc_dmetaphone_ajax #search_shadow.search_result {display: block;}</style>';
    }
    return $form;
}

/**
 * End of Appending our unique id to woocommerce product search field
 */
/**
 * Including required js file
 */
add_action('init', 'gc_doblemetaphone_register_script');

function gc_doblemetaphone_register_script() {
    wp_register_script('gc_dmetaphone_js', plugin_dir_url(__FILE__) . 'js/gc_dmetaphone.js', array('jquery'), '1.0', true);
    wp_enqueue_script('gc_dmetaphone_js');

    wp_register_style('gc_dmetaphone_css', plugins_url('/css/style.css', __FILE__), false, '1.0', 'all');
    wp_enqueue_style('gc_dmetaphone_css');

    if (get_option('gc_load_jquery') == 'yes') {
        wp_register_script('gc_use_jquery', plugin_dir_url(__FILE__) . 'js/jquery.dataTables.min.js', array('jquery'), '1.0', true);
        wp_enqueue_script('gc_use_jquery');
    }

    if (get_option('gc_load_datatable') == 'yes') {
        wp_register_script('gc_datatable_js', plugin_dir_url(__FILE__) . 'js/jquery.dataTables.min.js', array('jquery'), '1.0', true);
        wp_enqueue_script('gc_datatable_js');
        wp_register_style('gc_datatable_css', plugins_url('/css/dataTables.bootstrap.min.css', __FILE__), false, '1.0', 'all');
        wp_enqueue_style('gc_datatable_css');
    }
}

function load_gc_admin_style() {
    wp_register_style('gc_admin_css', plugins_url('/css/admin-style.css', __FILE__), false, '1.0');
    wp_enqueue_style('gc_admin_css');
}

add_action('admin_enqueue_scripts', 'load_gc_admin_style');
/**
 * End Including required js file
 */
/**
 * Handling Ajax Search
 */
add_action('wp_ajax_nopriv_gc_dm_search_products', 'gc_dm_search_products');
add_action('wp_ajax_gc_dm_search_products', 'gc_dm_search_products');

function gc_dm_search_products() {
    global $process;
    if (defined('DOING_AJAX') && DOING_AJAX) {
        if (isset($_REQUEST['key']) && $_REQUEST['key'] != get_option('gc_num_chars')) {
            $process->search_products();
        }
    }
    exit();
}

/**
 * End of Handling Ajax Search
 */
/**
 * Need to update our phonetic values if the product is updated
 */
add_action('transition_post_status', 'gc_update_product_metaphone', 10, 3);

function gc_update_product_metaphone($new_status, $old_status, $post) {
    require_once 'lib/GCDoubleMetaPhone.php';
    $process = new GCDoubleMetaPhone();
    if ($new_status == 'publish' && !empty($post->ID) && in_array($post->post_type, array('product'))) {
        $process->updateSingleProduct($post);
    } elseif ($new_status != 'publish' && !empty($post->ID) && in_array($post->post_type, array('product'))) {
        $process->deleteSingleProduct($post->ID);
    }
}


