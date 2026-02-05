# 📦 Import Data Module - Ready to Install!

## Package Information

**File:** `import_data_module.ocmod.zip` (34KB)  
**Version:** 1.0.0  
**Release Date:** February 5, 2026  
**Compatible:** OpenCart 2.x (2.3.0+)

---

## 🚀 Quick Install

### Step 1: Upload Extension
1. Login to OpenCart Admin
2. Go to **Extensions → Installer**
3. Upload `import_data_module.ocmod.zip`
4. Wait for success message

### Step 2: Install Module
1. Go to **Extensions → Extensions**
2. Filter by **Modules**
3. Find **Import Data Module**
4. Click **Install** (green + icon)

### Step 3: Access Module
Navigate to: **System → Import Data**

---

## 📋 What's Included

### Core Files
- ✅ Controller: `admin/controller/extension/module/import_data.php`
- ✅ Model: `admin/model/extension/module/import_data.php`
- ✅ Language: `admin/language/en-gb/extension/module/import_data.php`
- ✅ Views: 3 template files (.tpl)

### Documentation
- ✅ `README.md` - Complete feature guide
- ✅ `IMPORT_ENTITY_SQL_DOCUMENTATION.md` - Database structure reference
- ✅ `INSTALL.sh` - Installation reference
- ✅ `install.json` - Extension metadata

### Database Tables (Auto-created)
- `oc_import_history` - Tracks all imports
- `oc_import_errors` - Logs errors

---

## ✨ Key Features

### Supported Entity Types
- 📦 **Products** - Full product data with descriptions, categories, images
- 📁 **Categories** - Hierarchical categories with path structure
- 👥 **Customers** - Customer accounts with addresses
- 🏭 **Manufacturers** - Brand/manufacturer data
- 🛒 **Orders** - Complete order data (advanced)

### Import Modes
- **Insert Only** - Add new records, skip existing
- **Update Only** - Update existing, skip new
- **Upsert** - Insert new OR update existing

### Advanced Features
- ✅ Field mapping with visual preview
- ✅ Batch processing for large files
- ✅ Debug mode with SQL query logging
- ✅ Error handling with detailed logs
- ✅ Import history and statistics
- ✅ Delete existing option
- ✅ CSV and Excel file support

---

## 📝 Usage Example

### Import Categories from CSV

**1. Prepare CSV file:**
```csv
category_id,name,parent_id,status,sort_order
1,Electronics,0,1,1
2,Computers,1,1,1
3,Laptops,2,1,1
4,Desktops,2,1,2
5,Phones,1,1,2
```

**2. Upload in Step 1**
- Browse and select your CSV file
- Click Upload

**3. Map Fields in Step 2**
- Map CSV columns to database fields
- Required fields marked with *
- Preview sample data

**4. Configure in Step 3**
- Entity Type: Category
- Import Mode: Insert Only
- Batch Size: 100
- Debug Mode: On (for testing)
- Click **Start Import**

**5. Verify Results**
- Check **Catalog → Categories**
- View **Import History** for stats
- Review **Debug Log** if issues

---

## 🔧 Import Settings

### Entity Type
Choose what you're importing:
- Product
- Category
- Customer  
- Manufacturer
- Order

### Import Mode
- **Insert Only** - Safe for adding new data
- **Update Only** - Safe for bulk updates
- **Upsert** - Most flexible

### Options
- **Delete Existing** - Clear all data before import (⚠️ USE WITH CAUTION)
- **Ignore Errors** - Continue import on errors
- **Skip First Row** - If CSV has headers
- **Batch Size** - Records per batch (50-500)
- **Debug Mode** - Log SQL queries

---

## 📊 Import History

Track all imports with detailed statistics:
- Total records processed
- Successful inserts
- Successful updates
- Errors encountered
- Import date/time
- File name
- Settings used

**View errors:**
Click any import to see row-by-row error details

---

## 🐛 Debug Mode

Enable to log all SQL queries to:
```
system/storage/logs/import_debug.log
```

**Useful for:**
- Troubleshooting import issues
- Verifying SQL queries
- Understanding data flow
- Checking field mappings

**View log:** Click "View Debug Log" button in settings

**Clear log:** Click "Clear Log" button

---

## ⚠️ Important Notes

### Categories
Categories require proper path structure (`oc_category_path`) to display in admin. This module handles it automatically:
- Top-level: 1 path entry
- Child: 2 path entries  
- Grandchild: 3 path entries

### Products
Products need minimum 3 tables:
- `oc_product` - Main data
- `oc_product_description` - Name/description
- `oc_product_to_store` - Store assignment

### CSV Format
- UTF-8 encoding recommended
- First row can be headers (check "Skip First Row")
- Comma-separated values
- Required fields must not be empty

---

## 🔍 Troubleshooting

### Categories not showing
✅ Enable debug mode  
✅ Check `oc_category_path` entries  
✅ Verify parent_id exists  
✅ Check status = 1

### Import fails
✅ Check CSV encoding (UTF-8)  
✅ Verify required fields mapped  
✅ Enable "Continue on Errors"  
✅ Review debug log  
✅ Check Import History errors

### Slow imports
✅ Reduce batch size to 50  
✅ Disable debug mode  
✅ Check server resources  
✅ Split large files

---

## 📚 Additional Documentation

### README.md
Complete feature documentation and usage guide

### IMPORT_ENTITY_SQL_DOCUMENTATION.md  
Detailed SQL structure for all entity types:
- Exact INSERT statements
- All required tables
- Field descriptions
- Import examples
- Common pitfalls

### INSTALL.sh
Installation reference script

---

## 🆘 Support Checklist

Before asking for help:

1. ✅ Enable debug mode
2. ✅ Check debug log
3. ✅ Review Import History errors
4. ✅ Verify CSV format
5. ✅ Check field mappings
6. ✅ Review IMPORT_ENTITY_SQL_DOCUMENTATION.md
7. ✅ Test with small sample file

---

## 📦 Package Structure

```
import_data_module.ocmod.zip
└── import_data_extension/
    ├── install.json (metadata)
    ├── README.md (guide)
    ├── IMPORT_ENTITY_SQL_DOCUMENTATION.md
    ├── INSTALL.sh
    └── upload/
        └── admin/
            ├── controller/extension/module/import_data.php
            ├── model/extension/module/import_data.php
            ├── language/en-gb/extension/module/import_data.php
            └── view/template/extension/module/
                ├── import_data.tpl
                ├── import_data_mapping.tpl
                └── import_data_settings.tpl
```

---

## ✅ Installation Checklist

- [ ] Downloaded `import_data_module.ocmod.zip`
- [ ] Uploaded via Extensions → Installer
- [ ] Installed via Extensions → Modules
- [ ] Accessed System → Import Data
- [ ] Tested with sample CSV
- [ ] Reviewed documentation
- [ ] Configured debug mode

---

## 🎯 Ready to Use!

Your extension is packaged and ready for installation. Simply upload the ZIP file through OpenCart's Extension Installer and start importing data!

**Location:** `/Users/saqibashraf/Desktop/oc/oc-development/import_data_module.ocmod.zip`

**Next step:** Upload to your OpenCart installation!

---

*Module Version: 1.0.0 | Created: 2026-02-05 | No XML Required ✓*
