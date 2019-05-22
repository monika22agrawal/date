<?php 
    $frontend_assets =  base_url().'frontend_asset/';
?> 
<div class="wraper">
    <!--================Banner Area =================-->
    <section class="banner_area">
        <div class="container">
            <div class="banner_content">
                <h3 title="Subscribe"><img class="left_img" src="<?php echo AWS_CDN_FRONT_IMG;?>banner/t-left-img.png" alt=""><?php echo lang('subscribe'); ?><img class="right_img" src="<?php echo AWS_CDN_FRONT_IMG;?>banner/t-right-img.png" alt=""></h3>
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
                        <h4><?php echo lang('apoim_premium_ac'); ?></h4>
                        <p class="price mt-5 mb-15">
                            <?php echo lang('pay'); ?><span> $200 </span><?php echo lang('apoim_premium_monthly'); ?>
                        </p>
                        <div class="price_round">
                            <img src="<?php echo AWS_CDN_FRONT_IMG;?>premium.png" />
                        </div>
                        <p class="prce-pra mt-20"><?php echo lang('upgrade_premium_monthly'); ?></p>
                        <div class="form-group mt-20 mb-0">
                            <button type="button" class="btn form-control login_btn" onclick="openStripeModel(this)" data-ptype="3" data-title="Monthly Subscription"><?php echo lang('subscribe'); ?></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>