<nav class='custom-menu'>
	<ul>
	 	<li>{{ trans('inputting.copy') }}</li>
	</ul>
</nav>

@push('css')
	<style type="text/css">
		.custom-menu {
			width: 85px;
		    display: none;
		    z-index: 1000;
		    position: absolute;
		    overflow: hidden;
		    border: 1px solid #CCC;
		    white-space: nowrap;
		    font-family: sans-serif;
		    background: #FFF;
		    border-radius: 3px;
		    padding: 0;
		}

		/* Each of the items in the list */
		.custom-menu li {
			color: #0066aa;
			font-weight: bold;
		    padding: 14px 12px;
		    cursor: pointer;
		    text-align: center;
		    list-style-type: none;
		    transition: all .3s ease;
		    user-select: none;
		}

		.custom-menu li:hover {
		    background-color: #DEF;
		}
	</style>
@endpush
@push('script')
	<script type="text/javascript">

		// $('#demos').bind("contextmenu", function (event) {
    
		//     // Avoid the real one
		//     event.preventDefault();		    
		//     // Show contextmenu
		//     $(".custom-menu").finish().toggle(100).
		    
		//     // In the right position (the mouse)
		//     css({
		//         top: event.pageY - 157 + "px",
		//         left: event.pageX + "px"
		//     });
		// });
		
		function clickRContextmenu(type, id) {
			var selector = $(".custom-menu li");
			
        	selector.attr('data-type', type);
        	selector.attr('data-id', id);
		    $(".custom-menu").finish().toggle(100).css({
		        top: event.pageY - 157 + "px",
		        left: event.pageX + "px"
		    });
		}

		// If the document is clicked somewhere
		$(document).bind("mousedown", function (e) {
		    if (!$(e.target).parents(".custom-menu").length > 0) {
		        $(".custom-menu").hide(100);
		    }
		});

		// If the menu element is clicked
		$(document).on('click','.custom-menu li', function () {
			var id = $(this).attr('data-id');
			switch ($(this).attr('data-type')) {
	            case 'ri':
	            	angular.element('#widget-grid').scope().copyRI(id);
	                break;
	            case 'mh':
	            	angular.element('#widget-grid').scope().copyMH(id);
	                break;
	            case 'tv':
	            	angular.element('#widget-grid').scope().copyTV(id);
	                break;
	            default:
	        }
	        $(".custom-menu").hide(100);
        });
		
	</script>
@endpush