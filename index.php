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
	<script type="text/javascript" src="assets/js/bootstrap.min.js"></script>
	<script type="text/javascript">
		$(document).ready(function(){

			var current_url = $(location).attr("href");
			if(current_url.indexOf("index.php") > -1 || current_url.indexOf("http://localhost/poke_friends/") > -1){
				$("a.logout").remove();
			}

			$(".form_register").on("submit",function(){
				var form = $(this);

				$.post(form.attr("action"), form.serialize(), function(data){
					if(data.status){
						$(".message_search").hide();
						$(".message").hide();
						$(".message_generic").html(data.message).fadeIn(500, function(){
							$(this).delay(2000).fadeOut(500);
						});
					}else{
						$(".message_search").hide();
						$(".message").html(data.message).fadeIn(500, function(){
							$(this).delay(2000).fadeOut(500);
						});
					}
				},'json');

				return false;
			});


			$("#form_login").on("submit", function(){
				var form = $(this);

				$.post(form.attr("action"), form.serialize(), function(data){
					if(data.status){
						$(".message_search").hide();
						$(".message_generic").html(data.message).fadeIn(500, function(){
							$(this).delay(2000).fadeOut(500);
						});
						setTimeout(function(){
							window.location = data.url_redirect;
						},2000);
					}else{
						$(".message_search").hide();
						$(".message").html(data.message).fadeIn(500, function(){
							$(this).delay(2000).fadeOut(500);
						});
					}
				},"json");

				return false;
			});

			 $('.dropdown-menu').click(function(e) {
			        e.stopPropagation();
			 });

			
			/*$(".dropdown_btn").on("click", function(){
				if($(this).parent("#login_dropdown").hasClass("open"))
					$("#register_dropdown").removeClass("open");
					
				if($(this).parent("#register_dropdown").hasClass("open"))
					$("#login_dropdown").removeClass("open");
			});*/

			$('.dropdown').on('show.bs.dropdown', function(e){
				$(this).find('.dropdown-menu').first().stop(true, true).slideDown();
			});
			$('.dropdown').on('hide.bs.dropdown', function(e){
				$(this).find('.dropdown-menu').first().stop(true, true).slideUp();
			});

			$(".search").on("change",function(){
				$(".search").submit();
			});

			$(".search").on("submit", function(){
				var form = $(this);
				$.post(form.attr("action"), form.serialize(), function(data){
					if(data.status){
						$(".message_search").show();
						$(".message").hide();
						$(".message_search").html(data.message);
					}else{
						$(".message_search").hide();
						$(".message").html(data.message).fadeIn(500, function(){
							$(this).delay(2000).fadeOut(500);
						});
					}
				},"json");

				return false;
			});
			
			

		})
	</script>
</head>
<body>
	<div class="container-fluid">
		<div id="top_nav" class="navbar navbar-inverse navbar-fixed-top">
			<div class="container">
				<a data-toggle="modal" data-target="#login_modal" class="brand text-center">
					<h3 class="pull-left "style="">XD Poke Friends</h3>
				</a>

				<form action="class/users.php" method="post"  class="form-inline pull-left dropdown search">
					<input type="hidden" name="action" value="search_user">
					<div class="form-group">
    					<input type="text" name="search" class="form-control" id="form_search"  placeholder="Search i.e name/email">
  					</div>
				</form>

				<a href="class/users.php?url=logout" class="brand logout">   
				    <h3 class="pull-right">Logout</h3>
				</a>

				<div class="btn-group dropdown keep-open pull-right" id="login_dropdown">
					<button type="button" class="btn btn-primary">Login</button>
					<button type="button" class="btn btn-primary dropdown-toggle dropdown_btn" data-toggle="dropdown">
						<span class="caret"></span>
					</button>
					<ul class="dropdown-menu" role="menu" >
						<form id="form_login" action="class/users.php" method="post">
							<input type="hidden" name="action" value="login">
							<li>
								<input type="email" name="email" class="form-control" id="email" placeholder="Email">
							</li>
							<li>
								<input type="password" name="password" class="form-control" id="password" placeholder="password">
							</li>
							<li>
								<input type="submit" class="btn btn-default" value="submit"/>
							</li>
						</form>
					</ul>
				</div>

				<div class="btn-group dropdown keep-open pull-right" id="register_dropdown" style="margin-right: 15px;">
					<button type="button" class="btn btn-primary">Register</button>
					<button type="button" class="btn btn-primary dropdown-toggle dropdown_btn" data-toggle="dropdown">
						<span class="caret"></span>
					</button>
					<ul class="dropdown-menu" role="menu" >
						<form class="form_register" action="class/users.php" method="post">
							<input type="hidden" name="action" value="register">
							<li>
								<input type="text" class="form-control" name="first_name" placeholder="First Name" />
							</li>
							<li>
								<input type="text" class="form-control" name="last_name" placeholder="Last Name">
							</li>
							<li>
								<input type="email" class="form-control" name="email" placeholder="Email">
							</li>
							<li>
								<input type="password" class="form-control" name="password" placeholder="password">
							</li>
							<li>
								<input type="password" class="form-control" name="confirm_password" placeholder="Confirm Password">
							</li>
							<li>
								<input type="submit" class="btn btn-default" value="submit"/>
							</li>
						</form>
					</ul>
				</div>

				<div class="clearfix"></div>
			</div>
		</div>
		<div class="spacer"></div>


		<div class="messages">
			<div class="col-md-12">
				<div class="message_search"></div>
				<div class="message text-center"></div>
				<div class="message_generic text-center"></div>
			</div>
		</div>
	</div>
</body>
</html>