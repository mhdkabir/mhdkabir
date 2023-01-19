<?php
   
require APPPATH . 'libraries/REST_Controller.php';
     
class Staff extends REST_Controller {
   
    public function __construct() {
       parent::__construct();
      $this->load->model('Common_model','cm');
      $this->load->model('Staff_model','sm');
    }
   

    public function index_post()
     {
           $flag = $this->input->post('flag');
        
                switch ($flag) {
                  case "add_staff":
                      
                      //echo 123;die;
                      $lname = $this->input->post('lname');
                      $fname = $this->input->post('fname');
                      $DOB = $this->input->post('DOB');
                      $email = $this->input->post('email');
                      $mobile = $this->input->post('mobile');
                      $password = "123";
                      $gender = $this->input->post('gender');
                      $country = $this->input->post('country');
                      $state = $this->input->post('state');
                      $city = $this->input->post('city');
                      $pincode = $this->input->post('pincode');
                      $address = $this->input->post('address');
                      $file_name = $this->cm->file_upload('image', 'assets/images/');
                      $branch_id = $this->input->post('branch_id');
                      if(empty($lname) || empty($fname) || empty($branch_id) || empty($DOB) || empty($gender) || empty($country) || empty($state) || empty($city) || empty($pincode) || empty($address)){
                            $dataTosend = ['status'=>true, 'msg'=>'All field Required!!'];
                            echo json_encode($dataTosend); die();
                      }
                      
                      $saved_id = $this->cm->save('user_tbl',['name'=>$fname.' '.$lname, 'DOB'=>$DOB, 'image'=>$file_name, 'gender'=>$gender, 'country'=>$country, 'state'=>$state, 'city'=>$city, 
                      'pincode'=>$pincode, 'address'=>$address, 'branch_id'=>$branch_id, 'roll'=>'2', 'mobile'=>$mobile, 'email'=>$email, 'password'=>$password]);
                           if($saved_id){
                               $this->cm->save('staff_tbl',['branch_id'=>$branch_id, 'user_id'=>$saved_id]);
                               
                               $dataTosend = ['status'=>true, 'msg'=>'add staff successfull'];
                               echo json_encode($dataTosend); die();
                           }else{
                               $dataTosend = ['status'=>false, 'msg'=>'failed'];
                               echo json_encode($dataTosend); die();
                           }
                           
                      break;
                      
                      case "view_all_staff":
                          //echo 123; die;
                        //   $student_id = $this->input->post('student_id');
                        //     $class_id = $this->input->post('class_id');
                        //     $section_id = $this->input->post('section_id');
                            $branch_id =  $this->input->post('branch_id');
                            $name = $this->input->post('name');
                        if(empty($branch_id)){
                            $dataTosend = ['status'=>false, 'msg'=>'Branch Id Required'];
                            echo json_encode($dataTosend); die();
                        }else{
                            $q = "select  b.user_id,b.name,b.DOB,b.gender,b.country,b.state,b.city,b.pincode,b.address, b.image,a.branch_id from staff_tbl as a join user_tbl as b on a.user_id = b.user_id where b.name like '%".$name."%' and a.branch_id='$branch_id'";
                        
                                $res = $this->db->query($q)->result();
                                
                                foreach($res as $val){
                                    $val->image = ($val->image)? base_url('assets/images/').$val->image :'';
                                }
                                //print_r($res); die();
                                if($res){
                                    $dataTosend = ['status'=>true, 'msg'=>'view all staff', 'body'=>$res];
                                    echo json_encode($dataTosend); die();
                                }else{
                                    $dataTosend = ['status'=>false, 'msg'=>'Staff not found', 'body'=>[]];
                                    echo json_encode($dataTosend); die();
                                }
                        }
                        
                      
                      break;
                      
                      case "view_staff":
                          //echo 123; die;
                          $branch_id = $this->input->post('branch_id');
                          $user_id = $this->input->post('user_id');
                          if(!empty($branch_id) && (!empty($user_id))){
                             $sql = "select a.user_id,b.name,b.DOB,b.gender,b.country,b.state,b.city,b.pincode,b.address, b.image,a.branch_id,a.hire_date,a.notes,b.email,b.mobile from staff_tbl as a join user_tbl as b on a.user_id = b.user_id where a.branch_id='$branch_id' and a.user_id='$user_id'";
                          }else if(!empty($user_id)){
                             $sql = "select a.user_id,b.name,b.DOB,b.gender,b.country,b.state,b.city,b.pincode,b.address, b.image,a.branch_id,b.email,b.mobile from staff_tbl as a join user_tbl as b on a.user_id = b.user_id where a.user_id='$user_id'";
                          }else{
                              $sql = "select a.user_id,b.name,b.DOB,b.gender,b.country,b.state,b.city,b.pincode,b.address, b.image,a.branch_id,b.email,b.mobile from staff_tbl as a join user_tbl as b on a.user_id = b.user_id where a.branch_id='$branch_id'";
                          }
                          $res = $this->db->query($sql)->result();
                          foreach($res as $val){
                              $val->image = ($val->image)? base_url('assets/images/').$val->image :'';
                          }
                          //echo "<pre>"; print_r($res); die;
                          if($res){
                              $dataTosend = ['status'=>true, 'msg'=>'view staff', 'body'=>$res];
                              echo json_encode($dataTosend); die();
                          }else{
                              $dataTosend = ['status'=>false, 'msg'=>'record can not found'];
                              echo json_encode($dataTosend); die();
                          }
                      
                      break;
                      
                      case "view_reminder":
                          $branch_id = $this->input->post('branch_id');
                          if(!empty($branch_id)){
                             $sql = "select * from reminder where branch_id='$branch_id'";
                          }
                          $res = $this->db->query($sql)->result();
                          if($res){
                              $dataTosend = ['status'=>true, 'msg'=>'view reminder', 'body'=>$res];
                              echo json_encode($dataTosend); die();
                          }else{
                              $dataTosend = ['status'=>false, 'msg'=>'record can not found'];
                              echo json_encode($dataTosend); die();
                          }
                      
                      break;
                      
                      
                      case "delete_reminder":
                      
                      $id = $this->input->post('reminder_id');
                    
                      $sql = "DELETE FROM `reminder` WHERE rim_id='$id'";
                      $res = $this->db->query($sql);
                      if($res){
                          $dataTosend = ['status'=>true, 'msg'=>'reminder deleted successfully!'];
                          echo json_encode($dataTosend); die();
                      }else{
                          $dataTosend = ['status'=>false, 'msg'=>'unable to delete reminder'];
                          echo json_encode($dataTosend); die();
                      }
                      break;
                      
                      case "update_staff":
                      
                      $user_id = $this->input->post('user_id');
                      $lname = $this->input->post('lname');
                      $fname = $this->input->post('fname');
                      $DOB = $this->input->post('DOB');
                      $gender = $this->input->post('gender');
                      $email = $this->input->post('email');
                      $mobile = $this->input->post('mobile');
                      $country = $this->input->post('country');
                      $state = $this->input->post('state');
                      $city = $this->input->post('city');
                      $pincode = $this->input->post('pincode');
                      $address = $this->input->post('address');
                      $file_name = $this->cm->file_upload('image', 'assets/images/');
                      //$branch_id = $this->input->post('branch_id');
                      if(empty($lname) || empty($fname) || empty($DOB) || empty($gender) || empty($country) || empty($state) || empty($city) || empty($pincode) || empty($address)){
                            $dataTosend = ['status'=>false, 'msg'=>'Please All field update!!'];
                            echo json_encode($dataTosend); die();
                      }
                      
                      $whr = ['name'=>$fname.' '.$lname, 'DOB'=>$DOB, 'image'=>$file_name, 'gender'=>$gender, 'country'=>$country, 'state'=>$state, 'city'=>$city, 
                      'pincode'=>$pincode, 'address'=>$address];
                      $res = $this->cm->update('user_tbl',['user_id'=>$user_id],$whr);
                           if($res){
                               //$this->cm->save('staff_tbl',['branch_id'=>$branch_id, 'user_id'=>$res]);
                                // $res1 = $this->sm->select($branch_id);
                               $dataTosend = ['status'=>true, 'msg'=>'update staff successfull'];
                               echo json_encode($dataTosend); die();
                           }else{
                               $dataTosend = ['status'=>false, 'msg'=>'failed'];
                               echo json_encode($dataTosend); die();
                           }
                      
                      break;
                    case "add_attendance_staff":
                      
                      //echo 123;die;
                      $check_in = $this->input->post('check_in');
                      $check_out = $this->input->post('check_out');
                      $type = $this->input->post('type');
                      $staff_id = $this->input->post('staff_id');
                      $atnd_id = $this->input->post('attendance_id');
                   
                      if(empty($staff_id) || empty($type)){
                            $dataTosend = ['status'=>true, 'msg'=>'All field Required!!'];
                            echo json_encode($dataTosend); die();
                      }
                      if($type == "check_in"){
                      $saved_id = $this->cm->save('attendance_staff',['staff_id'=>$staff_id, 'type'=>$type, 'check_in'=>$check_in]);
                           if($saved_id){
                               $dataTosend = ['status'=>true, 'msg'=>'Attendance added successfull'];
                               echo json_encode($dataTosend); die();
                           }else{
                               $dataTosend = ['status'=>false, 'msg'=>'failed'];
                               echo json_encode($dataTosend); die();
                           }
                      }else{
                          $whr = array('atnd_id'=>$atnd_id);
                            $res = $this->cm->update('attendance_staff',['check_out'=>$check_out],$whr);
                             if($res){
                               $dataTosend = ['status'=>true, 'msg'=>'Attendance added successfull'];
                               echo json_encode($dataTosend); die();
                           }else{
                               $dataTosend = ['status'=>false, 'msg'=>'failed'];
                               echo json_encode($dataTosend); die();
                           }
                          
                      }
                           
                      break;
                       case "add_reminder":
                      
                      $name = $this->input->post('name');
                      $time = $this->input->post('time');
                      $item = $this->input->post('item');
                      $note = $this->input->post('note');
                      $student_id = $this->input->post('student_id');
                      $branch_id = $this->input->post('branch_id');
                      if(empty($name) || empty($time) || empty($item) || empty($note) || empty($student_id) || empty($branch_id)){
                            $dataTosend = ['status'=>false, 'msg'=>'Please All field update!!'];
                            echo json_encode($dataTosend); die();
                      }
                      
                      $add = ['name'=>$name, 'time'=>$time, 'item'=>$item, 'note'=>$note, 'student_id'=>$student_id, 'branch_id'=>$branch_id];
                       $saved_id = $this->cm->save('reminder',$add);
                           if($saved_id){
                               //$this->cm->save('staff_tbl',['branch_id'=>$branch_id, 'user_id'=>$res]);
                                // $res1 = $this->sm->select($branch_id);
                               $dataTosend = ['status'=>true, 'msg'=>'Reminder Added successfull'];
                               echo json_encode($dataTosend); die();
                           }else{
                               $dataTosend = ['status'=>false, 'msg'=>'failed'];
                               echo json_encode($dataTosend); die();
                           }
                      
                      break;
                      
                      case "edit_reminder":
                      
                      $name = $this->input->post('name');
                      $time = $this->input->post('time');
                      $item = $this->input->post('item');
                      $note = $this->input->post('note');
                      $student_id = $this->input->post('student_id');
                      $branch_id = $this->input->post('branch_id');
                      $reminder_id = $this->input->post('reminder_id');
                      $category = $this->input->post('category');
                      //$branch_id = $this->input->post('branch_id');
                      if(empty($name) || empty($time) || empty($note) || empty($reminder_id)){
                            $dataTosend = ['status'=>false, 'msg'=>'Please All field update!!'];
                            echo json_encode($dataTosend); die();
                      }
                      
                      $whr = ['name'=>$name, 'time'=>$time, 'item'=>$item, 'note'=>$note, 'branch_id'=>$branch_id];
                      $res = $this->cm->update('reminder',['rim_id'=>$reminder_id],$whr);
                           if($res){
                               
                               $dataTosend = ['status'=>true, 'msg'=>'reminder updated successfully'];
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