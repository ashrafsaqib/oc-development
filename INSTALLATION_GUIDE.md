# Import Data Module - Installation Guide

## Quick Installation Steps

### 1. Verify Files
Ensure all module files are in the correct locations:

```
✓ admin/controller/extension/module/import_data.php
✓ admin/model/extension/module/import_data.php
✓ admin/view/template/extension/module/import_data.tpl
✓ admin/view/template/extension/module/import_data_mapping.tpl
✓ admin/view/template/extension/module/import_data_settings.tpl
✓ admin/language/en-gb/extension/module/import_data.php
```

### 2. Create Upload Directory
The module needs a directory for temporary file storage:

```bash
mkdir -p system/storage/upload/import
chmod 755 system/storage/upload/import
```

Or via FTP:
1. Navigate to `system/storage/upload/`
2. Create folder named `import`
3. Set permissions to 755 (rwxr-xr-x)

### 3. Install Module via Admin Panel

**Step-by-Step:**

1. **Login to Admin**
   - Go to your OpenCart admin URL
   - Enter your admin credentials

2. **Navigate to Extensions**
   - Click `Extensions` in the left menu
   - Click `Extensions` again in submenu
   
3. **Filter Modules**
   - In the dropdown at top, select `Modules`
   - Wait for module list to load

4. **Find Import Data Module**
   - Scroll down or search for "Import Data"
   - You should see "Import Data Module" in the list

5. **Install**
   - Click the green `Install` button (+ icon)
   - Database tables are created automatically via install() method
   - Wait for success message

6. **Verify Installation**
   - You should see `Edit` and `Uninstall` buttons appear
   - Check that database tables were created

### 4. Access the Module

**Option 1: Via Extensions Menu**
- Go to `Extensions > Import Data`

**Option 2: Direct URL**
- Navigate to: `yoursite.com/admin/index.php?route=extension/module/import_data&token=YOUR_TOKEN`

### 5. Test Installation

1. **Access Module**
   - Click on Import Data in admin menu

2. **Upload Test File**
   - Use the provided `sample_products_import.csv`
   - Select "Products" as entity type
   - Click Continue

3. **Verify Mapping**
   - Check that columns are auto-mapped
   - Click Continue

4. **Test Import**
   - Select "Insert Only" mode
   - Enable "Continue on Errors"
   - Click "Start Import"

5. **Check Results**
   - Verify statistics show correct numbers
   - Check that products were imported
   - Review any errors

## Database Setup

### Tables Created Automatically

When you install the module, these tables are created:

#### 1. oc_import_history
```sql
CREATE TABLE IF NOT EXISTS `oc_import_history` (
    `import_id` int(11) NOT NULL AUTO_INCREMENT,
    `entity_type` varchar(50) NOT NULL,
    `filename` varchar(255) NOT NULL,
    `records_total` int(11) NOT NULL DEFAULT '0',
    `records_inserted` int(11) NOT NULL DEFAULT '0',
    `records_updated` int(11) NOT NULL DEFAULT '0',
    `records_errors` int(11) NOT NULL DEFAULT '0',
    `import_mode` varchar(20) NOT NULL,
    `settings` text,
    `status` varchar(20) NOT NULL DEFAULT 'pending',
    `error_log` text,
    `date_started` datetime NOT NULL,
    `date_completed` datetime DEFAULT NULL,
    PRIMARY KEY (`import_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
```

#### 2. oc_import_errors
```sql
CREATE TABLE IF NOT EXISTS `oc_import_errors` (
    `error_id` int(11) NOT NULL AUTO_INCREMENT,
    `import_id` int(11) NOT NULL,
    `row_number` int(11) NOT NULL,
    `error_message` text NOT NULL,
    `row_data` text,
    `date_added` datetime NOT NULL,
    PRIMARY KEY (`error_id`),
    KEY `import_id` (`import_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
```

### Manual Database Setup (If Needed)

If tables aren't created automatically:

1. **Access phpMyAdmin**
   - Login to your hosting control panel
   - Open phpMyAdmin
   - Select your OpenCart database

2. **Run SQL**
   - Click the SQL tab
   - Copy and paste the CREATE TABLE statements above
   - Replace `oc_` with your actual DB_PREFIX (check config.php)
   - Click Go

## Troubleshooting Installation

### Module Not Appearing in List

**Check 1: File Names**
```bash
# Controller file name must match class name
File: import_data.php
Class: ControllerExtensionModuleImportData
```

**Check 2: File Permissions**
```bash
# Ensure files are readable
chmod 644 admin/controller/extension/module/import_data.php
chmod 644 admin/model/extension/module/import_data.php
chmod 644 admin/view/template/extension/module/*.tpl
chmod 644 admin/language/en-gb/extension/module/import_data.php
```

**Check 3: Clear Cache**
- Delete everything in `system/storage/cache/`
- Delete everything in `system/storage/modification/`
- Refresh modifications: `Extensions > Modifications > Refresh`

### Install Button Not Working

**Check 1: User Permissions**
- Ensure your admin user has permission to install modules
- Go to `System > Users > User Groups`
- Edit your user group
- Ensure "extension/module" is in Access Permission and Modify Permission

**Check 2: Database Permissions**
- Verify database user has CREATE TABLE permission
- Check database connection in `admin/config.php`

### Database Tables Not Created

**Manual Creation:**
1. Open phpMyAdmin
2. Select OpenCart database
3. Run the SQL statements provided above
4. Verify tables exist with correct prefix

### Upload Directory Errors

**Create Directory:**
```bash
# Via SSH
cd /path/to/opencart
mkdir -p system/storage/upload/import
chmod 755 system/storage/upload/import
chown www-data:www-data system/storage/upload/import

# Via FTP
# Create folder: system/storage/upload/import
# Set permissions: 755
```

## Post-Installation Configuration

### 1. PHP Settings

Check your php.ini for these settings:

```ini
upload_max_filesize = 50M
post_max_size = 50M
max_execution_time = 300
memory_limit = 256M
```

### 2. Module Settings

Access module settings to configure:
- Default batch size
- Maximum file size
- Allowed file types
- Error handling preferences

### 3. User Permissions

Grant module access to specific user groups:

1. Go to `System > Users > User Groups`
2. Edit each user group that needs access
3. Add to **Access Permission**:
   - `extension/module/import_data`
4. Add to **Modify Permission**:
   - `extension/module/import_data`

## Verification Checklist

After installation, verify:

- [ ] Module appears in Extensions > Modules list
- [ ] Install button works without errors
- [ ] Module is accessible from menu
- [ ] Upload directory exists and is writable
- [ ] Database tables are created
- [ ] Test file upload works
- [ ] Column mapping displays correctly
- [ ] Sample import completes successfully
- [ ] Import history is recorded
- [ ] Error logging functions properly

## Sample Files for Testing

Use these sample files to test your installation:

1. **sample_products_import.csv**
   - 10 sample products
   - Tests product import functionality

2. **sample_categories_import.csv**
   - 7 sample categories
   - Tests category hierarchy

## Uninstallation

To remove the module:

1. **Uninstall via Admin**
   - Go to Extensions > Extensions > Modules
   - Find "Import Data Module"
   - Click Uninstall button
   - Database tables are automatically dropped via uninstall() method

2. **Remove Files (Optional)**
   ```bash
   rm admin/controller/extension/module/import_data.php
   rm admin/model/extension/module/import_data.php
   rm admin/view/template/extension/module/import_data*.tpl
   rm admin/language/en-gb/extension/module/import_data.php
   ```

**Note:** Database tables ARE automatically deleted on uninstall. Backup your data before uninstalling if you need to preserve import history.

## Getting Help

### Check Logs
1. OpenCart error log: `system/storage/logs/error.txt`
2. Import errors: Via module interface
3. PHP error log: Check server logs

### Common Issues
- File upload fails → Check PHP settings
- Module not visible → Clear cache
- Database errors → Check permissions
- Import hangs → Reduce batch size

### Support Resources
- OpenCart Forums: https://forum.opencart.com
- Documentation: See OPENCART_MODULE_DOCUMENTATION.md
- GitHub Issues: [Your repository URL]

---

**Installation Complete!**

You're now ready to use the Import Data Module. Start by importing the sample CSV files to familiarize yourself with the interface.

For detailed usage instructions, see [IMPORT_DATA_MODULE_README.md](IMPORT_DATA_MODULE_README.md)
