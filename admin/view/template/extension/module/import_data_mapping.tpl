<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-mapping" class="btn btn-primary btn-lg"><i class="fa fa-arrow-right"></i> <?php echo $button_continue; ?></button>
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
        <h3 class="panel-title"><i class="fa fa-exchange"></i> <?php echo $text_mapping_step; ?></h3>
      </div>
      <div class="panel-body">
        <!-- Wizard Progress -->
        <div class="wizard-progress">
          <ul class="wizard-steps">
            <li class="completed">
              <span class="step-number">1</span>
              <span class="step-title"><?php echo $text_step_upload; ?></span>
            </li>
            <li class="active">
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

        <div class="alert alert-info">
          <i class="fa fa-info-circle"></i> <?php echo $text_mapping_help; ?>
        </div>

        <form action="<?php echo $action; ?>" method="post" id="form-mapping" class="form-horizontal">
          <div class="well">
            <h4><?php echo $text_entity_type; ?>: <strong><?php echo ucfirst($entity_type); ?></strong></h4>
          </div>

          <!-- Column Mapping Table -->
          <div class="table-responsive">
            <table class="table table-bordered table-hover">
              <thead>
                <tr>
                  <th width="5%">#</th>
                  <th width="25%"><?php echo $column_file_header; ?></th>
                  <th width="25%"><?php echo $column_db_field; ?></th>
                  <th width="45%"><?php echo $column_sample_data; ?></th>
                </tr>
              </thead>
              <tbody>
                <?php $i = 0; foreach ($file_headers as $index => $header) { ?>
                <tr>
                  <td><?php echo $index + 1; ?></td>
                  <td><strong><?php echo htmlspecialchars($header); ?></strong></td>
                  <td>
                    <select name="mapping[<?php echo $index; ?>]" class="form-control">
                      <option value=""><?php echo $text_ignore; ?></option>
                      <?php foreach ($db_fields as $field_key => $field_name) { ?>
                      <option value="<?php echo $field_key; ?>" <?php echo (strtolower($header) == strtolower($field_key) || strtolower($header) == strtolower(str_replace('_', ' ', $field_key))) ? 'selected' : ''; ?>>
                        <?php echo $field_name; ?>
                      </option>
                      <?php } ?>
                    </select>
                  </td>
                  <td>
                    <div class="sample-data">
                      <?php if (isset($sample_data[0][$index])) { ?>
                      <div class="text-muted small">
                        <?php 
                        $samples = array();
                        foreach ($sample_data as $row) {
                          if (isset($row[$index]) && !empty($row[$index])) {
                            $samples[] = htmlspecialchars(substr($row[$index], 0, 50));
                            if (count($samples) >= 3) break;
                          }
                        }
                        echo implode('<br>', $samples);
                        ?>
                      </div>
                      <?php } ?>
                    </div>
                  </td>
                </tr>
                <?php $i++; } ?>
              </tbody>
            </table>
          </div>

          <!-- Auto-Map Button -->
          <div class="form-group">
            <div class="col-sm-12">
              <button type="button" id="button-auto-map" class="btn btn-info">
                <i class="fa fa-magic"></i> <?php echo $button_auto_map; ?>
              </button>
              <button type="button" id="button-clear-map" class="btn btn-warning">
                <i class="fa fa-eraser"></i> <?php echo $button_clear_map; ?>
              </button>
              <div class="pull-right">
                <button type="submit" form="form-mapping" class="btn btn-primary btn-lg">
                  <i class="fa fa-arrow-right"></i> <?php echo $button_continue; ?>
                </button>
              </div>
            </div>
          </div>
        </form>

        <!-- Sample Data Preview -->
        <hr>
        <h4><?php echo $text_sample_preview; ?></h4>
        <div class="table-responsive">
          <table class="table table-bordered table-sm small">
            <thead>
              <tr>
                <?php foreach ($file_headers as $header) { ?>
                <th><?php echo htmlspecialchars($header); ?></th>
                <?php } ?>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($sample_data as $row) { ?>
              <tr>
                <?php foreach ($row as $cell) { ?>
                <td><?php echo htmlspecialchars(substr($cell, 0, 50)); ?></td>
                <?php } ?>
              </tr>
              <?php } ?>
            </tbody>
          </table>
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
.sample-data {
  max-height: 60px;
  overflow: hidden;
}
</style>

<script type="text/javascript">
// Auto-map columns based on name similarity
$('#button-auto-map').on('click', function() {
    $('select[name^="mapping"]').each(function() {
        var $select = $(this);
        var headerText = $select.closest('tr').find('td:eq(1) strong').text().toLowerCase();
        
        $select.find('option').each(function() {
            var optionValue = $(this).val();
            var optionText = $(this).text().toLowerCase();
            
            if (optionValue && (
                headerText === optionValue.toLowerCase() || 
                headerText === optionValue.replace(/_/g, ' ').toLowerCase() ||
                headerText.replace(/\s+/g, '_') === optionValue.toLowerCase()
            )) {
                $select.val(optionValue);
                return false;
            }
        });
    });
    
    alert('<?php echo $text_auto_map_complete; ?>');
});

// Clear all mappings
$('#button-clear-map').on('click', function() {
    $('select[name^="mapping"]').val('');
});
</script>

<?php echo $footer; ?>
