<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <span id="message"></span>
        <h1>
            <?php echo $title = "Appointment"."($appoinment)"; ?>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo site_url('admin/dashboard');?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Events</li>
        </ol>
    </section>
    <!-- Main content -->
    <section class="content appoin">
        <div class="row">
            <div class="col-xs-12">
                <!-- /.box -->
                <div class="box">
                    <div class=" div-select col-md-3 "><!-- <label>Select Status</label> -->
                        <select name="appointmentStatus" class="form-control" id="status">
                            <option value ="">Select Status</option>
                            <option value ="new_app" >New Appointment Request</option>
                            <option value ="waiting" >Waiting for Approval</option>
                            <option value ="payment_pen" >Payment Pending</option>
                            <option value ="app_con" >Appointment Confirmed</option>
                            <option value ="counter_rej" >Counter Rejected</option>
                            <option value ="app_rej">Request Rejected</option>
                            <option value ="isFinish">Finished Appointment</option>
                            <option value ="req_cncl">Request Cancelled</option>
                        </select>
                    </div>
                    <div class="col-md-3 " >
                        <div class="appintmnt-list">
                            <input type="text" name="date" placeholder="Appointment date" class="form-control dte-rel" id="datepicker" data-date-format='yyyy-mm-dd'>
                            <button id="clearAppDate" class="clr-btn">clear</button>
                        </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body" id="post_details_nav" data-post-type="">

                        <table id="appointment_list" class="table table-bordered table-striped">
                            <thead>
                                <th>S.No.</th>
                                <th>Appointment By</th>
                                <th>Appointment Date</th>
                                <th>Appointment Place</th>
                                <th>Appointment Status</th>
                                <th style="width: 12%">Action</th>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->
<div id="form-modal-box"></div>