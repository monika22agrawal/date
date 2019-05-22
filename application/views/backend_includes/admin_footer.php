<?php
    $backend_asset = base_url().'backend_asset/';
?>

</div>
 <div class="loaderOverlay" id="tl_admin_loader"> 
    <div class="CssLoader">
        <div class="loader loader-5">
            <div class="loader-pacman"></div>
        </div>
        <img src="<?php echo AWS_CDN_BACK_CUSTOM_IMG ?>ic.png">
    </div>
</div>
    <footer class="main-footer">    
        <strong>Copyright &copy; <?php echo date('Y');?></strong> All rights
        reserved.
    </footer>
      <!-- /.control-sidebar -->
      <!-- Add the sidebar's background. This div must be placed
           immediately after the control sidebar -->
      <div class="control-sidebar-bg"></div>
    </div>
    <!-- ./wrapper -->

    <!-- jQuery 2.2.3 -->
    
    <!-- jQuery UI 1.11.4 -->
    <script src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
    <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
    <script>
      $.widget.bridge('uibutton', $.ui.button);
    </script>
    <!-- Bootstrap 3.3.6 -->
    <script src="<?php echo AWS_CDN_BACK_BOOTSTRAP_JS ?>bootstrap.min.js"></script>
    <!-- Material Design -->
    <script src="<?php echo AWS_CDN_BACK_DIST_JS ?>material.min.js"></script>
    <script src="<?php echo AWS_CDN_BACK_DIST_JS ?>ripples.min.js"></script>
    <script>
        $.material.init();
    </script>
    <!-- Morris.js charts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
    <!-- <script src="<?php //echo AWS_CDN_BACK_PLUGINS ?>morris/morris.min.js"></script>  -->
    <!-- Sparkline -->
    <script src="<?php echo AWS_CDN_BACK_PLUGINS ?>sparkline/jquery.sparkline.min.js"></script>
    <!-- jvectormap -->
    <script src="<?php echo AWS_CDN_BACK_PLUGINS ?>jvectormap/jquery-jvectormap-1.2.2.min.js"></script>
    <script src="<?php echo AWS_CDN_BACK_PLUGINS ?>jvectormap/jquery-jvectormap-world-mill-en.js"></script>
    <!-- jQuery Knob Chart -->
    <script src="<?php echo AWS_CDN_BACK_PLUGINS ?>knob/jquery.knob.js"></script>
    <!-- daterangepicker -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
    <script src="<?php echo AWS_CDN_BACK_PLUGINS ?>daterangepicker/daterangepicker.js"></script>
    <!-- datepicker -->
    <script src="<?php echo AWS_CDN_BACK_PLUGINS ?>datepicker/bootstrap-datepicker.js"></script>
    <!-- Bootstrap WYSIHTML5 -->
    <script src="<?php echo AWS_CDN_BACK_PLUGINS ?>bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
    <!-- Slimscroll -->
    <script src="<?php echo AWS_CDN_BACK_PLUGINS ?>slimScroll/jquery.slimscroll.min.js"></script>
    <!-- bootbox -->
    <script src="<?php echo AWS_CDN_BACK_PLUGINS ?>bootbox/bootbox.min.js"></script>
    <!-- FastClick -->
    <script src="<?php echo AWS_CDN_BACK_PLUGINS ?>fastclick/fastclick.js"></script>
    <script src="<?php echo AWS_CDN_BACK_PLUGINS ?>datatables/jquery.dataTables.min.js"></script>
    <script src="<?php echo AWS_CDN_BACK_PLUGINS ?>datatables/dataTables.bootstrap.min.js"></script>
    <script src="<?php echo AWS_CDN_BACK_PLUGINS ?>toastr/toastr.min.js"></script>
    <!-- AdminLTE App -->
    <script src="<?php echo AWS_CDN_BACK_DIST_JS ?>app.min.js"></script>
    <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
    <!-- <script src="<?php //echo AWS_CDN_BACK_DIST_JS ?>pages/dashboard.js"></script> -->
    <!-- AdminLTE for demo purposes -->
    <script src="<?php echo AWS_CDN_BACK_DIST_JS ?>demo.js"></script>
    <script src="<?php echo AWS_CDN_BACK_PLUGINS ?>light-gallery/js/lightgallery.js"></script>
    <script src="<?php echo base_url().APP_BACK_ASSETS;?>custom/js/admin_common.js" ></script>
    <!-- <script src="<?php echo $backend_asset;?>toaster/jquery.toaster.js"></script> -->
    <script src="https://cdn.ckeditor.com/4.9.2/standard/ckeditor.js"></script>

    <?php if(!empty($admin_scripts)) { load_admin_js($admin_scripts);}  //load required page scripts ?>
    <script type="text/javascript">
        
        $('#aniimated-idproof, #aniimated-biz').lightGallery({
            thumbnail: true,
            selector: 'a',
            download: false
        });
    </script>
</body>
</html>