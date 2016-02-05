<?php
session_start();
if(date("H:i") > date($_SESSION['expiretime'])){
    session_destroy();
    header("Location: index.php");
}
$_SESSION['expiretime'] = date("H:i", strtotime('+20 minutes'));
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title> GCS Brocast Campaing Manager - Load Target List File</title>
        <link rel="icon" href="img/tPago.ico" type="image/x-icon">
        <link href="css/bootstrap.css" rel="stylesheet">
        <style type="text/css">
        h1{border-style: none;
           border-color: #ccc;
           border-radius: 10px;
           background-color: #8a2be2;
        }
        </style>
    </head>
    <body>
        <div class="container-fluid">
            <h1>Loading File.........</h1>
        </div>
    </body>
</html>
<?php
include 'addedFunctions.php';
$error ="";
if (isset($_FILES['fileToUpload'])) {
    $file = $_FILES['fileToUpload'];
    $file_name = $file['name'];
    $file_tmp = $file['tmp_name'];
    $file_size = $file['size'];
    $file_error = $file['error'];

    $file_ext = explode('.',$file_name);
    $file_ext = strtolower(end($file_ext));
    $allowed = array('csv');

    if(in_array($file_ext, $allowed)) {
        if($file_error == 0) {
            $file_name_new = uniqid( '', true) . '.' . $file_ext;
            $file_destination = 'uploads/' . $file_name;
            if(move_uploaded_file($file_tmp, $file_destination )) {
                pgQResult("insert into t_targets (targets_id,name,description,status) values (DEFAULT, $1, $2, DEFAULT)", array($_POST['name'],$_POST['description']));
                $error = handleCSV($file_name);
            }
        } else {
            $error = "There was an error uploading file.[$file_error]";
          }
    } else { $error = "File must be a CSV.";
      }
}
header("Location: uploadTargets.php");

function handleCSV($file_name){
    $newfile_name = 'upload_'.rand(0,9999).'.csv';
    $origCSV = fopen('uploads/'.$file_name, "r");
    $newCSV = fopen('/tmp/'.$newfile_name,"w");
    fwrite($newCSV, 'targets_id,target,target_type,telco_id,message,amount,retries');
    $targetsDS = pgQResult('SELECT targets_id as targetid from t_targets where name = $1 and description = $2', array($_POST['name'],$_POST['description']));
    while (!feof($origCSV)) {
        $oRecord = fgetcsv($origCSV, 1024);
        if($oRecord[0] <> ''){
            $nRecord = $targetsDS['0']['targetid'].','.$oRecord[0].',1,'.$oRecord[1].',null,0.00,0';
            fwrite($newCSV, PHP_EOL.$nRecord);
        }
    }
    fclose($origCSV);
    fclose($newCSV);
    try {
        pgQResult("copy t_targets_details (targets_id, target, target_type, telco_id, message, amount, retries) from '/tmp/".$newfile_name."' with CSV HEADER", array());
        unlink('upload/'.$file_name);
        unlink('/tmp/'.$newfile_name);
    } catch (Exception $e) {
        return "There was an error uploading file to database.";
    }
    return "Targets loaded successfuly....";
}
?>