<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    /**
    * Custom Helper Functions
    * Author: Manish Pareek
    * version: 1.0
    */
    
    /**
     * [To print array]
     * @param array $arr
    */
    if ( ! function_exists('pr')) {
      function pr($arr)
      {
        echo '<pre>'; 
        print_r($arr);
        echo '</pre>';
        die;
      }
    }
    
    /**
     * [To print last query]
    */
    if ( ! function_exists('lq')) {
      function lq()
      {
        $CI = & get_instance();
        echo $CI->db->last_query();
        die;
      }
    }
    
    /**
     * [To get database error message]
    */
    if ( ! function_exists('db_err_msg')) {
      function db_err_msg()
      {
        $CI = & get_instance();
        $error = $CI->db->error();
        if(isset($error['message']) && !empty($error['message'])){
            return 'Database error - '.$error['message'];
        }else{
            return FALSE;
        }
      }
    }
    
    /**
     * [To parse html]
     * @param string $str
    */
    if (!function_exists('parseHTML')) {
        function parseHTML($str) {
            $str = str_replace('src="//', 'src="https://', $str);
            return $str;
        }
    }
    
    /**
     * [To create directory]
     * @param string $folder_path
    */
    if (!function_exists('make_directory')) {
      function make_directory($folder_path) {
        if (!file_exists($folder_path)) {
            mkdir($folder_path, 0777, true);
        }
      }
    }
    
    
    /**
     * [To get current datetime]
    */
    if ( ! function_exists('datetime')) {
      function datetime($default_format='Y-m-d H:i:s')
      {
        $datetime = date($default_format);
        return $datetime;
      }
    }
    
    
    
    /**
     * [To encode string]
     * @param string $str
    */
    if ( ! function_exists('encoding')) {
      function encoding($str){
          $one = serialize($str);
          $two = @gzcompress($one,9);
          $three = addslashes($two);
          $four = base64_encode($three);
          $five = strtr($four, '+/=', '-_.');
          return $five;
      }
    }
    
    /**
     * [To decode string]
     * @param string $str
    */
    if ( ! function_exists('decoding')) {
      function decoding($str){
        $one = strtr($str, '-_.', '+/=');
          $two = base64_decode($one);
          $three = stripslashes($two);
          $four = @gzuncompress($three);
          if ($four == '') {
              return "z1"; 
          } else {
              $five = unserialize($four);
              return $five;
          }
      }
    }
    
    
    /**
     * [To check number is digit or not]
     * @param int $element
    */
    if ( ! function_exists('is_digits')) {
      function is_digits($element){ // for check numeric no without decimal
          return !preg_match ("/[^0-9]/", $element);
      }
    }
    
    /**
     * [To get all months list]
    */
    if ( ! function_exists('getMonths')) {
      function getMonths(){
        $monthArr = array('January','February','March','April','May','June','July','August','September','October','November','December');
        return $monthArr ;
      }
    }
    
    if ( ! function_exists('getHeight')) {
      function getHeight(){
        $heightArr = array("121.92 (4 feet 0 inches)", "127.00 (4 feet 1 inches)", "129.54 (4 feet 2 inches)", "132.08 (4 feet 3 inches)", "134.62 (4 feet 4 inches)", "137.16 (4 feet 5 inches)", "139.70 (4 feet 6 inches)", "142.24 (4 feet 7 inches)", "144.78 (4 feet 8 inches)", "147.32 (4 feet 9 inches)", "149.86 (4 feet 10 inches)", "149.86 (4 feet 11 inches)", "152.40 (5 feet 0 inches)", "154.94 (5 feet 1 inches)", "157.48 (5 feet 2 inches)", "160.02 (5 feet 3 inches)", "162.56 (5 feet 4 inches)", "165.10 (5 feet 5 inches)", "167.64 (5 feet 6 inches)", "170.18 (5 feet 7 inches)", "172.72 (5 feet 8 inches)", "175.26 (5 feet 9 inches)", "177.80 (5 feet 10 inches)", "180.34 (5 feet 11 inches)", "182.88 (6 feet 0 inches)", "185.42 (6 feet 1 inches)", "185.42 (6 feet 2 inches)", "190.50 (6 feet 3 inches)", "193.04 (6 feet 4 inches)", "195.58 (6 feet 5 inches)", "198.12 (6 feet 6 inches)", "200.66 (6 feet 7 inches)", "203.20 (6 feet 8 inches)", "208.28 (6 feet 9 inches)", "214.36 (6 feet 10 inches)", "220.44 (6 feet 11 inches)", "221.52 (7 feet 0 inches)", "232.60 (7 feet 1 inches)", "237.68 (7 feet 2 inches)", "244.76 (7 feet 3 inches)", "250.84 (7 feet 4 inches)", "256.92 (7 feet 5 inches)", "263.00 (7 feet 6 inches)", "268.08 (7 feet 7 inches)", "273.16 (7 feet 8 inches)", "278.24 (7 feet 9 inches)", "283.32 (7 feet 10 inches)", "288.40 (7 feet 11 inches)", "293.48 (8 feet 0 inches)", "298.56 (8 feet 1 inches)", "303.64 (8 feet 2 inches)", "308.72 (8 feet 3 inches)", "313.80 (8 feet 4 inches)", "318.88 (8 feet 5 inches)", "323.96 (8 feet 6 inches)", "329.04 (8 feet 7 inches)", "334.12 (8 feet 8 inches)", "339.20 (8 feet 9 inches)", "344.28 (8 feet 10 inches)", "349.28 (8 feet 11 inches)", "354.36 (9 feet 0 inches)", "359.44 (9 feet 1 inches)", "364.52 (9 feet 2 inches)", "369.60 (9 feet 3 inches)", "374.68 (9 feet 4 inches)", "379.76 (9 feet 5 inches)", "384.84 (9 feet 6 inches)", "389.92 (9 feet 7 inches)", "395.00 (9 feet 8 inches)", "400.08 (9 feet 9 inches)", "405.16 (9 feet 10 inches)", "410.24 (9 feet 11 inches)", "415.32 (10 feet 0 inches)");
        return $heightArr ;
      }
    }
    
    /**
     * [To upload all files]
     * @param string $subfolder
     * @param string $ext
     * @param int $size
     * @param int $width
     * @param int $height
     * @param string $filename
    */
    if ( ! function_exists('fileUploading')) {
      function fileUploading($subfolder,$ext,$size="",$width="",$height="",$filename)
      {
          $CI = & get_instance();
          $config['upload_path']   = 'uploads/'.$subfolder.'/'; 
          $config['allowed_types'] = $ext; 
          if($size){
            $config['max_size']   = 100; 
          }
          if($width){
            $config['max_width']  = 1024; 
          }
          if($height){
            $config['max_height'] = 768;  
          }
          $CI->load->library('upload', $config);
          if (!$CI->upload->do_upload($filename)) {
            $error = array('error' => strip_tags($CI->upload->display_errors())); 
            return $error;
          }
         else { 
            $data = array('upload_data' => $CI->upload->data()); 
            return $data;
         } 
      }
    }
    
    
    
    if (!function_exists('fileUpload')) {
    
        function fileUpload($filename, $subfolder, $ext, $size = "", $width = "", $height = "") {
            $CI = & get_instance();
            $config['upload_path'] = 'uploads/' . $subfolder . '/';
            $config['allowed_types'] = $ext;
            if ($size) {
                $config['max_size'] = 10000;
            }
            if ($width) {
                $config['max_width'] = 102400;
            }
            if ($height) {
                $config['max_height'] = 76800;
            }
            $CI->load->library('upload', $config);
            $CI->upload->initialize($config);
            if (!$CI->upload->do_upload($filename)) {
                $error = array('error' => $CI->upload->display_errors());
                return $error;
            } else {
                $data = array('upload_data' => $CI->upload->data());
                return $data;
            }
        }
    
    }
    /**
     * [To check null value]
     * @param string $value
    */
    if ( ! function_exists('null_checker')) {
      function null_checker($value,$custom="")
      {
        $return = "";
        if($value != "" && $value != NULL){
          $return = ($value == "" || $value == NULL) ? $custom : $value;
          return $return;
        }else{
          return $return;
        }
      }
    }
    
    /**
    * [To get user image thumb]
    * @param  [string] $filepath
    * @param  [string] $subfolder
    * @param  [int] $width
    * @param  [int] $height
    * @param  [int] $min_width
    * @param  [int] $min_height
    */
    if (!function_exists('get_image_thumb'))
    {
      function get_image_thumb($filepath,$subfolder,$width,$height,$min_width="",$min_height="")
      {
    
        if(empty($min_width))
        {
          $min_width = $width;
        }
        if(empty($min_height))
        {
          $min_height = $height;
        }
        /* To get image sizes */
        $image_sizes = getimagesize($filepath);
        if(!empty($image_sizes))
        {
          $img_width  = $image_sizes[0];
          $img_height = $image_sizes[1];
          if($img_width <= $min_width && $img_height <= $min_height)
          {
            return $filepath;
          }
        }
    
        $ci   = &get_instance();
        /* Get file info using file path */
        $file_info = pathinfo($filepath);
        if(!empty($file_info)){
          $filename = $file_info['basename'];
          $ext      = $file_info['extension'];
          $dirname  = $file_info['dirname'].'/';
          $path     = $dirname.$filename;
          $file_status = @file_exists($path);
          if($file_status){
              $config['image_library']  = 'gd2';
              $config['source_image']   = $path;
              $config['create_thumb']   = TRUE;
              $config['maintain_ratio'] = TRUE;
              $config['width']          = $width;
              $config['height']         = $height;
              $ci->load->library('image_lib', $config);
              $ci->image_lib->initialize($config);
              if(!$ci->image_lib->resize()) {
                  return $path;
              } else {
                @chmod($path, 0777);
                $thumbnail = preg_replace('/(\.\w+)$/im', '', urlencode($filename)) . '_thumb.' . $ext;
                  return 'uploads/'.$subfolder.'/'.$thumbnail;
              }
          }else{
            return $filepath;
          }
        }else{
          return $filepath;
        }
      }
    }
    
    
    
    /**
    * [To delete file from directory]
    * @param  [string] $filename
    * @param  [string] $filepath
    */
    if (!function_exists('delete_file'))
    {
      function delete_file($filename,$filepath)
      {
        /* Send file path last slash */
        $file_path_name = $filepath.$filename;
        if(!empty($filename) && @file_exists($file_path_name) && @unlink($file_path_name)){
          return TRUE;
        }else{
          return FALSE;
        }
      }
    }
    
    /**
 * [for loading css on specific pages ]
 */
if (!function_exists('load_css')) {

    function load_css($css){
        
        if(empty($css))
            return;

        $css_base_path = $href = '';
        if(is_array($css)){

            foreach($css as $script_src){

                $href = $script_src;
                if(strpos($script_src, 'http://') === false && strpos($script_src, 'https://') === false){

                    $css_base_path = AWS_CDN_FRONT_ASSETS;
                    $href = $css_base_path.$script_src;
                }

                echo "<link href=\"$href\" rel=\"stylesheet\">\n";
            }
        }
        else{
            $href = $css;
            if(strpos($css, 'http://') === false && strpos($css, 'https://') === false){
                $css_base_path = AWS_CDN_FRONT_ASSETS;
                $href = $css_base_path.$css;
            }
            echo "<link href=\"$href\" rel=\"stylesheet\">";
        }
    }
}

    
    /**
 * [for loading css in ADMIN on specific pages ]
 */
if (!function_exists('load_amdin_css')) {

    function load_admin_css($css=''){
        
        if(empty($css))
            return;

        $css_base_path = $href = '';
        if(is_array($css)){
            foreach($css as $script_src){

                $href = $script_src;
                if(strpos($script_src, 'http://') === false && strpos($script_src, 'https://') === false){
                    $css_base_path = AWS_CDN_BACK_ASSETS;
                    $href = $css_base_path.$script_src;
                }
                echo "<link href=\"$href\" rel=\"stylesheet\">\n";
            }
        }
        else{
            $href = $css;
            if(strpos($css, 'http://') === false && strpos($css, 'https://') === false){
                $css_base_path = AWS_CDN_BACK_ASSETS;
                $href = $css_base_path.$css;
            }
            echo "<link href=\"$href\" rel=\"stylesheet\">";
        }
    }
}

/**
 * [for loading js on specific pages ]
 */
if (!function_exists('load_js')) {

    function load_js($js=''){
        
         if(empty($js))
            return;

        $js_base_path = $src = '';
        if(is_array($js)){

            foreach($js as $script_src){

                $src = $script_src;
                if(strpos($script_src, 'http://') === false && strpos($script_src, 'https://') === false){

                    $js_base_path = AWS_CDN_FRONT_ASSETS;
                    $src = $js_base_path.$script_src;
                }

                echo "<script src=\"$src\"></script>\n";
            }
        }
        else{

            $src = $js;
            if(strpos($js, 'http://') === false && strpos($js, 'https://') === false){
                $js_base_path = AWS_CDN_FRONT_ASSETS;
                $src = $js_base_path.$js;
            }
            echo "<script src=\"$src\"></script>";
        }
    }
}

/**
 * [for loading js in ADMIN on specific pages ]
 */
if (!function_exists('load_admin_js')) {

    function load_admin_js($js=''){
        
        if(empty($js))
            return;

        $js_base_path = $src = '';
        if(is_array($js)){
            foreach($js as $script_src){
                $src = $script_src;
                if(strpos($script_src, 'http://') === false && strpos($script_src, 'https://') === false){
                    $js_base_path = AWS_CDN_BACK_ASSETS;
                    $src = $js_base_path.$script_src;
                }
                echo "<script src=\"$src\"></script>\n";
            }
        }
        else{
            $src = $js;
            if(strpos($js, 'http://') === false && strpos($js, 'https://') === false){
                $js_base_path = AWS_CDN_BACK_ASSETS;
                $src = $js_base_path.$js;
            }
            echo "<script src=\"$src\"></script>";
        }
    }
}
    
    
    /**
     * [for making alias of title or statement ]
     */
    if (!function_exists('make_alias')) {
    
        function make_alias($string, $term_table){
            $string = strtolower(str_replace(' ', '_', $string));
            $alias = preg_replace('/[^A-Za-z0-9]/', '', $string);
            
            //check if alias is unique
            $CI = get_instance();  
            $where = array('alias'=>$alias);
            $res = $CI->common_model->getsingle($term_table, $where);
            if(!$res){
                return $alias;   //alias is unique
            }
            else{
                return $alias.'_'.rand(10, 99); //alias is not unique- append random two digits to make it unique
            }
        }
    }
    
    /**
     * [for making alias of title or statement ]
     */
    if (!function_exists('alpha_spaces')) {
    
        function alpha_spaces($string){
            if (preg_match('/^[a-zA-Z ]*$/', $string)) {
                return TRUE;
            }
            else{
                return FALSE; //match failed(string contains characters other than aplhabets and spaces)
            }
        }
    }
    
    /**
     * [to display placeholder text when actual text is empty ]
     */
    if (!function_exists('display_placeholder_text')) {
    
        function display_placeholder_text($string=''){
            if (empty($string)) {
                return 'NA'; //if string is empty return pleacholder text
            }
            else{
                return $string;  //if not return string as it is;
            }
        }
    }
    
    /**
     * [to display elapsed time as user friendly string from timestamp ]
     */
    if (!function_exists('time_elapsed_string')) {
        function time_elapsed_string($datetime, $full = false) {
            $now = new DateTime;
            $ago = new DateTime($datetime);
            $diff = $now->diff($ago);
    
            $diff->w = floor($diff->d / 7);
            $diff->d -= $diff->w * 7;
    
            $string = array(
                'y' => lang('year'),
                'm' => lang('month'),
                'w' => lang('week'),
                'd' => lang('day'),
                'h' => lang('hr'),
                'i' => lang('min'),
                's' => lang('sec'),
            );
            foreach ($string as $k => &$v) {
                if ($diff->$k) {
                    $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
                } else {
                    unset($string[$k]);
                }
            }
    
            if (!$full) $string = array_slice($string, 0, 1);
            return $string ? implode(', ', $string) . lang('ago') : lang('just_now');
         }//End Function
    }
    
    /**
     * [make img url from name or check if string already has url ]
     */
    if (!function_exists('make_img_url')) {
        function make_user_img_url($img_str) {
            if (!empty($img_str)) { 
                //check if image consists url- happens in social login case
                if (filter_var($img_str, FILTER_VALIDATE_URL)) { 
                  $img_src = $img_str;
                }
                else{
                   $img_src = base_url().USER_AVATAR_PATH.$img_str;
                }
            }
            else{
                $img_src = base_url().USER_DEFAULT_AVATAR; //return default image if image is empty
            }
            
            return $img_src;
        }
    }
    
    /**
     * [make log of any event/action in destination file]
     */
    if (!function_exists('log_event')) {
        function log_event($msg, $file_path='') {
            if(empty($file_path)){
                $file_path = FCPATH.'common_log.txt';
            }
            $perfix = '['.datetime().'] ';  //add current date time
            $msg = $perfix.$msg."\r\n"; //create new line
            error_log($msg, 3, $file_path);
        }
    }
    
    if(!function_exists('status_color')){

        function get_order_status_color($status){

            $status_arr = array( 'Free' => '#ff5722', 'Paid' => '#4caf50', '2' => '#ff5722' );

            if(array_key_exists($status, $status_arr)){

                return $status_arr[$status]; 

            } else{

                return 'NA'; 
            } 
        }
    }
    
    //Cross Site Scripting prevention filter
    function sanitize_input_text($str){

        $CI = & get_instance();  // get instance, access the CI superobject
        return $CI->security->xss_clean($str);  //security library must be autoloaded
    }
    
    //Certain characters have special significance in HTML into their corresponding HTML entities
    function sanitize_output_text($str){

        return htmlspecialchars($str);
    }
    
    //Get CSRF (Cross-site request forgery) token key-value array
    function get_csrf_token(){

        $CI = & get_instance();  // get instance, access the CI superobject
        $csrf = array(
            'name' => $CI->security->get_csrf_token_name(),  //csrf token key
            'hash' => $CI->security->get_csrf_hash()        //csrf token value
        );
        return $csrf;
    }

    if(!function_exists('getAddress')){         //using curl get address with all detail

        function getAddress($lat,$lng){
          $addr = array();
            if(!empty($lat) && !empty($lng)){

                $url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng=' . $lat . "," . $lng .'&key='.GOOGLE_API_KEY.'';
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                $response = curl_exec($ch);
                curl_close($ch);
                $output = json_decode($response);

                if (isset($output)) {

                    $city='';$state='';$country='';$address='';
                    $arr_count =  sizeof($output->results);

                    for ($i = 0; $i < $arr_count; $i++) {
                        //count lenght of array
                        if(empty($address)){
                            $address = $output->results[$i]->address_components;                     
                        }
                        $c_count = sizeof($output->results[$i]->address_components);
                        //loop for check  locality for city
                        for($j=0;$j<$c_count;$j++){

                            //get type value in array
                            $data = '';
                            $data = $output->results[$i]->address_components[$j]->types;

                            //check for locality exist set long name as city
                            if(in_array('locality', $data)){
                                //if locality exist get long name in city variable
                                $city = $output->results[$i]->address_components[$j]->long_name;        
                            }
                            if($city==''){
                                if(in_array('administrative_area_level_2',$data)){
                                    //if exist get long name in city variable
                                    $city = $output->results[$i]->address_components[$j]->long_name;
                                } 
                            }
                            if(in_array('administrative_area_level_1', $data)){
                                //if exist get long name in state variable
                                $state = $output->results[$i]->address_components[$j]->long_name;
                            }

                            if(in_array('country', $data)){
                                //if locality exist get long name in city variable
                                $country = $output->results[$i]->address_components[$j]->long_name;        
                            } 
                        }

                        $addr['current_address']  = $city.','.$state.','.$country;
                        $addr['formatted_address'] = $output->results[0]->formatted_address;

                        $addr['city']       = $city;
                        $addr['state']      = $state;
                        $addr['country']    = $country;
                    }
                    return $addr;               
                }else{
                    return false;   
                }
            }else{
                return false;   
            }  
        }
    }

    function limit_text($text, $limit=0){ 
      if(empty($limit)) $limit = 15;
     
      if(strlen($text) > $limit){
         $text = substr($text, 0, $limit).' ...';
      }
      return $text;
    }
    
?>