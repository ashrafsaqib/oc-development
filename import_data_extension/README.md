# Import Data Module for OpenCart 2.x

Version: 1.0.0

## Description

Advanced data import module for OpenCart 2.x that allows you to import products, categories, customers, manufacturers, and orders from CSV/Excel files.

## Features

- **Multiple Entity Types**: Import products, categories, customers, manufacturers, and orders
- **Flexible Import Modes**:
  - Insert Only: Only add new records
  - Update Only: Only update existing records
  - Upsert: Insert new or update existing records
- **Field Mapping**: Map CSV columns to database fields with visual preview
- **Batch Processing**: Import large files in configurable batches
- **Debug Mode**: Log all SQL queries for troubleshooting
- **Error Handling**: Continue on errors with detailed error logging
- **Import History**: Track all imports with statistics
- **Delete Existing**: Option to clear all existing data before import

## Installation

1. Download the `import_data_module.ocmod.zip` file
2. Go to Extensions > Installer in your OpenCart admin
3. Upload the ZIP file
4. Go to Extensions > Extensions > Choose "Modules"
5. Find "Import Data Module" and click Install
6. Click Edit to configure the module

## Usage

### Step 1: Upload File
- Navigate to System > Import Data
- Upload your CSV or Excel file
- File is parsed and headers are extracted

### Step 2: Map Fields
- Map CSV columns to database fields
- Preview sample data to verify mapping
- Required fields are marked with asterisk (*)

### Step 3: Configure Settings
- Choose entity type (product, category, etc.)
- Select import mode (insert/update/upsert)
- Set batch size for processing
- Enable debug mode if needed
- Choose whether to delete existing records

### Step 4: Import
- Click "Start Import" to begin
- View real-time progress
- Check import history for results

## Field Mappings

### Product Fields
- product_id, model, sku, upc, ean, jan, isbn, mpn
- name, description, tag, meta_title, meta_description, meta_keyword
- quantity, price, stock_status_id, manufacturer_id
- weight, weight_class_id, length, width, height, length_class_id
- status, sort_order, and more...

### Category Fields
- category_id, parent_id, name, description
- meta_title, meta_description, meta_keyword
- top, column, sort_order, status, image

### Customer Fields
- customer_id, firstname, lastname, email, telephone
- password, customer_group_id, status, newsletter

### Manufacturer Fields
- manufacturer_id, name, image, sort_order

## File Format Requirements

### CSV Format
- First row must contain column headers
- Use comma (,) as delimiter
- UTF-8 encoding recommended

### Required Fields by Entity

**Products:**
- model (required)
- name (required)

**Categories:**
- name (required)

**Customers:**
- firstname, lastname, email, telephone (all required)

**Manufacturers:**
- name (required)

## Debug Mode

Enable debug mode to log all SQL queries to:
`system/storage/logs/import_debug.log`

Useful for troubleshooting import issues and verifying data.

## Import Modes

### Insert Only
- Only creates new records
- Skips existing records (matched by ID or unique field)
- Safe for adding new data without affecting existing records

### Update Only
- Only updates existing records
- Skips new records
- Safe for bulk updates without creating duplicates

### Upsert (Insert or Update)
- Creates new records if they don't exist
- Updates existing records if found
- Most flexible but requires careful data preparation

## Troubleshooting

### Categories not showing in admin
- Ensure category_path table is populated correctly
- Enable debug mode to verify SQL queries
- Check that parent_id references exist

### Import fails with errors
- Enable "Continue on Errors" to skip problematic rows
- Check error log in Import History
- Verify CSV format and encoding

### Slow imports
- Reduce batch size (try 50 or 100)
- Disable debug mode for production imports
- Check server PHP memory and timeout settings

## Support

For issues or questions, please refer to:
- IMPORT_ENTITY_SQL_DOCUMENTATION.md (detailed SQL structure)
- System storage logs for error details
- Import history for statistics

## Technical Notes

### Database Tables Created
- oc_import_history: Tracks all import operations
- oc_import_errors: Logs row-level errors

### Category Path Logic
Categories use a hierarchical path structure (oc_category_path) that must be properly maintained:
- Top-level: 1 path entry (self at level 0)
- Child: 2 entries (parent at level 0, self at level 1)
- Grandchild: 3 entries (grandparent at 0, parent at 1, self at 2)

### Supported File Extensions
- .csv (recommended)
- .txt (treated as CSV)
- .xlsx (requires PHPExcel library)
- .xls (requires PHPExcel library)

## Version History

### 1.0.0 (2026-02-05)
- Initial release
- Support for products, categories, customers, manufacturers, orders
- Field mapping with preview
- Multiple import modes
- Debug logging
- Error handling and import history

## License

This extension is provided as-is for OpenCart 2.x installations.

## Credits

Developed with reference to OpenCart 2.3 database structure and best practices.
