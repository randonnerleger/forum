$(document).ready(function(){
	$(".openmenugauche").click(function(){
	  $(".menu, #menugauche, .pun, #bandeau, .openmenugauche").toggleClass("open");
	});

	/*$("#brdmenu")*/
	
	$("#openbrdmenu").click(function(){
		  $("#brdmenu, #brdmain, #centermenu, #bandeau, #openbrdmenu, .punwrap").toggleClass("opened");
	});
});