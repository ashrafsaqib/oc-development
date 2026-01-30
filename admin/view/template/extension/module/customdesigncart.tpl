<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-module" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
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
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-module" class="form-horizontal">
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-status"><?php echo $entry_status; ?></label>
            <div class="col-sm-10">
              <select name="module_customdesigncart_status" id="input-status" class="form-control">
                <?php if ($module_customdesigncart_status) { ?>
                <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                <option value="0"><?php echo $text_disabled; ?></option>
                <?php } else { ?>
                <option value="1"><?php echo $text_enabled; ?></option>
                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                <?php } ?>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-iframe-url"><?php echo $entry_iframe_url; ?></label>
            <div class="col-sm-10">
              <input type="text" name="module_customdesigncart_iframe_url" placeholder="<?php echo $entry_iframe_url; ?>" id="input-iframe-url" value="<?php echo $module_customdesigncart_iframe_url; ?>" class="form-control" />
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label"><?php echo $tab_custom_fonts; ?></label>
            <div class="col-sm-10">
              <a href="<?php echo $link_font_manager; ?>" class="btn btn-primary"><i class="fa fa-font"></i> Manage Custom Fonts</a>
              <a href="<?php echo $link_translation_manager; ?>" class="btn btn-info"><i class="fa fa-language"></i> Manage Translations</a>
              <a href="<?php echo $link_image_manager; ?>" class="btn btn-warning"><i class="fa fa-image"></i> Manage Images</a>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-single-product"><?php echo $entry_source_product; ?></label>
            <div class="col-sm-10">
              <input type="text" name="product" value="" placeholder="<?php echo $entry_source_product; ?>" id="input-single-product" class="form-control" />
              <input type="hidden" name="module_customdesigncart_product_id" value="" />
              <div id="single-product-selected"></div>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-related"><?php echo $entry_target_products; ?></label>
            <div class="col-sm-10">
              <input type="text" name="related" value="" placeholder="<?php echo $entry_target_products; ?>" id="input-related" class="form-control" />
              <div id="module-related" class="well well-sm" style="height: 150px; overflow: auto;">
                <?php foreach ($module_customdesigncart_related as $related_product) { ?>
                <div id="module-related<?php echo $related_product['product_id']; ?>"><i class="fa fa-minus-circle"></i> <?php echo $related_product['name']; ?>
                  <input type="hidden" name="module_customdesigncart_related[]" value="<?php echo $related_product['product_id']; ?>" />
                </div>
                <?php } ?>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-sm-10 col-sm-offset-2">
              <button type="button" id="button-copy-data" class="btn btn-primary"><i class="fa fa-copy"></i> Copy Product Data</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript"><!--
$(document).ready(function() {
    // Single Product Autocomplete
    $('input[name=\'product\']').autocomplete({
        'source': function(request, response) {
            $.ajax({
                url: '<?php echo $product_autocomplete; ?>&filter_name=' +  encodeURIComponent(request),
                dataType: 'json',
                success: function(json) {
                    response($.map(json, function(item) {
                        return {
                            label: item['name'],
                            value: item['product_id']
                        }
                    }));
                }
            });
        },
        'select': function(item) {
            $('input[name=\'product\']').val('');
            $('#single-product-selected').remove();
            $('input[name=\'module_customdesigncart_product_id\']').val(item['value']);
            
            $('#input-single-product').after('<div id="single-product-selected"><i class="fa fa-check-circle"></i> ' + item['label'] + ' <button type="button" class="btn btn-danger btn-xs" onclick="$(\'#single-product-selected\').remove(); $(\'input[name=\\\'module_customdesigncart_product_id\\\']\').val(\'\');"><i class="fa fa-times"></i></button></div>');
        }
    });

    // Related Products Autocomplete
    $('input[name=\'related\']').autocomplete({
        'source': function(request, response) {
            $.ajax({
                url: '<?php echo $product_autocomplete; ?>&filter_name=' +  encodeURIComponent(request),
                dataType: 'json',
                success: function(json) {
                    response($.map(json, function(item) {
                        return {
                            label: item['name'],
                            value: item['product_id']
                        }
                    }));
                }
            });
        },
        'select': function(item) {
            $('input[name=\'related\']').val('');
            
            $('#module-related' + item['value']).remove();
            
            $('#module-related').append('<div id="module-related' + item['value'] + '"><i class="fa fa-minus-circle"></i> ' + item['label'] + '<input type="hidden" name="module_customdesigncart_related[]" value="' + item['value'] + '" /></div>');	
        }	
    });

    $('#module-related').delegate('.fa-minus-circle', 'click', function() {
        $(this).parent().remove();
    });

    // Copy Data Button
    $('#button-copy-data').on('click', function() {
        var sourceProductId = $('input[name=\'module_customdesigncart_product_id\']').val();
        var targetProductIds = [];
        
        // Get all related product IDs
        $('input[name^=\'module_customdesigncart_related\']').each(function() {
            targetProductIds.push($(this).val());
        });
        
        if (!sourceProductId) {
            alert('Please select a source product first!');
            return;
        }
        
        if (targetProductIds.length === 0) {
            alert('Please select at least one target product!');
            return;
        }
        
        $.ajax({
            url: 'index.php?route=extension/module/customdesigncart/copyData&token=<?php echo $token; ?>',
            type: 'POST',
            data: {
                source_product_id: sourceProductId,
                target_product_ids: targetProductIds
            },
            dataType: 'json',
            beforeSend: function() {
                $('#button-copy-data').button('loading');
            },
            complete: function() {
                $('#button-copy-data').button('reset');
            },
            success: function(json) {
                if (json['success']) {
                    alert(json['success']);
                }
                if (json['error']) {
                    alert(json['error']);
                }
            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    });
});
//--></script>
<?php echo $footer; ?>
