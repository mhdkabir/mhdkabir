<?php
   
require APPPATH . 'libraries/REST_Controller.php';
     
class Observation extends REST_Controller {
   
    public function __construct() {
       parent::__construct();
      $this->load->model('Common_model','cm');
    }
   

    public function index_post()
     {
           $flag = $this->input->post('flag');
        
                switch ($flag) {
                  case "add_observation":

                    $section_id = $this->input->post('section_id');
                    $branch_id = $this->input->post('branch_id');
                    $class_id = $this->input->post('class_id');
                    $student_id = $this->input->post('student_id');
                    $notes = $this->input->post('notes');
                    $improvement = $this->input->post('improvement');
                    $observation = $this->input->post('observation');
                    $file_name = $this->cm->file_upload('image', 'assets/images/');
                    $time = $this->input->post('time');
                    if(empty($notes) || empty($observation)  || empty($time) || empty($improvement)){
                        $dataTosend = ['status'=>false, 'msg'=>'All field Required!!'];
                        echo json_encode($dataTosend); die();
                    }
                    $res = $this->cm->save('observation_tbl', ['section_id'=>$section_id,'class_id'=>$class_id,'student_id'=>$student_id,'notes'=>$notes, 'observation'=>$observation,'improvement'=>$improvement, 'image'=>$file_name, 'time'=>$time, 'branch_id'=>$branch_id]);
                    if($res){
                        //$this->cm->save('user_tbl',['branch_id'=>$branch_id]);
                        $dataTosend = ['status'=>true, 'msg'=>'observation add successfull'];
                        echo json_encode($dataTosend); die();
                    }else{
                        $dataTosend = ['status'=>false, 'msg'=>'record can not found'];
                        echo json_encode($dataTosend); die();
                    }
                      break;
                      
                case "view_observation":
                    //branch_id: 5, section_id: 12, class_id: 3, student_id: 64
                    $branch_id = $this->input->post('branch_id');
                    $section_id = $this->input->post('section_id');
                    $class_id = $this->input->post('class_id');
                    $student_id = $this->input->post('student_id');
                    
                    if(empty($branch_id) || empty($section_id)  || empty($class_id)  || empty($student_id)){
                        $dataTosend = ['status'=>false, 'msg'=>'All field required'];
                        echo json_encode($dataTosend); die();
                    }
                    // $sql = "SELECT `id`, `student_id`, `parent_id`, `fees_id`, `amount_paid`, `created_at`, `updated_at` FROM receipts where parent_id='$parent_id'";
                    // $res = $this->db->query($sql)->result();
                    $sql = "SELECT o.id,o.image , o.student_id, o.notes, o.improvement, o.observation, ut.name,
                    o.time FROM observation_tbl as o  join user_tbl as ut on ut.user_id = o.student_id where o.student_id='$student_id' and o.class_id='$class_id'";
                    $res = $this->db->query($sql)->result();
                    $job=array();
                  foreach($res as $result)
                  {
                         $test_arr=array();
                         $test_arr['observation_id']=$result->id;
                         $test_arr['student_id']=$result->student_id;
                         $test_arr['name']=$result->name;
					     $test_arr['image']=base_url('assets/images/').$result->image;
					     $test_arr['notes']=$result->notes;
                         $test_arr['id']=$result->id;
                         $test_arr['improvement']=$result->improvement;
                         $test_arr['observation']=$result->observation;
                         $test_arr['time']=$result->time;
                         $job[] = $test_arr; 
                  }
                    if($job){
                        $dataTosend = ['status'=>true, 'msg'=>'view obervation', 'body'=>$job];
                        echo json_encode($dataTosend); die();
                    }else{
                        $dataTosend = ['status'=>false, 'msg'=>'obervation not found'];
                        echo json_encode($dataTosend); die();
                    }
                
                case "delete_observation":
                      
                      $id = $this->input->post('observation_id');
                    
                      $sql = "DELETE FROM `observation_tbl` WHERE id='$id'";
                      $res = $this->db->query($sql);
                      if($res){
                          $dataTosend = ['status'=>true, 'msg'=>'observation deleted successfully!'];
                          echo json_encode($dataTosend); die();
                      }else{
                          $dataTosend = ['status'=>false, 'msg'=>'unable to delete observation'];
                          echo json_encode($dataTosend); die();
                      }
                break;
                
                case "update_observation":

                    $id = $this->input->post('observation_id');
                    $section_id = $this->input->post('section_id');
                    $branch_id = $this->input->post('branch_id');
                    $class_id = $this->input->post('class_id');
                    $student_id = $this->input->post('student_id');
                    $notes = $this->input->post('notes');
                    $improvement = $this->input->post('improvement');
                    $observation = $this->input->post('observation');
                    $file_name = $this->cm->file_upload('image', 'assets/images/');
                    $time = $this->input->post('time');
                    if(empty($notes) || empty($observation)  || empty($time) || empty($improvement)){
                        $dataTosend = ['status'=>false, 'msg'=>'All field Required!!'];
                        echo json_encode($dataTosend); die();
                    }
                    //$res = $this->cm->save('observation_tbl', ['section_id'=>$section_id,'class_id'=>$class_id,'student_id'=>$student_id,'notes'=>$notes, 'observation'=>$observation,'improvement'=>$improvement, 'image'=>$file_name, 'time'=>$time, 'branch_id'=>$branch_id]);
                    $whr = ['class_id'=>$class_id, 'section_id'=>$section_id, 'image'=>$file_name, 'branch_id'=>$branch_id, 'student_id'=>$student_id, 'notes'=>$notes, 'improvement'=>$improvement, 
                      'observation'=>$observation, 'time'=>$time];
                      $res = $this->cm->update('observation_tbl',['id'=>$id],$whr);
                    if($res){
                        //$this->cm->save('user_tbl',['branch_id'=>$branch_id]);
                        $dataTosend = ['status'=>true, 'msg'=>'observation update successfull'];
                        echo json_encode($dataTosend); die();
                    }else{
                        $dataTosend = ['status'=>false, 'msg'=>'observation can not found'];
                        echo json_encode($dataTosend); die();
                    }
                      break;
                      default:
                     $dataTosend = ['status'=>false,'msg'=>"Invalid Requst",'body'=>''];
                        echo json_encode($dataTosend); die(); 
                }
    } 
  	
}