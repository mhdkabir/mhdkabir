<?php
   
require APPPATH . 'libraries/REST_Controller.php';
     
class Admission extends REST_Controller {
   
    public function __construct() {
       parent::__construct();
       $this->load->model('Common_model','cm');
      
    }
   

    public function index_post()
     {
           $flag = $this->input->post('flag');
        
                switch ($flag) {
                  case "admission_get_student": 

                    //$branch_id =  $this->input->post('branch_id');
                    $data = array(); 
                    $data['list']= $td = $this->cm->get_data_orderBy('programs_tbl',[],'program_id asc');
                    //echo "<pre>"; print_r($td); die();
                    if($td){
                        $dataTosend = ['status'=>true, 'msg'=>'view admission student', 'body'=>$td];
                        echo json_encode($dataTosend); die();
                    }else{
                        $dataTosend = ['status'=>false, 'msg'=>'No data found!..', 'body'=>''];
                        echo json_encode($dataTosend); die();
                    }
                    
                    break;

                  case "admission_add_student":

                    $branch_id = $this->input->post('branch_id');
                    $student_name = $this->input->post('student_name');
                    $program_id = $this->input->post('program_id');
                    $parent_name = $this->input->post('parent_name');
                    $email = $this->input->post('email');
                    $mobile = $this->input->post('mobile');
                    $DOB = $this->input->post('DOB');
                    $age = $this->input->post('age');
                    $school_status = $this->input->post('school_status');
                    $date = date('Y-m-d'); 
                    $add = ['student_name'=>$student_name,'program_id'=>$program_id,'parent_name'=>$parent_name,
                             'email'=>$email, 'mobile'=>$mobile,'DOB'=>$DOB,'age'=>$age,'school_status'=>$school_status,
                           'branch_id'=>$branch_id, 'date'=> $date];
                    
                            $data = array(); 
                        $res = $this->cm->save('temp_student_tbl',$add);
                     
                   if($res)
                   {
                        $dataTosend = ['status'=>true, 'msg'=>'success_msg', 'Student add successfully', 'body'=>''];
                        echo json_encode($dataTosend); die();
                   }else{
                        $dataTosend = ['status'=>true, 'msg'=>'error_msg', 'Student note add..', 'body'=>$res];
                        echo json_encode($dataTosend); die();
                   }
                   
                    break;

                    case "view_admissions_from":

                      $form_id = $this->input->post('form_id');
                      $res = $this->cm->get_data('all_form_tbl',['form_id'=>$form_id]);
                      //echo $res;
                      if($res){
                        $dataTosend = ['status'=>true, 'msg'=>'view admissions from', 'body'=>$res];
                        echo json_encode($dataTosend); die();
                      }else{
                        $dataTosend = ['status'=>false, 'msg'=>'record not fount....', 'body'=>''];
                        echo json_encode($dataTosend); die();
                      }

                    break;

                    case "add_form_data":

                     $form_type = $this->input->post('form_type');
                     $pdf_name = $this->cm->file_upload('pdf_path', 'assets/forms/');
                     $branch_id = $this->input->post('branch_id');
                     $date = date('y-m-d');
                     if(empty($form_type)){
                            $this->session->set_flashdata('All field Required!!');
                            $server = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
                            redirect($server);die();
                     } 
                     $res = $this->cm->save('all_form_tbl',['form_type'=>$form_type, 'pdf_path'=>$pdf_name, 'branch_id'=>$branch_id, 'date'=>date('Y-m-d')]);
                     //echo $res;
                     if($res){
                       $dataTosend = ['status'=>true, 'msg'=>'success_msg', 'Form add successfully', 'body'=>''];
                       echo json_encode($dataTosend); die();
                     }else{
                       $dataTosend = ['status'=>false, 'msg'=>'error_msg', 'Form note add..', 'body'=>''];
                       echo json_encode($dataTosend); die();
                     }

                     break;

                    case "update_form_data":

                     $form_id = $this->input->post('form_id');
                     $form_type = $this->input->post('form_type');
                     $pdf_name = $this->cm->file_upload('pdf_path', 'assets/forms/');
                     $branch_id = $this->input->post('branch_id');
                     $date = date('y-m-d');
                     if(empty($form_type)){
                            $this->session->set_flashdata('All field Required!!');
                            $server = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
                            redirect($server);die();
                     } 
                     //$res = $this->cm->update('all_form_tbl',['form_type'=>$form_type, 'pdf_path'=>$pdf_name, 'branch_id'=>$branch_id, 'date'=>date('Y-m-d')]);
                     $whr = ['form_type'=>$form_type, 'pdf_path'=>$pdf_name, 'branch_id'=>$branch_id];
                     $res = $this->cm->update('all_form_tbl',['form_id'=>$form_id],$whr);
                     //echo $res;
                     if($res){
                       $dataTosend = ['status'=>true, 'msg'=>'success_msg', 'Form update successfully', 'body'=>''];
                       echo json_encode($dataTosend); die();
                     }else{
                       $dataTosend = ['status'=>false, 'msg'=>'error_msg', 'Form note add..', 'body'=>''];
                       echo json_encode($dataTosend); die();
                     }

                    break;

                    case "Delete_form_data":
                      $form_id = $this->input->post('form_id');
                      if(empty($form_id)){
                            $server = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
                            redirect($server);die();
                      }
                      $del = $this->cm->delete('all_form_tbl',['form_id'=>$form_id]);
                      if($del){
                        $dataTosend = ['status'=>true, 'msg'=>'success_msg', 'Form delete successfully', 'body'=>''];
                        echo json_encode($dataTosend); die();
                      }else{
                        $dataTosend = ['status'=>false, 'msg'=>'error_msg', 'Form note delete..', 'body'=>''];
                        echo json_encode($dataTosend); die();
                      }

                    break;

                    case "view_programs":

                      $program_id = $this->input->post('program_id');
                      $res = $this->cm->get_data('programs_tbl',['program_id'=>$program_id]);
                      //echo $res;
                      if($res){
                        $dataTosend = ['status'=>true, 'msg'=>'view program data', 'body'=>$res];
                        echo json_encode($dataTosend); die();
                      }else{
                        $dataTosend = ['status'=>false, 'msg'=>'failed', 'body'=>''];
                        echo json_encode($dataTosend); die();
                      }

                    break;

                    case "add_class":

                      $branch_id = $this->input->post('branch_id');
                      $name = $this->input->post('name');
                      $min_age = $this->input->post('min_age');
                      $max_age = $this->input->post('max_age');
                      $max_occupancy = $this->input->post('max_occupancy');
                      $start_date = $this->input->post('start_date');
                      $end_date = $this->input->post('end_date');
                      $description = $this->input->post('description');
                      if(empty($name) || empty($min_age) || empty($max_age) || empty($max_occupancy) || empty($start_date) || empty($end_date) || empty($description)){
                            $server = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
                            redirect($server);die();
                      }
                      $res = $this->cm->save('programs_tbl',['branch_id'=>$branch_id, 'name'=>$name, 'min_age'=>$min_age, 'max_age'=>$max_age,
                    'max_occupancy'=>$max_occupancy, 'start_date'=>$start_date, 'end_date'=>$end_date, 'description'=>$description]);
                    if($res){
                      $dataTosend = ['status'=>true, 'msg'=>'Program add successfully', 'body'=>''];
                      echo json_encode($dataTosend); die();
                    }else{
                      $dataTosend = ['status'=>false, 'msg'=>'can not added program..', 'body'=>''];
                      echo json_encode($dataTosend); die();
                    }


                    break;

                    case "view_total_student":

                      $branch_id = $this->input->post('branch_id');
                      //$school_status = $this->input->post('school_status');
                      // $sum = "SELECT school_status, COUNT(school_status) AS count  FROM `temp_student_tbl` 
                      // WHERE `branch_id` = '$branch_id' GROUP BY school_status"; 
                      // $res = $this->db->query($sum)->result(); 
                      // $arr = array(); 
                      // foreach($res as $val){
                      //     $arr[$val->school_status] = $val->count;
                      // }

                      $data = array();
                      $data['applied'] = $this->cm->count_row('temp_student_tbl',['branch_id'=>$branch_id, 'school_status'=>'applied']);
                      $data['Waitlist'] = $this->cm->count_row('temp_student_tbl',['branch_id'=>$branch_id, 'school_status'=>'Waitlist']);
                      $data['Toured'] = $this->cm->count_row('temp_student_tbl',['branch_id'=>$branch_id, 'school_status'=>'Toured']);
                      $data['Prospects'] = $this->cm->count_row('temp_student_tbl',['branch_id'=>$branch_id, 'school_status'=>'Prospects']);

                      if($data){
                          $dataTosend = ['status'=>true, 'msg'=>'view total student', 'body'=>$data];
                          echo json_encode($dataTosend); die();
                      }else{
                          $dataTosend = ['status'=>true, 'msg'=>'can not record..', 'body'=>''];
                          echo json_encode($dataTosend); die();
                      }
                    break;

                    case "view_admission_dashboard":

                      $this->db->select('s.temp_s_id, s.student_name, s.school_status, s.branch_id,
                      s.date, s.age , p.name, p.min_age, p.max_age');
                      $this->db->from('temp_student_tbl as s');
                      $this->db->join('programs_tbl as p' ,'p.program_id  = s.temp_s_id');
                      //$this->db->where('p.branch_id = s.branch_id');
                      $query = $this->db->get();
                      
                      $res=$query->result();
                      //echo "<pre>";print_r($res);die;   
                      if($res){
                        $dataTosend = ['status'=>true, 'msg'=>'view admission dashboard', 'body'=>$res];
                        echo json_encode($dataTosend); die();
                      }else{
                        $dataTosend = ['status'=>true, 'msg'=>'can not record...', 'body'=>''];
                        echo json_encode($dataTosend); die();
                      }

                    break;

                    case "update_admission_dashboard":

                     $temp_s_id = $this->input->post('temp_s_id');
                     //$student_name = $this->input->post('student_name');
                     $school_status = $this->input->post('school_status');
                     $branch_id = $this->input->post('branch_id');
                     //$date = $this->input->post('date');
                     //$age = $this->input->post('age');
                    //  $name = $this->input->post('name');
                    //  $min_age = $this->input->post('min_age');
                    //  $max_age = $this->input->post('max_age');
                     
                     if(empty($school_status) || empty($branch_id)){
                            $this->session->set_flashdata('All field Required!!');
                            $server = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
                            redirect($server);die();
                     } 

                     $whr = ['temp_s_id'=>$temp_s_id, 'school_status'=>$school_status, 'branch_id'=>$branch_id];
                      //echo $whr;

                     $res = $this->cm->update('temp_student_tbl',['temp_s_id'=>$temp_s_id],$whr);
                     
                     if($res){
                       $dataTosend = ['status'=>true, 'msg'=>'success_msg', 'Form update successfully', 'body'=>$res];
                       echo json_encode($dataTosend); die();
                     }else{
                       $dataTosend = ['status'=>false, 'msg'=>'error_msg', 'Form note add..', 'body'=>''];
                       echo json_encode($dataTosend); die();
                     }

                    break;

                    case "add_message":
                      // print_r($this->input->post());die;
                            //$message_id = $this->input->post('message_id');
                          //  echo  $_FILES['document_attached']['name'];die;
                            $student_id = $this->input->post('student_id');
                            $massage = $this->input->post('massage');
                            $date = $this->input->post('date');
                            $time = $this->input->post('time');
                            $img_name = $this->cm->file_types('document_attached');
                            $img = $this->cm->file_upload('document_attached','assets/message_img/');
                            
                            if(empty($massage )|| empty($img_name) || empty($img)){
                              $this->session->set_flashdata('All field Required!!');
                              $server = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
                              redirect($server);die();
                            }
                            //$res = $this->cm->save('all_form_tbl',['form_type'=>$form_type, 'pdf_path'=>$pdf_name, 'branch_id'=>$branch_id, 'date'=>date('Y-m-d')]);
                            
                            $res  = $this->cm->save('massages_tbl',['student_id'=>$student_id, 'file_type'=>$img_name, 'massage'=>$massage, 'document_attached'=>$img, 'date'=>$date, 'time'=>$time]);
                            //echo $res;
                            //echo "<pre>"; print_r($res);
                            if($res){
                                $dataTosend = ['status'=>true, 'msg'=>'success_msg', 'add message successfully', 'body'=>$res];
                                echo json_encode($dataTosend); die();
                            }else{
                                $dataTosend = ['status'=>false, 'msg'=>'error_msg', 'please message add...', 'body'=>''];
                                echo json_encode($dataTosend); die();
                            }

                    break;

                
                  default:
                     $dataTosend = ['status'=>false,'msg'=>"Invalid Requst",'body'=>''];
                        echo json_encode($dataTosend); die(); 
                }
    } 
  	
}