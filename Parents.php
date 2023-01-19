<?php
   
require APPPATH . 'libraries/REST_Controller.php';
     
class Parents extends REST_Controller {
   
    public function __construct() {
       parent::__construct();
       $this->load->model('Common_model','cm');
      
    }
   

    public function index_post()
     {
           $flag = $this->input->post('flag');
        
                switch ($flag) {
                  case "add_parent":
                   $fname = $this->input->post('fname');
                   $lname = $this->input->post('lname');
                   $email = $this->input->post('email');
                   $mobile = $this->input->post('mobile');
                   $branch_id = $this->input->post('branch_id');
                   $file_name = $this->cm->file_upload('image', 'assets/images/');
                   $student_id = $this->input->post('user_id');
                   $student_relational = $this->input->post('student_relational');
                   if(empty($fname) || empty($lname) || empty($email) || empty($mobile) || empty($student_relational) || empty($branch_id))
                   {
                            $dataTosend = ['status'=>true, 'msg'=>'All field Required!!'];
                            echo json_encode($dataTosend); die();
                   }
                   $user_id = $this->cm->save('user_tbl',['name'=>$fname.' '.$lname, 'email'=>$email, 'mobile'=>$mobile, 'image'=>$file_name, 'roll'=>'3', 'password'=>'123', 'branch_id'=>$branch_id]);
                   if($user_id){
                      $this->cm->save('parents_tbl',['student_id'=>$student_id, 'student_relational'=>$student_relational, 'guardian_id'=>$user_id]);
                       $dataTosend = ['status'=>true, 'msg'=>'add parents successfull', 'body'=>''];
                       echo json_encode($dataTosend); die();
                   }else{
                       $dataTosend = ['status'=>false, 'msg'=>'failed', 'body'=>''];
                       echo json_encode($dataTosend); die();
                   }
                  
                       
                    break;
                    
                    
                    case "delete_parent":
                   $parent_id = $this->input->post('parent_id');
                   if(empty($parent_id))
                   {
                            $dataTosend = ['status'=>true, 'msg'=>'All field Required!!'];
                            echo json_encode($dataTosend); die();
                   }
                   $sql = "DELETE FROM `user_tbl` WHERE user_id='$parent_id' and roll = '3'";
                   $res = $this->db->query($sql);
                   
                   $sql = "DELETE FROM `parents_tbl` WHERE guardian_id='$parent_id'";
                   $res = $this->db->query($sql);
                      if($res){
                          $dataTosend = ['status'=>true, 'msg'=>'parent deleted successfully!'];
                          echo json_encode($dataTosend); die();
                      }else{
                          $dataTosend = ['status'=>false, 'msg'=>'unable to delete parent'];
                          echo json_encode($dataTosend); die();
                      }
                  
                       
                    break;
                    
                    case "update_parent":
                   $fname = $this->input->post('fname');
                   $lname = $this->input->post('lname');
                   $email = $this->input->post('email');
                   $mobile = $this->input->post('mobile');
                   $file_name = $this->cm->file_upload('image', 'assets/images/');
                   $user_id = $this->input->post('user_id');
                   $student_relational = $this->input->post('student_relational');
                   if(empty($fname) || empty($lname) || empty($email) || empty($mobile) || empty($file_name))
                   {
                            $dataTosend = ['status'=>true, 'msg'=>'All field Required!!'];
                            echo json_encode($dataTosend); die();
                   }
                   $sql = ['name'=>$fname.' '.$lname, 'email'=>$email, 'mobile'=>$mobile, 'image'=>$file_name];
                   $res = $this->cm->update('user_tbl',['user_id'=>$user_id],$sql);
                   if($res){
                       $dataTosend = ['status'=>true, 'msg'=>'update parents successfull', 'body'=>''];
                       echo json_encode($dataTosend); die();
                   }else{
                       $dataTosend = ['status'=>false, 'msg'=>'failed', 'body'=>''];
                       echo json_encode($dataTosend); die();
                   }
                    
                    break;
                    
                    case "view_update_parent":
                        
                        $user_id = $this->input->post('user_id');
                        $sql = "select user_id,name,email,mobile,image from user_tbl where user_id='$user_id'";
                        $res = $this->db->query($sql)->result();
                        foreach($res as $val){
                                $val->image = ($val->image)? base_url('assets/images/').$val->image :'';
                            }
                        if($res){
                            $dataTosend = ['status'=>true, 'msg'=>'view update student', 'body'=>$res];
                            echo json_encode($dataTosend); die();
                        }else{
                            $dataTosend = ['status'=>false, 'msg'=>'record can not found'];
                            echo json_encode($dataTosend); die();
                        }
                    
                    break;
                    
                    case "view_all_parent":
                        $name = $this->input->post('name');
                        $branch_id = $this->input->post('branch_id');
                        
                        if(!empty($branch_id)){
                            $sql = "select user_id,name,email,branch_id,mobile,image from user_tbl where branch_id='$branch_id' and roll='3'";
                        }else if(!empty($name)){
                            $sql = "select user_id,name,email,branch_id,mobile,image from user_tbl where name like '%".$name."%' and roll='3'";
                        }else{
                            $sql = "select user_id,name,email,branch_id,mobile,image from user_tbl where name like '%".$name."%' and branch_id='$branch_id' and roll='3'";
                        }
                        $res = $this->db->query($sql)->result();
                        foreach($res as $val){
                                $val->image = ($val->image)? base_url('assets/images/').$val->image :'';
                            }
                        if($res){
                            $dataTosend = ['status'=>true, 'msg'=>'view update student', 'body'=>$res];
                            echo json_encode($dataTosend); die();
                        }else{
                            $dataTosend = ['status'=>false, 'msg'=>'record can not found'];
                            echo json_encode($dataTosend); die();
                        }
                    
                    break;
                    
                    
                    case "parents_add":
                        $guardian_id = $this->input->post('guardian_id');
                        $student_id = $this->input->post('student_id');
                        
                        if(empty($guardian_id) || empty($student_id)){
                          $dataTosend = ['status'=>false, 'msg'=>'All field required'];
                          echo json_encode($dataTosend); die();
                        }
                        
                        
                        $sql = "select guardian_id, student_id from parents_tbl where guardian_id='$guardian_id' and student_id='$student_id'";
                        $res = $this->db->query($sql)->result();
                        if($res){
                            $dataTosend = ['status'=>false, 'msg'=>'allready exists'];
                            echo json_encode($dataTosend); die();
                        }
                        
                        $save = $this->cm->save('parents_tbl',['guardian_id'=>$guardian_id, 'student_id'=>$student_id, 'student_relational'=>'parent']);
                        
                        if($save){
                            $dataTosend = ['status'=>true, 'msg'=>'parents add successfull'];
                            echo json_encode($dataTosend); die();
                        }else{
                            $dataTosend = ['status'=>false, 'msg'=>'parents not added'];
                            echo json_encode($dataTosend); die();
                        }
                    
                    break;
                    
                    case "view_student_prents":
                        
                        $user_id = $this->input->post('user_id');
                        if(empty($user_id)){
                            $dataTosend = ['status'=>true, 'msg'=>'all field required'];
                            echo json_encode($dataTosend); die();
                        }
                        
                    //   $sql = "select p.guardian_id as parent_id , p_tbl.email as parent_email ,p_tbl.mobile as parent_mobile, p_tbl.image as parent_image,
                    //   p_tbl.name as parent_name,p.student_relational from parents_tbl as p join user_tbl as p_tbl on p.guardian_id = p_tbl.user_id 
                    //   where p.guardian_id = '$guardian_id'";
                    $sql = "select p.user_id as parent_id, p.email as parent_email ,p.mobile as parent_mobile, p.image as parent_image,
                       p.name as parent_name from user_tbl as p
                       where p.user_id = '$user_id'";
                       $res = $this->db->query($sql)->result();
                       foreach($res as $val){
                                $val->parent_image = ($val->parent_image)? base_url('assets/images/').$val->parent_image :'';
                            }
                       
                       $job = $arr = array();
                       foreach($res as $value){
                           //$guardian_id = $value->guardian_id;
                           $arr['parent_id'] = $id = $value->parent_id;
                           $arr['parent_email'] = $value->parent_email;
                           $arr['parent_mobile'] = $value->parent_mobile;
                           $arr['parent_name'] = $value->parent_name;
                           $arr['parent_image'] = $value->parent_image;
                           
                           $sql= "SELECT ut.user_id, ut.name, st.class_id, st.section_id,ut.email, ut.address, ut.mobile,ut.image, st.class_id,st.section_id ,cl.name as class_name,sc.name as section_name   FROM parents_tbl as pt join user_tbl as ut on pt.student_id=ut.user_id  join student_tbl as st on st.user_id=ut.user_id 
                          join class_tbl as cl on cl.class_id =st.class_id join section_tbl as sc on sc.section_id = st.section_id WHERE pt.guardian_id = '$id'";
                        // $sql = "SELECT ut.user_id, ut.name, ut.email,ut.image, ut.address, ut.mobile, ct.name as class_name, s.name as section_name
                        // FROM user_tbl as ut join student_tbl as st on ut.user_id=st.user_id 
                        //  join class_tbl as ct on st.class_id=ct.class_id join section_tbl as s on st.section_id=s.section_id WHERE ut.user_id = '$id'";
                           $res = $this->db->query($sql)->result();
                           foreach($res as $val){
                                $val->image = ($val->image)? base_url('assets/images/').$val->image :'';
                            }
                         $sum = $dom = array();
                         foreach($res as $val)
                           {  
                              
                               $inta = array() ;
                               $paid = 0 ;
                               $totalfee = 0 ;
                                 $fees = $this->cm->get_data('fees',['class_id'=>$val->class_id]);
                                     foreach($fees as $fee)
                                       { 
                                          
                                            $recipts = $this->cm->get_data('receipts',['fees_id'=>$fee->id,'student_id'=>$val->user_id]);
                                             foreach($recipts as $recipt){
                                                 $paid  += $recipt->amount_paid;
                                             }
                                           $totalfee += $fee->amount;
                                           $inta[] = $fee;   
                                       } 
                                    //   die;
                              //$guardian_id = $val->guardian_id;
                              $dom['instalments'] =$inta; 
                              $dom['total_fee'] = $totalfee;  
                              $dom['paid'] = $paid;  
                              $dom['due_fee'] = $totalfee - $paid;  
                              $dom['user_id'] = $val->user_id;  
                              $dom['name'] = $val->name; 
                              $dom['mobile'] = $val->mobile; 
                              $dom['address'] = $val->address; 
                              $dom['image'] = $val->image;
                              $dom['class_name'] = $val->class_name;
                              $dom['class_id'] = $val->class_id;
                              $dom['section_id'] = $val->section_id;
                              $dom['section_name'] = $val->section_name;
                     
                              $sum[] =  $dom;
                           } 
                           
                           $arr['data'] = $sum;        
                             $sum = array();
                             $dom = array();
                           
                        $job[] = (object)$arr;    
                       }
                       if($job){
                           $dataTosend = ['status'=>true, 'msg'=>'vew parents student', 'body'=>$job];
                           echo json_encode($dataTosend); die();
                       }else{
                           $dataTosend = ['status'=>false, 'msg'=>'data not found'];
                           echo json_encode($dataTosend); die();
                       }
                    
                    break;
                    
                    

                  case "view_parent":
                          $user_id    = $this->input->post('user_id');
                          $class_id   = $this->input->post('class_id');
                          $section_id = $this->input->post('section_id');
                          $branch_id =  $this->input->post('branch_id');
                          //echo $branch_id;
                          $whr = '';                                              
                          if(!empty($user_id)){                            
                              $whr .= "and a.user_id ='$user_id'";
                          }                           
                                                                                                    
                          if( !empty($class_id)){
                                $whr.= "and a.class_id ='$class_id'";
                          }                
                                                              
                          if( !empty($section_id)){
                                $whr .= "and a.section_id ='$section_id'";
                          }
                          if( !empty($branch_id)){
                            $whr .= "and a.branch_id ='$branch_id'";
                          }
                      
                        if(!empty($branch_id)){
                            $q = "select  b.user_id,b.name,b.mobile,b.address,b.email from student_tbl as a join user_tbl as b on a.user_id = b.user_id 
                                    where a.branch_id ='$branch_id' $whr  " ;
                                    //echo $q; die();
                            $res = $this->db->query($q)->result();
                                                
                        $arr = $job = array(); 
                        
                        foreach($res as $val){
                          $arr['user_id'] = $id = $val->user_id;
                          $arr['name']    =  $val->name;
                          $arr['mobile']  =  $val->mobile;
                          $arr['address'] =  $val->address;
                          $arr['email']   =  $val->email;
                          
                          $qq = "select coalesce(p.guardian_id,'') as parent_id ,coalesce(p_tbl.email,'') as p_email ,coalesce(p_tbl.mobile,'') 
                                    as p_mobile,coalesce(p_tbl.image,'') as parent_image,coalesce(p_tbl.name,'') as parent_name,p.student_relational
                                    from parents_tbl as p   join user_tbl as p_tbl on p.guardian_id = p_tbl.user_id 
                                    where p.student_id = '$id' and p.student_relational = 'Parent'  ";
                                    //echo $qq; die();
                            $arr['data'] = $img =  $this->db->query($qq)->result();
                                foreach($img as $val){
                                    $val->parent_image = ($val->parent_image)? base_url('assets/images/').$val->parent_image :'';
                                }
                              $job[] = (object)$arr;  
                              
                            }

                            if($job){
                              $dataTosend = ['status'=>true, 'msg'=>'parents record', 'body'=>$job];
                              echo json_encode($dataTosend); die();
                            }else{
                              $dataTosend = ['status'=>true, 'msg'=>'failed', 'body'=>''];
                              echo json_encode($dataTosend); die();
                            }
                        }else if(!empty($user_id)){
                            $q = "select  b.user_id,b.name,b.mobile,b.address,b.email from student_tbl as a join user_tbl as b on a.user_id = b.user_id 
                                    where b.user_id='$user_id' $whr  " ;
                                    //echo $q; die();
                            $res = $this->db->query($q)->result();
                                                
                        $arr = $job = array(); 
                        
                        foreach($res as $val){
                          $arr['user_id'] = $id = $val->user_id;
                          $arr['name']    =  $val->name;
                          $arr['mobile']  =  $val->mobile;
                          $arr['address'] =  $val->address;
                          $arr['email']   =  $val->email;
                          
                          $qq = "select coalesce(p.guardian_id,'') as parent_id ,coalesce(p_tbl.email,'') as p_email ,coalesce(p_tbl.mobile,'') 
                                    as p_mobile,coalesce(p_tbl.image,'') as parent_image,coalesce(p_tbl.name,'') as parent_name,p.student_relational
                                    from parents_tbl as p   join user_tbl as p_tbl on p.guardian_id = p_tbl.user_id 
                                    where p.student_id = '$id' and p.student_relational = 'Parent'  ";
                                    //echo $qq; die();
                            $arr['data'] = $img =  $this->db->query($qq)->result();
                            foreach($img as $val){
                                $val->parent_image = ($val->parent_image)? base_url('assets/images/').$val->parent_image :'';
                            }
                              $job[] = (object)$arr;  
                            }
                            if($job){
                              $dataTosend = ['status'=>true, 'msg'=>'parents record', 'body'=>$job];
                              echo json_encode($dataTosend); die();
                            }else{
                              $dataTosend = ['status'=>true, 'msg'=>'failed', 'body'=>''];
                              echo json_encode($dataTosend); die();
                            }
                        }else{
                            $q = "select  b.user_id,b.name,b.mobile,b.address,b.email from student_tbl as a join user_tbl as b on a.user_id = b.user_id 
                                    where a.branch_id='$branch_id' and b.user_id='$user_id' $whr  " ;
                                    //echo $q; die();
                            $res = $this->db->query($q)->result();
                                                
                        $arr = $job = array(); 
                        
                        foreach($res as $val){
                          $arr['user_id'] = $id = $val->user_id;
                          $arr['name']    =  $val->name;
                          $arr['mobile']  =  $val->mobile;
                          $arr['address'] =  $val->address;
                          $arr['email']   =  $val->email;
                          
                          $qq = "select coalesce(p.guardian_id,'') as parent_id ,coalesce(p_tbl.email,'') as p_email ,coalesce(p_tbl.mobile,'') 
                                    as p_mobile,coalesce(p_tbl.image,'') as parent_image,coalesce(p_tbl.name,'') as parent_name,p.student_relational
                                    from parents_tbl as p   join user_tbl as p_tbl on p.guardian_id = p_tbl.user_id 
                                    where p.student_id = '$id' and p.student_relational = 'Parent'  ";
                                    //echo $qq; die();
                            $arr['data']= $img =  $this->db->query($qq)->result();
                                foreach($img as $val){
                                    $val->parent_image = ($val->parent_image)? base_url('assets/images/').$val->parent_image :'';
                                }
                              $job[] = (object)$arr;  
                            }
                            if($job){
                              $dataTosend = ['status'=>true, 'msg'=>'parents record', 'body'=>$job];
                              echo json_encode($dataTosend); die();
                            }else{
                              $dataTosend = ['status'=>true, 'msg'=>'failed', 'body'=>''];
                              echo json_encode($dataTosend); die();
                            }
                        }
                      
                   
                    break;
                      case "add_message":
                          
                    
                            $data = array(
                    'student_id'=> $this->input->post('student_id'),
                    'staff_id'=> $this->input->post('staff_id'),
                    'sender_type'=> $this->input->post('sender_type'),
                    'sender_id'=> $this->input->post('sender_id'),
                    'massage'=> $this->input->post('message'),
                    'date'=> $this->input->post('date'),
                    'time'=> $this->input->post('time'),
                    'mst_type'=> 1
                );
                $test = $this->cm->addRecords('massages_tbl',$data);
                if($test){
                     $dataTosend = ['status'=>true, 'msg'=>'Message added successfully', 'body'=>''];
                              echo json_encode($dataTosend); die();
                            }else{
                              $dataTosend = ['status'=>true, 'msg'=>'failed', 'body'=>''];
                              echo json_encode($dataTosend); die();
                            }
                          
                    break;
                    case "view_message":
                     $student_id= $this->input->post('user_id');
                    $sql = "select * from massages_tbl where student_id='$student_id'";
                        $res = $this->db->query($sql)->result();
                        if($res){
                           $dataTosend = ['status'=>true, 'msg'=>'view all messages', 'body'=>$res];
                              echo json_encode($dataTosend); die();
                        }
                        else
                        {
                            $dataTosend = ['status'=>false, 'msg'=>'no messages found', 'body'=>''];
                              echo json_encode($dataTosend); die();
                        }
                        
                          
                    break;
                    
                    
                    case "view_chat_list":
                     $student_id= $this->input->post('student_id');
                    $sql = "SELECT DISTINCT u.user_id , u.name, u.image FROM massages_tbl m JOIN user_tbl u ON m.staff_id = u.user_id WHERE m.student_id = '$student_id'";
                        $res = $this->db->query($sql)->result();
                        foreach($res as $val){
                                    $val->image = ($val->image)? base_url('assets/images/').$val->image :'';
                                }
                        if($res){
                           $dataTosend = ['status'=>true, 'msg'=>'view chat list', 'body'=>$res];
                              echo json_encode($dataTosend); die();
                        }
                        else
                        {
                            $dataTosend = ['status'=>false, 'msg'=>'no chats found', 'body'=>''];
                              echo json_encode($dataTosend); die();
                        }
                        
                    break;
                    
                    case "view_chat_messages":
                     $student_id= $this->input->post('student_id');
                     $staff_id= $this->input->post('staff_id');
                    $sql = "select * from massages_tbl where (student_id='$student_id' and staff_id='$staff_id') or (student_id='$staff_id' and staff_id='$student_id')";
                        $res = $this->db->query($sql)->result();
                        if($res){
                           $dataTosend = ['status'=>true, 'msg'=>'view all messages', 'body'=>$res];
                              echo json_encode($dataTosend); die();
                        }
                        else
                        {
                            $dataTosend = ['status'=>false, 'msg'=>'no chats found', 'body'=>''];
                              echo json_encode($dataTosend); die();
                        }                         
                    break;
                     case "add_message_class":
                           $class_id =$this->input->post('class_id');
                 $students = $this->cm->get_data('student_tbl',['class_id'=>$class_id]);
                 $test=[];
                 
                foreach($students as $student){
                     $data = array(
                        'student_id'=>$student->user_id ,
                        'massage'=> $this->input->post('message'),
                        'date'=> $this->input->post('date'),
                    'time'=> $this->input->post('time'),
                        'mst_type'=> 1
                     );
                $test = $this->cm->addRecords('massages_tbl',$data);
                }
                
                if($test){
                     $dataTosend = ['status'=>true, 'msg'=>'Message added successfully', 'body'=>''];
                      echo json_encode($dataTosend); die();
                    }else{
                      $dataTosend = ['status'=>false, 'msg'=>'failed', 'body'=>''];
                      echo json_encode($dataTosend); die();
                    }
                          
                    break;

                  default:
                     $dataTosend = ['status'=>false,'msg'=>"Invalid Requst",'body'=>''];
                        echo json_encode($dataTosend); die(); 
                }
    } 
  	
}