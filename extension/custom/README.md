# Registration Upload Field Extension for OpenCart 4

## Overview
This extension adds a file upload field to the customer registration form and provides an admin interface to view all uploaded files with customer context.

## Features
- ✅ Uses **Events** (priority) - No OCMOD/XML modifications needed
- ✅ Automatically creates a custom field for file uploads on installation
- ✅ File upload field appears on customer registration form
- ✅ Admin interface showing list of uploaded files
- ✅ Customer details (name, email, registration date)
- ✅ Direct links to customer profile and file downloads

## Installation

1. Navigate to **Admin Panel → Extensions → Other**
2. Find "Registration Upload Field" in the list
3. Click the **Install** button (green plus icon)
4. The extension will automatically:
   - Create a custom field named "Registration File"
   - Register event handlers
   - Enable the upload field on registration form

## Usage

### For Customers
- Visit the registration page (`/index.php?route=account/register`)
- Fill in registration details
- Use the "Registration File" upload button to attach a file
- Complete registration

### For Administrators
- Go to **Extensions → Other → Registration Upload Field**
- View table showing:
  - Customer name (clickable link to customer profile)
  - Customer email
  - Uploaded filename
  - Registration date
  - Download button for each file

## File Structure

```
extension/custom/
├── install.json
├── admin/
│   ├── controller/other/registration_upload.php
│   ├── model/other/registration_upload.php
│   ├── language/en-gb/other/registration_upload.php
│   └── view/template/other/registration_upload.twig
└── catalog/
    └── controller/event/registration_upload.php
```

## Technical Details

### Event Hooks
- **Trigger**: `view/account/register/before`
- **Action**: `extension/custom/event/registration_upload.addField`
- Ensures the custom field is present in registration form data

### Database
- Uses existing `oc_customer.custom_field` column (JSON format)
- Uses existing `oc_upload` table for file storage
- No custom database tables needed

### Custom Field
- **Type**: File
- **Location**: Account (registration)
- **Status**: Enabled by default
- **Required**: Optional (can be changed in Custom Fields settings)

## Uninstallation

1. Go to **Extensions → Other → Registration Upload Field**
2. Click **Uninstall** button
3. The extension will:
   - Remove event handlers
   - Delete the custom field (if created by extension)
   - Clean up settings

**Note**: Uploaded files in the database remain intact for historical records.

## Customization

### Change File Requirements
Edit the custom field in **Customers → Custom Fields** to:
- Make field required/optional
- Change sort order
- Enable/disable per customer group

### File Upload Settings
Configure in **System → Settings → Store → Option**:
- Maximum file size
- Allowed file extensions
- Upload directory

## Troubleshooting

### Extension doesn't show up
- Verify files are in `/extension/custom/` directory
- Check file permissions (should be readable)
- Clear cache: **Dashboard → Settings → Developer Settings → Theme/SASS cache**

### File upload not working
- Check PHP `upload_max_filesize` and `post_max_size`
- Verify storage directory permissions: `/system/storage/upload/`
- Check OpenCart file upload settings

### Custom field not appearing
- Go to **Customers → Custom Fields**
- Find "Registration File" and ensure Status = Enabled
- Check customer group assignments

## Support

For issues or questions:
1. Check OpenCart error logs: `/system/storage/logs/`
2. Verify PHP error logs
3. Review browser console for JavaScript errors

## Version History

- **1.0.0** (2026-02-12)
  - Initial release
  - Event-based implementation
  - Admin uploads listing interface
  - Customer context integration
