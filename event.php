<?php
        
class Event extends CI_Controller {
   
    public function __construct() {
       parent::__construct();
      $this->load->model('Common_model');
    }
       function index1()
       {
         $flag=$this->input->post('flag');
         switch ($flag) {
             case "add_event":
               
         

                   $title=$this->input->post('title');
                   $description = $this->input->post('description');
                   $fees = $this->input->post('fees');
                   $classes = $this->input->post('room');
                   $type = $this->input->post('type');
                   $date = $this->input->post('date');
                    $file_name = $this->Common_model->file_upload('image', 'upload/');  
                   $ftime = $this->input->post('ftime');
                   $totime = $this->input->post('totime');
                   $branch_id = $this->input->post('branch_id');
                     $fielddata=$this->Common_model->save($title,$description,$fees,$classes,$type,$date,$file_name,$ftime,$totime,$branch_id);


       if (empty($title)||empty($description)||empty($fees)||empty($classes)||empty($type)||empty($date)||empty($ftime)||empty($totime)||empty($branch_id)) {
             $senddata=["status"=>false,"msg"=>"all fields are required"];
             echo json_encode($senddata);
       }else{

        if ($fielddata){
           $senddata=["status"=>true,"msg"=>"data added successfully","body"=>$fielddata];
        }else{
            $senddata=["status"=>false,"msg"=>"some error found","body"=>''];
        }      echo json_encode($senddata);
       }    break;

    
    case "view_event":
    
        $id=$this->input->post('id');
       $date= $this->Common_model->show($id);
         if ($date) {
             $senddate=["status"=>true,"msg"=>"success","body"=>$date];
         }
         else
         {
            $senddate=["status"=>false,"msg"=>"no data","body"=>''];
         }
         echo json_encode($senddate);
       break;

       case "update_event":
         
        $id=$this->input->post('id');
        $title=$this->input->post('title');
        $description=$this->input->post('description');
        $fees=$this->input->post('fees');
        $classes=$this->input->post('room');
        $type=$this->input->post('type');
         $date = $this->input->post('date');
         $file_name=$this->Common_model->file_upload('image','upload/');
        $ftime=$this->input->post('ftime');
        $totime=$this->input->post('totime');
        $branch_id=$this->input->post('branch_id');
        $data=array('title'=>$title,'description'=>$description,'fees'=>$fees,'room'=>$classes,'type'=>$type,'date'=>$date,'image'=>$file_name,'ftime'=>$ftime,'totime'=>$totime,'branch_id'=>$branch_id);

            $id_data=$this->Common_model->edit($id,$data);

  if (empty($id)||empty($title)||empty($description)||empty($fees)||empty($classes)||empty($type)||empty($date)||empty($ftime)||empty($totime)||empty($branch_id)) {
             $updatedata=["status"=> false,"msg"=> "all fields are required","body"=>''];
             echo  json_encode($updatedata);
  }else{
         if ($id_data) {
              $updatedata=["status"=> true,"msg"=> "this column has successfuly updated","body"=>"$id_data"];
         }else{
             $updatedata=["status"=> false,"msg"=> "few errors","body"=>"$id_data"];
         }
         echo json_encode($updatedata);

       }
        break;
     
       case "update_event":
         
       
       $id= $this->input->post('id');

          $data=$this->Common_model->delete_data($id);
          if (empty($id)) {
                 
            $deletedate=["status"=>false,"msg"=>"assign your id first","body"=>''];

               echo json_encode($deletedate);
          }else{
                if($data)
                 {
                   $deletedate=["status"=>true,"msg"=>" deleted","body"=>"$data"];
                   
                 }else{
                     $deletedate=["status"=>false,"msg"=>"some errors ","body"=>''];
                 }
                 echo json_encode($deletedate);
          }
       break;
        default:
                     $dataTosend = ['status'=>false,'msg'=>"Invalid Requst",'body'=>''];
                        echo json_encode($dataTosend); die(); 
                }

     function add_inq()
     {

      $name=$this->input->post('name');
      $s_name=$this->input->post('school_name');
      $email=$this->input->post('email');
      $mobile=$this->input->post('mobile');
      $file_name=$this->Common_model->file_upload('image','upload/');
      $description=$this->input->post('description');
      $created=$this->input->post('created');
      $updated=$this->input->post('updated');

      $data=$this->Common_model->insert_inq($name,$s_name,$email,$mobile,$file_name,$description,$created,$updated);
               
                if (empty($name)||empty($s_name)||empty($email)||empty($mobile)||empty($file_name)||empty($description)||empty($created)||empty($updated)) {
                    $sendtodata=["status"=>false,"msg"=>"all field required","body"=>''];
                    echo   json_encode($sendtodata);
                }
                else
                {
                    if ($data) {
                        $sendtodata=["status"=>true,"msg"=>"your data inserted successfully","body"=>"$data"];
                    }else{
                        $sendtodata=["status"=>false,"msg"=>"data not inserted","body"=>''];
                    }
                      echo json_encode($sendtodata);
                }
     }   

     function view_inq()
     {

        $id=$this->input->post('id');
        $name=$this->input->post('name');
       $data=$this->Common_model->show_inq($id,$name);
       if (empty($id)) {
         $sendtodata=["status"=>false,"msg"=>"atleast one field required","body"=>''];
         echo json_encode($sendtodata);
       }
       else
       {
        if ($data) {
            $sendtodata=["status"=>true,"msg"=>"watch your data carefully","body"=>$data];
        }
        else{
            $sendtodata=["status"=>false,"msg"=>"there is any issue","body"=>''];
        }
        echo json_encode($sendtodata);
       }
     }   

      function edit_inq()
      {
      $id=$this->input->post('id');  
      $name=$this->input->post('name');
      $s_name=$this->input->post('school_name');
      $email=$this->input->post('email');
      $mobile=$this->input->post('mobile');
      $file_name=$this->Common_model->file_upload('image','upload/');
      $description=$this->input->post('description');
      $created=$this->input->post('created');
      $updated=$this->input->post('updated');
     
      $data=array('name'=>$name,'school_name'=>$s_name,'email'=>$email,'contact_no'=>$mobile,'image'=>$file_name,'description'=>$description,'created_at'=>$created,'updated_at'=>$updated);
      $fielddata=$this->Common_model->update_inq($id,$data);

         if (empty($id)||empty($name)||empty($s_name)||empty($email)||empty($mobile)||empty($file_name)||empty($description)||empty($created)||empty($updated)) 
         {
             $sendtodata=["status"=>false,"msg"=>"all fields are required","body"=>''];
             echo json_encode($sendtodata);
         }
         else
         {
            if ($fielddata)
             {
                $sendtodata=["status"=>true,"msg"=>"data updated successfully","body"=>"$fielddata"];
            }
            else
            {
                $sendtodata=["status"=>false,"msg"=>"there is any issue","body"=>''];
            }
            echo json_encode($sendtodata);
         }
      }
        function terminate_inq()
        {
            $id=$this->input->post('id');
            $data=$this->Common_model->delete_inq($id);
               if(empty($id))
               {
                $sendtodata=["status"=>false,"msg"=>"all fields are required","body"=>''];
                echo json_encode($sendtodata);
               }
               else
               {
                if ($data) {
                    $sendtodata=["status"=>true,"msg"=>"your data has truncet","body"=>"$data"];
                }
                else{
                    $sendtodata=["status"=>false,"msg"=>"any issue","body"=>''];
                }
                echo json_encode($sendtodata);
               }
        }
     

    }


    function orgapi()
    {
        $flag=$this->input->post('flag');
        switch ($flag) {
            case  "add_org":
               $cat_id = $this->input->post('cat_id');
             $org_name = $this->input->post('org_name');
             $mobile = $this->input->post('mobile');
             $image=$this->Common_model->file_upload('image','upload/');
             $school_capacity = $this->input->post('school_capacity');
             $admin_id = $this->input->post('admin_id');
             $data=$this->Common_model->addorg($cat_id,$org_name,$mobile,$image,$school_capacity,$admin_id);
             if (empty($cat_id)||empty($org_name)||empty($mobile)||empty($image)||empty($school_capacity)||empty($admin_id))
             {
                $sendtodata=["status"=>false,"msg"=>"all fields are required","body"=>""];
                echo json_encode($sendtodata);
             }    
             else
                {
                if ($data) {
                    $sendtodata=["status"=>true,"msg"=>"data inserted","body"=>"$data"];
                }
                else
                {
                    $sendtodata=["status"=>false,"msg"=>"error","body"=>""];
                }
                echo json_encode($sendtodata);
             } 

                break;
                case "view_org";
                 $id=$this->input->post('id');
                   $data=$this->Common_model->vieworg($id);
                   if (empty($id)) {
                       $sendtodata=["status"=>false,"msg"=>"atleast one field is required","body"=>""];
                       echo json_encode($sendtodata);
                   }
                   else
                   {
                    $sendtodata=["status"=>true,"msg"=>"here u are","body"=>$data];
                    echo json_encode($sendtodata);
                   }
                 break;

              case "update_org":
               
             $id =$this->input->post('id');
             $cat_id = $this->input->post('cat_id');
             $org_name = $this->input->post('org_name');
             $mobile = $this->input->post('mobile');
            $image=$this->Common_model->file_upload('image','upload/');
             $school_capacity = $this->input->post('school_capacity');
             $admin_id = $this->input->post('admin_id');
             
              $data=array('cat_id'=>$cat_id,'org_name'=>$org_name,'mobile'=>$mobile,'image'=>$image,'school_capacity'=>$school_capacity,'admin_id'=>$admin_id);
              $value=$this->Common_model->updatorg($id,$data);
              if (empty($id)||empty($cat_id)||empty($org_name)||empty($mobile)||empty($image)||empty($school_capacity)||empty($admin_id)) {
                  $sendtodata=["status"=>false,"msg"=>"all fields are required","body"=>''];
                  echo json_encode($sendtodata);
              }
              else
              {
                if ($value) {
                    $sendtodata=["status"=>true,"msg"=>"updated data","body"=>"$value"];
                }
                else
                {
                    $sendtodata=["status"=>false,"msg"=>"error","body"=>""];
                }
                echo json_encode($sendtodata);
              }
              break;

                case "delete_org":
                   $id=$this->input->post('id');
                   $data=$this->Common_model->deleteorg($id);
                   if (empty($id)) {
                       $sendtodata=["status"=>false,"msg"=>"atleast one field required","body"=>""];
                   }
                   else
                   {
                    if ($data) {
                        $sendtodata=["status"=>true,"msg"=>"row deleted","body"=>$data];
                    }
                    else
                    {
                        $sendtodata=["status"=>false,"msg"=>"error hai","body"=>""];
                    }
                    echo json_encode($sendtodata);
                   }
                    break;

              default:
               $sendtodata=["status"=>false,"msg"=>"error","body"=>""];
               echo  json_encode($sendtodata);
                break;
          }
    }



    function jointb()
    {

    $data['query'] = $this->Common_model->showjoin();
    // print_r($data['query']);
    // die();
    $this->load->view('join', $data);
    }
}
