<?php

	require_once 'config.php';

	session_start();
	if(!isset($_SESSION['connect']))
		$_SESSION['connect'] = false;
		
	if($_SESSION['connect'] == true)
		header('Location: index.php'); 

	$type = 'form-group';

	if(isset($_POST['valid']))
		$type = 'form-group has-warning';
	
	if(isset($_POST['login']) and !empty($_POST['login']) and isset($_POST['password']) and !empty($_POST['password'])) {
	
		$login    = hash('sha512', $_POST['login']);
		$password = hash('sha512', $_POST['password']);
		if($login == 'a2de4c12afbf942649c13c254abe760c1e1b531cc1317253717a2e1faaf19b0874cf5b3fa9fc293e720ce8cf03d34e27b760b57a55846fb7e32ec17f822768c9') {
		
			if($password == '95af61fd5e79bff3a2fd25fe97b4e82d08b6b95b3bea4f9a874ae6b886e3351e4b3e8aea6e0b5ccc4d497cd55bd2029ac28f8a271d71bf2d60ab6924f388e494') {
		
				$_SESSION['connect'] = true;
				if(isset($_SESSION['redirect']))
					header('Location: ' . $_SESSION['redirect']);
				else
				header('Location: index.php'); 
		
			} else {
			
				$type = 'form-group has-error';
			
			}
			
		} else {
		
			$type = 'form-group has-error';
		
		}

	}

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Identification</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<link href="assets/logo_mid_color.png" rel="shortcut icon" type="image/png">
	
	<link rel="stylesheet" type="text/css" href="styles.css">	
    <link rel="stylesheet" href="./css/bootstrap.css" media="screen">
    <link rel="stylesheet" href="./css/bootswatch.min.css">
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="./js/html5shiv.js"></script>
      <script src="./js/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
   <div class="container">
	
        <div class="row">
		  <div class="col-lg-4">
		  </div>
          <div class="col-lg-4" style="vertical-align: middle;">
            <div class="well bs-component">
              <form class="form-horizontal" method="POST" action="login.php">
                <fieldset>
                  <legend class="text-center"><img src="assets/logo_mid_color.png" style="float:left;height:20px;with:auto"></img>Trends Analysis Twitter</legend>
                  <div class="<?php echo $type ?>">
                    <label for="inputLogin" class="col-lg-3 control-label" style="text-align:left">Login</label>
                    <div class="col-lg-9 ">
                      <input type="text" name="login" class="form-control" id="inputLogin" placeholder="Login">
                    </div>
                  </div>
                  <div class="<?php echo $type ?>">
                    <label for="inputPassword" class="col-lg-3 control-label" style="text-align:left">Password</label>
                    <div class="col-lg-9">
                      <input type="password" name="password" class="form-control" id="inputPassword" placeholder="Password">
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="col-lg-12 col-lg-offset-4">
                      <button name="valid" type="submit" class="btn btn-primary">Connexion</button>
                    </div>
                  </div>
                </fieldset>
              </form>
            </div>
          </div>
		  <div class="col-lg-4">
		  </div>
        </div>
      </div>
	

	</div>
    <script src="./js/bootswatch.js"></script>
  </body>
</html>