<?php  if(!empty($media)){  ?>
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
         
        <div class="body">
            <div id="aniimated-thumbnials" class="list-unstyled row clearfix galleryList">
                <?php 
                foreach ($media as $rows) {

                    if(!filter_var($rows->imgName, FILTER_VALIDATE_URL) === false) {
                        $img = $rows->imgName;
                    }else if(!empty($rows->imgName)){ 
                        $img = AWS_CDN_USER_THUMB_IMG.$rows->imgName;
                    } else{
                        $img = AWS_CDN_USER_PLACEHOLDER_IMG;
                    }

                    if(!filter_var($rows->imgName, FILTER_VALIDATE_URL) === false) {
                        $imgGal = $rows->imgName;
                    }else if(!empty($rows->imgName)){ 
                        $imgGal = AWS_CDN_USER_IMG_PATH.$rows->imgName;
                    } else{
                        $imgGal = AWS_CDN_USER_PLACEHOLDER_IMG;
                    }
                ?>
                <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12" >                   

                    <a href="<?php echo  $imgGal ;?>">
                        <img class="img-responsive thumbnail resize resize2" src="<?php echo  $img ;?>">
                    </a>
                </div>
                <?php } ?> 
            </div>
        </div>    
    </div>
</div>

<?php }else{ ?>
    <center><span><font color="red">No Media Avialable!</font></span></center>
<?php  } ?>

<script type="text/javascript">
     
    $('#aniimated-thumbnials').lightGallery({
        thumbnail: true,
        selector: 'a',
        download: false
    });

</script>
    
                          