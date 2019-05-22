    function show_loader(){
        $('#tl_admin_loader').show();
    }

    function hide_loader(){
        $('#tl_admin_loader').hide();
    }

    var joinOffset=0;
    var joinLimit = 3;

    joinedMember();

    function joinedMember(){

        var base_url = $('#tl_admin_main_body').attr('data-base-url');
        var userId = $('#userId').val(); 
        var eventId = $('#eventId').val(); 
        var eventOrgId = $('#eventOrgId').val(); 
        var type  = 'myEvent';
        
        $.ajax({
            url: base_url+"admin/users/joinedMemberList",
            type: "POST",
            data:{userId:userId,eventId:eventId,type:type,offset:joinOffset,limit:joinLimit,eventOrgId:eventOrgId},              
            cache: false,   
            beforeSend: function() {
            
                show_loader()
            },                          
            success: function(data){ 
                
                hide_loader()
                if(joinOffset==0){
                   
                    $('#joinedMember').html(data);

                }else{
                    
                    $("#moreData").append(data);
                }

                var totalCount = $('#totalCount').val(); 
                var resultCount = $('div[id=jMember]').length; 
                if(totalCount>resultCount){

                    $('div#loadMore').show();

                }else{

                    $('div#loadMore').hide();
                    
                }   
                joinOffset += joinLimit;

            }
        });
    }

    $(document).on('click',"#btnLoad", function(event){ 

        var totalCount = $('#totalCount').val(); 
        var resultCount = $('div[id=jMember]').length;
        if(totalCount>resultCount){

            $('#btnLoad').show();

        }else{

            $('#btnLoad').hide('fast');
            
        }  
        joinedMember();
    });


  