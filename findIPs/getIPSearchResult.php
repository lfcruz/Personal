<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <?php
        $tableData = json_decode(file_get_contents('resultFound.json'),true);
        echo "<table border='1'";
        echo "<tr><td>IP</td><td>City</td></tr>";
        foreach($tableData as $keyTable => $keyTableValue){
            echo "<tr><td>".$keyTable."</td><td>".$keyTableValue."</td></tr>";
        }
        echo "</table>";
        ?>
    </body>
</html>
