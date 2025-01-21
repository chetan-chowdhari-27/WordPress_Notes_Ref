(function($){
	$(document).ready(function(){

		$( ".state-sort").change(function(e) {
      		var sortbyStates = $('.state-sort').find(":selected").data('sortbystates');
      		filterResult(true)
      		getCity(sortbyStates)
      		getDepartment(sortbyStates)
		});

		$( ".worklocation").change(function(e) {
      		filterResult()	
		});
        
        $( ".city-sort").change(function(e) {
      		var sortbyStates = $('.state-sort').find(":selected").data('sortbystates');
      		var sortbycity = $('.city-sort').find(":selected").data('sortbycity');
      		filterResult(false, true)
      		getDepartment(sortbyStates, sortbycity)
		});

		$( ".department-sort").change(function(e) {
      		filterResult(false)
		});

		$( ".jobtype-sort").change(function(e) {
			var sortbyJobtype = $('.jobtype-sort').find(":selected").data('sortbyjobtype');
      		filterResult(false, false)
		});

		function filterResult(reset, departemnt_reset){
            
            var search_keyword = jQuery('#autocomplete').val();
      		var sortbyStates = $('.state-sort').find(":selected").data('sortbystates');
      		var sortbycity = $('.city-sort').find(":selected").data('sortbycity');
      		var sortbydepartment = $('.department-sort').find(':selected').data('sortbydepartment');
      		var sortbyJobtype = $('.jobtype-sort').find(":selected").data('sortbyjobtype');
      		
      		if ( $('.worklocation').is(':checked') == true) {
      			var worklocation = 'yes';
      		} else {
      			var worklocation = 'No';
      		}
      		if(reset){
	      		sortbycity = '';
	      		sortbydepartment = '';
      		}
      		if(departemnt_reset){
				sortbydepartment = ''
			}
			
            $.ajax({
				url:wp_ajax.ajax_url,
				data: { action: 'orders_filter_sort',  search_keyword : search_keyword, sortbystates : sortbyStates , sortbycity : sortbycity , sortbydepartment : sortbydepartment, sortbyJobtype : sortbyJobtype , worklocation: worklocation },
				type: 'post',
				success: function(result) {
					$('.jobs-section').html(result);
				},
				error:function (result){
				   console.warn(result);
				}
			});
		}

		// Filter Cities
        function getCity(states,city){

        	var sortbycity = $('.city-sort').find(":selected").data('sortbycity');
      		var sortbydepartment = $('.department-sort').find(':selected').data('sortbydepartment');

            $('#citysort').attr('disabled','disabled');
            $('#departmentsort').attr('disabled','disabled');
        	$.ajax({
				url:wp_ajax.ajax_url,
				data: { action: 'orders_filter_city_sort',  sortbystates : states , sortbycity : sortbycity },
				type: 'post',
				success: function(result) {
					$('#citysort').html('');
					$('#citysort').html(result);
					$('#citysort').removeAttr('disabled');
					$('#departmentsort').removeAttr('disabled');
				},
				error:function (result){
				   console.warn(result);
				}
			});
        }

        // Filter Department
        function getDepartment(states,city){
            $('#departmentsort').attr('disabled','disabled');
        	$.ajax({
				url:wp_ajax.ajax_url,
				data: { action: 'orders_filter_department_sort',  sortbystates : states , sortbycity : city  },
				type: 'post',
				success: function(result) {
					$('#departmentsort').html('');
					$('#departmentsort').html(result);
					$('#departmentsort').removeAttr('disabled');
				},
				error:function (result){
				   console.warn(result);
				}
			});
        }

		// Search Function
		$(".careers-search").click(function(e){

			var search_keyword = jQuery('#autocomplete').val();
      		var sortbyStates = $('.state-sort').find(":selected").data('sortbystates');
      		var sortbycity = $('.city-sort').find(":selected").data('sortbycity');
      		var sortbydepartment = $('.department-sort').find(':selected').data('sortbydepartment');
      		var worklocation = $('.worklocation').is(':checked');

			$.ajax({
				url:wp_ajax.ajax_url,
				data: { action: 'orders_filter_sort',  search_keyword : search_keyword, sortbystates : sortbyStates , sortbycity : sortbycity , sortbydepartment : sortbydepartment, worklocation: worklocation },
				type: 'post',
				success: function(result) {
					$('.jobs-section').html(result);
				},
				error:function (result){
				   console.warn(result);
				}
			});
		});
	});
})(jQuery);

/* Hide Sibling menus on mobile menu click 
** Date: 24-03-2023
*/
if (jQuery(window).width() < 1025) {
	jQuery(document).ready(function(){
		jQuery( "ul.level_1 li a.level_1" ).on( "click", function() {
			jQuery(this).parent().siblings().children(".level_2").hide(1000);
			jQuery(this).parent().siblings().removeClass('opened');
		});
	});
}