<?php
session_start();
if(date("H:i") > date($_SESSION['expiretime'])){
    session_destroy();
    header("Location: index.php");
}
$_SESSION['expiretime'] = date("H:i", strtotime('+20 minutes'));
?>
<!--
To change this template, choose Tools | Templates
and open the template in the editor.
-->

<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title> GCS Brocast Campaing Manager - Load Target List File</title>
        <link rel="icon" href="img/tPago.ico" type="image/x-icon">
        <link href="css/bootstrap.css" rel="stylesheet">
        <style type="text/css">
        h1{text-align: left; margin-left: 5%}
        img{margin-left: 3%}
        th,tbody {text-align:center}
        button{margin-right: 18%}
        body{margin-top: 15px;}
        p{margin-left: 10%}
        </style>
    </head>
    <body>
        <div class="header">
            <img src="img/tPago.png" alt="logo" />
            <h1 class="form-signin-heading">BCM New Target List</h1>
        </div>
        <br>
        <br>
        <div class="container">
            <form action="upload.php" method="post" enctype="multipart/form-data">
                <label class="control-label" for="displayname"> Target List Name</label>
                <input name="name" type="text" class="form-control" placeholder="Target Name" required autofocus>
                <label for="displayname">Target List Description</label>
                <input name="description" type="text" class="form-control" placeholder="Target Description" required>
                <br>
                <label for="displayname">Select File</label>
                <input name="fileToUpload" data-options="required:true"  type="file" accept=".csv" class="form-control" id="mid" required>
                <br>
                <div class="col-xs-2" style="float:right">
                    <button class="btn btn-lg btn-info btn-block"  style="float:right" type="submit">Upload</button>
                </div>
            </form>
        </div>
 
    </body>
</html>
