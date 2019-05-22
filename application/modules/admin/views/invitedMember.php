<?php if(!empty($invited_member)){
 
    foreach ($invited_member['list'] as $value) { 

        if(!filter_var($value->userImgName, FILTER_VALIDATE_URL) === false) { 
            $img = $value->userImgName;
        }else if(!empty($value->userImgName)){ 
            $img = AWS_CDN_USER_THUMB_IMG.$value->userImgName;
        } else{                    
            $img = AWS_CDN_USER_PLACEHOLDER_IMG;
        }
?>
    <div class="friendVisit invitedVisitCount" id="iMember">
        <div class="media">
            <div class="media-left">
                <div class="usImg">
                    <a><img src="<?php echo $img;?>"></a>
                </div>
            </div>
            <div class="media-body">
                <div class="nme-ritz">
                    <h4 class="media-heading"><a><?php echo ucfirst($value->fullName);?></a></h4>
                    <div class="serviseslead"><?php echo ucfirst($value->workName);?></div>
                </div>                   
            </div>
        </div>
    </div>
    <?php }  
    if($offset==0){?>
    <div>
        <div id="memberData">
        <!--load data -->
        </div>

        <div class="PaginationBlock">
            <input type="hidden" name="totalCount" id="totalCountss" value="<?php echo $total_count?>">
            <div id="loadMember" class="text-center" >
                <button class="btn btn-flat margin" id="btnMem" >Load More</button>
            </div>
        </div>
    </div>

    <?php }
 }else{ ?>

    <div class="media-blck">
        <div class="text-center">
            <img src="<?php echo AWS_CDN_BACK_CUSTOM_IMG ?>team.png" alt="Image" width="80px" />
            <div class=""> No Member Available!</div>
        </div>
       
    </div>
<?php } ?>

        
