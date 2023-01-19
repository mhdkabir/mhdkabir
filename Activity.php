<?php
   
require APPPATH . 'libraries/REST_Controller.php';
     
class Activity extends REST_Controller {
   
    public function __construct() {
       parent::__construct();
      $this->load->model('Common_model','cm');
    }
   

    public function index_post()
     {
           $flag = $this->input->post('flag');
        
                switch ($flag) {
                  case "view_activity":
                      
                      $sql = "select activity_id,title,description,date,form_image,branch_id,class_id from activity_tbl";
                      $res = $this->db->query($sql)->result();
                      foreach($res as $val){
                           $val->form_image = ($val->form_image)? base_url('assets/message_img/').$val->form_image :'';
                       }
                      if($res){
                          $dataTosend = ['status'=>true, 'msg'=>'view all activity data', 'body'=>$res];
                          echo json_encode($dataTosend); die();
                      }else{
                          $dataTosend = ['status'=>false, 'msg'=>'failed'];
                          echo json_encode($dataTosend); die();
                      }
                    
                      break;
                      case "view_activity_student":
                       $student_id = $this->input->post('student_id');
                      $sql = "SELECT a.title ,a.description ,a.start_data,a.end_data,a.start_time,a.form_image, l.`student_id`, l.`activity_log_id` ,l.`act_feild_id`
                              ,l.`activity_fild_values`,l.`created_at`, f.feild_name ,f.label_name,f.position,f.attr_class,f.attr_name,f.attr_value,f.status FROM `activity_logs` as l
                              LEFT JOIN activity_feild as f on f.id = l.`act_feild_id` LEFT JOIN activity_tbl as a 
                              on a.activity_id = f.activity_id  WHERE l.`student_id`='$student_id'";
                      $res = $this->db->query($sql)->result();

                      if($res){
                          $dataTosend = ['status'=>true, 'msg'=>'view Student activity data', 'body'=>$res];
                          echo json_encode($dataTosend); die();
                      }else{
                          $dataTosend = ['status'=>false, 'msg'=>'failed'];
                          echo json_encode($dataTosend); die();
                      }
                    
                      break;
                      case "view_activity_field":
                           $id = $this->input->post('activity_id');
                             $res = $this->cm->get_data('activity_feild',['activity_id'=>$id]);
                         if($res){
                          $dataTosend = ['status'=>true, 'msg'=>'view all activity data', 'body'=>$res];
                          echo json_encode($dataTosend); die();
                      }else{
                          $dataTosend = ['status'=>false, 'msg'=>'failed'];
                          echo json_encode($dataTosend); die();
                      }
                          
                      break;
                      default:
                     $dataTosend = ['status'=>false,'msg'=>"Invalid Requst",'body'=>''];
                        echo json_encode($dataTosend); die(); 
                }
    } 
  	
}