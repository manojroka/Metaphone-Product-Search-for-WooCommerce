<?php

class GCDoubleMetaPhone {

    public $_message = '';
    public static $_existingprods = array();
    public $_convertedCount = 0;
    public $_metaphoneProdArry;
    public $_failedCount = 0;
    public $_searchRes;

    function __construct() {
        $this->_metaphoneProdArry = array(
            'woo_product_id' => '',
            'name_metaphone' => '',
            'short_des_metaphone' => '',
            'des_metaphone' => '',
            'name_txt' => '',
            'short_des_txt' => '',
            'des_txt' => '',
            'product_image_url' => ''
        );
        //Load the actual metaphone library
        require_once 'double_metaphone_lib.php';
    }

    public function updateSingleProduct($product) {
        $existingprods = self::getConvertedProds();
        $image = wp_get_attachment_image_src(get_post_thumbnail_id($product->ID), 'single-post-thumbnail');
        $metaphoneProdArry = array();
        $metaphoneProdArry['woo_product_id'] = $product->ID;

        //Getting title
        $metaphoneProdArry['name_metaphone'] = utf8_encode(self::getMetaphoneValue($product->post_title));

        //Getting short description metaphone
        $metaphoneProdArry['short_des_metaphone'] = self::getMetaphoneValue($product->post_excerpt);

        //Getting description metaphone
        $metaphoneProdArry['des_metaphone'] = self::getMetaphoneValue($product->post_content);

        //Getting title
        $metaphoneProdArry['name_txt'] = self::cleanB4Insert($product->post_title);

        //Getting short description
        $metaphoneProdArry['short_des_txt'] = self::cleanB4Insert($product->post_excerpt);

        //Getting description 
        $metaphoneProdArry['des_txt'] = self::cleanB4Insert($product->post_content);

        //getting product image url
        $metaphoneProdArry['product_image_url'] = isset($image[0]) ? $image[0] : '';

        if (!in_array($product->ID, $existingprods)) {  //new product added

            //saving the metaphone values into database
            if (self::saveMetaPhoneValues($metaphoneProdArry)) {
                //success
            } else {
                //failed
            }
        }else{  //existing product edited
            self::editExistingProductMeta($metaphoneProdArry);
        }
        
    }
    
    public static function editExistingProductMeta($metaphoneProdArry){
        global $wpdb;
        $sql = "update ".$wpdb->prefix . "gc_doublemetaphone set "
                . "name_metaphone = '".$metaphoneProdArry['name_metaphone']."',"
                . "short_des_metaphone = '".$metaphoneProdArry['short_des_metaphone']."',"
                . "des_metaphone = '".$metaphoneProdArry['des_metaphone']."',"
                . "name_txt = '".$metaphoneProdArry['name_txt']."',"
                . "short_des_txt = '".$metaphoneProdArry['short_des_txt']."',"
                . "product_image_url = '".$metaphoneProdArry['product_image_url']."',"
                . "des_txt = '".$metaphoneProdArry['des_txt']."' where woo_product_id = '".$metaphoneProdArry['woo_product_id']."'";
        return $wpdb->query($sql);
    }
    
    public function deleteSingleProduct($productid){
        global $wpdb;
        $sql = "delete from ".$wpdb->prefix . "gc_doublemetaphone where woo_product_id = '".$productid."'";
        return $wpdb->query($sql);
    }

    public function GetImageUrlsByProductId($attachmentIds) {

        $imgUrls = array();
        if (sizeof($attachmentIds) > 0) {
            foreach ($attachmentIds as $attachmentId) {
                $imgUrls[] = wp_get_attachment_url($attachmentId);
                break; //use only one
            }
        }
        return isset($imgUrls[0]) ? $imgUrls[0] : '';
    }

    public static function updateMetaPhones() {


        //getting pre-populated metaphones 
        $existingprods = self::getConvertedProds();

        $args = array('post_type' => 'product', 'posts_per_page' => -1);

        $loop = new WP_Query($args);
        $convertedCount = $failedCount = 0;
        while ($loop->have_posts()) {
            $loop->the_post();
            global $product;
            $image = wp_get_attachment_image_src(get_post_thumbnail_id($product->post->ID), 'single-post-thumbnail');
            if (!in_array($product->post->ID, $existingprods)) {
                $metaphoneProdArry = array();
                $metaphoneProdArry['woo_product_id'] = $product->post->ID;

                //Getting title
                $metaphoneProdArry['name_metaphone'] = utf8_encode(self::getMetaphoneValue($product->post->post_title));

                //Getting short description metaphone
                $metaphoneProdArry['short_des_metaphone'] = self::getMetaphoneValue($product->post->post_excerpt);

                //Getting description metaphone
                $metaphoneProdArry['des_metaphone'] = self::getMetaphoneValue($product->post->post_content);

                //Getting title
                $metaphoneProdArry['name_txt'] = self::cleanB4Insert($product->post->post_title);

                //Getting short description
                $metaphoneProdArry['short_des_txt'] = self::cleanB4Insert($product->post->post_excerpt);

                //Getting description 
                $metaphoneProdArry['des_txt'] = self::cleanB4Insert($product->post->post_content);

                //getting product image url
                $metaphoneProdArry['product_image_url'] = isset($image[0]) ? $image[0] : '';

                //saving the metaphone values into database
                if (self::saveMetaPhoneValues($metaphoneProdArry)) {
                    $convertedCount++;
                } else {
                    $failedCount++;
                }
            }
        }
        wp_reset_query();

//        $this->_message = '<p>The Double Metaphone values are updated successfully.</p>';
//        $this->_message .= '<p>Total successful conversions: ' . $this->_convertedCount . '</p>';
//        $this->_message .= '<p>Total failed conversions: ' . $this->_failedCount . '</p>';
    }

    private static function cleanB4Insert($str) {
        return htmlentities(strip_tags($str), ENT_QUOTES);
    }

    private static function saveMetaPhoneValues($metaphoneProdArry) {
        global $wpdb;
        $sql = "insert into " . $wpdb->prefix . "gc_doublemetaphone (woo_product_id,name_metaphone,short_des_metaphone,des_metaphone,name_txt,short_des_txt,des_txt,product_image_url) values ("
                . "'" . $metaphoneProdArry['woo_product_id'] . ""
                . "','" . $metaphoneProdArry['name_metaphone'] . ""
                . "','" . $metaphoneProdArry['short_des_metaphone'] . ""
                . "','" . $metaphoneProdArry['des_metaphone'] . ""
                . "','" . $metaphoneProdArry['name_txt'] . ""
                . "','" . $metaphoneProdArry['short_des_txt'] . ""
                . "','" . $metaphoneProdArry['des_txt'] . ""
                . "','" . $metaphoneProdArry['product_image_url'] . "')";

        return $wpdb->query($sql);
    }

    private static function getMetaphoneValue($text) {

        //escaping html tags
        $text = strip_tags($text);

        $ph_text_c = '';

        $items = explode(' ', $text);
        if (sizeof($items) > 0) {
            $i = 1;
            foreach ($items as $item) {
                $phonetic_res = double_metaphone(trim($item));
                //Now taking care of only Primary, will consider it in 2nd release
                $phonetic_val = $phonetic_res['primary'];
                if (strlen($phonetic_val) > 0) {
                    //lets make the phonetic value to be 4 byte long :: to be indexed
                    if (strlen($phonetic_val) == '1') {
                        $phonetic_val = $phonetic_val . '000';
                    } elseif (strlen($phonetic_val) == '2') {
                        $phonetic_val = $phonetic_val . '00';
                    } elseif (strlen($phonetic_val) == '3') {
                        $phonetic_val = $phonetic_val . '0';
                    }
                    if (sizeof($items) == $i) {
                        $ph_text_c .= $phonetic_val;
                    } else {
                        $ph_text_c .= $phonetic_val . ' ';
                    }
                    $i++;
                }
            }
        }
        return $ph_text_c;
    }

    public function showOutput() {
        if ($this->_message != '') {
            echo $this->_message;
        }
    }

    public function getOutputmessage() {
        return $this->_message;
    }

    private static function getConvertedProds() {
        global $wpdb;
        $products = array();
        $sql = "select woo_product_id from " . $wpdb->prefix . "gc_doublemetaphone";
        $data = $wpdb->get_results($sql, OBJECT);
        if (sizeof($data) > 0) {
            foreach ($data as $prod) {
                $products[] = $prod->woo_product_id;
            }
        }
        return $products;
    }

    public function search_products() {
        global $wpdb;
        $items = explode(' ', trim($_REQUEST['key']));

        $ph_text_c = '';
        $ph_whole_word = '"';

        //Mix it with the actual phrase search
        $text_actual_search = ' ';
        $text_whole_search = '"';

        if (sizeof($items) > 0) {
            $i = 1;
            foreach ($items as $item) {
                $phonetic_res = double_metaphone(trim($item));
                //Now taking care of only Primary, will consider it in 2nd release
                $phonetic_val = $phonetic_res['primary'];
                if (strlen($phonetic_val) > 0) {
                    //lets make the phonetic value to be 4 byte long :: to be indexed
                    if (sizeof($items) == $i) {
                        $ph_text_c .= '+' . $phonetic_val . '*';
                        $ph_whole_word .= $phonetic_val . '*';
                        $text_actual_search .= '+' . trim($item) . '*';
                        $text_whole_search .= trim($item) . '*';
                    } else {
                        $ph_text_c .= '+' . $phonetic_val . '* ';
                        $ph_whole_word .= $phonetic_val . '* ';
                        $text_actual_search .= '+' . trim($item) . '* ';
                        $text_whole_search .= trim($item) . '* ';
                    }
                    $i++;
                }
            }
        }
        $ph_whole_word .= '"';
        $text_whole_search .= '"';

        $sql = "select distinct dm.woo_product_id,dm.product_image_url,dm.name_txt, MATCH (dm.name_metaphone,dm.short_des_metaphone,des_metaphone) "
                . "AGAINST ('" . $ph_text_c . " " . $ph_whole_word . "' IN BOOLEAN MODE) AS Relevanz, "
                . "MATCH (dm.name_txt,dm.short_des_txt,des_txt) AGAINST ('" . $text_actual_search . " " . $text_whole_search . "' IN BOOLEAN MODE) AS Relevanz2, "
                . "MATCH (dm.name_txt) AGAINST ('" . $text_actual_search . " " . $text_whole_search . "' IN BOOLEAN MODE) AS RelevanzName, "
                . "MATCH (dm.name_metaphone) AGAINST ('" . $ph_text_c . " " . $ph_whole_word . "' IN BOOLEAN MODE) AS RelevanzPhNome "
                . "FROM " . $wpdb->prefix . "gc_doublemetaphone as dm WHERE "
                . "MATCH (dm.name_metaphone,dm.short_des_metaphone,des_metaphone) "
                . "AGAINST ('" . $ph_text_c . " " . $ph_whole_word . "' IN BOOLEAN MODE) OR "
                . "MATCH (dm.name_txt) "
                . "AGAINST ('" . $text_actual_search . " " . $text_whole_search . "' IN BOOLEAN MODE) OR "
                . "MATCH (dm.name_metaphone) "
                . "AGAINST ('" . $ph_text_c . " " . $ph_whole_word . "' IN BOOLEAN MODE) OR "
                . "MATCH (dm.name_txt,dm.short_des_txt,des_txt) AGAINST ('" . $text_actual_search . " " . $text_whole_search . "' IN BOOLEAN MODE) "
                . "ORDER by (Relevanz + (6*RelevanzName) + (4*RelevanzPhNome) + (3*Relevanz2)) DESC, dm.name_txt ASC LIMIT " . get_option('gc_num_results');

        //print_r($sql); exit;

        $this->_searchRes = $wpdb->get_results($sql, OBJECT);

        $html = $this->prepareSearchHtmlRes();
        echo $html;
        exit;
    }

    public function prepareSearchHtmlRes() {
        $html = '';
        if (sizeof($this->_searchRes) > 0) {
            $html .= '<div id="bit_closeButton">X</div><table id="product_list" class="table table-striped table-bordered" cellspacing="0" width="100%"><thead><tr><th width="150">Image</th><th>Product</th></tr></thead><tbody>';
            foreach ($this->_searchRes as $res) {
                $html .= '<tr>';
                $html .= '<td class="prod_img"><img src="' . $res->product_image_url . '"></td>';
                $html .= '<td><a href="' . get_permalink($res->woo_product_id) . '">' . $res->name_txt . '</a></td>';
                $html .= '</tr>';
            }
            $html .= '</tbody></table>';
        }
        return $html;
    }

}
