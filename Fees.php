<?php
   
require APPPATH . 'libraries/REST_Controller.php';
     
class Fees extends REST_Controller {
   
    public function __construct() {
       parent::__construct();
      $this->load->model('Common_model','cm');
    }
   

   public function index_post()
     {
           $flag = $this->input->post('flag');
        
                switch ($flag){
   
                  case "view_fees":
                    
                    $class_id = $this->input->post('class_id');
                    if(empty($class_id)){
                        $dataTosend = ['status'=>false, 'msg'=>'All field required'];
                        echo json_encode($dataTosend); die();
                    }
                    else{
                    $sql = "SELECT f.id, f.class_id, f.title,f.amount, f.notification_duration, f.due_date, ct.name FROM fees as f 
                       join class_tbl as ct on ct.class_id=f.class_id WHERE f.class_id='$class_id'";
                       $res = $this->db->query($sql)->result();

                       if($res){
                           $dataTosend = ['status'=>true, 'msg'=>'view fees', 'body'=>$res];
                           echo json_encode($dataTosend); die();
                       }else{
                           $dataTosend = ['status'=>false, 'msg'=>'fees not found'];
                           echo json_encode($dataTosend); die();
                       }
                    }
                    
                      break;
                      
                    case "paid_fees":
                    
                    $student_id = $this->input->post('student_id');
                    $parent_id = $this->input->post('parent_id');
                    $fees_id = $this->input->post('fees_id');
                    $amount_paid = $this->input->post('amount_paid');
                    //$amount_paid = $this->input->post('amount_paid');
                    $created_at = date('Y-m-d h-m-s');
                    $updated_at = date('Y-m-d h-m-s');
                    if(empty($student_id) || empty($parent_id) || empty($fees_id) || empty($amount_paid)){
                        $dataTosend = ['status'=>false, 'msg'=>'All field required'];
                        echo json_encode($dataTosend); die();
                    }
                    $res = $this->cm->save('receipts',['student_id'=>$student_id, 'parent_id'=>$parent_id, 'fees_id'=>$fees_id, 'amount_paid'=>$amount_paid,
                    'created_at'=>$created_at, 'updated_at'=>$updated_at]);
                    
                    if($res){
                        $dataTosend = ['status'=>true, 'msg'=>'fees added successfull'];
                        echo json_encode($dataTosend); die();
                    }else{
                        $dataTosend = ['status'=>false, 'msg'=>'fees not added'];
                        echo json_encode($dataTosend); die();
                    }
                    
                    break;
                    
                    
                    case "view_paid_fees":
                    
                    $parent_id = $this->input->post('parent_id');
                    $student_id = $this->input->post('student_id');
                    if(empty($parent_id) || empty($student_id)){
                        $dataTosend = ['status'=>false, 'msg'=>'All field required'];
                        echo json_encode($dataTosend); die();
                    }
                    // $sql = "SELECT `id`, `student_id`, `parent_id`, `fees_id`, `amount_paid`, `created_at`, `updated_at` FROM receipts where parent_id='$parent_id'";
                    // $res = $this->db->query($sql)->result();
                    $sql = "SELECT r.id,ut.image , r.student_id, r.parent_id, r.amount_paid, r.fees_id, f.amount, 
                    r.created_at, r.updated_at FROM receipts as r  join user_tbl as ut on ut.user_id = r.student_id join fees as f on r.fees_id=f.id where parent_id='$parent_id' and student_id='$student_id'";
                    $res = $this->db->query($sql)->result();
                    $job=array();
                  foreach($res as $result):
                  {
                         $test_arr=array();
                         $test_arr['student_id']=$result->student_id;
					     $test_arr['parent_id']=$result->parent_id;
					     $test_arr['image']=base_url('assets/images/').$result->image;
					     $test_arr['fees_id']=$result->fees_id;
                         $test_arr['paid_amount']=$result->paid_amount;
                         $test_arr['total_fees']=$result->total_fees;
                         $test_arr['due_fees']=$result->total_fees-$result->paid_amount;
                         $test_arr['created_at']=$result->created_at;
                         $test_arr['updated_at']=$result->updated_at;
                         $job[] = $test_arr; 
                  }
                    if($job){
                        $dataTosend = ['status'=>true, 'msg'=>'view paid fees', 'body'=>$job];
                        echo json_encode($dataTosend); die();
                    }else{
                        $dataTosend = ['status'=>false, 'msg'=>'fees not found'];
                        echo json_encode($dataTosend); die();
                    }
                    
                    
                    break;
                    endforeach;
                    
                    case "view_due_fees":
                    
                    $parent_id = $this->input->post('parent_id');
                    $student_id = $this->input->post('student_id');
                    if(empty($parent_id) || empty($student_id)){
                        $dataTosend = ['status'=>false, 'msg'=>'All field required'];
                        echo json_encode($dataTosend); die();
                    }
                    $sql = "SELECT r.id, ut.name, ut.image, r.student_id, r.parent_id, sum(r.amount_paid) as paid_amount, f.amount as total_fees FROM receipts as r join fees as f on r.fees_id=f.id
                    join user_tbl as ut on ut.user_id=r.student_id where parent_id='$parent_id' and student_id='$student_id'";

                     $sql2 = "SELECT ut.user_id, ut.name , ut.image ,  sum(r.amount_paid) as paid_amount , sum(f.amount) as total_fees
                                    FROM student_tbl as st 
                                    left join fees as f  on st.class_id = f.class_id left join receipts as r on r.fees_id = f.id
                                    join user_tbl as ut on ut.user_id = st.user_id WHERE st.user_id='$student_id'";
                    
                    $res = $this->db->query($sql2)->result();
                    $job=array();
                                                                      /* [user_id] => 236
                                                                    [name] => ak 2
                                                                    [image] => 489216.jpg
                                                                    [paid_amount] => 
                                                                    [total_fees] => */
                   ///echo "<pre>"; print_r($res); die(); 
                    
                   foreach($res as $result)
                   {
                         $test_arr=array();
                         $test_arr['name']=$result->name;
                         $test_arr['student_id']=$result->user_id;
					   //  $test_arr['parent_id']=$result->parent_id;
                         $test_arr['total_fees']=$result->total_fees;
                         $test_arr['due_fees']=$result->total_fees - $result->paid_amount;
                         $test_arr['image'] = base_url('assets/images/').$result->image;
                         $job[] = $test_arr;
                   }
                   
                    if($job){
                        $dataTosend = ['status'=>true, 'msg'=>'view paid fees', 'body'=>$job];
                        echo json_encode($dataTosend); die();
                    }else{
                        $dataTosend = ['status'=>false, 'msg'=>'fees not found'];
                        echo json_encode($dataTosend); die();
                    }
                    
                    
                    break;
                      case "view_all_paid_fees":
                    
                   $branch_id = $this->input->post('branch_id');
                   $class_id = $this->input->post('class_id') ?: 0;
                   if($class_id){$classcon = "and s.class_id = '$class_id'";}else{$classcon = "";}
                   $section_id = $this->input->post('section_id') ?: 0;
                   if($section_id){$sectioncon = "and s.section_id = '$section_id'";}else{$sectioncon = "";}
                   
                    // $sql = "SELECT `id`, `student_id`, `parent_id`, `fees_id`, `amount_paid`, `created_at`, `updated_at` FROM receipts where parent_id='$parent_id'";
                   // $res = $this->db->query($sql)->result();

                    $sql = "SELECT r.id,ut.image , ut.name , r.student_id, r.parent_id, r.amount_paid as paid_amount, r.fees_id, f.amount as total_fees,   
                    r.created_at, r.updated_at FROM receipts as r  join user_tbl as ut on ut.user_id=r.student_id join student_tbl s on s.user_id = ut.user_id
                    join fees as f on r.fees_id=f.id where ut.branch_id='$branch_id' ".$classcon.$sectioncon; 
                    $res = $this->db->query($sql)->result();
                    $job=array();
                  foreach($res as $result):
                  {
                         $test_arr=array();
                         $test_arr['student_id']=$result->student_id;
                         $test_arr['student_name']=$result->name;
					     $test_arr['parent_id']=$result->parent_id;
					     $test_arr['image']=base_url('assets/images/').$result->image;
					     $test_arr['fees_id']=$result->fees_id;
                         $test_arr['paid_amount']=$result->paid_amount;
                         $test_arr['total_fees']=$result->total_fees;
                         $test_arr['due_fees']=$result->total_fees-$result->paid_amount;
                         $test_arr['created_at']=$result->created_at;
                         $test_arr['updated_at']=$result->updated_at;
                         $job[] = $test_arr; 
                  }
                    if($job){
                        $dataTosend = ['status'=>true, 'msg'=>'view paid fees', 'body'=>$job];
                        echo json_encode($dataTosend); die();
                    }else{
                        $dataTosend = ['status'=>false, 'msg'=>'fees not found'];
                        echo json_encode($dataTosend); die();
                    }
                    break;
                endforeach;
                      case "view_all_due_fees":
                        $job=array();
                    $branch_id = $this->input->post('branch_id');
                    if(empty($branch_id)){
                        $dataTosend = ['status'=>false, 'msg'=>'All field required'];
                        echo json_encode($dataTosend); die();
                    }
                   $students = $this->cm->get_data('user_tbl',['branch_id'=>$branch_id,'roll'=>4]);
                  foreach($students as $student){
                    $sql = "SELECT ut.user_id, ut.name , ut.image ,  sum(r.amount_paid) as paid_amount , sum(f.amount) as total_fees
                                    FROM student_tbl as st 
                                    left join fees as f  on st.class_id = f.class_id left join receipts as r on r.fees_id = f.id
                                    join user_tbl as ut on ut.user_id = st.user_id WHERE st.user_id='$student->user_id'";
                            
                    $res = $this->db->query($sql)->result();
                
                   foreach($res as $result)
                   { 
                       
                         $test_arr=array();
                         $test_arr['name']=$result->name;
                         $test_arr['student_id']=$result->user_id;
                         $getparent = "select coalesce(p.guardian_id,'') as parent_id from parents_tbl as p   
                         join user_tbl as p_tbl on p.guardian_id = p_tbl.user_id  
                         where p.student_id = '280' and p.student_relational = 'Parent'  LIMIT 1";
                         $resparent = $this->db->query($getparent)->result();
                         foreach($resparent as $resultparent)
                         {
                             $test_arr['parent_id']=$resultparent->parent_id;
                         }
                         $test_arr['total_fees']= is_null($result->total_fees)?'0':$result->total_fees;
                         $test_arr['paid_fee']=is_null($result->paid_amount)?'0':$result->paid_amount;
                         $test_arr['due_fees']=$result->total_fees-$result->paid_amount;
                         $test_arr['image'] = base_url('assets/images/').$result->image;
                         $job[] = $test_arr;
                   }
                  }
                   
                    if($job){
                        $dataTosend = ['status'=>true, 'msg'=>'view paid fees', 'body'=>$job];
                        echo json_encode($dataTosend); die();
                    }else{
                        $dataTosend = ['status'=>false, 'msg'=>'fees not found'];
                        echo json_encode($dataTosend); die();
                    }
                    
                    
                    break;
                     case "view_admin_paid_fee":
                        $job=array();
                    $branch_id = $this->input->post('branch_id');
                    if(empty($branch_id)){
                        $dataTosend = ['status'=>false, 'msg'=>'All field required'];
                        echo json_encode($dataTosend); die();
                    }
                   $students = $this->cm->get_data('user_tbl',['branch_id'=>$branch_id,'roll'=>4]);
                  foreach($students as $student){
                    // $sql = "SELECT r.id, ut.name, ut.image, r.student_id, r.parent_id, sum(r.amount_paid) as paid_amount, f.amount as total_fees FROM receipts as r join fees as f on r.fees_id=f.id
                    // join user_tbl as ut on ut.user_id = r.student_id where student_id = '$student->user_id'";
                    $sql = "SELECT ut.user_id, ut.name , ut.image ,  ut.user_id ,  sum(r.amount_paid) as paid_amount , sum(f.amount) as total_fees
                            FROM fees as f  join  receipts as r on r.fees_id = f.id 
                            join  student_tbl as st on st.class_id = f.class_id
                            join user_tbl as ut on ut.user_id = st.user_id where ut.user_id='$student->user_id'";
                            
                    $res = $this->db->query($sql)->result();
                
                   foreach($res as $result)
                   { 
                       
                         $test_arr=array();
                         $test_arr['name']=$result->name;
                         $test_arr['student_id']=$result->user_id;
                         $test_arr['installment']= $this->cm->get_data('receipts',['student_id'=>$result->user_id]);
                         $test_arr['total_fees']= is_null($result->total_fees)?'0':$result->total_fees;
                         $test_arr['paid_fee']=is_null($result->paid_amount)?'0':$result->paid_amount;
                         $test_arr['due_fees']=$result->total_fees-$result->paid_amount;
                         $test_arr['image'] = base_url('assets/images/').$result->image;
                         $job[] = $test_arr;
                   }
                  }
                   
                    if($job){
                        $dataTosend = ['status'=>true, 'msg'=>'view paid fees', 'body'=>$job];
                        echo json_encode($dataTosend); die();
                    }else{
                        $dataTosend = ['status'=>false, 'msg'=>'fees not found'];
                        echo json_encode($dataTosend); die();
                    }
                    
                    
                    break;
                    
                      default:
                     $dataTosend = ['status'=>false,'msg'=>"Invalid Requst",'body'=>'dsccs'];
                        echo json_encode($dataTosend); die(); 
                }
    } 
  	
}