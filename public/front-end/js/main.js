$(document).ready(function() {
	//select only one checkbox
	// $("input[type=checkbox]").on('click', function() {
	// 	var $box = $(this);
	//   	if ($box.is(":checked")) {
	// 	    var check = "input[name='" + $box.attr("name") + "']";
	// 	    $(check).prop("checked", false);
	// 	    $(check).prop("disabled", false);
	// 	    $box.prop("checked", true);
	// 	    $box.prop("disabled", true).css('color','pink');
	//   	} else {
	//     	$box.prop("checked", false);
	//   	}
	// });
	
	//showListSubBureaus();
	$("ul.list_rmb").find("li.active").find('a').click();
});

//get value layer tree (left side bar)
function getLayerTreeValue(){
	return +$("#layer_tree input[type=checkbox]:checked").val();
}

/**
 * show list sub bureaus when rmb changes
 * @param {Object} id
 */
function showListSubBureaus(id, map){
	if (map == true) {
		$.post("/home/load_organization", {parent_id:id}, function(res){
			$("#SB_container").html(res);
		});
		
		$("#list_rmb li").removeClass("active");
		$("#rmb"+id).addClass("active");		
	} else {
		$.post("/home/load_organization", {parent_id:id}, function(res){
			$("#SB_container").html(res);
		});
	}
	
}

