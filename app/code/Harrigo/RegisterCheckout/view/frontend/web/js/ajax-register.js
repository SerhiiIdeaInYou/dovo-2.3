require(["jquery"], function($){


	//waits for elements to load in checkout
	function waitForElement(elementPath, callBack){
	  window.setTimeout(function(){
		if($(elementPath).length){
		  callBack(elementPath, $(elementPath));
		}else{
		  waitForElement(elementPath, callBack);
		}
	  },300)
	}
	var link = window.location.href;
	//get crosssell products / newsletter
	$.ajax({
	  url: "../harrigoregister/index/register",
	  type: "post",
	  data: {
		  lang: link
	  },
	  success: function(response) {
		waitForElement("#registerblock",function(){
					$("#registerblock").html(response);
			});
	  },
	  error: function(xhr) {
	  }
	});
	


});

