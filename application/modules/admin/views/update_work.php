<!-- Modal -->
<div class="modal fade" id="commonModal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">            
            <form class="form-horizontal" role="form" id="editFormAjax" method="post" action="<?php echo base_url('admin/interest/updateWorkData') ?>">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">Update Work</h4>
                </div>
                <input type="hidden" name="id" value="<?php echo $record->workId;?>" />
                <div class="modal-body">                    
                    <div class="alert alert-danger" id="error-box" style="display: none"></div>
                    <div class="form-body">
                        <div class="row">
                            <div class="col-md-12" >
                                <div class="form-group">
                                    <label class="col-md-3 control-label">Work In English</label>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control" name="work" value="<?php echo $record->name;?>" id="work" placeholder="Work in english" required />
                                    </div>                                   
                                </div>
                                <div class="form-group">
                                    <label class="col-md-3 control-label">Work In Spanish</label>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control" name="nameInSpanish" value="<?php echo $record->nameInSpanish;?>" id="worksp" placeholder="Work in spanish" required />
                                    </div>                                   
                                </div>  
                            </div>
                            <div class="space-22"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger btns-new dsaprve-btn" data-dismiss="modal">Close</button>
                    <button type="button" id="submit" class="btn btn-primary work btns-new apprve-btn" >Update</button>
                </div>
            </form>
        </div> <!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>

<script type="text/javascript">

    $('#work').keyup(function() {
        var $th = $(this);
        $th.val($th.val().replace(/(\s{2,})|[^a-zA-Z']/g, ' '));
        $th.val($th.val().replace(/^\s*/, ''));
    });

    $('#worksp').keyup(function() {
        var $th = $(this);
        $th.val($th.val().replace(/(\s{2,})|[^a-zA-Z']/g, ' '));
        $th.val($th.val().replace(/^\s*/, ''));
    });
</script>