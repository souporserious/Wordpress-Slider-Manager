jQuery(document).ready(function($){

    // Cache & Save The Persistent Page Element
    var el = $('#oe-slider-admin'),
    	sort = function () {
	    	el.find('ul.ui-sortable li').each( function(index) {
	    		$(this).find('.order').val(index+1);
			});
	    };
   
    // Delete Slide Button
    el.on('click', '.delete-slide', function(e) {
    
        e.preventDefault();
        
        if($('#manager_form_wrap li.slide').size() == 1) {
			
			alert('Sorry dude, you need at least one slide');	
		
		} else {
		
			var speed = 300;
		
			$(this).parent().parent().parent('.slide').animate({
				opacity: 0
			}, speed, function() {
				$(this).slideUp(speed, function() {
					$(this).remove();
					sort();
				});
			});	
		}
		
		return false;
    
    });
    
    // Add Slide Button
    el.on('click', '.add-slide', function (e) {
        
        e.preventDefault();
        
        var template = $('#slide-template').html(),
       		id = parseInt( $('ul.ui-sortable li').last().find('.order').val() ) || 0,
	   		newSlide = template.replace(/%id%/g, id).replace(/%id1%/g, id + 1);
        
        $(newSlide).hide().insertBefore($('ul.ui-sortable li').first()).fadeIn();
        
        sort();
        
    });
        
    // Add Image Button	
	el.on('click', '.add-image', function(e) {
	
		e.preventDefault();
		
		// store input for later use
		var input = $(this).parent().parent().find('.image_id'),
			imgPreview = $(this).parent().find('img'),
			file_frame;

		// Create the media frame.
		file_frame = wp.media.frames.file_frame = wp.media({
			title: $(this).data('uploader_title'),
			button: {
				text: $(this).data('uploader_button_text'),
			},
			multiple: false  // Set to true to allow multiple files to be selected
		});
		
		// When an image is selected, run a callback.
		file_frame.on('select', function() {
			// We set multiple to false so only get one image from the uploader
			attachment = file_frame.state().get('selection').first().toJSON();
		
			// Store ID in hidden input
			input.val(attachment.id);
			
			// Show a preview of the image we grabbed
			$(imgPreview).attr('src', attachment.url);
		});
		
		// Finally, open the modal
		file_frame.open();
	
	});
    
    // jQuery UI Sortable
    el.find('ul.ui-sortable').sortable({
    	placeholder: 'slide_holder',
    	handle: '.handle',
        update: function(event, ui) {
        	sort();
        }
    });
});