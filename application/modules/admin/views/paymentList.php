<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Payment List(<?php echo $payment;?>)
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo base_url();?>admin/dashboard"><i class="fa fa-dashboard"></i>Dashboard</a></li>
            <li>Payment</li>
        </ol>
    </section>
    <!-- Main content -->
    <section class="content usr-lst-block">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="pull-right col-md-3 noMargin ">                   
                    </div>
                    <div class="pull-right div-select col-md-3 noMargin">  
                    </div>
                    <div class="box-body">
                        <table id="Payment_list" value ="<?php echo $this->uri->segment(4);?>" class="table table-bordered table-striped" width="100%">
                            <thead>
                                <tr>
                                    <th>S.No.</th>
                                    <th>Name</th>
                                    <th>Transaction id</th>
                                    <th>Amount</th>
                                    <th>Payment Status</th>
                                    <th>Payment Type</th>
                                    <th>Action</th>
                            </thead>
                            <tfoot>
                            </tfoot>
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
<div id="form-modal-box"></div>