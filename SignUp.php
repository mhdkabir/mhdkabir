<?php
   
require APPPATH . 'libraries/REST_Controller.php';
     error_reporting(0);
class SignUp extends REST_Controller {
   
    public function __construct() {
       parent::__construct();
      $this->load->model('Common_model','cm');
    }
   

    public function index_post()
     {
           $flag = $this->input->post('flag');
        
                switch ($flag) {
                  case "sign_up":

                    $name = $this->input->post('name');
                    $email = $this->input->post('email');
                    //$contact = $this->input->post('mobile');
                    $password = $this->input->post('password');
                    $roll = $this->input->post('roll');
                    // echo $name;
                    // echo $email;
                    // echo $contact;
                    // echo $password;
                    if(empty($name) || empty($email) || empty($password)) 
                        {
                            $dataTosend = ['status'=>false, 'msg'=>'Enter all parameters!'];	
                            echo json_encode($dataTosend); die();
                        }
                        $res2  =  $this->cm->get_data('user_tbl',['email'=> $email]);
                       // echo "<pre>"; print_r($res2);die;
                        if($res2)
                        {
                            $dataTosend = ['status'=>false, 'msg'=>'email already !'];	
                            echo json_encode($dataTosend); die();
                        }else{
        
                            $rs = $this->cm->save('user_tbl',['name'=> $name, 'email'=>$email, 'password'=>$password,'roll'=>$roll]);
        
                                    //echo "<pre>"; print_r($res);
                        
                            if($rs){  
                                $res = $this->cm->get_data('user_tbl',['email'=>$email]);
                                $dataTosend = ['status'=>true, 'msg' => 'Registration successfully','body'=> $res];
                                echo json_encode($dataTosend); die();
                            }else{
                                $dataTosend = ['status'=>false, 'msg' => 'Registration failed'];
                                echo json_encode($dataTosend);die();
                            }
                            
                           
                            $dataTosend = array("response"=>array("status" =>false, "msg" => "faield email"));
                        }
                      break;
                      
                      
                      case "forgot_password":
                         $email = $this->input->post('email');
                         $res = $this->cm->get_data('user_tbl',['email'=>$email]);
                         if($res){
                          $data = array();
                          foreach($res as $record){
                              $password = $data['password'] =  $record->password;
                              $email = $data['email'] =  $record->email;
                              $to = $email;
                                 $subject = "Forgot Password";
                                 
                                 $message = "<b>This is your new password.</b>";
                                 $message .= "<h1>$password</h1>";
                                 
                                 $header = "From:admin@meritcard.com \r\n";
                                 $header .= "MIME-Version: 1.0\r\n";
                                 $header .= "Content-type: text/html\r\n";
                                 
                                 $retval = mail ($to,$subject,$message,$header);
                                 
                                 if( $retval == true ) {
                                    $dataTosend = ['status'=>true, 'msg'=>'password sent to email, please check.', 'body'=>$res];
                                    echo json_encode($dataTosend); die();
                                 }else {
                                    $dataTosend = ['status'=>false, 'msg'=>'unable to send email. please try again.'];
                                    echo json_encode($dataTosend); die();
                                 }
                                }
                            $dataTosend = ['status'=>true, 'msg'=>'password sent to email, please check.', 'body'=>$res];
                            echo json_encode($dataTosend); die();
                        }else{
                            if(empty($date)){
                                 $dataTosend = ['status'=>false, 'msg'=>'failed'];
                            }else{
                                 $dataTosend = ['status'=>true, 'msg'=>'view event','body'=>[]];
                            }
                            
                           
                            echo json_encode($dataTosend); die();
                        }

                      break;
                      default:
                     $dataTosend = ['status'=>false,'msg'=>"Invalid Requst",'body'=>''];
                        echo json_encode($dataTosend); die(); 
                }
    } 
  	
}