
<?php
global $nomPatient, $prenomPatient, $ddn;

require_once("getdata_2.php");


$title = "Agenda de <br/>$nomPatient $prenomPatient<br/>$ddn";
?>
<html>
<head>
    <title>
        <?php
            echo $title;
        ?>
    </title>
</head>
<body>
<h1><?php echo $title; ?></h1>
<?php
// table
?>


</body>
</html>
