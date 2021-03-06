jQuery(function($){
	$('.button-remove').on('click', function () {
		$(this).parents('.box-image').remove();
	});
});


window.aeria_setup_media_gallery_fields = function(){
	var file_frame;

	jQuery('.aeria_upload_media_gallery_button').off('click').on('click', function( event ){
		var $me = jQuery(this), type = $me.attr('data-type'), num_class = $me.attr('data-num');

	    target = $me.data('target').replace('##','#'), // FIX!
	    $target = jQuery(target),

	    event.preventDefault();

	    file_frame = wp.media.frames.file_frame = wp.media({
	    	title: jQuery( this ).data( 'uploader_title' ),
	    	button: {
	    		text: jQuery( this ).data( 'uploader_button_text' )
	    	},
	    	multiple: (num_class=='multi')
	    });

	    file_frame.on( 'select', function() {
	    	var attachments = file_frame.state().get('selection').toJSON();


	    	if(type=='list') {

	    		$target_image = jQuery(target+'_image');
	    		if($target_image.length) $target_image.attr('src',attachment.url);
	    	}
	    	if(type=='preview'){
	    		var $container = jQuery('.'+$me.attr('data-target'));

	    		 jQuery.each(attachments, function(i,attachment) {

					var box_count = ($container.children('.box-image').length)-1;
					var $box_to_copy = $container.find('.box-image').eq(0);

					if($box_to_copy.hasClass('aeria_upload_media_gallery_button')){
						//not found box to clone

						if(num_class =='multi'){
							$container.prepend('<div class="box-image item_0" style="display: none;"><div class="image multi file" style="background-image:url();"></div><div class="box-controls"><a class="button button-remove"><i class="glyphicon glyphicon-trash"></i></a></div><input type="hidden" name="gallery[]" value=""></div>');
						}else{
							$container.prepend('<div class="box-image item_0" style="display:none;"><div class="image single file" style="background-image:url();"></div><div class="box-controls"><a class="button button-remove"><i class="glyphicon glyphicon-trash"></i></a></div><input type="hidden" name="image[]" value=""></div>');
						}
						$box_to_copy = $container.find('.box-image').eq(0);
					}

					var $box_copy = $box_to_copy.clone().attr('style','display:inline-block;');

	    		 	if(attachment.type=='application') {
		    			$box_copy.find('.image').css('background-image','none').addClass('file').html('<h4>'+attachment.title+'</h4>');
		    		}else{
		    			$box_copy.find('.image').css('background-image','url("'+attachment.url+'")').removeClass('file').empty();
		    		}

		    		$box_copy.find('input').val(attachment.url);
		    		$box_copy.find('.button-edit').attr('href', 'post.php?post='+attachment.id+'&action=edit');
		    		jQuery($container).find('.box-image').eq(box_count).before($box_copy);
		    		if($box_to_copy.find('input').val()=='')$box_to_copy.remove();

		    		if(num_class=='single') {
		    			$box_to_copy.remove();
		    		}

		    		jQuery('.button-remove').on('click', function () {
		    			jQuery(this).parents('.box-image').remove();
		    		});
	    		 });

	    	}


	    });

		file_frame.open();
	});
};

window.aeria_setup_media_gallery_fields();
