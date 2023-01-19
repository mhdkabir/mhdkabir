<?php
   
require APPPATH . 'libraries/REST_Controller.php';
     
class Room extends REST_Controller {
   
    public function __construct() {
       parent::__construct();
       $this->load->model('Common_model','cm');
      
    }
   

    public function index_post()
     {
           $flag = $this->input->post('flag');
        
                switch ($flag) {
                  case "add_section": 
                    
                    $class_id = $this->input->post('class_id');
                    $branch_id = $this->input->post('branch_id');
                    $name = $this->input->post('name');
                    $max_occupancy = $this->input->post('max_occupancy');
                    if(empty($class_id) || empty($branch_id) || empty($name) || empty($max_occupancy)){
                        $dataTosend = ['status'=>true, 'msg'=>'All field Required!!'];
                        echo json_encode($dataTosend); die();
                    }
                    $res = $this->cm->get_data('section_tbl',['class_id'=>$class_id, 'branch_id'=>$branch_id, 'name'=>$name, 'max_occupancy'=>$max_occupancy]);
                    if($res){
                        $dataTosend = ['status'=>true, 'msg'=>'allready record save', 'body'=>''];
                        echo json_encode($dataTosend); die();
                    }else{
                        $res2 = $this->cm->save('section_tbl',['class_id'=>$class_id, 'branch_id'=>$branch_id, 'name'=>$name, 'max_occupancy'=>$max_occupancy]);
                        if($res2){
                            $dataTosend = ['status'=>true, 'msg'=>'record addedd successfull', 'body'=>''];
                            echo json_encode($dataTosend); die();
                        }else{
                            $dataTosend = ['status'=>false, 'msg'=>'record can not addedd', 'body'=>''];
                            echo json_encode($dataTosend); die();
                        }
                        
                    }
                    break;
                    
                    case "update_section":
                        $section_id = $this->input->post('section_id');
                        $name = $this->input->post('name');
                        $max_occupancy = $this->input->post('max_occupancy');
                        if(empty($name) || empty($max_occupancy)){
                            $dataTosend = ['status'=>false, 'msg'=>'All field Required'];
                            echo json_encode($dataTosend); die();
                        }
                        $sql = ['name'=>$name, 'max_occupancy'=>$max_occupancy];
                        $res = $this->cm->update('section_tbl',['section_id'=>$section_id],$sql);
                        if($res){
                            $dataTosend = ['status'=>true, 'msg'=>'Update Section Successfull'];
                            echo json_encode($dataTosend); die();
                        }else{
                            $dataTosend = ['status'=>false, 'msg'=>'Update not found'];
                        }
                    
                    break;
                    
                    case "delete_section":
                        $section_id = $this->input->post('section_id');
                        $delete = $this->cm->delete('section_tbl',['section_id'=>$section_id]);
                        if($delete){
                            $dataTosend = ['status'=>true, 'msg'=>'Delete section successfull'];
                            echo json_encode($dataTosend); die();
                        }else{
                            $dataTosend = ['status'=>false, 'msg'=>'section not deleted'];
                            echo json_encode($dataTosend); die();
                        }
                    break;

                  case "view_section":
                    $branch_id = $this->input->post('branch_id');
                    $class_id = $this->input->post('class_id');
                    if(empty($class_id) || empty($branch_id)){
                        $dataTosend = ['status'=>true, 'msg'=>'All field Required!!'];
                        echo json_encode($dataTosend); die();
                    }
                    $whr = array();

                    $whr['branch_id'] = $branch_id;
                    if($class_id){
                        $whr['class_id'] = $class_id;
                    }
                   

                    $result = $this->cm->get_data('section_tbl',$whr);
                    if($result){
                        $dataTosend = ['status'=>true, 'msg'=>'all record show', 'body'=>$result];
                        echo json_encode($dataTosend); die();
                    }else{
                        $dataTosend = ['status'=>false, 'msg'=>'student not found'];
                        echo json_encode($dataTosend); die();
                    }

                    break;
                    
                    case "viewSection":
                        $branch_id = $this->input->post('branch_id');
                        $class_id = $this->input->post('class_id');
                            if(empty($branch_id) || empty($class_id)){
                                $dataTosend = ['status'=>false, 'msg'=>'All field Required'];
                                echo json_encode($dataTosend); die();
                            }
                            $sql = "select s.section_id,s.class_id,s.branch_id,s.name,s.max_occupancy,j.name as class_name from section_tbl as s join class_tbl as j 
                            on s.class_id = j.class_id where s.branch_id='$branch_id' and s.class_id='$class_id'";

                        $res = $this->db->query($sql)->result();
                        //echo "<pre>"; print_r($res); die();
                        if($res){
                            $dataTosend = ['status'=>true, 'msg'=>'view section', 'body'=>$res];
                            echo json_encode($dataTosend); die();
                        }else{
                            $dataTosend = ['staus'=>false, 'msg'=>'student section not found'];
                            echo json_encode($dataTosend); die();
                        }
                    break;
                
                  default:
                     $dataTosend = ['status'=>false,'msg'=>"Invalid Requst",'body'=>''];
                        echo json_encode($dataTosend); die(); 
                }
    } 
  	
}