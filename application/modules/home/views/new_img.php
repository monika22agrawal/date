<?php 
    $frontend_assets =  base_url().'frontend_asset/';
?>
<?php if(!empty($type)){ ?>

    <div id="dImg<?php echo $lastId;?>" class="log_div log_rel item ml-10 img_count_no">
        <img class="smll-sze" src="<?php echo $newImg; ?>" id="pImg<?php echo $lastId; ?>" >
        <div class="text-center upload_pic_in_album">
            <input type="file" id="imgMy<?php echo $lastId;?>" class="inputfile hideDiv" name="pImage" onchange="userProfileImages('<?php echo $lastId;?>'); document.getElementById('pImg'+<?php echo $countImg;?>).src = window.URL.createObjectURL(this.files[0]);" style="display: none;">
        </div> 
        <div class="crcle">
            <a href="javascript:void(0)" onclick="deleteProfileImages('<?php echo $lastId; ?>'); "><span class="fa fa-close"></span></a>
        </div>                                                                 
    </div>


<?php }else{ 

	if(!empty($images)){

	?>
	<aside class="s_widget photo_widget" id="loadImgSlider">
	<div class="s_title">
        <h4><?php echo lang('photo');?></h4>
        <img src="<?php echo AWS_CDN_FRONT_IMG;?>widget-title-border.png" alt="">
    </div>
    <div id="apoim-gal" class="lgt-gal-photo">

        <div class="load-img-slider">

        <?php foreach ($images as $value) { ?> 

            <?php  
    
            if(!filter_var($value->imgName, FILTER_VALIDATE_URL) === false) {
                $imgslider = $value->imgName;
            }else if(!empty($value->imgName)){
                $imgslider = AWS_CDN_USER_MEDIUM_IMG.$value->imgName;
            } else{
                $imgslider = AWS_CDN_USER_PLACEHOLDER_IMG;
            }

            if(!filter_var($value->imgName, FILTER_VALIDATE_URL) === false) {
                $imgview = $value->imgName;
            }else if(!empty($value->imgName)){
                $imgview = AWS_CDN_USER_THUMB_IMG.$value->imgName;
            } else{
                $imgview = AWS_CDN_USER_PLACEHOLDER_IMG;
            }
            ?>

            <div class="pic" data-src="<?php echo $imgslider;?>"><img src="<?php echo $imgview;?>" alt=""></div>
        <?php } ?>
        </div>
    </div>
</aside>
<?php } }?>

<script type="text/javascript">
	
	$('#apoim-gal').lightGallery({
      selector: '.pic',
      loop:false,
      zoom:false,
      fullScreen:false,
      share:false,
      download:false,
      autoplayControls:false,
      thumbnail: false
    });
</script>

