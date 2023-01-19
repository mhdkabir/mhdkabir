<?php
   
require APPPATH . 'libraries/REST_Controller.php';
     
class Joining_date extends REST_Controller {
   
    public function __construct() {
       parent::__construct();
      $this->load->model('Common_model','cm');
    }
   

    public function index_post()
     {
           $flag = $this->input->post('flag');
        
                switch ($flag) {
   
                  case "join_date":
                    
                    $hire_date = $this->input->post('hire_date');
                    $notes     = $this->input->post('notes');
                    $user_id = $this->input->post('user_id');
                    if(empty($hire_date) || empty($notes)){
                        $dataTosend = ['status'=>false, 'msg'=>'All field Required!!'];
                        echo json_encode($dataTosend); die();
                    }
                    $whr = ['hire_date'=>$hire_date, 'notes'=>$notes];
                    $res = $this->cm->update('staff_tbl',['user_id'=>$user_id],$whr);
                    if($res){
                        $dataTosend = ['status'=>true, 'msg'=>'joining successfull'];
                        echo json_encode($dataTosend); die();
                    }else{
                        $dataTosend = ['status'=>false,'msg'=>'faield'];
                        echo json_encode($dataTosend); die();
                    }
                    

                      break;
                      default:
                     $dataTosend = ['status'=>false,'msg'=>"Invalid Requst",'body'=>''];
                        echo json_encode($dataTosend); die(); 
                }
    } 
  	
}