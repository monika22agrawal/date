<?php
    $frontend_assets =  base_url().'frontend_asset/';

    $activeTabs = $this->uri->segment('1');

if(!empty($this->uri->segment('2'))){

    $activeTabs = $this->uri->segment('2');
} 

if( !empty($this->uri->segment('2')) && !empty($this->uri->segment('3')) ){
    $activeTabs = $this->uri->segment('3');
}

    $userDetails = $this->common_model->usersDetail($this->session->userdata('userId'));
?>
<!--================Footer Area =================-->
  
    <section id="sidebar-right-noti" class="sidebar-menu sidebar-right3">
        <!-- To show notification list -->
        <div class="Notification" id="notifyOpen">
            <div class="notifyList">
                <div class="notifyHead">
                    <h2><?php echo lang('notification'); ?></h2>
                    <a id="sidebar_close_icon_noti" href="javascript:void(0);" ><i class="fa fa-times"></i></a><!-- id="notifyClose" -->
                </div>
                <ul>                    
                    <div id="notificationList"></div>
                </ul>
            </div>
        </div>
    </section>

    <!--stripe payment pop up-->

    <div class="modal fade" id="stripepopup" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content mdl-subs">
                <div class="modal-header mdl-hdr">
                    <h5 class="modal-title prmte" id="exampleModalLabel"><span id="showTitle"></span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="/charge" data-submit="<?php echo base_url('/welcome/stripe_sub') ?>" method="post" id="payment-form">

                    <input type="hidden" name="" value="" id="getPaymentType">
                    <input type="hidden" name="pageType" value="" id="getPageType">

                    <!-- Only for event member payment and companion payment -->
                    <input type="hidden" name="eventId" value="" id="eventIdPay">
                    <input type="hidden" name="memberId" value="" id="memberIdPay">
                    <input type="hidden" name="eventMemId" value="" id="eventMemIdPay">
                    <input type="hidden" name="eventAmount" value="" id="eventAmtPay">
                    <input type="hidden" name="compId" value="" id="compIdPay">
                    <input type="hidden" name="compMemId" value="" id="compMemIdPay">
                    <input type="hidden" name="groupChat" value="" id="groupChat">
                    <!-- Only for event member payment and companion payment -->

                    <!-- For appointment payment -->
                    <input type="hidden" name="appId" value="" id="appIdPay">
                    <input type="hidden" name="amount" value="" id="appAmount">
                    <input type="hidden" name="appForId" value="" id="appForId">

                    <div class="modal-body mdl-body">
                        <div class="regfrm mdl-pad mt-20">
                            <div class="form-group regfld">
                                <label class="mb-10">Credit or debit card
                                    <span data-toggle="tooltip" title="Test card number: 4242 4242 4242 4242, a random three-digit CVC number, any expiration date in the future, and a random five-digit U.S. ZIP code">(See test detail here)</span>
                                </label>
                                <div id="card-element">
                                <!-- A Stripe Element will be inserted here. -->
                                </div>
                                <!-- Used to display Element errors. -->
                                <div id="card-errors" role="alert"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer mdl-ftr">
                        <div class="form-group text-right pay-btn">
                            <button type="submit" value="LogIn" class="btn form-control login_btn btn_focs_whte"><?php echo lang('pay');?></button>
                            <a href="javascript:void(0)" data-dismiss="modal"><?php echo lang('close');?></a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!--/stripe payment pop up-->    

    <input type="hidden" id="curLat" name="" value="<?php echo $this->session->userdata('lat'); ?>">
    <input type="hidden" id="curLong" name="" value="<?php echo $this->session->userdata('long'); ?>">
    <input type="hidden" id="curAdddress" name="" value="<?php echo $this->session->userdata('address'); ?>">        
    <input type="hidden" id="curCity" name="" value="<?php echo $this->session->userdata('city'); ?>">        
    <input type="hidden" id="curState" name="" value="<?php echo $this->session->userdata('state'); ?>">        
    <input type="hidden" id="curCountry" name="" value="<?php echo $this->session->userdata('country'); ?>">        

        <footer class="footer_area">
            <div class="copyright">
                <div class="footer-flex">
                    <div class="container">
                        <div class="flex-prprty">
                            <div class="copyright_left">
                                <div class="copyright_text">
                                    <ul>
                                        <li><a href="<?php echo base_url('home/about_us');?>" target="_blank"><?php echo lang('about'); ?></a></li>
                                        <li><a href="<?php echo base_url('home/terms');?>" target="_blank"><?php echo lang('terms_conditions'); ?></a></li>
                                        <li><a href="<?php echo base_url('home/privacy');?>" target="_blank"><?php echo lang('privacy_policy'); ?></a></li> 
                                    </ul>
                                </div>
                            </div>
                            <div class="copyright_right">
                                <div class="copyright_social">
                                    <ul>
                                        <li><a href="javascript:void(0);"><i class="fa fa-facebook" aria-hidden="true"></i></a></li>
                                        <li><a href="javascript:void(0);"><i class="fa fa-twitter" aria-hidden="true"></i></a></li>
                                        <li><a href="javascript:void(0);"><i class="fa fa-linkedin" aria-hidden="true"></i></a></li>
                                        <!-- <li><a href="javascript:void(0);"><i class="fa fa-google-plus" aria-hidden="true"></i></a></li> -->
                                        <li><a href="javascript:void(0);"><i class="fa fa-instagram" aria-hidden="true"></i></a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="copyrgt-sec text-center">
                    <h4>Copyright © <?php echo date('Y');?>. <a href="<?php echo base_url();?>">Apoim</a> . <?php echo lang('copy_right'); ?></h4>
                </div>
            </div>
        </footer>
        <!--================End Footer Area =================-->

        <?php $getCount = $this->common_model->get_total_count(NOTIFICATIONS,array('notificationFor'=>$this->session->userdata('userId'),'isRead' =>0,'webShow'=>0)); ?>

    <div class="sidebar_overlay_noti"></div>
    <?php if($this->session->userdata('userId') != '' && $this->session->userdata('front_login') == TRUE){?>
        <div class="Notify">
            <a id="notification" href="javascript:void(0);" onclick="ajax_notifications('<?php echo base_url('home/getNotificationList');?>')" class="notificatonIcon"> <span class="icNot"><i class="fa fa-bell"></i></span>
            <?php if($getCount != 0){?><span class="notCount" id='totalCount'><?php echo $getCount;?></span><?php } ?> </a>
        </div>
    <?php } ?>
        
        
        <!-- Include all compiled plugins (below), or include individual files as needed -->
        <script src="<?php echo AWS_CDN_FRONT_JS; ?>bootstrap.min.js"></script>
  
        <!-- Extra plugin js -->
        <script src="<?php echo AWS_CDN_FRONT_VENDORS; ?>image-dropdown/jquery.dd.min.js"></script>
        <script src="<?php echo AWS_CDN_FRONT_VENDORS; ?>bootstrap-selector/bootstrap-select.js"></script>

        <script src="<?php echo AWS_CDN_FRONT_VENDORS; ?>bootstrap-datepicker/js/moment-with-locales.js"></script>
        
        <script src="<?php echo AWS_CDN_FRONT_VENDORS; ?>bootstrap-datepicker/js/bootstrap-datetimepicker.min.js"></script>
      
        <!-- <script src="<?php echo $frontend_assets ?>vendors/counter-up/jquery.counterup.min.js"></script> -->
        <script src="<?php echo AWS_CDN_FRONT_JS; ?>owl.carousel.min.js"></script>
        <script src="<?php echo AWS_CDN_FRONT_JS; ?>toastr.min.js"></script>
        <script src="<?php echo AWS_CDN_FRONT_VENDORS; ?>progress-circle/circle-progress.min.js"></script>
        <script src="<?php echo AWS_CDN_FRONT_VENDORS; ?>jquery-ui/jquery-ui.js"></script>
          
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/lightgallery/1.6.11/js/lightgallery-all.min.js"></script>     

        <?php if(!empty($front_scripts)) { load_js($front_scripts);}  //load required page scripts ?>
        <?php 

        if($activeTabs == 'nearByYou' || $activeTabs == 'viewAppOnMap' || $activeTabs == 'userProfile' || $activeTabs == 'eventRequestDetail' || $activeTabs == 'myEventDetail' || $activeTabs == 'addBusiness'){ ?>

            <script type="text/javascript" src="<?php echo base_url().APP_FRONT_ASSETS; ?>custom/js/stripe.js"></script>

        <?php } ?>
        <script type="text/javascript" src="<?php echo base_url().APP_FRONT_ASSETS; ?>custom/js/front_common.js"></script>        
        
        <script src="<?php echo AWS_CDN_FRONT_JS; ?>custom.js"></script>

        <script type="text/javascript">
            
            // set function calling time for showing notifications on browser
            Notification.requestPermission().then(function(result) {
                console.log(result);
            });
            
            // get notification status 1 or 0 from db to show notification
            function myFunction(){

                let latitude    = $.trim($('#curLat').val());
                let longitude   = $.trim($('#curLong').val());
                let address     = $.trim($('#curAdddress').val());
                let city        = $.trim($('#curCity').val());
                let state       = $.trim($('#curState').val());
                let country     = $.trim($('#curCountry').val());

                $.ajax({
                    url: '<?php echo base_url(); ?>home/checklogin',
                    type: "POST",
                    data: {latitude:latitude,longitude:longitude,address:address,city:city,state:state,country:country},
                    cache: false,
                    dataType: "JSON", 
                    success: function(result) {

                        if(result.status == 1){
                            
                            var data = result.html;    
                            var URL = result.url;           
                            var data1 = data[0].message;
                            //console.log(data1);
                            //$.each(JSON.parse(result), function(key, value) {
                                spawnNotification(data1.body,data1.title,URL)
                            // });
                        }
                    }
                });
            }
            myFunction();
            window.setInterval(function(){
                myFunction();
            }, 7000);
            // to view notification on browsers
            Notification.requestPermission();
            function spawnNotification(theBody,theTitle,URL) {
                
                var options = {
                    body: theBody,
                    icon: frontImgPath+'browser_logo.png'
                }
                var notification = new Notification(theTitle, options);
                notification.onclick = function(event) {
                    event.preventDefault(); // prevent the browser from focusing the Notification's tab
                    window.location.href = URL; 
                }
                setTimeout(notification.close.bind(notification), 7000);
            }

            // to connect firebase & set user's online/ofline status 
            var onlineId = "<?php echo $this->session->userdata('userId'); ?>";
            var email = "<?php echo $this->session->userdata('email'); ?>";
            if(onlineId != ''){
                var myConnectionsRef = firebase.database().ref('online/'+onlineId+'/connections');
                //var lastOnlineRef = firebase.database().ref('online/'+onlineId+'/lastOnline');
                
                var userRef = firebase.database().ref('online/'+onlineId+'/lastOnline');
                var userRef1 = firebase.database().ref('online/'+onlineId+'/email');
                var userRef2 = firebase.database().ref('online/'+onlineId+'/timestamp');

                var connectedRef = firebase.database().ref('.info/connected');
                connectedRef.on('value', function(snap) {

                    if (snap.val()) {
                        userRef.onDisconnect().set("offline");
                        userRef2.onDisconnect().set(Date.now());
                        //userRef.onDisconnect().set({"Check":"offline","time":Date.now()});
                        userRef.set('online');
                        userRef1.set(email);
                        userRef2.set('');
                        //userRef.set({"Check":"online","time":Date.now()});
                    }
                });
            }
            
            <?php if($this->session->userdata('userId') != ''){?>

                firebase.database().ref().child('webnotification').child('<?php echo $userDetails->userId;?>').set(null);
            // to register user's in firebase db
            function authUser(){
                firebase.auth().createUserWithEmailAndPassword('<?php echo $userDetails->userId;?>'+"@apoim.com" ,'12345678').catch(function(error) {});
                        firebase.auth().signInWithEmailAndPassword('<?php echo $userDetails->userId;?>'+"@apoim.com" ,'12345678').catch(function(error) {});

                        var userObj = {
                            "email" : '<?php echo $userDetails->email;?>',
                            "firebaseId" : "",
                            "firebaseToken" :"<?php echo $userDetails->deviceToken;?>",
                            "isNotification" :"<?php echo $userDetails->isNotification;?>",
                            "authToken" :"<?php echo $userDetails->authToken;?>",
                            "name" : '<?php echo $userDetails->fullName;?>',
                            "profilePic" : '<?php echo !empty($userDetails->profileImage) ? $userDetails->profileImage : AWS_CDN_USER_PLACEHOLDER_IMG;?>',
                            "uid" : '<?php echo $userDetails->userId;?>'.toString(),
                        } 
                        firebase.database().ref("users").child('<?php echo $userDetails->userId;?>').set(userObj);
            } authUser();

            /*firebase.database().ref("chat_history").child('<?php echo $userDetails->userId;?>').on('child_added', function(snapshot1) { 
                checkMsg();
            });

            firebase.database().ref("chat_history").child('<?php echo $userDetails->userId;?>').on('child_changed', function(snapshot2) { 
                checkMsg();
            });

            function checkMsg(){
                firebase.database().ref("chat_history").child('<?php echo $userDetails->userId;?>').on('value', function(snapshot3) { 
                    var oneMsgs = snapshot3.val();
                    var showD = 0;
                    $.each(oneMsgs, function(key, value) {
                        if (typeof(value) != "undefined"){

                            if(value.readBy == '<?php echo $userDetails->userId;?>'){
                            if(value.readBy){
                                showD = 1;
                            }
                            }
                        }
                    });

                    if(showD == 1){
                        $('.showUIconId').addClass('fa fa-circle');
                    }else{
                        $('.showUIconId').removeClass('fa fa-circle');
                    }       
                });
            }*/

            var reciveIdRef = firebase.database().ref().child('webnotification').child('<?php echo $userDetails->userId;?>');
            reciveIdRef.on("value",getNotiData);

            function getNotiData(rdata){

                var rdata = rdata.val();

                if(rdata){

                    var keys = Object.keys(rdata);

                    for (var i = 0; i < keys.length; i++) {

                        var k       = keys[i];
                        var message = rdata[k].body;
                        var title   = rdata[k].title;
                        var url     = rdata[k].url;

                        notifyBrowser(title, message, url);
                        window.setInterval(function(){
                           firebase.database().ref().child('webnotification').child('<?php echo $userDetails->userId;?>').child(k).set(null);
                        }, 1000);
                        
                    }
                }
            }

            function notifyBrowser(title, desc, url) {

                if (!Notification) {

                    console.log('Desktop notifications not available in your browser..');
                    return;
                }

                if (Notification.permission !== "granted") {

                    Notification.requestPermission();

                } else {

                    var notification = new Notification(title, {
                        icon: frontImgPath+'browser_logo.png',
                        body: desc,
                    });

                    notification.onclick = function() {

                        window.open(url);
                    };

                    notification.onclose = function() {

                        console.log('Notification closed');
                    };
                }
            }

            
            <?php } ?>
            
            
        </script>
    </body>
</html>