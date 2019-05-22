<!-- Modal -->
<div class="modal fade" id="commonModal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">            
            <form class="form-horizontal" role="form" id="editFormAjax" method="post" action="<?php echo base_url('admin/interest/updateEducationData') ?>">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">Update Education</h4>
                </div>
                <input type="hidden" name="id" value="<?php echo $record->eduId;?>" />
                <div class="modal-body">                    
                    <div class="alert alert-danger" id="error-box" style="display: none"></div>
                    <div class="form-body">
                        <div class="row">
                            <div class="col-md-12" >
                                <div class="form-group">
                                    <label class="col-md-3 control-label">Education In English</label>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control" name="education" value="<?php echo $record->education;?>" id="education" placeholder="Education in english" required />
                                    </div>                                   
                                </div>
                                <div class="form-group">
                                    <label class="col-md-3 control-label">Education In Spanish</label>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control" name="eduInSpanish" value="<?php echo $record->eduInSpanish;?>" id="educationsp" placeholder="Education in spanish" required />
                                    </div>                                   
                                </div>  
                            </div>
                            <div class="space-22"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger btns-new dsaprve-btn" data-dismiss="modal">Close</button>
                    <button type="button" id="submit" class="btn btn-primary education btns-new apprve-btn" >Update</button>
                </div>
            </form>
        </div> <!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>

<script type="text/javascript">

    $('#education').keyup(function() {
        var $th = $(this);
        $th.val($th.val().replace(/(\s{2,})|[^a-zA-Z']/g, ' '));
        $th.val($th.val().replace(/^\s*/, ''));
    });

    $('#educationsp').keyup(function() {
        var $th = $(this);
        $th.val($th.val().replace(/(\s{2,})|[^a-zA-Z']/g, ' '));
        $th.val($th.val().replace(/^\s*/, ''));
    });
</script>