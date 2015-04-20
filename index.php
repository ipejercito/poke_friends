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

			var current_url = $(location).attr("href");
			if(current_url.indexOf("index.php") > -1 || current_url.indexOf("http://localhost/poke_friends/") > -1){
				$("a.logout").remove();
			}

			$("#form_register").on("submit",function(){
				var form = $(this);

				$.post(form.attr("action"), form.serialize(), function(data){
					if(data.status){
						$(".message").html(data.message).fadeIn(500, function(){
							$(this).delay(3000).fadeOut(500);
						});
					}else{
						$(".message").html(data.message).fadeIn(500, function(){
							$(this).delay(3000).fadeOut(500);
						});
					}
				},'json');

				return false;
			});

			$("#form_login").on("submit", function(){
				var form = $(this);

				$.post(form.attr("action"), form.serialize(), function(data){
					if(data.status){
						$(".message").html(data.message).fadeIn(500, function(){
							$(this).delay(3000).fadeOut(500);
						});
						setTimeout(function(){
							window.location = data.url_redirect;
						},3000);
					}else{
						$(".message").html(data.message).fadeIn(500, function(){
							$(this).delay(3000).fadeOut(500);
						});;
					}
				},"json");

				return false;
			});
			
		})
	</script>
</head>
<body>
	<div class="container">
		<?= include("./assets/includes/header.php") ?>
		<div class="row">
			<div class="col-md-6">
				<form class="form-horizontal" id="form_register" action="class/users.php" method="post"> 
					<input type="hidden" name="action" value="register">
					<h2 class="text-left">Register</h2>
					<div class="form-group">
						<label for="first_name" class="col-sm-3 control-label">First Name</label>
						<div class="col-sm-6">
							<input type="text" class="form-control" name="first_name" id="first_name" placeholder="First Name">
						</div>
					</div>
					<div class="form-group">
						<label for="last_name" class="col-sm-3 control-label">Last Name</label>
						<div class="col-sm-6">
							<input type="text" class="form-control" name="last_name" id="last_name" placeholder="Last Name">
						</div>
					</div>
					<div class="form-group">
						<label for="email" class="col-sm-3 control-label">Email</label>
						<div class="col-sm-6">
							<input type="email" class="form-control" name="email" id="email" placeholder="Email">
						</div>
					</div>
					<div class="form-group">
						<label for="password" class="col-sm-3 control-label">Password</label>
						<div class="col-sm-6">
							<input type="password" class="form-control" name="password" id="password" placeholder="password">
						</div>
					</div>
					<div class="form-group">
						<label for="confirm_password" class="col-sm-3 control-label">Confirm Password</label>
						<div class="col-sm-6">
							<input type="password" class="form-control" name="confirm_password" id="confirm_password" placeholder="Confirm Password">
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-offset-2 col-sm-10">
							<button type="submit" class="btn btn-default">Sign in</button>
						</div>
					</div>
				</form>
			</div>

			<div class="col-md-6">
				<form class="form-horizontal" id="form_login" action="class/users.php" method="post">
					<input type="hidden" name="action" value="login">
					<h2 class="text-left">Login</h2>
					<div class="form-group">
						<label for="email" class="col-sm-2 control-label">Email</label>
						<div class="col-sm-6">
							<input type="email" name="email" class="form-control" id="email" placeholder="Email">
						</div>
					</div>
					<div class="form-group">
						<label for="password" class="col-sm-2 control-label">Password</label>
						<div class="col-sm-6">
							<input type="password" name="password" class="form-control" id="password" placeholder="password">
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-offset-2 col-sm-10">
							<button type="submit" class="btn btn-default">Sign in</button>
						</div>
					</div>
				</form>
			</div>

			<div class="col-md-12">
				<div class="message text-center"></div>
			</div>

		</div>
	</div>
</body>
</html>