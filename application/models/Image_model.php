<?php
use Aws\Common\Exception\MultipartUploadException;
use Aws\S3\MultipartUploader;
use Aws\S3\S3Client;
use Aws\S3\ObjectUploader;
class Image_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('string');
    }
    
    //add image data in attachment table
    function add_image_data($att_data){
        $this->db->insert(ATTACHMENTS, $att_data);
        $last_id = $this->db->insert_id();
        if($last_id){
            return $last_id;
        }
        else{
            return FALSE;
        }
    }
    
    function image_sizes($folder){
        //add folder name
        $img_sizes = array();
        
        switch($folder){

            case 'profile' :
                $img_sizes['thumbnail'] = array('width'=>300, 'height'=>300, 'folder'=>'/thumb');
                $img_sizes['medium'] = array('width'=>600, 'height'=>600, 'folder'=>'/medium');
                //$img_sizes['large'] = array('width'=>1024,'height'=>768,'folder'=>'/large');
                break;

            case 'business' :
                $img_sizes['thumbnail'] = array('width'=>420, 'height'=>241, 'folder'=>'/thumb');
                $img_sizes['medium'] = array('width'=>600, 'height'=>600, 'folder'=>'/medium');
                break;
                
            case 'event' :
                $img_sizes['thumbnail'] = array('width'=>370, 'height'=>278, 'folder'=>'/thumb');
                $img_sizes['medium'] = array('width'=>660, 'height'=>444, 'folder'=>'/medium');
                break;

            case 'idProof' :
                $img_sizes['thumbnail'] = array('width'=>370, 'height'=>278, 'folder'=>'/thumb');
                $img_sizes['medium'] = array('width'=>660, 'height'=>444, 'folder'=>'/medium');
                break;

            case 'faceProof' :
                $img_sizes['thumbnail'] = array('width'=>370, 'height'=>278, 'folder'=>'/thumb');
                //$img_sizes['medium'] = array('width'=>660, 'height'=>444, 'folder'=>'/medium');
                break;

            case 'pdf' :
                $img_sizes['thumbnail'] = array('width'=>370, 'height'=>278, 'folder'=>'/thumb');
                $img_sizes['medium'] = array('width'=>660, 'height'=>444, 'folder'=>'/medium');
                break;
        }
            
        return $img_sizes;
    }

    function makedirs($folder='', $mode=DIR_WRITE_MODE, $defaultFolder='uploads/'){

        if(!@is_dir(FCPATH . $defaultFolder)) {

            mkdir(FCPATH . $defaultFolder, $mode);
        }
        if(!empty($folder)) {

            if(!@is_dir(FCPATH . $defaultFolder . '/' . $folder)){
                mkdir(FCPATH . $defaultFolder . '/' . $folder, $mode,true);
            }
        } 
    }//End Function
    function makedirsBk($folder='', $mode=DIR_WRITE_MODE, $defaultFolder='../uploads/'){

        if(!@is_dir(FCPATH . $defaultFolder)) {

            mkdir(FCPATH . $defaultFolder, $mode);
        }
        if(!empty($folder)) {

            if(!@is_dir(FCPATH . $defaultFolder . '/' . $folder)){
                mkdir(FCPATH . $defaultFolder . '/' . $folder, $mode,true);
            }
        } 
    }//End Function

    function rotate_image($exif_img_path, $rotate_config=array()){
        
        //Check EXIF
        $exif = @exif_read_data($exif_img_path);
        
        if($exif && isset($exif['Orientation'])){
            
            $ort = $exif['Orientation'];

            if ($ort == 6 || $ort == 5)
                $rotate_config['rotation_angle'] = '270';
            if ($ort == 3 || $ort == 4)
                $rotate_config['rotation_angle'] = '180';
            if ($ort == 8 || $ort == 7)
                $rotate_config['rotation_angle'] = '90';
                
            $this->image_lib->initialize($rotate_config); 

            if ( ! $this->image_lib->rotate()){
                // Error Message here
                $error = array('error' => $this->image_lib->display_errors());
                return $error;
            }
            
            $this->image_lib->clear();
        }
        
        return TRUE;
    }
     
    function updateMedia($image,$folder,$height=768,$width=1024,$path=FALSE){

        $this->makedirs($folder);
        
        $realpath = $path ?'../uploads/':'uploads/';
        $allowed_types = "jpg|png|jpeg";    
        $img_name = random_string('alnum', 16);         //generate random string for image name
        
        $img_sizes_arr = $this->image_sizes($folder);   //predefined sizes in model
        
        //We will set min height and width according to thumbnail size
        $min_width = $img_sizes_arr['thumbnail']['width'];
        $min_height = $img_sizes_arr['thumbnail']['height'];
                
        $config = array(
            'upload_path'       => $realpath.$folder,
            'allowed_types'     => $allowed_types,
            'max_size'          => "10240",             // File size limitation, initially w'll set to 10mb (Can be changed)
            'max_height'        => "9000",             // max height in px
            'max_width'         => "9000",             // max width in px
            'min_width'         => $min_width,          // min width in px
            'min_height'        => $min_height,         // min height in px
            'file_name'         => $img_name,
            'overwrite'         => FALSE,
            'remove_spaces'     => TRUE,
            'quality'           => '100%',
        );
        
        $this->load->library('upload'); //upload library
        $this->upload->initialize($config);
 
        if(!$this->upload->do_upload($image)){
            $error = array('error' => $this->upload->display_errors());
            return $error; //error in upload
        }
        
        //image uploaded successfully - We will now resize and crop this image
        
        $image_data = $this->upload->data(); //get uploaded image data
        
        $this->load->library('image_lib'); //Load image manipulation library
        $thumb_img = $source_img_path = '';

        //upload to AWS S3 bucket?
        if($this->config->item('bucket_upload')===TRUE){
            $upload_path=$realpath.$folder;
            $source_img_path = FCPATH .$upload_path.'/'.$image_data['file_name'];
            $s3_result = $this->aws_bucket_upload($source_img_path, $upload_path, $image_data['file_name'], FALSE);
            
            if($s3_result!==TRUE){
                $error = array('error' => 'Problem uploading image. Please try again');
                return $error; //error in upload
            }
        }

        foreach($img_sizes_arr as $k=>$v){
            
            // create resize sub-folder
            $sub_folder = $folder.$v['folder'];
            $this->makedirs($sub_folder);

            $real_path = realpath(FCPATH .$realpath .$folder);

            $resize['image_library']      = 'gd2';
            $resize['source_image']       = $image_data['full_path'];
            $resize['new_image']          = $real_path.$v['folder'].'/'.$image_data['file_name'];
            $resize['maintain_ratio']     = TRUE; //maintain original image ratio
            $resize['width']              = $v['width'];
            $resize['height']             = $v['height'];
            $resize['quality']            = '100%';
            // We need to know whether to use width or height edge as the hard-value. 
            // After the original image has been resized, either the original image width’s edge or 
            // the height’s edge will be the same as the container
            $dim = (intval($image_data["image_width"]) / intval($image_data["image_height"])) - ($v['width'] / $v['height']);
            $resize['master_dim'] = ($dim > 0)? "height" : "width";

            $this->image_lib->initialize($resize);
            $is_resize = $this->image_lib->resize();   //create resized copies
            
            //image resizing maintaining it's aspect ratio is done. Now we will start cropping the resized image
            $source_img = $real_path.$v['folder'].'/'.$image_data['file_name'];
            
            if($is_resize && file_exists($source_img)){

                //rotate image if necessary
                $rotate_config['image_library'] = 'gd2';
                $rotate_config['source_image'] = $source_img;
                $this->rotate_image($image_data['full_path'], $rotate_config);
                
                $source_image_arr = getimagesize($source_img);
                $source_image_width = $source_image_arr[0];
                $source_image_height = $source_image_arr[1];
                
                $source_ratio = $source_image_width / $source_image_height;
                $new_ratio = $v['width'] / $v['height'];
                
                if($source_ratio != $new_ratio){
                    
                    //image cropping config
                    $crop_config['image_library'] = 'gd2';
                    $crop_config['source_image'] = $source_img;
                    $crop_config['new_image'] = $source_img;
                    $crop_config['quality'] = "100%";
                    $crop_config['maintain_ratio'] = FALSE;
                    $crop_config['width'] = $v['width'];
                    $crop_config['height'] = $v['height'];
                    
                    if($new_ratio > $source_ratio || (($new_ratio == 1) && ($source_ratio < 1))){
                        //Source image height is greater than crop image height
                        //So we need to move on vertical(Y) axis while keeping horizantal(X) axis static(0)
                        $crop_config['y_axis'] = round(($source_image_height - $crop_config['height'])/2);
                        $crop_config['x_axis'] = 0;
                    }else{
                        //Source image width is greater than crop image width
                        //So we need to move on horizontal(X) axis while keeping vertical(Y) axis static(0)
                        $crop_config['x_axis'] = round(($source_image_width - $crop_config['width'])/2);
                        $crop_config['y_axis'] = 0;
                    }
                    //$crop_config['x_axis'] = 0;
                    //$crop_config['y_axis'] = 0;
                    
                    $this->image_lib->initialize($crop_config); 
                    $this->image_lib->crop();
                    $this->image_lib->clear();
                }
            }

            //upload to AWS S3 bucket?
            if($this->config->item('bucket_upload')===TRUE){
                $dest_upload_path = $realpath.$folder.$v['folder'];
                $s3_result = $this->aws_bucket_upload($source_img, $dest_upload_path, $image_data['file_name']); 
            }
        }        

        if(empty($thumb_img))
            $thumb_img = $image_data['file_name'];

        unlink($source_img_path);
        return $thumb_img; 

    } // End Function

    function unlinkFile($path,$file){

            $main   = $path.$file;
            $thumb  = $path.'thumb/'.$file;
            $medium = $path.'medium/'.$file;
            $large = $path.'large/'.$file;

            if(file_exists(FCPATH.$main)):
                unlink( FCPATH.$main);
            endif;
            if(file_exists(FCPATH.$thumb)):
                unlink( FCPATH.$thumb);
            endif;
            if(file_exists(FCPATH.$medium)):
                unlink( FCPATH.$medium);
            endif;
            if(file_exists(FCPATH.$large)):
                unlink( FCPATH.$large);
            endif;
            return TRUE;
    }//End function

    /**
     * Upload multipart data to AWS S3 bucket
     * Added in ver 3.1
     */
    function aws_bucket_upload($source_img_path, $upload_path, $image_name, $unlink=TRUE){

        $s3Client = S3Client::factory(
        array(
            'credentials' => array(
                'key' => AWS_BUCKET_KEY,
                'secret' => AWS_BUCKET_SECRET
            ),
            'version' => 'latest',
            'region'  => AWS_BUCKET_REGION
        )
        );
        
        $bucket = AWS_BUCKET_NAME;

        // path: uploads/profile/thumb/abc.jpg
        // If Folder is not there at bucket, SDK will create it and then upload
        $key = $upload_path.'/'.$image_name; 
        
        // Using stream instead of file path
        $source = fopen($source_img_path, 'rb');
        
        $uploader = new ObjectUploader(
            $s3Client,
            $bucket,
            $key,
            $source
        );
        
            try {
                $result = $uploader->upload();
                if ($result["@metadata"]["statusCode"] == '200') {
                    //print('<p>File successfully uploaded to ' . $result["ObjectURL"] . '.</p>');

                    //File uploaded successfully, unlink file from application uploads folder
                    if($unlink === TRUE){
                        unlink($source_img_path);                        
                    }
                    return TRUE;
                }
                
            } catch (MultipartUploadException $e) {
                rewind($source);
                $uploader = new MultipartUploader($s3Client, $source, [
                    'state' => $e->getState(),
                ]);
            }
        

        //Abort a multipart upload if failed
        try {
            $result = $uploader->upload();
        } catch (MultipartUploadException $e) {
            // State contains the "Bucket", "Key", and "UploadId"
            $params = $e->getState()->getId();
            $result = $s3Client->abortMultipartUpload($params);
            return FALSE;
        }
    }

    /**
     * Upload, resize and compress image to make it usable for other third
     * party APIs
     * Added in ver 3.1
     */
    function upload_n_compress($image, $folder){

        $this->makedirs($folder);
        
        $realpath = 'uploads/';
        $allowed_types = "jpg|png|jpeg";    
        $img_name = random_string('alnum', 16);  //generate random string for image name
        
        //We will set min height and width according to thumbnail size
        $min_width  = 300;
        $min_height = 300;
                
        $config = array(
            
            'upload_path'       => $realpath.$folder,
            'allowed_types'     => $allowed_types,
            'max_size'          => "10240",         // File size limitation, initially w'll set to 10mb (Can be changed)
            'max_height'        => "9000",          // max height in px
            'max_width'         => "9000",          // max width in px
            'min_width'         => $min_width,      // min width in px
            'min_height'        => $min_height,     // min height in px
            'file_name'         => $img_name,
            'overwrite'         => TRUE,
            'remove_spaces'     => TRUE,
        );
        
        $this->load->library('upload'); //upload library
        $this->upload->initialize($config);
 
        if(!$this->upload->do_upload($image)){
            $error = array('error' => 'Problem uploading image please try again');
            return $error; //error in upload
        }
        
        //image uploaded successfully-We will now resize and compress this image
        
        $image_data = $this->upload->data(); //get uploaded image data
        $this->load->library('image_lib'); //Load image manipulation library
        // create compress, resize and put it in same directory
        $resize['image_library']      = 'gd2';
        $resize['source_image']       = $image_data['full_path'];
        $resize['new_image']          = $image_data['full_path'];
        $resize['maintain_ratio']     = TRUE; //maintain original image ratio
        $resize['width']              = '500';
        $resize['height']             = '500';
        $resize['quality']            = '90%';  //reduce image quality to reduce its size

        $this->image_lib->initialize($resize);
        $is_resize = $this->image_lib->resize();
        if(!$is_resize){
            $error = array('error' => $this->image_lib->display_errors());
            return $error; //error in upload 
        }
        $this->image_lib->clear();  //clear memory
        return $image_data['file_name'];
    }

    function updateGallery($fileName,$folder,$hieght=250,$width=250) {
        $this->makedirs($folder);

        $storedFile         = array();
        $allowed_types      = "gif|jpg|png|jpeg"; 
        $files              = $_FILES[$fileName];
        $number_of_files    = sizeof($_FILES[$fileName]['tmp_name']);

        // we first load the upload library
        $this->load->library('upload');
        // next we pass the upload path for the images
        $configG['upload_path']         = 'uploads/'.$folder;
        $configG['allowed_types']       = $allowed_types;
        $configG['max_size']            = '2048000';
        $configG['encrypt_name']        = TRUE;
        $configG['quality']             = '100%';
   
        // now, taking into account that there can be more than one file, for each file we will have to do the upload
        for ($i = 0; $i < $number_of_files; $i++)
        {
            $_FILES[$fileName]['name']      = $files['name'][$i];
            $_FILES[$fileName]['type']      = $files['type'][$i];
            $_FILES[$fileName]['tmp_name']  = $files['tmp_name'][$i];
            $_FILES[$fileName]['error']     = $files['error'][$i];
            $_FILES[$fileName]['size']      = $files['size'][$i];

            //now we initialize the upload library
            $this->upload->initialize($configG);
            if ($this->upload->do_upload($fileName))
            {
                $savedFile = $this->upload->data();//upload the image
            
                $folder_thumb = $folder.'/thumb/';
                $this->makedirs($folder_thumb);
                //your desired config for the resize() function
                $config1 = array(
                    'image_library'     => 'gd2',
                    'source_image'      => $savedFile['full_path'], //get original image
                    'maintain_ratio'    => false,
                    //'create_thumb'    => TRUE,
                    'width'             => 100,
                    'height'            => 100,
                    'new_image'         => realpath(FCPATH .'uploads/'.$folder_thumb),
                    'quality'           => '100%'
                );  
                $this->load->library('image_lib'); //load image_library
                $this->image_lib->initialize($config1);
                $this->image_lib->resize();
                $folder_resize = $folder.'/resize/';
                $this->makedirs($folder_resize);

                $resize1['source_image']    = $savedFile['full_path'];
                $resize1['new_image']       = realpath(FCPATH .'uploads/'.$folder_resize);
                $resize1['maintain_ratio']  = FALSE;
                $resize1['width']           = $width;
                $resize1['height']          = $hieght;
                $resize1['quality']         = '100%';

                $this->image_lib->initialize($resize1);
                $this->image_lib->resize();

                $storedFile[$i]['name'] = $savedFile['file_name'];
                $storedFile[$i]['type'] = $savedFile['file_type'];
                
                $this->image_lib->clear();
                
            } else {
                $storedFile[$i]['error'] = $this->upload->display_errors();
            }
        } // END OF FOR LOOP
         
        return $storedFile;
          
    }//FUnction

    function upload_img($profile_image,$folder) { 

        $this->makedirs($folder);

        $config = array(
            'upload_path' => FCPATH.'uploads/'.$folder,
            'allowed_types' => "gif|jpg|png|jpeg|JPG|PNG|JPEG|",
            'overwrite' => false,
            'max_size' => "2048000", // Can be set to particular file size , here it is 2 MB(2048 Kb)
            'encrypt_name'=>TRUE ,
            'remove_spaces'=>TRUE
        );

        $this->load->library('upload');
        $this->upload->initialize($config);

        if(!$this->upload->do_upload($profile_image)){
            $error = array('error' => $this->upload->display_errors());
            return $error;

        } else {

            $this->load->library('image_lib');
            $folder_thumb = $folder.'/thumb/';
            $this->makedirs($folder_thumb);

            $width = 100;
            $height = 100;

            $image_data = $this->upload->data(); //upload the image

            $resize['source_image'] = $image_data['full_path'];
            $resize['new_image'] = realpath(APPPATH . '../upload/' . $folder_thumb);
            $resize['maintain_ratio'] = true;
            $resize['width'] = $width;
            $resize['height'] = $height;

            //send resize array to image_lib's  initialize function
            $this->image_lib->initialize($resize);
            $this->image_lib->resize();
            $this->image_lib->clear();

            return $image_data['file_name'];
        }
    }

}// End of class Image_model

?>
