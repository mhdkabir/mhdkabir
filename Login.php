<?php
   
require APPPATH . 'libraries/REST_Controller.php';
     
class Login extends REST_Controller {
   
    public function __construct() {
       parent::__construct();
      $this->load->model('Common_model','cm');
    }
   

    public function index_post()
     {
           $flag = $this->input->post('flag');
        
                switch ($flag) {
   
                  case "login":
                    
                    $email = $this->input->post('email');
                    $password = $this->input->post('password');

                    if(empty($email) || empty($password)){
                      $dataTosend = array("response"=>array("status" => "false", "msg" => "Enter all parameters!"));	
                      echo json_encode($dataTosend); die();
                    }
                    $res = $this->cm->get_data('user_tbl',['email'=>$email, 'password'=>$password]);
                    // print_r($res);die;
                    if($res){
                         $data = array();
                          foreach($res as $record){
                              $data['user_id'] =  $record->user_id;
                              $data['name']    =  $record->name;
                              $data['email']   =  $record->email;
                              $data['mobile']  =  $record->mobile;
                              $data['image']  =  $record->image;
                              $data['branch_id']  =  $record->branch_id;
                              $data['org_id']  =  $record->org_id;
                              $data['roll']  =  $record->roll;

                          }
                          $dataTosend = ['status'=>true, 'msg' => 'Login successfull','body'=>$data];
                          echo json_encode($dataTosend); die();
                    }else{
                      $res = $this->cm->get_data('user_tbl',['email'=>$email]);
                      if($res){
                        $dataTosend = ['status'=>false, 'msg' => 'password not match'];
                        echo json_encode($dataTosend); die();
                      }else{
                        $dataTosend = ['status'=>false, 'msg' => 'Please valid email'];
                        echo json_encode($dataTosend); die();
                      }
                      $dataTosend = array("response"=>array("status" => "false", "msg" => "Invalid Requst"));
                    }

                      break;
                       case "change_password":
                    
                    $user_id = $this->input->post('user_id');
                    $password = $this->input->post('password');
                    $new_password = $this->input->post('new_password');

                    if(empty($user_id) || empty($password)){
                      $dataTosend = array("response"=>array("status" => "false", "msg" => "Enter all parameters!"));	
                      echo json_encode($dataTosend); die();
                    }
                    $res = $this->cm->get_data('user_tbl',['user_id'=>$user_id, 'password'=>$password]);
                    // print_r($res);die;
                    if($res){
                        
                     $res2 = $this->cm->update('user_tbl',['user_id'=>$user_id],['password'=>$new_password]);
                      if($res2){
                        $dataTosend = ['status'=>false, 'msg' => 'password Changed'];
                        echo json_encode($dataTosend); die();
                      }else{
                        $dataTosend = ['status'=>false, 'msg' => 'server error'];
                        echo json_encode($dataTosend); die();
                      }
                        }else{
                            $dataTosend = ['status'=>false, 'msg' => 'password not match'];
                            echo json_encode($dataTosend); die();
                            }

                      break;
                      default:
                     $dataTosend = ['status'=>false,'msg'=>"Invalid Requst",'body'=>''];
                        echo json_encode($dataTosend); die(); 
                }
    } 
  	
}