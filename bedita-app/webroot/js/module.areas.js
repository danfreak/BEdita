/**
*	areas custom js
*
*	
*	TO DEFINE in view:
*	ajaxContentsUrl = url to ajax calls for load content for a section
*	ajaxSectionsUrl = url to ajax calls for load children sections
*	ajaxSectionObjectUrl = url to ajax calls for load a section object
*/

var ajaxContentsUrl = "/areas/listContentAjax";
var ajaxSectionsUrl = "/areas/listSectionAjax";
var ajaxSectionObjectUrl = "/areas/loadSectionAjax";


// function to bind click event on tree in publication module 
function loadSectionAjax(container) {
	
	//reset alert and icons an submits
		$(".secondacolonna .modules label").removeClass().addClass("areas");
		$("INPUT").removeAttr("readonly").removeAttr("disabled");
	//
	
	rel = container.attr("rel").split(":");
	urlC = ajaxContentsUrl + "/" + rel[1];
	urlS = ajaxSectionsUrl + "/" + rel[1];
	urlSO = ajaxSectionObjectUrl + "/" + rel[1];
	
	$(".main, #loading").show();
	
	// load section
	$("#areapropertiesC").load(urlSO, function() {
		
		// restore tab behavior for section detail tabs (permission, custom properties)
		$(".tab").toggle(
			function () {
				$(this).next().toggle() 		
				$("h2",this).css("background-position","right -25px");
	
	  		},
			function () {
				$(this).next().toggle() 		
				$("h2",this).css("background-position","right 0px");
	
	  		}
	  	);
		
		// load contents 
		$("#areacontentC").load(urlC, function() {
			
			// load children sections
			$("#areasectionsC").load(urlS, function() {
				
				$("#loading").hide();
				
			}); 
		});
	});
	
	$(".publishingtree H2 A").removeClass("on");
	$(".publishingtree LI A").removeClass("on");
	container.addClass("on");
	$("#sectionTitle").text(container.text());
	$(".head H1").text(container.text());
	
	// open tab if it's not opened
	if ( $(".tab:first").next().css("display") == "none" ) {
		$(".tab:first").click();
	}
	
}

$(document).ready(function() {

	/*...........................................    
	
	   load  areas (publishing)
	
	...........................................*/	
	
	// unbind default behavior on tree
	$(".publishingtree H2 A").unbind("click");
		
	$(".publishingtree H2 A").click(function() {
		loadSectionAjax($(this));
		action = $("#updateForm").attr("action");
	
		$("#updateForm").attr("action", action.replace(/saveSection/, "saveArea"));
	});


	/*...........................................    
	
	   load sections
	
	...........................................*/	

	// unbind default behavior on tree
	$(".publishingtree LI A").unbind("click");
	
	// set on click behavior on tree sections
	$(".publishingtree LI A").click(function() {
		loadSectionAjax($(this));
		action = $("#updateForm").attr("action");
		$("#updateForm").attr("action", action.replace(/saveArea/, "saveSection"));
	});




});