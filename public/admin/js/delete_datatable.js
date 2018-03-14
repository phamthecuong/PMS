function remove(id, title, yes, no)
{
	console.log(title+id);
	document.getElementById(title+id).className += " btnsubmit";
    bootbox.confirm({
    message : title,
    buttons: {
        confirm: {
            label: yes,
            className: 'btn-success'
        },
        cancel: {
            label: no,
            className: 'btn-danger'
        }
    },
    callback : function(result)
    {
        if(result)
        {
        	// document.getElementById(id).style.pointerEvents  = "none";
            document.getElementById(id).submit();
            
            // console.log(id);
        }
        else
        {
        	document.getElementById(title+id).className = " btn btn-info btn-flat";
        }
	}});
}

//  duc.dn  11.11.2016
// $('#bnt_delete').one( "click", function() {
//   alert( "This will be displayed only once." );
// });
//  $(document).ready(function(){
// 	$('#bnt_delete').click(function(){
// 		console.log('dyt');
// 	});
//  });