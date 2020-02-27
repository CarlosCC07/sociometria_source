var xhr;
var dataOK = [false,false,false,false,false];
var errorFormMessage = ["Correcto.",
                         "Incorrecto.",
                         "Las contraseñas deben ser iguales.",
                         "Contraseña mayor o igual a 6 caracteres.",
                         "Nombre de la empresa existente.",
                         "Usuario existente.",
                         "Empresa existente.",
                         "Ya existe un análisis de esta empresa en el mismo año.",
                         "No dejes comas al inicio o al final.",
                         ];


function view(option,idCompany,nameCompany,idAnalisis,bd,idUser,user){
  var url ="";
  switch(option){
    case 0:url = "admin/companies.php";break;
    case 1:url = "admin/company_form.php";break;
    
    case 20:url = "admin/analisis.php?idCompany="+idCompany+"&nameCompany="+nameCompany;break;
    case 21:url = "admin/analisis_form.php?idCompany="+idCompany+"&nameCompany="+nameCompany;break;
    case 22:url = "admin/upload_bd.php?idCompany="+idCompany+"&nameCompany="+nameCompany+"&idAnalisis="+idAnalisis;break;
    case 23:url = "admin/duplicated_people.php?idCompany="+idCompany+"&nameCompany="+nameCompany+"&idAnalisis="+idAnalisis+"&base="+bd;break;
    case 24:url = "admin/not_inBD.php?idCompany="+idCompany+"&nameCompany="+nameCompany+"&idAnalisis="+idAnalisis+"&base="+bd;break;
    case 25:url = "admin/not_inAnswer.php?idCompany="+idCompany+"&nameCompany="+nameCompany+"&idAnalisis="+idAnalisis+"&base="+bd;break;
    case 26:url = "admin/analisis_info.php?idCompany="+idCompany+"&nameCompany="+nameCompany+"&idAnalisis="+idAnalisis;break;

    case 30:url = "admin/users.php";break;
    case 31:url = "admin/users_form.php";break;
    case 32:url = "admin/password_user.php?idUser="+idUser+"&user="+user;break;

    case 40:url = "admin/registers.php";break;

    case 50:url = "admin/help_admin.php";break;

  }
  $.ajax({
    data: null,
    url : url,
    type:'GET',
    success : function (data){
      $('div#result').html("");
      $(data).hide().appendTo('div#result').fadeIn();
      

    }
  });
}

/* Esta función recibe un campo de entrada de texto para mostrar el error correspondiente.
 * Type indica que tipo de error es. */
 
function messagesForm(field,type){    
    switch(type){
      case 0: $(field).parent().attr("class","control-group success");
              $(field).next().html(errorFormMessage[0]);
              $(field).next().attr("class","help-inline success");break;
      default:$(field).parent().attr("class","control-group error");
              $(field).next().html(errorFormMessage[type]);
              $(field).next().attr("class","help-inline error");break;
    }
}

function validateCompanyUser(field){
  if(validateBlank(field)){
    var data = {value:field.value,type:field.id};
    $.ajax({
      data: data,
      type:'POST',
      url : 'admin/exist.php',
      success : function (data){ 
        if(!parseInt(data)){
          messagesForm(field,0);
          dataOK[$("input").index(field)] = true;
        }
        else{ 
            var index = (field.id=="userCompany")?5:6;
            messagesForm(field,index);
            dataOK[$("input").index(field)] = false;
        }
      }
    });
  }
}


function validateAnalisisYear(field,name){
  if(validateBlank(field)){
    var data = {value:field.value,name:name};
    $.ajax({
      data: data,
      type:'POST',
      url : 'admin/existAnalisis.php',
      success : function (data){ 
        if(!parseInt(data)){
          messagesForm("#year",0);
          dataOK[1] = true;

        }else{
          messagesForm("#year",7);
          dataOK[1] = false;

        }
      }
    });
  }
}

/* Función para validar la contraseña de entrada en ambos campos. */

function validatePass(field){
  if(validateBlank(field)){
    if(field.value.length >= 6){
      if(field.value != $("#password").val() || field.value != $("#password2").val()){
        dataOK[2] = dataOK[3] = dataOK[4] = false;
        messagesForm(field,2);
        if(field.id == "password")
          messagesForm("#password2",2);
        else
          messagesForm("#password",2);

      }
      else{
        dataOK[2] = dataOK[3] = dataOK[4] = true;
        messagesForm(field,0);
        if(field.id == "password")
          messagesForm("#password2",0);
        else
          messagesForm("#password",0);
      }
    }
    else{
      dataOK[2] = dataOK[3] = dataOK[4] = false;
      messagesForm(field,3);
    }
  }
  else
    dataOK[4] = false;
}

/* Esta función valida que el campo de entrada no esté en blanco */
function validateBlank(field){

  if(field.value.length == 0 || /^\s+$/.test(field.value)){
    messagesForm(field,1);
    return 0;
  }else
    messagesForm(field,0);
  
  return 1;
}

function createCompany(){      
  if(dataOK[4] && dataOK[0] && dataOK[2] && dataOK[3] && dataOK[1]){
    var data = {nameCompany: $("#nameCompany").val(), userCompany : $("#userCompany").val(),password : $("#password").val(),password2 : $("#password2").val()};
    $.ajax({
      data: data,
      type:'POST',
      url : 'admin/create_company.php',
      success : function (data){   
        $('div#result').html(data);
        setTimeout(function() {
          dataOK[0] = dataOK[1] = dataOK[2] = dataOK[3] = dataOK[4] = false;
          view(0);
        }, 1500);
         
         
      }
    });
  }
  else{
    var inputs = document.getElementsByClassName('form-control');
    for(var i = 0;i<4;i++){
      if(inputs[i].value == "")
         validateBlank(inputs[i]);
    } 

    alert("Favor de llenar todos los campos.");
  }
}

function deleteAnalisis(idAnalisis,idCompany,nameCompany,bd,year){
  if(confirm("¿Realmente quieres eliminar este análisis?")){
    if(!bd){
      $.ajax({
      data: {idAnalisis:idAnalisis,nameCompany:nameCompany,year:year},
      url : 'admin/delete_analisis.php',
      type:'POST',
      success : function (data){
        alert("Borrado correctamente "+data);
        view(20,idCompany,nameCompany);
      }});

    }else{
      alert("Debes limpiar el análisis antes de eliminarlo."); 

    }
    
  }
}

function deleteCompany(id,cName){
  if(confirm("¿Realmente quieres borrar la empresa?")){
    $.ajax({
      data: {id:id,cName:cName},
      url : 'admin/delete_company.php',
      type:'POST',
      success : function (data){
        if(parseInt(data)){
          alert("Borrado correctamente");
          view(0);
        }else
          alert("Esta empresa tiene análisis, bórralos primero."); 
      }});
  }
}

function loadFiles(idCompany,nameCompany,idAnalisis,type){
  var file1 = document.getElementById('file').files[0];
  var file2 = document.getElementById('file2').files[0];
  var plants = document.getElementById('plants').value;
  var year = document.getElementById('year').value;
  var extra = document.getElementById('extra').value;
  if(type || (dataOK[0] && dataOK[1])){  
    if(file1 && file2 && plants && year){
      if(file1.name.split(".")[1] != "csv" || file2.name.split(".")[1] != "csv" )
        alert("El formato de los archivos debe de ser .csv")
      else{
       
      uploadFiles(file1,file2,plants,year,idCompany,nameCompany,idAnalisis,extra);

      }
    }else{
      var inputs = document.getElementsByClassName('form-control');
      for(var i = 0;i<inputs.length;i++){
        if(inputs[i].value == "")
           validateBlank(inputs[i]);
      } 

      alert("Favor de llenar todos los campos apropiadamente.");
    }
  }else{
      var inputs = document.getElementsByClassName('form-control');
      for(var i = 0;i<inputs.length;i++){
        if(inputs[i].value == "")
           validateBlank(inputs[i]);
      } 

      alert("Favor de llenar todos los campos apropiadamente.");

  }

  
}

function uploadFiles(f1,f2,p,y,idCompany,nameCompany,idAnalisis,extra){
  var formData = new FormData();
  formData.append('survey', f1);
  formData.append('employees', f2);
  formData.append('plants', p);
  formData.append('year', y);
  formData.append('extra', extra);

  formData.append('company', nameCompany);
  formData.append('id', idCompany);
  formData.append('idAnalisis', idAnalisis);
  
  xhr = new XMLHttpRequest();
  xhr.onreadystatechange=uploadComplete;
  xhr.open("POST", 'admin/create_analisis.php');
  xhr.send(formData);
}

function uploadComplete(){
  if (xhr.readyState==4)
  {
    if(xhr.responseText){
      dataOK[0] = dataOK[1] = false;
      var idCompany = xhr.responseText.split(',')[0];
      var nameCompany = xhr.responseText.split(',')[1];
      view(20,idCompany,nameCompany);

    }else
        $('div#result').html("<h3>Error "+xhr.responseText+"</h3>");
    
  }
}



function cleanAnalisis(idAnalisis,bdName,idCompany,nameCompany,year){
  if (confirm("¿Realmente quieres limpiar este analisis?")){
    $.ajax({
      data: {idAnalisis:idAnalisis,bdName:bdName,year:year},
      url :'admin/clean_analisis.php',
      type:'POST',
      success : function (data){
        view(20,idCompany,nameCompany);
        alert("Limpieza concluida");
      }
   });
  }

}


function init(companyBD,year,idAnalisis,idCompany,nameCompany){
  $('div#result').html(""); 
  $('div#result').html('<img src="img/loading.gif" /><br>Lectura y traspaso de información...');
  $.ajax({
    data: {companyBD:companyBD,year:year,idAnalisis:idAnalisis},
    url : 'init/init.php',
    type:'POST',
    success : function (data){
      //$('div#result').html(data); 
      count(idCompany,nameCompany);
    }
  });
}

function count(idCompany,nameCompany){
  $('div#result').html(""); 
  $('div#result').html('<img src="img/loading.gif" /><br>Conteo directo e indirecto...');
  $.ajax({
    data: null,
    url : 'init/count.php',
    success : function (data){
      global(idCompany,nameCompany);
    }
  });   
}

function global(idCompany,nameCompany){
  $('div#result').html(""); 
  $('div#result').html('<img src="img/loading.gif" /><br>Análisis global...');
  $.ajax({
    data: null,
    url : 'init/global.php',
    success : function (data){
      extend(idCompany,nameCompany);
    }
  });
}

function extend(idCompany,nameCompany){
  $('div#result').html(""); 
  $('div#result').html('<img src="img/loading.gif" /><br>Aplicando IIX...');
  $.ajax({
    data: null,
    url : 'init/extend.php',
    success : function (data){
      $('div#result').html("");     
      $('<h3>Análisis finalizado</h3>').hide().appendTo('div#result').fadeIn();
       setTimeout(function() {
          view(20,idCompany,nameCompany);
          backup();
   
        }, 2000);
      
    }
  });   
}

function backup(){
         if (confirm("¿Deseas respaldar la información?")){
             $.ajax({
                data: null,
                url : 'admin/get_db.php',
                success : function (data){
                  $.ajax({
                    data: {db:data},
                    url : 'admin/backup.php',
                    type:'POST',
                    success : function (ok){
                      if(parseInt(ok)){
                        alert("Información respaldada correctamente");
                      }else{
                        alert("Error al respaldar");
                      }
                    }
                  }); 
                  
                }
              });   
          }
}

function changePassword(idUser){
  if(dataOK[4]){
    $.ajax({
      data: {password:$("#password").val(),idUser:idUser},
      url :'admin/change_password.php',
      type:'POST',
      success : function (data){
        view(30);
        alert("Contraseña cambiada correctamente");
      }
   });

  }

}

function createUser(){
  if(dataOK[4] && dataOK[1]){
    $.ajax({
      data: {userCompany:$("#userCompany").val(),password:$("#password").val(),idCompany:$("#companies").val()},
      url :'admin/create_user.php',
      type:'POST',
      success : function (data){
        dataOK[4] = dataOK[1] = false;
        view(30);
      }
    });
  }else{

   var inputs = document.getElementsByClassName('form-control');
    for(var i = 0;i<4;i++){
      if(inputs[i].value == "")
         validateBlank(inputs[i]);
    } 

    alert("Favor de llenar todos los campos.");
  }

}

function deleteUser(idUser,user){
  if (confirm("¿Realmente deseas borrar a "+user+" de la base de datos")){
    $.ajax({
      data: {idUser:idUser,user:user},
      url :'admin/delete_user.php',
      type:'POST',
      success : function (data){
        view(30);
        alert("Usuario borrado correctamente");
      }
   });

  }

}

function validateCommas(field){

  if(validateBlank(field)){
      if(!(/^,|,$/.test(field.value))){
        messagesForm(field,0);
        dataOK[0] = true;
        return 1;
      }else{
        messagesForm(field,8);
        dataOK[0] = false;
      }

  }
  return 0;
}

function validatePlants(field){
  if(validateCommas(field)){
    var string = "";
    var plants = field.value.split(",");
    for(i in plants)
      string += plants[i]+" debe equivaler a "+i+", ";
    
    alert("En tu hoja de csv: "+string.substr(0,string.length-2));
  }

}

function validatePersonal(field){
  $('#personalPreview').html("");

  if(validateCommas(field)){
    var string = '<b>Vista Previa</b><br><br><table border="1">';
    var personal = field.value.split(",");
    for(i in personal){
      if(personal[i] != ""){
        var j = parseInt(i)+1;
        string+='<tr class="per'+j+'"><td>'+j+': '+personal[i]+'</td></tr>';
      }
    }

    string+="</table><br><br>";

    $('#personalPreview').html(string); 

  }

}

