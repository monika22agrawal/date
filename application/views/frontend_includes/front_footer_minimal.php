<?php
    $frontend_assets =  base_url().'frontend_asset/';
?>
<!--================Footer Area =================-->
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
        
        <script src="<?php echo AWS_CDN_FRONT_JS; ?>bootstrap.min.js"></script>
      
    </body>
</html>