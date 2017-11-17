// IL Humanities admin - Firebelly 2015
/*jshint latedef:false*/

// Good design for good reason for good namespace
var IHC_admin = (function($) {

  var _uploadFiles,
      _uploadCount = 0,
      _updateTimer;

  function _init() {
    // AJAX CSV import handling
    if ($('body[class*="csv-importer"]').length) {
      if (window.File && window.FileList && window.FileReader) {
        $('#csv-submit').prop('disabled', true);

        // Init drag-and-drop dropzone if supported
        var filedrag = $('#filedrag')[0];
        filedrag.addEventListener('dragover', IHC_admin.dragHover, false);
        filedrag.addEventListener('dragleave', IHC_admin.dragHover, false);
        filedrag.addEventListener('drop', IHC_admin.dragDrop, false);
        filedrag.style.display = 'block';

        // If selecting files using input, update drag-drop zone with number of files
        $('#csv-import').on('change', function() {
          _uploadFiles = $('#csv-import')[0].files;
          _updateFileDrag();
        });

        // Multifile CSV uploads with progress bar
        $('#csv-upload-form').on('submit', function(e) {
          // Avoid standard submit
          e.preventDefault();

          // Hide any previous notices
          $('#csv-upload-form').find('.error,.updated').slideUp();

          // Reset stats
          _uploadCount = 0;

          if (_uploadFiles && _uploadFiles.length > 0) {
            // Submit uploads and process CSV files
            _handleUploadFile();
          }
        });
      }
    }

    // event date/time handlers
    $('#_cmb2_event_start_date').change(function() {
      if ($('#_cmb2_event_end_date').val()==='') {
        $('#_cmb2_event_end_date').val($('#_cmb2_event_start_date').val());
      } else {
        var end = Date.parse($('#_cmb2_event_end_date').val());
        var start = Date.parse($('#_cmb2_event_start_date').val());
        if (end < start) {
          $('#_cmb2_event_end_date').val($('#_cmb2_event_start_date').val());
        }
      }
    });
    $('#_cmb2_event_start_time').change(function() {
      if ($('#_cmb2_event_end_time').val()==='') {
        $('#_cmb2_event_end_time').val($('#_cmb2_event_start_time').val());
      }
    });

    // hack the update from bottom plugin to show it earlier
    $(window).scroll(function(){
      _updateTimer = setTimeout(function() {
        clearTimeout(_updateTimer);
        if($(window).scrollTop() > $('#submitdiv').height()) {
          $('#updatefrombottom').show();
        } else {
          $('#updatefrombottom').hide();
        }
      }, 250);
    });

  }

  function _dragHover(e) {
    e.stopPropagation();
    e.preventDefault();
    e.target.className = (e.type === "dragover" ? "hover" : "");
  }
  function _dragDrop(e) {
    _dragHover(e);
    _uploadFiles = e.target.files || e.dataTransfer.files;
    _updateFileDrag();
  }
  function _updateFileDrag() {
    $('#filedrag').text((_uploadFiles.length) ? _uploadFiles.length + ' file' + (_uploadFiles.length===1 ? '' : 's') + ' to import' : 'or drop files here');
    $('#csv-submit').prop('disabled', (_uploadFiles.length===0));
  }

  function _handleUploadFile() {
    // Disable form elements to avoid interrupting upload
    $('#csv-upload-form input[type=file]').prop('disabled', true);
    $('#csv-submit').prop('disabled', true).val('Uploading');

    // Build FormData object to submit with each file
    var data = new FormData();
    data.append('csv_import[]', _uploadFiles[_uploadCount]);

    // This sets action to trigger AJAX function
    data.append('action', $('#csv-upload-form input[name=action]').val());

    // Show progress bar and set to percentage width
    $('.progress-bar').show().addClass('active');
    $('.progress-done').css('width', Math.floor((_uploadCount+1) / _uploadFiles.length * 100) + '%');
    $.ajax({
        type: 'POST',
        url: ajaxurl,
        data: data,
        contentType: false,
        cache: false,
        processData: false,
        success: function(data) {
          _uploadCount++;
          // If error, show and stop uploading
          if (typeof data === 'string' || typeof data.error !== 'undefined') {
            var err_txt = (typeof data === 'string') ? 'Error: '+data : data.error[0];
            $('<div class="error"><p>' + err_txt + '</p></div>').insertBefore('#csv-upload-form fieldset');
            $('.progress-bar').fadeOut('slow').removeClass('active');
            _resetCSVUploadForm();
          } else if (_uploadCount < _uploadFiles.length) {
            // Otherwise, move onto processing next file
            _handleUploadFile();
          } else {
            // If we're all done, hide progress bar, show stats, and reset the form
            $('.progress-bar').fadeOut('slow').removeClass('active');
            $('<div class="updated">Success: ' + data.notice.join(' and ') + ' in ' + data.stats.exec_time + ' seconds.</div>').insertBefore('#csv-upload-form fieldset');
            _resetCSVUploadForm();
          }
        },
    });
  }

  // Resets form after uploads
  function _resetCSVUploadForm() {
    $('#csv-upload-form input[type=file]').prop('disabled', false).val('');
    $('#csv-submit').prop('disabled', false).val('Import');
    _uploadFiles = [];
    _updateFileDrag();
  }

  // public functions
  return {
    init: _init,
    dragHover: function(e) { _dragHover(e); },
    dragDrop: _dragDrop
  };

})(jQuery);

// Fire up the mothership
jQuery(document).ready(IHC_admin.init);
