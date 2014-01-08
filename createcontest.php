<?php
include_once("lib/session.inc");
include_once("lib/create.inc");
?>
<html>
<body bgcolor="<?=$page_bg_color?>" link="0000cc" alink="000066" vlink="0000cc">
<table width="90%" align="center" cellpadding="1" cellspacing="0" border="0" bgcolor="#000000">
        <tr><td>
                <table width="100%" cellpadding="5" cellspacing="0" border="0">
                        <tr bgcolor="<?=$title_bg_color?>">
                                <td>
                                <!-- Beautification hack. 2006-09-25 -sb -->

                                <font color="#ffffff">
                                <b>Creating Contest</b>  <small></small>
                                </font>
                                </td>
                                <td align="right">
                                         <font color="#ffffff">
                                         <b>ADMIN</b>
                                         </font>
                                </td>
                        </tr>
                        <tr>
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
                echo "<p>";
                echo "<form method=POST action=createcontest2.php>\n";
                echo "<table align=center bgcolor=#ffffff cellpadding=0 cellspacing=0 border=0<tr><td>";
                echo "<table width=100% cellpadding=5 cellspacing=1 border=0>\n";
                echo "  <tr bgcolor=\"$hd_bg_color1\">\n";
                echo "          <td align=\"center\" colspan=\"2\"><font color=\"$hd_txt_color1\"><b>Contest Setup</b></font></td>\n";
                echo "  </tr>";
                echo "  <tr bgcolor=\"$hd_bg_color2\">";
                echo "          <td colspan=\"2\">Fill out info for your contest:</td>";
                echo "  </tr>";
        echo "          <tr bgcolor=\"$data_bg_color1\">";
        echo "                  <td>Name of the contest <b>host</b>:</td>";
        echo "                  <td><input type=\"text\" name=\"contest_host\" ";
        echo "                          size=\"20\" value=\"$host\">";
        echo "                          </input></td>";
        echo "          </tr>";
        echo "          <tr bgcolor=\"$data_bg_color1\">";
        echo "                  <td>The contest's <b>name</b>:</td>";
        echo "                  <td><input type=\"text\" name=\"contest_name\" ";
        echo "                          size=\"20\" value=\"$contest_name\">";
        echo "                          </input></td>";
        echo "          </tr>";
        echo "          <tr bgcolor=\"$data_bg_color1\">";
        echo "                  <td>Database Host:</td> ";
        echo "                  <td><input type=\"text\" name=\"dbhost\" ";
        echo "                          size=\"20\" value=\"$username\"></td>";
        echo "          </tr>";
        echo "          <tr bgcolor=\"$data_bg_color1\">";
        echo "                  <td>Database Host Password</td> ";
        echo "                  <td><input type=password name=\"dbpassword\" ";
        echo "                          size=\"20\" value=\"$password\"></td>";
        echo "          </tr>";
        echo "          <tr align=center bgcolor=\"$data_bg_color1\">";
        echo "                  <td colspan=2> <input type=\"submit\" value=";
        echo                    "\"Submit\" name=\"B1\"></input></td> ";
        echo "          </tr>";
        echo "  </table>";
        echo "  </form>";
?>
