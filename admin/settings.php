<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if(isset($_POST['gc_savesettings'])){
    if(check_admin_referer( 'gc_dmetaphone_setting_action', 'gc_dmetaphone_setting_field' )){
        update_option('gc_load_datatable',gc_doublemetaphone_validate_yes_no($_POST['gc_load_datatable']));
        $num_res = intval(sanitize_text_field($_POST['gc_num_results']));
        if($num_res){
            update_option('gc_num_results',$num_res);
        }
        update_option('gc_display_black_shadow',gc_doublemetaphone_validate_yes_no($_POST['gc_display_black_shadow']));
        $num_char = intval(sanitize_text_field($_POST['gc_num_chars']));
        if($num_char){
            update_option('gc_num_chars',$num_char);
        }
        update_option('gc_ajax_search_for_wpbox',gc_doublemetaphone_validate_yes_no($_POST['gc_ajax_search_for_wpbox']));
    }else{
        wp_die('Security check fail');
    }
}

?>

<div class="wrap" id="gc_metaphone_settings">
    <form action="<?php echo wp_nonce_url('admin.php?page=gc-double-metaphones'); ?>" method="post">
        <table class="form-table"><tbody>
                <tr>
                    <th scope="row" class="titledesc">
                        <label for="gc_num_chars">Minimum Number of Characters to Start Ajax Search</label>
                    </th>
                    <td class="forminp forminp-number">
                       <input type="text" name="gc_num_chars" value="<?php echo get_option('gc_num_chars'); ?>" />
                    </td>
                </tr>
                
                <tr>
                    <th scope="row" class="titledesc">
                        <label for="gc_load_datatable">Use Bootstrap Datatable</label>
                    </th>
                    <td class="forminp forminp-number">
                        <select id="gc_load_datatable" name="gc_load_datatable">
                            <option value="yes" <?php if(get_option('gc_load_datatable') == 'yes') echo 'selected'; ?>>Yes</option>
                            <option value="no" <?php if(get_option('gc_load_datatable') == 'no') echo 'selected'; ?>>No</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row" class="titledesc">
                        <label for="gc_load_datatable">Add Feature in default Wordpress Search</label>
                    </th>
                    <td class="forminp forminp-number">
                        <select id="gc_load_datatable" name="gc_ajax_search_for_wpbox">
                            <option value="yes" <?php if(get_option('gc_ajax_search_for_wpbox') == 'yes') echo 'selected'; ?>>Yes</option>
                            <option value="no" <?php if(get_option('gc_ajax_search_for_wpbox') == 'no') echo 'selected'; ?>>No</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row" class="titledesc">
                        <label for="gc_num_results">Number of Results</label>
                    </th>
                    <td class="forminp forminp-number">
                        <input type="text" name="gc_num_results" value="<?php echo get_option('gc_num_results'); ?>" />
                    </td>
                </tr>
                <tr>
                    <th scope="row" class="titledesc">
                        <label for="gc_display_black_shadow">Display Black Shadow on Search Result Display Screen</label>
                    </th>
                    <td class="forminp forminp-number">
                        <select id="gc_display_black_shadow" name="gc_display_black_shadow">
                            <option value="yes" <?php if(get_option('gc_display_black_shadow') == 'yes') echo 'selected'; ?>>Yes</option>
                            <option value="no" <?php if(get_option('gc_display_black_shadow') == 'no') echo 'selected'; ?>>No</option>
                        </select>
                    </td>
                </tr>
            </tbody>
        </table>
        <?php wp_nonce_field( 'gc_dmetaphone_setting_action','gc_dmetaphone_setting_field'); ?>
        <p><input type="submit" name="gc_savesettings" class="button" value="Save Settings"></p>
    </form>
</div>