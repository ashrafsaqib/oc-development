<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-translations" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
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
    <div class="alert alert-danger alert-dismissible"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <?php if ($success) { ?>
    <div class="alert alert-success alert-dismissible"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-language"></i> <?php echo $text_edit_translations; ?></h3>
      </div>
      <div class="panel-body">
        <form action="<?php echo $action_translations; ?>" method="post" enctype="multipart/form-data" id="form-translations" class="form-horizontal">
          <ul class="nav nav-tabs" role="tablist">
            <?php $tab_index = 0; ?>
            <?php foreach ($languages as $language) { ?>
            <li role="presentation" class="<?php echo ($tab_index == 0) ? 'active' : ''; ?>">
              <a href="#tab-<?php echo $language['code']; ?>" aria-controls="tab-<?php echo $language['code']; ?>" role="tab" data-toggle="tab">
                <img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['name']; ?>" /> <?php echo $language['name']; ?>
              </a>
            </li>
            <?php $tab_index++; ?>
            <?php } ?>
          </ul>
          <div class="tab-content">
            <?php $tab_index = 0; ?>
            <?php foreach ($languages as $language) { ?>
            <div role="tabpanel" class="tab-pane <?php echo ($tab_index == 0) ? 'active' : ''; ?>" id="tab-<?php echo $language['code']; ?>">
              <table class="table table-striped table-bordered table-hover">
                <thead>
                  <tr>
                    <td class="text-left" style="width: 30%;"><?php echo $entry_translation_key; ?></td>
                    <td class="text-left"><?php echo $entry_translation_value; ?></td>
                  </tr>
                </thead>
                <tbody>
                  <?php if (isset($translations[$language['code']])) { ?>
                    <?php foreach ($translations[$language['code']] as $key => $value) { ?>
                    <tr>
                      <td class="text-left"><strong><?php echo $key; ?></strong></td>
                      <td class="text-left"><input type="text" name="translations[<?php echo $language['code']; ?>][<?php echo $key; ?>]" value="<?php echo htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); ?>" class="form-control" /></td>
                    </tr>
                    <?php } ?>
                  <?php } ?>
                </tbody>
              </table>
            </div>
            <?php $tab_index++; ?>
            <?php } ?>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript"><!--
$(document).ready(function() {
    $('.nav-tabs a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        // Tab switching is handled by Bootstrap
    });
});
//--></script>
<?php echo $footer; ?>
