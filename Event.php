<?php
   
require APPPATH . 'libraries/REST_Controller.php';
     
class Event extends REST_Controller {
   
    public function __construct() {
       parent::__construct();
      $this->load->model('Common_model','cm');
      $this->load->model('Event_model','em');
    }
   

    public function index_post()
     {
           $flag = $this->input->post('flag');
        
                switch ($flag) {
   
                  case "add_event":
                    
                   $title = $this->input->post('title');
                   $description = $this->input->post('description');
                   $fees = $this->input->post('fees');
                   $classes = $this->input->post('room');
                   $room = $classes;
                   $classes_name = explode(',', $classes);
                   
                   $type = $this->input->post('type');
                   $date = $this->input->post('date');
                   $ftime = $this->input->post('ftime');
                   $totime = $this->input->post('totime');
                   $branch_id = $this->input->post('branch_id');
                   //$school_id = $this->input->post('school_id');
                   //$start_date	 = $this->input->post('start_date');
                   //$end_date	 = $this->input->post('end_date');
                   //$class_id	 = $this->input->post('class_id');
                   $created_at	 = date('Y-m-d H:i:s');
                   if(empty($title) || empty($description) || empty($fees) || empty($room) || empty($type) || empty($date) || empty($ftime) || empty($totime) || empty($branch_id)){
                    $dataTosend = ['status'=>false, 'msg'=>'All field Required!!'];
                    
                   }

                   $saved_id = $this->cm->save('event',['title'=>$title, 'description'=>$description, 'fees'=>$fees, 'room'=>$room, 'type'=>$type, 'date'=>$date, 'ftime'=>$ftime,
                   'totime'=>$totime, 'branch_id'=>$branch_id]);
                   if($saved_id){
                        foreach($classes_name as $class_id){
                            // print_r($class_id);
                         $insertEvent = $this->cm->addRecords('event_class',['event_id'=>$saved_id,'class_id'=>$class_id]);
                        }
                       //$view = $this->em->select($saved_id);
                       $dataTosend = ['status'=>true, 'msg'=>'add event successfull',];
                       echo json_encode($dataTosend); die();
                   }else{
                       $dataTosend = ['status'=>false, 'msg'=>'error_msg', 'something went wrong please try again'];
                       echo json_encode($dataTosend); die();
                   }

                      break;

                      case "view_event":
                         $branch_id = $this->input->post('branch_id');
                         $date = $this->input->post('date');
                         $class_id = $this->input->post('class_id');
                         $sql = "select e.id,title,description,room,fees,type,date,ftime,totime,e.branch_id,ec.class_id,c.name as class_name from event e join 
                         event_class ec on e.id = ec.event_id join class_tbl c on c.class_id = ec.class_id  where date like '%".$date."%' and e.branch_id='$branch_id' 
                         GROUP BY e.id ORDER BY date DESC";
                        $res = $this->db->query($sql)->result();
                        // echo "<pre>";
                        // print_r($res);
                        // die();
                        if($res){
                            $dataTosend = ['status'=>true, 'msg'=>'view event', 'body'=>$res];
                            echo json_encode($dataTosend); die();
                        }else{
                            if(empty($date)){
                                $sql = "select e.id,title,description,room,fees,type,date,ftime,totime,e.branch_id,ec.class_id,c.name as class_name from event e join 
                         event_class ec on e.id = ec.event_id join class_tbl c on c.class_id = ec.class_id  where e.branch_id='$branch_id' GROUP BY e.id
                         ORDER BY date DESC";
                        $res = $this->db->query($sql)->result();
                                 $dataTosend = ['status'=>true, 'msg'=>'view event', 'body'=>$res];
                            }else{
                                 $dataTosend = ['status'=>true, 'msg'=>'view event','body'=>[]];
                            }
                            
                           
                            echo json_encode($dataTosend); die();
                        }

                      break;

                      case "update_event":

                           $id = $this->input->post('id');
                           $title = $this->input->post('title');
                           $description = $this->input->post('description');
                           $fees = $this->input->post('fees');
                           $room = $this->input->post('room');
                           $type = $this->input->post('type');
                           $date = $this->input->post('date');
                           $ftime = $this->input->post('ftime');
                           $totime = $this->input->post('totime');
                           $updated_at	 = date('Y-m-d H:i:s');
                           if(empty($title) || empty($description) || empty($fees) || empty($room) || empty($type) || empty($date) || empty($ftime) || empty($totime)){
                            $dataTosend = ['status'=>false, 'msg'=>'All field Required!!'];
                            
                           }
                        $whr = ['title'=>$title, 'description'=>$description, 'fees'=>$fees, 'room'=>$room, 'type'=>$type, 'date'=>$date, 'ftime'=>$ftime, 'totime'=>$totime];
                        $res = $this->cm->update('event',['id'=>$id],$whr);
                        if($res){
                            $dataTosend = ['status'=>true, 'msg'=>'update event successfull'];
                            echo json_encode($dataTosend); die();
                        }else{
                            $dataTosend = ['status'=>false, 'msg'=>'failed', 'body'=>''];
                            echo json_encode($dataTosend); die();
                        }

                      break;
                     
                      
                      case "applied_event":
                         // print_r($this->input->post()); die;
                      $student_id = $this->input->post('student_id');
                      $event_id = $this->input->post('event_id');
                      $class_id = $this->input->post('class_id');
                      $enrolled_by_flag = $this->input->post('enrolled_by_flag');
                      $enrolled_by = $this->input->post('enrolled_by');
                      $created_at = date('Y-m-d h-m-s');
                      $updated_at = date('Y-m-d h-m-s');
                      if(empty($student_id) || empty($event_id) || empty($class_id) || empty($enrolled_by_flag) || empty($enrolled_by)){
                          $dataTosend = ['status'=>false, 'msg'=>'all field required'];
                          echo json_encode($dataTosend); die();
                      }
                      //echo 123; die;
                     $save = $this->cm->save('event_join_student',['student_id'=>$student_id, 'event_id'=>$event_id, 'class_id'=>$class_id, 'enrolled_by_flag'=>$enrolled_by_flag,
                     'enrolled_by'=>$enrolled_by, 'created_at'=>$created_at, 'updated_at'=>$updated_at]);
                      //echo 123; die;
                      if($save){
                          $dataTosend = ['status'=>true, 'msg'=>'event applied successfull'];
                          echo json_encode($dataTosend); die();
                      }else{
                          $dataTosend = ['status'=>false, 'msg'=>'event not added'];
                          echo json_encode($dataTosend); die();
                      }
                      
                      break;
                      
                      
                      case "applied_event_list":
                      
                    //   $student_id = $this->input->post('student_id');
                      $branch_id = $this->input->post('branch_id');
                      if(empty($branch_id)){
                          $dataTosend = ['status'=>false, 'msg'=>'all field required'];
                          echo json_encode($dataTosend); die();
                      }
                      
                    //   $sql  = "SELECT ut.name as student_name, e.title, e.fees, es.student_id, es.event_id, es.created_at, e.description, e.ftime, e.totime, st.name as section_name, 
                    //   st.section_id, ct.class_id, ct.name as class_name FROM event_join_student as es 
                    //   JOIN event as e on es.event_id=e.id JOIN user_tbl as ut on ut.user_id=es.student_id join section_tbl as st on
                    //   ut.section_id=st.section_id join class_tbl as ct on st.class_id=ct.class_id where es.student_id='$student_id' and ct.branch_id='$branch_id'";
                      $sql = "SELECT ut.name as student_name, e.title, e.fees, es.student_id, es.event_id, es.created_at, e.description, e.ftime, e.totime, st.name as section_name, 
                      st.section_id, ct.class_id, ct.name as class_name FROM event_join_student as es 
                      JOIN event as e on es.event_id=e.id JOIN user_tbl as ut on ut.user_id=es.student_id join section_tbl as st on
                      ut.section_id=st.section_id join class_tbl as ct on st.class_id=ct.class_id where  ct.branch_id='$branch_id'";
                     
                      $res = $this->db->query($sql)->result();
                      if($res){
                          $dataTosend = ['status'=>true, 'msg'=>'event list', 'body'=>$res];
                          echo json_encode($dataTosend); die();
                      }else{
                          $dataTosend = ['status'=>false, 'msg'=>'event not found'];
                          echo json_encode($dataTosend); die();
                      }
                      
                      break;
                      
                      
                      case "view_event_date":
                          $date = $this->input->post('date');
                          $sql = "select id,title,description,fees,room,type,date,ftime,totime from event WHERE event.date LIKE '%".$date."%'";
                          $res = $this->db->query($sql)->result();
                          if($res){
                              $dataTosend = ['status'=>true, 'msg'=>'view event all date', 'body'=>$res];
                              echo json_encode($dataTosend); die();
                          }else{
                              $dataTosend = ['status'=>false, 'msg'=>'faield'];
                              echo json_encode($dataTosend); die();
                          }
                          
                      case "delete_event":
                      
                      $id = $this->input->post('event_id');
                    
                      $sql = "DELETE FROM `event` WHERE id='$id'";
                      $res = $this->db->query($sql);
                      if($res){
                          $dataTosend = ['status'=>true, 'msg'=>'event deleted successfully!'];
                          echo json_encode($dataTosend); die();
                      }else{
                          $dataTosend = ['status'=>false, 'msg'=>'unable to delete event'];
                          echo json_encode($dataTosend); die();
                      }
                      break;
                      
                      break;
                      default:
                     $dataTosend = ['status'=>false,'msg'=>"Invalid Requst",'body'=>''];
                        echo json_encode($dataTosend); die(); 
                }
    } 
  	
}