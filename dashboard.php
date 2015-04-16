<?php
	include("class/users.php");
	include("class/pokes.php");
	$users = new Users();
	$pokes = new Pokes();

	if(!isset($_SESSION["user_info"]) && empty($_SESSION["user_info"]))
		header("location:404.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Document</title>
	<link rel="stylesheet" href="assets/style/style.css">
	<link rel="stylesheet" href="assets/style/bootstrap.css">
	<script type="text/javascript" src="assets/js/jquery.js"></script>
	<script type="text/javascript">
		$(document).ready(function(){

			var number_of_people = $("span.number_of_people").length;
			if(number_of_people > 0){
				$(".h2_dashboard").append('<h4>"There are <b>'+number_of_people+'</b> people who poked you"');
			}else{
				$("div.well").removeClass("col-md-3");
				$("div.well").css("width","162px").prepend("<h4><strong>No Pokes Yet!</strong></h4>");
			}

			
			var no_pokes = document.getElementById("no_pokes");
			if(no_pokes != null){
				$("#no_pokes").append("<h3 class='text-center no_pokes_text'><strong>No Pokes Yet</strong></h3>")
			}
			

			$(".poke_form").on("click",function(){

				var form = $(this);
				var id_array = [];
				var name = form.parents("td").parents("tr").children().closest("td:first-child").text();

				$.post(form.attr("action"), form.serialize(), function(data){
					if(data.status){

						$("#no_pokes, .no_pokes_text").remove();

						var you_poked = $("#you_poked").children().find("tr");

						$.each(you_poked, function(index, val ) {
							id_array.push(val.id);
							if(val.id == data.user_poke){
								increment_poke = $(this).children("td:last-child").text();
								$(this).children("td:last-child").text(parseInt(increment_poke) + 1)
							}
						});

						
						if($.inArray(data.user_poke,id_array) == -1){
							$(".your_pokes").append(data.recent_user_poke);
						}

						if(data.one_poke){
							$("#second_row").prepend(data.poke_first_instance);
							$(".your_pokes").append(data.recent_user_poke);
						}

						$(".message").html("<div class='alert alert-success col-md-6 col-md-6 col-md-offset-3 text-center'><strong>You've just poke "+name+"</strong></div>");
						
					}else{
						$(".message").html(data.message);
					}
				},"json");
				return false;
			});
			
		})
	</script>
</head>
<body>
	<div class="container">
		<div class="row">
			<?= include("./assets/includes/header.php") ?>
			<h2 class="h2_dashboard">Welcome <?= $_SESSION['user_info']['first_name'] ?> <?= $_SESSION['user_info']['last_name'] ?></h2>
			<div class="well col-md-3">
<?php 		foreach($pokes->get_who_poked_me($_SESSION['user_info']['user_id']) as $other_pokes)
			{	?>
				<span class="number_of_people"></span>
				<h5><?= $other_pokes["first_name"] ?> <?= $other_pokes["last_name"] ?> poked you <strong><?= $other_pokes["number_of_pokes"] ?> times</strong></h5>
	<?php 	}	?>
			</div>
			<div class="clearfix"></div>
			<h3>List of persons you can poke</h3>
			<table class="table table-striped users_list">
				<tr>
					<th>Name</th>
					<th>Email</th>
					<th>Joined Date</th>
					<th>Action</th>
				</tr>
				<tbody>
			<?php
				foreach($users->get_other_users($_SESSION['user_info']['user_id']) as $user)
				{	?>
					<tr>
						<td><?= $user["first_name"] ?>  <?= $user["last_name"] ?></td>
						<td><?= $user["email"] ?></td>
						<td><?= $user["created_at"] ?></td>
						<td>
							<form class="poke_form" action="class/pokes.php" method="post">
								<input type="hidden" name="action" value="add_poke">
								<input type="hidden" name="my_user_id" value="<?= $_SESSION["user_info"]["user_id"] ?>">
								<input type="hidden" name="other_user_id" value="<?= $user["id"] ?>">
								<input class="btn btn-primary" type="submit" value="poke">
							</form>
						</td>
					</tr>
		<?php   }	?>
				</tbody>
			</table>
			<div class="message"></div>
		</div>
		<div class="row" id="second_row">
	<?php 	
		if(count($pokes->get_poked_users($_SESSION['user_info']['user_id'])) > 0)
		{	?>
			<h3>List of persons you poked</h3>
			<table class="table table-striped users_list" id="you_poked">
				<tr>
					<th>Name</th>
					<th>Email</th>
					<th>Number of Pokes</th>

				</tr>
				<tbody class="your_pokes">	
			<?php
					foreach($pokes->get_poked_users($_SESSION['user_info']['user_id']) as $user_pokes)
					{	?>
					<tr id="<?= $user_pokes["other_user_id"] ?>">
						<td><?= $user_pokes["first_name"] ?> <?= $user_pokes["last_name"] ?></td>
						<td><?= $user_pokes["email"] ?></td>
						<td><?= $user_pokes["number_of_pokes"] ?></td>
					</tr>
			<?php 	}	?>					
				</tbody>
			</table>
<?php 	}
		else
		{	?>
			<span id="no_pokes"></span>
<?php	}	?>
		</div>
	</div>
</body>
</html>