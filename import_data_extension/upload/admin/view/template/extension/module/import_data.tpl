<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a>
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
    <?php if ($error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <?php if ($success) { ?>
    <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-upload"></i> <?php echo $text_upload_step; ?></h3>
      </div>
      <div class="panel-body">
        <!-- Wizard Progress -->
        <div class="wizard-progress">
          <ul class="wizard-steps">
            <li class="active">
              <span class="step-number">1</span>
              <span class="step-title"><?php echo $text_step_upload; ?></span>
            </li>
            <li>
              <span class="step-number">2</span>
              <span class="step-title"><?php echo $text_step_mapping; ?></span>
            </li>
            <li>
              <span class="step-number">3</span>
              <span class="step-title"><?php echo $text_step_settings; ?></span>
            </li>
            <li>
              <span class="step-number">4</span>
              <span class="step-title"><?php echo $text_step_import; ?></span>
            </li>
          </ul>
        </div>

        <form id="form-import" class="form-horizontal">
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-entity-type"><?php echo $entry_entity_type; ?></label>
            <div class="col-sm-10">
              <select name="entity_type" id="input-entity-type" class="form-control">
                <option value=""><?php echo $text_select; ?></option>
                <?php foreach ($entity_types as $key => $value) { ?>
                <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                <?php } ?>
              </select>
            </div>
          </div>

          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-file"><?php echo $entry_file; ?></label>
            <div class="col-sm-10">
              <input type="file" name="import_file" id="input-file" class="form-control" accept=".csv,.txt,.xlsx,.xls" />
              <span class="help-block"><?php echo $help_file; ?></span>
            </div>
          </div>

          <div class="form-group">
            <div class="col-sm-10 col-sm-offset-2">
              <button type="button" id="button-upload" class="btn btn-primary">
                <i class="fa fa-arrow-right"></i> <?php echo $button_continue; ?>
              </button>
            </div>
          </div>
        </form>

        <!-- Recent Imports -->
        <?php if ($recent_imports) { ?>
        <hr>
        <h4><?php echo $text_recent_imports; ?></h4>
        <div class="table-responsive">
          <table class="table table-bordered table-hover">
            <thead>
              <tr>
                <th><?php echo $column_date; ?></th>
                <th><?php echo $column_entity; ?></th>
                <th><?php echo $column_filename; ?></th>
                <th><?php echo $column_records; ?></th>
                <th><?php echo $column_status; ?></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($recent_imports as $import) { ?>
              <tr>
                <td><?php echo $import['date_started']; ?></td>
                <td><?php echo $import['entity_type']; ?></td>
                <td><?php echo $import['filename']; ?></td>
                <td>
                  <span class="text-success"><?php echo $import['records_inserted']; ?> inserted</span> / 
                  <span class="text-info"><?php echo $import['records_updated']; ?> updated</span> / 
                  <span class="text-danger"><?php echo $import['records_errors']; ?> errors</span>
                </td>
                <td>
                  <?php if ($import['status'] == 'completed') { ?>
                  <span class="label label-success"><?php echo $import['status']; ?></span>
                  <?php } elseif ($import['status'] == 'failed') { ?>
                  <span class="label label-danger"><?php echo $import['status']; ?></span>
                  <?php } else { ?>
                  <span class="label label-warning"><?php echo $import['status']; ?></span>
                  <?php } ?>
                </td>
              </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
        <?php } ?>
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
.wizard-steps li.active:after {
  background: #5cb85c;
}
.wizard-steps li.active .step-number {
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
</style>

<script type="text/javascript">
$('#button-upload').on('click', function() {
    var entity_type = $('#input-entity-type').val();
    var file = $('#input-file')[0].files[0];

    if (!entity_type) {
        alert('<?php echo $error_entity_type; ?>');
        return;
    }

    if (!file) {
        alert('<?php echo $error_upload; ?>');
        return;
    }

    var formData = new FormData();
    formData.append('import_file', file);
    formData.append('entity_type', entity_type);

    $.ajax({
        url: '<?php echo html_entity_decode($action, ENT_QUOTES, 'UTF-8'); ?>',
        type: 'POST',
        data: formData,
        dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        beforeSend: function() {
            $('#button-upload').button('loading');
        },
        complete: function() {
            $('#button-upload').button('reset');
        },
        success: function(json) {
            if (json['error']) {
                alert(json['error']);
            }

            if (json['success']) {
                window.location = json['redirect'].replace(/&amp;/g, '&');
            }
        },
        error: function(xhr, ajaxOptions, thrownError) {
            alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
        }
    });
});
</script>

<?php echo $footer; ?>
