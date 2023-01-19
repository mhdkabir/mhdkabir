<?php
   error_reporting(0);
require APPPATH .'/libraries/REST_Controller.php';
     
class ProductController extends REST_Controller {
    
   
    public function __construct() {
       parent::__construct();
       
        $this->load->database();

         $this->load->model('Common_model', 'cm');   
       
    }
       
    public function index_post(){
             
       $flag = $this->input->post('flag');
     
       switch ($flag){
            // echo $flag;die;
            case "add_product":
               {
                 
                $form_errors = '';
                $add_product_rules = [
                            'add_product_info' => [
                                [
                                    'field' => 'category_id',
                                    'label' => 'Category Name',
                                    'rules' => 'required',
                                    'errors' => [
                                        'required' => 'Category Name field is required.',
                                    ],
                                ],
                                [
                                    'field' => 'product_name',
                                    'label' => 'Product',
                                    'rules' => 'required',
                                    'errors' => [
                                        'required' => 'Product name field is required.',
                                    ],
                                ],
                                [
                                    'field' => 'product_color',
                                    'label' => 'Product Color',
                                    'rules' => 'required',
                                    'errors' => [
                                        'required' => 'Product color field is required.',
                                    ],
                                ],
                                [
                                    'field' => 'product_size',
                                    'label' => 'Product Size',
                                    'rules' => 'required',
                                    'errors' => [
                                        'required' => 'Product size field is required.',
                                    ],
                                ],
                                [
                                    'field' => 'product_price',
                                    'label' => 'Product Price',
                                    'rules' => 'required',
                                    'errors' => [
                                        'required' => 'Product price field is required.',
                                    ],
                                ],
                                [
                                    'field' => 'product_description',
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
                        $this->form_validation->set_rules($add_product_rules['add_product_info']);
                        
                        if($this->form_validation->run() == TRUE){
                            
                                $featured_image = '';
                                if(isset($_FILES['featured_image'])){  
                                    
                                    $ext = pathinfo($_FILES['featured_image']['name'], PATHINFO_EXTENSION);
                                    $featured_image = rand(1000,9999).time().'.'.$ext;
                                    
                                   // $featured_image = rand(1000,9999).$_FILES['featured_image']['name'];
                                    $target_file = "./common_uploads/product_images/".$featured_image;
                           
                                        if (!move_uploaded_file($_FILES["featured_image"]["tmp_name"], $target_file))
                                        {
                                              $featured_image = '';
                                        }
                                       //code to arrange product size in proper order like S,M,L,XL
                                        $arranged_prod_size=array();
                                        $product_size_arr   = explode(",",($post['product_size']));
                                         
                                            if(in_array("S", $product_size_arr)){
                                                array_push($arranged_prod_size,'S');
                                            }
                                            if(in_array("M", $product_size_arr)){
                                                array_push($arranged_prod_size,'M');
                                            }
                                            if(in_array("L", $product_size_arr)){
                                                array_push($arranged_prod_size,'L');
                                            }
                                            if(in_array("XL", $product_size_arr)){
                                                array_push($arranged_prod_size,'XL');
                                            }
                                        $str=implode(",",$arranged_prod_size);
                                       
                                        
                                        $form_data['user_id']       = $post['user_id'];
                                        $form_data['category_id']   = $post['category_id'];
                                        $form_data['prod_name']     = $post['product_name'];
                                        $form_data['color']         = $post['product_color'];
                                        $form_data['size']          = $str;
                                        $form_data['price']         = $post['product_price'];
                                        $form_data['offer']         = $post['product_offer'];
                                        $form_data['description']   = $post['product_description'];
                                        $form_data['featured_image']= $featured_image;
                                    
                                        $form_data['created_at']    = date('Y-m-d H:i:s');
                                        $form_data['updated_at']    = date('Y-m-d H:i:s');
                                
                                        $prod_id = $this->common_model->addData('tbl_shop_product', $form_data);
                                    //  $prod_id=1;
                                        if($prod_id){
                                            for($i=1;$i<4;$i++){
                                                $prod_image = '';
                                                if(isset($_FILES['prod_image_'.$i])){    
                                                    $ext = pathinfo($_FILES['prod_image_'.$i]['name'], PATHINFO_EXTENSION);
                                                    $prod_image = rand(1000,9999).time().'.'.$ext;
                                                    //echo $prod_image;
                                                    $target_file = "./common_uploads/product_images/".$prod_image;
                                           
                                                        if (!move_uploaded_file($_FILES['prod_image_'.$i]["tmp_name"], $target_file))
                                                        {
                                                              $prod_image = '';
                                                        }
                                                         $prod_data['prod_id']      = $prod_id;
                                                         $prod_data['prod_image']   = $prod_image;
                                                         $gallery_id = $this->common_model->addData('tbl_product_gallery', $prod_data);
                                                         $prod_pics_count[]=$gallery_id;
                                                            
                                                }
                                                                                                 
                                            }
                                           // die();
                                                $total_pics=count($prod_pics_count);
                                                 $dataTosend = ['status'=>true,'msg'=>'Product added successfully !.','body'=>$total_pics];
                                            

                                        }else{
                                        $dataTosend = ['status'=>false,'msg'=>'Unable to add product . Please try again. ','body'=>''];
                                        } 
                                        
                                        
                                        
                                }else{
                                   $dataTosend = ['status'=>false,'msg'=>'Product Image is required ','body'=>'']; 
                                }  
                         
                            
                        }else{
                            $form_errors = $this->form_validation->error_array();
                            $dataTosend = ['status'=>false,'msg'=>'','body'=>$form_errors];
                            
                        }
             
                     echo json_encode($dataTosend);
                            break;
                }
            case "update_product":
               {
                $form_errors = '';
                $update_product_rules = [
                            'update_product_info' => [
                                [
                                    'field' => 'category_id',
                                    'label' => 'Category Name',
                                    'rules' => 'required',
                                    'errors' => [
                                        'required' => 'Category Name field is required.',
                                    ],
                                ],
                                [
                                    'field' => 'product_name',
                                    'label' => 'Product',
                                    'rules' => 'required',
                                    'errors' => [
                                        'required' => 'Product name field is required.',
                                    ],
                                ],
                                [
                                    'field' => 'product_color',
                                    'label' => 'Product Color',
                                    'rules' => 'required',
                                    'errors' => [
                                        'required' => 'Product color field is required.',
                                    ],
                                ],
                                [
                                    'field' => 'product_size',
                                    'label' => 'Product Size',
                                    'rules' => 'required',
                                    'errors' => [
                                        'required' => 'Product size field is required.',
                                    ],
                                ],
                                [
                                    'field' => 'product_price',
                                    'label' => 'Product Price',
                                    'rules' => 'required',
                                    'errors' => [
                                        'required' => 'Product price field is required.',
                                    ],
                                ],
                                [
                                    'field' => 'product_description',
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
                        $this->form_validation->set_rules($update_product_rules['update_product_info']);
                        
                        if($this->form_validation->run() == TRUE){
                            
                                $featured_image = '';
                                if(isset($_FILES['featured_image'])){  
                                    
                                    $ext = pathinfo($_FILES['featured_image']['name'], PATHINFO_EXTENSION);
                                    $featured_image = rand(1000,9999).time().'.'.$ext;
                                    
                                   // $featured_image = rand(1000,9999).$_FILES['featured_image']['name'];
                                    $target_file = "./common_uploads/product_images/".$featured_image;
                           
                                        if (!move_uploaded_file($_FILES["featured_image"]["tmp_name"], $target_file))
                                        {
                                              $featured_image = '';
                                        }
                                         //code to arrange product size in proper order like S,M,L,XL
                                         $arranged_prod_size=array();
                                        $product_size_arr   = explode(",",($post['product_size']));
                                         
                                            if(in_array("S", $product_size_arr)){
                                                array_push($arranged_prod_size,'S');
                                            }
                                            if(in_array("M", $product_size_arr)){
                                                array_push($arranged_prod_size,'M');
                                            }
                                            if(in_array("L", $product_size_arr)){
                                                array_push($arranged_prod_size,'L');
                                            }
                                            if(in_array("XL", $product_size_arr)){
                                                array_push($arranged_prod_size,'XL');
                                            }
                                        $str=implode(",",$arranged_prod_size);
                                     
                                        $prod_id         = $post['prod_id'];
                                        $user_id         = $post['user_id'];
                                        $category_id     = $post['category_id'];
                                        $prod_name       = $post['product_name'];
                                        $color           = $post['product_color'];
                                        $size            = $str;
                                        $price           = $post['product_price'];
                                        $offer           = $post['product_offer'];
                                        $description     = $post['product_description'];
                                        $featured_image  = $featured_image;
                                        $updated_at      = date('Y-m-d H:i:s');
                                $data=['user_id'=>$user_id,'category_id'=>$category_id,'prod_name'=>$prod_name,'color'=>$color,'size'=>$size,'price'=>$price,'offer'=>$offer,'description'=>$description,'featured_image'=>$featured_image,'updated_at'=>$updated_at];
                                $prod_updated = $this->common_model->updateData('tbl_shop_product', ['prod_id'=>$prod_id], $data);      
                                    
                                if($prod_updated){
                                            $gallery_deleted=$this->common_model->delete_data('tbl_product_gallery', ['prod_id'=>$prod_id]);
                                            if($gallery_deleted){
                                                 for($i=1;$i<4;$i++){
                                                        $prod_image = '';
                                                        if(!empty($_FILES['prod_image_'.$i]['name'])){    
                                                            $ext = pathinfo($_FILES['prod_image_'.$i]['name'], PATHINFO_EXTENSION);
                                                            $prod_image = rand(1000,9999).time().'.'.$ext;
                                                            //echo $prod_image;
                                                            $target_file = "./common_uploads/product_images/".$prod_image;
                                                   
                                                                if (!move_uploaded_file($_FILES['prod_image_'.$i]["tmp_name"], $target_file)){
                                                                      $prod_image = '';
                                                                }
                                                               
                                                            $gallery_data['prod_id']=$prod_id;
                                                            $gallery_data['prod_image']=$prod_image;
                                                            //  $gallery_id =   $this->common_model->updateData('tbl_product_gallery', ['id'=>$post['id_'.$i]], $data);
                                                                $gallery_id =   $this->common_model->insert('tbl_product_gallery',$gallery_data);
                                                                 
                                                                 $prod_pics_count[]=$gallery_id;
                                                                    
                                                        }
                                                    
                                                }
                                                 $total_pics=count($prod_pics_count);
                                                 $dataTosend = ['status'=>true,'msg'=>'Product updated successfully !.','body'=>$total_pics];
                                            }else{
                                                 $dataTosend = ['status'=>false,'msg'=>'Unable to update product gallery images . Please try again. ','body'=>'']; 
                                            }
                                            
                                            
                                           
                                                                                                 
                                            }else{
                                                $dataTosend = ['status'=>false,'msg'=>'Unable to update product . Please try again. ','body'=>''];
                                            }
                                        
                                               
                                            

                                        }else{
                                                 $dataTosend = ['status'=>false,'msg'=>'Product Image is required ','body'=>''];
                                        } 
                                        
                                        
                                        
                                }else{
                                    $form_errors = $this->form_validation->error_array();
                                    $dataTosend = ['status'=>false,'msg'=>'','body'=>$form_errors]; 
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
                                  $prod->percentage_off=(round((($prod->price - $prod->offer)*100)/$prod->price)).'%';
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
            
            case "edit_product":
               {
                 $prod_id=$this->input->post('prod_id');
                if(!empty($prod_id)){
                    $q1="select * from tbl_shop_product where prod_id='$prod_id'";
                    $product_foud=$this->db->query($q1)->row();
                     if($product_foud){
                          $product_foud->featured_image=base_url().'common_uploads/product_images/'.$product_foud->featured_image;
                            //$product_foud->percentage_off=(round(($product_foud->offer*100)/$product_foud->price)).'%';
                            $product_foud->percentage_off=(round((($product_foud->price - $product_foud->offer)*100)/$product_foud->price)).'%';

                            $q2="select id,prod_image from tbl_product_gallery where prod_id='$prod_id'";
                            $gallery_images=$this->db->query($q2)->result();
                             if($gallery_images){
                                  $i=1;
                                  foreach($gallery_images as $prod){
                                  
                                $arr['id'] = ($prod->id)?$prod->id:'';
                                $arr['image'] = ($prod->prod_image)?base_url().'common_uploads/product_images/'.$prod->prod_image:'';
                               // $prod->featured_image = base_url().'common_uploads/product_images/'.$prod->featured_image;
                               $product_foud->gallery[$i]=$arr;
                               $i++;
                                }
                                //  $job[]=$arr;
                                // $product_foud->gallery=$job;
                             }
                           // print_r($product_foud);die();
                           
                             $dataTosend = ['status'=>true,'msg'=>'Poduct found.','body'=>$product_foud];
                        }else{
                            $dataTosend = ['status'=>false,'msg'=>'Poduct not found.','body'=>''];
                        }
                }else{
                  $dataTosend = ['status'=>false,'msg'=>'Please select product.','body'=>''];   
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
                             $q2="delete from tbl_my_wishlist where prod_id='$prod_id'";
                            $wishlist_prod_deleted=$this->db->query($q2);
                             $q3="delete from tbl_cart where prod_id='$prod_id'";
                            $cart_prod_deleted=$this->db->query($q3);
                            $q4="delete from tbl_product_gallery where prod_id='$prod_id'";
                            $gallery_images=$this->db->query($q4);
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
            case "search_product":
               {
                 $user_id = $this->input->post('user_id');
                 $p_name  = $this->input->post('p_name');
               
                    $q="select * from tbl_shop_product where user_id = '$user_id' and  prod_name like '$p_name%'";
                    $product_foud = $this->db->query($q)->result();
                        if($product_foud){
                            foreach($product_foud as $prod){
                                $prod->featured_image = base_url().'common_uploads/product_images/'.$prod->featured_image;
                            }
                             $dataTosend = ['status'=>true,'msg'=>'Poduct found.','body'=>$product_foud];
                        }else{
                            $dataTosend  = ['status'=>false,'msg'=>'Poduct not found.','body'=>''];
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