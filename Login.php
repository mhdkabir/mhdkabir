<?php
class Login extends CI_Controller {

	function __construct()
{
	parent::__construct();
	 $this->load->model('Formmodel');
    $this->load->library('email');
    $this->load->model('common_model','cm');
    
 }


 public function index()
	{		
	$this->load->view('login');
	}
	
	
	public function register()
	{  		
	$this->load->view('register');
	}

  function register2(){
     $data['date']=$this->Formmodel->reg_customer();	
	  $this->load->view('service/register',$data);


  }


	function register2_addData()
	{
		
		$name=$this->input->post('name');
		$mobile=$this->input->post('mobile');
		$pass=$this->input->post('pass');
		$select=$this->input->post('select');	
   
 $this->Formmodel->insert_reg($name,$mobile,$pass,$select);	
    	 
	}
	 function login_action()
      {
		$mobile=$this->input->post('mobile');
		$pass=$this->input->post('pass');
		$data=$this->Formmodel->login_customer($mobile,$pass); 
		
 		    if($data){
 		$id=$data[0]->user_id;
		$email=$data[0]->email;
		$image=$data[0]->image;
		$name=$data[0]->user_name ;	
		$state=$data[0]->state;
        $city=$data[0]->city;
        $type=$data[0]->type;
        $mobile=$data[0]->mobile;    	
       $this->session->set_userdata('mail',$email);
			 $this->session->set_userdata('name',$name);
			 $this->session->set_userdata('user_id',$id);
			 $this->session->set_userdata('image',$image);
			 $this->session->set_userdata('city',$city);
			 $this->session->set_userdata('state',$state);
			 $this->session->set_userdata('user_type',$type);
 		    	if($type==2){
 		    	redirect(base_url('service')); }

 		    else{
 		    	
 		    	redirect(base_url('Home'));
 		    }
 		}
       
       else{
       	 $this->session->set_flashdata('error','Invalid mobile or password');
       	 redirect(base_url('Login'));
       }

      }
      public function home()
	{
		$data['date']=$this->Formmodel->alldata();
		$this->load->view('service/home',$data);
	}
	public function dashboard()
	{
		$data['date']=$this->Formmodel->customer_alldata();
		$this->load->view('dashboard',$data);
	}
	 function customer_reg()
       {
        	
		$name=$this->input->post('name');
		$mobile=$this->input->post('mobile');
		$pass=$this->input->post('pass');
   	$this->Formmodel->customer_reg($name,$mobile,$pass);
        
	}


public function logout()
	{  		
	    $this->session->sess_destroy();
          redirect(base_url('Login')); 
 
	}

  public function forget_pass()
  {
  	$this->load->view('forgot_pass');
  }
  public function forgot_action()
  {
  	$mail=$this->input->post('mail');
  	$data=$this->Formmodel->forgot_model($mail);
  $password= $data[0]->pin_code;
  $mail=$data[0]->email;
  // $this->session->set_userdata('password',$password);
  // $this->session->set_userdata('mail',$mail);
  $from_email = "alikabir078600@gmail.com";
       
        //Load email library
        $this->load->library('email');
        $this->email->from($from_email, 'Identification');
        $this->email->to($mail);
        if ($mail) {
        	
        $this->email->subject('password sent to your registered email');
        $this->email->message($password,'is your password ! use it.');
        //Send mail
        if($this->email->send()){
            $this->session->set_flashdata("email_sent","Congragulation  your request Sent Successfully.");
         redirect(base_url('Login/forget_pass'));
      }
        else{
            $this->session->set_flashdata("not_sent","Your request did not send yet");
        redirect(base_url('Login/forget_pass'));
 }
}
else
{
	echo "no email id found";
}

  }

  }