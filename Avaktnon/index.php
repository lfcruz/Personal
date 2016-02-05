<?php 
session_start();
?>
<html lang="en">
  <head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" href="img/Good-Bad_Debt.ico" type="image/x-icon">
  <title>Avaktnon GDM - Login</title>
  <link href="css/login.css" rel="stylesheet">
  <link href="css/bootstrap.css" rel="stylesheet">
  <!--<script src="js/jquery.js"></script>-->
  </head>

  <body>

      <div class="container">

        <form action="lib/interface.php" method="post"  class="form-signin" >
        <img src ="img/Good-Bad_Debt.png" height="60" width="65">
        <h3 class="form-signin-heading">Avaknon GDM Login</h3>
        <label class="control-label" for="displayname">User</label>
        <input name="name" type="text" class="form-control" placeholder="User Name" required autofocus>
        <label for="displayname">Password</label>
        <input name="password" type="password" class="form-control" placeholder="Password" required>
        <button class="btn btn-lg btn-info btn-block" type="submit">Sign in</button>

      </form>

    </div>


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <!-- <script src="js/bootstrap.min.js"></script> -->
  </body>
</html>