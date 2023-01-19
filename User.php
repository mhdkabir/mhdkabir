<?php
   
require APPPATH . 'libraries/REST_Controller.php';
     
class User extends REST_Controller {
   
    public function __construct() {
       parent::__construct();
       $this->load->model('Common_model','cm');
      
    }
   

    public function index_post()
     {
           $flag = $this->input->post('flag');
        
                switch ($flag) {
                  case "user_view_profile": 
                        
                        $user_id = $this->input->post('user_id');
                        $res = $this->cm->get_data('user_tbl',['user_id'=>$user_id]);
                        if($res) {       
                          $dataTosend = ['status'=>true, 'msg' => 'userprofile record','body'=>$res];
                          echo json_encode($dataTosend); die();
                        }else{
                          $dataTosend = ['status'=>false, 'msg' => 'failed','body'=>''];
                          echo json_encode($dataTosend);die();
                        }
                    break;

                  case "user_profile_update":

                        $name = $this->input->post('name');
                        $email = $this->input->post('email');
                        $address = $this->input->post('address');
                        $password = $this->input->post('password');
                        $mobile = $this->input->post('mobile');
                        $DOB = $this->input->post('DOB');
                        $user_id = $this->input->post('user_id');
                        if(empty($name) || empty($email) || empty($address) || empty($password) || empty($mobile) || empty($DOB)){
                          $dataTosend = ['status'=>false, 'msg'=>'All field Required!!'];
                          echo json_encode($dataTosend); die();
                          
                        }
                        $res = $this->cm->update('user_tbl',['user_id'=>$user_id],['name'=>$name, 'email'=>$email, 'address'=>$address, 'password'=>$password, 'mobile'=>$mobile, 'DOB'=>$DOB]);
                        if($res){
                            $dataTosend = ['status'=>true, 'msg'=>'userProfile update successfull', 'body'=>''];
                            echo json_encode($dataTosend); die();
                        }else{
                            $dataTosend = ['status'=>false, 'msg'=>'profile is not update', 'body'=>''];
                            echo json_encode($dataTosend); die();
                        }
                    break;
                
                  default:
                     $dataTosend = ['status'=>false,'msg'=>"Invalid Requst",'body'=>''];
                        echo json_encode($dataTosend); die(); 
                }
    } 
  	
}