<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />
<title>Sociometría</title>
<link href="https://i1284.photobucket.com/albums/a569/Matchpeople/Mpicon_zpsda6b1e04.jpg" type="image/x-icon" rel="shortcut icon" />

   <link href="../Css/PNE.css" rel="stylesheet" type="text/css">
   <link href="../Css/reset.css" rel="stylesheet" type="text/css">
   <style>
   hr { height:2px; background-color:#666; padding:0px; margin:0px}
   </style>
    <script src="js/jquery-1.10.2.min.js"></script>
<script type="text/javascript">
  window.onkeypress = function(e) {
     var key = e.keyCode ? e.keyCode : e.which;

     if (key == 13) {
      verify();
     }
  }
    
     $(document).ready(function() {
          $("#triquiback img").load(function(){
               resize_bg();
          })
          $(window).resize(function(){
               resize_bg();
          })
          function resize_bg(){
               $("#triquiback img").css("left","0");
               var doc_width = $(window).width();
               var doc_height = $(window).height();
               var image_width = $("#triquiback img").width();
               var image_height = $("#triquiback img").height();
               var image_ratio = image_width/image_height;
               var new_width = doc_width;
               var new_height = Math.round(new_width/image_ratio);
               if(new_height<doc_height){
                    new_height = doc_height;
                    new_width = Math.round(new_height*image_ratio);
                    var width_offset = Math.round((new_width-doc_width)/2);
                    $("#triquiback img").css("left","-"+width_offset+"px");
               }
               $("#triquiback img").width(new_width);
               $("#triquiback img").height(new_height);
          }
     });


  function verify(){
    var username = document.getElementById("username").value;
    var password = document.getElementById("password").value;
    var data = {username:username,password:password};
    $.ajax({
      data: data,
      type:'POST',
      url : 'form.php',
      success : function (data){
        if(!parseInt(data))
          alert("Usuario o contraseña incorrectos");
        else
          if(parseInt(data) == 1)
            document.location.href="/sociometria/admin.php";
          else
            window.location = "/sociometria/index.php";

      }
    });

  }

     </script>


</head>

<body>
<div id="triquiback">
          <img src="img/shutterstock_76925098.jpg" > 
</div>

<div id="header">
  <div id="conenedorHeader">
    <div id="logo">
      <ul>
        <li>
          <div id="icono"><a href="index.php" target="_self"><img src="../img/icono.jpg"></a></div>
        </li>
        <li>
          <div id="logomatch"><a href="index.php" target="_self"><img src="../img/logoMatchpeople.png"></a></div>
        </li>
      </ul>
    </div>
    <div id="submenu" style="padding-top:1%">
    <h3 style="width:340px; float:left; text-align:center; color:white;">Sociometría</h3>
      </div>
    </div>
</div>

<div>
	<div class="entrar">
				<div class="passwordbox" >	
       				<div>
                    <h4>Por favor ingrese usuario y contraseña</h4>
                    </div>
                    <input type="text" name="username" id="username" value="" placeholder="Usuario" style="margin-bottom:5px"/>
       		<br />
					<input type="password" name="password" id="password" value="" placeholder="Contraseña"/>
					<br />
					<button name="btn" class="Esntar" onclick="verify()">Acceder</button>
				</div>
                <div class="botarga"></div>
			</div>
            
        </div>
<div id="footer" style=" position:fixed; bottom:0px; background-color:#FFF;">
	<hr>
    	<div id="contenidofooter" >
	<ul >
        <li>
          <div id="facebook"><a href="https://www.facebook.com/pages/Matchpeople/138678919477097" target="_new"></a></div>
        </li>
        <li>
          <div id="twitter"><a href="https://twitter.com/MatchPeople" target="_new"></a></div>
        </li>
        <li>
          <div id="linkedin"><a href="https://www.linkedin.com/company/match-people" target="_new"></a></div>
        </li>
        <li>
          <div id="googleplus"><a href="https://plus.google.com/u/0/108438838286769020984/posts" target="_new"></a></div>
        </li>
        <li style="background-color:#FFF; width:3px">l</li>
        <li>
          <div id="espanolActivo"><a href="index.php" target="_self"></a></div>
        </li>
        <!--<li>
          <div id="ingles"><a href="index2.php" target="_self"></a></div>
        </li>
-->      </ul></div>
</div>
</body>
</html>
