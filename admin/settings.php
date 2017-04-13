<?php
if(isset($_POST['gc_savesettings'])){
    update_option('gc_load_jquery',$_POST['gc_load_jquery']);
    update_option('gc_load_datatable',$_POST['gc_load_datatable']);
    update_option('gc_num_results',$_POST['gc_num_results']);
    update_option('gc_display_black_shadow',$_POST['gc_display_black_shadow']);
    update_option('gc_num_chars',$_POST['gc_num_chars']);
    update_option('gc_ajax_search_for_wpbox',$_POST['gc_ajax_search_for_wpbox']);
}
?>

<div class="wrap" id="gc_metaphone_settings">
    <form action="" method="post">
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
                        <label for="gc_ajax_search_for_wpbox">Use Ajax Search for Wordpress Search</label>
                    </th>
                    <td class="forminp forminp-number">
                        <select id="gc_ajax_search_for_wpbox" name="gc_ajax_search_for_wpbox">
                            <option value="yes" <?php if(get_option('gc_ajax_search_for_wpbox') == 'yes') echo 'selected'; ?>>Yes</option>
                            <option value="no" <?php if(get_option('gc_ajax_search_for_wpbox') == 'no') echo 'selected'; ?>>No</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row" class="titledesc">
                        <label for="gc_load_jquery">Use jQuery</label>
                    </th>
                    <td class="forminp forminp-number">
                        <select id="gc_load_jquery" name="gc_load_jquery">
                            <option value="yes" <?php if(get_option('gc_load_jquery') == 'yes') echo 'selected'; ?>>Yes</option>
                            <option value="no" <?php if(get_option('gc_load_jquery') == 'no') echo 'selected'; ?>>No</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row" class="titledesc">
                        <label for="gc_load_jquery">Use Bootstrap Datatable</label>
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
        <p><input type="submit" name="gc_savesettings" class="button" value="Save Settings"></p>
    </form>
</div>