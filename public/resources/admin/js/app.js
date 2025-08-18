// Scroll to top
function pageTop() {
    window.scrollTo({
        top: 50,
        behavior: 'smooth',
    });
}

// Scroll to middle
function pageMiddle() {
    const pos = document.documentElement.scrollHeight * 0.4;
    window.scrollTo({
        top: pos,
        behavior: 'smooth',
    });
}

//Scroll to bottom
function pageBottom() {
    window.scrollTo({
        top: document.body.scrollHeight,
        behavior: 'smooth',
    });
}

// Success
function success(msg) {
  toastr.success(msg ?? 'Success!');
}

// Error
function error(msg) {
  toastr.error(msg ?? 'Something went wrong!');
}

// Preview image
function previewImage(inputSelector, imgSelector) {
  $(inputSelector).change(function (e) {
    if (this.files && this.files[0]) {
      var reader = new FileReader();
      reader.onload = function (e) {
        $(imgSelector).attr('src', e.target.result);
      };
      reader.readAsDataURL(this.files[0]);
    }
  });
}

$(document).ready(function () {
  // Summernote
  $('.summernote').summernote({
    height: 200,
    resize: true,
    fontNamesIgnoreCheck: ['Titillium Web'],
    fontNames: $.summernote.options.fontNames.concat(['Titillium Web'])
  });

  //Selct2
  $('.select2').select2({
      width: '100%'
  });
});