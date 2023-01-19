<?php
   
require APPPATH . 'libraries/REST_Controller.php';
     
class Add_qualification extends REST_Controller {
   
    public function __construct() {
       parent::__construct();
      $this->load->model('Common_model','cm');
    }
   

    public function index_post()
     {
           $flag = $this->input->post('flag');
        
                switch ($flag) {
   
                  case "add_qualification":
                    
                    $staff_id = $this->input->post('staff_id');
                    $degree_type = $this->input->post('degree_type');
                    $certification_name = $this->input->post('certification_name');
                    $percentage = $this->input->post('percentage');
                    $from_year = $this->input->post('from_year');
                    $to_year = $this->input->post('to_year');
                    $staff_user_id = $this->input->post('staff_user_id');
                    $doc_name = $this->cm->file_upload('attachment_doct', 'assets/Doc_img/');
                    $notes = $this->input->post('notes');
                    if(empty($degree_type) || empty($certification_name) || empty($percentage) || empty($from_year) || empty($to_year) ){
                        $dataTosend = ['status'=>false, 'msg'=>'All field Required!!'];
                        echo json_encode($dataTosend); die();
                    }
                    $res = $this->cm->save('staff_certification_tbl',['degree_type'=>$degree_type, 'certification_name'=>$certification_name, 'percentage'=>$percentage, 'from_year'=>$from_year, 
                    'to_year'=>$to_year, 'attachment_doct'=>$doc_name, 'notes'=>$notes, 'staff_user_id'=>$staff_user_id]);
                    if($res){
                        $dataTosend = ['status'=>true, 'msg'=>'add qualification successfull'];
                        echo json_encode($dataTosend); die();
                    }else{
                        $dataTosend = ['staus'=>false, 'msg'=>'failed'];
                        echo json_encode($dataTosend); die();
                    }
                    

                      break;
                      
                      case "view_qualification":
                          //$id = $this->input->post('id');
                          $staff_user_id= $this->input->post('staff_user_id');
                          $sql = "select id,degree_type,certification_name,percentage,from_year,to_year,attachment_doct,notes,staff_user_id from staff_certification_tbl where staff_user_id='$staff_user_id'";
                          $res = $this->db->query($sql)->result();
                          //echo "<pre>"; print_r($res); die;
                          foreach($res as $val){
                                    $val->attachment_doct = ($val->attachment_doct)? base_url('assets/Doc_img/').$val->attachment_doct :'';
                            }
                          if($res){
                              $dataTosend = ['status'=>true, 'msg'=>'view qualification', 'body'=>$res];
                              echo json_encode($dataTosend); die();
                          }else{
                              $dataTosend = ['status'=>false, 'msg'=>'record not found'];
                              echo json_encode($dataTosend); die();
                          }
                      
                      break;
                      
                      case "update_qualification":
                          //print_r($this->input->post()); die;
                          $id = $this->input->post('id');
                          $degree_type = $this->input->post('degree_type');
                          $certification_name = $this->input->post('certification_name');
                          $percentage = $this->input->post('percentage');
                          $from_year = $this->input->post('from_year');
                          $to_year = $this->input->post('to_year');
                          $doc_name = $this->cm->file_upload('attachment_doct', 'assets/Doc_img/');
                          $notes = $this->input->post('notes');
                          if(empty($degree_type) || empty($certification_name) || empty($percentage) || empty($from_year) || empty($to_year) || empty($doc_name) || empty($notes)){
                              $dataTosend = ['status'=>true, 'msg'=>'Please updated'];
                          }
                          $whr = ['degree_type'=>$degree_type, 'certification_name'=>$certification_name, 'percentage'=>$percentage, 'from_year'=>$from_year, 'to_year'=>$to_year, 'attachment_doct'=>$doc_name, 'notes'=>$notes];
                          $res = $this->cm->update('staff_certification_tbl',['id'=>$id],$whr);
                          if($res){
                              $dataTosend = ['status'=>true, 'msg'=>'Update Qualification Successfull'];
                              echo json_encode($dataTosend); die();
                          }else{
                              $dataTosend = ['status'=>false, 'msg'=>'Qulification Note Updated'];
                              echo json_encode($dataTosend); die();
                          }
                      
                      break;
                      
                      
                      case "delete_qualification":
                      
                      $id = $this->input->post('id');
                    
                      $sql = "DELETE FROM `staff_certification_tbl` WHERE id='$id'";
                      $res = $this->db->query($sql);
                      if($res){
                          $dataTosend = ['status'=>true, 'msg'=>'qualification delete successfull'];
                          echo json_encode($dataTosend); die();
                      }else{
                          $dataTosend = ['status'=>false, 'msg'=>'qualification not delete'];
                          echo json_encode($dataTosend); die();
                      }
                      break;
                      
                      case "view_degree":
                          
                          //$this->cm->get_data('qualification_table',[]);
                          $sql = "select * from staff_certification_tbl GROUP BY degree_type";
                          $res = $this->db->query($sql)->result();
                          if($res){
                              $dataTosend = ['status'=>true, 'msg'=>'view all degree', 'body'=>$res];
                              echo json_encode($dataTosend); die();
                          }else{
                              $dataTosend = ['status'=>false, 'msg'=>'record not found'];
                              echo json_encode($dataTosend); die();
                          }
                      break;
                      default:
                     $dataTosend = ['status'=>false,'msg'=>"Invalid Requst",'body'=>''];
                        echo json_encode($dataTosend); die(); 
                }
    } 
  	
}