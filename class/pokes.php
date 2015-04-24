<?php
	include_once("connection.php");
	include_once("users.php");
	include_once("template.php");
	Class Pokes extends Database{

		public $template;

		public function __construct()
		{
			parent::__construct();
			$this->template = new Template();
			if(isset($_POST["action"]) && $_POST["action"] == "add_poke")
				$this->add_poke();
			if(isset($_POST["action"]) && $_POST["action"] == "view_pokes_history")
				$this->view_pokes_history();
			if(isset($_GET["action"]) && $_GET["action"] == "paginate_result")
				$this->paginate_result();
		}

		protected function add_poke()
		{
			$query = "INSERT INTO pokes(my_user_id, other_user_id, status, created_at)
					  VALUE('".mysqli_real_escape_string($this->connection, $_POST["my_user_id"])."',
					  	'".mysqli_real_escape_string($this->connection, $_POST["other_user_id"])."',
					  	'".mysqli_real_escape_string($this->connection, 1)."',NOW())";
			$insert_pokes = mysqli_query($this->connection, $query);
			
			$query_pokes = "SELECT pokes.id, pokes.other_user_id FROM pokes
							WHERE pokes.id=".mysqli_insert_id($this->connection);
			$you_poked_info = $this->fetch_record($query_pokes);

			$query_get_recent_poke = "SELECT users.id, users.first_name, users.last_name, users.email,
									  pokes.other_user_id, pokes.my_user_id, COUNT(pokes.other_user_id) as number_of_pokes
									  FROM users LEFT JOIN pokes 
									  ON pokes.other_user_id = users.id
									  WHERE pokes.my_user_id ='".$_SESSION['user_info']['user_id']."' AND
									  pokes.other_user_id = '".$you_poked_info["other_user_id"]."'";
									  
			$result_get_recent_poke = $this->fetch_all($query_get_recent_poke);

			$query_count_poke = "SELECT pokes.my_user_id,
								 COUNT(pokes.id) as instance_of_pokes FROM pokes
								 WHERE pokes.my_user_id=".$_SESSION['user_info']['user_id'];
			$result_count_poke = $this->fetch_record($query_count_poke);
			$one_poke = ($result_count_poke["instance_of_pokes"] == 1) ? TRUE : FALSE;
			
			$format_recent_poke = "<tr id=".$result_get_recent_poke[0]["other_user_id"].">".
									"<td>".$result_get_recent_poke[0]["first_name"]." ".$result_get_recent_poke[0]["last_name"]."</td>".
									"<td>".$result_get_recent_poke[0]["email"]."</td>".
									"<td>".$result_get_recent_poke[0]["number_of_pokes"]."</td>".
								  "</tr>";

			$poke_first_instance = "<h3>List of persons you poked</h3>
									<table class='table table-striped users_list' id='you_poked'>
										<tr>
											<th>Name</th>
											<th>Email</th>
											<th>Number of Pokes</th>

										</tr>
										<tbody class='your_pokes'>
										</tbody>
									</table>";
			if($insert_pokes)
			{
				$data["status"] = TRUE;
				$data["user_poke"] = $you_poked_info["other_user_id"];
				$data["recent_user_poke"] = $format_recent_poke;
				$data["poke_first_instance"] = $poke_first_instance;
				$data["one_poke"] = $one_poke;
				
			}
			else
			{
				$data["status"] = FALSE;
				$data["message"] = $this->template->success_error_htm(FALSE) .
								   "You're poke didn't end well". 
								   $this->template->error_success_after();
			}

			echo json_encode($data);
		}

		public function get_poked_users($user_id)
		{
			$query = "SELECT users.id, users.first_name, users.last_name, users.email, users.created_at,
					  pokes.my_user_id, pokes.other_user_id, 
                      COUNT(pokes.other_user_id) as number_of_pokes
                      FROM users
					  LEFT JOIN pokes ON users.id = pokes.other_user_id
					  WHERE pokes.my_user_id ='".$user_id."'
					  GROUP BY users.id";
			return $this->fetch_all($query);

		}

		public function get_who_poked_me($user_id)
		{
			$query = "SELECT users.id, users.first_name, users.last_name, users.email,
					  pokes.my_user_id, pokes.other_user_id, COUNT(pokes.my_user_id) AS number_of_pokes
				      FROM pokes LEFT JOIN users ON users.id = pokes.my_user_id
					  WHERE pokes.other_user_id ='".$user_id."'
					  GROUP BY users.id";
			return $this->fetch_all($query);
		}


		public function view_pokes_history()
		{
			$poke_html = "";
			$query = "SELECT users.id, users.first_name, users.last_name, users.email,
					  pokes.my_user_id, pokes.other_user_id, pokes.created_at
					  FROM pokes LEFT JOIN users ON users.id = pokes.my_user_id
					  WHERE pokes.other_user_id ='".$_SESSION["user_info"]["user_id"].
					  "'AND pokes.my_user_id ='".$_POST["other_user_id"].
					  "'LIMIT 7";
			$persons_poked_me = $this->fetch_all($query);


			if(count($persons_poked_me) > 0)
			{
				$data["status"] = TRUE;
				foreach($persons_poked_me as $person_poked_me)
				{
					$poke_html .="<tr class='row_pokes'>
									<td>".$person_poked_me["first_name"]." ".$person_poked_me["last_name"]."</td>
									<td>".$person_poked_me["email"]."</td>
									<td>".date_format(date_create($person_poked_me["created_at"]),'g:ia \o\n l jS F Y')."</td>
								  </tr>";
				}	
				$data["message"] = $poke_html;
				$data["page_num"] = $this->pokes_history_pagination_number($_POST["other_user_id"]);
			}
			else
			{
				$data["status"] = FALSE;
				$data["message"] = $this->template->success_error_htm(FALSE) .
								   "Something went wrong while fetching your pokes".
								   $this->template->error_success_after();
			}

			echo json_encode($data);
		}

		public function pokes_history_pagination_number($other_user_id)
		{
			$htm_page_num = "";
			$per_page = 7;
			$query = "SELECT COUNT(*) as total_rows
					  FROM pokes LEFT JOIN users ON users.id = pokes.my_user_id
					  WHERE pokes.other_user_id ='".$_SESSION["user_info"]["user_id"]."'
					  AND pokes.my_user_id =".$other_user_id;
			$pokes_count = $this->fetch_record($query);
			$max_page_num = ceil($pokes_count["total_rows"] / $per_page);

			if($pokes_count["total_rows"] > 7)
			{
				$htm_page_num .= "<nav>
									<ul class='pagination'>";
				foreach(range(1, $max_page_num) as $page_number)
				{
					$htm_page_num .= "<li>
										<a href='pokes.php?action=paginate_history&paginate_result=".$page_number."'>".$page_number."</a>
									  </li>";
				}

				$htm_page_num .= "	</ul>
								</nav>";
			}

			return $htm_page_num;
		}

		public function paginate_result()
		{
			echo "here";
		}

	}

	$pokes = new Pokes();
?>
