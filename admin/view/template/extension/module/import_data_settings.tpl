<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="button" id="button-import" class="btn btn-success btn-lg"><i class="fa fa-check"></i> <?php echo $button_import; ?></button>
        <a href="<?php echo $back; ?>" class="btn btn-default"><i class="fa fa-reply"></i> <?php echo $button_back; ?></a>
      </div>
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-cog"></i> <?php echo $text_settings_step; ?></h3>
      </div>
      <div class="panel-body">
        <!-- Wizard Progress -->
        <div class="wizard-progress">
          <ul class="wizard-steps">
            <li class="completed">
              <span class="step-number">1</span>
              <span class="step-title"><?php echo $text_step_upload; ?></span>
            </li>
            <li class="completed">
              <span class="step-number">2</span>
              <span class="step-title"><?php echo $text_step_mapping; ?></span>
            </li>
            <li class="active">
              <span class="step-number">3</span>
              <span class="step-title"><?php echo $text_step_settings; ?></span>
            </li>
            <li>
              <span class="step-number">4</span>
              <span class="step-title"><?php echo $text_step_import; ?></span>
            </li>
          </ul>
        </div>

        <form id="form-settings" class="form-horizontal">
          <div class="well">
            <h4><?php echo $text_entity_type; ?>: <strong><?php echo ucfirst($entity_type); ?></strong></h4>
          </div>

          <!-- Import Mode -->
          <fieldset>
            <legend><?php echo $text_import_mode; ?></legend>
            
            <div class="form-group">
              <label class="col-sm-3 control-label"><?php echo $entry_import_mode; ?></label>
              <div class="col-sm-9">
                <div class="radio">
                  <label>
                    <input type="radio" name="import_mode" value="insert" checked>
                    <strong><?php echo $text_mode_insert; ?></strong>
                    <span class="help-block"><?php echo $help_mode_insert; ?></span>
                  </label>
                </div>
                <div class="radio">
                  <label>
                    <input type="radio" name="import_mode" value="update">
                    <strong><?php echo $text_mode_update; ?></strong>
                    <span class="help-block"><?php echo $help_mode_update; ?></span>
                  </label>
                </div>
                <div class="radio">
                  <label>
                    <input type="radio" name="import_mode" value="upsert">
                    <strong><?php echo $text_mode_upsert; ?></strong>
                    <span class="help-block"><?php echo $help_mode_upsert; ?></span>
                  </label>
                </div>
              </div>
            </div>
          </fieldset>

          <hr>

          <!-- Advanced Options -->
          <fieldset>
            <legend><?php echo $text_advanced_options; ?></legend>

            <div class="form-group">
              <label class="col-sm-3 control-label" for="input-skip-first"><?php echo $entry_skip_first_row; ?></label>
              <div class="col-sm-9">
                <div class="checkbox">
                  <label>
                    <input type="checkbox" name="skip_first_row" id="input-skip-first" value="1" checked>
                    <?php echo $help_skip_first_row; ?>
                  </label>
                </div>
              </div>
            </div>

            <div class="form-group">
              <label class="col-sm-3 control-label" for="input-delete-existing"><?php echo $entry_delete_existing; ?></label>
              <div class="col-sm-9">
                <div class="checkbox">
                  <label>
                    <input type="checkbox" name="delete_existing" id="input-delete-existing" value="1">
                    <span class="text-danger"><?php echo $help_delete_existing; ?></span>
                  </label>
                </div>
              </div>
            </div>

            <div class="form-group">
              <label class="col-sm-3 control-label" for="input-update-existing"><?php echo $entry_update_existing; ?></label>
              <div class="col-sm-9">
                <div class="checkbox">
                  <label>
                    <input type="checkbox" name="update_existing" id="input-update-existing" value="1" checked>
                    <?php echo $help_update_existing; ?>
                  </label>
                </div>
              </div>
            </div>

            <div class="form-group">
              <label class="col-sm-3 control-label" for="input-ignore-errors"><?php echo $entry_ignore_errors; ?></label>
              <div class="col-sm-9">
                <div class="checkbox">
                  <label>
                    <input type="checkbox" name="ignore_errors" id="input-ignore-errors" value="1" checked>
                    <?php echo $help_ignore_errors; ?>
                  </label>
                </div>
              </div>
            </div>

            <div class="form-group">
              <label class="col-sm-3 control-label" for="input-batch-size"><?php echo $entry_batch_size; ?></label>
              <div class="col-sm-9">
                <input type="number" name="batch_size" id="input-batch-size" value="100" class="form-control" min="1" max="1000">
                <span class="help-block"><?php echo $help_batch_size; ?></span>
              </div>
            </div>

            <div class="form-group">
              <label class="col-sm-3 control-label" for="input-debug-mode"><?php echo $entry_debug_mode; ?></label>
              <div class="col-sm-9">
                <div class="checkbox">
                  <label>
                    <input type="checkbox" name="debug_mode" id="input-debug-mode" value="1">
                    <?php echo $help_debug_mode; ?>
                  </label>
                </div>
                <div style="margin-top: 10px;">
                  <a href="<?php echo $view_log; ?>" target="_blank" class="btn btn-info btn-sm">
                    <i class="fa fa-file-text-o"></i> <?php echo $button_view_log; ?>
                  </a>
                  <button type="button" id="button-clear-log" class="btn btn-warning btn-sm">
                    <i class="fa fa-trash-o"></i> <?php echo $button_clear_log; ?>
                  </button>
                </div>
              </div>
            </div>
          </fieldset>

          <hr>

          <!-- Import Summary -->
          <fieldset>
            <legend><?php echo $text_import_summary; ?></legend>
            <div class="alert alert-warning">
              <h4><i class="fa fa-exclamation-triangle"></i> <?php echo $text_warning; ?></h4>
              <p><?php echo $text_import_warning; ?></p>
            </div>
            
            <!-- Start Import Button -->
            <div class="text-center" style="margin-top: 20px;">
              <button type="button" id="button-import-bottom" class="btn btn-success btn-lg" style="padding: 15px 40px; font-size: 18px;">
                <i class="fa fa-check-circle"></i> <?php echo $button_import; ?>
              </button>
            </div>
          </fieldset>
        </form>

        <!-- Progress Bar (hidden initially) -->
        <div id="import-progress" style="display: none;">
          <hr>
          <h4><?php echo $text_importing; ?></h4>
          <div class="progress">
            <div class="progress-bar progress-bar-striped active" role="progressbar" style="width: 0%">
              <span class="sr-only">0%</span>
            </div>
          </div>
          <div id="import-status" class="text-center"></div>
        </div>

        <!-- Import Results (hidden initially) -->
        <div id="import-results" style="display: none;">
          <hr>
          <h4><?php echo $text_import_complete; ?></h4>
          <div class="row">
            <div class="col-md-3">
              <div class="panel panel-success">
                <div class="panel-heading text-center">
                  <h3 class="panel-title"><?php echo $text_inserted; ?></h3>
                </div>
                <div class="panel-body text-center">
                  <h2 id="result-inserted">0</h2>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="panel panel-info">
                <div class="panel-heading text-center">
                  <h3 class="panel-title"><?php echo $text_updated; ?></h3>
                </div>
                <div class="panel-body text-center">
                  <h2 id="result-updated">0</h2>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="panel panel-danger">
                <div class="panel-heading text-center">
                  <h3 class="panel-title"><?php echo $text_errors; ?></h3>
                </div>
                <div class="panel-body text-center">
                  <h2 id="result-errors">0</h2>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="panel panel-default">
                <div class="panel-heading text-center">
                  <h3 class="panel-title"><?php echo $text_total; ?></h3>
                </div>
                <div class="panel-body text-center">
                  <h2 id="result-total">0</h2>
                </div>
              </div>
            </div>
          </div>
          <div class="text-center">
            <a href="<?php echo $home; ?>" class="btn btn-primary">
              <i class="fa fa-refresh"></i> <?php echo $button_new_import; ?>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
.wizard-progress {
  margin-bottom: 30px;
}
.wizard-steps {
  display: flex;
  justify-content: space-between;
  list-style: none;
  padding: 0;
  margin: 0;
}
.wizard-steps li {
  flex: 1;
  text-align: center;
  position: relative;
  padding: 10px;
}
.wizard-steps li:not(:last-child):after {
  content: '';
  position: absolute;
  top: 25px;
  right: -50%;
  width: 100%;
  height: 2px;
  background: #ddd;
  z-index: -1;
}
.wizard-steps li.active:after,
.wizard-steps li.completed:after {
  background: #5cb85c;
}
.wizard-steps li.active .step-number,
.wizard-steps li.completed .step-number {
  background: #5cb85c;
  color: white;
}
.step-number {
  display: inline-block;
  width: 40px;
  height: 40px;
  line-height: 40px;
  border-radius: 50%;
  background: #ddd;
  margin-bottom: 5px;
}
.step-title {
  display: block;
  font-size: 12px;
}
.help-block {
  margin: 0;
  font-size: 12px;
  color: #666;
}
</style>

<script type="text/javascript">
// Handle both import buttons
$('#button-import, #button-import-bottom').on('click', function() {
    if ($('#input-delete-existing').is(':checked')) {
        if (!confirm('<?php echo $text_delete_confirm; ?>')) {
            return;
        }
    }

    var formData = $('#form-settings').serialize();

    $.ajax({
        url: '<?php echo html_entity_decode($action, ENT_QUOTES, 'UTF-8'); ?>',
        type: 'POST',
        data: formData,
        dataType: 'json',
        beforeSend: function() {
            $('#button-import, #button-import-bottom').prop('disabled', true).hide();
            $('#import-progress').show();
            $('.progress-bar').css('width', '50%').find('.sr-only').text('50%');
            $('#import-status').html('<i class="fa fa-spinner fa-spin"></i> <?php echo $text_processing; ?>');
        },
        complete: function() {
            $('.progress-bar').css('width', '100%').find('.sr-only').text('100%');
        },
        success: function(json) {
            if (json['error']) {
                alert(json['error']);
                $('#import-progress').hide();
            }

            if (json['success']) {
                $('#import-progress').hide();
                $('#import-results').show();
                
                // Update statistics
                $('#result-inserted').text(json['statistics']['inserted']);
                $('#result-updated').text(json['statistics']['updated']);
                $('#result-errors').text(json['statistics']['errors']);
                $('#result-total').text(
                    parseInt(json['statistics']['inserted']) + 
                    parseInt(json['statistics']['updated']) + 
                    parseInt(json['statistics']['errors'])
                );

                // Show success message
                $('html, body').animate({ scrollTop: $('#import-results').offset().top - 100 }, 'slow');
            }
        },
        error: function(xhr, ajaxOptions, thrownError) {
            alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            $('#import-progress').hide();
        }
    });
});

// Disable update_existing when insert mode is selected
$('input[name="import_mode"]').on('change', function() {
    if ($(this).val() === 'insert') {
        $('#input-update-existing').prop('checked', false).prop('disabled', true);
    } else {
        $('#input-update-existing').prop('disabled', false);
    }
});

// Clear debug log button
$('#button-clear-log').on('click', function() {
    if (confirm('Are you sure you want to clear the debug log?')) {
        $.ajax({
            url: '<?php echo html_entity_decode($clear_log, ENT_QUOTES, 'UTF-8'); ?>',
            type: 'GET',
            dataType: 'json',
            success: function(json) {
                if (json['success']) {
                    alert('<?php echo $text_log_cleared; ?>');
                }
            }
        });
    }
});
</script>

<?php echo $footer; ?>
