<?php 
class Appointment_model extends CI_Model {

    function getAppointmentList($appointById,$appointForId,$where,$offset,$limit,$listType = ''){

        $date = date('Y-m-d H:i:s');
        $defaultImg = AWS_CDN_USER_PLACEHOLDER_IMG;
        $imgUrl = AWS_CDN_USER_THUMB_IMG;

        $this->db->select('
            
            a.*, CONCAT(a.appointDate,SPACE(1),a.appointTime) as appointDateTime, u.fullName as ByName, uf.fullName as ForName, u.gender as ByGender, uf.gender as ForGender, IF(u.address IS NULL or u.address ="" or u.address ="0","NA",u.address) as ByAddress, IF(uf.address IS NULL or uf.address = "" or uf.address ="0","NA",uf.address) as ForAddress, u.latitude as ByLatitude, uf.latitude as ForLatitude, u.longitude as ByLongitude, uf.longitude as ForLongitude,
            (
                case
                    when( MIN(uImg.userImgId) IS NULL)
                        THEN "'.$defaultImg.'"
                    ELSE  
                        (SELECT 
                            (case 
                                when( image = "" OR  image IS NULL)
                                    THEN "'.$defaultImg.'"
                                when(  image !="" AND isSocial =1)
                                    THEN  image
                            ELSE
                                concat("'.$imgUrl.'", image)
                            END ) as image FROM '.USERS_IMAGE.' WHERE userImgId = MAX(uImg.userImgId))
                END 
            ) as byImage,
            (
                case
                    when( MIN(ufImg.userImgId) IS NULL)
                        THEN "'.$defaultImg.'"
                    ELSE  
                        (SELECT 
                            (case 
                                when( image = "" OR  image IS NULL)
                                    THEN "'.$defaultImg.'"
                                when(  image !="" AND isSocial =1)
                                    THEN  image
                            ELSE
                                concat("'.$imgUrl.'", image)
                            END ) as image FROM '.USERS_IMAGE.' WHERE userImgId = MAX(ufImg.userImgId))
                END 
            ) as forImage

        ');

        $this->db->from(APPOINTMENTS.' as a');

        $this->db->join(USERS.' as u','u.userId = a.appointById','left');
        $this->db->join(USERS.' as uf','uf.userId = a.appointForId','left');
        $this->db->join(USERS_IMAGE.' as uImg','uImg.user_id = a.appointById','left');
        $this->db->join(USERS_IMAGE.' as ufImg','ufImg.user_id = a.appointForId','left');
        
        if($where && $listType != 'all'){

            $this->db->where($where);
            $this->db->where(array('a.isDelete'=>0,'a.isFinish'=>0));

        }elseif($listType == 'all'){

            $this->db->group_start();
                $this->db->where(array('a.appointById'    =>$this->authData->userId));
                $this->db->or_where(array('a.appointForId'=>$this->authData->userId));
            $this->db->group_end();

        }elseif($listType == 'finished'){
           
            $this->db->group_start();
                $this->db->where(array('appointById'=>$this->authData->userId));
                $this->db->or_where(array('appointForId'=>$this->authData->userId));
            $this->db->group_end();
            $this->db->where(array('a.isFinish'=>1));

        }else{

            $this->db->where($where);
        } 

        if($listType == 'all' || $listType == 'received'){

            $this->db->where(array('a.appointmentStatus !='=> 5));
        }      
        
        $this->db->where(array('CONCAT(a.appointDate,SPACE(1),a.appointTime) > '=>$date));

        $this->db->limit($limit, $offset);

        $this->db->group_by('appId');
        $this->db->group_by('uImg.user_id');
        $this->db->group_by('ufImg.user_id');

        $this->db->order_by('appId','DESC');

        $query = $this->db->get();
        $result = $query->result();

        // for notification isRead 1
        $notType = array('create_appointment','delete_appointment');

        $this->db->where('notificationBy',$this->authData->userId);
        $this->db->or_where('notificationFor',$this->authData->userId);
        $this->db->where_in('notificationType',$notType);
        $this->db->update(NOTIFICATIONS,array('isRead'=>1));

        return $result;
    }

    // to get customer id from users table 
    function getStripeCustomerId(){

        $getCusId = $this->common_model->getsingle(USERS,array('userId'=>$this->authData->userId));
        if($getCusId){
            return $getCusId->stripeCustomerId;
        }else{
            return FALSE;
        }

    } // End of function

    // to save customer id in db
    function saveCustomerId($customer_id){

        $where = array('userId'=>$this->authData->userId);
        $isUpdated = $this->common_model->updateFields(USERS, array('stripeCustomerId'=>$customer_id),$where);

        if($isUpdated){
            return TRUE;
        }

    } // End of function
}

       