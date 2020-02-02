<?php
require '../headers/admin_header.php';

// echo exec("sudo -u root -s ./backup.sh -p < pass.txt");
//echo shell_exec("mkdir jejejee");
//echo shell_exec("bash -c 'FILES=/opt/lampp/var/mysql/*/;for f in \$FILES;do name=`basename \$f`;/opt/lampp/bin/mysqldump -u administrator -pMatch2014 \$name > ~/Dropbox/Respaldo_Sociometria/BD/\$name.sql; done'");

echo shell_exec("bash -c 'FILES=/opt/lampp/var/mysql/*/;for f in \$FILES;do aux=`basename \$f`;name=\"\$name,\$aux\"; done;echo \$name;'");

//shell_exec("bash -c '/opt/lampp/bin/mysqldump -u administrator -pMatch2014 adminSociometria > ~/Dropbox/Respaldo_Sociometria/BD/holax.sql'");

?>
