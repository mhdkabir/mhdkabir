<?php
   
require APPPATH . 'libraries/REST_Controller.php';
     
class Blog extends REST_Controller {
   
    public function __construct() {
       parent::__construct();
      $this->load->model('Common_model','cm');
    }
   

    public function index_post()
     {
           $flag = $this->input->post('flag');
        
                switch ($flag) {
                  case "get_blog":

                    //$id = $this->uri->segment(3);
                    // $data = [];
                    // $data['b_logs']= $rs = $this->cm->get_data('blog_tbl',[]);
                    // $data = array(); 
                    $blog_id = $this->input->post('blog_id');
                    $res = $this->cm->get_data('blog_tbl',['blog_id'=>$blog_id]);
                    //echo "<pre>"; print_r($rs); die();
                    if($res){
                        $dataTosend = ['status'=>true, 'msg'=>'view blog', 'body'=>$res];
                        echo json_encode($dataTosend); die();
                    }else{
                        $dataTosend = ['status'=>false, 'msg'=>'No data found!..', 'body'=>''];
                        echo json_encode($dataTosend); die();
                    }

                  
                      break;

                      case "add_blog":
                       
                        $school_id = $this->input->post('school_id');
                        $branch_id = $this->input->post('branch_id');
                        $title = $this->input->post('title');
                        $description = $this->input->post('description');
                      //  $featured_Image = $this->input->post('featured_Image');
                        $tags = $this->input->post('tags');
                        $img =  $this->cm->file_upload('featured_Image', 'assets/blog/')  ;

                        if(empty($title) || empty($description))
                        {
                            $this->session->set_flashdata('All field Required!!');
                            $server = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
                            redirect($server);die();
                        }
                        $res = $this->cm->save('blog_tbl',['title'=>$title, 'description'=>$description, 'featured_Image'=>$img, 'tags'=>$tags, 'school_id'=>$school_id, 'branch_id'=>$branch_id]);
                        //echo $res;
                        if($res){
                            $dataTosend = ['status'=>true, 'msg'=>'Blog add successfully', 'body'=>''];
                            echo json_encode($dataTosend); die();
                        }else{
                            $dataTosend = ['status'=>false, 'msg'=>'error_msg', 'something went wrong please try again..', 'body'=>''];
                            echo json_encode($dataTosend); die();
                        }
                            

                        
                        break;

                         case "update_blog":


                        $school_id = $this->input->post('school_id');
                        $branch_id = $this->input->post('branch_id');
                        $title = $this->input->post('title');
                        $description = $this->input->post('description');
                        $tags = $this->input->post('tags');
                        $blog_id = $this->input->post('blog_id');
                        $img =  $this->cm->file_upload('featured_Image', 'assets/blog/')  ;

                        $whr = ['title'=>$title, 'description'=>$description, 'featured_Image'=>$img, 'tags'=>$tags, 'school_id'=>$school_id, 'branch_id'=>$branch_id];
                        $res = $this->cm->update('blog_tbl',['blog_id'=>$blog_id],$whr);
                        //echo $res;
                        if($res){
                            $dataTosend = ['status'=>true, 'msg'=>'Blog update successfully', 'body'=>$res];
                            echo json_encode($dataTosend); die();
                        }else{
                            $dataTosend = ['status'=>false, 'msg'=>'error_msg', 'something went wrong please try again..', 'body'=>''];
                            echo json_encode($dataTosend); die();
                        }
                        break;

                        case "delete_blog":
                        
                        // $id = $this->uri->segment(3);
                        // $res = $this->cm->delete('blog_tbl',['blog_id'=>$id]);
                        $blog_id = $this->input->post('blog_id');
                        if(empty($blog_id)){
                            $dataTosend = ['status'=>false, 'msg'=>'Enter Id'];
                            echo json_encode($dataTosend); die();
                        }
                        $res = $this->cm->delete('blog_tbl',['blog_id'=>$blog_id]);
                        //echo $res;
                        if($res){
                            $dataTosend = ['status'=>true, 'msg'=>'success_msg', 'Blog deleted successfully', 'body'=>''];
                            echo json_encode($dataTosend); die();
                         }else{
                            $dataTosend = ['status'=>false, 'msg'=>'error_msg', 'something went wrong please try again..', 'body'=>''];
                            echo json_encode($dataTosend); die();
                        }

                      break;
                      default:
                     $dataTosend = ['status'=>false,'msg'=>"Invalid Requst",'body'=>''];
                        echo json_encode($dataTosend); die(); 
                }
    } 
  	
}