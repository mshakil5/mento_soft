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
  $('.modal-backdrop').remove();
});

$('.modal').on('hidden.bs.modal', function () {
    $('.modal-backdrop').remove();
});

// Global remove button handler
$(document).on('click', '.remove-file', function() {
    const btn = $(this);
    const filename = btn.data('filename');
    const path = btn.data('path');
    const model = btn.data('model');
    const id = btn.data('id');
    const col = btn.data('col');

    if (!filename || !path || !model || !id || !col) return;

    if(!confirm('Are you sure?')) return;

    $.ajax({
        url: '/admin/remove-file',
        type: 'POST',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            filename: filename,
            path: path,
            model: model,
            id: id,
            col: col
        },
        success: function(res) {
            btn.prev('img').remove();
            btn.remove();  
            success(res.message);
        },
        error: function(xhr) {
            console.error(xhr.responseText);
            pageTop();
            if (xhr.responseJSON && xhr.responseJSON.errors)
                error(Object.values(xhr.responseJSON.errors)[0][0]);
            else
                error();
        }
    });
});

function openTaskModal(projectId = null) {
    let $select = $('#projectSelect');

    if(projectId){
        $select.val(projectId).trigger('change').prop('disabled', true);
    } else {
        $select.val('').trigger('change').prop('disabled', false);
    }

    $('#tasksModal').modal('show');
}

$(document).on('shown.bs.modal', '.modal', function () {
    $(this).find('.modal-select2').each(function () {
        if (!$(this).hasClass('select2-hidden-accessible')) {
            $(this).select2({
                width: '100%',
                dropdownParent: $(this).closest('.modal')
            });
        }
    });
});

$('#tasksModal form').submit(function(e) {
    e.preventDefault();

    const $form = $(this);
    const projectId = $('#projectSelect').val();
    const employeeSelect = $('#employeeSelect').val();

    if(!projectId) {
        error('Please select a project!');
        return;
    }

    if(!employeeSelect) {
        error('Please select an employee!');
        return;
    }

    $.ajax({
        url: `/admin/client-projects/${projectId}/tasks`,
        type: 'POST',
        data: $form.serialize(),
        success: function(res) {
          success(res.message);
          pageTop();
          clearTaskModal();
          setTimeout(function() {
              location.reload();
          }, 1000);
        },
        error: function(xhr) {
          console.error(xhr.responseText);
          pageTop();
          if (xhr.responseJSON && xhr.responseJSON.errors)
            error(Object.values(xhr.responseJSON.errors)[0][0]);
          else
            error();
        }
    });
});

$('#tasksModal').on('hidden.bs.modal', function () {
    clearTaskModal();
});

function clearTaskModal() {
    $('#tasksModal').modal('hide');
    $('#tasksModal form')[0].reset();
    $('.summernote').summernote('reset');
    $('#tasksModal select').val('').trigger('change');
}

// Accounting hide
$(document).ready(function(){
    var $btn = $('#toggleAccounting');
    var $wrapper = $('#accountingWrapper');
    var isVisible = localStorage.getItem('accountingMenuVisible') === 'true';

    $wrapper.css('display', isVisible ? 'block' : 'none');
    $btn.text(isVisible ? 'Hide Accounting' : 'Show Accounting')
        .toggleClass('btn-info', !isVisible)
        .toggleClass('btn-warning', isVisible);

    $btn.on('click', function(){
        $wrapper.toggle();
        var visible = $wrapper.is(':visible');
        $btn.text(visible ? 'Hide Accounting' : 'Show Accounting')
            .toggleClass('btn-info', !visible)
            .toggleClass('btn-warning', visible);
        localStorage.setItem('accountingMenuVisible', visible);
    });
});

$(document).ready(function(){
    $(document).on('submit', '.ajaxForm', function(e){
        e.preventDefault();

        var $form = $(this);
        var url = $form.attr('action');
        var formData = new FormData(this);

        $.ajax({
            url: url,
            method: $form.attr('method') || 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(res){
                $form.closest('.modal').modal('hide');
                $form[0].reset();
                success(res.message);
            },
            error: function(xhr) {
                console.error(xhr.responseText);
                pageTop();
                if (xhr.responseJSON && xhr.responseJSON.errors)
                    error(Object.values(xhr.responseJSON.errors)[0][0]);
                else
                    error();
            }
        });
    });
});