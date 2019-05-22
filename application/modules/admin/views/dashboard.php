<style type="text/css">
    .info-box-icon i{
        padding-top: 20px;
    }
</style>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Welcome Admin
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo site_url('admin'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Dashboard</li>
        </ol>
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="row">
            <!-- /.col -->
            <!-- /.col -->
            <!-- fix for small devices only -->
            <div class="clearfix visible-sm-block"></div>
            <!-- /.col -->
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <a href="<?php echo site_url('admin/users/userList'); ?>">
                        <span class="info-box-icon bg-yellow" style="height: 93px;"><i class="ion ion-android-person"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Users</span>
                            <span class="info-box-number"><?php echo $count;?></span>
                        </div>
                        <!-- /.info-box-content -->
                    </a>
                </div>
                <!-- /.info-box -->
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <a href="<?php echo site_url('admin/users/paymentList'); ?>">
                        <span class="info-box-icon bg-yellow" style="height: 93px;"><i class="fa fa-cc-visa"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Payments</span>
                            <span class="info-box-number"><?php echo $payment_count;?></span>
                        </div>
                        <!-- /.info-box-content -->
                    </a>
                </div>
                <!-- /.info-box -->
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <a href="<?php echo site_url('admin/users/eventList'); ?>">
                        <span class="info-box-icon bg-yellow" style="height: 93px;"><i class="fa fa-calendar"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Events</span>
                            <span class="info-box-number"><?php echo $event_count;?></span>
                        </div>
                        <!-- /.info-box-content -->
                    </a>
                </div>
                <!-- /.info-box -->
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <a href="<?php echo site_url('admin/users/appointmentList'); ?>">
                        <span class="info-box-icon bg-yellow" style="height: 93px;"><i class="fa fa-calendar-check-o"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Active Appoinments</span>
                            <span class="info-box-number"><?php echo $apoinment_count;?></span>
                        </div>
                        <!-- /.info-box-content -->
                    </a>
                </div>
                <!-- /.info-box -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
        <!-- /.row -->
        <!-- Main row -->
        <!-- /.row -->
    </section>
    <!-- /.content -->
</div>