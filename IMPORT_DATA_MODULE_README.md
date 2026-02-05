# Import Data Module for OpenCart 2.x

A powerful data import module for OpenCart 2.x, similar to Salesforce Data Loader, with a user-friendly wizard interface.

## Features

✨ **4-Step Wizard Interface**
- Step 1: File Upload & Entity Selection
- Step 2: Column Mapping with Auto-Detection
- Step 3: Import Settings Configuration
- Step 4: Import Execution with Progress Tracking

🗂️ **Multiple Entity Types**
- Products (with descriptions, prices, stock)
- Categories
- Customers
- Manufacturers
- Orders
- Attributes
- Options

📊 **File Format Support**
- CSV files (.csv)
- Text files (.txt)
- Excel files (.xlsx, .xls)

⚙️ **Import Modes**
- **Insert Only**: Add new records only
- **Update Only**: Update existing records only
- **Upsert**: Insert new or update existing (recommended)

🔧 **Advanced Options**
- Skip header row
- Delete all existing data before import
- Update existing records
- Continue on errors
- Configurable batch size
- Error logging and tracking

📈 **Import Tracking**
- History of all imports
- Success/failure statistics
- Detailed error logs
- Row-level error tracking

## Installation

### Quick Install

1. **Upload Files**: Copy all module files to your OpenCart installation

2. **Install Module**:
   - Login to OpenCart Admin
   - Go to: `Extensions > Extensions`
   - Select `Modules` from the filter dropdown
   - Find `Import Data Module`
   - Click the green `Install` button

3. **Access Module**:
   - Navigate to: `Extensions > Import Data`
   - Or access via the module menu item

### Files Installed

```
admin/
├── controller/extension/module/import_data.php
├── model/extension/module/import_data.php
├── view/template/extension/module/
│   ├── import_data.tpl
│   ├── import_data_mapping.tpl
│   └── import_data_settings.tpl
└── language/en-gb/extension/module/import_data.php
```

## Usage

### Basic Import Workflow

#### Step 1: Upload File
1. Select the entity type (Products, Categories, etc.)
2. Choose your CSV/Excel file
3. Click Continue

#### Step 2: Map Columns
1. Review auto-detected column mappings
2. Adjust mappings as needed
3. Use "Auto-Map" button for automatic detection
4. Preview sample data
5. Click Continue

#### Step 3: Configure Settings
1. Choose import mode:
   - Insert Only
   - Update Only
   - Upsert (recommended)
2. Set advanced options:
   - Skip first row (if headers)
   - Delete existing data (⚠️ use with caution)
   - Update existing records
   - Continue on errors
   - Batch size
3. Click "Start Import"

#### Step 4: View Results
- See import statistics
- Review inserted/updated/error counts
- Access error logs if needed

### Example CSV Format (Products)

```csv
model,name,price,quantity,status
PROD-001,"Product Name 1",19.99,100,1
PROD-002,"Product Name 2",29.99,50,1
PROD-003,"Product Name 3",39.99,75,1
```

### Example CSV Format (Categories)

```csv
category_id,name,parent_id,status
1,"Electronics",0,1
2,"Computers",1,1
3,"Laptops",2,1
```

## CSV File Preparation Guidelines

### General Rules

1. **Headers**: First row should contain column names
2. **Encoding**: Use UTF-8 encoding
3. **Delimiters**: Use comma (,) as delimiter
4. **Quotes**: Wrap text with quotes if it contains commas
5. **Line Breaks**: Use standard line breaks (\n or \r\n)

### Product Import Fields

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| model | string | Yes | Product model/SKU |
| name | string | Yes | Product name |
| price | decimal | Yes | Product price |
| quantity | integer | No | Stock quantity |
| status | boolean | No | 1=enabled, 0=disabled |
| sku | string | No | SKU code |
| description | text | No | Product description |
| category_id | integer | No | Category ID |
| manufacturer_id | integer | No | Manufacturer ID |
| weight | decimal | No | Product weight |
| image | string | No | Image path |

### Category Import Fields

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| category_id | integer | No | Category ID (for updates) |
| name | string | Yes | Category name |
| parent_id | integer | No | Parent category ID |
| status | boolean | No | 1=enabled, 0=disabled |
| sort_order | integer | No | Display order |

### Customer Import Fields

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| customer_id | integer | No | Customer ID (for updates) |
| firstname | string | Yes | First name |
| lastname | string | Yes | Last name |
| email | string | Yes | Email address |
| telephone | string | Yes | Phone number |
| status | boolean | No | 1=enabled, 0=disabled |

## Import Modes Explained

### Insert Only
- Adds only new records
- Skips records that already exist
- Safe mode for adding data
- Best for initial imports

### Update Only
- Updates only existing records
- Skips records that don't exist
- Good for bulk updates
- Requires ID or unique field

### Upsert (Insert or Update)
- Adds new records
- Updates existing records
- Most flexible option
- **Recommended for most cases**

## Advanced Features

### Auto-Mapping
The system automatically detects column mappings by matching:
- Exact column names
- Column names with underscores vs spaces
- Common variations (e.g., "Product Name" → "name")

### Error Handling
- Errors are logged with row numbers
- Failed rows are stored for review
- Option to continue or stop on errors
- Detailed error messages

### Batch Processing
- Process large files in chunks
- Configurable batch size (default: 100)
- Prevents memory exhaustion
- Optimizes performance

### Import History
- Track all import operations
- View success/failure statistics
- Access historical data
- Audit trail for compliance

## Troubleshooting

### Import Fails

**File Upload Error**
- Check PHP `upload_max_filesize` setting
- Verify file permissions
- Ensure upload directory exists

**Memory Error**
- Reduce batch size
- Increase PHP `memory_limit`
- Process smaller files

**Timeout Error**
- Increase PHP `max_execution_time`
- Reduce batch size
- Use smaller files

### Data Issues

**Records Not Imported**
- Check column mapping
- Verify required fields are mapped
- Review error logs
- Check data format

**Existing Records Not Updated**
- Ensure "Update Existing" is enabled
- Verify ID field is mapped correctly
- Check import mode setting

### Module Not Visible

**Not in Module List**
- Clear OpenCart cache
- Check file permissions
- Verify file paths
- Refresh modifications

## Database Tables

The module creates two tables:

### `oc_import_history`
Stores import operation history
- import_id
- entity_type
- filename
- records_inserted/updated/errors
- settings
- status
- dates

### `oc_import_errors`
Stores import errors
- error_id
- import_id
- row_number
- error_message
- row_data
- date_added

## Performance Tips

1. **Large Files**: Split into smaller chunks
2. **Batch Size**: Adjust based on server capacity
3. **Images**: Upload images separately to server first
4. **Test First**: Use small sample file to verify mappings
5. **Backup**: Always backup database before large imports

## Security

- File type validation
- Input sanitization
- SQL injection protection
- User permission checks
- Error message sanitization

## Requirements

- OpenCart 2.x (2.0 - 2.3)
- PHP 5.4 or higher
- MySQL 5.5 or higher
- Write permissions on upload directory
- Optional: PHPExcel for Excel file support

## Support

### Logs Location
- OpenCart Logs: `system/storage/logs/`
- Import Errors: Via module interface

### Common Issues
1. Check file format and encoding
2. Verify column mappings
3. Review import settings
4. Check error logs
5. Test with small sample file

## License

Custom module for OpenCart 2.x

## Credits

Developed as a comprehensive data import solution for OpenCart 2.x, inspired by Salesforce Data Loader.

---

## Quick Reference

### Supported File Types
✅ CSV (.csv)  
✅ TXT (.txt)  
✅ XLSX (.xlsx)  
✅ XLS (.xls)

### Supported Entities
✅ Products  
✅ Categories  
✅ Customers  
✅ Manufacturers  
✅ Orders  
✅ Attributes  
✅ Options

### Import Modes
✅ Insert Only  
✅ Update Only  
✅ Upsert (Insert/Update)

---

**For detailed module development documentation, see [OPENCART_MODULE_DOCUMENTATION.md](OPENCART_MODULE_DOCUMENTATION.md)**
