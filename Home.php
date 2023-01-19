<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {
function __construct()
{
	parent::__construct();
	 $this->load->model('Formmodel');
	$this->load->model('common_model','cm');
   	$uId = $this->session->userData('user_id');
   	$res__2 = $this->cm->count_row('user_tbl',['user_id'=>$uId]);
   	if(!$res__2){ 

			$this->session->set_flashdata('error','Invalid User!..');
   		redirect(base_url('Login')); }
 }

	
	
	
	public function login()
	{  		
	$this->load->view('login');
	}
	public function index()
	{
	  $user_id = $this->session->userData('user_id');
	  $dx = $this->input->get('date'); 
	  $date = (empty($dx))? date('Y-m-d') : $dx ;

	  $u_time = strtotime($date);

	  $nowTime = strtotime(date('Y-m-d'));
	
	 
	  if($u_time > $nowTime ){

	  	 $this->session->set_flashdata('error','Date is greater than current date');
	  	 $this->load->view('dashboard'); //die(); 
	  }else{

	  //	echo $u_time.' ==jk==  '. $nowTime; die(); 
	  // $q = "SELECT a.vendor_id , b.user_name,b.image FROM vandor_hire_tbl as a join user_tbl as b on a.vendor_id = b.user_id and b.type = '2' WHERE a.user_id = '$user_id' and start_date <= '$date'  ";
	//, sum(Case WHEN (c.attendance_status = '0') THEN 1 ELSE 0 END Saleable) as vendor_app 

	 $q = "SELECT a.vendor_id , b.user_name,b.image,COALESCE(c.attendance_status,0) as vendor_attn FROM vandor_hire_tbl as a join user_tbl as b on a.vendor_id = b.user_id and b.type = '2' left join vendor_attendance_tbl as c on a.user_id = c.user_id and a.vendor_id = c.vendor_id 
		 and c.date = '$date' WHERE a.user_id = '$user_id' and start_date <= '$date'"; 

	  	$data = array() ;
		$data['v_list'] =$tt = $this->db->query($q)->result();
 		
 		// echo " $q  ==  <pre>"; print_r( $tt);  die() ;



		$this->load->view('dashboard',$data);

	}
	}
	
public function vendor_attendance(){

	$vendor_id = $this->input->post('vendor_id');
	$date      = $this->input->post('date');

	if(empty($vendor_id) || empty($date) )	  
	{
		  echo json_encode(['status'=>false,'msg'=>'All Field Required','body'=>'']); die(); 
	}

    $user_id    = $this->session->userData('user_id');

$check_rows = $this->cm->get_data('vendor_attendance_tbl',['user_id'=>$user_id,
			'vendor_id'=>$vendor_id,'date' => $date ]);


if($check_rows){
			
			$attn_id = $check_rows[0]->attn_id;	
			$oldAttn = $check_rows[0]->attendance_status;
			$newAttn = ($oldAttn == 1)? 0:1;	
	    
	       $res = $this->cm->user_update('vendor_attendance_tbl',['user_id'=>$user_id,'vendor_id'=>$vendor_id, 'date' =>$date ],['attendance_status'=>$newAttn ]);

		}else{
			 $res = $this->cm->save('vendor_attendance_tbl',['vendor_id'=>$vendor_id,
	 		'user_id'=>$user_id,'date'=>$date,'attendance_status'=>'1']);
		
		}

		
		if($res){

				$total_attn = $this->cm->count_row('vendor_attendance_tbl',['user_id'=>$user_id,
			'vendor_id'=>$vendor_id,'attendance_status' => '1' ]);
				echo json_encode(['status'=>true,'msg'=>'success','body'=> $total_attn ]);
				}else{
					echo json_encode(['status'=>false,'msg'=>'server error','body'=> '' ]);
					}
	


}

	
	public function attendance()
	{
		$this->load->view('attendance');
	}
	      
	  

	public function chat($id)
	{      $this->session->set_userdata('chat_msg',$id);
		$user_id = $this->session->userData('user_id');
		$receiver_id = $this->uri->segment(3);
		$q = "SELECT * FROM `chat_tbl` WHERE sender_id in($user_id,$receiver_id) and receiver_id in 
				($user_id,$receiver_id)";	

		$data['chat_list']=$qqqq = $this->db->query($q)->result();
		
		// echo "<pre>"; print_r($qqqq); die();
$data['dta']=$this->Formmodel->chat_msg();

		$this->load->view('chat',$data);

	}
	public function chatList()
	{
		
		$user_type = $this->session->userData('user_type');
		$s_type = ($user_type == 2) ? 1: 2;
		$data['user_list'] = $this->cm->get_data('user_tbl',["type" =>$s_type]);

		$this->load->view('chat_list',$data);
	}
	
	
	public function findme()
	{
         $data['by_locat']=$this->Formmodel->serch_by_locate();     
	    $data['search_dta']=$this->Formmodel->search_all();
		$data['dataa']=$this->Formmodel->service_info();
		if ($this->input->get('serch')) {
			$keyword=$this->input->get('select');

			//echo "rajj === $keyword"; die(); 
		 // $data['search_dta']=$this->Formmodel->search_me($keyword); 
		  $data['search_dta'] = $this->cm->get_data('user_tbl',['role'=> $keyword]); 
        	$this->load->view('findme',$data);
		}
		else
		{
			$this->load->view('findme',$data);
		}
	}
	 

	public function finddetail($id)
	{		
          $user_type = $this->session->userData('user_type');
		$s_type = ($user_type == 1) ? 1: 2;
		$data['user_list'] = $this->cm->get_data('user_tbl',["type" =>$s_type]);
		$data['detail']=$this->Formmodel->find_detail($id);
	          $this->session->set_userdata('cid',$id);
		$data['rate_detail']=$this->Formmodel->show_rate_detail();
		$this->load->view('find_detail',$data);
	}
	
	public function publiclist()
	{
		$this->load->view('public_list');
	}
	
	public function monthlyReport()
	{
		
		$first_date = date('Y-m-d',strtotime('first day of this month'));
		$last_date = date('Y-m-d',strtotime('last day of this month'));

		 $user_id    = $this->session->userData('user_id');
	  $q = "SELECT a.vendor_id ,a.par_day_amount , b.user_name,b.image, sum(COALESCE(c.attendance_status,0)) as vendor_attn FROM vandor_hire_tbl as a join user_tbl as b on a.vendor_id = b.user_id and b.type = '2' left join vendor_attendance_tbl as c on a.user_id = c.user_id and a.vendor_id = c.vendor_id 
		 WHERE a.user_id = '$user_id' and (c.date BETWEEN '$first_date' and '$last_date')  GROUP by c.vendor_id ";
	

	$total_vandor_amt = 0; 
	 $res = $this->db->query($q)->result(); 

	 foreach ($res as $val) {
	 	$val->total_amount =  $val->par_day_amount * $val->vendor_attn; 

	 	$total_vandor_amt += $val->total_amount;
	 }

	$sendData['vendorReports'] = $res;
	$sendData['total_vandor_amt'] = $total_vandor_amt;
	
	$this->load->view('monthly_report',$sendData);
  }
	
	public function addService()
	{
		$this->load->view('add_service');
	}
   
     
     

     function login_show()
     {
     	$data['dat']=$this->Formmodel->select();
     	$this->load->view('service/profile',$data);

     }

public function profile()
	{
	
	$data['date'] =$tt=$this->Formmodel->select();
	//echo "<pre>"; print_r($_SESSION); die(); 
  $this->load->view('profile',$data);

	}

   function edit_service()
        { 

        		 $name=$this->input->post('name');
        		 $email=$this->input->post('email');
        		 $mobile=$this->input->post('mobile');
        		// $i=$this->Formmodel->file_upload('image', 'assets/images/user/');
        		 $state  = $this->input->post('select_state');
        		 $city   = $this->input->post('select_city');
        		 $pass   = $this->input->post('pass');
        		 $address= $this->input->post('add');
      			 $img 	 = '';
       
        if(empty($name) || empty($email)|| empty($mobile)|| empty($pass)|| empty($address))
        	{
        		$this->session->set_flashdata('error','All Field Required');
    		    	redirect(base_url('Home/profile')); die(); 
        	}	 
      
		if(isset($_FILES["image"]["name"])){		
 	    	$img = $this->Formmodel->file_upload('image', 'assets/images/user/');
		}
  // `user_tbl`(`user_id`, `user_name`, `email`, `address`, `mobile`, `image`, `type`, `pin_code`, `role`, `state`, `city`, `township_name`, `date`, `user_status`
		
	   $upData = ['user_name'=>$name,'email'=>$email,'address'=>$address,'mobile'=>$mobile,
				'pin_code'=>$pass];
		
		if(! empty($img)){
				 $upData['image'] = $img;
			}$user_id = $this->session->userData('user_id');
			
		$res = $this->cm->user_update('user_tbl',["user_id"=>$user_id],$upData);
	

		if($res){
			   $this->session->set_userdata('mail',$email);
			 $this->session->set_userdata('name',$name);
			
			


			$this->session->set_flashdata('success','Vendor profile updated successfully');
    		    	redirect(base_url('Home/profile')); die(); 
    		    }else{
    		    	$this->session->set_flashdata('error','Server error');
    		    	redirect(base_url('Home/profile')); die(); 
    		    }
     
      
        
        	
        }

} 
