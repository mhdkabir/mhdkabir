<?php
   error_reporting(0);
require APPPATH .'/libraries/REST_Controller.php';
     
class ShopController extends REST_Controller {
    
   
    public function __construct() {
       parent::__construct();
       
        $this->load->database();

         $this->load->model('Common_model', 'cm');   
       
    }
       
    public function index_post(){
             
       $flag = $this->input->post('flag');
       //echo $flag;die;
       switch ($flag){
           
            case "get_states": 
               {
                  $state_qry="select * from tbl_state_list ";
                  $state_list=$this->db->query($state_qry)->result();
                        if($state_list){
                              $dataTosend = ['status'=>true,'msg'=>'state_list found .','body'=>$state_list];
                        }else{
                             $dataTosend = ['status'=>false,'msg'=>'state_list not found . ','body'=>""];
                        }
                       
                         echo json_encode($dataTosend);
                                break;
                }
                
            case "get_city":
               {
                  $state_id = $this->input->post('state_id');
                 // $city_name = $this->input->post('city_name');
                  $city_qry="select city_id,city_name from tbl_city_list where state_id='$state_id' ";
                  $city_list=$this->db->query($city_qry)->result();
                        if($city_list){
                              $dataTosend = ['status'=>true,'msg'=>'city_list found .','body'=>$city_list];
                        }else{
                             $dataTosend = ['status'=>false,'msg'=>'city_list not found . ','body'=>""];
                        }
                       
                         echo json_encode($dataTosend);
                                break;
                }
            
            case "get_category":
               {
                
                  $category_qry="select category_id,category_name from tbl_category";
                  $category_found=$this->db->query($category_qry)->result();
                        if($category_found){
                              $dataTosend = ['status'=>true,'msg'=>'Category found successfully !.','body'=>$category_found];
                        }else{
                             $dataTosend = ['status'=>false,'msg'=>'Category not found .','body'=>''];
                        }
                       
                         echo json_encode($dataTosend);
                                break;
                }
            case "show_shop_details":
               {
                  $shop_id=$this->input->post('shop_id');     
                  $user_id=$this->input->post('user_id');     
                  $shop_qry="select * from tbl_shop where shop_id='$shop_id' ";
                  $shop_details_found=$this->db->query($shop_qry)->row();
                        if($shop_details_found){
                            $shop_details_found->shop_image=base_url().'common_uploads/shop_images/'.$shop_details_found->shop_image;
                             $product_qry="select * from tbl_shop_product where user_id=".$shop_details_found->user_id." order by prod_id desc";
                             $product_found=$this->db->query($product_qry)->result();
                             if($product_found){
                                 for($i=0;$i<count($product_found);$i++){
                                      $q="select id from tbl_my_wishlist where user_id='$user_id' and prod_id=".$product_found[$i]->prod_id;
                                        $res=$this->db->query($q)->row();
                                        if($res){
                                            $product_found[$i]->exist_in_wishlist=1;
                                        }else{
                                            $product_found[$i]->exist_in_wishlist=0;
                                        }
                                $product_found[$i]->percentage_off=(round((($product_found[$i]->price - $product_found[$i]->offer)*100)/$product_found[$i]->price)).'%';
                                // $product_found[$i]->percentage_off=(round(($product_found[$i]->offer*100)/$product_found[$i]->price)).'%';
                                $product_found[$i]->featured_image=base_url().'common_uploads/product_images/'.$product_found[$i]->featured_image;
                                 }
                                 $shop_details_found->shop_products=$product_found;
                             }else{
                                $shop_details_found->shop_products='No product available in this shop.';                                 
                             }
                              $dataTosend = ['status'=>true,'msg'=>'Shop details found successfully !.','body'=>$shop_details_found];
                        }else{
                             $dataTosend = ['status'=>false,'msg'=>'Shop details not found ','body'=>''];
                        }
                       
                         echo json_encode($dataTosend);
                                break;
                }
           
            case "update_shop":
               {
                 
                          $form_errors = '';
                		  $shop_rules = [
                			'update_shop_info' => [
                				[
                					'field' => 'shop_name',
                					'label' => 'Shop Name',
                					'rules' => 'required',
                					'errors' => [
                	                    'required' => 'Shop Name field is required.',
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
                					'field' => 'contact_no',
                					'label' => 'Contact Number',
                					'rules' => 'required|numeric|min_length[10]|max_length[10] ',
                					'errors' => [
                	                    'required' => 'Contact Number field is required.',
                	                    'numeric' => 'Contact Number should be in digits.',
                	                    'min_length' => 'Contact Number minimun length is 10 digits',
                	                    'max_length' => 'Contact Number maximum length is 10 digits',
                	                   
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
                					'field' => 'description',
                					'label' => 'Description',
                					'rules' => 'required',
                					'errors' => [
                	                    'required' => 'Description field is required.',
                		            ],
                				],
                			]
                		];
                		
                		$post = $this->input->post();
                	//	print_r($post);die;
                		$this->form_validation->set_data($post);
                		// $this->form_validation->reset_validation();
                		$this->form_validation->set_rules($shop_rules['update_shop_info']);
                		
                		if($this->form_validation->run() == TRUE){
                		    
                                $shop_image = '';
                                if(isset($_FILES['shop_image'])){
                                    
                                    $ext = pathinfo($_FILES['shop_image']['name'], PATHINFO_EXTENSION);
                                    $shop_image = rand(1000,9999).time().'.'.$ext;
                                    
                                    //$shop_image = rand(1000,9999).$_FILES['shop_image']['name'];
                                    $target_file = "./common_uploads/shop_images/".$shop_image;
                    	   
                                        if (!move_uploaded_file($_FILES["shop_image"]["tmp_name"], $target_file))
                                        {
                                              $shop_image = '';
                                        }
                                        $shop_id	    	= $post['shop_id'];
                                        $user_id	    	= $post['user_id'];
                                        $shop_name	    	= $post['shop_name'];
                            			$shop_contact_no	= $post['contact_no'];
                            			$state_id	        = $post['state_id'];
                            			$city_id	    	= $post['city_id'];
                            			$address	        = $post['address'];
                            			$description	    = $post['description'];
                            			$shop_image		    = $shop_image;
                            			//$created_at 	    = date('Y-m-d H:i:s');
                            			$updated_at 	    = date('Y-m-d H:i:s');
                				$data=['user_id'=>$user_id,'shop_name'=>$shop_name,'shop_contact_no'=>$shop_contact_no,'state_id'=>$state_id,'city_id'=>$city_id,'address'=>$address,'description'=>$description,'shop_image'=>$shop_image,'updated_at'=>$updated_at];
                				$shop_updated =	$this->common_model->updateData('tbl_shop', ['shop_id'=>$shop_id], $data);
                            		
                            			
                            			if($shop_updated){
                            			      $dataTosend = ['status'=>true,'msg'=>'Shop updated successfully !.','body'=>''];
                            			}else{
                            			$dataTosend = ['status'=>false,'msg'=>'Unable to update shop details . Please try again. ','body'=>''];
                            			} 
                                        
                                        
                                        
                                }else{
                                   $dataTosend = ['status'=>false,'msg'=>'Shop Image is required ','body'=>'']; 
                                }  
                		 
                			
                		}else{
                			$form_errors = $this->form_validation->error_array();
                			$dataTosend = ['status'=>false,'msg'=>$form_errors,'body'=>''];
                			
                		}
             
                     echo json_encode($dataTosend);
                            break;
                }
                case "add_shop":
               {
                 $shop_name=$this->input->post('shop_name');
                 if(!empty($shop_name)){
                     $form_errors = '';
                          $shop_rules = [
                            'add_shop_info' => [
                                [
                                    'field' => 'shop_name',
                                    'label' => 'Shop Name',
                                    'rules' => 'required',
                                    'errors' => [
                                        'required' => 'Shop Name field is required.',
                                    ],
                                ],
                                [
                                    'field' => 'user_id',
                                    'label' => 'User',
                                    'rules' => 'required|is_unique[tbl_shop.user_id]',
                                    'errors' => [
                                        'required' => 'User ID field is required.',
                                         'is_unique' => 'You can add only one shop.',
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
                                    'field' => 'contact_no',
                                    'label' => 'Contact Number',
                                    'rules' => 'required|numeric|min_length[10]|max_length[10] ',
                                    'errors' => [
                                        'required' => 'Contact Number field is required.',
                                        'numeric' => 'Contact Number should be in digits.',
                                        'min_length' => 'Contact Number minimun length is 10 digits',
                                        'max_length' => 'Contact Number maximum length is 10 digits',
                                        
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
                                    'field' => 'description',
                                    'label' => 'Description',
                                    'rules' => 'required',
                                    'errors' => [
                                        'required' => 'Description field is required.',
                                    ],
                                ],
                            ]
                        ];
                        
                        $post = $this->input->post();
                    //  print_r($post);die;
                        $this->form_validation->set_data($post);
                        // $this->form_validation->reset_validation();
                        $this->form_validation->set_rules($shop_rules['add_shop_info']);
                        
                        if($this->form_validation->run() == TRUE){
                            
                                $shop_image = '';
                                if(isset($_FILES['shop_image'])){ 
                                    
                                     $ext = pathinfo($_FILES['shop_image']['name'], PATHINFO_EXTENSION);
                                    $shop_image = rand(1000,9999).time().'.'.$ext;
                                    
                                    //$shop_image = rand(1000,9999).$_FILES['shop_image']['name'];
                                    $target_file = "./common_uploads/shop_images/".$shop_image;
                           
                                        if (!move_uploaded_file($_FILES["shop_image"]["tmp_name"], $target_file))
                                        {
                                              $shop_image = '';
                                        }
                                        $form_data['user_id']           = $post['user_id'];
                                        $form_data['shop_name']         = $post['shop_name'];
                                        $form_data['shop_contact_no']   = $post['contact_no'];
                                        $form_data['state_id']          = $post['state_id'];
                                        $form_data['city_id']           = $post['city_id'];
                                        $form_data['address']           = $post['address'];
                                        $form_data['description']       = $post['description'];
                                        $form_data['shop_image']        = $shop_image;
                                        $form_data['created_at']        = date('Y-m-d H:i:s');
                                        $form_data['updated_at']        = date('Y-m-d H:i:s');
                                
                                        $shop_added = $this->common_model->addData('tbl_shop', $form_data);
                                        
                                        if($shop_added){
                                              $dataTosend = ['status'=>true,'shop_status'=>0,'msg'=>'Shop added successfully !.','body'=>''];
                                        }else{
                                        $dataTosend = ['status'=>false,'msg'=>'Unable to add shop . Please try again. ','body'=>''];
                                        } 
                                        
                                        
                                        
                                }else{
                                   $dataTosend = ['status'=>false,'msg'=>'Shop Image is required ','body'=>'']; 
                                }  
                         
                            
                        }else{
                            $form_errors = $this->form_validation->error_array();
                            $dataTosend = ['status'=>false,'msg'=>$form_errors,'body'=>''];
                            
                        }
                 }else{
                     $user_id = $this->input->post('user_id');
                     $find_shop_qry="select * from tbl_shop where user_id='$user_id'";
                     $shop_found=$this->db->query($find_shop_qry)->row();
                     if($shop_found){
                        // echo $_SERVER['HTTP_HOST'];die;
                        // $shop_found->shop_image='https://'.$_SERVER['HTTP_HOST'].'common_uploads/shop_images/'.$shop_found->shop_image;
                         $shop_found->shop_image = base_url().'common_uploads/shop_images/'.$shop_found->shop_image;
                            $dataTosend = ['status'=>true,'shop_status'=>1,'msg'=>'Shop already added.','body'=>$shop_found];
                     }else{
                            $dataTosend = ['status'=>false,'shop_status'=>0,'msg'=>'Shop not found .','body'=>'']; 
                     }
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