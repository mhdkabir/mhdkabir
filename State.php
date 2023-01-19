<?php
   
require APPPATH . 'libraries/REST_Controller.php';
     
class State extends REST_Controller {
   
    public function __construct() {
       parent::__construct();
      $this->load->model('Common_model','cm');
    }
   

    public function index_post()
     {
           $flag = $this->input->post('flag');
        
                switch ($flag) {
   
                  case "get_state":
                    
                    //$state_id = $this->input->post('state_id');
                    $res = $this->cm->get_data('state_tbl',[]);
                    if($res){
                        $dataTosend = ['status'=>true, 'msg'=>'view state', 'body'=>$res];
                        echo json_encode($dataTosend); die();
                    }else{
                        $dataTosend = ['status'=>false, 'msg'=>'error_msg','no record'];
                        echo json_encode($dataTosend); die();
                    }
                    

                      break;
                      default:
                     $dataTosend = ['status'=>false,'msg'=>"Invalid Requst",'body'=>''];
                        echo json_encode($dataTosend); die(); 
                }
    } 
  	
}