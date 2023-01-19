<?php
   
require APPPATH . 'libraries/REST_Controller.php';
     
class Chat extends REST_Controller {
   
    public function __construct() {
       parent::__construct();
      $this->load->model('Common_model','cm');
    }
   

    public function index_post()
     {
           $flag = $this->input->post('flag');
        
                switch ($flag) {
   
                  case "add_chat":
                    
                    $student_id = $this->input->post('student_id');
                    $message = $this->input->post('message');
                    $sender_type = $this->input->post('sender_type');
                    $date = date('y:m:d');
                    //$time = time('h:m:s');
                    
                    if(empty($student_id) || empty($message) || empty($sender_type)){
                        $dataTosend = ['status'=>false, 'msg'=>'All field Required!!'];
                    }
                    $res = $this->cm->save('chat_message_tbl',['student_id'=>$student_id, 'message'=>$message, 'sender_type'=>$sender_type]);
                        if($res){
                            $dataTosend = ['status'=>true, 'msg'=>'message add successfull'];
                            echo json_encode($dataTosend); die();
                        }else{
                            $dataTosend = ['status'=>false, 'msg'=>'please add message'];
                            echo json_encode('$dataTosend'); die();
                        }
                      break;
                      default:
                     $dataTosend = ['status'=>false,'msg'=>"Invalid Requst",'body'=>''];
                        echo json_encode($dataTosend); die(); 
                }
    } 
  	
}