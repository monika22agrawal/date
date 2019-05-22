<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            About us
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo base_url('admin/dashboard');?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li>Content</li>
            <li class="active">About us</li>
        </ol>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            
            <div class="col-md-12">

                <div class="box box-info">
                    
                    <div class=" div-select col-md-3 "><!-- <label>Select Status</label> -->
                        <select name="appointmentStatus" class="form-control" id="langType">
                            <option value ="english" >English</option>
                            <option value ="spanish" >Spanish</option>                           
                        </select>
                    </div>
                   
                    <div class="clearfix"></div>
                    <!-- /.box-header -->
                    <div class="box-body pad">
                        <form id="privateEditFormAjax" method="POST" action="<?php echo base_url('admin/option/update_about_page'); ?>">
                            <div id="first">
                                <textarea id="editor1" name="contentsEng" rows="10" cols="80">
                                    <?php 
                                    if(!empty($contentEng->option_value)){
                                        echo $contentEng->option_value;
                                    }
                                    ?>
                                </textarea>
                            </div>
                            <div id="second" style="display: none;">
                                <textarea id="editor2" name="contentsSp" rows="10" cols="80" >
                                    <?php 
                                    if(!empty($contentSp->option_value)){
                                        echo $contentSp->option_value;
                                    }
                                    ?>
                                </textarea>
                            </div>
                            <input type="hidden" value="about_page" name="content">
                            <input type="hidden" value="english" name="lang_type" id="lang_type">
                            <input class="btn btn-primary pull-right" type="submit" value='Add' style="background: #a51d29;color: white;">
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
        CKEDITOR.replace('editor2');
        //bootstrap WYSIHTML5 - text editor
        $(".textarea").wysihtml5();
    });

    $("select").change(function(){

    var lang = $('#langType').val();
    $('#lang_type').val(lang);

    switch (lang){

        case 'english' :  
            $('#first').show();
            $('#second').hide();
            break;

        case 'spanish' :  
            $('#second').show();
            $('#first').hide();
            break;

        default :  
            $('#first').show();
            $('#second').hide();
    }
});
</script>