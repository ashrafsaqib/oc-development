<?php
class ControllerExtensionModuleInventoryManager extends Controller {
    private $error = array();

    public function index() {
        // Load necessary language, model, and library files
        $this->load->language('extension/module/inventory');
        $this->load->model('extension/module/inventory_manager');
        $this->load->model('catalog/product');
        $this->load->model('user/user');
        $this->load->model('localisation/order_status');

        // Set the document title and add breadcrumbs
        $this->document->setTitle($this->language->get('heading_title'));

        $url = '';

        // Handle filter parameters
        if (isset($this->request->get['filter_product_id'])) {
            $filter_product_id = $this->request->get['filter_product_id'];
        } else {
            $filter_product_id = null;
        }

        if (isset($this->request->get['filter_reference_type'])) {
            $filter_reference_type = $this->request->get['filter_reference_type'];
        } else {
            $filter_reference_type = null;
        }

        if (isset($this->request->get['filter_date_start'])) {
            $filter_date_start = $this->request->get['filter_date_start'];
        } else {
            $filter_date_start = null;
        }

        if (isset($this->request->get['filter_date_end'])) {
            $filter_date_end = $this->request->get['filter_date_end'];
        } else {
            $filter_date_end = null;
        }
        
        // Pagination parameters
        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        // Handle form submission for adding a new log entry
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->addLog();
            $this->session->data['success'] = $this->language->get('text_success_add_log');
            $this->response->redirect($this->url->link('extension/module/inventory_manager', 'user_token=' . $this->session->data['user_token'], true));
        }

        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
        );
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/module/inventory_manager', 'user_token=' . $this->session->data['user_token'], true)
        );

        // URL actions for the form and log refresh
        $data['action'] = $this->url->link('extension/module/inventory_manager', 'user_token=' . $this->session->data['user_token'], true);
        $data['user_token'] = $this->session->data['user_token'];
        $data['dashboard'] = $this->url->link('extension/module/inventory', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);
        $data['clear_logs'] = $this->url->link('extension/module/inventory_manager/clearlogs', 'user_token=' . $this->session->data['user_token'], true);
        $data['export_logs'] = $this->url->link('extension/module/inventory_manager/export', 'user_token=' . $this->session->data['user_token'], true);

        // Get and process data for the view
        $data['heading_title'] = $this->language->get('heading_title');
        $data['text_form'] = $this->language->get('text_form');
        $data['text_no_results'] = $this->language->get('text_no_results');
        $data['column_date'] = $this->language->get('column_date');
        $data['column_product'] = $this->language->get('column_product');
        $data['column_stock_change'] = $this->language->get('column_stock_change');
        $data['column_current_stock'] = $this->language->get('column_current_stock');
        $data['column_notes'] = $this->language->get('column_notes');
        $data['column_reference'] = $this->language->get('column_reference');
        $data['column_order_status'] = $this->language->get('column_order_status');
        $data['column_user'] = $this->language->get('column_user');
        $data['entry_product'] = $this->language->get('entry_product');
        $data['entry_change'] = $this->language->get('entry_change');
        $data['entry_notes'] = $this->language->get('entry_notes');
        $data['entry_reference_type'] = $this->language->get('entry_reference_type');
        $data['entry_date_start'] = $this->language->get('entry_date_start');
        $data['entry_date_end'] = $this->language->get('entry_date_end');
        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');
        $data['button_filter'] = $this->language->get('button_filter');
        $data['button_clear_filter'] = $this->language->get('button_clear_filter');

        $data['reference_types'] = array(
            'manual'   => $this->language->get('text_reference_manual'),
            'order'    => $this->language->get('text_reference_order'),
            'return'   => $this->language->get('text_reference_return'),
            'api'      => $this->language->get('text_reference_api')
        );

        // Check for success and error messages
        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['product_id'])) {
            $data['error_product_id'] = $this->error['product_id'];
        } else {
            $data['error_product_id'] = '';
        }

        if (isset($this->error['stock_change'])) {
            $data['error_stock_change'] = $this->error['stock_change'];
        } else {
            $this->error_stock_change = '';
        }

        // Get and filter inventory log data
        $filter_data = array(
            'filter_product_id'         => $filter_product_id,
            'filter_reference_type'  => $filter_reference_type,
            'filter_date_start'      => $filter_date_start,
            'filter_date_end'        => $filter_date_end,
            'start'                  => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit'                  => $this->config->get('config_limit_admin')
        );

        $data['inventory_logs'] = array();
        $log_total = $this->model_extension_module_inventory_manager->getTotalInventoryLogs($filter_data);
        $results = $this->model_extension_module_inventory_manager->getInventoryLogs($filter_data);

        foreach ($results as $result) {
            $product_info = $this->model_catalog_product->getProduct($result['product_id']);
            $product_name = ($product_info) ? $product_info['name'] : 'Product not found';

            $user_info = $this->model_user_user->getUser($result['user_id']);
            $user_name = ($user_info) ? $user_info['username'] : 'N/A';

            $order_status_info = $this->model_localisation_order_status->getOrderStatus($result['order_status_id']);
            $order_status_name = ($order_status_info) ? $order_status_info['name'] : 'N/A';
            
            $data['inventory_logs'][] = array(
                'inventory_log_id' => $result['inventory_log_id'],
                'href'          => $this->url->link('extension/module/inventory_report', 'user_token=' . $this->session->data['user_token'] . '&filter_product_id=' . $result['product_id'], true),
                'product_name'     => $product_name,
                'product_id'       => $result['product_id'],
                'date_added'       => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
                'stock_change'     => $result['stock_change'],
                'current_stock'    => $result['current_stock'],
                'notes'            => $result['notes'],
                'reference_type'   => isset($data['reference_types'][$result['reference_type']]) ? $data['reference_types'][$result['reference_type']] : ucfirst($result['reference_type']),
                'reference_id'     => $result['reference_id'],
                'order_status_name'=> $order_status_name,
                'user_name'        => $user_name
            );
        }

        // Add form data for new entries
        $data['product_id'] = '';
        $data['product_name'] = '';
        $data['stock_change'] = '';
        $data['notes'] = '';
        
        // Add filter values for the view
        if ($filter_product_id) {
            $filter_product_info = $this->model_catalog_product->getProduct($filter_product_id);
            $data['filter_product'] = ($filter_product_info) ? $filter_product_info['name'] : '';
        } else {
            $data['filter_product'] = '';
        }
        $data['filter_product_id'] = $filter_product_id;
        $data['filter_reference_type'] = $filter_reference_type;
        $data['filter_date_start'] = $filter_date_start;
        $data['filter_date_end'] = $filter_date_end;
        
        // Pagination
        $pagination = new Pagination();
        $pagination->total = $log_total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url = $this->url->link('extension/module/inventory_manager', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

        $data['pagination'] = $pagination->render();
        $data['results'] = sprintf($this->language->get('text_pagination'), ($log_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($log_total - $this->config->get('config_limit_admin'))) ? $log_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $log_total, ceil($log_total / $this->config->get('config_limit_admin')));


        // Render the view
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/inventory_manager', $data));
    }

    protected function validateForm() {
        if (!$this->user->hasPermission('modify', 'extension/module/inventory_manager')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!isset($this->request->post['product_id']) || empty($this->request->post['product_id'])) {
            $this->error['product_id'] = $this->language->get('error_product');
        }

        if (!isset($this->request->post['stock_change']) || empty($this->request->post['stock_change']) || !is_numeric($this->request->post['stock_change']) || (int)$this->request->post['stock_change'] === 0) {
            $this->error['stock_change'] = $this->language->get('error_change');
        }

        return !$this->error;
    }

    public function addLog() {
        if ($this->validateForm()) {
            $this->load->model('extension/module/inventory_manager');
            $this->load->model('catalog/product');

            $product_id = (int)$this->request->post['product_id'];
            $stock_change = (int)$this->request->post['stock_change'];

            // Get current stock before updating
            $product_info = $this->model_catalog_product->getProduct($product_id);
            $current_stock = $product_info['quantity'] + $stock_change;

            $log_data = array(
                'product_id'     => $product_id,
                'stock_change'   => $stock_change,
                'current_stock'  => $current_stock,
                'notes'          => $this->request->post['notes'],
                'reference_type' => 'manual',
                'reference_id'   => 0,
                'order_status_id' => null, // Manual entries have no order status
                'user_id'        => $this->user->getId(),
                'option_id'       => null,
                'option_value_id' => null
            );

            // Add the log entry and update product quantity
            $this->model_extension_module_inventory_manager->addInventoryLog($log_data);

            // Update the product's quantity in the database
            $this->model_extension_module_inventory_manager->editProductQuantity($product_id, $stock_change);

            // Set a success message
            $this->session->data['success'] = $this->language->get('text_success');
        }
    }

    public function autocomplete() {
        $json = array();
        
        if (isset($this->request->get['filter_name'])) {
            $this->load->model('extension/module/inventory_manager');

            $filter_data = array(
                'filter_name' => $this->request->get['filter_name'],
                'start'       => 0,
                'limit'       => 20
            );

            $results = $this->model_extension_module_inventory_manager->getProducts($filter_data);

            foreach ($results as $result) {
                $json[] = array(
                    'product_id' => $result['product_id'],
                    'name'       => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
                );
            }
        }
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
    
    /**
     * AJAX: Analyze uploaded CSV and return columns + preview
     */
    public function import_analyze() {
        $this->load->language('extension/module/inventory');

        $json = array();

        if (!$this->user->hasPermission('modify', 'extension/module/inventory_manager')) {
            $json['error'] = $this->language->get('error_permission');
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        // basic validation: file exists and is csv and not too large
        if (empty($this->request->files['import_file']) || $this->request->files['import_file']['error'] != UPLOAD_ERR_OK) {
            $json['error'] = $this->language->get('error_no_file') ? $this->language->get('error_no_file') : 'No file uploaded.';
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $file = $this->request->files['import_file']['tmp_name'];
        $file_size = $this->request->files['import_file']['size'];
        $file_name = $this->request->files['import_file']['name'];

        // Reject very large files (e.g., > 8MB) to avoid timeouts; adjust as needed
        if ($file_size > 8 * 1024 * 1024) {
            $json['error'] = 'File is too large. Max allowed size is 8MB.';
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        // simple extension check
        $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        if (!in_array($ext, array('csv', 'txt'))) {
            $json['error'] = 'Invalid file type. Please upload a CSV file.';
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        if (!is_uploaded_file($file) && !is_readable($file)) {
            // allow reading from tmp for CLI/dev
            if (!is_readable($file)) {
                $json['error'] = 'Uploaded file is not readable.';
                $this->response->addHeader('Content-Type: application/json');
                $this->response->setOutput(json_encode($json));
                return;
            }
        }

        $handle = fopen($file, 'r');
        if (!$handle) {
            $json['error'] = 'Failed to open uploaded file.';
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

    $rows = array();
        $max_preview = 6; // include header + 5 rows
        $count = 0;
        while (($data = fgetcsv($handle)) !== false && $count < $max_preview) {
            // normalize encoding to UTF-8
            foreach ($data as &$cell) {
                if (!mb_check_encoding($cell, 'UTF-8')) {
                    $cell = utf8_encode($cell);
                }
                $cell = trim($cell);
            }
            $rows[] = $data;
            $count++;
        }
        fclose($handle);

        if (empty($rows)) {
            $json['error'] = $this->language->get('error_no_columns') ? $this->language->get('error_no_columns') : 'No data found in file.';
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        // Heuristic: if first row contains any non-numeric cell (and not all cells numeric), treat as header
        $first = $rows[0];
        $has_header = false;
        $num_cells = count($first);
        $numeric_count = 0;
        foreach ($first as $cell) {
            if (is_numeric(str_replace(array(',', '.'), '', $cell))) {
                $numeric_count++;
            }
        }
        if ($numeric_count < $num_cells) {
            $has_header = true;
        }

        // Build columns list
        $columns = array();
        if ($has_header) {
            foreach ($first as $c) {
                $columns[] = (string)$c;
            }
            // preview excludes header
            $preview_rows = array_slice($rows, 1);
        } else {
            for ($i = 0; $i < $num_cells; $i++) {
                $columns[] = 'Column ' . ($i + 1);
            }
            $preview_rows = $rows;
        }

        // limit preview to 5 rows
        $preview_rows = array_slice($preview_rows, 0, 5);

        $json['columns'] = $columns;
        $json['preview'] = $preview_rows;
        $json['has_header'] = $has_header ? true : false;

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    /**
     * AJAX: Import CSV using provided mapping; updates product quantities and logs changes
     */
    public function import_do() {
        $this->load->language('extension/module/inventory');

        $json = array();

        if (!$this->user->hasPermission('modify', 'extension/module/inventory_manager')) {
            $json['error'] = $this->language->get('error_permission');
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        if (empty($this->request->files['import_file']) || $this->request->files['import_file']['error'] != UPLOAD_ERR_OK) {
            $json['error'] = $this->language->get('error_no_file') ? $this->language->get('error_no_file') : 'No file uploaded.';
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $product_col = isset($this->request->post['product_column']) ? (int)$this->request->post['product_column'] : null;
        $quantity_col = isset($this->request->post['quantity_column']) ? (int)$this->request->post['quantity_column'] : null;
        $has_header = isset($this->request->post['has_header']) && (int)$this->request->post['has_header'] ? true : false;

        if ($product_col === null || $quantity_col === null) {
            $json['error'] = 'Invalid column mapping.';
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $file = $this->request->files['import_file']['tmp_name'];
        $file_size = $this->request->files['import_file']['size'];
        $file_name = $this->request->files['import_file']['name'];

        // simple limits
        if ($file_size > 16 * 1024 * 1024) {
            $json['error'] = 'File is too large. Max allowed size is 16MB.';
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        // extension guard
        $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        if (!in_array($ext, array('csv', 'txt'))) {
            $json['error'] = 'Invalid file type. Please upload a CSV file.';
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $handle = fopen($file, 'r');
        if (!$handle) {
            $json['error'] = 'Failed to open uploaded file.';
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $row_index = 0;
        $imported = 0;
        $skipped = 0;
        $errors = array();

        // If header, skip first row
        while (($data = fgetcsv($handle)) !== false) {
            // normalize
            foreach ($data as &$cell) {
                if (!mb_check_encoding($cell, 'UTF-8')) {
                    $cell = utf8_encode($cell);
                }
                $cell = trim($cell);
            }

            if ($row_index == 0 && $has_header) { $row_index++; continue; }

            // ensure columns exist
            if (!isset($data[$product_col]) || !isset($data[$quantity_col])) {
                $skipped++;
                $row_index++;
                continue;
            }

            $identifier = trim($data[$product_col]);
            $qty_raw = trim($data[$quantity_col]);

            // normalize number (remove thousands separators)
            $qty = (float)str_replace(',', '', $qty_raw);

            if ($identifier === '' || $qty_raw === '') {
                $skipped++;
                $row_index++;
                continue;
            }

            // Resolve product by user-selected product field
            $product_field = isset($this->request->post['product_field']) ? $this->request->post['product_field'] : 'sku';
            $product_id = 0;
            $current_qty = 0;

            switch ($product_field) {
                case 'product_id':
                    // expect numeric id
                    if (ctype_digit($identifier)) {
                        $query = $this->db->query("SELECT product_id, quantity FROM " . DB_PREFIX . "product WHERE product_id = '" . (int)$identifier . "' LIMIT 1");
                        if ($query->num_rows) {
                            $product_id = (int)$query->row['product_id'];
                            $current_qty = (float)$query->row['quantity'];
                        }
                    } else {
                        $errors[] = "Row " . ($row_index+1) . ": Invalid product_id value (" . htmlspecialchars($identifier) . ")";
                    }
                    break;
                case 'name':
                    // try exact name (case-insensitive) then partial match
                    $query = $this->db->query("SELECT p.product_id, p.quantity FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND LCASE(pd.name) = '" . $this->db->escape(utf8_strtolower($identifier)) . "' LIMIT 1");
                    if ($query->num_rows) {
                        $product_id = (int)$query->row['product_id'];
                        $current_qty = (float)$query->row['quantity'];
                    } else {
                        $query = $this->db->query("SELECT p.product_id, p.quantity FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND pd.name LIKE '%" . $this->db->escape($identifier) . "%' LIMIT 1");
                        if ($query->num_rows) {
                            $product_id = (int)$query->row['product_id'];
                            $current_qty = (float)$query->row['quantity'];
                        }
                    }
                    break;
                case 'sku':
                case 'model':
                case 'ean':
                case 'upc':
                case 'jan':
                case 'isbn':
                case 'mpn':
                    // direct product table field lookup
                    $field = $product_field;
                    $query = $this->db->query("SELECT product_id, quantity FROM " . DB_PREFIX . "product WHERE " . $field . " = '" . $this->db->escape($identifier) . "' LIMIT 1");
                    if ($query->num_rows) {
                        $product_id = (int)$query->row['product_id'];
                        $current_qty = (float)$query->row['quantity'];
                    }
                    break;
                default:
                    // fallback: try product_id then sku then model then name
                    if (ctype_digit($identifier)) {
                        $query = $this->db->query("SELECT product_id, quantity FROM " . DB_PREFIX . "product WHERE product_id = '" . (int)$identifier . "' LIMIT 1");
                        if ($query->num_rows) {
                            $product_id = (int)$query->row['product_id'];
                            $current_qty = (float)$query->row['quantity'];
                        }
                    }

                    if (!$product_id) {
                        $query = $this->db->query("SELECT product_id, quantity FROM " . DB_PREFIX . "product WHERE sku = '" . $this->db->escape($identifier) . "' LIMIT 1");
                        if ($query->num_rows) {
                            $product_id = (int)$query->row['product_id'];
                            $current_qty = (float)$query->row['quantity'];
                        }
                    }

                    if (!$product_id) {
                        $query = $this->db->query("SELECT product_id, quantity FROM " . DB_PREFIX . "product WHERE model = '" . $this->db->escape($identifier) . "' LIMIT 1");
                        if ($query->num_rows) {
                            $product_id = (int)$query->row['product_id'];
                            $current_qty = (float)$query->row['quantity'];
                        }
                    }

                    if (!$product_id) {
                        $query = $this->db->query("SELECT p.product_id, p.quantity FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND pd.name LIKE '%" . $this->db->escape($identifier) . "%' LIMIT 1");
                        if ($query->num_rows) {
                            $product_id = (int)$query->row['product_id'];
                            $current_qty = (float)$query->row['quantity'];
                        }
                    }
                    break;
            }

            if (!$product_id) {
                $errors[] = "Row " . ($row_index+1) . ": Product not found (" . htmlspecialchars($identifier) . ")";
                $skipped++;
                $row_index++;
                continue;
            }

            // compute change depending on import mode
            $import_mode = isset($this->request->post['import_mode']) ? $this->request->post['import_mode'] : 'absolute';
            if ($import_mode === 'delta') {
                // CSV value is a delta to apply
                $stock_change = (int)round($qty);
                $new_qty = $current_qty + $stock_change;
            } else {
                // absolute: CSV value indicates the new absolute quantity
                $stock_change = (int)round($qty - $current_qty);
                $new_qty = (int)round($qty);
            }

            // Build log data and perform update
            $this->load->model('extension/module/inventory_manager');

            $log_data = array(
                'product_id' => $product_id,
                'option_id' => null,
                'option_value_id' => null,
                'reference_type' => 'import',
                'reference_id' => 0,
                'order_status_id' => null,
                'stock_change' => $stock_change,
                'current_stock' => (int)$new_qty,
                'user_id' => $this->user->getId(),
                'notes' => 'Imported via CSV'
            );

            // add log and update qty
            $this->model_extension_module_inventory_manager->addInventoryLog($log_data);
            if ($stock_change != 0) {
                $this->model_extension_module_inventory_manager->editProductQuantity($product_id, $stock_change);
            }

            $imported++;
            $row_index++;
        }

        fclose($handle);

        $json['success'] = sprintf('Imported %d rows, skipped %d rows.', $imported, $skipped);
        if (!empty($errors)) { $json['errors'] = $errors; }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
    public function clearlogs() {
        $this->load->model('extension/module/inventory_manager');
        $this->model_extension_module_inventory_manager->clearInventoryLogs();
        $this->session->data['success'] = 'Inventory logs cleared successfully.';
        $this->response->redirect($this->url->link('extension/module/inventory_manager', 'user_token=' . $this->session->data['user_token'], true));
    }

    public function export() {
        if (!$this->user->hasPermission('access', 'extension/module/inventory_manager')) {
            $this->response->redirect($this->url->link('extension/module/inventory_manager', 'user_token=' . $this->session->data['user_token'], true));
            return;
        }

        $this->load->language('extension/module/inventory');
        $this->load->model('extension/module/inventory_manager');
        $this->load->model('catalog/product');
        $this->load->model('user/user');
        $this->load->model('localisation/order_status');

        $filter_product_id = isset($this->request->get['filter_product_id']) ? (int)$this->request->get['filter_product_id'] : null;
        $filter_reference_type = isset($this->request->get['filter_reference_type']) ? $this->request->get['filter_reference_type'] : null;
        $filter_date_start = isset($this->request->get['filter_date_start']) ? $this->request->get['filter_date_start'] : null;
        $filter_date_end = isset($this->request->get['filter_date_end']) ? $this->request->get['filter_date_end'] : null;

        $filter_data = array(
            'filter_product_id' => $filter_product_id,
            'filter_reference_type' => $filter_reference_type,
            'filter_date_start' => $filter_date_start,
            'filter_date_end' => $filter_date_end
        );

        $results = $this->model_extension_module_inventory_manager->getInventoryLogs($filter_data);

        $reference_types = array(
            'manual' => $this->language->get('text_reference_manual'),
            'order' => $this->language->get('text_reference_order'),
            'return' => $this->language->get('text_reference_return'),
            'api' => $this->language->get('text_reference_api')
        );

        $output = fopen('php://temp', 'w+');

        fputcsv($output, array(
            $this->language->get('column_date'),
            $this->language->get('column_product'),
            $this->language->get('column_stock_change'),
            $this->language->get('column_current_stock'),
            $this->language->get('column_notes'),
            $this->language->get('column_reference'),
            $this->language->get('column_order_status'),
            $this->language->get('column_user')
        ));

        foreach ($results as $result) {
            $product_info = $this->model_catalog_product->getProduct($result['product_id']);
            $product_name = ($product_info) ? $product_info['name'] : 'Product not found';

            $user_info = $this->model_user_user->getUser($result['user_id']);
            $user_name = ($user_info) ? $user_info['username'] : 'N/A';

            $order_status_info = $this->model_localisation_order_status->getOrderStatus($result['order_status_id']);
            $order_status_name = ($order_status_info) ? $order_status_info['name'] : 'N/A';

            $reference_type = isset($reference_types[$result['reference_type']]) ? $reference_types[$result['reference_type']] : ucfirst($result['reference_type']);
            $reference = $reference_type;

            if (!empty($result['reference_id'])) {
                $reference .= ' (#' . (int)$result['reference_id'] . ')';
            }

            fputcsv($output, array(
                date($this->language->get('date_format_short'), strtotime($result['date_added'])),
                $product_name,
                (int)$result['stock_change'],
                (int)$result['current_stock'],
                $result['notes'],
                $reference,
                $order_status_name,
                $user_name
            ));
        }

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        $filename = 'inventory_logs_' . date('Ymd_His') . '.csv';

        $this->response->addHeader('Content-Type: text/csv; charset=utf-8');
        $this->response->addHeader('Content-Disposition: attachment; filename="' . $filename . '"');
        $this->response->setOutput("\xEF\xBB\xBF" . $csv);
    }
}
