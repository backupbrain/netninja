<?php

require_once("application/index.php");

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<title>Router Admin</title>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

	<script src="assets/js/jquery-1.11.1.min.js"></script>
	
	<link  href="assets/bootstrap-3.1.1-dist/css/bootstrap.min.css" rel="stylesheet">
	<link href="assets/bootstrap-3.1.1-dist/css/bootstrap-theme.min.css"  rel="stylesheet">
	<script src="assets/bootstrap-3.1.1-dist/js/bootstrap.min.js"></script>
	<script src="assets/js/router.js"></script>
</head>

<body>

  	<a href="#content" class="sr-only sr-only-focusable">Skip to main content</a>


    <div class="container">
		<div id="loginbox" style="margin-top:50px;" class="mainbox col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">
			<div class="panel panel-info" >
				<div class="panel-heading">
					<div class="panel-title">Router Login</div>
				</div>     

				<div style="padding-top:30px" class="panel-body" >

					<div style="display:none" id="login-alert" class="alert alert-danger col-sm-12">
						Username/Password were incorrect
					</div>
                            
					<div style="clear:both">&nbsp;</div>
					<form method="post" action="/" id="loginform" class="form-horizontal" role="form">

						<div style="margin-bottom: 25px" class="input-group">
							<span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
							<input id="login-username" type="text" class="form-control" name="username" value="" placeholder="username" />
						</div>
                                
						<div style="margin-bottom: 25px" class="input-group">
							<span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
							<input id="login-password" type="password" class="form-control" name="password" placeholder="password" />
						</div>
                                    



						<div style="margin-top:10px" class="form-group">
							<div class="col-sm-12 controls">
								<a id="btn-login" href="#" onClick="javascript:$(this).parents('form:first').submit();" class="btn btn-success">Login</a>
							</div>
						</div>
					
					</form>
				</div> 
			</div>  
        </div>
    </div>
    

</body>
</html>