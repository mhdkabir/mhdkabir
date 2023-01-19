<?php
   error_reporting(0);
require APPPATH .'/libraries/REST_Controller.php';
     
class OrderController extends REST_Controller {
    
   
    public function __construct() {
       parent::__construct();
       
        $this->load->database();

         $this->load->model('Common_model', 'cm');   
       
    }
       
    public function index_post(){
             
       $flag = $this->input->post('flag');
     
       switch ($flag){
            // echo $flag;die;
            
            case "place_order":
               {
                  
                   $user_id             = $this->input->post('user_id');
                   $address_id          = $this->input->post('address_id');
                   $transaction_id      = $this->input->post('transaction_id');
                   $payment_method      = $this->input->post('payment_method');
                   $file_name = $this->cm->file_upload('image',base_url('upload/') );
                   $order_name=  $this->input->post('order_name');
                   $order_quantity=$this->input->post('order_quantity');
                   $real_price_total    = $this->input->post('real_price_total');
                   $discount            = $this->input->post('discount');
                   $final_payable_amt   = $this->input->post('final_payable_amt');
                   $updated_at 	        = date('Y-m-d H:i:s');
                   $created_at 	        = date('Y-m-d H:i:s');
     
                   if($payment_method==0){
                        $q1="Insert into tbl_orders(user_id,payment_method,order_image,order_name,order_quantity,total,address_id,discount,final_total,updated_at,created_at)values('$user_id','$payment_method','$file_name','$order_name','$order_quantity','$real_price_total','$address_id','$discount','$final_payable_amt','$updated_at','$created_at')";
                        $res=$this->db->query($q1);
                        $order_id = $this->db->insert_id();
              
                        $qry12="SELECT * from tbl_cart  WHERE user_id='$user_id'";
                        $res12=$this->db->query($qry12)->result();         
     
                         foreach($res12 as $val)   
                    	     {
                              
                                $qty = $val->qty; 
                                $prod_id  = $val->prod_id; 
                                $size  = $val->size; 
                                $price = $val->price; 
                                $offer = $val->offer; 
                               
                         
                            $qry_5 ="Insert into tbl_order_detail(order_id,user_id,prod_id,quantity,size,price,offer_price,updated_at,created_at)             
                    	                             values('$order_id','$user_id','$prod_id','$qty','$size','$price','$offer','$updated_at','$created_at')";               
                    	    $row_inserted =  $this->db->query($qry_5);  
                            
                           // echo"<pre>";print_r($qry_5);die();
                            
                           
                            if($row_inserted){
        	                        $rw = "delete from tbl_cart where user_id = '$user_id' and id='$val->id'"; 
        	                        $row_deleted=$this->db->query($rw);        
                    	   }
                    	           
                    	     }
                        $dataTosend = ['status'=>true,'msg'=>'Order placed successfully !','body'=>""];
                   }else{
                       //for paymentgatway
                         $dataTosend = ['status'=>false,'msg'=>'something went wrong please try again','body'=>""];
                   }
               
               
                     echo json_encode($dataTosend); 
                            break;
                        }
               case "view_all_product":
               {
                 $user_id=$this->input->post('user_id');
                if(!empty($user_id)){
                    $q="select * from tbl_shop_product where user_id='$user_id' order by prod_id desc";
                    $product_foud=$this->db->query($q)->result();
                        if($product_foud){
                            foreach($product_foud as $prod){
                                $prod->featured_image = base_url().'common_uploads/product_images/'.$prod->featured_image;
                            }
                             $dataTosend = ['status'=>true,'msg'=>'Poduct found.','body'=>$product_foud];
                        }else{
                            $dataTosend = ['status'=>false,'msg'=>'Poduct not found.','body'=>''];
                        }
                }else{
                  $dataTosend = ['status'=>false,'msg'=>'Please login.','body'=>''];   
                }
             
                     echo json_encode($dataTosend);
                            break;
                        }
                  case "view_all_address":
               {
                 $user_id =   $this->input->post('user_id');
                 $q1="select a.*,s.state_name,c.city_name from tbl_user_address as a join tbl_state_list as s on a.u_state=s.state_id join tbl_city_list as c on c.city_id=a.city where a.user_id=$user_id order by a.id desc";
                  $address_found = $this->db->query($q1)->result();
                //print_r($address_found);die;
                if($address_found){
                  
                     for($i=0;$i<count($address_found);$i++){
                        
                        
                         $arr['id']                     =   $address_found[$i]->id;
                         $arr['user_id']                =   $address_found[$i]->user_id;
                         $arr['name']                   =   $address_found[$i]->name;
                         $arr['address_email']          =   $address_found[$i]->address_email;
                         $arr['u_state']                =   $address_found[$i]->state_name;
                         $arr['user_contact']           =   $address_found[$i]->user_contact;
                         $arr['address']                =   $address_found[$i]->address;
                         $arr['street']                 =   $address_found[$i]->street;
                         $arr['city']                   =   $address_found[$i]->city_name;
                         $arr['zipcode']                =   $address_found[$i]->zipcode;
                         $arr['default_address']        =   $address_found[$i]->default_address;
                         $arr['updated_at']             =   $address_found[$i]->updated_at;
                         $arr['created_at']             =   $address_found[$i]->created_at;
                       
                                $job[]=$arr;
                         
                     }
                      $dataTosend = ['status'=>true,'msg'=>'Address found','body'=>$job];
                     
                }else{
                   $dataTosend = ['status'=>false,'msg'=>'Address not found.','body'=>'']; 
                }
                
             
                     echo json_encode($dataTosend);
                            break;
                }
                  case "edit_address":
               {
                 $id =   $this->input->post('id');
                 $q1="select a.*,s.state_name,c.city_name from tbl_user_address as a join tbl_state_list as s on s.state_id=a.u_state join tbl_city_list as c on c.city_id=a.city where id='$id'";
                  $address_found = $this->db->query($q1)->row();
                
                if($address_found){
                  
                      $dataTosend = ['status'=>true,'msg'=>'Address found','body'=>$address_found];
                     
                }else{
                   $dataTosend = ['status'=>false,'msg'=>'Address not found.','body'=>'']; 
                }
                
             
                     echo json_encode($dataTosend);
                            break;
                }
                  case "delete_address":
               {
                 $id =   $this->input->post('id');
                 $del_q1="delete from tbl_user_address where id='$id'";
                  $address_deleted= $this->db->query($del_q1);
                
                if($address_deleted){
                  
                      $dataTosend = ['status'=>true,'msg'=>'Address deleted successfully !','body'=>''];
                     
                }else{
                   $dataTosend = ['status'=>false,'msg'=>'Unable to delete address. Please try again.','body'=>'']; 
                }
    
                     echo json_encode($dataTosend);
                            break;
                }
                case "view_orders":
               {
                 $user_id =   $this->input->post('user_id');
                 $q1="select order_id,order_image,order_name,order_quantity,final_total,created_at from tbl_orders where user_id='$user_id' order by order_id desc";
                  $product = $this->db->query($q1)->result();
                
                if($product){
                  
                     for($i=0;$i<count($product);$i++){
                        
                        
         $arr['order_id']               =   $product[$i]->order_id;
     $arr['order_image']              = base_url().'/common_uploads/product_images/'.$product[$i]->order_image;
     $arr['order_name']           =$product[$i]->order_name;
     $arr['order_quantity']      =$product[$i]->order_quantity;
                         $arr['final_total']            =   $product[$i]->final_total;
                         $arr['created_at']             =   date("d M Y h:i A", strtotime($product[$i]->created_at));
                       
                                $job[]=$arr;
                         
                     }
                      $dataTosend = ['status'=>true,'msg'=>'Orders found','body'=>$job];
                     
                }else{
                   $dataTosend = ['status'=>false,'msg'=>'No order found.','body'=>'']; 
                }
                
             
                     echo json_encode($dataTosend);
                            break;
                }
              case "view_order_details":
               {
                 $order_id =   $this->input->post('order_id');
                 $q1="select o.*,p.prod_name,p.color,p.description,p.featured_image from tbl_order_detail as o join tbl_shop_product as p on o.prod_id=p.prod_id where order_id='$order_id'";
                  $product = $this->db->query($q1)->result();
                
                if($product){
                  
                     for($i=0;$i<count($product);$i++){
                    
                    $product[$i]->featured_image  =base_url().'common_uploads/product_images/'.$product[$i]->featured_image;
                    $product[$i]->updated_at  =date("d M Y h:i A", strtotime($product[$i]->updated_at));
                    $product[$i]->created_at  =date("d M Y h:i A", strtotime($product[$i]->created_at));
            
                     }
                      $dataTosend = ['status'=>true,'msg'=>'Order details found','body'=>$product];
                     
                }else{
                   $dataTosend = ['status'=>false,'msg'=>'No order details found.','body'=>'']; 
                }
                
             
                     echo json_encode($dataTosend);
                            break;
                }
            case "add_address":
               {
                 
                $form_errors = '';
                $add_address_rules = [
                			'add_address_info' => [
                				[
                					'field' => 'name',
                					'label' => 'Name',
                					'rules' => 'required',
                					'errors' => [
                	                    'required' => 'Name field is required.',
                		            ],
                				],
                				[
                					'field' => 'email',
                					'label' => 'Email',
                					'rules' => 'required',
                					'errors' => [
                	                    'required' => 'Email name field is required.',
                		            ],
                				],
                				[
                					'field' => 'contact_no',
                					'label' => 'Contact Number',
                					'rules' => 'required',
                					'errors' => [
                	                    'required' => 'Contact Number field is required.',
                		            ],
                				],
                				[
                					'field' => 'state_id',
                					'label' => 'State',
                					'rules' => 'required',
                					'errors' => [
                	                    'required' => 'State field is required.',
                		            ],
                				],
                				[
                					'field' => 'city_id',
                					'label' => 'City',
                					'rules' => 'required',
                					'errors' => [
                	                    'required' => 'City field is required.',
                		            ],
                				],
                				[
                					'field' => 'address',
                					'label' => 'Address',
                					'rules' => 'required',
                					'errors' => [
                	                    'required' => 'Address field is required.',
                		            ],
                				],
                				[
                					'field' => 'street',
                					'label' => 'Address',
                					'rules' => 'required',
                					'errors' => [
                	                    'required' => 'Address field is required.',
                		            ],
                				],
                				[
                					'field' => 'zipcode',
                					'label' => 'Zipcode',
                					'rules' => 'required',
                					'errors' => [
                	                    'required' => 'Zipcode field is required.',
                		            ],
                				]
                			]
                		];
                		
                		$post = $this->input->post();
                	//	print_r($post);die;
                		$this->form_validation->set_data($post);
                		// $this->form_validation->reset_validation();
                		$this->form_validation->set_rules($add_address_rules['add_address_info']);
                		
                		if($this->form_validation->run() == TRUE){
                                        $qr="select id from tbl_user_address where default_address = 1 and user_id=".$post['user_id'];
                                        $add_found=$this->db->query($qr)->row();
                                        if($add_found){
                                            $default=0;
                                        }else{
                                             $default=1;
                                        }
                                        $form_data['user_id']	    = $post['user_id'];
                                        $form_data['name']  	    = $post['name'];
                                        $form_data['address_email']	= $post['email'];
                            			$form_data['u_state']       = $post['state_id'];
                            			$form_data['user_contact']  = $post['contact_no'];
                            			$form_data['address']	    = $post['address'];
                            			$form_data['street']	    = $post['street'];
                            			$form_data['city']	    	= $post['city_id'];
                            			$form_data['zipcode']	    = $post['zipcode'];
                            			$form_data['default_address']= $default;
                            			$form_data['updated_at'] 	= date('Y-m-d H:i:s');
                            			$form_data['created_at'] 	= date('Y-m-d H:i:s');
                            		
                				
                            			$address_id = $this->common_model->addData('tbl_user_address', $form_data);
                            		
                            			if($address_id){
                                           	$dataTosend = ['status'=>true,'msg'=>'Address saved successfully ! ','body'=>''];

                            			}else{
                            			$dataTosend = ['status'=>false,'msg'=>'Unable to add address . Please try again. ','body'=>''];
                            			} 
                                    
                			
                		}else{
                			$form_errors = $this->form_validation->error_array();
                			$dataTosend = ['status'=>false,'msg'=>'','body'=>$form_errors];
                			
                		}
             
                     echo json_encode($dataTosend);
                            break;
                }
             case "update_address":
               {
                 
                $form_errors = '';
                $update_address_rules = [
                			'update_address_info' => [
                				[
                					'field' => 'name',
                					'label' => 'Name',
                					'rules' => 'required',
                					'errors' => [
                	                    'required' => 'Name field is required.',
                		            ],
                				],
                				[
                					'field' => 'email',
                					'label' => 'Email',
                					'rules' => 'required',
                					'errors' => [
                	                    'required' => 'Email name field is required.',
                		            ],
                				],
                				[
                					'field' => 'contact_no',
                					'label' => 'Contact Number',
                					'rules' => 'required',
                					'errors' => [
                	                    'required' => 'Contact Number field is required.',
                		            ],
                				],
                				[
                					'field' => 'state_id',
                					'label' => 'State',
                					'rules' => 'required',
                					'errors' => [
                	                    'required' => 'State field is required.',
                		            ],
                				],
                				[
                					'field' => 'city_id',
                					'label' => 'City',
                					'rules' => 'required',
                					'errors' => [
                	                    'required' => 'City field is required.',
                		            ],
                				],
                				[
                					'field' => 'address',
                					'label' => 'Address',
                					'rules' => 'required',
                					'errors' => [
                	                    'required' => 'Address field is required.',
                		            ],
                				],
                				[
                					'field' => 'street',
                					'label' => 'Address',
                					'rules' => 'required',
                					'errors' => [
                	                    'required' => 'Address field is required.',
                		            ],
                				],
                				[
                					'field' => 'zipcode',
                					'label' => 'Zipcode',
                					'rules' => 'required',
                					'errors' => [
                	                    'required' => 'Zipcode field is required.',
                		            ],
                				]
                			]
                		];
                		
                		$post = $this->input->post();
                	//	print_r($post);die;
                		$this->form_validation->set_data($post);
                		// $this->form_validation->reset_validation();
                		$this->form_validation->set_rules($update_address_rules['update_address_info']);
                		
                		if($this->form_validation->run() == TRUE){
                		        if($post['default_address']==1){
                		            $qr="update tbl_user_address set default_address=0 where user_id=".$post['user_id'];
                                        $res=$this->db->query($qr);
                		        }
                                       
                                        $id	                = $post['id'];
                                        $user_id	        = $post['user_id'];
                                        $name  	            = $post['name'];
                                        $address_email	    = $post['email'];
                            			$u_state            = $post['state_id'];
                            			$user_contact       = $post['contact_no'];
                            			$address	        = $post['address'];
                            			$street	            = $post['street'];
                            			$city	    	    = $post['city_id'];
                            			$zipcode	        = $post['zipcode'];
                            			$default_address    = $post['default_address'];
                            			$updated_at 	    = date('Y-m-d H:i:s');
                $data=['user_id'=>$user_id,'name'=>$name,'address_email'=>$address_email,'u_state'=>$u_state,'user_contact'=>$user_contact,'address'=>$address,'street'=>$street,'city'=>$city,'zipcode'=>$zipcode,'default_address'=>$default_address,'updated_at'=>$updated_at];
                            	$address_updated =	$this->common_model->updateData('tbl_user_address', ['id'=>$id], $data);
                            		
                            			if($address_updated){
                                        $dataTosend = ['status'=>true,'msg'=>'Address updated successfully ! ','body'=>''];

                            			}else{
                            			$dataTosend = ['status'=>false,'msg'=>'Unable to update address . Please try again. ','body'=>''];
                            			} 
                                    
                			
                		}else{
                			$form_errors = $this->form_validation->error_array();
                			$dataTosend = ['status'=>false,'msg'=>'','body'=>$form_errors];
                			
                		}
             
                     echo json_encode($dataTosend);
                            break;
                }
                
                
                    
            default:
                {  
                    
                  $dataTosend = ['status'=>false,'msg'=>'invalid flag value ','body'=>""];
                     echo json_encode($dataTosend);
                     break;
                     
                }
        }
    
            
                    
            
    }

}