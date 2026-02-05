<?php
/**
 * Import Data Module - Controller
 * OpenCart 2.x compatible data import module with wizard interface
 * Similar to Salesforce Data Loader functionality
 */
class ControllerExtensionModuleImportData extends Controller {
    private $error = array();

    /**
     * Main index method - displays wizard step 1 (file upload)
     */
    public function index() {
        $this->load->language('extension/module/import_data');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('extension/module/import_data');
        
        // Breadcrumbs
        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
        );
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=module', true)
        );
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/module/import_data', 'token=' . $this->session->data['token'], true)
        );

        // Set URLs
        $data['action'] = $this->url->link('extension/module/import_data/upload', 'token=' . $this->session->data['token'], true);
        $data['cancel'] = $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=module', true);
        $data['token'] = $this->session->data['token'];

        // Language strings
        $data['heading_title'] = $this->language->get('heading_title');
        $data['text_upload_step'] = $this->language->get('text_upload_step');
        $data['text_step_upload'] = $this->language->get('text_step_upload');
        $data['text_step_mapping'] = $this->language->get('text_step_mapping');
        $data['text_step_settings'] = $this->language->get('text_step_settings');
        $data['text_step_import'] = $this->language->get('text_step_import');
        $data['text_select'] = $this->language->get('text_select');
        $data['text_recent_imports'] = $this->language->get('text_recent_imports');
        $data['entry_entity_type'] = $this->language->get('entry_entity_type');
        $data['entry_file'] = $this->language->get('entry_file');
        $data['help_file'] = $this->language->get('help_file');
        $data['button_continue'] = $this->language->get('button_continue');
        $data['button_cancel'] = $this->language->get('button_cancel');
        $data['column_date'] = $this->language->get('column_date');
        $data['column_entity'] = $this->language->get('column_entity');
        $data['column_filename'] = $this->language->get('column_filename');
        $data['column_records'] = $this->language->get('column_records');
        $data['column_status'] = $this->language->get('column_status');
        $data['error_entity_type'] = $this->language->get('error_entity_type');
        $data['error_upload'] = $this->language->get('error_upload');

        // Error and success messages
        $data['error_warning'] = '';
        $data['success'] = '';

        // Get entity types for import
        $data['entity_types'] = array(
            'product' => $this->language->get('text_product'),
            'category' => $this->language->get('text_category'),
            'customer' => $this->language->get('text_customer'),
            'order' => $this->language->get('text_order'),
            'manufacturer' => $this->language->get('text_manufacturer'),
            'attribute' => $this->language->get('text_attribute'),
            'option' => $this->language->get('text_option')
        );

        // Get recent imports
        $data['recent_imports'] = $this->model_extension_module_import_data->getRecentImports();

        // Render template
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/import_data', $data));
    }

    /**
     * Step 1: Handle file upload
     */
    public function upload() {
        $this->load->language('extension/module/import_data');
        $json = array();

        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            // Validate upload
            if (!isset($this->request->files['import_file']) || $this->request->files['import_file']['error'] != UPLOAD_ERR_OK) {
                $json['error'] = $this->language->get('error_upload');
            }

            if (!isset($this->request->post['entity_type']) || empty($this->request->post['entity_type'])) {
                $json['error'] = $this->language->get('error_entity_type');
            }

            if (!$json) {
                $file = $this->request->files['import_file'];
                $allowed_extensions = array('csv', 'txt', 'xlsx', 'xls');
                $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

                if (!in_array($file_extension, $allowed_extensions)) {
                    $json['error'] = $this->language->get('error_file_type');
                } else {
                    // Save uploaded file
                    $upload_dir = DIR_UPLOAD . 'import/';
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0755, true);
                    }

                    $filename = 'import_' . time() . '_' . uniqid() . '.' . $file_extension;
                    $filepath = $upload_dir . $filename;

                    if (move_uploaded_file($file['tmp_name'], $filepath)) {
                        // Parse file to get headers
                        $this->load->model('extension/module/import_data');
                        $headers = $this->model_extension_module_import_data->parseFileHeaders($filepath, $file_extension);

                        if ($headers) {
                            // Store session data for next step
                            $this->session->data['import_data'] = array(
                                'filename' => $filename,
                                'filepath' => $filepath,
                                'entity_type' => $this->request->post['entity_type'],
                                'file_extension' => $file_extension,
                                'headers' => $headers
                            );

                            $json['success'] = $this->language->get('text_upload_success');
                            $json['redirect'] = $this->url->link('extension/module/import_data/mapping', 'token=' . $this->session->data['token'], true);
                        } else {
                            $json['error'] = $this->language->get('error_parse_file');
                        }
                    } else {
                        $json['error'] = $this->language->get('error_file_save');
                    }
                }
            }
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    /**
     * Step 2: Column Mapping
     */
    public function mapping() {
        $this->load->language('extension/module/import_data');

        if (!isset($this->session->data['import_data'])) {
            $this->response->redirect($this->url->link('extension/module/import_data', 'token=' . $this->session->data['token'], true));
        }

        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('extension/module/import_data');

        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
        );
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/module/import_data', 'token=' . $this->session->data['token'], true)
        );
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_mapping'),
            'href' => $this->url->link('extension/module/import_data/mapping', 'token=' . $this->session->data['token'], true)
        );

        // Get file headers
        $data['file_headers'] = $this->session->data['import_data']['headers'];
        $data['entity_type'] = $this->session->data['import_data']['entity_type'];

        // Get database fields for entity type
        $data['db_fields'] = $this->model_extension_module_import_data->getEntityFields($data['entity_type']);

        // Get sample data (first 5 rows)
        $data['sample_data'] = $this->model_extension_module_import_data->getSampleData(
            $this->session->data['import_data']['filepath'],
            $this->session->data['import_data']['file_extension'],
            5
        );

        $data['action'] = $this->url->link('extension/module/import_data/settings', 'token=' . $this->session->data['token'], true);
        $data['back'] = $this->url->link('extension/module/import_data', 'token=' . $this->session->data['token'], true);
        $data['token'] = $this->session->data['token'];

        // Language strings
        $data['heading_title'] = $this->language->get('heading_title');
        $data['text_mapping_step'] = $this->language->get('text_mapping_step');
        $data['text_step_upload'] = $this->language->get('text_step_upload');
        $data['text_step_mapping'] = $this->language->get('text_step_mapping');
        $data['text_step_settings'] = $this->language->get('text_step_settings');
        $data['text_step_import'] = $this->language->get('text_step_import');
        $data['text_mapping_help'] = $this->language->get('text_mapping_help');
        $data['text_entity_type'] = $this->language->get('text_entity_type');
        $data['text_ignore'] = $this->language->get('text_ignore');
        $data['text_sample_preview'] = $this->language->get('text_sample_preview');
        $data['text_auto_map_complete'] = $this->language->get('text_auto_map_complete');
        $data['column_file_header'] = $this->language->get('column_file_header');
        $data['column_db_field'] = $this->language->get('column_db_field');
        $data['column_sample_data'] = $this->language->get('column_sample_data');
        $data['button_continue'] = $this->language->get('button_continue');
        $data['button_back'] = $this->language->get('button_back');
        $data['button_auto_map'] = $this->language->get('button_auto_map');
        $data['button_clear_map'] = $this->language->get('button_clear_map');

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/import_data_mapping', $data));
    }

    /**
     * Step 3: Import Settings
     */
    public function settings() {
        $this->load->language('extension/module/import_data');

        if (!isset($this->session->data['import_data'])) {
            $this->response->redirect($this->url->link('extension/module/import_data', 'token=' . $this->session->data['token'], true));
        }

        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            // Store mapping in session
            $this->session->data['import_data']['mapping'] = $this->request->post['mapping'];
        }

        $this->document->setTitle($this->language->get('heading_title'));

        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
        );
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/module/import_data', 'token=' . $this->session->data['token'], true)
        );
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_settings'),
            'href' => $this->url->link('extension/module/import_data/settings', 'token=' . $this->session->data['token'], true)
        );

        $data['action'] = $this->url->link('extension/module/import_data/process', 'token=' . $this->session->data['token'], true);
        $data['back'] = $this->url->link('extension/module/import_data/mapping', 'token=' . $this->session->data['token'], true);
        $data['home'] = $this->url->link('extension/module/import_data', 'token=' . $this->session->data['token'], true);
        $data['view_log'] = $this->url->link('extension/module/import_data/viewLog', 'token=' . $this->session->data['token'], true);
        $data['clear_log'] = $this->url->link('extension/module/import_data/clearLog', 'token=' . $this->session->data['token'], true);
        $data['token'] = $this->session->data['token'];

        $data['entity_type'] = $this->session->data['import_data']['entity_type'];

        // Language strings
        $data['heading_title'] = $this->language->get('heading_title');
        $data['text_settings_step'] = $this->language->get('text_settings_step');
        $data['text_step_upload'] = $this->language->get('text_step_upload');
        $data['text_step_mapping'] = $this->language->get('text_step_mapping');
        $data['text_step_settings'] = $this->language->get('text_step_settings');
        $data['text_step_import'] = $this->language->get('text_step_import');
        $data['text_entity_type'] = $this->language->get('text_entity_type');
        $data['text_import_mode'] = $this->language->get('text_import_mode');
        $data['text_advanced_options'] = $this->language->get('text_advanced_options');
        $data['text_import_summary'] = $this->language->get('text_import_summary');
        $data['text_warning'] = $this->language->get('text_warning');
        $data['text_import_warning'] = $this->language->get('text_import_warning');
        $data['text_importing'] = $this->language->get('text_importing');
        $data['text_processing'] = $this->language->get('text_processing');
        $data['text_import_complete'] = $this->language->get('text_import_complete');
        $data['text_inserted'] = $this->language->get('text_inserted');
        $data['text_updated'] = $this->language->get('text_updated');
        $data['text_errors'] = $this->language->get('text_errors');
        $data['text_total'] = $this->language->get('text_total');
        $data['text_delete_confirm'] = $this->language->get('text_delete_confirm');
        $data['text_mode_insert'] = $this->language->get('text_mode_insert');
        $data['text_mode_update'] = $this->language->get('text_mode_update');
        $data['text_mode_upsert'] = $this->language->get('text_mode_upsert');
        $data['entry_import_mode'] = $this->language->get('entry_import_mode');
        $data['entry_skip_first_row'] = $this->language->get('entry_skip_first_row');
        $data['entry_delete_existing'] = $this->language->get('entry_delete_existing');
        $data['entry_update_existing'] = $this->language->get('entry_update_existing');
        $data['entry_ignore_errors'] = $this->language->get('entry_ignore_errors');
        $data['entry_batch_size'] = $this->language->get('entry_batch_size');
        $data['entry_debug_mode'] = $this->language->get('entry_debug_mode');
        $data['help_mode_insert'] = $this->language->get('help_mode_insert');
        $data['help_mode_update'] = $this->language->get('help_mode_update');
        $data['help_mode_upsert'] = $this->language->get('help_mode_upsert');
        $data['help_skip_first_row'] = $this->language->get('help_skip_first_row');
        $data['help_delete_existing'] = $this->language->get('help_delete_existing');
        $data['help_update_existing'] = $this->language->get('help_update_existing');
        $data['help_ignore_errors'] = $this->language->get('help_ignore_errors');
        $data['help_batch_size'] = $this->language->get('help_batch_size');
        $data['help_debug_mode'] = $this->language->get('help_debug_mode');
        $data['button_import'] = $this->language->get('button_import');
        $data['button_back'] = $this->language->get('button_back');
        $data['button_new_import'] = $this->language->get('button_new_import');
        $data['button_view_log'] = $this->language->get('button_view_log');
        $data['button_clear_log'] = $this->language->get('button_clear_log');
        $data['text_log_cleared'] = $this->language->get('text_log_cleared');

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/import_data_settings', $data));
    }

    /**
     * Step 4: Process Import
     */
    public function process() {
        $this->load->language('extension/module/import_data');
        $json = array();

        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            if (!isset($this->session->data['import_data'])) {
                $json['error'] = $this->language->get('error_session');
            } else {
                $this->load->model('extension/module/import_data');

                // Store settings
                $settings = array(
                    'import_mode' => isset($this->request->post['import_mode']) ? $this->request->post['import_mode'] : 'insert',
                    'skip_first_row' => isset($this->request->post['skip_first_row']) ? 1 : 0,
                    'batch_size' => isset($this->request->post['batch_size']) ? (int)$this->request->post['batch_size'] : 100,
                    'delete_existing' => isset($this->request->post['delete_existing']) ? 1 : 0,
                    'update_existing' => isset($this->request->post['update_existing']) ? 1 : 0,
                    'ignore_errors' => isset($this->request->post['ignore_errors']) ? 1 : 0,
                    'debug_mode' => isset($this->request->post['debug_mode']) ? 1 : 0
                );

                $import_data = array(
                    'filepath' => $this->session->data['import_data']['filepath'],
                    'file_extension' => $this->session->data['import_data']['file_extension'],
                    'entity_type' => $this->session->data['import_data']['entity_type'],
                    'mapping' => $this->session->data['import_data']['mapping'],
                    'settings' => $settings
                );

                // Start import process
                $result = $this->model_extension_module_import_data->processImport($import_data);

                if ($result['success']) {
                    $json['success'] = sprintf(
                        $this->language->get('text_import_success'),
                        $result['inserted'],
                        $result['updated'],
                        $result['errors']
                    );
                    $json['statistics'] = $result;

                    // Clear session data
                    unset($this->session->data['import_data']);
                } else {
                    $json['error'] = $result['error'];
                }
            }
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    /**
     * Install method - called when module is installed
     */
    public function install() {
        $this->load->model('extension/module/import_data');
        $this->model_extension_module_import_data->install();
    }

    /**
     * Uninstall method - called when module is uninstalled
     */
    public function uninstall() {
        $this->load->model('extension/module/import_data');
        $this->model_extension_module_import_data->uninstall();
    }

    /**
     * Get import history
     */
    public function history() {
        $this->load->language('extension/module/import_data');
        $this->load->model('extension/module/import_data');

        $data['imports'] = $this->model_extension_module_import_data->getImportHistory();

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/import_data_history', $data));
    }

    /**
     * View debug log
     */
    public function viewLog() {
        $log_file = DIR_LOGS . 'import_debug.log';
        
        header('Content-Type: text/plain; charset=utf-8');
        
        if (file_exists($log_file)) {
            echo file_get_contents($log_file);
        } else {
            $this->load->language('extension/module/import_data');
            echo $this->language->get('text_log_empty');
        }
        
        exit;
    }

    /**
     * Clear debug log
     */
    public function clearLog() {
        $this->load->language('extension/module/import_data');
        $json = array();
        
        $log_file = DIR_LOGS . 'import_debug.log';
        
        if (file_exists($log_file)) {
            file_put_contents($log_file, '');
        }
        
        $json['success'] = $this->language->get('success_log_cleared');
        
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
}
