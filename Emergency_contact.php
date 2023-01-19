<?php
   
require APPPATH . 'libraries/REST_Controller.php';
     
class Emergency_contact extends REST_Controller {
   
    public function __construct() {
       parent::__construct();
      $this->load->model('Common_model','cm');
    }
   

    public function index_post()
     {
           $flag = $this->input->post('flag');
        
                switch ($flag) {
   
                  case "addContact":
                    
                    $name = $this->input->post('name');
                    $email = $this->input->post('email');
                    $relation = $this->input->post('relation');
                    $number = $this->input->post('number');
                    $student_id = $this->input->post('student_id');
                    if(empty($name) || empty($email) || empty($relation) || empty($number) || empty($student_id)){
                        $dataTosend = ['status'=>false, 'msg'=>'All field Required!!'];
                        echo json_encode($dataTosend); die();
                    }
                    $res = $this->cm->save('emergency_contact',['name'=>$name, 'email'=>$email, 'relation'=>$relation, 'number'=>$number, 'student_id'=>$student_id]);
                    if($res){
                        $dataTosend = ['status'=>true, 'msg'=>'Emergency contact add successfull'];
                        echo json_encode($dataTosend); die();
                    }else{
                        $dataTosend = ['status'=>false, 'msg'=>'failed'];
                        echo json_encode($dataTosend); die();
                    }
                      break;
                      
                      case "view_emergency_contact":
                          //echo 123; die;
                      $id = $this->input->post('id');
                      $student_id = $this->input->post('student_id');
                      if(!empty($student_id) && (!empty($id))){
                          $sql = "select id,name,email,relation,number,student_id from emergency_contact where student_id='$student_id' and id='$id'";
                      }else if(!empty($student_id)){
                          $sql = "select id,name,email,relation,number,student_id from emergency_contact where student_id='$student_id'";
                      }else{
                          $sql = "select id,name,email,relation,number,student_id from emergency_contact where id='$id'";
                      }
                      $res = $this->db->query($sql)->result();
                      if($res){
                          $dataTosend = ['status'=>true, 'msg'=>'view emergency_contact', 'body'=>$res];
                          echo json_encode($dataTosend); die();
                      }else{
                          $dataTosend = ['status'=>false, 'msg'=>'record not found'];
                          echo json_encode($dataTosend); die();
                      }
                      
                      break;
                      
                      case "update_contact":
                    
                    $id = $this->input->post('id');
                    $name = $this->input->post('name');
                    $email = $this->input->post('email');
                    $relation = $this->input->post('relation');
                    $number = $this->input->post('number');
                    //$student_id = $this->input->post('student_id');
                    if(empty($name) || empty($email) || empty($relation) || empty($number)){
                        $dataTosend = ['status'=>false, 'msg'=>'All field Required!!'];
                        echo json_encode($dataTosend); die();
                    }
                    $sql = ['name'=>$name, 'email'=>$email, 'relation'=>$relation, 'number'=>$number];
                    $res = $this->cm->update('emergency_contact',['id'=>$id],$sql);
                    if($res){
                        $dataTosend = ['status'=>true, 'msg'=>'Update Emergency contact successfull'];
                        echo json_encode($dataTosend); die();
                    }else{
                        $dataTosend = ['status'=>false, 'msg'=>'Please Update Contact'];
                        echo json_encode($dataTosend); die();
                    }
                      
                      break;
                      default:
                     $dataTosend = ['status'=>false,'msg'=>"Invalid Requst",'body'=>''];
                        echo json_encode($dataTosend); die(); 
                }
    } 
  	
}