<?php
   
require APPPATH . 'libraries/REST_Controller.php';
     
class Class_room extends REST_Controller {
   
    public function __construct() {
       parent::__construct();
       $this->load->model('Common_model','cm');
      
    }
   

    public function index_post()
     {
           $flag = $this->input->post('flag');
        
                switch ($flag) {
                  case "viewClass": 
                    
                    $branch_id = $this->input->post('branch_id');
                    $res = $this->cm->get_data('class_tbl',['branch_id'=>$branch_id]);
                    if($res){
                      $dataTosend = ['status'=>true, 'msg'=>'all record', 'body'=>$res];
                      echo json_encode($dataTosend); die();
                    }else{
                          $dataTosend = ['status'=>false, 'msg' => 'failed'];
                          echo json_encode($dataTosend);die();
                    }
                    break;

                  case "addClass":

                   $branch_id = $this->input->post('branch_id');
                   $name = $this->input->post('name');
                   $age_group = $this->input->post('age_group');
                   $max_occupancy = $this->input->post('max_occupancy');
                   if(empty($branch_id) || empty($name) || empty($age_group) || empty($max_occupancy)){
                        $dataTosend = ['status'=>true, 'msg'=>'All field Required!!'];
                        echo json_encode($dataTosend); die();
                   }
                   $res = $this->cm->get_data('class_tbl',['branch_id'=>$branch_id, 'name'=>$name, 'age_group'=>$age_group, 'max_occupancy'=>$max_occupancy]);
                   if($res){
                     $dataTosend = ['status'=>true, 'msg'=>'allready record save'];
                     echo json_encode($dataTosend); die();
                   }else{
                    $rs = $this->cm->save('class_tbl',['branch_id'=>$branch_id, 'name'=>$name, 'age_group'=>$age_group, 'max_occupancy'=>$max_occupancy]);
                    if($rs){
                        $dataTosend = ['status'=>true, 'msg'=>'add class successfull'];
                        echo json_encode($dataTosend); die();
                    }else{
                      $dataTosend = ['status'=>false, 'msg'=>'record not added'];
                      echo json_encode($dataTosend); die();
                    }
                     
                   }
                    break;
                
                  default:
                     $dataTosend = ['status'=>false,'msg'=>"Invalid Requst"];
                        echo json_encode($dataTosend); die(); 
                }
    } 
  	
}