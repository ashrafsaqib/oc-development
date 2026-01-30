<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-images" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
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
        <h3 class="panel-title"><i class="fa fa-image"></i> <?php echo $text_edit_images; ?></h3>
      </div>
      <div class="panel-body">
        <form action="<?php echo $action_images; ?>" method="post" enctype="multipart/form-data" id="form-images">
          <table id="images" class="table table-striped table-bordered table-hover">
            <thead>
              <tr>
                <td class="text-left">Image</td>
                <td></td>
              </tr>
            </thead>
            <tbody>
              <?php $image_row = 0; ?>
              <?php foreach ($images as $image) { ?>
              <tr id="image-row<?php echo $image_row; ?>">
                <td class="text-left">
                  <a href="" id="thumb-image<?php echo $image_row; ?>" data-toggle="image" class="img-thumbnail">
                    <img src="<?php echo $image['thumb']; ?>" alt="" title="" data-placeholder="<?php echo $placeholder; ?>" />
                  </a>
                  <input type="hidden" name="images[]" value="<?php echo $image['path']; ?>" id="input-image<?php echo $image_row; ?>" />
                </td>
                <td class="text-left">
                  <button type="button" onclick="$('#image-row<?php echo $image_row; ?>').remove();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button>
                </td>
              </tr>
              <?php $image_row++; ?>
              <?php } ?>
            </tbody>
            <tfoot>
              <tr>
                <td colspan="1"></td>
                <td class="text-left">
                  <button type="button" onclick="addImage();" data-toggle="tooltip" title="<?php echo $button_add; ?>" class="btn btn-primary"><i class="fa fa-plus-circle"></i></button>
                  <button type="button" data-toggle="modal" data-target="#modal-bulk-add" title="<?php echo $button_bulk_add; ?>" class="btn btn-info"><i class="fa fa-upload"></i> <?php echo $button_bulk_add; ?></button>
                </td>
              </tr>
            </tfoot>
          </table>
        </form>
      </div>
    </div>
  </div>
</div>

<div id="modal-bulk-add" class="modal fade" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><?php echo $text_bulk_add_title; ?></h4>
      </div>
      <div class="modal-body">
        <p><?php echo $text_bulk_add_instructions; ?></p>
        <textarea id="bulk-add-paths" class="form-control" rows="10"></textarea>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" id="button-bulk-add-confirm" class="btn btn-primary">Add Images</button>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript"><!--
var image_row = <?php echo $image_row; ?>;

function addImage(path, thumb) {
    path = path || '';
    thumb = thumb || '<?php echo $placeholder; ?>';

    html  = '<tr id="image-row' + image_row + '">';
    html += '  <td class="text-left">';
    html += '    <a href="" id="thumb-image' + image_row + '" data-toggle="image" class="img-thumbnail"><img src="' + thumb + '" alt="" title="" data-placeholder="<?php echo $placeholder; ?>" /></a>';
    html += '    <input type="hidden" name="images[]" value="' + path + '" id="input-image' + image_row + '" />';
    html += '  </td>';
    html += '  <td class="text-left"><button type="button" onclick="$(\'#image-row' + image_row  + '\').remove();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>';
    html += '</tr>';

    $('#images tbody').append(html);

    image_row++;
}

$('#button-bulk-add-confirm').on('click', function() {
    var paths = $('#bulk-add-paths').val().split(',').filter(function(path) {
        return path.trim() !== '';
    });

    if (paths.length > 0) {
        $.ajax({
            url: 'index.php?route=extension/module/customdesigncart/getThumbnails&token=<?php echo $token; ?>',
            type: 'POST',
            dataType: 'json',
            data: { paths: paths },
            success: function(json) {
                for (var path in json) {
                    if (json.hasOwnProperty(path)) {
                        addImage(path, json[path]);
                    }
                }
                $('#modal-bulk-add').modal('hide');
                $('#bulk-add-paths').val('');
            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    }
});
//--></script>
<?php echo $footer; ?>
