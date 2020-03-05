
function changeNotFoundOrder(object){
	notFound(object.selectedIndex);
}      

function notFound(order){
	$.ajax({
		data: null,
		url : 'ajaxShow/notFound.php?order='+order,
		success : function (data){
			$('div#charging').html("");
			$('div#result2').html("");
			$('div#result').html("");				 	
		 	$(data).hide().appendTo('div#result').fadeIn();
		 	var select = document.getElementById("order");
 			select.selectedIndex = order;
		}
	});
}



function globalAnalisisShow(plant,orderBy,limit,seg,extra){
	var value = 0;
        var segm = 0;

	if(extra != null)
		value = extra;
        if(seg != null)
                segm = seg;

	$.ajax({
		data: null,
		url : 'ajaxShow/analisisGlobalShow.php?plant='+plant+"&orderBy="+orderBy+"&limit="+limit+"&seg="+segm+"&value="+value,
		success : function (data){
			$('div#charging').html("");
			$('div#result2').html("");
			$('div#result').html("");		 	
		 	$(data).hide().appendTo('div#result').fadeIn();
		 //	if(value == 0){
		 		var select = document.getElementById("orderBy");
		 		select.selectedIndex = orderBy;
		 		select = document.getElementById("limit");
		 		select.selectedIndex = limit;
                                select = document.getElementById("seg");
                                select.selectedIndex = segm;
		 	//}
		}
		});
}

function analisis(type,plant,orderBy,limit,seg,extra){

	var value = 0;
	var segm = 0;
	var lim = 0;
	var oB = 2;
	if(extra != null)
	    value = extra;
	if(seg != null)
	    segm = seg;
	if(limit != null)
	    lim = limit;
	if(orderBy != null)
	    oB = orderBy;

		$.ajax({
				data: null,
				url : 'ajaxShow/analisis.php?type='+type+'&plant='+plant+'&orderBy='+orderBy+'&limit='+limit+'&seg='+segm+'&value='+value,
				success : function (data){
					$('div#charging').html("");
					$('div#result2').html("");
					$('div#result').html("");				 	
				 	$(data).hide().appendTo('div#result').fadeIn();
				 	$('#download').attr("href","http://pdfcrowd.com/"+window.location);
				 	
					var select = document.getElementById("orderBy");
					select.selectedIndex = orderBy;
					select = document.getElementById("limit");
					select.selectedIndex = limit;
					select = document.getElementById("seg");
					select.selectedIndex = segm;
				}
				});

}

function analisisPerson(idPerson,type,plant){
	$.ajax({
		data: null,
		url : 'ajaxShow/analisisPerson.php?idPerson='+idPerson+'&type='+type+'&plant='+plant,
		success : function (data){
			$('div#charging').html("");
			$('div#result2').html("");
			$('div#result').html("");
			$(data).hide().appendTo('div#result').fadeIn();
		 	
		}
		});

}

function searchName(){

	var name = $("#search").val();
	if(name!=""){
		var $contentAjax = $('div#charging').html('<img src="img/loading.gif" />');
		$('div#result').html("");	
		var x;
		if(window.event){ // IE8 and earlier
			x=event.keyCode;
		}else if(event.which){ // IE9/Firefox/Chrome/Opera/Safari
			x=event.which;
		}
		keychar=String.fromCharCode(x);
		$.ajax({
				data: null,
				url : 'ajaxShow/search.php?name='+name,
				success : function (data){
					$('div#charging').html("");
					
				 	$('div#result').html(data);
				 	
				}
		});
	}

}

function help(){
	$.ajax({
		data: null,
		url : 'ajaxShow/help.php',
		success : function (data){
			$('div#charging').html("");
			$('div#result2').html("");
			$('div#result').html("");
			$(data).hide().appendTo('div#result').fadeIn();
		 	
		}
		});
}


/*
 * Función para mostrar información de ayuda antes de mandar a imprimir
 * o descargar
 *
 */

function printInfo(){
		$(function (){ 
			$("#help1").popover('show');
		});
		
		setTimeout(function() {
			$('#help1').popover('destroy');  
		}, 3000);
	
	
	}

/*
 * Función para llamar a imprimir
 *
 */
	
function printContent(){
	window.print();
}

/*
 * Función para cambiar la forma en la que aparece la información en 
 * análisis global
 * objet : objeto del select donde se guarda informacion de que tipo es
 */

function changeGlobalOrder(object,extra){


		var type = document.getElementById("orderBy");
		var type2= document.getElementById("limit");
                var type3= document.getElementById("seg");

		globalAnalisisShow(object.name,type.selectedIndex,type2.selectedIndex,type3.selectedIndex,extra);
}

function changeTypeOrder(object,currentPlant,extra){
	var type = document.getElementById("orderBy");
	var type2= document.getElementById("limit");
	var type3= document.getElementById("seg");

	analisis(object.name,currentPlant,type.selectedIndex,type2.selectedIndex,type3.selectedIndex,extra);
}

/*
 * Función para mostrar al momento de una búsqueda que información desplegar
 * de esa persona.
 * idPerson: id de la persona a buscar, name: nombre de la persona
 */
	
function whichInfo(idPerson,name){
		
		var info = "<h4>"+name+"</h4><br><br>"+
					"<a onclick=\"analisisPerson("+idPerson+",0)\">Ascendencia</a><br>"+
					"<a onclick=\"analisisPerson("+idPerson+",1)\">Afinidad</a><br>"+
					"<a onclick=\"analisisPerson("+idPerson+",2)\">Popularidad</a>";
		$('div#result').html(info);
		
}

/*
 * Función para tomar imagen de las tablas y diagramas que arroja el sistema.
 *
 */

function takeSnapShot(){
		
		   html2canvas($("#infoDownload"), {
		   		onrendered: function(canvas) {
		       		var image = canvas.toDataURL("image/png");
		       		var win=window.open(image, '_blank');		       
		     	},letterRendering: 1,allowTaint:1

		    });
}

function showindirect(idPerson,type,isII){
	$.ajax({
		data: null,
		url : 'ajaxShow/showIndirect.php?idPerson='+idPerson+'&type='+type+'&isII='+isII,
		success : function (data){
			$('div#charging').html("");
			$('div#result2').html("");
			$('div#result').html("");
			$(data).hide().appendTo('div#result').fadeIn();
		 	
		}
		});


}

function showInfo(idAnalisis,analisisName){
	$.ajax({
		data: {idAnalisis:idAnalisis},
		url : 'ajaxShow/show_info.php',
		type: 'POST',
		success : function (data){
			$('div#index').html("");
			$(data).hide().appendTo('div#index').fadeIn();
			$('#analisisName').html(analisisName);
			
		}
	});
}
