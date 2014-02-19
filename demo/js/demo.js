$(document).ready(function(){ 
	$("form#facets").facets({ 
		URLParams : [ {
    				name: "ajax",
    				value: "true"
					},
					],
		preAJAX : function () { 
			//validate inputs here!
			var minPrice = $("#minPrice").val();
			var maxPrice = $("#maxPrice").val();
			if((minPrice != "" && minPrice != undefined && !/^\d+$/.test(minPrice)) || (maxPrice != "" && maxPrice != undefined && !/^\d+$/.test(maxPrice))) { 
				alert("Minimum and Maximum price must be numbers.");
				return false;	
			}
			else if(minPrice != undefined && maxPrice != undefined && /^\d+$/.test(minPrice) && /^\d+$/.test(maxPrice) && parseInt(minPrice) > parseInt(maxPrice)) {
				alert("Maximum price must be larger than minimum price.");
				return false;
			}

			var minYear = $("#minYear").val();
			var maxYear = $("#maxYear").val();
			if((minYear != "" && minYear != undefined && !/^\d{4}$/.test(minYear)) || (maxYear != "" && maxYear != undefined && !/^\d{4}$/.test(maxYear))) { 
				alert("Minimum and Maximum year must contain 4 numbers.");
				return false;	
			}
			else if(minYear != undefined && maxYear != undefined && /^\d+$/.test(minYear) && /^\d+$/.test(maxYear) && parseInt(minYear) > parseInt(maxYear)) {
				alert("Maximum year must be larger than minimum year.");
				return false;
			}

			return true;
		}
	});
});
