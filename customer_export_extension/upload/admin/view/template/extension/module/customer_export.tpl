<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="button" id="button-export" data-toggle="tooltip" title="<?php echo $button_export; ?>" class="btn btn-primary"><i class="fa fa-download"></i> <?php echo $button_export; ?></button>
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
        <h3 class="panel-title"><i class="fa fa-filter"></i> <?php echo $button_filter; ?></h3>
      </div>
      <div class="panel-body">
        <div class="well">
          <div class="row">
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label" for="input-search"><?php echo $entry_search; ?></label>
                <input type="text" name="filter_search" value="" placeholder="<?php echo $entry_search; ?>" id="input-search" class="form-control" />
              </div>
              <div class="form-group">
                <label class="control-label" for="input-customer-group"><?php echo $entry_customer_group; ?></label>
                <select name="filter_customer_group_id" id="input-customer-group" class="form-control">
                  <option value=""><?php echo $text_all_customer_groups; ?></option>
                  <?php foreach ($customer_groups as $customer_group) { ?>
                  <option value="<?php echo $customer_group['customer_group_id']; ?>"><?php echo $customer_group['name']; ?></option>
                  <?php } ?>
                </select>
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label" for="input-status"><?php echo $entry_status; ?></label>
                <select name="filter_status" id="input-status" class="form-control">
                  <option value=""><?php echo $text_all_status; ?></option>
                  <option value="1"><?php echo $text_enabled; ?></option>
                  <option value="0"><?php echo $text_disabled; ?></option>
                </select>
              </div>
              <div class="form-group">
                <label class="control-label" for="input-approved"><?php echo $entry_approved; ?></label>
                <select name="filter_approved" id="input-approved" class="form-control">
                  <option value=""><?php echo $text_all_approved; ?></option>
                  <option value="1"><?php echo $text_yes; ?></option>
                  <option value="0"><?php echo $text_no; ?></option>
                </select>
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label" for="input-newsletter"><?php echo $entry_newsletter; ?></label>
                <select name="filter_newsletter" id="input-newsletter" class="form-control">
                  <option value=""><?php echo $text_newsletter_all; ?></option>
                  <option value="1"><?php echo $text_newsletter_subscribed; ?></option>
                  <option value="0"><?php echo $text_newsletter_unsubscribed; ?></option>
                </select>
              </div>
              <div class="form-group">
                <label class="control-label" for="input-date-added-from"><?php echo $entry_date_added_from; ?></label>
                <div class="input-group date">
                  <input type="text" name="filter_date_added_from" value="" placeholder="<?php echo $entry_date_added_from; ?>" data-date-format="YYYY-MM-DD" id="input-date-added-from" class="form-control" />
                  <span class="input-group-btn">
                  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span>
                </div>
              </div>
              <div class="form-group">
                <label class="control-label" for="input-date-added-to"><?php echo $entry_date_added_to; ?></label>
                <div class="input-group date">
                  <input type="text" name="filter_date_added_to" value="" placeholder="<?php echo $entry_date_added_to; ?>" data-date-format="YYYY-MM-DD" id="input-date-added-to" class="form-control" />
                  <span class="input-group-btn">
                  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-12 text-right">
              <button type="button" id="button-filter" class="btn btn-primary"><i class="fa fa-search"></i> <?php echo $button_filter; ?></button>
            </div>
          </div>
        </div>
        
        <div class="table-responsive">
          <table class="table table-bordered table-hover">
            <thead>
              <tr>
                <td class="text-right"><?php echo $column_customer_id; ?></td>
                <td class="text-left"><?php echo $column_name; ?></td>
                <td class="text-left"><?php echo $column_email; ?></td>
                <td class="text-left"><?php echo $column_customer_group; ?></td>
                <td class="text-left"><?php echo $column_status; ?></td>
                <td class="text-left"><?php echo $column_approved; ?></td>
                <td class="text-left"><?php echo $column_date_added; ?></td>
              </tr>
            </thead>
            <tbody id="customer-list">
              <tr>
                <td colspan="7" class="text-center">Loading...</td>
              </tr>
            </tbody>
          </table>
        </div>
        
        <div class="row">
          <div class="col-sm-6 text-left">
            <div id="pagination-info"></div>
          </div>
          <div class="col-sm-6 text-right">
            <ul class="pagination" id="pagination">
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
var current_page = 1;
var limit = 10;

$(document).ready(function() {
	// Initialize date pickers
	$('.date').datetimepicker({
		pickTime: false
	});
	
	// Load initial customer list
	loadCustomers();
	
	// Filter button click
	$('#button-filter').on('click', function() {
		current_page = 1;
		loadCustomers();
	});
	
	// Export button click
	$('#button-export').on('click', function() {
		exportCustomers();
	});
	
	// Search on enter key
	$('#input-search').on('keypress', function(e) {
		if (e.which == 13) {
			current_page = 1;
			loadCustomers();
		}
	});
});

function loadCustomers() {
	var filter_data = {
		filter_customer_group_id: $('select[name="filter_customer_group_id"]').val(),
		filter_status: $('select[name="filter_status"]').val(),
		filter_approved: $('select[name="filter_approved"]').val(),
		filter_date_added_from: $('input[name="filter_date_added_from"]').val(),
		filter_date_added_to: $('input[name="filter_date_added_to"]').val(),
		filter_newsletter: $('select[name="filter_newsletter"]').val(),
		filter_search: $('input[name="filter_search"]').val(),
		start: (current_page - 1) * limit,
		limit: limit,
		token: '<?php echo $token; ?>'
	};
	
	$.ajax({
		url: 'index.php?route=extension/module/customer_export/getList&token=<?php echo $token; ?>',
		type: 'get',
		data: filter_data,
		dataType: 'json',
		beforeSend: function() {
			$('#customer-list').html('<tr><td colspan="7" class="text-center"><i class="fa fa-spinner fa-spin"></i> Loading...</td></tr>');
		},
		success: function(json) {
			var html = '';
			
			if (json.customers.length > 0) {
				for (var i = 0; i < json.customers.length; i++) {
					html += '<tr>';
					html += '<td class="text-right">' + json.customers[i].customer_id + '</td>';
					html += '<td class="text-left">' + json.customers[i].name + '</td>';
					html += '<td class="text-left">' + json.customers[i].email + '</td>';
					html += '<td class="text-left">' + json.customers[i].customer_group + '</td>';
					html += '<td class="text-left">' + json.customers[i].status + '</td>';
					html += '<td class="text-left">' + json.customers[i].approved + '</td>';
					html += '<td class="text-left">' + json.customers[i].date_added + '</td>';
					html += '</tr>';
				}
			} else {
				html = '<tr><td colspan="7" class="text-center">No customers found.</td></tr>';
			}
			
			$('#customer-list').html(html);
			
			// Update pagination
			updatePagination(json.total);
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
}

function updatePagination(total) {
	var total_pages = Math.ceil(total / limit);
	
	$('#pagination-info').html('Showing ' + ((current_page - 1) * limit + 1) + ' to ' + Math.min(current_page * limit, total) + ' of ' + total + ' customers');
	
	var pagination_html = '';
	
	if (total_pages > 1) {
		// Previous button
		if (current_page > 1) {
			pagination_html += '<li><a href="javascript:void(0);" onclick="changePage(' + (current_page - 1) + ')">«</a></li>';
		}
		
		// Page numbers
		var start_page = Math.max(1, current_page - 2);
		var end_page = Math.min(total_pages, current_page + 2);
		
		if (start_page > 1) {
			pagination_html += '<li><a href="javascript:void(0);" onclick="changePage(1)">1</a></li>';
			if (start_page > 2) {
				pagination_html += '<li class="disabled"><span>...</span></li>';
			}
		}
		
		for (var i = start_page; i <= end_page; i++) {
			if (i == current_page) {
				pagination_html += '<li class="active"><span>' + i + '</span></li>';
			} else {
				pagination_html += '<li><a href="javascript:void(0);" onclick="changePage(' + i + ')">' + i + '</a></li>';
			}
		}
		
		if (end_page < total_pages) {
			if (end_page < total_pages - 1) {
				pagination_html += '<li class="disabled"><span>...</span></li>';
			}
			pagination_html += '<li><a href="javascript:void(0);" onclick="changePage(' + total_pages + ')">' + total_pages + '</a></li>';
		}
		
		// Next button
		if (current_page < total_pages) {
			pagination_html += '<li><a href="javascript:void(0);" onclick="changePage(' + (current_page + 1) + ')">»</a></li>';
		}
	}
	
	$('#pagination').html(pagination_html);
}

function changePage(page) {
	current_page = page;
	loadCustomers();
}

function exportCustomers() {
	var filter_data = {
		filter_customer_group_id: $('select[name="filter_customer_group_id"]').val(),
		filter_status: $('select[name="filter_status"]').val(),
		filter_approved: $('select[name="filter_approved"]').val(),
		filter_date_added_from: $('input[name="filter_date_added_from"]').val(),
		filter_date_added_to: $('input[name="filter_date_added_to"]').val(),
		filter_newsletter: $('select[name="filter_newsletter"]').val(),
		filter_search: $('input[name="filter_search"]').val(),
		token: '<?php echo $token; ?>'
	};
	
	// Create a form and submit it
	var form = $('<form></form>');
	form.attr('method', 'post');
	form.attr('action', 'index.php?route=extension/module/customer_export/export&token=<?php echo $token; ?>');
	
	$.each(filter_data, function(key, value) {
		var input = $('<input type="hidden" />');
		input.attr('name', key);
		input.attr('value', value);
		form.append(input);
	});
	
	$('body').append(form);
	form.submit();
	form.remove();
}
</script>

<?php echo $footer; ?>
