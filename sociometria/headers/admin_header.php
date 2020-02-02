<?php   
if(session_status() == PHP_SESSION_NONE) 
		session_start();
if(empty($_SESSION['user']))
    header('Location:bienvenida.php');
else if($_SESSION['permissions'] == 2 )
        header('Location:index.php');
?>