For directions on installing Touche, see that installation docs in the
top level of this repository.

Using the Touche repository in a live system.  After cloning the
repository, it should be in a location that is not directly accessible
to the web server you've installed.  The easiest way to use the
repository is to set up symbolic links in the right locations to the
files inside.  Here are the links that need to be done (all
directories are relative to the contest user and assume the repository
is in a directory named src):

    ln -s ~/src/develop develop
    ln -s ~/src/public_html public_html/develop
    ln -s ~/src/createcontest.php public_html/createcontest.php
    ln -s ~/src/createcontest2.php public_html/createcontest2.php
    ln -s ~/src/dbcreate.sql public_html/dbcreate.sql

More documents should be created and pointed to with this README!!!
