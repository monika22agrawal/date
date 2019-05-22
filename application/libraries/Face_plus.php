<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Face_plus {
    
   function compareFace($file1,$file2){             // for comparing two images using 1 img for filedata and another is fileurl

        $url = 'https://api-us.faceplusplus.com/facepp/v3/compare';
    
        $filename1 = $file1['name'];
        $filedata1 = $file1['tmp_name'];
        $filesize1 = $file1['size'];
        $filetype1 = $file1['type'];        
       
        $api_key    = FACE_API_KEY;
        $api_secret = FACE_API_SECRET_KEY;

        if ($filedata1 != '' && $file2 != '') {

            $headers = array("Content-Type:multipart/form-data"); // cURL headers for file uploading
            $postfields['api_key']      = $api_key;
            $postfields['api_secret']   = $api_secret;
            $postfields['image_file1']  = new \CurlFile($filedata1, $filetype1, $filename1);
            $postfields['image_url2']   = $file2;
            
            $ch = curl_init();

            $options = array(

                CURLOPT_URL             => $url,
                CURLOPT_HEADER          => FALSE,
                CURLOPT_POST            => 1,
                CURLOPT_HTTPHEADER      => $headers,
                CURLOPT_POSTFIELDS      => $postfields,
                CURLOPT_RETURNTRANSFER  => TRUE

            ); // cURL options

            curl_setopt_array($ch, $options);

            $response = curl_exec($ch);

            if(!curl_errno($ch)) {

                $info = curl_getinfo($ch);
                $output = json_decode($response);

               return $output;

            } else {

                $errmsg = curl_error($ch);
                return $errmsg;
            }

            curl_close($ch);

        } else {

            $errmsg = "face_select_file_req";
            return $errmsg;
        }
    }

    function compareFaceUrl($file1,$file2){             // for comparing two images using both urls

        $url = 'https://api-us.faceplusplus.com/facepp/v3/compare';
       
        $api_key    = FACE_API_KEY;
        $api_secret = FACE_API_SECRET_KEY;

        if ($file1 != '' && $file2 != '') {

            $headers = array("Content-Type:multipart/form-data"); // cURL headers for file uploading
            $postfields['api_key']      = $api_key;
            $postfields['api_secret']   = $api_secret;
            $postfields['image_url1']   = $file1;
            $postfields['image_url2']   = $file2;
            
            $ch = curl_init();

            $options = array(

                CURLOPT_URL             => $url,
                CURLOPT_HEADER          => FALSE,
                CURLOPT_POST            => 1,
                CURLOPT_HTTPHEADER      => $headers,
                CURLOPT_POSTFIELDS      => $postfields,
                CURLOPT_RETURNTRANSFER  => TRUE

            ); // cURL options

            curl_setopt_array($ch, $options);

            $response = curl_exec($ch);

            if(!curl_errno($ch)) {

                $info = curl_getinfo($ch);
                $output = json_decode($response);

               return $output;

            } else {

                $errmsg = curl_error($ch);
                return $errmsg;
            }

            curl_close($ch);

        } else {

            $errmsg = "face_select_file_req";
            return $errmsg;
        }
    }

    function detectFaceImageUrl($fileUrl){             // for detecting images using image url

        $url = 'https://api-us.faceplusplus.com/facepp/v3/detect';
    
        $api_key    = FACE_API_KEY;
        $api_secret = FACE_API_SECRET_KEY;

        $postfields['api_key']              = $api_key;
        $postfields['api_secret']           = $api_secret;
        $postfields['image_url']            = $fileUrl;
        $postfields['return_landmark']      = 1;
        $postfields['return_attributes']    = 'gender,age';
        
        $ch = curl_init();

        $options = array(

            CURLOPT_URL             => $url,
            CURLOPT_POST            => 1,
            CURLOPT_POSTFIELDS      => $postfields,
            CURLOPT_RETURNTRANSFER  => true

        ); // cURL options

        curl_setopt_array($ch, $options);

        if(!curl_errno($ch)) {

                $response = curl_exec($ch);

                $info = curl_getinfo($ch);
                 
                $output = json_decode($response);

                if(isset($output->error_message)){

                    $var = explode(':', $output->error_message);

                    switch ($var[0]) {

                        case "IMAGE_ERROR_UNSUPPORTED_FORMAT" :

                            $output = "face_file_format";
                            break;

                        case "INVALID_IMAGE_SIZE" :

                            $output = "face_file_size_err";
                            break;

                        case "INVALID_IMAGE_URL" :

                            //$output = 'Failed downloading image from URL. The image URL is wrong or invalid.';
                            $output = 'something_wrong';
                            break;

                        case "IMAGE_FILE_TOO_LARGE" :

                            $output = "face_file_size_err";
                            break;

                        case "IMAGE_DOWNLOAD_TIMEOUT" :

                            //$output = 'Image download timeout, please try after some time.';
                            $output = 'something_wrong';
                            break;

                        case "CONCURRENCY_LIMIT_EXCEEDED" :

                            //$output = 'The rate limit for this API Key has been exceeded, please try after some time.';
                            $output = 'something_wrong';
                            break;

                        default:
                            $output = 'something_wrong';
                    }
                }

                return $output;

            } else {

                $errmsg = curl_error($ch);
                return $errmsg;
            }

            curl_close($ch);     

        return $response;
    }

    function detectFaceImageFile($imgFile){             // for detecting images using image file data

        $url = 'https://api-us.faceplusplus.com/facepp/v3/detect';
    
        $api_key    = FACE_API_KEY;
        $api_secret = FACE_API_SECRET_KEY;

        $filename = $imgFile['name'];
        $filedata = $imgFile['tmp_name'];
        $filesize = $imgFile['size'];
        $filetype = $imgFile['type'];  

        if ($filedata != '') {

            $postfields['api_key']              = $api_key;
            $postfields['api_secret']           = $api_secret;
            $postfields['image_file']           = new \CurlFile($filedata, $filetype, $filename);
            $postfields['return_landmark']      = 1;
            $postfields['return_attributes']    = 'gender,age';

            $headers = array("Content-Type:multipart/form-data"); // cURL headers for file uploading
            
            $ch = curl_init();

            $options = array(

                CURLOPT_URL             => $url,
                CURLOPT_HEADER          => FALSE,
                CURLOPT_POST            => 1,
                CURLOPT_HTTPHEADER      => $headers,
                CURLOPT_POSTFIELDS      => $postfields,
                CURLOPT_RETURNTRANSFER  => TRUE

            ); // cURL options

            curl_setopt_array($ch, $options);      

            if(!curl_errno($ch)) {

                $response = curl_exec($ch);

                $info = curl_getinfo($ch);
                 
                $output = json_decode($response);

                if(isset($output->error_message)){

                    $var = explode(':', $output->error_message);

                    switch ($var[0]) {

                        case "IMAGE_ERROR_UNSUPPORTED_FORMAT" :

                            $output = "face_file_format";
                            break;

                        case "INVALID_IMAGE_SIZE" :

                            $output = "face_file_size_err";
                            break;

                        case "INVALID_IMAGE_URL" :

                            //$output = 'Failed downloading image from URL. The image URL is wrong or invalid.';
                            $output = 'something_wrong';
                            break;

                        case "IMAGE_FILE_TOO_LARGE" :

                            $output = "face_file_size_err";
                            break;

                        case "IMAGE_DOWNLOAD_TIMEOUT" :

                            //$output = 'Image download timeout, please try after some time.';
                            $output = 'something_wrong';
                            break;

                        case "CONCURRENCY_LIMIT_EXCEEDED" :

                            //$output = 'The rate limit for this API Key has been exceeded, please try after some time.';
                            $output = 'something_wrong';
                            break;

                        default:
                            $output = 'something_wrong';
                    }
                }

                return $output;

            } else {

                $errmsg = curl_error($ch);
                return $errmsg;
            }

            curl_close($ch);               

        } else {

            $errmsg = "face_select_file_req";
            return $errmsg;
        }
    }

}