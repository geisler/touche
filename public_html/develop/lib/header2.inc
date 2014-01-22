<?
#
# Copyright (C) 2002, 2003 David Whittington
# Copyright (C) 2003 Jonathan Geisler
# Copyright (C) 2005 Victor Replogle
# Copyright (C) 2005 Steve Overton
#
# See the file "COPYING" for further information about the copyright
# and warranty status of this work.
#
# arch-tag: lib/header.inc
#
    include_once ("lib/session.inc");
    include_once ("lib/config.inc");
    include_once ("lib/data.inc");

?>

<!DOCTYPE html>
<html>
<head>
<style>
	<?php include_once("styles/css/bootstrap.css"); ?>
</style>
</head>
<body>


<!-- This is the start of the top bar -->
<div class="page-header">
        <div class="container">
        <div class="img-responsive2">
            <?php 
            $path =  "http://$_SERVER[HTTP_HOST]/images/ToucheLogo.png";
            //header("Content-Type: image/png");
            echo "<img src='$path' alt='Logo'>";
            ?>
        </div>
         	<div class="text-right">
            	<h3><?=$team_name?></h3>
        	</div>
        </div>
</div>


<div class='container'>
<div class='innerglow'>