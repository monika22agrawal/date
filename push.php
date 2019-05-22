 <?php  
    define('API_ACCESS_KEY','AAAAw2tm2SA:APA91bHyNfeMWWthWwQ9AN8K21A_wqyHKpmGyUExnM8wbBsHXT6tZas8mE7Ur4ThmhgX33F_j1n2cRXcJ934bH_MNv5e0KOAY9uT-GFlwo7uaWb_DmHc_D0b3k8hbK2TEACcm1zbjU--');
    
    $msg = $notificationMsg;
    $message = array('title'=>"APOIM",'body'=> "Test for APOIM! Yes it is working!" ,'postId'=>1,'type'=> 'post');
    $ios_message = array( "title"=>"APOIM",
            "subtitle"=>"" ,"body"=>"Test for notifications again!","points"=>"0","businessId"=>1,"isFavourite"=> "no"
          );

    $reg_ids = array('dQ74_vq9okA:APA91bHOWTToGAtdlFY2mj4moga27mN7Og-krwUxH10tSS_r4R15SORE0OIh5qeDMtyX12id7vSLUxoV535LOE8uSQFNa8uEVBW0OGcGjCMi92I5m0uTXAh5vBXKdC0hwPo1qrF13HfJ');

    $fields = array('registration_ids'=>$reg_ids, 'data'=>$message, 'notification'=>$message);
    //echo '<pre>'; print_r($fields);
    /*$fields = array(
        'to' 	=> 'ee26e1837f2c6170569e253cc5b496eea01904b48e3c526e4b9181c3cefc1123', //firebase ID or device token
        'data' => array('message'=>'hello ios'),
        'notification'=> array('body' => 'Enter your message')
    );*/
    //$messageBody['aps'] = array('alert' => $message,'badge' => 1, 'sound' => 'default','mutable-content'=>1,'data'=> $attrs);

    //'data'=> array('title'=>"Elite",'message'=> "Test for elite! Is it working?" ,'postId'=>1,'type'=> 'post')   -- for andriod
    //'aps'=> array('alert' => $message,'badge' => 1, 'sound' => 'default','mutable-content'=>1,'data'=> array("attachment-url"=>""))   -- for ios
    $headers = array(
        'Authorization: key=' . API_ACCESS_KEY,
        'Content-Type: application/json'
    );

    //curl request
    $ch = curl_init();
    curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
    //curl_setopt( $ch,CURLOPT_URL, 'https://android.googleapis.com/gcm/send' );
    curl_setopt( $ch,CURLOPT_POST, true );
    curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
    curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
    curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
    curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
    $result = curl_exec($ch );
    curl_close( $ch );

    print_r($result);
    die('check');
 ?>
