<?php
   error_reporting(0);
require APPPATH .'/libraries/REST_Controller.php';
     
class CartController extends REST_Controller {
    
   
    public function __construct() {
       parent::__construct();
       
        $this->load->database();

         $this->load->model('Common_model', 'cm');   
       
    }
       
    public function index_post(){
             
       $flag = $this->input->post('flag');
     
       switch ($flag){
            // echo $flag;die;
            
            case "add_to_cart":
               {
                   $prod_id =   $this->input->post('prod_id');
                   $user_id =   $this->input->post('user_id');
                   $qty     =   $this->input->post('quantity');
                   $size     =   $this->input->post('product_size');
                 $q1="select id,qty,size,price ,offer from tbl_cart where (prod_id='$prod_id' and user_id='$user_id' and size='$size')";
                $product_exist=$this->db->query($q1)->row(); 
                 // print_r($product_exist);die;
                   if($product_exist){
            //               print_r($product_exist);die;
            // echo 'ex'.$product_exist->qty;
            // echo 'new'.$qty;
            // die;
                        $id	    	        = $product_exist->id;
                        $user_id	    	= $user_id;
                        $qty	    	    = $product_exist->qty + $qty;
            			$prod_id	        = $prod_id;
            			$offer	            = $product_exist->offer;
            			$offer_total	   	= $qty*$product_exist->offer;
            			$size		        = $product_exist->size;
            			$price		        = $product_exist->price;
            			$price_total		= $qty*$product_exist->price;
            			$final_total		= $qty*($product_exist->price - $product_exist->offer);
            			$updated_at	        = date('Y-m-d H:i:s');
            
            			$data=['user_id'=>$user_id,'qty'=>$qty,'prod_id'=>$prod_id,'offer'=>$offer,'offer_total'=>$offer_total,'size'=>$size,'price'=>$price,'price_total'=>$price_total,'final_total'=>$final_total,'updated_at'=>$updated_at];
                        // print_r($data);die;
                        $cart_updated =	$this->common_model->updateData('tbl_cart', ['id'=>$id], $data);
                        
                        if($cart_updated){
                            $dataTosend = ['status'=>true,'msg'=>'Product added successfully !','body'=>''];    
                        }else{
                            $dataTosend = ['status'=>false,'msg'=>'Failed to  add product !','body'=>''];
                        }
                        
                   }else{
                       $q1="select price ,offer from tbl_shop_product where prod_id='$prod_id'";
                       $product=$this->db->query($q1)->row();
              
                 if($product){
                     
                        $form_data['user_id']	    	= $user_id;
                        $form_data['qty']	    	    = $qty;
            			$form_data['prod_id']	        = $prod_id;
            			$form_data['offer']	            = $product->offer;
            			$form_data['offer_total']	   	= $qty*$product->offer;
            			$form_data['size']		        = $size;
            			$form_data['price']		        = $product->price;
            			$form_data['price_total']		= $qty*$product->price;
            			$form_data['final_total']		= $qty*($product->price-$product->offer);
            			$form_data['updated_at'] 	    = date('Y-m-d H:i:s');
            			$form_data['created_at'] 	    = date('Y-m-d H:i:s');
    			
            			$added_to_cart = $this->common_model->addData('tbl_cart', $form_data);
            			    if($added_to_cart){
            			        $dataTosend = ['status'=>true,'msg'=>'Product added successfully !','body'=>''];
            			    }else{
            			        $dataTosend = ['status'=>false,'msg'=>'Add to cart process failed ! Try again.','body'=>''];
            			    }
                        }else{
                            $dataTosend = ['status'=>false,'msg'=>'Add to cart process failed ! Try again.','body'=>''];
                        } 
                   }
               
               
                     echo json_encode($dataTosend);
                            break;
                }
                
                case "re_order":
               {
                   $order_id =   $this->input->post('order_id');
                   $user_id =   $this->input->post('user_id');
                   
                 $q1="select * from tbl_order_detail where (order_id='$order_id')";
                 $product_exists=$this->db->query($q1)->result_array(); 
                 
                 if($product_exists){
                     foreach($product_exists as $row){
                         $prod_id	    	        = $row['prod_id'];
                         $quantity	    	        = $row['quantity'];
                         $size	    	            = $row['size'];
             
                         
                          $q1="select price ,offer from tbl_shop_product where prod_id='$prod_id'";
                       $product=$this->db->query($q1)->row();
              
                //   print_r($product);die;
                 
            
                       if($product){
                     
                           $form_data['user_id']	    	= $user_id;
                           $form_data['qty']	    	    = $quantity;
            		    	$form_data['prod_id']	        = $prod_id;
            		    	$form_data['offer']	            = $product->offer;
            		    	$form_data['offer_total']	   	= $quantity*$product->offer;
            			  $form_data['size']		        = $size;
            			   $form_data['price']		        = $product->price;
            		     	$form_data['price_total']		= $quantity*$product->price;
            			   $form_data['final_total']		= $quantity*($product->price-$product->offer);
            			   $form_data['updated_at'] 	    = date('Y-m-d H:i:s');
            			   $form_data['created_at'] 	    = date('Y-m-d H:i:s');
    // 			print_r($form_data);die;
            		     	$added_to_cart = $this->common_model->addData('tbl_cart', $form_data);
            			    if($added_to_cart){
            			        $dataTosend = ['status'=>true,'msg'=>'Product added successfully !','body'=>''];
            			    }else{
            			        $dataTosend = ['status'=>false,'msg'=>'Add to cart process failed ! Try again.','body'=>''];
            			    }
                        }else{
                            $dataTosend = ['status'=>false,'msg'=>'Add to cart process failed ! Try again.','body'=>''];
                        }
                         
                     }
                   }else{
                       $dataTosend = ['status'=>true,'msg'=>'Product not found','body'=>""];
                   }
                 
                 
                 
                     
                    
                    
                    echo json_encode($dataTosend);
                            break;
                }
                
             case "view_cart":
               {
                   $user_id =   $this->input->post('user_id');      
                    $q = "SELECT a.*,b.user_id as onner_id,b.color,b.prod_name,b.size,b.offer,b.description,b.featured_image  FROM tbl_cart as a 
                            left join tbl_shop_product as b on a.prod_id = b.prod_id WHERE a.user_id = '$user_id'";
                   // $res = $this->cm->getData('tbl_cart',['user_id'=>$user_id]); 
                   
                   $res = $this->db->query($q)->result(); 
                    if($res){  $arr = $job= array();
                                foreach($res as $val){
                            //     $p_id = $res[0]->prod_id; 
                            //   $p_images = $this->cm->getData('tbl_product_gallery',['prod_id'=>$p_id]);    
                                $arr['id'] =$val->id;
                                $arr['qty'] =$val->qty;
                                $arr['offer'] =$val->offer;
                                $arr['offer_total'] =$val->offer_total;
                                $arr['price'] =$val->price;
                                $arr['price_total'] =$val->price_total;
                                $arr['final_total'] =$val->final_total;
                                $arr['percentage_offer'] =(round((($val->price - $val->offer)*100)/$val->price)).'%';         
                               
                                $arr['prod_id'] =$val->prod_id;
                                $arr['prod_name'] =$val->prod_name;
                                $arr['onner_id'] =$val->onner_id;
                                $arr['color'] =$val->color;
                                $arr['size'] =$val->size;
                                $arr['description'] =$val->description;
                                $arr['p_image'] = isset($val->featured_image) ? base_url().'common_uploads/product_images/'.$val->featured_image : '' ;
                                    $job[] = $arr;       
                                }
            			        $dataTosend = ['status'=>true,'msg'=>'success','body'=>$job];
            			    }else{
            			        $dataTosend = ['status'=>false,'msg'=>'No data found!..','body'=>''];
            			    }
                    
                     echo json_encode($dataTosend);
                            break;
                }
             case "update_quantity":
               {
                   $id      =   $this->input->post('id');
                   $prod_id =   $this->input->post('prod_id');
                   $user_id =   $this->input->post('user_id');
                   $qty     =   $this->input->post('quantity');
                 
                 $q1="select price ,offer from tbl_shop_product where prod_id='$prod_id'";
                $product=$this->db->query($q1)->row();
              
                 if($product){
                     
                        $user_id	    	= $user_id;
                        $qty	    	    = $qty;
            			$prod_id	        = $prod_id;
            			$offer	            = $product->offer;
            			$offer_total	   	= $qty*$product->offer;
            			$price		        = $product->price;
            			$price_total		= $qty*$product->price;
            			$final_total		= $qty*($product->price-$product->offer);
            			$updated_at	        = date('Y-m-d H:i:s');
            		
            			$data=['user_id'=>$user_id,'qty'=>$qty,'prod_id'=>$prod_id,'offer'=>$offer,'offer_total'=>$offer_total,'price'=>$price,'price_total'=>$price_total,'final_total'=>$final_total,'updated_at'=>$updated_at];
                        $cart_updated =	$this->common_model->updateData('tbl_cart', ['id'=>$id], $data);		
    
            			    if($cart_updated){
            			        $dataTosend = ['status'=>true,'msg'=>'Cart updated successfully !','body'=>''];
            			    }else{
            			        $dataTosend = ['status'=>false,'msg'=>'Failed ! Try again.','body'=>''];
            			    }
                        }else{
                            $dataTosend = ['status'=>false,'msg'=>'product not found! Try again.','body'=>''];
                        }
               
                     echo json_encode($dataTosend);
                            break;
                }    
                
            case "add_to_my_wishlist":
               {
                   $prod_id =   $this->input->post('prod_id');
                   $user_id =   $this->input->post('user_id');
                 
                 if($prod_id && $user_id){
                     $q="select id from tbl_my_wishlist where user_id='$user_id' and prod_id='$prod_id'";
                     $res=$this->db->query($q)->row();
                     if($res){
                          $dataTosend = ['status'=>false,'msg'=>'Product exist in wishlist.','body'=>''];
                     }else{
                        $form_data['user_id']	    	= $user_id;
                        $form_data['prod_id']	    	= $prod_id;
            			$form_data['updated_at'] 	    = date('Y-m-d H:i:s');
            			$form_data['created_at'] 	    = date('Y-m-d H:i:s');
    			
            			$added_to_wishlist = $this->common_model->addData('tbl_my_wishlist', $form_data);
            			    if($added_to_wishlist){
            			        $dataTosend = ['status'=>true,'msg'=>'Product added successfully !','body'=>''];
            			    }else{
            			        $dataTosend = ['status'=>false,'msg'=>'Add to wishlist process failed ! Try again.','body'=>''];
            			    }
                     }
                       
                        }else{
                            $dataTosend = ['status'=>false,'msg'=>'Add to wishlist process failed ! Try again.','body'=>''];
                        }
               
                     echo json_encode($dataTosend);
                            break;
                }
            case "view_my_wishlist":
               {
                 $user_id =   $this->input->post('user_id');
                 $q1="select id,prod_id from tbl_my_wishlist where user_id='$user_id' order by id desc";
                  $product = $this->db->query($q1)->result();
               // print_r($product);die;
                if($product){
                  
                     for($i=0;$i<count($product);$i++){
                         //  $product[$i]->user_id=$user_id;
                         $q2="select * from tbl_shop_product where prod_id=".$product[$i]->prod_id;
                         $product_info=$this->db->query($q2)->row();
                         if($product_info){
                             $arr['id']         =   $product[$i]->id;
                             $arr['prod_id']    =   $product[$i]->prod_id;
                             $arr['user_id']    =   $user_id;
                             $arr['category_id']=   $product_info->category_id;
                             $arr['prod_name']  =   $product_info->prod_name;
                             $arr['color']      =   $product_info->color;
                             $arr['size']       =   $product_info->size;
                             $arr['price']      =   $product_info->price;
                             $arr['offer']      =   $product_info->offer;
                             $arr['description']=   $product_info->description;
                            
                             $arr['featured_image'] = base_url().'/common_uploads/product_images/'.$product_info->featured_image;
                             $arr['percentage_off']=(round((($product_info->price - $product_info->offer)*100)/$product_info->price)).'%';
                             //$product_info->percentage_off=(round(($product_info->offer*100)/$product_info->price)).'%';
                             $q3 = "select * from tbl_shop where user_id=".$product_info->user_id;
                             $shop_foud=$this->db->query($q3)->row();
                                if($shop_foud){
                                   
                                    $shop_foud->shop_image = base_url().'common_uploads/shop_images/'.$shop_foud->shop_image;
                                     $arr['shop_details']=['action'=>true,'res'=>$shop_foud];
                                    //$product_info->shop_details=$shop_foud;
                                    
                                }else{
                                    $array=[];
                                     $arr['shop_details']=['action'=>false,'res'=>(object)$array];
                                    // $product_info->shop_details='Shop details not available.';
                                }
                              
                               //$product[$i]->product_details=$product_info;
                                $job[]=$arr;
                         }
                             
                     }
                     //print_r($job);die;
                     if($job!=null){
                      $dataTosend = ['status'=>true,'msg'=>'Products available in wishlist.','body'=>$job];   
                     }else{
                         $dataTosend = ['status'=>false,'msg'=>'No Poduct available in wishlist.','body'=>''];
                     }
                      
                     
                }else{
                   $dataTosend = ['status'=>false,'msg'=>'No Poduct available in wishlist.','body'=>'']; 
                }
                
             
                     echo json_encode($dataTosend);
                            break;
                }
              case "delete_cart":
               {
                    $id = $this->input->post('id');  
                    
                    
                    $q ="delete from tbl_cart where id ='$id'";
                    $res = $this->db->query($q);
                    if($res){
            			        $dataTosend = ['status'=>true,'msg'=>'cart item deleted successfully','body'=>''];
            			    }else{
            			        $dataTosend = ['status'=>false,'msg'=>'cart item not delete','body'=>''];
            			    }
                             echo json_encode($dataTosend);
                            break;
                }
           case "delete_wishlist":
               {
                    $prod_id = $this->input->post('prod_id');  
                    $user_id = $this->input->post('user_id');  
                    $q ="delete from tbl_my_wishlist where prod_id ='$prod_id' and user_id= '$user_id'";
                    $res = $this->db->query($q);
                    if($res){
            			        $dataTosend = ['status'=>true,'msg'=>'wishlist deleted successfully','body'=>''];
            			    }else{
            			        $dataTosend = ['status'=>false,'msg'=>'wishlist not delete','body'=>''];
            			    }
                             echo json_encode($dataTosend);
                            break;
                }
            case "delete_product":
               {
                 $prod_id=$this->input->post('prod_id');
                if(!empty($prod_id)){
                    $q1="delete from tbl_shop_product where prod_id='$prod_id'";
                    $product_deleted=$this->db->query($q1);
                    // $product_foud->featured_image=base_url().'common_uploads/product_images/'.$product_foud->featured_image;
                        if($product_deleted){
                            $q2="delete from tbl_product_gallery where prod_id='$prod_id'";
                            $gallery_images=$this->db->query($q2);
                             if($gallery_images){
                                $dataTosend = ['status'=>true,'msg'=>'Poduct deleted successfully !.','body'=>''];  
                             }else{
                                  $dataTosend = ['status'=>false,'msg'=>'Unable to delete product.','body'=>''];
                             }
                        
                        }else{
                            $dataTosend = ['status'=>false,'msg'=>'Unable to delete product.','body'=>''];
                        }
                }else{
                  $dataTosend = ['status'=>false,'msg'=>'Somthing went wrong .Please try again.','body'=>''];   
                }
             
                     echo json_encode($dataTosend);
                            break;
                }        
           case "add_contactUs":
               {
                   $name =   $this->input->post('name');
                   $email =   $this->input->post('email');
                   $dis     =   $this->input->post('dis');
               if(empty($name) || empty($email) || empty($dis)){
                      $dataTosend = ['status'=>false,'msg'=>'All field required','body'=>''];
                     echo json_encode($dataTosend); die(); 
                  }  
                         
    		        	$whr = ['contact_name'=> $name,'contact_email'=> $email,'contact_decription'=>$dis ];
            			     $res = $this->cm->addData('tbl_contact_us', $whr);
            			    if($res){
            			        $dataTosend = ['status'=>true,'msg'=>'contact added successfully !','body'=>''];
            			    }else{
            			        $dataTosend = ['status'=>false,'msg'=>'contact add process failed ! Try again.','body'=>''];
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