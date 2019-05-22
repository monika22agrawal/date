
    function show_loader(){
        $('#tl_admin_loader').show();
    }

    function hide_loader(){
        $('#tl_admin_loader').hide();
    }

    var off=0;
    var lim = 3;
    invitedMembers();

    function invitedMembers(){

        var base_url = $('#tl_admin_main_body').attr('data-base-url');
        var userId = $('#userId').val(); 
        var eventId = $('#eventId').val(); 
        

        $.ajax({
            url: base_url+"admin/users/invitedMemberList",
            type: "POST",
            data:{userId:userId,eventId:eventId,offset:off,limit:lim},              
            cache: false,   
            beforeSend: function() {
            
                show_loader()
            },                          
            success: function(data){ 
                
                hide_loader()
                if(off==0){
                   
                    $('#inviteMember').html(data);

                }else{
                    
                    $("#memberData").append(data);
                }

                var totalCounts = $('#totalCountss').val(); 
                var resultCounts = $('div[id=iMember]').length; 
                if(totalCounts>resultCounts){

                    $('div#loadMember').show();

                }else{

                    $('div#loadMember').hide();
                    
                }   
                off += lim;
            }
        });
    }

    $(document).on('click',"#btnMem", function(event){ 

        var totalCounts = $('#totalCountss').val(); 
        var resultCounts = $('div[id=iMember]').length;
        if(totalCounts>resultCounts){

            $('#btnMem').show();

        }else{

            $('#btnMem').hide('fast');
            
        }  
        invitedMembers();
    });


  