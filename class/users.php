<?php
session_start();
include_once("connection.php");
include_once("template.php");

Class Users extends Database{

	public $template;
	public function __construct()
	{
		parent::__construct();
		$this->template = new Template();

		if(isset($_POST["action"]) && $_POST["action"] == "register")
			$this->register_user();
		if(isset($_POST["action"]) && $_POST["action"] == "login")
			$this->login_user();
		if(isset($_GET["url"]) && $_GET["url"] == "logout")
			$this->logout();
		if(isset($_POST["action"]) && $_POST["action"] == "search_user")
			$this->search_user();
		if(isset($_POST["action"]) && $_POST["action"] == "update_user")
			$this->update_user();
	}

	public function register_user()
	{
		$error_string = "";
		$query_user = "SELECT users.first_name, users.last_name, users.email
					   FROM users 
					   WHERE users.first_name = '".mysqli_real_escape_string($this->connection, $_POST['first_name'])."'
					   OR users.last_name ='".mysqli_real_escape_string($this->connection, $_POST['last_name'])."' 
					   AND users.email='".mysqli_real_escape_string($this->connection, $_POST['email'])."'";

		$user_in_db = $this->fetch_record($query_user);

		if(count($user_in_db) > 1)
			$errors[] = "This user has already been registered";
		if(empty($_POST['first_name']))
			$errors[] = "Please enter your first name";
		else if(is_numeric($_POST['first_name']))
			$errors[] = "First name cannot contain numbers";
		if(empty($_POST['last_name']))
			$errors[] = "Please enter your last name";
		else if(is_numeric($_POST['last_name']))
			$errors[] = "Last name cannot contain numbers";
		if(empty($_POST["password"]))
			$errors[] = "Password cant be empty";
		if(empty($_POST["confirm_password"]))
			$errors[] = "Confirm password cant be empty";
		if(empty($_POST["email"]))
			$errors[] = "Email cant be empty";
		if($_POST["password"] != $_POST["confirm_password"])
			$errors[] = "Password doesnt match.";

		if(empty($errors))
		{
			$insert_user = "INSERT INTO users(first_name, last_name, email, password, created_at)
					        VALUES('".mysqli_real_escape_string($this->connection,$_POST["first_name"])."',
					         '".mysqli_real_escape_string($this->connection,$_POST["last_name"])."',
					  		 '".mysqli_real_escape_string($this->connection,$_POST["email"])."',
					  		 '".mysqli_real_escape_string($this->connection,$_POST["password"])."',
					  		 NOW())";
			$insert_result = mysqli_query($this->connection, $insert_user);	
			if(count($insert_result) > 0)
			{
				$data["status"] = TRUE;
				$data["message"] = $this->template->success_error_htm(TRUE) ."You've have successfully register" 
								   . $this->template->error_success_after();
			}
			else
			{
				$data["status"] = FALSE;
				$data["message"] = $this->template->success_error_htm(FALSE) ."Something went wrong in database"
								   . $this->template->error_success_after();
			}

		}
		else
		{	
			$data["status"] = FALSE;
			foreach($errors as $error)
			{
				$error_string .= $this->template->success_error_htm(FALSE) ."". $error."". $this->template->error_success_after();
			}
			$data["message"] = $error_string;
		}
		

		echo json_encode($data);
	}

	public function login_user()
	{
		$error_string = "";
		if(empty($_POST["email"]))
			$errors[] = "Put your email first";
		if(empty($_POST["password"]))
			$errors[] = "Put your password first";
		if(empty($errors))
		{

			$query_login = "SELECT * FROM users
							WHERE users.email='".mysqli_real_escape_string($this->connection, $_POST["email"])."' 
							AND users.password='".mysqli_real_escape_string($this->connection, $_POST["password"])."'";
			$query_login_result = $this->fetch_record($query_login);

			if(count($query_login_result) > 0)
			{
				$_SESSION["user_info"] = array(
					 	"user_id" => $query_login_result["id"],
						"first_name" => $query_login_result["first_name"],
						"last_name" => $query_login_result["last_name"],
						"email" => $query_login_result["email"],
						"created_at" => $query_login_result["created_at"],
						"is_logged_in" => TRUE
						);
				$data["status"] = TRUE;
				$data["url_redirect"] = "dashboard.php";
				$data["message"] = $this->template->success_error_htm(TRUE) ."You've successfully logged in"
								   . $this->template->error_success_after();
			}
			else
			{
				$data["status"] = FALSE;
				$data["message"] = $this->template->success_error_htm(FALSE) ."Log in credentials error"
								   . $this->template->error_success_after();
			}
		}
		else
		{
			$data["status"] = FALSE;
			foreach($errors as $error)
			{
				$error_string .=  $this->template->success_error_htm(FALSE) ."". $error."". $this->template->error_success_after();
			}
			$data["message"] = $error_string;
		}

		echo json_encode($data);
	}

	public function get_all_users()
	{
		$query = "SELECT * FROM users";
		return $this->fetch_all($query);
	}

	public function get_other_users($user_id)
	{
		$query = "SELECT * FROM users 
				  WHERE id !=".$user_id;
		return $this->fetch_all($query);
	}

	public function search_user()
	{
		$string_htm = "";
		$query = "SELECT * FROM users WHERE first_name LIKE '%{$_POST["search"]}%'
		 		  OR last_name LIKE '%{$_POST["search"]}%' OR email LIKE '%{$_POST["search"]}%'";
		$users = $this->fetch_all($query);

		if(empty($_POST["search"]))
		{
			$data["status"] = FALSE;
			$data["message"] = $this->template->success_error_htm(FALSE) ."You haven't type anything on search".
							   $this->template->error_success_after();
		}
		else
		{
			if(count($users) > 0)
			{
				$data["status"] = TRUE;
				$string_htm .= "<div class='panel panel-info panel_search'>
									<div class='panel-heading'>Your search returns ".count($users)." users</div>
									<div class='panel-body'>
										<table class='table'>
											<tr>
												<th>Photo</th>
												<th>Name</th>
												<th>Email</th>
												<th>Joined Date</th>
											</tr>
											<tbody>";
							foreach($users as $user)
							{
								$string_htm .= "<tr>
													<td><img class='photo' src='".$user["photo"]."' /></td>
													<td>".$user["first_name"]." ".$user["last_name"]."</td>
													<td>".$user["email"]."</td>
													<td>".$user["created_at"]."</td>
											   </tr>";
							}
							$string_htm .= 	"</tbody>
										</table>
									</div>
								</div><div style='clearfix'></div>";
				$data["message"] = $string_htm;
			}
			else
			{
				$data["status"] = FALSE;
				$data["message"] = $this->template->success_error_htm(FALSE) ."No results".
								   $this->template->error_success_after();
			}
		}

		echo json_encode($data);
	}

	protected function update_user()
	{
		$error_string = "";
		$img_max_size = 100000;
		$acceptable_type = array("image/jpeg","image/jpg","image/png","image/gif");

		if(empty($_POST['first_name']))
			$errors[] = "Please enter your first name";
		else if(is_numeric($_POST['first_name']))
			$errors[] = "First name cannot contain numbers";
		if(empty($_POST['last_name']))
			$errors[] = "Please enter your last name";
		else if(is_numeric($_POST['last_name']))
			$errors[] = "Last name cannot contain numbers";
		if(empty($_POST["password"]))
			$errors[] = "Password cant be empty";
		if(empty($_POST["email"]))
			$errors[] = "Email cant be empty";

		if(empty($_FILES) || !isset($_FILES['photo']))
			$errors[] = "Image cant be empty";
		else if($_FILES['photo']['size'] > $img_max_size)
			$errors[] = "Image should not exceed 1mb";
		else if(!in_array($_FILES['photo']['type'],$acceptable_type))
			$errors[] = "Invalid file type";

		if(empty($errors))
		{
			$directory = "../assets/uploads/";
			$db_directory = "assets/uploads/";
			$file_name = $_FILES['photo']['name'];
			$file_path = $directory.$file_name;
			$db_file_path = $db_directory.$file_name;
			
			if(move_uploaded_file($_FILES['photo']['tmp_name'], $file_path))
			{

				$query = "UPDATE users SET first_name='".mysqli_real_escape_string($this->connection,$_POST["first_name"])."', 
						  last_name='".mysqli_real_escape_string($this->connection,$_POST["last_name"])."',
						  email='".mysqli_real_escape_string($this->connection,$_POST["email"])."',
						  password='".mysqli_real_escape_string($this->connection,$_POST["password"])."', 
						  photo='".$db_file_path."', updated_at = NOW() 
						  WHERE id=".$_SESSION["user_info"]["user_id"];
				$update_result = mysqli_query($this->connection, $query);

				if(count($update_result) > 0)
					$data["message"] = $this->template->success_error_htm(TRUE) ."Your profile is successfully updated"
									   . $this->template->error_success_after();
				else
					$data["message"] = $this->template->success_error_htm(FALSE) ."Sql error profile not updated"
									   . $this->template->error_success_after();
			}
			else
				$data["message"] = $this->template->success_error_htm(FALSE) ."Error uploading the image"
									   . $this->template->error_success_after();

		}
		else
		{
			foreach($errors as $error)
			{
				$error_string .= $this->template->success_error_htm(FALSE) .$error. 
								$this->template->error_success_after();
			}
			$data["message"] = $error_string;
		}
		
		echo json_encode($data["message"]);

	}

	protected function is_logged_in($logged_in)
	{
		if($logged_in == TRUE)
			header("location:dashboard.php");
		else
			header("location:404.php");
	}

	protected function logout()
	{
		session_destroy();
		header("location:../index.php");
	}

}

$users = New Users();
?>	