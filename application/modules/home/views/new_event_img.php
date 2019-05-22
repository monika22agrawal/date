<ul class="add-media clearfix">
	<?php foreach ($eventImg as $key => $value) { 

		if(!empty($value->eventImageName)){ 
	        $img = AWS_CDN_EVENT_THUMB_IMG.$value->eventImageName;
	    } else{                    
	        $img = AWS_CDN_EVENT_PLACEHOLDER_IMG;
	    }

	?>
	
	<li id="dImg<?php echo $value->eventImgId;?>" class='img_count_no'>
		<div class="upload_rht">
		    <img src="<?php echo $img;?>" class="img_count_no" id="eventImg<?php echo $key;?>"/>
		    <i class="fa fa-close" onclick="deleteEventImg('<?php echo $value->eventImgId; ?>'); "></i>
		</div>	
	</li>

	<?php } ?>
</ul>