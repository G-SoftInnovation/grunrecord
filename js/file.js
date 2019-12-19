function readURL(input) {
  if (input.files && input.files[0]) {

    var reader = new FileReader();

    reader.onload = function(e) {
      jQuery('.image-upload-wrap').hide();

      jQuery('.file-upload-image').attr('src', e.target.result);
      jQuery('.file-upload-content').show();

      jQuery('.image-title').html(input.files[0].name);
    };

    reader.readAsDataURL(input.files[0]);

  } else {
    removeUpload();
  }
}

function removeUpload() {
  jQuery('.file-upload-input').replaceWith(jQuery('.file-upload-input').clone());
  jQuery('.file-upload-content').hide();
  jQuery('.image-upload-wrap').show();
}
jQuery('.image-upload-wrap').bind('dragover', function () {
        jQuery('.image-upload-wrap').addClass('image-dropping');
    });
    jQuery('.image-upload-wrap').bind('dragleave', function () {
        jQuery('.image-upload-wrap').removeClass('image-dropping');
});