<?php
   
require APPPATH . 'libraries/REST_Controller.php';
     
class School_profile extends REST_Controller {
   
    public function __construct() {
       parent::__construct();
      $this->load->model('Common_model','cm');
      $this->load->model('School_profile_model','sm');
    }
   

    public function index_post()
     {
           $flag = $this->input->post('flag');
        
                switch ($flag) {
                  case "school_profile":
                $org_id = $this->input->post('org_id');
                $admin_id = $this->input->post('admin_id');
                $sql = "select org_id,org_name,address,mobile,state,zip_code,city,school_capacity, admin_id from organisation where org_id='$admin_id'";
                //echo $sql;
                $res = $this->db->query($sql)->result();
                    //print_r($res); die;
                    if($res){
                        $dataTosend = ['status'=>true, 'msg'=>'view school profile', 'body'=>$res];
                        echo json_encode($dataTosend); die();
                    }else{
                        $dataTosend = ['status'=>false, 'msg'=>'No data found!..', 'body'=>''];
                        echo json_encode($dataTosend); die();
                    }
                   
                      break;

                      case "get_city":
                        //$state_id = $this->input->post('state_id');
                        $RES =  $this->cm->get_data('city_tbl',[]);
     
                        if($RES){
                                $dataTosend = ['status'=>true, 'msg' =>'Success','body'=> $RES];
                                echo json_encode($dataTosend); die();
                            }else{
                                $dataTosend = ['status'=>false, 'msg' => 'No data found!..','body'=>''];
                                echo json_encode($dataTosend);die();
                            }

                        break;

                        case "update_school_profile":
                            $org_name = $this->input->post('org_name');
                            $address = $this->input->post('address');
                            $mobile = $this->input->post('mobile');
                            $state = $this->input->post('state');
                            $city = $this->input->post('city');
                            $school_capacity = $this->input->post('school_capacity');
                            $zip_code = $this->input->post('zip_code');
                            $org_id = $this->input->post('org_id');
                            if(empty($org_name) || empty($address) || empty($mobile) || empty($state) || empty($city) || empty($school_capacity) || empty($zip_code)){
                                $dataTosend = ['status'=>true, 'msg'=>'All field Required!!'];
                                echo json_encode($dataTosend); die();
                            }
                            
                            $whr = ['org_name'=>$org_name,'address'=>$address,'mobile'=>$mobile,
                                 'state'=>$state,'city'=>$city,'school_capacity'=>$school_capacity,'zip_code'=>$zip_code] ;
                                 $res = $this->cm->update('organisation',['org_id'=>$org_id],$whr);
                                 //echo $res;
                                 if($res){
                                     //$this->cm->update('user_tbl',['state'=>$state,'city'=>$city],['org_id'=>$res]);
                                     $dataTosend = ['status'=>true, 'msg'=>'school profile successfully', 'body'=>''];
                                     echo json_encode($dataTosend); die();
                                 }else{
                                     $dataTosend = ['status'=>true, 'msg'=>'something went wrong please try again', 'body'=>''];
                                     echo json_encode($dataTosend); die();
                                 }
    
                            break;
                      default:
                     $dataTosend = ['status'=>false,'msg'=>"Invalid Requst",'body'=>''];
                        echo json_encode($dataTosend); die(); 
                }
    } 
  	
}