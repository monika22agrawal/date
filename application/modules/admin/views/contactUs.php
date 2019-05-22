<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Contact us
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo base_url('admin/dashboard');?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li>Content</li>
            <li class="active">Contact us</li>
        </ol>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-info">
                    <div class="box-header">
                        </h3>
                        <!-- /. tools -->
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body pad">
                        <form id="updateAbout">
                            <textarea id="editor1" name="contents" rows="10" cols="80">
                            <?php if(!empty($content)){
                                echo $content->option_value;
                                }?>                       
                            </textarea>
                            <input type="hidden" value="contact_type" name="content">
                            <input class="btn btn-primary" type="submit" value='Add' style="background: #a51d29;color: white;">
                        </form>
                    </div>
                </div>
                <!-- /.box -->
            </div>
            <!-- /.col-->
        </div>
        <!-- ./row -->
    </section>
    <!-- /.content -->
</div>
<script>
    $(function () {
        // Replace the <textarea id="editor1"> with a CKEditor
        // instance, using default configuration.
        CKEDITOR.replace('editor1');
        //bootstrap WYSIHTML5 - text editor
        $(".textarea").wysihtml5();
    });
</script>