<?php
// Heading
$_['heading_title']         = 'Import Data Module';

// Text
$_['text_extension']        = 'Extensions';
$_['text_success']          = 'Success: You have modified Import Data module!';
$_['text_edit']             = 'Edit Import Data Module';
$_['text_home']             = 'Home';
$_['text_upload_step']      = 'Step 1: Upload File';
$_['text_mapping_step']     = 'Step 2: Column Mapping';
$_['text_settings_step']    = 'Step 3: Import Settings';
$_['text_import_step']      = 'Step 4: Import Data';
$_['text_step_upload']      = 'Upload';
$_['text_step_mapping']     = 'Mapping';
$_['text_step_settings']    = 'Settings';
$_['text_step_import']      = 'Import';
$_['text_select']           = '-- Please Select --';
$_['text_ignore']           = '-- Ignore Column --';
$_['text_product']          = 'Products';
$_['text_category']         = 'Categories';
$_['text_customer']         = 'Customers';
$_['text_order']            = 'Orders';
$_['text_manufacturer']     = 'Manufacturers';
$_['text_attribute']        = 'Attributes';
$_['text_option']           = 'Options';
$_['text_recent_imports']   = 'Recent Imports';
$_['text_upload_success']   = 'File uploaded successfully!';
$_['text_import_success']   = 'Import completed! Inserted: %s, Updated: %s, Errors: %s';
$_['text_mapping_help']     = 'Map columns from your file to database fields. The system will try to auto-detect matching columns. You can also manually select each mapping.';
$_['text_entity_type']      = 'Importing';
$_['text_sample_preview']   = 'Sample Data Preview (First 5 Rows)';
$_['text_auto_map_complete']= 'Auto-mapping completed! Please review the mappings.';
$_['text_import_mode']      = 'Import Mode';
$_['text_advanced_options'] = 'Advanced Options';
$_['text_import_summary']   = 'Ready to Import';
$_['text_warning']          = 'Warning';
$_['text_import_warning']   = 'Please review all settings before proceeding. This action will modify your database. Make sure you have a backup!';
$_['text_importing']        = 'Importing Data...';
$_['text_processing']       = 'Processing records, please wait...';
$_['text_import_complete']  = 'Import Complete!';
$_['text_inserted']         = 'Inserted';
$_['text_updated']          = 'Updated';
$_['text_errors']           = 'Errors';
$_['text_total']            = 'Total';
$_['text_delete_confirm']   = 'WARNING: This will delete ALL existing records before importing! Are you absolutely sure?';
$_['text_mode_insert']      = 'Insert Only';
$_['text_mode_update']      = 'Update Only';
$_['text_mode_upsert']      = 'Insert or Update (Upsert)';

// Entry
$_['entry_entity_type']     = 'Data Type';
$_['entry_file']            = 'Import File';
$_['entry_import_mode']     = 'Import Mode';
$_['entry_skip_first_row']  = 'Skip First Row';
$_['entry_delete_existing'] = 'Delete All Existing Records';
$_['entry_update_existing'] = 'Update Existing Records';
$_['entry_ignore_errors']   = 'Continue on Errors';
$_['entry_batch_size']      = 'Batch Size';
$_['entry_debug_mode']      = 'Debug Mode';

// Help
$_['help_file']             = 'Supported formats: CSV, TXT, XLSX, XLS. Maximum file size: 50MB';
$_['help_mode_insert']      = 'Only insert new records. Skip if record already exists.';
$_['help_mode_update']      = 'Only update existing records. Skip if record does not exist.';
$_['help_mode_upsert']      = 'Insert new records and update existing ones (recommended).';
$_['help_skip_first_row']   = 'Check this if your file has header row with column names.';
$_['help_delete_existing']  = 'WARNING: This will permanently delete all existing records of this type before importing!';
$_['help_update_existing']  = 'If a record already exists (matched by ID or unique field), update it with new data.';
$_['help_ignore_errors']    = 'Continue importing even if some records fail. Failed records will be logged.';
$_['help_batch_size']       = 'Number of records to process at once. Lower values use less memory but take longer.';
$_['help_debug_mode']       = 'Enable debug mode to log all SQL queries during import (for troubleshooting).';

// Debug
$_['text_debug_log']        = 'Debug Log';
$_['button_view_log']       = 'View Debug Log';
$_['button_clear_log']      = 'Clear Log';
$_['text_log_empty']        = 'Log is empty';
$_['text_log_cleared']      = 'Debug log has been cleared!';
$_['success_log_cleared']   = 'Success: Debug log has been cleared!';

// Column
$_['column_date']           = 'Date';
$_['column_entity']         = 'Type';
$_['column_filename']       = 'File';
$_['column_records']        = 'Records';
$_['column_status']         = 'Status';
$_['column_file_header']    = 'File Column';
$_['column_db_field']       = 'Database Field';
$_['column_sample_data']    = 'Sample Data';

// Button
$_['button_continue']       = 'Continue';
$_['button_back']           = 'Back';
$_['button_import']         = 'Start Import';
$_['button_cancel']         = 'Cancel';
$_['button_auto_map']       = 'Auto-Map Columns';
$_['button_clear_map']      = 'Clear Mapping';
$_['button_new_import']     = 'Start New Import';

// Error
$_['error_permission']      = 'Warning: You do not have permission to modify Import Data module!';
$_['error_upload']          = 'Please select a file to upload!';
$_['error_entity_type']     = 'Please select a data type!';
$_['error_file_type']       = 'Invalid file type! Only CSV, TXT, XLSX, and XLS files are allowed.';
$_['error_file_save']       = 'Error saving uploaded file!';
$_['error_parse_file']      = 'Error reading file! Please check the file format.';
$_['error_session']         = 'Import session expired! Please start over.';
