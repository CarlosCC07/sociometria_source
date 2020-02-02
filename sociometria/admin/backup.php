<?php
require '../headers/admin_header.php';

	$databases = explode(",",$_POST['db']);
	$i;
	for($i=1;$i<count($databases);$i++){
		$realValue = $databases[$i];
		shell_exec("bash -c '/opt/lampp/bin/mysqldump -u administrator -pMatch2014 ".$realValue." > ~/Dropbox/Respaldo_Sociometria/BD/".$realValue.".sql'");
	}
	if($i == count($databases))
		echo 1;
	else
		echo 0;

// echo exec("sudo -u root -s ./backup.sh -p < pass.txt");
//echo shell_exec("mkdir jejejee");
//echo shell_exec("bash -c 'FILES=/opt/lampp/var/mysql/*/;for f in \$FILES;do name=`basename \$f`;/opt/lampp/bin/mysqldump -u administrator -pMatch2014 \$name > ~/Dropbox/Respaldo_Sociometria/BD/\$name.sql; done'");

//echo shell_exec("bash -c 'FILES=/opt/lampp/var/mysql/*/;for f in \$FILES;do name=`basename \$f`;echo \$name; done'");

//shell_exec("bash -c '/opt/lampp/bin/mysqldump -u administrator -pMatch2014 adminSociometria > ~/Dropbox/Respaldo_Sociometria/BD/holax.sql'");

?>
