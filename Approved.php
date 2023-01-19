<?php
   
require APPPATH . 'libraries/REST_Controller.php';
     
class Approved extends REST_Controller {
   
    public function __construct() {
       parent::__construct();
      $this->load->model('Common_model','cm');
    }
   

    public function index_post()
     {
           $flag = $this->input->post('flag');
        
                switch ($flag) {
                  case "add_approved":

                    $name = $this->input->post('name');
                    $email = $this->input->post('email');
                    $mobile = $this->input->post('mobile');
                    $student_id = $this->input->post('student_id');
                    $student_relational = $this->input->post('student_relational');
                    if(empty($name) || empty($email) || empty($mobile) || empty($student_id) || empty($student_relational)){
                        $server = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
                        redirect($server); die();
                    }
                    $res = $this->cm->save('user_tbl',['name'=>$name, 'email'=>$email, 'mobile'=>$mobile]);
                    if($res){
                        $this->cm->save('parents_tbl',['student_id'=>$student_id, 'student_relational'=>$student_relational, 'guardian_id'=>$res]);
                        $dataTosend = ['status'=>true, 'msg'=>'add approved successfull', 'body'=>''];
                        echo json_encode($dataTosend); die();
                    }else{
                        $dataTosend = ['status'=>false, 'msg'=>'failed', 'body'=>''];
                        echo json_encode($dataTosend); die();
                    }
                   
                      break;

                      case "view_approved_pickup":

                            $student_id = $this->input->post('student_id');
                            $class_id = $this->input->post('class_id');
                            $section_id = $this->input->post('section_id');
                            $branch_id =  $this->input->post('branch_id');
                            $whr = '';                                              
                            if(!empty($user_id)){                            
                                $whr .= "and a.user_id ='$user_id'";
                            }else{                             
                                                                                                        
                            if( !empty($class_id)){
                                    $whr.= "and a.class_id ='$class_id'";
                            }                
                                                                
                            if( !empty($section_id)){
                                    $whr .= "and a.section_id ='$section_id'";
                            }
                            if( !empty($branch_id)){
                                $whr .= "and a.branch_id ='$branch_id'";
                            }
                            }
                            
                            
                            $q = "select  b.user_id,b.name,b.mobile,b.address,b.email from student_tbl as a join user_tbl as b on a.user_id = b.user_id 
                                        where a.branch_id ='$branch_id' $whr  " ;
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
                                        where p.student_id = '$id' and p.student_relational = 'Approved Pickup'  ";
                                $arr['data']   =  $this->db->query($qq)->result();
                            $job[] = (object)$arr;  
                            }
                            if($job){
                                $dataTosend = ['status'=>true, 'msg'=>'approved record', 'bpdy'=>$job];
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