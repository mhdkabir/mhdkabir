<?php
   
require APPPATH . 'libraries/REST_Controller.php';
    //  error_reporting(0);
class Student extends REST_Controller {
   
    public function __construct() {
       parent::__construct();
         
       $this->load->model('Common_model','cm');
       $this->load->model('Student_model','sm');
        
    }
   
    public function index_post()
     {
           $flag = $this->input->post('flag');
        
                switch ($flag) {
                  case "add_new_student":
                        //  echo 123; die();
                    {
                        //print_r($this->input->post()); die;
                        //$name = $this->input->post('name');
                        $fname = $this->input->post('fname');
                        $lname = $this->input->post('lname');
                        $DOB = $this->input->post('DOB');
                        $gender = $this->input->post('gender');
                        $address = $this->input->post('address');
                        $country = $this->input->post('country');
                        $pincode = $this->input->post('pincode');
                        $city = $this->input->post('city');
                        $state = $this->input->post('state');
                        $branch_id = $this->input->post('branch_id');
                        $file_name = $this->cm->file_upload('image', 'assets/images/');
                        $section_id = $this->input->post('section_id');     
                        $branch_id = $this->input->post('branch_id'); 
                        $class_id = $this->input->post('class_id'); 
                        
                        if(empty($fname) || empty($lname) || empty($DOB) || empty($gender) || empty($address) || empty($country) || empty($pincode) 
                        || empty($city) || empty($state)  || empty($section_id) || empty($class_id)){
                            $dataTosend = ['status'=>true, 'msg'=>'All field Required!!'];
                            echo json_encode($dataTosend); die();
                        } 
                        $user_id = $this->cm->save('user_tbl',['name'=>$fname.' '.$lname, 'DOB'=>$DOB, 'gender'=>$gender, 'address'=>$address, 'country'=>$country, 'state'=>$state,
                        'city'=>$city, 'image'=>$file_name, 'pincode'=>$pincode, 'branch_id'=>$branch_id, 'section_id'=>$section_id, 'roll'=>'4']);
                        //echo "<pre>"; print_r($res);
                        if($user_id){
                             $student_id = $this->cm->save('student_tbl',['user_id'=>$user_id,'branch_id'=>$branch_id, 'section_id'=>$section_id, 'class_id'=>$class_id]);
                            
                            $dataTosend = ['status'=>true, 'msg'=>'new student added'];
                            echo json_encode($dataTosend); die();
                        }else{
                            $dataTosend = ['status'=>false, 'msg'=>'please add student', 'body'=>''];
                            echo json_encode($dataTosend); die();
                        }
                    }
                    break;

                  case "view_student":
                      
                      $user_id = $this->input->post('user_id');
                      $branch_id = $this->input->post('branch_id');
                      $class_id = $this->input->post('class_id') ?: 0;
                      if($class_id){$classcon = "and s.class_id = '$class_id'";}else{$classcon = "";}
                      $section_id = $this->input->post('section_id') ?: 0;
                      if($section_id){$sectioncon = "and s.section_id = '$section_id'";}else{$sectioncon = "";}
                      if(!empty($branch_id) && (!empty($user_id))){
                         $sql = "select b.user_id,b.name,b.mobile,b.DOB,b.pincode,b.gender,b.city,b.state,b.address,b.email, b.image,cl.name as class_name,
                         st.name as section_name,a.branch_id from student_tbl as a join user_tbl as b on a.user_id = b.user_id join class_tbl as cl on 
                         cl.class_id=a.class_id join section_tbl as st on a.section_id=st.section_id where a.branch_id='$branch_id' 
                         and a.user_id='$user_id'".$classcon.$sectioncon;
                      }else if(!empty($user_id)){
                         $sql = "select b.user_id,b.name,b.mobile,b.DOB,b.pincode,b.gender,b.city,b.state,b.address,b.email, b.image,cl.name as class_name,
                         st.name as section_name,a.branch_id from student_tbl as a join user_tbl as b on a.user_id = b.user_id join class_tbl as cl on 
                         cl.class_id=a.class_id join section_tbl as st on a.section_id=st.section_id where a.user_id='$user_id'".$classcon.$sectioncon;
                      }else if(!empty($branch_id)){
                         $sql = "select b.user_id,b.name,b.mobile,b.DOB,b.pincode,b.gender,b.city,b.state,b.address,b.email, b.image,cl.name as class_name,
                         st.name as section_name,a.branch_id from student_tbl as a join user_tbl as b on a.user_id = b.user_id join class_tbl as cl on 
                         cl.class_id=a.class_id join section_tbl as st on a.section_id=st.section_id where a.branch_id='$branch_id'".$classcon.$sectioncon;
                      }
                      $res = $this->db->query($sql)->result();
                      //echo "<pre>"; print_r($res); die();
                      foreach($res as $val){
                                $val->image = ($val->image)? base_url('assets/images/').$val->image :'';
                            }
                      if($res){
                          $dataTosend = ['status'=>true, 'msg'=>'view student', 'body'=>$res];
                          echo json_encode($dataTosend); die();
                      }else{
                          $dataTosend = ['status'=>false, 'msg'=>'record not found'];
                          echo json_encode($dataTosend); die();
                      }
                      
                      
                    break;
                    
                    case "update_student":
                        // echo 123; die();
                        //print_r($this->input->post()); die;
                        $fname = $this->input->post('fname');
                        $lname = $this->input->post('lname');
                        $DOB = $this->input->post('DOB');
                        $gender = $this->input->post('gender');
                        $address = $this->input->post('address');
                        $country = $this->input->post('country');
                        $pincode = $this->input->post('pincode');
                        $city = $this->input->post('city');
                        $state = $this->input->post('state');
                        //$branch_id = $this->input->post('branch_id');
                        $user_id = $this->input->post('user_id');
                        $class_id = $this->input->post('class_id');
                        $section_id = $this->input->post('section_id');
                        $file_name = $this->cm->file_upload('image', 'assets/images/');; 
                        
                        if(empty($fname) || empty($lname) || empty($DOB) || empty($gender) || empty($address) || empty($country) || empty($pincode) 
                        || empty($city) || empty($state) || empty($file_name)){
                            $dataTosend = ['status'=>true, 'msg'=>'All field Required!!'];
                            echo json_encode($dataTosend); die();
                        } 
                        $whr = ['name'=>$fname.' '.$lname, 'DOB'=>$DOB, 'gender'=>$gender, 'address'=>$address, 'country'=>$country, 'state'=>$state,
                        'city'=>$city, 'image'=>$file_name, 'pincode'=>$pincode];
                        $res = $this->cm->update('user_tbl',['user_id'=>$user_id],$whr);
                        
                        $whr = ['class_id'=>$class_id, 'section_id'=>$section_id];
                        $res = $this->cm->update('student_tbl',['user_id'=>$user_id],$whr);
                        
                        //echo "<pre>"; print_r($res);
                        if($res){
                                // $res1 = $this->sm->select($user_id);
                             //$student_id = $this->cm->update('student_tbl',['user_id'=>$user_id,'branch_id'=>$branch_id, 'section_id'=>$section_id, 'class_id'=>$class_id]);
                            $dataTosend = ['status'=>true, 'msg'=>'update student successfull'];
                            echo json_encode($dataTosend); die();
                        }else{
                            $dataTosend = ['status'=>false, 'msg'=>'student not updated', 'body'=>''];
                            echo json_encode($dataTosend); die();
                        }
                    
                    break;
                    
                    case "view_all_student":
                        
                            $student_id = $this->input->post('student_id');
                            $class_id = $this->input->post('class_id');
                            $section_id = $this->input->post('section_id');
                            $branch_id =  $this->input->post('branch_id');
            
                            
                        if(!empty($section_id) && (!empty($class_id)) && (!empty($branch_id))){
                         $q = "select b.user_id,b.name,b.mobile,b.address,b.email, b.image,a.class_id,a.section_id,a.branch_id from student_tbl as a join user_tbl as b on a.user_id = b.user_id 
                        where a.section_id = '".$section_id."' and a.class_id = '".$class_id."' and a.branch_id = '".$branch_id."'"; 
                        //echo $q;
                        }else if(!empty($section_id)  && (!empty($branch_id)) ){
                            $q = "select b.user_id,b.name,b.mobile,b.address,b.email, b.image,a.class_id,a.section_id,a.branch_id from student_tbl as a join user_tbl as b on a.user_id = b.user_id 
                        where a.section_id = '$section_id' and a.branch_id = '$branch_id'";
                        }else if(!empty($class_id)  && (!empty($branch_id))){
                            $q = "select b.user_id,b.name,b.mobile,b.address,b.email, b.image,a.class_id,a.section_id,a.branch_id from student_tbl as a join user_tbl as b on a.user_id = b.user_id 
                        where a.class_id = '$class_id' and a.branch_id = '$branch_id'";
                        }else if(!empty($branch_id)){
                            $q = "select b.user_id,b.name,b.mobile,b.address,b.email, b.image,a.class_id,a.section_id,a.branch_id from student_tbl as a join user_tbl as b on a.user_id = b.user_id
                            where a.branch_id = '$branch_id'";  
                        }else{
                            $q = "select b.user_id,b.name,b.mobile,b.address,b.email, b.image,a.class_id,a.section_id,a.branch_id from student_tbl as a join user_tbl as b on a.user_id = b.user_id";
                        }
                                $res = $this->db->query($q)->result();
                                //echo "<pre>"; print_r($res); die();
                                foreach($res as $val){
                                     $val->image = ($val->image)? base_url('assets/images/').$val->image :'';
                                }
                                //print_r($res); die();
                                if($res){
                                    $dataTosend = ['status'=>true, 'msg'=>'view all student', 'body'=>$res];
                                    echo json_encode($dataTosend); die();
                                }else{
                                    $dataTosend = ['status'=>false, 'msg'=>'record not found', 'body'=>[]];
                                    echo json_encode($dataTosend); die();
                                }
                    break;
                    case "add_attendance_user":
                      
                      
                      $check_in = $this->input->post('check_in');
                      $check_out = $this->input->post('check_out');
                      $type = $this->input->post('type');
                      $user_id = $this->input->post('user_id');
                      $atnd_id = $this->input->post('attendance_id');
                   
                      if(empty($user_id) || empty($type)){
                            $dataTosend = ['status'=>true, 'msg'=>'All field Required!!'];
                            echo json_encode($dataTosend); die();
                      }
                      if($type=="absent")
                      {
                        $saved_id= $this->cm->save('attendance_user',['user_id'=>$user_id,'type'=>$type]);
                        if($saved_id)
                    {                                                                                       
                               $dataTosend = ['status'=>true, 'msg'=>'absend added successfull'];
                               echo json_encode($dataTosend); die();
                           }else{
                               $dataTosend = ['status'=>false, 'msg'=>'failed'];
                               echo json_encode($dataTosend); die();
                           }
                      }else
                      {
                        if ($type=="leave") {
                        $saved_id = $this->cm->save('attendance_user',['user_id'=>$user_id,'type'=>$type]);
                         if($saved_id)
                    {                                                                                       
                               $dataTosend = ['status'=>true, 'msg'=>'leave added successfull'];
                               echo json_encode($dataTosend); die();
                           }else{
                               $dataTosend = ['status'=>false, 'msg'=>'failed'];
                               echo json_encode($dataTosend); die();
                           }
                        }
                      }
                      if($type == "check_in"){
                      $saved_id = $this->cm->save('attendance_user',['user_id'=>$user_id, 'type'=>$type, 'check_in'=>$check_in]);
                           if($saved_id){                                                                                       
                               $dataTosend = ['status'=>true, 'msg'=>'Attendance added successfull'];
                               echo json_encode($dataTosend); die();
                           }else{
                               $dataTosend = ['status'=>false, 'msg'=>'failed'];
                               echo json_encode($dataTosend); die();
                           }
                      }else{
                          $whr = array('atnd_id'=>$atnd_id);
                            $res = $this->cm->update('attendance_user',['check_out'=>$check_out],$whr);
                             if($res){
                               $dataTosend = ['status'=>true, 'msg'=>'Attendance upadded successfull'];
                               echo json_encode($dataTosend); die();
                           }else{
                               $dataTosend = ['status'=>false, 'msg'=>'failed'];
                               echo json_encode($dataTosend); die();
                           }
                          
                      }
                           
                      break;
                      
                
                  default:
                     $dataTosend = ['status'=>false,'msg'=>"Invalid Requst",'body'=>''];
                        echo json_encode($dataTosend); die(); 
                }
    } 
  	
}