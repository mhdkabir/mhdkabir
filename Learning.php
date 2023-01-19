<?php
   
require APPPATH . 'libraries/REST_Controller.php';
     
class Learning extends REST_Controller {
   
    public function __construct() {
       parent::__construct();
      $this->load->model('Common_model','cm');
    }
   

    public function index_post()
     {
           $flag = $this->input->post('flag');
        
                switch ($flag) {
                  case "view_lesson_plan":
                        $class_id = $this->input->post('class_id');
                        $week_no = $this->input->post('week_no');
                        $where= array();
                        $class_id?$where['class_id'] =$class_id :'';
                        $week_no?$where['week_no'] =$week_no :'';
                        // $where = array('class_id'=>$class_id,'week_no'=>$week_no);    
                        $res = $this->cm->get_data('lesson_plan',$where);
                       
                      if($res){
                          $dataTosend = ['status'=>true, 'msg'=>'Lesson Plan Details', 'body'=>$res];
                          echo json_encode($dataTosend); die();
                      }else{
                          $dataTosend = ['status'=>false, 'msg'=>'No Data Found!..'];
                          echo json_encode($dataTosend); die();
                      }
                      break;
                       case "get_subject":
                        $class_id = $this->input->post('class_id');
                        $branch_id = $this->input->post('branch_id');
                        $res = $this->cm->get_data('subject_tbl',['class_id'=>$class_id,'branch_id'=>$branch_id]);
                       
                      if($res){
                          $dataTosend = ['status'=>true, 'msg'=>'Lesson Plan Details', 'body'=>$res];
                          echo json_encode($dataTosend); die();
                      }else{
                          $dataTosend = ['status'=>false, 'msg'=>'failed'];
                          echo json_encode($dataTosend); die();
                      }
                      break;
                 
                      case "view_lesson_plan_details_by_week":
                            $week_no = $this->input->post('week_no');

                          $sql = " SELECT * FROM plan_details as d LEFT JOIN  `lesson_plan` as p  on d.plan_id = p.`plan_id` LEFT JOIN
                            lesson as l on l.lesson_id = d.lesson_id WHERE  p.`week_no` = '$week_no'";
                       $res = $this->db->query($sql)->result();
                       if($res){
                          $dataTosend = ['status'=>true, 'msg'=>'view all activity data', 'body'=>$res];
                          echo json_encode($dataTosend); die();
                      }else{
                          $dataTosend = ['status'=>false, 'msg'=>'failed'];
                          echo json_encode($dataTosend); die();
                      }
                          
                      break;
                      case "view_lesson_plan_details":
                           $plan_id = $this->input->post('plan_id');

                          $sql = " SELECT * FROM plan_details as d LEFT JOIN  `lesson_plan` as p  on d.plan_id = p.`plan_id` LEFT JOIN
                            lesson as l on l.lesson_id = d.lesson_id WHERE  p.`plan_id` = '$plan_id'";
                       $res = $this->db->query($sql)->result();
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