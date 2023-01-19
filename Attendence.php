<?php
   
require APPPATH . 'libraries/REST_Controller.php';
     
class Attendence extends REST_Controller {
   
    public function __construct() {
       parent::__construct();
      $this->load->model('Common_model','cm');
    }
   

    public function index_post()
     {
           $flag = $this->input->post('flag');
        
                switch ($flag) {
                  case "view_stu_attendence":
                            $branch_id = $this->input->post('branch_id');
                            $class_id = $this->input->post('class_id');
                            $section_id = $this->input->post('section_id');
                            $sql = "SELECT a.`check_in`,a. `type`, a.`check_out` , a.`user_id`, s.class_id,s.branch_id,s.section_id 
                            ,u.name , u.mobile ,u.address FROM `attendance_user` as a LEFT JOIN student_tbl as s on 
                            s.user_id = a.`user_id` LEFT JOIN user_tbl as u on u.user_id = s.user_id   
                            WHERE s.branch_id = '$branch_id' and s.class_id = '$class_id' and s.section_id='$section_id'";
                            //  $sql = "select id,title,description,fees,room,type,date,ftime,totime from event WHERE event.date LIKE '%".$date."%'";
                          $res = $this->db->query($sql)->result();
                        //   die;
                         if($res){
                          $dataTosend = ['status'=>true, 'msg'=>'view all activity data', 'body'=>$res];
                          echo json_encode($dataTosend); die();
                      }else{
                          $dataTosend = ['status'=>false, 'msg'=>'No Attendance Found!'];
                          echo json_encode($dataTosend); die();
                      }
                 
                   
                      break;

                      case "view_sta_attendence":
                            $branch_id = $this->input->post('branch_id');
                            $sql = "SELECT a.`check_in` , a.`check_out` ,  a.staff_id , u.name , u.mobile ,u.address FROM attendance_staff as a  LEFT JOIN user_tbl as u on u.user_id = a.staff_id  WHERE u.branch_id = '$branch_id'";
                            //  $sql = "select id,title,description,fees,room,type,date,ftime,totime from event WHERE event.date LIKE '%".$date."%'";
                          $res = $this->db->query($sql)->result();
                        //   die;
                         if($res){
                          $dataTosend = ['status'=>true, 'msg'=>'view all activity data', 'body'=>$res];
                          echo json_encode($dataTosend); die();
                      }else{
                          $dataTosend = ['status'=>false, 'msg'=>'No Attendance Found!'];
                          echo json_encode($dataTosend); die();
                      }
                 

                        break;
                      default:
                     $dataTosend = ['status'=>false,'msg'=>"Invalid Requst",'body'=>''];
                        echo json_encode($dataTosend); die(); 
                }
    } 
  	
}