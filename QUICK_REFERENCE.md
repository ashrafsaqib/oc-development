# Quick Reference Card - Import Data Module

## 🚀 Quick Start (5 Minutes)

1. **Install Module**
   ```
   Extensions > Extensions > Modules > Import Data > Install
   ```

2. **Access Module**
   ```
   Extensions > Import Data
   ```

3. **Import Sample Data**
   - Upload: `sample_products_import.csv`
   - Entity: Products
   - Mode: Upsert
   - Click: Start Import

## 📋 Files Checklist

```
✓ admin/controller/extension/module/import_data.php
✓ admin/model/extension/module/import_data.php
✓ admin/view/template/extension/module/import_data.tpl
✓ admin/view/template/extension/module/import_data_mapping.tpl
✓ admin/view/template/extension/module/import_data_settings.tpl
✓ admin/language/en-gb/extension/module/import_data.php

Database tables created automatically on install.
```

## 🔑 Key Concepts

### Import Modes
| Mode | New Records | Existing Records |
|------|-------------|------------------|
| Insert | ✅ Add | ⏭️ Skip |
| Update | ⏭️ Skip | ✅ Update |
| Upsert | ✅ Add | ✅ Update |

### Supported Formats
- ✅ CSV (.csv)
- ✅ TXT (.txt)  
- ✅ XLSX (.xlsx)
- ✅ XLS (.xls)

### Supported Entities
1. Products
2. Categories
3. Customers
4. Manufacturers
5. Orders
6. Attributes
7. Options

## 📊 CSV Format Examples

### Products
```csv
model,name,price,quantity,status
PROD-001,"Product Name",19.99,100,1
```

### Categories
```csv
category_id,name,parent_id,status
1,"Electronics",0,1
```

### Customers
```csv
firstname,lastname,email,telephone
John,Doe,john@example.com,1234567890
```

## ⚙️ Settings Quick Reference

### Import Settings
```
Skip First Row:      ✅ (if headers exist)
Delete Existing:     ⚠️ (use with caution!)
Update Existing:     ✅ (recommended)
Continue on Errors:  ✅ (for large imports)
Batch Size:         100 (adjust as needed)
```

## 🎯 Wizard Steps

```
1. UPLOAD    → Select entity & file
2. MAPPING   → Match columns
3. SETTINGS  → Configure options
4. IMPORT    → Execute & review
```

## 🐛 Troubleshooting

### Quick Fixes

**Module Not Visible**
```bash
rm -rf system/storage/cache/*
rm -rf system/storage/modification/*
# Refresh modifications in admin
```

**Upload Fails**
```ini
; In php.ini:
upload_max_filesize = 50M
post_max_size = 50M
max_execution_time = 300
memory_limit = 256M
```

**Import Timeout**
```
1. Reduce batch size (50 instead of 100)
2. Process smaller files
3. Increase max_execution_time
```

## 📁 Required Directories

```bash
mkdir -p system/storage/upload/import
chmod 755 system/storage/upload/import
```

## 🔐 User Permissions

Add to user group:
```
Access:  extension/module/import_data
Modify:  extension/module/import_data
```

## 💾 Database Tables

```sql
oc_import_history  -- Import logs
oc_import_errors   -- Error logs
```

## 📈 Product Fields Reference

### Required
- `model` - Product model/SKU
- `name` - Product name
- `price` - Product price

### Optional
- `quantity` - Stock quantity
- `status` - 1=enabled, 0=disabled
- `sku` - SKU code
- `description` - Product description
- `category_id` - Category ID
- `manufacturer_id` - Manufacturer ID
- `weight` - Product weight
- `image` - Image path

## 🔄 Common Workflows

### Initial Product Import
```
1. Upload → Products
2. Map all columns
3. Settings → Insert Only
4. Import
```

### Update Prices
```
1. Upload → Products
2. Map: product_id, price
3. Settings → Update Only
4. Import
```

### Full Sync
```
1. Upload → Products
2. Map all columns
3. Settings → Upsert + Update Existing
4. Import
```

## 💡 Tips & Best Practices

### Before Import
✅ Backup database
✅ Test with small file first
✅ Verify CSV encoding (UTF-8)
✅ Check required fields

### During Import
✅ Monitor progress
✅ Don't close browser
✅ Check error logs if issues

### After Import
✅ Review statistics
✅ Verify data in frontend
✅ Check error logs
✅ Re-import failed rows

## 🎨 CSV Preparation

### Good CSV
```csv
model,name,price,status
"PROD-001","Product Name",19.99,1
"PROD-002","Product, with comma",29.99,1
```

### Bad CSV
```csv
model;name;price;status          # Wrong delimiter
PROD-001|Product Name|19.99|1    # Wrong delimiter
model,name,price,status
PROD-001,Product Name,19.99      # Missing column
```

## 📞 Support Resources

### Documentation
- [Installation Guide](INSTALLATION_GUIDE.md)
- [User Guide](IMPORT_DATA_MODULE_README.md)
- [Developer Guide](OPENCART_MODULE_DOCUMENTATION.md)
- [Architecture](VISUAL_ARCHITECTURE.md)

### Logs
- OpenCart: `system/storage/logs/error.txt`
- Import: Via module interface
- PHP: Check server logs

### Sample Files
- `sample_products_import.csv`
- `sample_categories_import.csv`

## ⚡ Performance Tuning

### For Large Files (10,000+ rows)

1. **Increase Resources**
   ```ini
   memory_limit = 512M
   max_execution_time = 600
   ```

2. **Optimize Settings**
   ```
   Batch Size: 50
   Continue on Errors: Yes
   ```

3. **Split Files**
   ```
   Split into 5,000 row chunks
   Import separately
   ```

## 🔧 Module Customization

### Add New Entity
1. Edit: `getEntityFields()` in model
2. Add: Import method (e.g., `importYourEntity()`)
3. Update: Language file
4. Test: With sample data

### Change Batch Size Default
```php
// In controller settings() method:
'batch_size' => 50, // Change from 100
```

## 📝 Column Mapping Tips

### Auto-Map Works Best With
- Exact column names (e.g., `price`)
- Underscored names (e.g., `product_id`)
- Common variations

### Manual Mapping Needed For
- Custom column names
- Abbreviated names
- Non-English headers

## 🎓 Learning Resources

### Study Order
1. Read: INSTALLATION_GUIDE.md
2. Install: Follow guide
3. Test: Use sample files
4. Learn: OPENCART_MODULE_DOCUMENTATION.md
5. Customize: Modify for needs

### Key Files to Study
1. Controller: Request handling
2. Model: Business logic
3. View: UI templates
4. Language: Translations

## 🏆 Success Metrics

### Successful Import Shows
✅ Green success message
✅ Statistics display correctly
✅ Records appear in database
✅ No critical errors
✅ Import history recorded

### Check After Import
✅ Admin product list
✅ Frontend product display
✅ Import history page
✅ Error logs (if any)

## 🚨 Common Mistakes

❌ Not backing up database
❌ Wrong CSV encoding (not UTF-8)
❌ Missing required fields
❌ Incorrect column mapping
❌ Using Delete All without backup
❌ Not testing with small file first
❌ Closing browser during import

## ✅ Pre-Import Checklist

- [ ] Database backed up
- [ ] CSV file UTF-8 encoded
- [ ] Headers match format
- [ ] Required fields present
- [ ] Test file created (10 rows)
- [ ] Entity type selected
- [ ] Column mapping reviewed
- [ ] Import mode chosen
- [ ] Settings configured

## 📊 Statistics Legend

```
Inserted: New records added
Updated:  Existing records modified
Errors:   Failed records
Total:    Inserted + Updated + Errors
```

## 🎯 Quick Commands

### Clear Cache
```bash
cd /path/to/opencart
rm -rf system/storage/cache/*
rm -rf system/storage/modification/*
```

### Check Logs
```bash
tail -f system/storage/logs/error.txt
```

### Fix Permissions
```bash
chmod 755 system/storage/upload/import
chown www-data:www-data system/storage/upload/import
```

## 📞 Getting Help

1. ✅ Check documentation
2. ✅ Review error logs
3. ✅ Test with sample files
4. ✅ Verify file format
5. ✅ Check PHP settings
6. ✅ Review OpenCart forums

---

## Quick Action Matrix

| Need | Go To | Action |
|------|-------|--------|
| Install | Extensions > Modules | Click Install |
| Import | Extensions > Import Data | Follow wizard |
| History | Module > Recent Imports | View list |
| Errors | Import Results | Check logs |
| Help | Documentation files | Read guide |

---

**Keep this card handy for quick reference during imports!**

**Version**: 1.0.0  
**Module**: Import Data Module for OpenCart 2.x
