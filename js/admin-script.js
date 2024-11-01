(function($){
  $(document).ready(function(){

    var mediaLibraryFrame;

    $("#wads-select-image").on('click', function(event){
      event.preventDefault();

      if (mediaLibraryFrame) {
        mediaLibraryFrame.open();
        return;
      }

      mediaLibraryFrame = wp.media({
        title: 'Select or Upload Image',
        button: {
          text: 'Use Image'
        },
        multiple: false
      });

      mediaLibraryFrame.on('select', function(){
        var attachment = mediaLibraryFrame.state().get('selection').first().toJSON();

        if (attachment.type == 'image') {
          $("#wads_attachment_id").val(attachment.id);

          $("#wads-image-width").val(attachment.width);
          $("#wads-image-height").val(attachment.height);
          $("#wads-image-settings").show();
          $("#wads-clear-image").show();

          $("#wads-placeholder-image").css('background-image', 'url(' + attachment.url + ')');
        } else {
          alert('Error: Selected media must be an image.');
        }
      });

      mediaLibraryFrame.open();

    });

    $("#wads-clear-image-button").on('click', function(event){
      event.preventDefault();
      $("#wads_attachment_id").val('');
      $("#wads-image-settings").hide();
      $("#wads-image-width").val('');
      $("#wads-image-height").val('');
      $("#wads-clear-image").hide();
      $("#wads-placeholder-image").css('background-image', 'none');
    });

  });
})(jQuery);
