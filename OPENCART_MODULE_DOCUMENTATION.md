# OpenCart 2 Module Development Guide

## Complete Guide: Import Data Module

This document provides a comprehensive guide on how OpenCart 2 modules are created, using the Import Data Module as a practical example.

---

## Table of Contents

1. [OpenCart 2 Module Architecture](#opencart-2-module-architecture)
2. [Module Structure](#module-structure)
3. [Creating Your Module](#creating-your-module)
4. [Installation & Usage](#installation--usage)
5. [Import Data Module Features](#import-data-module-features)
6. [Troubleshooting](#troubleshooting)

---

## OpenCart 2 Module Architecture

### MVC Pattern
OpenCart 2 follows the Model-View-Controller (MVC) pattern:

- **Controller**: Handles user requests and business logic
- **Model**: Manages database operations
- **View**: Handles presentation/UI (TPL template files)

### Directory Structure

```
admin/
├── controller/extension/module/     # Controller files
├── model/extension/module/          # Model files
├── view/template/extension/module/  # View template files
└── language/en-gb/extension/module/ # Language files
```

---

## Module Structure

### 1. Controller (`admin/controller/extension/module/your_module.php`)

**Purpose**: Handle HTTP requests, validate input, coordinate between model and view

**Key Components**:
- Class name: `ControllerExtensionModuleYourModule`
- Extends: `Controller` base class
- Methods:
  - `index()`: Main entry point
  - `install()`: Called when module is installed
  - `uninstall()`: Called when module is uninstalled
  - Custom methods for specific actions

**Example Structure**:
```php
<?php
class ControllerExtensionModuleYourModule extends Controller {
    public function index() {
        // Load language files
        $this->load->language('extension/module/your_module');
        
        // Set page title
        $this->document->setTitle($this->language->get('heading_title'));
        
        // Load model
        $this->load->model('extension/module/your_module');
        
        // Prepare data for view
        $data = array();
        
        // Load view components
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        
        // Render view
        $this->response->setOutput($this->load->view('extension/module/your_module', $data));
    }
    
    public function install() {
        $this->load->model('extension/module/your_module');
        $this->model_extension_module_your_module->install();
    }
    
    public function uninstall() {
        $this->load->model('extension/module/your_module');
        $this->model_extension_module_your_module->uninstall();
    }
}
```

### 2. Model (`admin/model/extension/module/your_module.php`)

**Purpose**: Handle all database operations

**Key Components**:
- Class name: `ModelExtensionModuleYourModule`
- Extends: `Model` base class
- Database access: `$this->db->query()`
- Methods for CRUD operations

**Example Structure**:
```php
<?php
class ModelExtensionModuleYourModule extends Model {
    public function install() {
        $this->db->query("
            CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "your_table` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `name` varchar(255) NOT NULL,
                `date_added` datetime NOT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8;
        ");
    }
    
    public function uninstall() {
        // Optionally drop tables
        // $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "your_table`");
    }
    
    public function getData() {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "your_table");
        return $query->rows;
    }
    
    public function addRecord($data) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "your_table SET 
            name = '" . $this->db->escape($data['name']) . "',
            date_added = NOW()
        ");
        return $this->db->getLastId();
    }
}
```

### 3. View (`admin/view/template/extension/module/your_module.tpl`)

**Purpose**: HTML template for rendering UI

**Key Components**:
- PHP variables passed from controller
- OpenCart helper variables: `$header`, `$footer`, `$column_left`
- Form elements and tables
- JavaScript for AJAX and validation

**Example Structure**:
```php
<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <h1><?php echo $heading_title; ?></h1>
        </div>
    </div>
    <div class="container-fluid">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Your Module Content</h3>
            </div>
            <div class="panel-body">
                <!-- Your content here -->
            </div>
        </div>
    </div>
</div>
<?php echo $footer; ?>
```

### 4. Language (`admin/language/en-gb/extension/module/your_module.php`)

**Purpose**: Store all translatable strings

**Example Structure**:
```php
<?php
// Heading
$_['heading_title'] = 'Your Module Name';

// Text
$_['text_success'] = 'Success: Module updated!';
$_['text_edit'] = 'Edit Module';

// Entry (form labels)
$_['entry_name'] = 'Name';
$_['entry_status'] = 'Status';

// Button
$_['button_save'] = 'Save';
$_['button_cancel'] = 'Cancel';

// Error
$_['error_permission'] = 'Warning: No permission!';
$_['error_name'] = 'Name must be between 3 and 64 characters!';
```

---

## Creating Your Module

### Step-by-Step Process

#### Step 1: Plan Your Module
- Define functionality
- Design database schema
- Plan user interface

#### Step 2: Create Directory Structure
```bash
# Create controller
admin/controller/extension/module/your_module.php

# Create model
admin/model/extension/module/your_module.php

# Create view
admin/view/template/extension/module/your_module.tpl

# Create language file
admin/language/en-gb/extension/module/your_module.php
```

#### Step 3: Implement Controller
1. Create class extending `Controller`
2. Implement `index()` method
3. Add `install()` and `uninstall()` methods
4. Add custom action methods

#### Step 4: Implement Model
1. Create class extending `Model`
2. Implement database operations
3. Add `install()` for table creation
4. Add CRUD methods

#### Step 5: Create View Template
1. Include header, column_left, footer
2. Add breadcrumbs
3. Create forms and tables
4. Add JavaScript for interactions

#### Step 6: Add Language Strings
1. Define all text strings
2. Use language->get() in controller
3. Access via `$_['key']` in templates

#### Step 7: Install Module
1. Go to Extensions > Extensions
2. Filter by "Modules"
3. Find your module
4. Click Install button
5. Access module from admin menu

---

## Installation & Usage

### Installing the Import Data Module

1. **Upload Files**: Ensure all module files are in place:
   ```
   admin/controller/extension/module/import_data.php
   admin/model/extension/module/import_data.php
   admin/view/template/extension/module/import_data*.tpl (3 files)
   admin/language/en-gb/extension/module/import_data.php
   ```

2. **Install via Admin Panel**:
   - Login to OpenCart Admin
   - Navigate to: Extensions > Extensions
   - Select "Modules" from filter dropdown
   - Find "Import Data Module"
   - Click the green Install (+) button

3. **Access Module**:
   - After installation, access via: Extensions > Import Data
   - Or create a menu item for easier access

### Database Tables Created

The module automatically creates two tables:

1. **`oc_import_history`**: Tracks all import operations
2. **`oc_import_errors`**: Logs errors during import

---

## Import Data Module Features

### 1. Multi-Step Wizard Interface

#### Step 1: File Upload
- Select entity type (Products, Categories, Customers, etc.)
- Upload CSV, TXT, XLSX, or XLS files
- View recent imports history

#### Step 2: Column Mapping
- Auto-detect and map columns
- Manual column mapping
- Preview sample data (first 5 rows)
- Auto-map and clear mapping buttons

#### Step 3: Import Settings
- **Import Modes**:
  - Insert Only: Skip existing records
  - Update Only: Only update existing
  - Upsert: Insert new or update existing
  
- **Advanced Options**:
  - Skip first row (headers)
  - Delete all existing records
  - Update existing records
  - Continue on errors
  - Batch size configuration

#### Step 4: Import Execution
- Progress tracking
- Real-time statistics
- Error logging
- Success/failure reporting

### 2. Supported Entity Types

- **Products**: Full product data with descriptions, prices, stock
- **Categories**: Category hierarchy
- **Customers**: Customer information
- **Manufacturers**: Brand/manufacturer data
- **Orders**: Order information
- **Attributes**: Product attributes
- **Options**: Product options

### 3. Import Modes

#### Insert Only Mode
```php
// Only inserts new records
// Skips if record exists
if (!$product_exists) {
    $this->insertProduct($data);
}
```

#### Update Only Mode
```php
// Only updates existing records
// Skips if record doesn't exist
if ($product_exists) {
    $this->updateProduct($product_id, $data);
}
```

#### Upsert Mode (Recommended)
```php
// Inserts or updates as needed
if ($product_exists) {
    $this->updateProduct($product_id, $data);
} else {
    $this->insertProduct($data);
}
```

### 4. Error Handling

- Continues on errors (optional)
- Logs all errors with row numbers
- Stores failed row data for debugging
- Summary statistics after import

### 5. File Format Support

#### CSV/TXT Files
```php
if (($handle = fopen($filepath, 'r')) !== FALSE) {
    while (($row = fgetcsv($handle, 0, ',')) !== FALSE) {
        // Process row
    }
    fclose($handle);
}
```

#### Excel Files (XLSX/XLS)
Requires PHPExcel library:
```php
require_once(DIR_SYSTEM . 'library/PHPExcel.php');
$objPHPExcel = PHPExcel_IOFactory::load($filepath);
$worksheet = $objPHPExcel->getActiveSheet();
```

---

## Advanced Features

### 1. Batch Processing
Process large files in batches to avoid memory issues:
```php
$batch_size = 100; // Process 100 records at a time
foreach (array_chunk($rows, $batch_size) as $batch) {
    foreach ($batch as $row) {
        $this->importRow($row);
    }
}
```

### 2. Transaction Support (Optional)
For data integrity:
```php
$this->db->query("START TRANSACTION");
try {
    // Import operations
    $this->db->query("COMMIT");
} catch (Exception $e) {
    $this->db->query("ROLLBACK");
}
```

### 3. Progress Tracking
Track import progress for user feedback:
```php
$processed = 0;
$total = count($rows);
foreach ($rows as $row) {
    // Process row
    $processed++;
    $progress = ($processed / $total) * 100;
    // Update progress indicator
}
```

---

## Troubleshooting

### Common Issues

#### 1. Module Not Appearing in Extension List
**Solution**: 
- Check file paths and naming conventions
- Ensure class name matches filename
- Clear OpenCart cache
- Check file permissions

#### 2. Database Tables Not Created
**Solution**:
- Verify install() method is called
- Check database credentials in config.php
- Ensure DB_PREFIX is correctly used
- Check MySQL user permissions

#### 3. File Upload Fails
**Solution**:
- Check PHP upload_max_filesize in php.ini
- Verify upload directory exists and is writable
- Check file extension validation
- Review error logs

#### 4. Import Process Times Out
**Solution**:
- Reduce batch size
- Increase PHP max_execution_time
- Process in chunks with AJAX calls
- Use background processing for large files

#### 5. Memory Exhausted Error
**Solution**:
- Increase PHP memory_limit
- Process file in streams instead of loading all at once
- Reduce batch size
- Optimize data structures

---

## Best Practices

### 1. Security
- Always escape user input: `$this->db->escape()`
- Validate file types and sizes
- Check user permissions
- Use prepared statements when possible

### 2. Performance
- Use batch processing for large datasets
- Index database tables appropriately
- Cache frequently accessed data
- Optimize database queries

### 3. Error Handling
- Always use try-catch blocks
- Log errors for debugging
- Provide user-friendly error messages
- Never expose system details in errors

### 4. Code Organization
- Follow MVC pattern strictly
- Keep controllers thin, models fat
- Use meaningful method and variable names
- Comment complex logic

### 5. Testing
- Test with various file formats
- Test with large datasets
- Test error scenarios
- Validate data integrity after import

---

## Module Customization

### Adding New Entity Types

1. **Add to Controller** (`import_data.php`):
```php
$data['entity_types'] = array(
    'your_entity' => $this->language->get('text_your_entity')
);
```

2. **Add Fields in Model** (`getEntityFields()`):
```php
case 'your_entity':
    $fields = array(
        'id' => 'ID',
        'name' => 'Name',
        // Add more fields
    );
    break;
```

3. **Add Import Logic** (`importRow()`):
```php
case 'your_entity':
    $result = $this->importYourEntity($data, $settings);
    break;
```

4. **Implement Import Method**:
```php
private function importYourEntity($data, $settings) {
    // Your import logic here
}
```

### Extending Functionality

- Add export feature
- Implement scheduled imports
- Add data validation rules
- Create import templates
- Add multi-language support

---

## File Checklist

When creating a module, ensure you have:

- [ ] Controller file with proper class name
- [ ] Model file with database operations
- [ ] View template(s) with UI
- [ ] Language file with all strings
- [ ] install() method for database setup
- [ ] uninstall() method for cleanup
- [ ] Proper error handling
- [ ] User permission checks
- [ ] Input validation
- [ ] Documentation

---

## Resources

### OpenCart Documentation
- Official Docs: https://docs.opencart.com
- Forum: https://forum.opencart.com
- GitHub: https://github.com/opencart/opencart

### PHP Libraries for Import
- **PHPExcel**: Excel file processing
- **PHPSpreadsheet**: Modern Excel library
- **League CSV**: CSV processing
- **Box/Spout**: Fast file processing

---

## Conclusion

This Import Data Module demonstrates key concepts of OpenCart 2 module development:

1. **MVC Architecture**: Separation of concerns
2. **Database Operations**: CRUD operations and table management
3. **User Interface**: Multi-step wizard with AJAX
4. **File Processing**: Multiple format support
5. **Error Handling**: Comprehensive logging and recovery
6. **Batch Processing**: Handling large datasets
7. **User Experience**: Progress tracking and feedback

Use this as a template for creating your own OpenCart 2 modules!

---

## Support

For issues or questions:
1. Check OpenCart logs: `system/storage/logs/`
2. Enable error reporting in PHP
3. Review OpenCart forums
4. Check module error logs in database

---

**Version**: 1.0.0  
**Last Updated**: February 2026  
**Compatibility**: OpenCart 2.x
