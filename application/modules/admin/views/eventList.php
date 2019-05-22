<script src="https://maps.googleapis.com/maps/api/js?libraries=places&key=<?php echo GOOGLE_API_KEY;?>"></script>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <span id="message"></span>
        <h1>
            <?php echo $title = "Events"."($events)"; ?>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo site_url('admin/dashboard');?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Events</li>
        </ol>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <!-- /.box -->
                <div class="box">
                    <div class=" div-select col-md-2 "><!-- <label>Select Status</label> -->
                        <select name="payment" class="form-control" id="payment">
                            <option value ="">Select Payment Type</option>
                            <option value ="2" >Free</option>
                            <option value ="1" >Paid</option>
                        </select>
                    </div>
                    <div class=" div-select col-md-2 "><!-- <label>Select Status</label> -->
                        <select name="privacy" class="form-control" id="privacy">
                            <option value ="">Select Privacy</option>
                            <option value ="1" >Public</option>
                            <option value ="2" >Private</option>
                        </select>
                    </div>
                    <div class=" col-md-4"><!-- <label>Select Status</label> -->
                        <input class="form-control" id="address" value="" name="eventPlace" type="text" placeholder="Event Place">
                    </div>
                    <div class=" col-md-4" >
                        <!-- <div>
                            <input type="text" name="datefilter" placeholder="Event date" class="form-control" id="datepicker" data-date-format='yyyy-mm-dd'>
                        </div> -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group"> 
                                    <input class="form-control selector5 dte-rel" name="start" type="text" placeholder="Start Date" id="startdate" data-date-format='yyyy-mm-dd'>
                                    <button id="clearStartDate" class="clr-btn">clear</button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">      
                                    <input class="form-control selector6 dte-rel" name="end" type="text" placeholder="End Date" id="enddate" data-date-format='yyyy-mm-dd'>
                                    <button id="clearEndDate" class="clr-btn">clear</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body" id="post_details_nav" data-post-type="">
                        <?php //if (!empty($list)): ?>
                        <table id="all_event_list" class="table table-bordered table-striped">
                            <thead>
                                <th>S.No.</th>
                                <th>Event Name</th>
                                <th>Event Place</th>                               
                                <th>Event Organiser</th>
                                <th>Privacy</th>
                                <th>Payment Type</th>
                                <th>Status</th>
                                <th>Image</th>
                                <th style="width: 12%">Action</th>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                        <?php 
                            //else:
                                //echo '<h3>No record found</h3>';
                            //endif; ?>
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
