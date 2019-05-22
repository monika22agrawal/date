<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <span id="message"></span>
        <h1>
            <?php echo $title = "Eductaion"."($education)"; ?>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo site_url('admin/dashboard');?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active"><a href="<?php echo site_url('admin/interest/educationList');?>">Eductaion</a></li>
        </ol>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <!-- /.box -->
                <div class="box">
                    <button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#myModal">Add Eductaion</button>
                    <!-- /.box-header -->
                    <div class="box-body" id="post_details_nav" data-post-type="">
                        <?php //if (!empty($list)): ?>
                        <table id="education_list" class="table table-bordered table-striped">
                            <thead>
                                <th>S.No.</th>
                                <th>Eductaion In English</th>
                                <th>Eductaion In Spanish</th>
                                <th>Action</th>
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
<div id="form-modal-box"></div>
<!-- Modal -->
<div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <form class="form-horizontal" role="form" id="myformedu" method="post" action="<?php echo base_url('admin/interest/addEducation') ?>">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">Add Education</h4>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger" id="error-box" style="display: none"></div>
                    <div class="form-body">
                        <div class="row">
                            <div class="col-md-12" >
                                <div class="form-group">
                                    <label class="col-md-3 control-label">Education In English</label>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control" name="education" id="educationeng" placeholder="Education in english" required />
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12" >
                                <div class="form-group">
                                    <label class="col-md-3 control-label">Education In Spanish</label>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control" name="eduInSpanish" id="educationsp" placeholder="Education in spanish" required />
                                    </div>
                                </div>
                            </div>
                            <div class="space-22"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger btns-new dsaprve-btn" data-dismiss="modal">Close</button>
                    <button type="button" id="submit" class="btn btn-primary education btns-new apprve-btn" >Add</button>
                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<script type="text/javascript">

    $('#education').keyup(function() {
        var $th = $(this);
        $th.val($th.val().replace(/(\s{2,})|[^a-zA-Z']/g, ' '));
        $th.val($th.val().replace(/^\s*/, ''));
    });
    
</script>