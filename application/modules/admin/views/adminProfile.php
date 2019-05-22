<style type="text/css">
    .detail{
        padding-left: 18px;
    }
    .profile-user-img {
        width: 45%;
        height: 120px;
    }
</style>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Admin Profile
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo site_url('admin'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="active">Admin profile</li>
        </ol>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-3">
                <div class="box box-primary">
                    <div class="box-body box-profile">
                        <?php 
                            $imgUrl = AWS_CDN_USER_PLACEHOLDER_IMG;
                            if(!empty($admin->profileImage)){
                                $imgUrl = AWS_CDN_USER_IMG_PATH.$admin->profileImage;
                            } ?>
                        <img id="blah" class="profile-user-img img-responsive img-circle image" src="<?php echo $imgUrl; ?>" alt="User profile picture" width="128px" height="128px">
                        <h3 class="profile-username text-center"><?php echo ucfirst($admin->name); ?></h3>
                        <!-- <p class="text-muted text-center">Software Engineer</p> -->
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
                <!-- About Me Box -->
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <center>
                            <h3 class="box-title">About Me</h3>
                        </center>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <center>
                            <strong><i class="fa fa-envelope margin-r-5"></i> Email</strong>
                            <p class="text-muted detail"><?php echo $admin->email; ?></p>
                        </center>
                        <hr>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
            <!-- /.col -->
            <div class="col-md-9">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <!-- <li class="active"><a href="#activity" data-toggle="tab">Activity</a></li>
                            <li><a href="#timeline" data-toggle="tab">Timeline</a></li> -->
                        <li class="active"><a href="#settings" data-toggle="tab">Profile</a></li>
                        <li><a href="#changePassword" data-toggle="tab">Change Password</a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="active tab-pane" id="settings">
                            <form class="form-horizontal" method="post" action="<?php echo base_url('admin/updateProfile') ?>">
                                <div class="form-group">
                                    <label for="inputName" class="col-sm-2 control-label" style="font-size:14px; ">Name</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" name="name" id="inputName" placeholder="Name" value="<?php echo $admin->name; ?>" style="position: relative; top:-11px;">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputImg" class="col-sm-2 control-label" style="font-size:14px; ">Image</label>
                                    <div class="col-sm-10 ">
                                        <div class="form-control" style="cursor: pointer;">
                                            <a id="inputImage"><label  for="upload-photo" id="inputImage">Browse</label></a>
                                        </div>
                                        <input type="file" name="image" id="upload-photo" style="cursor: pointer;" /></br>
                                    </div>
                                    <div class="ceo_file_error file_error text-danger" style="margin-left: 163px;"></div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-offset-2 col-sm-10">
                                        <button type="button" class="btn btn-danger update_admin_profile">Update</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- /.tab-pane -->
                        <div class="tab-pane" id="changePassword">
                            <form class="form-horizontal" method="POST" action="<?php echo base_url('admin/changePassword') ?>">
                                <div class="form-group">
                                    <label for="inputCpass" class="col-sm-3 control-label" style="font-size:14px; ">Current Password</label>
                                    <div class="col-sm-9">
                                        <input type="password" class="form-control" name="password" id="inputCpass" style="position: relative; top:-11px;" >
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputNewPass" class="col-sm-3 control-label" style="font-size:14px; ">New Password</label>
                                    <div class="col-sm-9">
                                        <input type="password" class="form-control" name="npassword" id="inputNewPass" style="position: relative; top:-11px;">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputRPass" class="col-sm-3 control-label" style="font-size:14px; ">Retype New Password</label>
                                    <div class="col-sm-9">
                                        <input type="password" class="form-control" name="rnpassword" id="inputRPass" style="position: relative; top:-11px;">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-offset-2 col-sm-10">
                                        <button type="button" class="btn btn-danger change_password">Update</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- /.tab-pane -->
                    </div>
                    <!-- /.tab-content -->
                </div>
                <!-- /.nav-tabs-custom -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </section>
    <!-- /.content -->
</div>