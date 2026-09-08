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
    <?php if ($success) { ?>
    <div class="alert alert-success alert-dismissible"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
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
            <label class="col-sm-2 control-label" for="input-admin-iframe-url"><?php echo $entry_admin_iframe_url; ?></label>
            <div class="col-sm-10">
              <input type="text" name="module_customdesigncart_admin_iframe_url" id="input-admin-iframe-url" value="<?php echo $module_customdesigncart_admin_iframe_url; ?>" class="form-control" readonly="readonly" />
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label"><?php echo $entry_boundary_global_settings; ?></label>
            <div class="col-sm-10">
              <input type="hidden" name="module_customdesigncart_global_boundary" value="0" />
              <label class="checkbox-inline"><input type="checkbox" name="module_customdesigncart_global_boundary" value="1" <?php if ($module_customdesigncart_global_boundary) { ?>checked="checked"<?php } ?>> <?php echo $entry_boundary; ?></label>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label"><?php echo $entry_text_global_settings; ?></label>
            <div class="col-sm-10">
              <div class="well well-sm">
                <div class="row">
                  <div class="col-sm-4"><input type="hidden" name="module_customdesigncart_text_global_font" value="0" /><label class="checkbox-inline"><input type="checkbox" name="module_customdesigncart_text_global_font" value="1" <?php if ($module_customdesigncart_text_global_font) { ?>checked="checked"<?php } ?>> <?php echo $entry_text_font; ?></label></div>
                  <div class="col-sm-4"><input type="hidden" name="module_customdesigncart_text_global_curve" value="0" /><label class="checkbox-inline"><input type="checkbox" name="module_customdesigncart_text_global_curve" value="1" <?php if ($module_customdesigncart_text_global_curve) { ?>checked="checked"<?php } ?>> <?php echo $entry_text_curve; ?></label></div>
                  <div class="col-sm-4"><input type="hidden" name="module_customdesigncart_text_global_space" value="0" /><label class="checkbox-inline"><input type="checkbox" name="module_customdesigncart_text_global_space" value="1" <?php if ($module_customdesigncart_text_global_space) { ?>checked="checked"<?php } ?>> <?php echo $entry_text_space; ?></label></div>
                  <div class="col-sm-4"><input type="hidden" name="module_customdesigncart_text_global_style" value="0" /><label class="checkbox-inline"><input type="checkbox" name="module_customdesigncart_text_global_style" value="1" <?php if ($module_customdesigncart_text_global_style) { ?>checked="checked"<?php } ?>> <?php echo $entry_text_style; ?></label></div>
                  <div class="col-sm-4"><input type="hidden" name="module_customdesigncart_text_global_align" value="0" /><label class="checkbox-inline"><input type="checkbox" name="module_customdesigncart_text_global_align" value="1" <?php if ($module_customdesigncart_text_global_align) { ?>checked="checked"<?php } ?>> <?php echo $entry_text_align; ?></label></div>
                  <div class="col-sm-4"><input type="hidden" name="module_customdesigncart_text_global_size" value="0" /><label class="checkbox-inline"><input type="checkbox" name="module_customdesigncart_text_global_size" value="1" <?php if ($module_customdesigncart_text_global_size) { ?>checked="checked"<?php } ?>> <?php echo $entry_text_size; ?></label></div>
                  <div class="col-sm-4"><input type="hidden" name="module_customdesigncart_text_global_color" value="0" /><label class="checkbox-inline"><input type="checkbox" name="module_customdesigncart_text_global_color" value="1" <?php if ($module_customdesigncart_text_global_color) { ?>checked="checked"<?php } ?>> <?php echo $entry_text_color; ?></label></div>
                  <div class="col-sm-4"><input type="hidden" name="module_customdesigncart_text_global_pos" value="0" /><label class="checkbox-inline"><input type="checkbox" name="module_customdesigncart_text_global_pos" value="1" <?php if ($module_customdesigncart_text_global_pos) { ?>checked="checked"<?php } ?>> <?php echo $entry_text_pos; ?></label></div>
                  <div class="col-sm-4"><input type="hidden" name="module_customdesigncart_text_global_rot" value="0" /><label class="checkbox-inline"><input type="checkbox" name="module_customdesigncart_text_global_rot" value="1" <?php if ($module_customdesigncart_text_global_rot) { ?>checked="checked"<?php } ?>> <?php echo $entry_text_rot; ?></label></div>
                </div>
              </div>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label"><?php echo $entry_image_global_settings; ?></label>
            <div class="col-sm-10">
              <div class="well well-sm">
                <div class="row">
                  <div class="col-sm-4"><input type="hidden" name="module_customdesigncart_image_global_upload" value="0" /><label class="checkbox-inline"><input type="checkbox" name="module_customdesigncart_image_global_upload" value="1" <?php if ($module_customdesigncart_image_global_upload) { ?>checked="checked"<?php } ?>> <?php echo $entry_image_upload; ?></label></div>
                  <div class="col-sm-4"><input type="hidden" name="module_customdesigncart_image_global_opacity" value="0" /><label class="checkbox-inline"><input type="checkbox" name="module_customdesigncart_image_global_opacity" value="1" <?php if ($module_customdesigncart_image_global_opacity) { ?>checked="checked"<?php } ?>> <?php echo $entry_image_opacity; ?></label></div>
                  <div class="col-sm-4"><input type="hidden" name="module_customdesigncart_image_global_size" value="0" /><label class="checkbox-inline"><input type="checkbox" name="module_customdesigncart_image_global_size" value="1" <?php if ($module_customdesigncart_image_global_size) { ?>checked="checked"<?php } ?>> <?php echo $entry_image_size; ?></label></div>
                  <div class="col-sm-4"><input type="hidden" name="module_customdesigncart_image_global_color" value="0" /><label class="checkbox-inline"><input type="checkbox" name="module_customdesigncart_image_global_color" value="1" <?php if ($module_customdesigncart_image_global_color) { ?>checked="checked"<?php } ?>> <?php echo $entry_image_color; ?></label></div>
                  <div class="col-sm-4"><input type="hidden" name="module_customdesigncart_image_global_pos" value="0" /><label class="checkbox-inline"><input type="checkbox" name="module_customdesigncart_image_global_pos" value="1" <?php if ($module_customdesigncart_image_global_pos) { ?>checked="checked"<?php } ?>> <?php echo $entry_image_pos; ?></label></div>
                  <div class="col-sm-4"><input type="hidden" name="module_customdesigncart_image_global_rot" value="0" /><label class="checkbox-inline"><input type="checkbox" name="module_customdesigncart_image_global_rot" value="1" <?php if ($module_customdesigncart_image_global_rot) { ?>checked="checked"<?php } ?>> <?php echo $entry_image_rot; ?></label></div>
                </div>
              </div>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-price-custom-product"><?php echo $entry_price_custom_product; ?></label>
            <div class="col-sm-10">
              <input type="text" name="module_customdesigncart_price_custom_product" placeholder="<?php echo $entry_price_custom_product; ?>" id="input-price-custom-product" value="<?php echo $module_customdesigncart_price_custom_product; ?>" class="form-control" />
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-price-text-layer"><?php echo $entry_price_text_layer; ?></label>
            <div class="col-sm-10">
              <input type="text" name="module_customdesigncart_price_text_layer" placeholder="<?php echo $entry_price_text_layer; ?>" id="input-price-text-layer" value="<?php echo $module_customdesigncart_price_text_layer; ?>" class="form-control" />
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-price-image-layer"><?php echo $entry_price_image_layer; ?></label>
            <div class="col-sm-10">
              <input type="text" name="module_customdesigncart_price_image_layer" placeholder="<?php echo $entry_price_image_layer; ?>" id="input-price-image-layer" value="<?php echo $module_customdesigncart_price_image_layer; ?>" class="form-control" />
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
        </form>
      </div>
    </div>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-money"></i> <?php echo $entry_bulk_price_update; ?></h3>
      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" class="form-horizontal">
          <input type="hidden" name="bulk_price_update" value="1" />
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-bulk-price"><?php echo $entry_bulk_price; ?></label>
            <div class="col-sm-10">
              <input type="number" name="custom_product_additional_price" id="input-bulk-price" value="" step="0.01" class="form-control" required="required" />
            </div>
          </div>
          <div class="form-group">
            <div class="col-sm-10 col-sm-offset-2">
              <button type="submit" class="btn btn-warning"><i class="fa fa-refresh"></i> <?php echo $entry_bulk_price_update; ?></button>
            </div>
          </div>
        </form>
      </div>
    </div>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-copy"></i> Copy Design Settings Between Products</h3>
      </div>
      <div class="panel-body">
        <p class="text-muted"><small><i class="fa fa-info-circle"></i> Use this section to copy custom design settings from a source product to one or more target products.</small></p>
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-module" class="form-horizontal">
          <div class="form-group" style="margin-bottom: 20px;">
            <label class="col-sm-2 control-label" for="input-single-product"><i class="fa fa-cube"></i> <?php echo $entry_source_product; ?></label>
            <div class="col-sm-10">
              <input type="text" name="product" value="" placeholder="<?php echo $entry_source_product; ?>" id="input-single-product" class="form-control" />
              <input type="hidden" name="module_customdesigncart_product_id" value="" />
              <div id="single-product-selected" style="margin-top: 10px;"></div>
            </div>
          </div>
          <hr style="margin: 15px 0;">
          <div class="form-group" style="margin-bottom: 20px;">
            <label class="col-sm-2 control-label" for="input-related"><i class="fa fa-cubes"></i> <?php echo $entry_target_products; ?></label>
            <div class="col-sm-10">
              <input type="text" name="related" value="" placeholder="<?php echo $entry_target_products; ?>" id="input-related" class="form-control" />
              <div id="module-related" class="well well-sm" style="height: 150px; overflow: auto; margin-top: 10px; background-color: #f9f9f9;">
                <?php if (!empty($module_customdesigncart_related)) { ?>
                  <?php foreach ($module_customdesigncart_related as $related_product) { ?>
                  <div id="module-related<?php echo $related_product['product_id']; ?>" style="padding: 8px; background: white; border: 1px solid #ddd; margin-bottom: 5px; border-radius: 3px; display: flex; justify-content: space-between; align-items: center;">
                    <span><i class="fa fa-check-circle" style="color: #27ae60;"></i> <?php echo $related_product['name']; ?></span>
                    <button type="button" class="btn btn-xs btn-danger" onclick="$(this).closest('div').remove();" title="Remove product"><i class="fa fa-times"></i></button>
                    <input type="hidden" name="module_customdesigncart_related[]" value="<?php echo $related_product['product_id']; ?>" />
                  </div>
                  <?php } ?>
                <?php } else { ?>
                  <div style="padding: 15px; text-align: center; color: #999;"><small><i class="fa fa-inbox"></i> No target products selected yet</small></div>
                <?php } ?>
              </div>
            </div>
          </div>
          <hr style="margin: 15px 0;">
          <div class="form-group">
            <div class="col-sm-10 col-sm-offset-2">
              <button type="button" id="button-copy-data" class="btn btn-success btn-lg"><i class="fa fa-files-o"></i> Copy Design Settings</button>
              <span class="help-block"><small><i class="fa fa-lightbulb-o"></i> Select one source product and one or more target products, then click the button above to copy all design settings.</small></span>
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
            
            $('#input-single-product').after('<div id="single-product-selected" style="padding: 10px; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 3px; color: #155724;"><i class="fa fa-check-circle"></i> <strong>' + item['label'] + '</strong> <button type="button" class="btn btn-xs btn-link" style="color: #155724;" onclick="$(\'#single-product-selected\').remove(); $(\'input[name=\\\'module_customdesigncart_product_id\\\']\').val(\'\');"><i class="fa fa-times"></i> Remove</button></div>');
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
            
            // Remove empty message if exists
            $('#module-related').find('div:contains("No target products")').remove();
            
            $('#module-related').append('<div id="module-related' + item['value'] + '" style="padding: 8px; background: white; border: 1px solid #ddd; margin-bottom: 5px; border-radius: 3px; display: flex; justify-content: space-between; align-items: center;"><span><i class="fa fa-check-circle" style="color: #27ae60;"></i> ' + item['label'] + '</span><button type="button" class="btn btn-xs btn-danger" onclick="$(this).closest(\'div\').remove();" title="Remove product"><i class="fa fa-times"></i></button><input type="hidden" name="module_customdesigncart_related[]" value="' + item['value'] + '" /></div>');	
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
//-->
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
