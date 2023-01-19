<?php
/**
 * 
 */
class WardrobeController extends MY_Controller
{
	
	/*const GCSE_API_KEY = "AIzaSyAzlNcgBZ6UuyxP9URMcxZLhgd4SIezONU";
	const GCSE_SEARCH_ENGINE_ID = "013933632415335686094:0ra2xj8calo";*/
	const GCSE_API_KEY = "AIzaSyCNPrfPF28EAd2cxl0xkZXwDFEUYinMpRU";
	const GCSE_SEARCH_ENGINE_ID = "000666982851490961312:2tiz08px9oo";
	// Holds the GoogleService for reuse
	private $service;
	// Holds the optParam for our search engine id
	private $optParamSEID;

	function __construct($appName = "wardrobe-wizard"){

		parent::__construct();
		$this->load->library('googleApiClient');
		$this->load->library('colorConvertion');
		
		$gmail_services = [
			[
				"api_key" => "AIzaSyCNPrfPF28EAd2cxl0xkZXwDFEUYinMpRU",
				"engine_id" => "000666982851490961312:2tiz08px9oo",
				"gmail_id" => "ashish.thinkdebug@gmail.com"
			],
			[
				"api_key" => "AIzaSyAzlNcgBZ6UuyxP9URMcxZLhgd4SIezONU",
				"engine_id" => "013933632415335686094:0ra2xj8calo",
				"gmail_id" => "jeetendra.thinkdebug@gmail.com"
			],
			[
				"api_key" => "AIzaSyAcTI2RsZfWqouAzLvwBd9utZNNqdJD3H0",
				"engine_id" => "000926217943617619031:mhslknkhodi",
				"gmail_id" => "devone.thinkdebug@gmail.com"
			],
		];

		$search_count_res = $this->common_model->getData('tbl_search_count', 'row', ['search_date'=>date('Y-m-d')]);
		$key_index = ($search_count_res) ? intval($search_count_res->total_search / 100) : 0;
		$gcse_api_key = $gmail_services[$key_index]['api_key'];
		$gcse_search_engine_id = $gmail_services[$key_index]['engine_id'];

		$client = new Google_Client();

        // application name is an arbitrary name
        $client->setApplicationName($appName);

        // the developer key is the API Key for a specific google project
        $client->setDeveloperKey($gcse_api_key);

        // create new service
        $this->service = new Google_Service_Customsearch($client);

        // You must specify a custom search engine.  You can do this either by setting
        // the element "cx" to the search engine id, or by setting the element "cref"
        // to the public url for that search engine.
        // 
        // For a full list of possible params see https://github.com/google/google-api-php-client-services/blob/master/src/Google/Service/Customsearch/Resource/Cse.php
        $this->optParamSEID = array("cx"=>$gcse_search_engine_id);

        $this->gmail_services = $gmail_services;
        $this->gcse_api_key = $gcse_api_key;
	}

	public function getGSERequestData($post=''){
		$data_set = [];
		$data_set['bottomwear'] = [];
		$data_set['footwear'] = [];
		$data_set['outerwear'] = [];
		$data_set['topwear'] = [];
		$data = [
				'bottomwear' => [ 'Men' => ['Jeans', 'Pants'],  'Women'=> ['Jeans', 'Pants', 'Skirts', 'Shorts']],
				'footwear' => ['Men' => ['Sneakers', 'Shoes'], 'Women'=>['Sneakers', 'Shoes','Heels', 'Sandals', 'Boots']],
				'outerwear' => ['Coats', 'Jackets', 'Sweaters'],
				'topwear' => ['Men' => ['Shirts', 'T-Shirts'], 'Women'=>['Shirts', 'T-Shirts','Dress', 'Onesies']],
				'colorcode' => 'FF0000',
				'user_id' => 2,
			];
		$user_id = $data['user_id'];
		$color_name = '';
		$color_name = $this->getColorCodeName($data);
		$user_res = $this->common_model->getData('tbl_users', 'row', ['id'=>$user_id]);
		$user_search = $this->common_model->getData('tbl_search_user_2', 'row', ['user_id'=>$user_id], 'search_id DESC', 1);

		$user_gender = ($user_res->gender == 'male') ? 'Men' : 'Women';
	
		foreach($data['bottomwear'][$user_gender] as $bval){
			$bottom_wear_query = $user_search->nearest_color_name.' '.$bval.' For '.$user_gender;
			$bottom_wear_pera = [
				'q'=>$bottom_wear_query,
				'exactTerms'=> $user_gender,
				'num'=>'4',
				'start'=>'1',
				// 'filter'=>'1',
				// 'gl'=>'us',
				// 'imgSize'=>'MEDIUM',
				// 'safe'=>'active'
			];
			$bottom_wear_results = $this->getSearchResults($bottom_wear_pera);
			if(!empty($bottom_wear_results)){
				$temp_arr = [];
				foreach($bottom_wear_results as $value){
					$row = array();
					$row['title']	= isset($value['title']) ? $value['title'] : '';
					$row['url']		= isset($value['link']) ? $value['link'] : '';
					$row['description']	= isset($value['snippet']) ? $value['snippet'] : '';
					$row['cse_image']			= isset($value['pagemap']['cse_image']) ? $value['pagemap']['cse_image'][0]['src'] : '';
					$temp_arr[] = $row;
				}
				$data_set['bottomwear'] = array_merge($data_set['bottomwear'], $temp_arr);
			}else{
				$data_set['bottomwear'] = [];
			}
		}
		
		/* Footwaer Start */
		foreach($data['footwear'][$user_gender] as $fval){
			$footwear_query = $fval.' For '.$user_gender;
			// echo $footwear_query;die;
			$footwear_pera = [
				'q'=>$footwear_query,
				'exactTerms'=> $user_gender,
				'num'=>'4',
				'start'=>'1',
				// 'filter'=>'1',
				// 'gl'=>'us',
				// 'imgSize'=>'MEDIUM',
				// 'safe'=>'active'
			];
			$footwear_results = $this->getSearchResults($footwear_pera);
			if(!empty($footwear_results)){
				$temp_arr = [];
				foreach($footwear_results as $f_value){
					$row = array();
					$row['title']	= isset($f_value['title']) ? $f_value['title'] : '';
					$row['url']		= isset($f_value['link']) ? $f_value['link'] : '';
					$row['description']	= isset($f_value['snippet']) ? $f_value['snippet'] : '';
					$row['cse_image']			= isset($f_value['pagemap']['cse_image']) ? $f_value['pagemap']['cse_image'][0]['src'] : '';
					$temp_arr[] = $row;
				}
				$data_set['footwear'] = array_merge($data_set['footwear'], $temp_arr);
			}else{
				$data_set['footwear'] = [];
			}
		}

		/* outerwear Start */
		foreach($data['outerwear'] as $oval){
			$outerwear_query = $oval.' For '.$user_gender;
			$outerwear_pera = [
				'q'=>$outerwear_query,
				'exactTerms'=> $user_gender,
				'num'=>'4',
				'start'=>'1',
				// 'filter'=>'1',
				// 'gl'=>'us',
				// 'imgSize'=>'MEDIUM',
				// 'safe'=>'active'
			];
			$outerwear_results = $this->getSearchResults($outerwear_pera);
			// echo "<pre>";print_r($outerwear_results);die;
			if(!empty($outerwear_results)){
				$temp_arr = [];
				foreach($outerwear_results as $o_value){
					$row = array();
					$row['title']	= isset($o_value['title']) ? $o_value['title'] : '';
					$row['url']		= isset($o_value['link']) ? $o_value['link'] : '';
					$row['description']	= isset($o_value['snippet']) ? $o_value['snippet'] : '';
					$row['cse_image']	= isset($o_value['pagemap']['cse_image']) ? $o_value['pagemap']['cse_image'][0]['src'] : '';
					$temp_arr[] = $row;
				}
				$data_set['outerwear'] = array_merge($data_set['outerwear'], $temp_arr);
			}else{
				$data_set['outerwear'] = [];
			}
		}
		/* topwear Start */
		foreach($data['topwear'][$user_gender] as $tval){

			$topwear_query = $tval.' For '.$user_gender;

			$topwear_pera = [
				'q'=>$topwear_query,
				'exactTerms'=> $user_gender,
				'num'=>'4',
				'start'=>'1',
				// 'filter'=>'1',
				// 'gl'=>'us',
				// 'imgSize'=>'MEDIUM',
				// 'safe'=>'active'
			];
			$topwear_results = $this->getSearchResults($topwear_pera);
			// echo "<pre>";print_r($topwear_results);die;
			if(!empty($topwear_results)){
				$temp_arr = [];
				foreach($topwear_results as $t_value){
					$row = array();
					$row['title']	= isset($t_value['title']) ? $t_value['title'] : '';
					$row['url']		= isset($t_value['link']) ? $t_value['link'] : '';
					$row['description']	= isset($t_value['snippet']) ? $t_value['snippet'] : '';
					$row['cse_image']			= isset($t_value['pagemap']['cse_image']) ? $t_value['pagemap']['cse_image'][0]['src'] : '';
					$temp_arr[] = $row;
				}
				$data_set['topwear'] = array_merge($data_set['topwear'], $temp_arr);
			}else{
				$data_set['topwear'] = [];
			}
		}

		// echo "<pre>";print_r($data_set);die;		
		echo json_encode($data_set);die;
	}

	/**
     * A simplistic function to take a search term & search options and return an 
     * array of results.  You may want to 
     * 
     * @param string    $searchTerm     The term you want to search for
     * @param array     $optParams      See: For a full list of possible params see https://github.com/google/google-api-php-client-services/blob/master/src/Google/Service/Customsearch/Resource/Cse.php
     * @return array                                An array of search result items
     */
	private function getSearchResults($optParams = array()){
        // return array containing search result items
        $items = array();
        // Merge our search engine id into the $optParams
        // If $optParams already specified a 'cx' element, it will replace our default
        $optParams = array_merge($this->optParamSEID, $optParams);
        // set search term & params and execute the query
        $results = $this->service->cse->listCse($optParams);

        // Since cse inherits from Google_Collections (which implements Iterator)
        // we can loop through the results by using `getItems()`
        foreach($results->getItems() as $k=>$item){
            // var_dump($item);
            $items[] = $item;
        }

        return $items;
	}

	private function getColorCodeName($data = ''){
		$res = $this->colorconvertion->name($_POST['colorcode']);
		return $res[1];
	}

	public function indivisualUsertSet(){
             
		$api_key = $this->gmail_services[0]['api_key'];
		
		$data_set['topwear'] = [];
		$user_id = $this->input->post('user_id');
		//echo $user_id;exit;
		$colorcode = $this->input->post('colorcode');
		//echo $colorcode;exit;
		$tokenKey_post = $this->input->post('token_key');
		$user_wear_arr = explode(',', $this->input->post('user_wear'));
		$user_res = $this->common_model->getData('tbl_users', 'row', ['id'=>$user_id]);
		if(!empty($user_res)){
			if($user_res->token_key == $tokenKey_post){
				$color_name = $this->getColorCodeName($colorcode);
				$user_gender = ($user_res->gender == 'MALE') ? 'Men' : 'Women';
				
				/*$search_count_res = $this->common_model->getData('tbl_search_count', 'row', ['user_id'=>$user_id, 'search_date'=>date('Y-m-d'), 'api_key'=>$api_key] );
				// $total_search = ($search_count_res) ? $search_count_res->total_search : 0;
				if(!empty($search_count_res)){
					$total_search = $search_count_res->total_search;
				}else{
					$search_count_data = ['user_id'=>$user_id, 'total_search'=>0, 'search_date'=>date('Y-m-d'), 'api_key'=>$api_key, 'created_at'=>date('Y-m-d')];
					$this->common_model->addData('tbl_search_count', $search_count_data );
					$total_search = 0;
				}*/
				$total_search = 0;
				foreach($user_wear_arr as $bval)
				{
					$total_search = $total_search + 1;
					$topwear_query = $color_name.' '.$bval.' For '.$user_gender;
					$exact_query = $bval.' For '.$user_gender;

					$topwear_pera = [
						'hq'=>$topwear_query,
						'exactTerms'=> $exact_query,
						'num'=>'4',
						'start'=>'1',
						'filter'=>'1',
						'gl'=>'us',
						'imgSize'=>'MEDIUM',
						'safe'=>'active'
					];
					
					$topwear_results = $this->getSearchResults($topwear_pera);

					
					if(!empty($topwear_results)){
						$temp_arr = [];
						
						foreach($topwear_results as $t_value){
							$row = array();
							$row['title']	= isset($t_value['title']) ? $t_value['title'] : '';
							$row['url']		= isset($t_value['link']) ? $t_value['link'] : '';
							$row['description']	= isset($t_value['snippet']) ? $t_value['snippet'] : '';
							$row['cse_image']	= isset($t_value['pagemap']['cse_image']) ? $t_value['pagemap']['cse_image'][0]['src'] : '';
							$temp_arr[] = $row;
						}
							
						$searched_data = [];
						$searched_data['user_id'] = $user_id;
						$searched_data['search_query'] = $topwear_query;
						$searched_data['color_code'] = $colorcode;
						$searched_data['nearest_color_name'] = $color_name;
						$searched_data['created_at'] = date('Y-m-d H:i;s');
						$searched_data['updated_at'] = date('Y-m-d H:i;s');
						$this->common_model->addData('tbl_search_user_'.$user_id, $searched_data);
						$data_set['topwear'] = array_merge($data_set['topwear'], $temp_arr);

					}else{
						$data_set['topwear'] = [];
					}

				}
				$search_count_data = ['user_id'=>$user_id, 'total_search'=>$total_search, 'search_date'=>date('Y-m-d'), 'api_key'=>$this->gcse_api_key, 'created_at'=>date('Y-m-d'), 'updated_at'=>date('Y-m-d')];
			
				$this->common_model->addOrUpdateSearchCountByDate($search_count_data);

				if(!empty($data_set['topwear'])){
					echo json_encode(['status'=>'true', 'data'=>$data_set['topwear'] ] );
				}else{
					echo json_encode(['status'=>'false', 'data'=>'', 'message'=>'empty data set']);
				}
			}else{
				echo json_encode(['status'=>'false', 'data'=>'', 'message'=>'invalid token key']);
			}
		}else{
			echo json_encode(['status'=>'false', 'data'=>'', 'message'=>'invalid user id']);
		}
	}

	public function userLogin(){
		try{
			$post = $this->input->post();
			
		//	print_r($post); die(); 
			if(!empty($post['email']) && !empty($post['password'])){                        
				$email = $post['email'];
				$password = md5($post['password']);
				$user_res = $this->common_model->getData('tbl_users', 'row', ['user_email'=>$email, 'password'=>$password]);
				if(!empty($user_res)){
					$token_key = bin2hex($this->encryption->create_key(16));
					$this->common_model->updateData('tbl_users', ['id'=>$user_res->id], ['token_key'=>$token_key]);
					echo json_encode( [ 'status'=>'true','msg'=>'Login successfully !', 'data'=>array_merge( (array)$user_res, ['token_key'=>$token_key] ) ] );
				}else{
					echo json_encode(['status'=>'false','msg'=>'Either email or password is wrong.']);
				}
			}else{
				echo json_encode(['status'=>'false','msg'=>'Email and password fields are required.']);
			}
		}catch(Exception $e){
			echo json_encode(['status'=>'false', 'error'=>"Error : ". $e->getMessage()]);
		}
	}
	
	

	public function userRegistration(){
		$form_errors = '';
		$user_rules = [
			'add' => [
				[
					'field' => 'user_name',
					'label' => 'User Name',
					'rules' => 'required',
					'errors' => [
	                    'required' => 'User Name field is required.',
		            ],
				],
				[
					'field' => 'user_email',
					'label' => 'User Email',
					'rules' => 'required|valid_email|is_unique[tbl_users.user_email] ',
					'errors' => [
	                    'required' => 'Email field is required.',
	                    'valid_email' => 'Email should be valid.',
	                    'is_unique' => 'Email already exist',
		            ],
				],
				[
					'field' => 'user_mobile',
					'label' => 'User Mobile',
					'rules' => 'required|numeric|is_unique[tbl_users.user_mobile]|min_length[10]|max_length[10] ',
					'errors' => [
	                    'required' => 'User Mobile field is required.',
	                    'min_length' => 'Mobile minimun length is 10 characters',
	                    'is_unique' => 'Mobile number already exist',
		            ],
				],
				[
					'field' => 'password',
					'label' => 'Password',
					'rules' => 'required',
				],
				// [
				// 	'field' => 'c_password',
				// 	'label' => 'Confirm Password',
				// 	'rules' => 'required|matches[password] ',
				// ],
			]
		];
		$post = $this->input->post();
		$this->form_validation->set_data($post);
		// $this->form_validation->reset_validation();
		$this->form_validation->set_rules($user_rules['add']);
		
		if($this->form_validation->run() == TRUE){
			$token_key = bin2hex($this->encryption->create_key(16));
			$form_data['user_name']		= $post['user_name'];
			$form_data['user_email']	= $post['user_email'];
			$form_data['user_mobile']	= $post['user_mobile'];
			$form_data['password']		= md5($post['password']);
			$form_data['gender']		= $post['gender'];
			$form_data['token_key']		= $token_key;
			$form_data['user_status'] 	= '1';
			$form_data['user_type'] 	= 'user';
			$form_data['created_at'] 	= date('Y-m-d H:i:s');
			$form_data['updated_at'] 	= date('Y-m-d H:i:s');
			$user_id = $this->common_model->addData('tbl_users', $form_data);
			if($user_id){
				if($this->common_model->createSearchUserTable($user_id)){
					
					$search_tbl_data = [
						'user_id'=>$user_id, 
						'model_type'=>'search', 
						'table_name'=>'tbl_search_user_'.$user_id,
						'created_at'=>date('Y-m-d H:i:s'), 
						'updated_at'=>date('Y-m-d H:i:s'), 
					];
					$this->common_model->addData('tbl_user_search_tables', $search_tbl_data);

					$user_res = $this->common_model->getData('tbl_users', 'row', ['id'=>$user_id] );
					if(!empty($user_res)){
						echo json_encode(['status'=>'success',  'data'=>$user_res, 'message'=>'User has been register successfully!']);
					}else{
						echo json_encode(['status'=>'success',  'data'=>'', 'message'=>'User has been register successfully!']);
					}
				}else{
					echo json_encode(['status'=>'false', 'message'=>'create user search table failed. ']);
				}
			}else{
				echo json_encode(['status'=>'false', 'message'=>'user registration failed. ']);
			}
		}else{
			$form_errors = $this->form_validation->error_array();
			echo json_encode(['status'=>'false', 'data'=>$form_errors]);
		}
	}

	public function addUserMeasurement(){
		$post = $this->input->post();
		if(!empty($post)){
			$user_id = $post['user_id'];
			$tokenKey_post = $post['token_key'];
			$user_res = $this->common_model->getData('tbl_users', 'row', ['id'=>$user_id]);
			if(!empty($user_res))
			{
				if($user_res->token_key == $tokenKey_post)
				{
					if($user_res->gender == 'MALE'){
						$form_data['user_id'] = $user_id;
						$form_data['collar_type']	= $post['collar_type'];
						$form_data['chest'] 		= $post['chest'];
						$form_data['waist'] 		= $post['waist'];
						$form_data['sleeve_length'] = $post['sleeve_length'];
						$form_data['inseam'] 		= $post['inseam'];
						$form_data['shoe_size'] 	= $post['shoe_size'];
						$form_data['created_at']  = date('Y-m-d H:i:s');
						$form_data['updated_at']  = date('Y-m-d H:i:s');
					}else{
						$form_data['user_id'] = $user_id;
						$form_data['bust']		= $post['bust'];
						$form_data['hips'] 		= $post['hips'];
						$form_data['waist'] 	= $post['waist'];
						$form_data['arm_length'] = $post['arm_length'];
						$form_data['shoe_size']  = $post['shoe_size'];
						$form_data['created_at']  = date('Y-m-d H:i:s');
						$form_data['updated_at']  = date('Y-m-d H:i:s');
					}
					$um_id = $this->common_model->addData('tbl_user_measurements', $form_data);
					if(!empty($um_id)){
						echo json_encode(['status'=>'true', 'message'=>'measurement add successfully']);
					}else{
						echo json_encode(['status'=>'false', 'message'=>'add measurement failed.']);
					}
				}else{
					echo json_encode(['status'=>'false', 'message'=>'token key is invalid.']);
				}
			}else{
				echo json_encode(['status'=>'false', 'message'=>'empty user data']);
			}
		}else{
			echo json_encode(['status'=>'false', 'message'=>'empty post data']);
		}
	}

	public function getCategories(){
		$categories = $this->common_model->getData('tbl_category', 'result', []);
		if(!empty($categories)){
			echo json_encode(['status'=>'true', 'data'=>$categories]);
		}else{
			echo json_encode(['status'=>'false', 'data'=>'']);
		}
	}

	public function addProduct(){
		$post = $this->input->post();
		if(!empty($post)){
			$user_id = $post['user_id'];
			$tokenKey_post = $post['token_key'];
			$user_res = $this->common_model->getData('tbl_users', 'row', ['id'=>$user_id]);
			if(!empty($user_res)){
				if($user_res->token_key == $tokenKey_post){
					$form_data['user_id'] = $user_id;
					$form_data['product_name'] 	= $post['product_name'];
					$form_data['category_id'] 	= $post['category_id'];
					$form_data['decription'] 	= $post['decription'];
					
					if($_FILES["product_img"]["name"]) {
					   	$product_img = 'product_img';
					   	$fieldName = "product_img";
					   	$Path = 'assets/upload/products';
					   	$product_img_data = $this->ImageUpload($_FILES["product_img"]["name"], $product_img, $Path, $fieldName);
						// echo "<pre>";print_r($product_img_data);die;
					   	if(isset($product_img_data['error']) && !empty($product_img_data['error']) ){
					   		json_encode(['status'=>'false', 'error'=>$product_img_data['error']]);
					   		exit();
					   	}
					}

					$form_data['product_img'] 	= $Path.'/'.$product_img_data['file_name'];
					// $post['sp_main_img'] = $Path.'/'.$sp_main_img;
					$form_data['product_status'] = 1;
					$form_data['created_at'] 	= date('Y-m-d H:i:s');
					$form_data['updated_at'] 	= date('Y-m-d H:i:s');
					// echo "<pre>";print_r($form_data);die;
					$product_id = $this->common_model->addData('tbl_user_products', $form_data);
					if($product_id != ''){
						echo json_encode(['status'=>'true', 'product_id'=>$product_id]);
						exit();
					}
				}else{
					echo json_encode(['status'=>'false', 'message'=>'token key is invalid.']);
				}
			}else{
				echo json_encode(['status'=>'false', 'message'=>'empty user data']);
			}
		}else{
			echo json_encode(['status'=>'false', 'message'=>'empty post data']);
		}
	}

	public function getUserProductsList(){
		$data = [];
		$post = $this->input->post();
	
	//	print_r($post); die();
		
			//$user_id = $post['user_id'];
			$user_id = $this->input->post('user_id');
			$tokenKey_post =  $this->input->post('token_key');
		
		
		if(!empty($user_id) && !empty($tokenKey_post)){
			
			$category_id =  $this->input->post('category_id');
			 $whr =  (empty($category_id))? ['user_id'=>$user_id] : ['user_id'=>$user_id, 'category_id'=>$category_id];
			
			$user_res = $this->common_model->getData('tbl_users', 'row', ['id'=>$user_id]);
		
			if(!empty($user_res)){
				if($user_res->token_key == $tokenKey_post){
				// 	$categories = $this->common_model->getData('tbl_category', 'result', []);
				// 	if(!empty($categories)){
				// 		foreach($categories as $category){
						   // print_r($category);exit;
							$data = $this->common_model->getData('tbl_user_products', 'result',$whr );
					//	}
						echo json_encode(['status'=>'true','base_url'=>base_url(), 'data'=>$data]);
				/*	}else{
						echo json_encode(['status'=>'false', 'message'=>'empty categories']);
					}*/
				}else{
					echo json_encode(['status'=>'false', 'message'=>'token key is invalid.']);
				}
			}else{
				echo json_encode(['status'=>'false', 'message'=>'empty user data']);
			}
		}else{
			echo json_encode(['status'=>'false', 'message'=>'empty post data']);
		}
	}

	public function getBestWardrobeMatch(){
		$flag = '';
		$data_set = [];
		$data_set['bottomwear'] = [];
		$data_set['footwear'] = [];
		$data_set['outerwear'] = [];
		$data_set['topwear'] = [];
		$dataSets = [
			'bottomwear' => [ 'Men' => ['Jeans', 'Pants'],  'Women'=> ['Jeans', 'Pants', 'Skirts', 'Shorts']],
			'footwear' => ['Men' => ['Sneakers', 'Shoes'], 'Women'=>['Sneakers', 'Shoes','Heels', 'Sandals', 'Boots']],
			'outerwear' => ['Coats', 'Jackets', 'Sweaters'],
			'topwear' => ['Men' => ['Shirts', 'T-Shirts'], 'Women'=>['Shirts', 'T-Shirts','Dress', 'Onesies']]
		];

		$user_id = $this->input->post('user_id');
		$colorcode = $this->input->post('colorcode');
		$tokenKey_post = $this->input->post('token_key');
		$user_res = $this->common_model->getData('tbl_users', 'row', ['id'=>$user_id]);

		if(!empty($user_res)){
			if($user_res->token_key == $tokenKey_post){
				$color_name = $this->getColorCodeName($colorcode);
				$user_gender = ($user_res->gender == 'male') ? 'Men' : 'Women';
		
			$total_search = 0;
			/* BottomWear */
			foreach($dataSets['bottomwear'][$user_gender] as $bval)
			{
				$total_search = $total_search + 1;
				$dynamic_search_query = $color_name.' '.$bval.' For '.$user_gender;
				$exact_query = $bval.' For '.$user_gender;

				$topwear_pera = [
					'hq'=>$dynamic_search_query,
					'exactTerms'=> $exact_query,
					'num'=>'4',
					'start'=>'1',
					'filter'=>'1',
					'gl'=>'us',
					'imgSize'=>'MEDIUM',
					'safe'=>'active'
				];
				
				$search_results = $this->getSearchResults($topwear_pera);
				
				if(!empty($search_results)){
					$temp_arr = [];
					
					foreach($search_results as $t_value){
						$row = array();
						$row['title']	= isset($t_value['title']) ? $t_value['title'] : '';
						$row['url']		= isset($t_value['link']) ? $t_value['link'] : '';
						$row['description']	= isset($t_value['snippet']) ? $t_value['snippet'] : '';
						$row['cse_image']	= isset($t_value['pagemap']['cse_image']) ? $t_value['pagemap']['cse_image'][0]['src'] : '';
						$temp_arr[] = $row;
					}
						
					$searched_data = [];
					$searched_data['user_id'] = $user_id;
					$searched_data['search_query'] = $dynamic_search_query;
					$searched_data['color_code'] = $colorcode;
					$searched_data['nearest_color_name'] = $color_name;
					$searched_data['created_at'] = date('Y-m-d H:i;s');
					$searched_data['updated_at'] = date('Y-m-d H:i;s');
					$this->common_model->addData('tbl_search_user_'.$user_id, $searched_data);
					$data_set['bottomwear'] = array_merge($data_set['bottomwear'], $temp_arr);
				}else{
					$data_set['bottomwear'] = [];
				}

			}
			/* Footwears */
			foreach($dataSets['footwear'][$user_gender] as $bval)
			{
				$total_search = $total_search + 1;
				$dynamic_search_query = $color_name.' '.$bval.' For '.$user_gender;
				$exact_query = $bval.' For '.$user_gender;

				$topwear_pera = [
					'hq'=>$dynamic_search_query,
					'exactTerms'=> $exact_query,
					'num'=>'4',
					'start'=>'1',
					'filter'=>'1',
					'gl'=>'us',
					'imgSize'=>'MEDIUM',
					'safe'=>'active'
				];
				
				$search_results = $this->getSearchResults($topwear_pera);
				
				if(!empty($search_results)){
					$temp_arr = [];
					
					foreach($search_results as $t_value){
						$row = array();
						$row['title']	= isset($t_value['title']) ? $t_value['title'] : '';
						$row['url']		= isset($t_value['link']) ? $t_value['link'] : '';
						$row['description']	= isset($t_value['snippet']) ? $t_value['snippet'] : '';
						$row['cse_image']	= isset($t_value['pagemap']['cse_image']) ? $t_value['pagemap']['cse_image'][0]['src'] : '';
						$temp_arr[] = $row;
					}
						
					$searched_data = [];
					$searched_data['user_id'] = $user_id;
					$searched_data['search_query'] = $dynamic_search_query;
					$searched_data['color_code'] = $colorcode;
					$searched_data['nearest_color_name'] = $color_name;
					$searched_data['created_at'] = date('Y-m-d H:i;s');
					$searched_data['updated_at'] = date('Y-m-d H:i;s');
					$this->common_model->addData('tbl_search_user_'.$user_id, $searched_data);
					$data_set['footwear'] = array_merge($data_set['footwear'], $temp_arr);
				}else{
					$data_set['footwear'] = [];
				}
			}
			/* TopWear */
			foreach($dataSets['topwear'][$user_gender] as $bval)
			{
				$total_search = $total_search + 1;
				$dynamic_search_query = $color_name.' '.$bval.' For '.$user_gender;
				$exact_query = $bval.' For '.$user_gender;

				$topwear_pera = [
					'hq'=>$dynamic_search_query,
					'exactTerms'=> $exact_query,
					'num'=>'4',
					'start'=>'1',
					'filter'=>'1',
					'gl'=>'us',
					'imgSize'=>'MEDIUM',
					'safe'=>'active'
				];
				
				$search_results = $this->getSearchResults($topwear_pera);
				
				if(!empty($search_results)){
					$temp_arr = [];
					
					foreach($search_results as $t_value){
						$row = array();
						$row['title']	= isset($t_value['title']) ? $t_value['title'] : '';
						$row['url']		= isset($t_value['link']) ? $t_value['link'] : '';
						$row['description']	= isset($t_value['snippet']) ? $t_value['snippet'] : '';
						$row['cse_image']	= isset($t_value['pagemap']['cse_image']) ? $t_value['pagemap']['cse_image'][0]['src'] : '';
						$temp_arr[] = $row;
					}
						
					$searched_data = [];
					$searched_data['user_id'] = $user_id;
					$searched_data['search_query'] = $dynamic_search_query;
					$searched_data['color_code'] = $colorcode;
					$searched_data['nearest_color_name'] = $color_name;
					$searched_data['created_at'] = date('Y-m-d H:i;s');
					$searched_data['updated_at'] = date('Y-m-d H:i;s');
					$this->common_model->addData('tbl_search_user_'.$user_id, $searched_data);
					$data_set['topwear'] = array_merge($data_set['topwear'], $temp_arr);
				}else{
					$data_set['topwear'] = [];
				}
			}
			/* OuterWear */
			foreach($dataSets['outerwear'] as $bval)
			{
				$total_search = $total_search + 1;
				$dynamic_search_query = $color_name.' '.$bval.' For '.$user_gender;
				$exact_query = $bval.' For '.$user_gender;

				$topwear_pera = [
					'hq'=>$dynamic_search_query,
					'exactTerms'=> $exact_query,
					'num'=>'4',
					'start'=>'1',
					'filter'=>'1',
					'gl'=>'us',
					'imgSize'=>'MEDIUM',
					'safe'=>'active'
				];
				
				$search_results = $this->getSearchResults($topwear_pera);
				
				if(!empty($search_results)){
					$temp_arr = [];
					
					foreach($search_results as $t_value){
						$row = array();
						$row['title']	= isset($t_value['title']) ? $t_value['title'] : '';
						$row['url']		= isset($t_value['link']) ? $t_value['link'] : '';
						$row['description']	= isset($t_value['snippet']) ? $t_value['snippet'] : '';
						$row['cse_image']	= isset($t_value['pagemap']['cse_image']) ? $t_value['pagemap']['cse_image'][0]['src'] : '';
						$temp_arr[] = $row;
					}
						
					$searched_data = [];
					$searched_data['user_id'] = $user_id;
					$searched_data['search_query'] = $dynamic_search_query;
					$searched_data['color_code'] = $colorcode;
					$searched_data['nearest_color_name'] = $color_name;
					$searched_data['created_at'] = date('Y-m-d H:i;s');
					$searched_data['updated_at'] = date('Y-m-d H:i;s');
					$this->common_model->addData('tbl_search_user_'.$user_id, $searched_data);
					$data_set['outerwear'] = array_merge($data_set['outerwear'], $temp_arr);
				}else{
					$data_set['outerwear'] = [];
				}
			}

				$search_count_data = ['user_id'=>$user_id, 'total_search'=>$total_search, 'search_date'=>date('Y-m-d'), 'api_key'=>$this->gcse_api_key, 'created_at'=>date('Y-m-d'), 'updated_at'=>date('Y-m-d')];
				// $this->common_model->addData('tbl_search_count', $search_count_data );
				// $this->common_model->updateData('tbl_search_count', ['user_id'=>$user_id, 'search_date'=>date('Y-m-d')], ['total_search'=>$total_search, 'updated_at'=>date('Y-m-d')]);
				$this->common_model->addOrUpdateSearchCountByDate($search_count_data);

				if(!empty($data_set)){
					echo json_encode(['status'=>1, 'data'=>$data_set ] );
				}else{
					echo json_encode(['status'=>0, 'data'=>'', 'message'=>'empty data set']);
				}
			}else{
				echo json_encode(['status'=>0, 'data'=>'', 'message'=>'invalid token key']);
			}
		}else{
			echo json_encode(['status'=>0, 'data'=>'', 'message'=>'invalid user id']);
		}
	}

	public function isTokenKeyValid($post){
		if(!empty($post)){
			$user_id = $post['user_id'];
			$tokenKey_post = $post['token_key'];
			$user_res = $this->common_model->getData('tbl_users', 'row', ['id'=>$user_id]);
			if(!empty($user_res)){
				if($user_res->token_key == $tokenKey_post){
					return true;
				}else{
					echo json_encode(['status'=>'false', 'message'=>'token key is invalid.']);
					exit();
				}
			}else{
				echo json_encode(['status'=>'false', 'message'=>'empty user data']);
				exit();
			}
		}else{
			echo json_encode(['status'=>'false', 'message'=>'empty post data']);
			exit();
		}
	}

	public function test_email(){
		$email = "ashish.thinkdebug@gmail.com";
		$subject = "Test message";
		$message = $this->load->view('mail/verify_mail', $mail_data=['user_otp'=>'123456'], true);
		// $message = "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.";
		$res = $this->send_mail($email, $subject, $message);
		echo "<pre>";print_r($res);
	}
}
?>