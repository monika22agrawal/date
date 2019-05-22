<?php 

$frontend_assets =  base_url().'frontend_asset/';

if(!empty($type)){ ?>
    
    <div class="profile_content">

        <div class="user_img usr-img-like">

            <?php  
            
            if(!filter_var($profile->imgName, FILTER_VALIDATE_URL) === false) { 
                $img = $profile->imgName;
            }else if(!empty($profile->imgName)){ 
                $img = AWS_CDN_USER_THUMB_IMG.$profile->imgName;
            } else{                    
                $img = AWS_CDN_USER_PLACEHOLDER_IMG;
            } ?>

            <img class="img-circle" src="<?php echo $img;?>" alt="">

        </div>

        <div class="user_name">
            <h3 class="text-transform-upr"><?php echo ucfirst($profile->fullName);?></h3>
            <h4><?php if($profile->gender == 1) { echo lang('male_gender');}elseif($profile->gender == 2){echo lang('female_gender');}else{ echo lang('transgender_gender');}?> | <?php echo $profile->age;?></h4>
            <ul>

                <li class="adress mrkr-icn"><span class="fa fa-map-marker"></span><?php echo !empty($profile->address) ? $profile->address : "NA";?></li>
                <ul>
                    <li class="action-icn text-center pt-8">
                        <ul title="<?php echo lang('like'); ?>">
                            <li class="lkes-cnt icn-fnt icn-clrd"><span class="fa fa-heart"></span></li>
                            <li class="lkes-cnt"><?php echo $profile->totalLikes;?></li>
                        </ul>
                    </li>
                    <li class="action-icn pl-25 text-center pt-8">
                        <ul title="<?php echo lang('visits'); ?>">
                            <li class="lkes-cnt icn-fnt icn-clrd"><span class="fa fa-eye"></span></li>
                            <li class="lkes-cnt"><?php echo $profile->totalVisits;?></li>
                        </ul>
                    </li>
                </ul>
            </ul>
        </div>
    </div>

<?php } else { ?>
<div class="profile_list pro-detl">
    <ul>
        <li><span class="min-wdth"><?php echo lang('work_as'); ?></span><a><?php echo !empty($profile->work) ? $profile->work : "NA";?></a></li>
        <li><span class="min-wdth"><?php echo lang('education'); ?></span><a><?php echo !empty($profile->education) ? $profile->education  : "NA";?></a></li>
        <li><span class="min-wdth"><?php echo lang('height'); ?></span><a><?php echo !empty($profile->height) ? $profile->height  : "NA";?></a></li>
        <li><span class="min-wdth"><?php echo lang('weight'); ?></span><a><?php echo !empty($profile->weight) ? $profile->weight  : "NA";?></a></li>
        <li><span class="min-wdth"></span><a></a></li>
    </ul>
    
    <ul>                                    
        <li><span><?php echo lang('relationship'); ?></span><a><?php echo $profile->relationship;?></a></li>
        <li><span><?php echo lang('i_speak'); ?></span><a><?php  $var = explode(',', $profile->language); echo implode(', ', $var); ?></a></li>
        <!-- <li><span><?php echo lang('event_type'); ?></span><a><?php echo ($profile->eventType == 1) ? 'Paid' : ($profile->eventType == 2) ? 'Free' : 'NA';?></a></li> -->
        <li><span><?php echo lang('appointment_type'); ?></span><a><?php echo ($profile->appointmentType == 1) ? 'Paid' : ($profile->appointmentType == 2) ? 'Free' : 'NA';?></a></li>
        <li><a></a></li>
    </ul>    
</div>
<div class="intrst-prt-sec">
    <h2 class="pt-8"><?php echo lang('interest'); ?></h2>
    <div class="interst-spn">
        <?php if(!empty($profile->game)){

            $var = explode(',', $profile->game); 

            foreach ($var as $k => $value) {  ?>

                <span class="<?php echo $k==0 ? 'frst-chld': '';?>"><?php echo $value;?></span>

            <?php } 

            }else { ?>
    
               <span><?php echo 'NA';?></span>   

        <?php } ?>
    </div> 
</div>                                                                                                 
<div class="clearfix"></div>
<div class="members_about_box">
    <h4><?php echo lang('about_me');?></h4>
    <p><?php echo !empty($profile->about) ? $profile->about :"NA";?></p>                            
</div>
<?php if(!empty($bizDetail)){ ?>
    <div class="my-busness-sec mt-15 mb-30">
        <div class="s_title">
            <h4 class="busness-head"><?php echo ucfirst($bizDetail->businessName); ?></h4>
            <img src="<?php echo AWS_CDN_FRONT_IMG;?>widget-title-border.png" alt="">
        </div>
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="busness-img-tab">
                    <img src="<?php echo $bizDetail->businessImage; ?>">
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 pl-0">
                <section class="map_area map-bsnes-area">
                    <?php if($profile->gender == '1'){ 
                        $mapIcon = MAP_ICON_MAIL;
                    }else{
                        $mapIcon = MAP_USER_FEMAIL;
                    }
                    ?>
                    
                    <div  width="100%" height="100%" frameborder="0" style="border:0" allowfullscreen id="mapId" data-icon="<?php echo $mapIcon;?>" data-img="<div style='width:219px;' class='map_loca_name_row'><div class='infoCnt'><div class='map_add3'><?php echo $bizDetail->businessAddress;?></div></div></div>" data-name="<?php echo $profile->fullName;?>" data-address="<?php echo $bizDetail->businessAddress;?>" data-lat="<?php echo $bizDetail->businesslat;?>" data-long="<?php echo $bizDetail->businesslong;?>" class="map-section map-sec">
                    </div>
                </section>
            </div>
        </div>
    </div>
<?php } ?>
<?php } ?>
<script type="text/javascript">    
    initMapProfileBusiness();
</script>