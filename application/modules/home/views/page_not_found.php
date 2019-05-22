<?php 
    $frontend_assets =  base_url().'frontend_asset/';
    
?>
<div class="wraper">
    <!--================Banner Area =================-->
    <section class="banner_area">
        <div class="container">
            <div class="banner_content">
                <h3 title="Subscribe"><img class="left_img" src="<?php echo AWS_CDN_FRONT_IMG;?>banner/t-left-img.png" alt="">Not Found<img class="right_img" src="<?php echo AWS_CDN_FRONT_IMG;?>banner/t-right-img.png" alt=""></h3>
            </div>
        </div>
    </section>
    <!--================End Banner Area =================-->

    <!--================Contact From Area =================-->
    <section class="contact_form_area">
        <div class="container">
            <div class="">
                <div class="price_list">
                    <div class="price_box active-price non-brdr">
                        <h4>404 Page not found !</h4>
                   
                        <div class="price_round">
                            <img src="<?php echo AWS_CDN_FRONT_IMG; ?>404.svg" />
                        </div>
                        <p class="prce-pra mt-20">You requested the page that is no longer there.</p>
                        <div class="form-group mt-20 mb-0">
                        	<a class="btn form-control login_btn" href="<?php echo base_url('home');?>">Back To Home</a>
                            <!-- <button type="button" class="btn form-control login_btn" onclick="openStripeModel(this)" data-ptype="3" data-title="Monthly Subscription">Subscribe</button> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
