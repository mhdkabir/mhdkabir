<?php
   error_reporting(0);
require APPPATH .'/libraries/REST_Controller.php';
     
class MarketplaceController extends REST_Controller {
    
   
    public function __construct() {
       parent::__construct();
       
        $this->load->database();

         $this->load->model('Common_model', 'cm');   
       
    }
       
    public function index_post(){
             
       $flag = $this->input->post('flag');
     
       switch ($flag){
            // echo $flag;die;
            
            case "view_marketplace_products":
               {
                 $user_id=$this->input->post(user_id);
                   //$offset_index=$this->input->post('offset_index');
                $q1="select * from tbl_shop_product order by prod_id desc";
                $product_foud=$this->db->query($q1)->result();
              
                 if($product_foud){
                            for($i=0;$i<count($product_foud);$i++){
                                $q="select id from tbl_my_wishlist where user_id='$user_id' and prod_id=".$product_foud[$i]->prod_id;
                                $res=$this->db->query($q)->row();
                                if($res){
                                    $product_foud[$i]->exist_in_wishlist="1";
                                }else{
                                    $product_foud[$i]->exist_in_wishlist="0";
                                }
                                $product_foud[$i]->featured_image=base_url().'common_uploads/product_images/'.$product_foud[$i]->featured_image;
                                $product_foud[$i]->percentage_off=(round((($product_foud[$i]->price - $product_foud[$i]->offer)*100)/$product_foud[$i]->price)).'%';
                                // $product_foud[$i]->percentage_off=(round(($product_foud[$i]->offer*100)/$product_foud[$i]->price)).'%';
                                
                                $q2="select * from tbl_shop where user_id=".$product_foud[$i]->user_id;
                                $shop_foud=$this->db->query($q2)->row();
                                if($shop_foud){
                                    $shop_foud->shop_image=base_url().'common_uploads/shop_images/'.$shop_foud->shop_image;
                                    $product_foud[$i]->shop_details=$shop_foud;
                                     //print_r($product_foud->shop_details);
                                }else{
                                    $array=[];
                                     $product_foud[$i]->shop_details=(object)$array;
                                }
                               
                            }
                             // print_r($product_foud[0]);die();
                             $dataTosend = ['status'=>true,'msg'=>'Poduct found.','body'=>$product_foud];
                        }else{
                            $dataTosend = ['status'=>false,'msg'=>'Poduct not found.','body'=>''];
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