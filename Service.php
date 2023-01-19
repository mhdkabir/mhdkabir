<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Service extends CI_Controller {

	function __construct()
{
	parent::__construct();
	 $this->load->model('Formmodel');
	 $this->load->model('common_model','cm');
   	$uId = $this->session->userData('user_id');
   	$res__2 = $this->cm->count_row('user_tbl',['user_id'=>$uId]);
 
   	if(!$res__2){ 
       $this->session->set_flashdata('error','Invalid User!..');
   		redirect(base_url('Login'));
   		 }
 }
    
  public function finddetail($id)
	{		$data['detail']=$this->Formmodel->find_detail($id);
	          $this->session->set_userdata('cid',$id);
		$data['rate_detail']=$this->Formmodel->show_rate_detail();
		$this->load->view('service/find_detail',$data);
	} function find_detail($id)
     {
      $data=$this->db->query("select * from user_tbl where user_id=$id");

      return $data->result();
     }

      function show_rate_detail()
     {    
        $id=$this->session->userdata('cid');
        $data=$this->db->query("select * from service_rate_card where card_id='$id'");
        return $data->result();
     }

	
	public function attendance()
	{
		$this->load->view('service/attendance');
	}
	
	public function index()
	{
		$user_type = $this->session->userData('user_type');
		$s_type = ($user_type == 2) ? 1: 2;
		$data['user_list'] = $this->cm->get_data('user_tbl',["type" =>$s_type]);

		$this->load->view('service/chat_list',$data);

	}
	
	public function detail()
	{
		$this->load->view('service/detail');
	}
	
	public function chat($id)
	
	{       // $this->session->sess_destroy();
		 // echo "<pre>"; print_r($_SESSION); die();
		$this->session->set_userdata('chat',$id);
		$user_id = $this->session->userData('user_id');
		$receiver_id = $this->uri->segment(3);
		$q = "SELECT * FROM `chat_tbl` WHERE sender_id in($user_id,$receiver_id) and receiver_id in 
				($user_id,$receiver_id)";	

		$data['chat_list']=$qqqq = $this->db->query($q)->result();
		
		// echo "<pre>"; print_r($qqqq); die();

    $data['dta']=$this->Formmodel->vend_chat();
		$this->load->view('service/chat',$data);
	}
	public function chatList()
	{
	  //echo "<pre>"; print_r($_SESSION); die();

		$user_type = $this->session->userData('user_type');
		$s_type = ($user_type == 2) ? 1: 2;
		$data['user_list'] = $this->cm->get_data('user_tbl',["type" =>$s_type]);

		$this->load->view('service/chat_list',$data);
	}
  
 public function vandor_status_add()
	 {
	 	  $user_id     = $this->session->userData('user_id');
		  $receiver_id = $this->input->post('receiver_id');
	 	 

	 	 		$res = $this->cm->user_update('vandor_hire_tbl',['user_id'=>$receiver_id,
	 	 			      'vendor_id'=>$user_id,'vendor_status' => '0' ],[ 'vendor_status' => '1'] );

	 	 

				if($res){
						echo json_encode(['status'=>true,'msg'=>'User order accept  successfully','body'=>'' ]);
				}else{
						echo json_encode(['status'=>false,'msg'=>'User order not accept','body'=>'']);
					}





		}
	
   public function get_chat_data()
	 {
	 	  $user_id     = $this->session->userData('user_id');
		  $receiver_id = $this->input->post('receiver_id');
	 	  $user_type   = $this->session->userData('user_type');

	 	    $hire_me_btn = 0;
	 	 	if($user_type == 2){

	 	 		$hire_me_btn = $this->cm->count_row('vandor_hire_tbl',['user_id'=>$receiver_id,'vendor_id'=>$user_id,'vendor_status' => '0' ]);

	 	 		//echo "$hire_me_btn == row count == $receiver_id"; die();

	 	 	}




		$q = "SELECT * FROM `chat_tbl` WHERE sender_id in($user_id,$receiver_id) and receiver_id in 
				($user_id,$receiver_id)";	

		$res = $this->db->query($q)->result();

			if($res){
						echo json_encode(['status'=>true,'msg'=>'success','body'=>$res, 'hire_me_status'=>$hire_me_btn ]);
				}else{
						echo json_encode(['status'=>false,'msg'=>'No data Found!..','body'=>'', 'hire_me_status'=>$hire_me_btn ]);
					}
	  }

 public function hire_vendor()
	 {
		  $user_id = $this->session->userData('user_id');
		  $hours   = $this->input->post('hours');
		  $amount  = $this->input->post('amount');
		  $days    = $this->input->post('days');
		  $s_date    = $this->input->post('s_date');
		  $receiver_id    = $this->input->post('receiver_id');

	if(empty($hours) || empty($amount) || empty($days) || empty($s_date)  || empty($receiver_id))	  
	{
		  echo json_encode(['status'=>false,'msg'=>'All Field Required','body'=>'']);
	}

    $whr = ['user_id'=>$user_id,'vendor_id'=>$receiver_id,'par_day_hours'=>$hours,
      					 'par_day_amount'=>$amount, 'start_date' => $s_date , 'days'=>$days];


		$check_rows = $this->cm->count_row('vandor_hire_tbl',['user_id'=>$user_id,
			'vendor_id'=>$receiver_id,'vendor_status' => '0' ]);

		if($check_rows){

				  $res = $this->cm->user_update('vandor_hire_tbl',['user_id'=>$user_id,'vendor_id'=>$receiver_id, 'vendor_status' => '0' ],$whr);

		}else{
			 $res = $this->cm->save('vandor_hire_tbl',$whr);
		
		}

         

			if($res){
						echo json_encode(['status'=>true,'msg'=>'success','body'=>$res]);
				}else{
						echo json_encode(['status'=>false,'msg'=>'server error ','body'=>'']);
					}
	  }


	public function monthlyReport()
	{
       $user_id = $this->session->userData('user_id');
       $this->load->view('service/monthly_report');


	}
	//////////////////////////////////
 function rateCard(){
		if ($this->input->post('sbmt')) {
			$id=$this->session->userdata('user_id');
			$select=$this->input->post('select');
			$charge=$this->input->post('charge');
			$area=$this->input->post('area');
			$this->Formmodel->rate_card($id,$select,$charge,$area);
			$data['rate_data']=$this->Formmodel->show_rate_card();
			$this->load->view('service/rate_card',$data);
		}else{
			$data['rate_data']=$this->Formmodel->show_rate_card();
		  $this->load->view('service/rate_card',$data);
	}
 	
	}

    function  edit_rate($id)
   	   {
   	   	$id=$this->uri->segment(3);
   	   	  $red['editdata']=$this->Formmodel->edit_ratecard($id);
   	   	$this->load->view('service/edit_rate_card',$red);
   	   }

   	   function update_rate()
   	   {
   	   		$id=$this->input->post('id');
   	   		$time=$this->input->post('select');
   	   		$charge=$this->input->post('charge');
   	   		$area=$this->input->post('area');
   	   		$res=$this->Formmodel->update_ratecard($id,$time,$charge,$area);
   	   		if ($res) {
   	   			redirect('service/rateCard');
   	   			 $this->session->set_flashdata('update','you are successfully updated');
   	   		}
   	   		else
   	   		{
   	   			redirect ('service/rateCard');
   	   			$this->session->set_flashdata('update_error','some error in updation');
   	   		}
   	   }

      function delete_rate($id)
      {
          $id=$this->uri->segment(3);
          $res=$this->Formmodel->delete_ratecard($id);
          if ($res) {
          	redirect('service/rateCard');
          }
          else
          	{
          		redirect('service/rateCard');
          	}
      }



	////////////////////////////////////
	public function myincharges(){
		$this->load->view('service/my_incharges');
	}
	
public function	get_city(){
	$s_id = $this->input->post('s_id');
	$res = $this->cm->get_data('city_tbl',['state_id' =>$s_id]);
	if($res){
		echo json_encode(['status'=>true,'msg'=>"success",'body'=>$res]);
	}else{

			echo json_encode(['status'=>false,'msg'=>"No Data Found!..",'body'=>'']);
	}
}
	
	
	public function profile()
	{
	
	$data['date'] =$tt=$this->Formmodel->select();
	$data['city']=$this->Formmodel->city_table();
	$data['state']=$this->Formmodel->state_tbl();


	//echo "<pre>"; print_r($_SESSION); die(); 



  $this->load->view('service/profile',$data);

	}


 
	  
	public function locality()
	{   $data['locate']=$this->cm->show_locat();
		$this->load->view('service/locality',$data);
	}
   
  
  public function trunc_locate($id)
  {  
  	 $id=$this->uri->segment(3);
     $data=$this->cm->delete_locate($id);
     if ($data) {
     	    $this->session->set_flashdata('successs','your data delete successfully');
     	   redirect(base_url("Service/locality"));
     }
     else{
     	$this->session->set_flashdata('eror','some error in datas deletion');
     	redirect(base_url("service/locality"));
     }
  }
  
   function  edit_locate($id)
   	   {
   	   	$id=$this->uri->segment(3);
   	   	  $red['editdata']=$this->cm->edit_location($id);
   	   	  if ($red) {   	   	  
   	      	   $this->load->view('service/edit_locate',$red);
   	 }
   	   }

   	   function update_locate()
   	   {
   	   		$id=$this->input->post('id');
   	   		$title=$this->input->post('title');
   	   		$res=$this->cm->update_location($id,$title);
   	   		if ($res) {
   	   			$this->session->set_flashdata('success','you are successfully updated');
   	   			redirect('service/locality');
   	   		}
   	   		else
   	   		{
   	   			$this->session->set_flashdata('error','some error in updation');
   	   			redirect ('service/rateCard');
   	   		}

}

	 function edit_service()
        { 


        
        		 $name=$this->input->post('name');
        		 $email=$this->input->post('email');
        		 $mobile=$this->input->post('mobile');
        		 $pass=$this->input->post('pass');
        		// $i=$this->Formmodel->file_upload('image', 'assets/images/user/');
        		 $state  = $this->input->post('select_state');
        		 $city   = $this->input->post('select_city');
        		 $address= $this->input->post('add');
      			 $img 	 = '';
       
        if(empty($name) || empty($email)|| empty($mobile)||  empty($address))
        	{
        		$this->session->set_flashdata('error','All Field Required');
    		    	redirect(base_url('Service/profile')); die(); 
        	}	 
      
		if(isset($_FILES["image"]["name"])){		
 	    	$img = $this->Formmodel->file_upload('image', 'assets/images/user/');
		}
  // `user_tbl`(`user_id`, `user_name`, `email`, `address`, `mobile`, `image`, `type`, `pin_code`, `role`, `state`, `city`, `township_name`, `date`, `user_status`
		
	   $upData = ['user_name'=>$name,'email'=>$email,'address'=>$address,'mobile'=>$mobile,
				'pin_code'=>$pass];
		
		if(! empty($img)){
				 $upData['image'] = $img;
			}

		if(! empty($state)){
				 $upData['state'] = $state;
			 $this->session->set_userdata('city',$city);

			}
		if(! empty($city)){
				 $upData['city'] = $city;
				 $this->session->set_userdata('state',$state);
			}

	   $user_id = $this->session->userData('user_id');
			
		$res = $this->cm->user_update('user_tbl',["user_id"=>$user_id],$upData);
	

		if($res){
			   $this->session->set_userdata('mail',$email);
			 $this->session->set_userdata('name',$name);
			
			


			$this->session->set_flashdata('success','Vendor profile update successfully');
    		    	redirect(base_url('Service/profile')); die(); 
    		    }else{
    		    	$this->session->set_flashdata('error','Server error');
    		    	redirect(base_url('Service/profile')); die(); 
    		    }
     
      
        
        	
        }

  function chat_data_add(){

  		$dd = $this->input->post(); 
  		$chat_data = $this->input->post('chat'); 
  		$receiver_id = $this->input->post('receiver_id'); 

  		$user_id = $this->session->userData('user_id');

  	// echo "$user_id <pre>"; print_r($dd); die();	

  		$f_type = ''; $img = ''; 	
  	  if(isset($_FILES["fileToUpload"]["name"])){		
  		
  		   $target_dir = "assets/chat_image/"; 
			
			     $f_type = $_FILES["fileToUpload"]["type"];
				 $img = rand(1000,9999).'_'.basename($_FILES["fileToUpload"]["name"]);
				$target_file = $target_dir .$img;
			if(!move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
			        $img = '';
			        $f_type = '';
				} 
			}

	 if(empty($chat_data) &&  empty($img)){
			echo json_encode(['status'=>false,'msg'=>"$chat_data == All field required == $img  ",'body'=>'']);
			die();		
	 }		

	
		$addData = ['chat_description'=> $chat_data ,'sender_id'=>$user_id,
		'receiver_id'=>$receiver_id,'file_type'=>$f_type,'file_data'=>$img,'date'=>date('Y-m-d'),
		'time'=>date('h:i') ];	
			$res = $this->cm->save('chat_tbl',$addData);

		if($res){
			 
				echo json_encode(['status'=>true,'msg'=>'success','body'=>$res]); die();
		}else{
				echo json_encode(['status'=>false,'msg'=>'server error','body'=> $addData ]); die();
				}

  }
  
}	