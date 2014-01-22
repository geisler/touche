<?php
include_once("lib/session.inc");
include_once("lib/create.inc");
?>
<html>
<head>
    <style>
    <?php include_once("develop/styles/css/bootstrap.css"); ?>
    </style>
</head>

<body>

<div class="page-header">
    <div class="container">
        <div class="img-responsive2">
            <?php 
            $path =  "http://$_SERVER[HTTP_HOST]/images/ToucheLogo.png";
            header("Content-Type: image/png");
            echo "<img src='$path' alt='Logo'>";
            ?>

        </div>
        <div class="text-right">
        <h2>ADMIN</h2>
    </div>
    </div>
</div>


<div class="container">
    <h2>Initial Contest Setup</h2>

                            
                            
                
                          
                                <td bgcolor="#ffffff" colspan="2">
<?php			echo "<center>\n";

                # Print out any errors
                if(isset($error)) {
                    echo "<br>";
                    foreach($error as $er) {
                        echo "<b><font color=#ff0000>$er</font></b>";
                    }
                }

                echo "</center>";
                
                //Begin Form
                echo "<form  class=\"form-horizontal\" method=POST action=createcontest2.php>\n";

                //Name of Contest Host/Organization field
                
                echo "<div class=\"row\">";
                echo "<div class=\"col-xs-4\">";
                echo "<div class=\"col-sm-10\">";
                echo "<input type=\"text\" class=\"form-control\" name=\"contest_host\" placeholder=\"Name of Contest Host/Organization\" value=\"$host\">";
                echo "</div>";
                echo "</div>";
                echo "</div>";

                //Contest Name field
                echo "<div class=\"row\">";
                echo "<div class=\"col-xs-4\">";
                echo "<div class=\"col-sm-10\">";
                echo "<input type=\"text\" class=\"form-control\" name=\"contest_name\" placeholder=\"The Contest's Name\" value=\"$contest_name\">";
                echo "</div>";
                echo "</div>";
                echo "</div>";

                //Database Host field
                echo "<div class=\"row\">";
                echo "<div class=\"col-xs-4\">";
                echo "<div class=\"col-sm-10\">";
                echo "<input type=\"text\" class=\"form-control\" name=\"dbhost\" placeholder=\"Database Host\" value=\"$username\">";
                echo "</div>";
                echo "</div>";
                echo "</div>";

                //Database Host Password field 
                echo "<div class=\"row\">";
                echo "<div class=\"col-xs-4\">";
                echo "<div class=\"col-sm-10\">";
                echo "<input type=\"password\" class=\"form-control\" name=\"dbpassword\" placeholder=\"Database Host Password\" value=\"$password\">";
                echo "</div>";
                echo "</div>";
                echo "</div>";

                //Submit Button field
                echo "<div class=\"row\">";
                echo" <div class=\"col-xs-4\">";
                echo "<div class=\"col-sm-10\">";
                echo "<input type=\"submit\" class=\"btn btn-default\" name=\"B1\" value=\"Submit\">";
                echo "</div>";
                echo "</div>";
                echo "</div>";

                // End Form field 
                echo "  </form>";
                echo "</div>";
?>
