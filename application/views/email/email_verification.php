<!DOCTYPE html>
<html>
<head>
	<title>Apoim</title>
	<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<?php 
    $frontend_assets =  base_url().'frontend_asset/';
?>
<body style="padding: 0; margin: 0; font-family: 'Open Sans', sans-serif;">
    <div class="Frame" style="width: 100%; max-width: 650px; box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box; background: #f2f2f2; text-align: center; padding: 20px 30px; margin: 0 auto;">
        <table style="background: #fff; border-top: 6px solid #a51d29;" width="100%" border="0" align="center" cellspacing="0" cellpadding="0">
            <tr>
                <td style="padding-top: 30px; box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;">
                    <div class="logo">
                        <img alt="Apoim" src="<?php echo AWS_CDN_FRONT_IMG; ?>logo-2.png">
                    </div>
                </td>
            </tr>
        </table>
        <div style="background: #fff; padding: 50px 0 30px; box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;">
            <table width="100%" border="0" align="center" cellspacing="0" cellpadding="0" style="padding-bottom: 25px;">
                <tr>
                    <td style="padding-left: 20px; padding-right: 10px; box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;">
                        <table width="100%" border="0" align="center" cellspacing="0" cellpadding="0">
                            <tr>
                                <td>
                                    <!-- <img style="width: 100%; max-width: 120px;" src="http://design.mindiii.com/gourav/emailTemplate/kinklink/lock.png"> -->
                                    <h2 style="margin: 0; font-size: 30px; color: #333;">Welcome to Apoim !</h2>
                                    <p style="margin-top: 30px;font-size: 16px;color: #828282;width: 100%;font-weight: 400; text-align: left;">Hello</p>
                                    <p style="margin-top: 20px;font-size: 16px;color: #828282;width: 100%;font-weight: 400; text-align: left;line-height: 25px;">We're excited to have you get started with us, here we sent you your email verification code, with this you can register into application.</p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            <table width="100%" border="0" align="center" cellspacing="0" cellpadding="0" style="padding-bottom: 25px;">
                <tr>
                    <td style="padding-left: 20px; padding-right: 10px; box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;">
                        <table width="100%" border="0" align="center" cellspacing="0" cellpadding="0">
                            <tr>
                                <td>
                                    <h2 style="margin: 0; font-size: 20px; color: #333;">Your Verification Code is</h2>
                                    <p style="margin: 15px auto;font-size: 16px;font-weight: 600;display: inline-block;background: #a51d29;padding: 10px 20px;border-radius: 50px;color: #fff;"><?php echo $code; ?></p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            <table width="100%" border="0" align="center" cellspacing="0" cellpadding="0">
                 <tr>
                    <td>
                        <p style="color: #828282; margin: 0;">For more information visit our website </p>
                        <a style="color: #a51d29; text-decoration: none; font-weight: 600;" href="#"><?php echo base_url('home');?></a>
                    </td>
                </tr>
            </table>
        </div>
        <div style="background: #232323;">
            <table width="100%" border="0" align="center" cellspacing="0" cellpadding="0">
                <tr>
                    <td>
                        <table width="100%" border="0" align="center" cellspacing="0" cellpadding="0">
                            <tr>
                                <td style="padding: 30px 20px; box-sizing: border-box; -moz-box-sizing: border-box; -webkit-box-sizing: border-box;">
                                    <div style="display: inline-block; width: 100%; margin-bottom: 10px;">
                                        <a style="margin: 0 3px;" href=""><img alt="Facebook" src="<?php echo AWS_CDN_FRONT_IMG; ?>facebook-bottom.png"></a>
                                        <a style="margin: 0 3px;" href=""><img alt="Twitter" src="<?php echo AWS_CDN_FRONT_IMG; ?>twitter-bottom.png"></a>
                                        <a style="margin: 0 3px;" href=""><img alt="GooglePlus" src="<?php echo AWS_CDN_FRONT_IMG; ?>googleplus-bottom.png"></a>
                                        <a style="margin: 0 3px;" href=""><img alt="Pinterest" src="<?php echo AWS_CDN_FRONT_IMG; ?>pinterest-bottom.png"></a>
                                    </div>
                                    <p style="color: #fff; margin: 0 0 5px 0; font-size: 13px;"> © <?php echo date('Y');?> <a style="color: #fff; text-decoration: none;" href="#">apoim</a>. All rights reserved.</p>
                                    <p style="color: #fff; margin: 0; font-size: 13px;"><a style="color: #fff;" href="">4433 W Sunset Blvd. Los Angeles. CA 90022 </a> / Phone:  <a style="color: #fff; text-decoration: none;" href="">(323) 677-0088</a>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>