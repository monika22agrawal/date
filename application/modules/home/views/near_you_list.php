<?php 

    $frontend_assets =  base_url().'frontend_asset/';
    if($this->session->userdata('latitude') != '' && $this->session->userdata('longitude') != '' ){
        $latitude=$this->session->userdata('latitude');
        $longitude=$this->session->userdata('longitude');
    }else{
        $latitude="";
        $longitude="";
    }
?>
<?php if($viewType == 'list'){ if(!empty($nearUsers)){ foreach ($nearUsers as $get) { ?>

    <script type="text/javascript">
        checkOnline("<?php echo $get['userId']; ?>");
    </script>
    <div class="col-sm-2 col-xs-6 usersVisitCount" id="userOnId<?php echo $get['userId']; ?>">
        <div class="active_mem_item">
            <ul class="nav navbar-nav">
                <li class="dropdown tool_hover">
                
                <?php  
                if(!filter_var($get['profileImage'], FILTER_VALIDATE_URL) === false) { 
                    $img = $get['profileImage'];
                }else if(!empty($get['profileImage'])){ 
                    $img = AWS_CDN_USER_THUMB_IMG.$get['profileImage'];
                } else{                    
                    $img = AWS_CDN_USER_PLACEHOLDER_IMG;
                } ?>
                <a href="<?php echo base_url('home/user/userDetail/').encoding($get['userId']).'/';?>"><img class="round-img" src="<?php echo $img;?>" alt="">
                    <!-- show online/offline for mobile view -->
                    <span class="statusOnOff">
                        <div class="" id="mobileOnOff<?php echo $get['userId']; ?>"></div>
                    </span>
                    <!-- show online/offline for mobile view -->
                </a>

                    <ul class="dropdown-menu">
                        <li>
                            <div class="head_area">
                                <h4><?php echo ucfirst($get['fullName']);?></h4>
                                <div class="clearfix"></div>
                            </div>
                            <div class="media whte-hovr-box">
                                <div class="media-left">
                                    <img class="sml-img" src="<?php echo $img;?>" alt="">
                                </div>
                                <div class="media-body pt-8">
                                    <h6><?php echo $get['age'].' yrs';?> , <span class="gndr-term"><?php if($get['gender'] == '1'){
                                        echo lang('male_gender');
                                    }elseif($get['gender'] == '2'){
                                        echo lang('female_gender');
                                    } else {
                                        echo lang('transgender_gender');
                                    } ?>
                                    </span></h6>
                                    <p class="str-sec-pra">
                                        <?php $count = $get['totalRating'];

                                        for($i=1;$i<=$count;$i++){ ?>

                                            <span class="fa fa-star"></span> 

                                        <?php } $minCount = 5-$count; 
                                        
                                        for($j=1;$j<=$minCount;$j++){ ?>

                                            <span class="fa fa-star-o"></span>

                                        <?php } ?>
                                    </p>
                                </div>
                                <div class="" id="checkOnline<?php echo $get['userId']; ?>"></div>
                            </div>
                        </li>
                    </ul>
                </li>
            </ul>
            <h4><?php $var = explode(' ', $get['fullName']); echo ucfirst($var[0]);?></h4>
        </div>
    </div>

<?php } } elseif ($page == 0){ echo "<div class='notFound'><h3>".lang('no_user_found')."</h3></div>"?> 
    <input type="hidden" id="totalUsers-count" value="<?php echo count($nearUsers);?>">          
<?php } }elseif($viewType == 'map'){?>  


<!-- to view users on map -->

<?php
    $i = 1;
    $location = $info = $userIds = array();
    
    if(!empty($nearUsersOnMap)){
        
        foreach($nearUsersOnMap as $latLong){
            
            if($latLong['showOnMap'] == 1){

                ?>
                <script type="text/javascript">
                    checkOnline("<?php echo $latLong['userId']; ?>");
                </script>
                <?php

                $createApp = '';
                $appUrl = AWS_CDN_FRONT_IMG.'ico_map_app.png'; 
                if($this->session->userdata('front_login') == true){

                    /*if($latLong['isAppointment']->isAppointment == 1){
                        $createApp = '<a href="javascript:void(0);" onclick="msg(this)" data-msg="You already sent appointment request to '.$latLong["fullName"].'"><img src='.$appUrl.'></a>';
                    }elseif($latLong['isAppointment']->isAppointment == 2){
                        $createApp = '<a href="javascript:void(0);" onclick="msg(this)" data-msg="You already have an appointment with '.$latLong["fullName"].'"><img src='.$appUrl.'></a>';
                    }else{*/
                        $createApp = '<a href="'.base_url("home/appointment/createAppointment/").encoding($latLong["userId"])."/".'"><img src='.$appUrl.'></a>';
                    //}
                }

                $add = !empty($latLong['address']) ? str_replace(',', '', $latLong['address']) : 'NA';

                if(!filter_var($latLong['profileImage'], FILTER_VALIDATE_URL) === false) { 
                    $img = $latLong['profileImage'];
                }else if(!empty($latLong['profileImage'])){ 
                    $img = AWS_CDN_USER_THUMB_IMG.$latLong['profileImage'];
                } else{                    
                    $img = AWS_CDN_USER_PLACEHOLDER_IMG;
                }

                if($latLong['gender'] == '1'){
                    $mapIcon = MAP_ICON_MAIL;
                }else if($latLong['gender'] == '2'){
                    $mapIcon = MAP_USER_FEMAIL;
                }else{
                    $mapIcon = MAP_USER_TRANSGENDER;
                }

                $url = base_url()."home/user/userDetail/".encoding($latLong['userId']).'/';  

                $userIds[]= $latLong['userId'];
                $location[]= $latLong['fullName'].', '.$latLong['latitude'].', '.$latLong['longitude'].', '.$mapIcon.','.($i);
                $info[]= "<div style='width:219px;' class='map_loca_name_row'><div class='map_img1'>".'<img style="border-radius: 50%;height: 47px;width: 47px;" src="'.$img.'">'."</div> <div class='infoCnt'><a href='javascript:void(0);' class='map_nme2'>".$latLong['fullName']."</a><div class='map_add3'>".$add."</div><div class='appIconmap'>".$createApp."</div></div></div>";
                $i++;
            }
        }
    }    
?>
<script type="text/javascript">   
    
    var locations = <?php echo json_encode($location); ?>;
    var userIds = <?php echo json_encode($userIds); ?>;
    var info = <?php echo json_encode($info);?>;
    var latitude = <?php echo json_encode($latitude);?>;
    var longitude = <?php echo json_encode($longitude);?>;   

    userMap(locations,info,userIds);

    function userMap(locations,info,userIds) {

        for (i = 0; i < locations.length; i++) { 
            var userOnlineStatus = $('#userOnlineStatus').val();

            firebase.database().ref("online").child(userIds[i]).on('value', function(snapshot) {

                if (snapshot.exists()) {

                    var onoff = snapshot.val().lastOnline;
                }else{
                    var onoff = 'offline';
                }
                if(userOnlineStatus == 2 && onoff == 'offline'){
                    delete locations[i];
                } 
            });
        }       

        var newLat = $('#lat').val();
        var newLong = $('#long').val();
        if(newLat != '' && newLong != ''){
            latitude = newLat;
            longitude = newLong;
        }

        var img = '<?php echo MAP_USER;?>';
        var map = new google.maps.Map(document.getElementById('map'), {
            zoom: 13,
            center: new google.maps.LatLng(latitude, longitude),
            mapTypeId: google.maps.MapTypeId.ROADMAP
        });

        //var marker, i;
        for (i = 0; i < locations.length; i++) { 

            var infowindow = new google.maps.InfoWindow();
            if(locations[i]){
                 
                loc_array = locations[i].split(",");
                // info_array = info[i].split(",");

                marker = new google.maps.Marker({
                    position: new google.maps.LatLng(loc_array[1], loc_array[2]),
                    map: map,
                    icon: loc_array[3],
                    animation: google.maps.Animation.DROP
                });

                google.maps.event.addListener(marker, 'click', (function(marker, i) {
                    return function() {
                        //loc_array = locations[i].split(",");
                        info_array = info[i].split(",");
                        infowindow.setContent(info_array[0]);
                        infowindow.open(map, marker);
                    }
                })(marker, i));
            }
        }
    }
    
</script>
<?php }?>