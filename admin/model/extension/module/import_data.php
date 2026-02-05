<?php
/**
 * Import Data Module - Model
 * Handles all database operations and file parsing for import
 */
class ModelExtensionModuleImportData extends Model {
    
    private $debug_mode = false;
    private $debug_log_file = '';
    
    /**
     * Enable debug mode and set log file
     */
    private function enableDebugMode() {
        $this->debug_mode = true;
        $this->debug_log_file = DIR_LOGS . 'import_debug.log';
    }
    
    /**
     * Log debug information
     */
    private function debugLog($message, $data = null) {
        if (!$this->debug_mode) {
            return;
        }
        
        $log_message = '[' . date('Y-m-d H:i:s') . '] ' . $message;
        
        if ($data !== null) {
            $log_message .= "\n" . print_r($data, true);
        }
        
        $log_message .= "\n" . str_repeat('-', 80) . "\n";
        
        file_put_contents($this->debug_log_file, $log_message, FILE_APPEND);
    }
    
    /**
     * Log SQL query for debugging
     */
    private function debugQuery($sql) {
        if ($this->debug_mode) {
            $this->debugLog('SQL Query:', $sql);
        }
        return $this->db->query($sql);
    }
    
    /**
     * Install module - create necessary database tables
     */
    public function install() {
        // Create import history table
        $this->db->query("
            CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "import_history` (
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
        ");

        // Create import error log table
        $this->db->query("
            CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "import_errors` (
                `error_id` int(11) NOT NULL AUTO_INCREMENT,
                `import_id` int(11) NOT NULL,
                `row_number` int(11) NOT NULL,
                `error_message` text NOT NULL,
                `row_data` text,
                `date_added` datetime NOT NULL,
                PRIMARY KEY (`error_id`),
                KEY `import_id` (`import_id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8;
        ");
    }

    /**
     * Uninstall module - remove tables
     */
    public function uninstall() {
        // Drop tables on uninstall
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "import_history`");
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "import_errors`");
    }

    /**
     * Parse file headers from uploaded file
     */
    public function parseFileHeaders($filepath, $extension) {
        $headers = array();

        if ($extension == 'csv' || $extension == 'txt') {
            if (($handle = fopen($filepath, 'r')) !== FALSE) {
                $headers = fgetcsv($handle, 0, ',');
                fclose($handle);
            }
        } elseif ($extension == 'xlsx' || $extension == 'xls') {
            // For Excel files, you would need PHPExcel or PhpSpreadsheet library
            // This is a simplified version
            if (file_exists(DIR_SYSTEM . 'library/PHPExcel.php')) {
                require_once(DIR_SYSTEM . 'library/PHPExcel.php');
                try {
                    $objPHPExcel = PHPExcel_IOFactory::load($filepath);
                    $worksheet = $objPHPExcel->getActiveSheet();
                    $headers = $worksheet->rangeToArray('A1:' . $worksheet->getHighestColumn() . '1')[0];
                } catch (Exception $e) {
                    return false;
                }
            } else {
                // Fallback: treat as CSV
                if (($handle = fopen($filepath, 'r')) !== FALSE) {
                    $headers = fgetcsv($handle, 0, ',');
                    fclose($handle);
                }
            }
        }

        return $headers;
    }

    /**
     * Get sample data from file
     */
    public function getSampleData($filepath, $extension, $rows = 5) {
        $data = array();

        if ($extension == 'csv' || $extension == 'txt') {
            if (($handle = fopen($filepath, 'r')) !== FALSE) {
                $row_count = 0;
                while (($row = fgetcsv($handle, 0, ',')) !== FALSE && $row_count < $rows + 1) {
                    if ($row_count > 0) { // Skip header row
                        $data[] = $row;
                    }
                    $row_count++;
                }
                fclose($handle);
            }
        } elseif ($extension == 'xlsx' || $extension == 'xls') {
            if (file_exists(DIR_SYSTEM . 'library/PHPExcel.php')) {
                require_once(DIR_SYSTEM . 'library/PHPExcel.php');
                try {
                    $objPHPExcel = PHPExcel_IOFactory::load($filepath);
                    $worksheet = $objPHPExcel->getActiveSheet();
                    $highestRow = min($rows + 1, $worksheet->getHighestRow());
                    
                    for ($row = 2; $row <= $highestRow; $row++) {
                        $rowData = $worksheet->rangeToArray('A' . $row . ':' . $worksheet->getHighestColumn() . $row)[0];
                        $data[] = $rowData;
                    }
                } catch (Exception $e) {
                    return array();
                }
            }
        }

        return $data;
    }

    /**
     * Get database fields for entity type
     */
    public function getEntityFields($entity_type) {
        $fields = array();

        switch ($entity_type) {
            case 'product':
                $fields = array(
                    'product_id' => 'Product ID',
                    'model' => 'Model',
                    'sku' => 'SKU',
                    'upc' => 'UPC',
                    'ean' => 'EAN',
                    'jan' => 'JAN',
                    'isbn' => 'ISBN',
                    'mpn' => 'MPN',
                    'location' => 'Location',
                    'quantity' => 'Quantity',
                    'stock_status_id' => 'Stock Status ID',
                    'image' => 'Image',
                    'manufacturer_id' => 'Manufacturer ID',
                    'shipping' => 'Requires Shipping',
                    'price' => 'Price',
                    'points' => 'Points',
                    'tax_class_id' => 'Tax Class ID',
                    'date_available' => 'Date Available',
                    'weight' => 'Weight',
                    'weight_class_id' => 'Weight Class ID',
                    'length' => 'Length',
                    'width' => 'Width',
                    'height' => 'Height',
                    'length_class_id' => 'Length Class ID',
                    'subtract' => 'Subtract Stock',
                    'minimum' => 'Minimum Quantity',
                    'sort_order' => 'Sort Order',
                    'status' => 'Status',
                    'name' => 'Product Name',
                    'description' => 'Description',
                    'tag' => 'Tags',
                    'meta_title' => 'Meta Title',
                    'meta_description' => 'Meta Description',
                    'meta_keyword' => 'Meta Keywords',
                    'category_id' => 'Category ID',
                    'category_ids' => 'Category IDs (comma separated)'
                );
                break;

            case 'category':
                $fields = array(
                    'category_id' => 'Category ID',
                    'parent_id' => 'Parent ID',
                    'top' => 'Top Category',
                    'column' => 'Columns',
                    'sort_order' => 'Sort Order',
                    'status' => 'Status',
                    'name' => 'Category Name',
                    'description' => 'Description',
                    'meta_title' => 'Meta Title',
                    'meta_description' => 'Meta Description',
                    'meta_keyword' => 'Meta Keywords'
                );
                break;

            case 'customer':
                $fields = array(
                    'customer_id' => 'Customer ID',
                    'customer_group_id' => 'Customer Group ID',
                    'firstname' => 'First Name',
                    'lastname' => 'Last Name',
                    'email' => 'Email',
                    'telephone' => 'Telephone',
                    'fax' => 'Fax',
                    'password' => 'Password',
                    'newsletter' => 'Newsletter',
                    'status' => 'Status',
                    'approved' => 'Approved',
                    'safe' => 'Safe'
                );
                break;

            case 'manufacturer':
                $fields = array(
                    'manufacturer_id' => 'Manufacturer ID',
                    'name' => 'Name',
                    'image' => 'Image',
                    'sort_order' => 'Sort Order'
                );
                break;

            case 'order':
                $fields = array(
                    'order_id' => 'Order ID',
                    'invoice_no' => 'Invoice Number',
                    'invoice_prefix' => 'Invoice Prefix',
                    'store_id' => 'Store ID',
                    'store_name' => 'Store Name',
                    'store_url' => 'Store URL',
                    'customer_id' => 'Customer ID',
                    'customer_group_id' => 'Customer Group ID',
                    'firstname' => 'First Name',
                    'lastname' => 'Last Name',
                    'email' => 'Email',
                    'telephone' => 'Telephone',
                    'payment_firstname' => 'Payment First Name',
                    'payment_lastname' => 'Payment Last Name',
                    'payment_address_1' => 'Payment Address 1',
                    'payment_city' => 'Payment City',
                    'payment_postcode' => 'Payment Postcode',
                    'payment_country' => 'Payment Country',
                    'payment_method' => 'Payment Method',
                    'shipping_firstname' => 'Shipping First Name',
                    'shipping_lastname' => 'Shipping Last Name',
                    'shipping_address_1' => 'Shipping Address 1',
                    'shipping_city' => 'Shipping City',
                    'shipping_postcode' => 'Shipping Postcode',
                    'shipping_country' => 'Shipping Country',
                    'shipping_method' => 'Shipping Method',
                    'total' => 'Total',
                    'order_status_id' => 'Order Status ID',
                    'currency_code' => 'Currency Code',
                    'currency_value' => 'Currency Value'
                );
                break;

            case 'attribute':
                $fields = array(
                    'attribute_id' => 'Attribute ID',
                    'attribute_group_id' => 'Attribute Group ID',
                    'sort_order' => 'Sort Order',
                    'name' => 'Attribute Name'
                );
                break;

            case 'option':
                $fields = array(
                    'option_id' => 'Option ID',
                    'type' => 'Type',
                    'sort_order' => 'Sort Order',
                    'name' => 'Option Name'
                );
                break;

            default:
                $fields = array();
                break;
        }

        return $fields;
    }

    /**
     * Process the import
     */
    public function processImport($import_data) {
        // Enable debug mode if requested
        if (isset($import_data['settings']['debug_mode']) && $import_data['settings']['debug_mode']) {
            $this->enableDebugMode();
            $this->debugLog('=== IMPORT STARTED ===', array(
                'entity_type' => $import_data['entity_type'],
                'settings' => $import_data['settings'],
                'mapping' => $import_data['mapping']
            ));
        }
        
        $result = array(
            'success' => false,
            'inserted' => 0,
            'updated' => 0,
            'errors' => 0,
            'error_messages' => array()
        );

        // Create import history record
        $import_id = $this->createImportHistory($import_data);

        try {
            // Delete existing records if requested
            if ($import_data['settings']['delete_existing']) {
                $this->deleteExistingRecords($import_data['entity_type']);
            }

            // Read and process file
            $rows = $this->readFileData(
                $import_data['filepath'],
                $import_data['file_extension'],
                $import_data['settings']['skip_first_row']
            );

            $batch_size = $import_data['settings']['batch_size'];
            $batch = array();
            $row_number = 1;

            foreach ($rows as $row) {
                try {
                    // Map row data to database fields
                    $mapped_data = $this->mapRowData($row, $import_data['mapping']);

                    // Import based on mode
                    $import_result = $this->importRow(
                        $import_data['entity_type'],
                        $mapped_data,
                        $import_data['settings']
                    );

                    if ($import_result['success']) {
                        if ($import_result['action'] == 'insert') {
                            $result['inserted']++;
                        } elseif ($import_result['action'] == 'update') {
                            $result['updated']++;
                        }
                    } else {
                        $result['errors']++;
                        $this->logImportError($import_id, $row_number, $import_result['error'], $row);
                        
                        if (!$import_data['settings']['ignore_errors']) {
                            throw new Exception($import_result['error']);
                        }
                    }
                } catch (Exception $e) {
                    $result['errors']++;
                    $this->logImportError($import_id, $row_number, $e->getMessage(), $row);
                    
                    if (!$import_data['settings']['ignore_errors']) {
                        throw $e;
                    }
                }

                $row_number++;
            }

            $result['success'] = true;
            $this->updateImportHistory($import_id, 'completed', $result);

        } catch (Exception $e) {
            $result['error'] = $e->getMessage();
            $this->updateImportHistory($import_id, 'failed', $result);
        }

        return $result;
    }

    /**
     * Read all data from file
     */
    private function readFileData($filepath, $extension, $skip_first_row = true) {
        $data = array();

        if ($extension == 'csv' || $extension == 'txt') {
            if (($handle = fopen($filepath, 'r')) !== FALSE) {
                $row_count = 0;
                while (($row = fgetcsv($handle, 0, ',')) !== FALSE) {
                    if ($skip_first_row && $row_count == 0) {
                        $row_count++;
                        continue;
                    }
                    $data[] = $row;
                    $row_count++;
                }
                fclose($handle);
            }
        } elseif ($extension == 'xlsx' || $extension == 'xls') {
            if (file_exists(DIR_SYSTEM . 'library/PHPExcel.php')) {
                require_once(DIR_SYSTEM . 'library/PHPExcel.php');
                try {
                    $objPHPExcel = PHPExcel_IOFactory::load($filepath);
                    $worksheet = $objPHPExcel->getActiveSheet();
                    $highestRow = $worksheet->getHighestRow();
                    $start_row = $skip_first_row ? 2 : 1;
                    
                    for ($row = $start_row; $row <= $highestRow; $row++) {
                        $rowData = $worksheet->rangeToArray('A' . $row . ':' . $worksheet->getHighestColumn() . $row)[0];
                        $data[] = $rowData;
                    }
                } catch (Exception $e) {
                    throw new Exception('Error reading Excel file: ' . $e->getMessage());
                }
            }
        }

        return $data;
    }

    /**
     * Map row data to database fields
     */
    private function mapRowData($row, $mapping) {
        $mapped_data = array();

        foreach ($mapping as $file_column => $db_field) {
            if (!empty($db_field) && isset($row[$file_column])) {
                $mapped_data[$db_field] = $row[$file_column];
            }
        }

        return $mapped_data;
    }

    /**
     * Import a single row
     */
    private function importRow($entity_type, $data, $settings) {
        $result = array('success' => false, 'action' => '', 'error' => '');

        switch ($entity_type) {
            case 'product':
                $result = $this->importProduct($data, $settings);
                break;
            case 'category':
                $result = $this->importCategory($data, $settings);
                break;
            case 'customer':
                $result = $this->importCustomer($data, $settings);
                break;
            case 'manufacturer':
                $result = $this->importManufacturer($data, $settings);
                break;
            default:
                $result['error'] = 'Unsupported entity type';
                break;
        }

        return $result;
    }

    /**
     * Import product
     */
    private function importProduct($data, $settings) {
        $result = array('success' => false, 'action' => '', 'error' => '');

        try {
            // Check if product exists
            $product_id = isset($data['product_id']) ? (int)$data['product_id'] : 0;
            $exists = false;
            
            // Check if product exists in database
            if ($product_id > 0) {
                $query = $this->debugQuery("SELECT product_id FROM " . DB_PREFIX . "product WHERE product_id = '" . (int)$product_id . "'");
                if ($query->num_rows) {
                    $exists = true;
                }
            }
            
            if (!$exists && isset($data['model'])) {
                // Try to find by model
                $query = $this->debugQuery("SELECT product_id FROM " . DB_PREFIX . "product WHERE model = '" . $this->db->escape($data['model']) . "'");
                if ($query->num_rows) {
                    $product_id = $query->row['product_id'];
                    $exists = true;
                }
            }

            // Determine action based on import mode
            $import_mode = isset($settings['import_mode']) ? $settings['import_mode'] : 'insert';
            
            if ($import_mode == 'insert') {
                // Insert Only mode - only insert new records
                if (!$exists) {
                    $product_id = $this->insertProduct($data);
                    $result['success'] = true;
                    $result['action'] = 'insert';
                } else {
                    $result['success'] = true;
                    $result['action'] = 'skip';
                }
            } elseif ($import_mode == 'update') {
                // Update Only mode - only update existing records
                if ($exists) {
                    $this->updateProduct($product_id, $data);
                    $result['success'] = true;
                    $result['action'] = 'update';
                } else {
                    $result['success'] = true;
                    $result['action'] = 'skip';
                }
            } elseif ($import_mode == 'upsert') {
                // Upsert mode - insert new or update existing
                if ($exists) {
                    $this->updateProduct($product_id, $data);
                    $result['success'] = true;
                    $result['action'] = 'update';
                } else {
                    $product_id = $this->insertProduct($data);
                    $result['success'] = true;
                    $result['action'] = 'insert';
                }
            }

        } catch (Exception $e) {
            $result['error'] = $e->getMessage();
        }

        return $result;
    }

    /**
     * Insert new product
     */
    private function insertProduct($data) {
        // Build INSERT with optional product_id
        $sql = "INSERT INTO " . DB_PREFIX . "product SET ";
        
        // If product_id provided, use it (otherwise auto-increment)
        if (isset($data['product_id']) && $data['product_id']) {
            $sql .= "product_id = '" . (int)$data['product_id'] . "', ";
        }
        
        $sql .= "
            model = '" . $this->db->escape(isset($data['model']) ? $data['model'] : '') . "',
            sku = '" . $this->db->escape(isset($data['sku']) ? $data['sku'] : '') . "',
            upc = '" . $this->db->escape(isset($data['upc']) ? $data['upc'] : '') . "',
            ean = '" . $this->db->escape(isset($data['ean']) ? $data['ean'] : '') . "',
            jan = '" . $this->db->escape(isset($data['jan']) ? $data['jan'] : '') . "',
            isbn = '" . $this->db->escape(isset($data['isbn']) ? $data['isbn'] : '') . "',
            mpn = '" . $this->db->escape(isset($data['mpn']) ? $data['mpn'] : '') . "',
            location = '" . $this->db->escape(isset($data['location']) ? $data['location'] : '') . "',
            quantity = '" . (int)(isset($data['quantity']) ? $data['quantity'] : 0) . "',
            stock_status_id = '" . (int)(isset($data['stock_status_id']) ? $data['stock_status_id'] : 5) . "',
            image = '" . $this->db->escape(isset($data['image']) ? $data['image'] : '') . "',
            manufacturer_id = '" . (int)(isset($data['manufacturer_id']) ? $data['manufacturer_id'] : 0) . "',
            shipping = '" . (int)(isset($data['shipping']) ? $data['shipping'] : 1) . "',
            price = '" . (float)(isset($data['price']) ? $data['price'] : 0) . "',
            points = '" . (int)(isset($data['points']) ? $data['points'] : 0) . "',
            tax_class_id = '" . (int)(isset($data['tax_class_id']) ? $data['tax_class_id'] : 0) . "',
            date_available = '" . $this->db->escape(isset($data['date_available']) ? $data['date_available'] : date('Y-m-d')) . "',
            weight = '" . (float)(isset($data['weight']) ? $data['weight'] : 0) . "',
            weight_class_id = '" . (int)(isset($data['weight_class_id']) ? $data['weight_class_id'] : 1) . "',
            length = '" . (float)(isset($data['length']) ? $data['length'] : 0) . "',
            width = '" . (float)(isset($data['width']) ? $data['width'] : 0) . "',
            height = '" . (float)(isset($data['height']) ? $data['height'] : 0) . "',
            length_class_id = '" . (int)(isset($data['length_class_id']) ? $data['length_class_id'] : 1) . "',
            subtract = '" . (int)(isset($data['subtract']) ? $data['subtract'] : 1) . "',
            minimum = '" . (int)(isset($data['minimum']) ? $data['minimum'] : 1) . "',
            sort_order = '" . (int)(isset($data['sort_order']) ? $data['sort_order'] : 0) . "',
            status = '" . (int)(isset($data['status']) ? $data['status'] : 1) . "',
            viewed = '0',
            date_added = NOW(),
            date_modified = NOW()
        ";
        
        $this->debugQuery($sql);

        // Get the inserted product_id (either provided or auto-generated)
        $product_id = isset($data['product_id']) && $data['product_id'] ? (int)$data['product_id'] : $this->db->getLastId();

        // Insert product description
        $this->debugQuery("INSERT INTO " . DB_PREFIX . "product_description SET 
            product_id = '" . (int)$product_id . "',
            language_id = '" . (int)$this->config->get('config_language_id') . "',
            name = '" . $this->db->escape(isset($data['name']) ? $data['name'] : '') . "',
            description = '" . $this->db->escape(isset($data['description']) ? $data['description'] : '') . "',
            tag = '" . $this->db->escape(isset($data['tag']) ? $data['tag'] : '') . "',
            meta_title = '" . $this->db->escape(isset($data['meta_title']) ? $data['meta_title'] : (isset($data['name']) ? $data['name'] : '')) . "',
            meta_description = '" . $this->db->escape(isset($data['meta_description']) ? $data['meta_description'] : '') . "',
            meta_keyword = '" . $this->db->escape(isset($data['meta_keyword']) ? $data['meta_keyword'] : '') . "'
        ");

        // Insert to store
        $this->debugQuery("INSERT INTO " . DB_PREFIX . "product_to_store SET 
            product_id = '" . (int)$product_id . "',
            store_id = '0'
        ");

        // Handle categories
        if (isset($data['category_id']) || isset($data['category_ids'])) {
            $category_ids = array();
            
            if (isset($data['category_ids'])) {
                $category_ids = explode(',', $data['category_ids']);
            } elseif (isset($data['category_id'])) {
                $category_ids[] = $data['category_id'];
            }

            foreach ($category_ids as $category_id) {
                $category_id = trim($category_id);
                if ($category_id) {
                    $this->debugQuery("INSERT INTO " . DB_PREFIX . "product_to_category SET 
                        product_id = '" . (int)$product_id . "',
                        category_id = '" . (int)$category_id . "'
                    ");
                }
            }
        }

        return $product_id;
    }

    /**
     * Update existing product
     */
    private function updateProduct($product_id, $data) {
        $sql = "UPDATE " . DB_PREFIX . "product SET date_modified = NOW()";
        
        if (isset($data['model'])) $sql .= ", model = '" . $this->db->escape($data['model']) . "'";
        if (isset($data['sku'])) $sql .= ", sku = '" . $this->db->escape($data['sku']) . "'";
        if (isset($data['upc'])) $sql .= ", upc = '" . $this->db->escape($data['upc']) . "'";
        if (isset($data['ean'])) $sql .= ", ean = '" . $this->db->escape($data['ean']) . "'";
        if (isset($data['jan'])) $sql .= ", jan = '" . $this->db->escape($data['jan']) . "'";
        if (isset($data['isbn'])) $sql .= ", isbn = '" . $this->db->escape($data['isbn']) . "'";
        if (isset($data['mpn'])) $sql .= ", mpn = '" . $this->db->escape($data['mpn']) . "'";
        if (isset($data['location'])) $sql .= ", location = '" . $this->db->escape($data['location']) . "'";
        if (isset($data['quantity'])) $sql .= ", quantity = '" . (int)$data['quantity'] . "'";
        if (isset($data['stock_status_id'])) $sql .= ", stock_status_id = '" . (int)$data['stock_status_id'] . "'";
        if (isset($data['image'])) $sql .= ", image = '" . $this->db->escape($data['image']) . "'";
        if (isset($data['manufacturer_id'])) $sql .= ", manufacturer_id = '" . (int)$data['manufacturer_id'] . "'";
        if (isset($data['shipping'])) $sql .= ", shipping = '" . (int)$data['shipping'] . "'";
        if (isset($data['price'])) $sql .= ", price = '" . (float)$data['price'] . "'";
        if (isset($data['points'])) $sql .= ", points = '" . (int)$data['points'] . "'";
        if (isset($data['tax_class_id'])) $sql .= ", tax_class_id = '" . (int)$data['tax_class_id'] . "'";
        if (isset($data['date_available'])) $sql .= ", date_available = '" . $this->db->escape($data['date_available']) . "'";
        if (isset($data['weight'])) $sql .= ", weight = '" . (float)$data['weight'] . "'";
        if (isset($data['weight_class_id'])) $sql .= ", weight_class_id = '" . (int)$data['weight_class_id'] . "'";
        if (isset($data['length'])) $sql .= ", length = '" . (float)$data['length'] . "'";
        if (isset($data['width'])) $sql .= ", width = '" . (float)$data['width'] . "'";
        if (isset($data['height'])) $sql .= ", height = '" . (float)$data['height'] . "'";
        if (isset($data['length_class_id'])) $sql .= ", length_class_id = '" . (int)$data['length_class_id'] . "'";
        if (isset($data['subtract'])) $sql .= ", subtract = '" . (int)$data['subtract'] . "'";
        if (isset($data['minimum'])) $sql .= ", minimum = '" . (int)$data['minimum'] . "'";
        if (isset($data['sort_order'])) $sql .= ", sort_order = '" . (int)$data['sort_order'] . "'";
        if (isset($data['status'])) $sql .= ", status = '" . (int)$data['status'] . "'";
        
        $sql .= " WHERE product_id = '" . (int)$product_id . "'";
        
        $this->debugQuery($sql);

        // Update description if provided
        if (isset($data['name']) || isset($data['description'])) {
            $desc_sql = "UPDATE " . DB_PREFIX . "product_description SET ";
            $updates = array();
            
            if (isset($data['name'])) $updates[] = "name = '" . $this->db->escape($data['name']) . "'";
            if (isset($data['description'])) $updates[] = "description = '" . $this->db->escape($data['description']) . "'";
            if (isset($data['meta_title'])) $updates[] = "meta_title = '" . $this->db->escape($data['meta_title']) . "'";
            
            if ($updates) {
                $desc_sql .= implode(', ', $updates);
                $desc_sql .= " WHERE product_id = '" . (int)$product_id . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'";
                $this->debugQuery($desc_sql);
            }
        }
    }

    /**
     * Import category
     */
    private function importCategory($data, $settings) {
        $result = array('success' => false, 'action' => '', 'error' => '');

        try {
            // Check if category exists
            $category_id = isset($data['category_id']) ? (int)$data['category_id'] : 0;
            $exists = false;
            
            // Check if category exists in database
            if ($category_id > 0) {
                $query = $this->debugQuery("SELECT category_id FROM " . DB_PREFIX . "category WHERE category_id = '" . (int)$category_id . "'");
                if ($query->num_rows) {
                    $exists = true;
                }
            }
            
            if (!$exists && isset($data['name'])) {
                // Try to find by name
                $query = $this->debugQuery("SELECT category_id FROM " . DB_PREFIX . "category_description WHERE name = '" . $this->db->escape($data['name']) . "' LIMIT 1");
                if ($query->num_rows) {
                    $category_id = $query->row['category_id'];
                    $exists = true;
                }
            }

            // Determine action based on import mode
            $import_mode = isset($settings['import_mode']) ? $settings['import_mode'] : 'insert';
            
            if ($import_mode == 'insert') {
                // Insert Only mode - only insert new records
                if (!$exists) {
                    $category_id = $this->insertCategory($data);
                    $result['success'] = true;
                    $result['action'] = 'insert';
                } else {
                    $result['success'] = true;
                    $result['action'] = 'skip';
                }
            } elseif ($import_mode == 'update') {
                // Update Only mode - only update existing records
                if ($exists) {
                    $this->updateCategory($category_id, $data);
                    $result['success'] = true;
                    $result['action'] = 'update';
                } else {
                    $result['success'] = true;
                    $result['action'] = 'skip';
                }
            } elseif ($import_mode == 'upsert') {
                // Upsert mode - insert new or update existing
                if ($exists) {
                    $this->updateCategory($category_id, $data);
                    $result['success'] = true;
                    $result['action'] = 'update';
                } else {
                    $category_id = $this->insertCategory($data);
                    $result['success'] = true;
                    $result['action'] = 'insert';
                }
            }

        } catch (Exception $e) {
            $result['error'] = $e->getMessage();
        }

        return $result;
    }

    /**
     * Insert new category
     */
    private function insertCategory($data) {
        $this->debugLog('Inserting category', $data);
        
        // Build INSERT with optional category_id
        $sql = "INSERT INTO " . DB_PREFIX . "category SET ";
        
        // If category_id provided, use it (otherwise auto-increment)
        if (isset($data['category_id']) && $data['category_id']) {
            $sql .= "category_id = '" . (int)$data['category_id'] . "', ";
        }
        
        $sql .= "
            image = '" . $this->db->escape(isset($data['image']) ? $data['image'] : '') . "',
            parent_id = '" . (int)(isset($data['parent_id']) ? $data['parent_id'] : 0) . "',
            `top` = '" . (int)(isset($data['top']) ? $data['top'] : 0) . "',
            `column` = '" . (int)(isset($data['column']) ? $data['column'] : 1) . "',
            sort_order = '" . (int)(isset($data['sort_order']) ? $data['sort_order'] : 0) . "',
            status = '" . (int)(isset($data['status']) ? $data['status'] : 1) . "',
            date_added = NOW(),
            date_modified = NOW()
        ";
        
        $this->debugQuery($sql);

        // Get the inserted category_id (either provided or auto-generated)
        $category_id = isset($data['category_id']) && $data['category_id'] ? (int)$data['category_id'] : $this->db->getLastId();
        $this->debugLog('Created category ID: ' . $category_id);

        // Insert category description
        $sql = "INSERT INTO " . DB_PREFIX . "category_description SET 
            category_id = '" . (int)$category_id . "',
            language_id = '" . (int)$this->config->get('config_language_id') . "',
            name = '" . $this->db->escape(isset($data['name']) ? $data['name'] : '') . "',
            description = '" . $this->db->escape(isset($data['description']) ? $data['description'] : '') . "',
            meta_title = '" . $this->db->escape(isset($data['meta_title']) ? $data['meta_title'] : (isset($data['name']) ? $data['name'] : '')) . "',
            meta_description = '" . $this->db->escape(isset($data['meta_description']) ? $data['meta_description'] : '') . "',
            meta_keyword = '" . $this->db->escape(isset($data['meta_keyword']) ? $data['meta_keyword'] : '') . "'
        ";
        
        $this->debugQuery($sql);

        // Insert to store
        $sql = "INSERT INTO " . DB_PREFIX . "category_to_store SET 
            category_id = '" . (int)$category_id . "',
            store_id = '0'
        ";
        $this->debugQuery($sql);

        // Insert category path (required for category to show in admin)
        // Get the path from parent
        $level = 0;
        $parent_id = isset($data['parent_id']) ? (int)$data['parent_id'] : 0;
        
        $this->debugLog('Setting up category path for category ' . $category_id . ' with parent ' . $parent_id);
        
        if ($parent_id) {
            $query = $this->debugQuery("SELECT * FROM " . DB_PREFIX . "category_path WHERE category_id = '" . (int)$parent_id . "' ORDER BY level ASC");
            
            $this->debugLog('Parent paths found: ' . $query->num_rows);
            
            foreach ($query->rows as $result) {
                $sql = "INSERT INTO " . DB_PREFIX . "category_path SET 
                    category_id = '" . (int)$category_id . "', 
                    path_id = '" . (int)$result['path_id'] . "', 
                    level = '" . (int)$level . "'
                ";
                $this->debugQuery($sql);
                $level++;
            }
        }
        
        // Insert own path
        $sql = "INSERT INTO " . DB_PREFIX . "category_path SET 
            category_id = '" . (int)$category_id . "', 
            path_id = '" . (int)$category_id . "', 
            level = '" . (int)$level . "'
        ";
        $this->debugQuery($sql);

        $this->debugLog('Category path setup complete. Final level: ' . $level);

        return $category_id;
    }

    /**
     * Update existing category
     */
    private function updateCategory($category_id, $data) {
        $sql = "UPDATE " . DB_PREFIX . "category SET date_modified = NOW()";
        
        if (isset($data['image'])) $sql .= ", image = '" . $this->db->escape($data['image']) . "'";
        if (isset($data['parent_id'])) $sql .= ", parent_id = '" . (int)$data['parent_id'] . "'";
        if (isset($data['top'])) $sql .= ", `top` = '" . (int)$data['top'] . "'";
        if (isset($data['column'])) $sql .= ", `column` = '" . (int)$data['column'] . "'";
        if (isset($data['sort_order'])) $sql .= ", sort_order = '" . (int)$data['sort_order'] . "'";
        if (isset($data['status'])) $sql .= ", status = '" . (int)$data['status'] . "'";
        
        $sql .= " WHERE category_id = '" . (int)$category_id . "'";
        
        $this->debugQuery($sql);

        // Update description if provided
        if (isset($data['name']) || isset($data['description'])) {
            $desc_sql = "UPDATE " . DB_PREFIX . "category_description SET ";
            $updates = array();
            
            if (isset($data['name'])) $updates[] = "name = '" . $this->db->escape($data['name']) . "'";
            if (isset($data['description'])) $updates[] = "description = '" . $this->db->escape($data['description']) . "'";
            if (isset($data['meta_title'])) $updates[] = "meta_title = '" . $this->db->escape($data['meta_title']) . "'";
            if (isset($data['meta_description'])) $updates[] = "meta_description = '" . $this->db->escape($data['meta_description']) . "'";
            if (isset($data['meta_keyword'])) $updates[] = "meta_keyword = '" . $this->db->escape($data['meta_keyword']) . "'";
            
            if ($updates) {
                $desc_sql .= implode(', ', $updates);
                $desc_sql .= " WHERE category_id = '" . (int)$category_id . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'";
                $this->debugQuery($desc_sql);
            }
        }
    }

    /**
     * Import customer
     */
    private function importCustomer($data, $settings) {
        // Similar structure to importProduct
        return array('success' => true, 'action' => 'insert');
    }

    /**
     * Import manufacturer
     */
    private function importManufacturer($data, $settings) {
        // Similar structure to importProduct
        return array('success' => true, 'action' => 'insert');
    }

    /**
     * Delete existing records
     * Based on IMPORT_ENTITY_SQL_DOCUMENTATION.md - truncates all related tables
     */
    private function deleteExistingRecords($entity_type) {
        switch ($entity_type) {
            case 'product':
                // Main product tables (required)
                $this->debugQuery("TRUNCATE TABLE " . DB_PREFIX . "product");
                $this->debugQuery("TRUNCATE TABLE " . DB_PREFIX . "product_description");
                $this->debugQuery("TRUNCATE TABLE " . DB_PREFIX . "product_to_store");
                $this->debugQuery("TRUNCATE TABLE " . DB_PREFIX . "product_to_category");
                
                // Optional product tables
                $this->debugQuery("TRUNCATE TABLE " . DB_PREFIX . "product_attribute");
                $this->debugQuery("TRUNCATE TABLE " . DB_PREFIX . "product_discount");
                $this->debugQuery("TRUNCATE TABLE " . DB_PREFIX . "product_image");
                $this->debugQuery("TRUNCATE TABLE " . DB_PREFIX . "product_option");
                $this->debugQuery("TRUNCATE TABLE " . DB_PREFIX . "product_option_value");
                $this->debugQuery("TRUNCATE TABLE " . DB_PREFIX . "product_related");
                $this->debugQuery("TRUNCATE TABLE " . DB_PREFIX . "product_reward");
                $this->debugQuery("TRUNCATE TABLE " . DB_PREFIX . "product_special");
                $this->debugQuery("TRUNCATE TABLE " . DB_PREFIX . "product_filter");
                $this->debugQuery("TRUNCATE TABLE " . DB_PREFIX . "product_recurring");
                $this->debugQuery("TRUNCATE TABLE " . DB_PREFIX . "product_to_download");
                $this->debugQuery("TRUNCATE TABLE " . DB_PREFIX . "product_to_layout");
                break;
                
            case 'category':
                // All 4 required category tables
                $this->debugQuery("TRUNCATE TABLE " . DB_PREFIX . "category");
                $this->debugQuery("TRUNCATE TABLE " . DB_PREFIX . "category_description");
                $this->debugQuery("TRUNCATE TABLE " . DB_PREFIX . "category_to_store");
                $this->debugQuery("TRUNCATE TABLE " . DB_PREFIX . "category_path");  // CRITICAL
                
                // Optional category tables
                $this->debugQuery("TRUNCATE TABLE " . DB_PREFIX . "category_to_layout");
                $this->debugQuery("TRUNCATE TABLE " . DB_PREFIX . "category_filter");
                break;
                
            case 'customer':
                // Main customer table
                $this->debugQuery("TRUNCATE TABLE " . DB_PREFIX . "customer");
                
                // Related customer tables
                $this->debugQuery("TRUNCATE TABLE " . DB_PREFIX . "address");
                $this->debugQuery("TRUNCATE TABLE " . DB_PREFIX . "customer_activity");
                $this->debugQuery("TRUNCATE TABLE " . DB_PREFIX . "customer_approval");
                $this->debugQuery("TRUNCATE TABLE " . DB_PREFIX . "customer_history");
                $this->debugQuery("TRUNCATE TABLE " . DB_PREFIX . "customer_ip");
                $this->debugQuery("TRUNCATE TABLE " . DB_PREFIX . "customer_login");
                $this->debugQuery("TRUNCATE TABLE " . DB_PREFIX . "customer_online");
                $this->debugQuery("TRUNCATE TABLE " . DB_PREFIX . "customer_reward");
                $this->debugQuery("TRUNCATE TABLE " . DB_PREFIX . "customer_search");
                $this->debugQuery("TRUNCATE TABLE " . DB_PREFIX . "customer_transaction");
                $this->debugQuery("TRUNCATE TABLE " . DB_PREFIX . "customer_wishlist");
                break;
                
            case 'manufacturer':
                // Both required manufacturer tables
                $this->debugQuery("TRUNCATE TABLE " . DB_PREFIX . "manufacturer");
                $this->debugQuery("TRUNCATE TABLE " . DB_PREFIX . "manufacturer_to_store");
                break;
                
            case 'order':
                // Main order tables (required)
                $this->debugQuery("TRUNCATE TABLE " . DB_PREFIX . "order");
                $this->debugQuery("TRUNCATE TABLE " . DB_PREFIX . "order_product");
                $this->debugQuery("TRUNCATE TABLE " . DB_PREFIX . "order_option");
                $this->debugQuery("TRUNCATE TABLE " . DB_PREFIX . "order_total");
                $this->debugQuery("TRUNCATE TABLE " . DB_PREFIX . "order_history");
                
                // Optional order tables
                $this->debugQuery("TRUNCATE TABLE " . DB_PREFIX . "order_voucher");
                $this->debugQuery("TRUNCATE TABLE " . DB_PREFIX . "order_recurring");
                $this->debugQuery("TRUNCATE TABLE " . DB_PREFIX . "order_recurring_transaction");
                break;
        }
    }

    /**
     * Create import history record
     */
    private function createImportHistory($import_data) {
        $this->debugQuery("INSERT INTO " . DB_PREFIX . "import_history SET 
            entity_type = '" . $this->db->escape($import_data['entity_type']) . "',
            filename = '" . $this->db->escape(basename($import_data['filepath'])) . "',
            import_mode = '" . $this->db->escape($import_data['settings']['import_mode']) . "',
            settings = '" . $this->db->escape(json_encode($import_data['settings'])) . "',
            status = 'processing',
            date_started = NOW()
        ");

        return $this->db->getLastId();
    }

    /**
     * Update import history
     */
    private function updateImportHistory($import_id, $status, $result) {
        $this->debugQuery("UPDATE " . DB_PREFIX . "import_history SET 
            status = '" . $this->db->escape($status) . "',
            records_inserted = '" . (int)$result['inserted'] . "',
            records_updated = '" . (int)$result['updated'] . "',
            records_errors = '" . (int)$result['errors'] . "',
            date_completed = NOW()
            WHERE import_id = '" . (int)$import_id . "'
        ");
    }

    /**
     * Log import error
     */
    private function logImportError($import_id, $row_number, $error_message, $row_data) {
        $this->debugQuery("INSERT INTO " . DB_PREFIX . "import_errors SET 
            import_id = '" . (int)$import_id . "',
            row_number = '" . (int)$row_number . "',
            error_message = '" . $this->db->escape($error_message) . "',
            row_data = '" . $this->db->escape(json_encode($row_data)) . "',
            date_added = NOW()
        ");
    }

    /**
     * Get recent imports
     */
    public function getRecentImports($limit = 10) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "import_history 
            ORDER BY date_started DESC 
            LIMIT " . (int)$limit
        );

        return $query->rows;
    }

    /**
     * Get import history
     */
    public function getImportHistory() {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "import_history 
            ORDER BY date_started DESC
        ");

        return $query->rows;
    }

    /**
     * Get import errors
     */
    public function getImportErrors($import_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "import_errors 
            WHERE import_id = '" . (int)$import_id . "'
            ORDER BY row_number ASC
        ");

        return $query->rows;
    }
}
