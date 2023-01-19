<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class vendor extends CI_Controller {
function __construct()
{
	parent::__construct();
	 
	 $this->load->library('session');
     $this->load->model('common_model','cm');
 }

 function add_location()
{
		$user_id = $this->session->userData('user_id');

   $dd = $this->input->post(); 
  		$title = $this->input->post('title'); 
   // echo "$user_id <pre>"; print_r($dd); die();	
     if(empty($title)){
         	$this->session->set_flashdata('error_msg', 'Title Field Required');
     	redirect("admin/signup"); die();
     }
		 $arr = [] ; 
	foreach ($title as  $val) {
			  
			   $addData = ['vendor_id'=>$user_id,'location'=>$val,'active_status'=>1];
				
				
				 $responce = $this->cm->save('vendor_locations',$addData);
				  
				if($responce){  $arr [] =  $responce ;   }
			}		
   
		if($arr){
			 	  $this->session->set_flashdata('success', 'location add successfully');
     	            redirect(base_url("Service/locality")); die();
		}else{
				$this->session->set_flashdata('error', 'Server error');
     	         redirect(base_url("Service/locality")); die();
				}



}














}