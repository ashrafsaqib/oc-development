# Customer Export Module for OpenCart 2.x

## Description
This module allows administrators to export customer data with comprehensive filtering options, customizable export settings, and support for multiple formats (CSV, XML, JSON).

## Features
- Filter customers by:
  - Customer Group
  - Status (Enabled/Disabled)
  - Approved Status
  - Newsletter Subscription
  - Date Range
  - Search by Name/Email
  
- Export Settings (Collapsible Panel):
  - Select specific columns to export (all selected by default)
  - Limit number of records to export (leave empty for all)
  - Include/exclude IDs option
  - Multiple export formats: CSV, XML, JSON
  
- Export includes:
  - Customer basic information
  - All addresses
  - Order history and total spent
  - Reward points
  - Store credit
  - Wishlist products
  - Login history

## Installation Instructions

### Method 1: Via Extension Installer (Recommended)
1. Download the `customer_export.ocmod.zip` file
2. Go to your OpenCart admin panel
3. Navigate to **Extensions > Installer**
4. Click **Upload** and select the `customer_export.ocmod.zip` file
5. Wait for the upload to complete
6. Go to **Extensions > Modifications**
7. Click the **Refresh** button (top right)
8. The module is now installed

### Method 2: Manual Installation via FTP
1. Extract the `customer_export.ocmod.zip` file
2. Upload the contents of the `upload` folder to your OpenCart root directory via FTP
   - This will place files in their correct locations
3. No database changes are required

## Usage
1. After installation, go to **Extensions > Extensions**
2. Select **Modules** from the dropdown
3. Find **Customer Export** in the list
4. Click **Install** (if not already installed)
5. Access the module via **Extensions > Modules > Customer Export** or through the admin menu

## Accessing the Module
After installation, you can access the Customer Export module at:
```
Admin Panel > Extensions > Modules > Customer Export
```

Or directly via URL:
```
admin/index.php?route=extension/module/customer_export
```

## Using the Export Feature

### Basic Export
1. Set your desired filters (optional)
2. Click the **Filter** button to preview customers
3. Click the **Export to CSV** button to download the data

### Advanced Export Settings
1. Click on **Export Settings** to expand the settings panel
2. **Select Columns**: Choose which data fields to include in the export
3. **Export Limit**: Enter a number to limit records, or leave empty for all
4. **Include IDs**: Check to include customer IDs and related IDs
5. **Export Format**: Choose between CSV, XML, or JSON
6. Click **Export** to download

### Export Format Examples

#### CSV Format
Standard comma-separated values file, compatible with Excel and most spreadsheet applications.

#### XML Format
Structured XML format with customer data in a hierarchical structure:
```xml
<?xml version="1.0" encoding="UTF-8"?>
<customers>
  <customer>
    <customer_id>1</customer_id>
    <firstname>John</firstname>
    <lastname>Doe</lastname>
    ...
  </customer>
</customers>
```

#### JSON Format
JSON format for easy integration with web applications and APIs:
```json
{
  "customers": [
    {
      "customer_id": "1",
      "firstname": "John",
      "lastname": "Doe",
      ...
    }
  ]
}
```

## Exported Data Fields (Available Columns)
- Customer ID
- First Name
- Last Name
- Email
- Telephone
- Fax
- Customer Group
- Status
- Approved
- Safe
- Newsletter
- IP
- Date Added
- Total Orders
- Total Spent
- Total Addresses
- Reward Points
- Store Credit
- Addresses (all addresses concatenated)
- Wishlist Products
- Last Login
- Total Logins

## System Requirements
- OpenCart 2.x
- PHP 5.4 or higher
- MySQL 5.5 or higher

## Support
For support, please contact your development team or create an issue in your project repository.

## Version History
- **1.1.0** (2026-02-04)
  - Added export settings panel (collapsible)
  - Column selection feature
  - Export limit option
  - Include/exclude IDs option
  - Multiple export formats (CSV, XML, JSON)
  
- **1.0.0** (2026-02-04)
  - Initial release
  - Customer list with filtering
  - CSV export with comprehensive data

## License
Custom Development - All Rights Reserved

## Credits
Developed for OpenCart 2.x platform
