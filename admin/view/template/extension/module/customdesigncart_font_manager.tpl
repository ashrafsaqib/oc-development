<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-fonts" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
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
        <h3 class="panel-title"><i class="fa fa-font"></i> <?php echo $text_edit_fonts; ?></h3>
      </div>
      <div class="panel-body">
        <form action="<?php echo $action_fonts; ?>" method="post" enctype="multipart/form-data" id="form-fonts" class="form-horizontal">
          <table id="fonts" class="table table-striped table-bordered table-hover">
            <thead>
              <tr>
                <td class="text-left"><?php echo $entry_font_family; ?></td>
                <td class="text-left"><?php echo $entry_font_url; ?></td>
                <td></td>
              </tr>
            </thead>
            <tbody>
              <?php $font_row = 0; ?>
              <?php foreach ($custom_fonts as $font) { ?>
              <tr id="font-row<?php echo $font_row; ?>">
                <td class="text-left"><input type="text" name="fonts[<?php echo $font_row; ?>][name]" value="<?php echo $font['name']; ?>" placeholder="<?php echo $entry_font_family; ?>" class="form-control" /></td>
                <td class="text-left"><input type="text" name="fonts[<?php echo $font_row; ?>][url]" value="<?php echo $font['url']; ?>" placeholder="<?php echo $entry_font_url; ?>" class="form-control" /></td>
                <td class="text-left"><button type="button" onclick="$('#font-row<?php echo $font_row; ?>').remove();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>
              </tr>
              <?php $font_row++; ?>
              <?php } ?>
            </tbody>
            <tfoot>
              <tr>
                <td colspan="2"></td>
                <td class="text-left"><button type="button" onclick="addFont();" data-toggle="tooltip" title="<?php echo $button_add_font; ?>" class="btn btn-primary"><i class="fa fa-plus-circle"></i></button></td>
              </tr>
            </tfoot>
          </table>
        </form>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript"><!--
var font_row = <?php echo $font_row; ?>;

function addFont() {
    html  = '<tr id="font-row' + font_row + '">';
    html += '  <td class="text-left"><input type="text" name="fonts[' + font_row + '][name]" value="" placeholder="<?php echo $entry_font_family; ?>" class="form-control" /></td>';
    html += '  <td class="text-left"><input type="text" name="fonts[' + font_row + '][url]" value="" placeholder="<?php echo $entry_font_url; ?>" class="form-control" /></td>';
    html += '  <td class="text-left"><button type="button" onclick="$(\'#font-row' + font_row + '\').remove();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>';
    html += '</tr>';
    
    $('#fonts tbody').append(html);
    
    font_row++;
}
//--></script>
<?php echo $footer; ?>
