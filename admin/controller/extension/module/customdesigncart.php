<?php
class ControllerExtensionModuleCustomDesignCart extends Controller {
    private $error = array();

    public function install() {
        
        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "order_custom` (
            `order_custom_id` int(11) NOT NULL AUTO_INCREMENT,
            `order_id` int(11) NOT NULL,
            `order_product_id` int(11) NOT NULL,
            `custom_data` text,
            PRIMARY KEY (`order_custom_id`),
            KEY `order_id` (`order_id`),
            KEY `order_product_id` (`order_product_id`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");
        
        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "product_custom_config` (
            `config_id` int(11) NOT NULL AUTO_INCREMENT,
            `product_id` int(11) NOT NULL,
            `custom_data` text,
            `variant` text,
            `title` text,
            PRIMARY KEY (`config_id`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");

        $custom_data = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "cart` LIKE 'custom_data'");
        if (!$custom_data->num_rows) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "cart` ADD `custom_data` TEXT NULL AFTER `option`");
        }
    }
    
    public function uninstall() {
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "product_custom_config`");
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "order_custom`");

        $custom_data = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "cart` LIKE 'custom_data'");
        if ($custom_data->num_rows) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "cart` DROP COLUMN `custom_data`");
        }
    }

    public function index() {
        $this->load->language('extension/module/customdesigncart');
        
        $this->document->setTitle($this->language->get('heading_title'));
        
        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('module_customdesigncart', $this->request->post);
            
            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=module', true));
        }

        $data['heading_title'] = $this->language->get('heading_title');
        
        $data['text_edit'] = $this->language->get('text_edit');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        
        $data['entry_status'] = $this->language->get('entry_status');
        $data['entry_iframe_url'] = $this->language->get('entry_iframe_url');
        $data['entry_source_product'] = $this->language->get('entry_source_product');
        $data['entry_target_products'] = $this->language->get('entry_target_products');
        $data['tab_custom_fonts'] = $this->language->get('tab_custom_fonts');
        
        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

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
            'href' => $this->url->link('extension/module/customdesigncart', 'token=' . $this->session->data['token'], true)
        );

        $data['action'] = $this->url->link('extension/module/customdesigncart', 'token=' . $this->session->data['token'], true);
        $data['cancel'] = $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=module', true);
        
        $data['link_font_manager'] = $this->url->link('extension/module/customdesigncart/fontManager', 'token=' . $this->session->data['token'], true);
        $data['link_translation_manager'] = $this->url->link('extension/module/customdesigncart/translationManager', 'token=' . $this->session->data['token'], true);
        $data['link_image_manager'] = $this->url->link('extension/module/customdesigncart/imageManager', 'token=' . $this->session->data['token'], true);

        if (isset($this->request->post['module_customdesigncart_status'])) {
            $data['module_customdesigncart_status'] = $this->request->post['module_customdesigncart_status'];
        } else {
            $data['module_customdesigncart_status'] = $this->config->get('module_customdesigncart_status');
        }

        if (isset($this->request->post['module_customdesigncart_iframe_url'])) {
            $data['module_customdesigncart_iframe_url'] = $this->request->post['module_customdesigncart_iframe_url'];
        } else {
            if (!$this->config->get('module_customdesigncart_iframe_url')) {
                if ($this->request->server['HTTPS']) {
                    $base = HTTPS_CATALOG;
                } else {
                    $base = HTTP_CATALOG;
                }
                $data['module_customdesigncart_iframe_url'] = $base . 'catalog/view/javascript/customdesigncart/widget/index.html';
            } else {
                $data['module_customdesigncart_iframe_url'] = $this->config->get('module_customdesigncart_iframe_url');
            }
        }

        $this->load->model('catalog/product');
        
        // Get related products
        $data['module_customdesigncart_related'] = array();
        
        if (isset($this->request->post['module_customdesigncart_related'])) {
            $products = $this->request->post['module_customdesigncart_related'];
        } else {
            $products = $this->config->get('module_customdesigncart_related');
        }
        
        if (!empty($products)) {
            foreach ($products as $product_id) {
                $product_info = $this->model_catalog_product->getProduct($product_id);
                
                if ($product_info) {
                    $data['module_customdesigncart_related'][] = array(
                        'product_id' => $product_info['product_id'],
                        'name'       => $product_info['name']
                    );
                }
            }
        }
        $data['product_autocomplete'] = $this->url->link('catalog/product/autocomplete', 'token=' . $this->session->data['token'], true);
        $data['token'] = $this->session->data['token'];

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/customdesigncart.tpl', $data));
    }

    public function fontManager() {
        $this->load->language('extension/module/customdesigncart');
        
        $this->document->setTitle($this->language->get('tab_custom_fonts'));

        $data['heading_title'] = $this->language->get('tab_custom_fonts');
        $data['text_edit_fonts'] = $this->language->get('text_edit_fonts');
        $data['entry_font_family'] = $this->language->get('entry_font_family');
        $data['entry_font_url'] = $this->language->get('entry_font_url');
        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');
        $data['button_add_font'] = $this->language->get('button_add_font');
        $data['button_remove'] = $this->language->get('button_remove');

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }

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
            'href' => $this->url->link('extension/module/customdesigncart', 'token=' . $this->session->data['token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('tab_custom_fonts'),
            'href' => $this->url->link('extension/module/customdesigncart/fontManager', 'token=' . $this->session->data['token'], true)
        );

        $data['action_fonts'] = $this->url->link('extension/module/customdesigncart/saveCustomFonts', 'token=' . $this->session->data['token'], true);
        $data['cancel'] = $this->url->link('extension/module/customdesigncart', 'token=' . $this->session->data['token'], true);

        $data['custom_fonts'] = array();
        $fonts_file = DIR_CATALOG . 'view/javascript/customdesigncart/fonts.json';
        if (file_exists($fonts_file) && filesize($fonts_file) > 0) {
            $json = file_get_contents($fonts_file);
            $font_data = json_decode($json, true);
            if (isset($font_data['fonts'])) {
                $data['custom_fonts'] = $font_data['fonts'];
            }
        }

        $data['font_row'] = isset($data['custom_fonts']) ? count($data['custom_fonts']) : 0;

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/customdesigncart_font_manager.tpl', $data));
    }

    public function translationManager() {
        $this->load->language('extension/module/customdesigncart');
        
        $this->document->setTitle($this->language->get('text_translation_manager'));

        $data['heading_title'] = $this->language->get('text_translation_manager');
        $data['text_edit_translations'] = $this->language->get('text_edit_translations');
        $data['entry_translation_key'] = $this->language->get('entry_translation_key');
        $data['entry_translation_value'] = $this->language->get('entry_translation_value');
        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }

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
            'href' => $this->url->link('extension/module/customdesigncart', 'token=' . $this->session->data['token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_translation_manager'),
            'href' => $this->url->link('extension/module/customdesigncart/translationManager', 'token=' . $this->session->data['token'], true)
        );

        $data['action_translations'] = $this->url->link('extension/module/customdesigncart/saveTranslations', 'token=' . $this->session->data['token'], true);
        $data['cancel'] = $this->url->link('extension/module/customdesigncart', 'token=' . $this->session->data['token'], true);

        $this->load->model('localisation/language');
        $data['languages'] = $this->model_localisation_language->getLanguages();
        $data['translations'] = array();

        if (count($data['languages']) > 1) {
            $default_translation_file = DIR_CATALOG . 'view/javascript/customdesigncart/translation.json';
            $default_translations = array();
            if (file_exists($default_translation_file) && filesize($default_translation_file) > 0) {
                $json = file_get_contents($default_translation_file);
                $default_translations = json_decode($json, true);
            }

            foreach ($data['languages'] as $language) {
                $lang_code = $language['code'];
                $translation_file = DIR_CATALOG . 'view/javascript/customdesigncart/translation.' . $lang_code . '.json';

                $translations = array();
                if (file_exists($translation_file) && filesize($translation_file) > 0) {
                    $json = file_get_contents($translation_file);
                    $translations = json_decode($json, true);
                }

                if (empty($translations) && !empty($default_translations)) {
                    $translations = $default_translations;
                }

                $data['translations'][$lang_code] = $translations;
            }
        } else {
            $translation_file = DIR_CATALOG . 'view/javascript/customdesigncart/translation.json';
            if (file_exists($translation_file) && filesize($translation_file) > 0) {
                $json = file_get_contents($translation_file);
                $translations = json_decode($json, true);
                if (!empty($data['languages'])) {
                    $language = reset($data['languages']);
                    if ($language && isset($language['code'])) {
                        $data['translations'][$language['code']] = $translations;
                    }
                } else {
                    $data['translations']['default'] = $translations;
                }
            }
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/customdesigncart_translation_manager.tpl', $data));
    }

    public function saveTranslations() {
        $this->load->language('extension/module/customdesigncart');
        
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            if (isset($this->request->post['translations'])) {
                $this->load->model('localisation/language');
                $languages = $this->model_localisation_language->getLanguages();

                if (count($languages) > 1) {
                    foreach ($this->request->post['translations'] as $lang_code => $translations) {
                        $json_data = json_encode($translations, JSON_PRETTY_PRINT);
                        $file = DIR_CATALOG . 'view/javascript/customdesigncart/translation.' . $lang_code . '.json';
                        file_put_contents($file, $json_data);
                    }
                } else {
                    $translations = reset($this->request->post['translations']);
                    $json_data = json_encode($translations, JSON_PRETTY_PRINT);
                    $file = DIR_CATALOG . 'view/javascript/customdesigncart/translation.json';
                    file_put_contents($file, $json_data);
                }
                $this->session->data['success'] = $this->language->get('text_translation_save_success');
            }
        }
        $this->response->redirect($this->url->link('extension/module/customdesigncart/translationManager', 'token=' . $this->session->data['token'], true));
    }

    public function imageManager() {
        $this->load->language('extension/module/customdesigncart');
        
        $this->document->setTitle($this->language->get('text_image_manager'));
        
        $this->load->model('tool/image');

        $data['heading_title'] = $this->language->get('text_image_manager');
        $data['text_edit_images'] = $this->language->get('text_edit_images');
        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');
        $data['button_add'] = $this->language->get('button_add');
        $data['button_remove'] = $this->language->get('button_remove');
        $data['button_bulk_add'] = $this->language->get('button_bulk_add');
        $data['text_bulk_add_title'] = $this->language->get('text_bulk_add_title');
        $data['text_bulk_add_instructions'] = $this->language->get('text_bulk_add_instructions');

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }

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
            'href' => $this->url->link('extension/module/customdesigncart', 'token=' . $this->session->data['token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_image_manager'),
            'href' => $this->url->link('extension/module/customdesigncart/imageManager', 'token=' . $this->session->data['token'], true)
        );

        $data['action_images'] = $this->url->link('extension/module/customdesigncart/saveImages', 'token=' . $this->session->data['token'], true);
        $data['cancel'] = $this->url->link('extension/module/customdesigncart', 'token=' . $this->session->data['token'], true);

        $data['images'] = array();
        $images_file = DIR_CATALOG . 'view/javascript/customdesigncart/images.json';
        if (file_exists($images_file)) {
            $image_paths = json_decode(file_get_contents($images_file), true);
            if (is_array($image_paths)) {
                foreach ($image_paths as $path) {
                    if (is_file(DIR_IMAGE . $path)) {
                        $data['images'][] = array(
                            'path'  => $path,
                            'thumb' => $this->model_tool_image->resize($path, 100, 100)
                        );
                    }
                }
            }
        }

        $data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);
        $data['image_row'] = count($data['images']);

        $data['token'] = $this->session->data['token'];

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/customdesigncart_image_manager.tpl', $data));
    }

    public function saveImages() {
        $this->load->language('extension/module/customdesigncart');
        
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $images = isset($this->request->post['images']) ? $this->request->post['images'] : array();
            // Remove duplicates and re-index the array
            $unique_images = array_values(array_unique($images));
            $json_data = json_encode($unique_images, JSON_PRETTY_PRINT);
            $file = DIR_CATALOG . 'view/javascript/customdesigncart/images.json';

            if (file_put_contents($file, $json_data)) {
                $this->session->data['success'] = $this->language->get('text_image_save_success');
            }
        }
        $this->response->redirect($this->url->link('extension/module/customdesigncart/imageManager', 'token=' . $this->session->data['token'], true));
    }

    public function getThumbnails() {
        $json = array();
        $this->load->model('tool/image');

        if (isset($this->request->post['paths']) && is_array($this->request->post['paths'])) {
            foreach ($this->request->post['paths'] as $path) {
                $path = trim($path);
                if ($path && is_file(DIR_IMAGE . $path)) {
                    $json[$path] = $this->model_tool_image->resize($path, 100, 100);
                } else {
                    $json[$path] = $this->model_tool_image->resize('no_image.png', 100, 100);
                }
            }
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function saveCustomFonts() {
        $this->load->language('extension/module/customdesigncart');
        
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $fonts = array();
            if (isset($this->request->post['fonts'])) {
                foreach ($this->request->post['fonts'] as $font) {
                    if (!empty($font['name']) && !empty($font['url'])) {
                        $fonts[] = array(
                            'name' => $font['name'],
                            'url'  => $font['url']
                        );
                    }
                }
            }

            $json_data = json_encode(array('fonts' => $fonts), JSON_PRETTY_PRINT);
            $file = DIR_CATALOG . 'view/javascript/customdesigncart/fonts.json';

            if (file_put_contents($file, $json_data)) {
                $this->session->data['success'] = $this->language->get('text_font_save_success');
            } else {
                $this->error['warning'] = $this->language->get('error_font_save');
            }
        }
        $this->response->redirect($this->url->link('extension/module/customdesigncart/fontManager', 'token=' . $this->session->data['token'], true));
    }

    public function deleteProductConfig() {
        $config_id = isset($this->request->post['config_id']) ? (int)$this->request->post['config_id'] : 0;
        $this->db->query("DELETE FROM " . DB_PREFIX . "product_custom_config WHERE config_id = '" . (int)$config_id . "'");
    }

    public function saveProductConfig($product_id, $data, $variant_config = '', $variant_title = '', $variant_id = 0) {
        if ($variant_id) {
            $this->db->query("UPDATE " . DB_PREFIX . "product_custom_config SET product_id = '" . (int)$product_id . "', custom_data = '" . $this->db->escape($data) . "', variant = '" . $this->db->escape($variant_config) . "', title = '" . $this->db->escape($variant_title) . "' WHERE config_id = '" . (int)$variant_id . "'");
        } else {
            if ($variant_title) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "product_custom_config SET product_id = '" . (int)$product_id . "', custom_data = '" . $this->db->escape($data) . "', variant = '" . $this->db->escape($variant_config) . "', title = '" . $this->db->escape($variant_title) . "'");
            } else {
                $this->db->query("DELETE FROM " . DB_PREFIX . "product_custom_config WHERE product_id = '" . (int)$product_id . "' AND variant IS NULL");
                $this->db->query("INSERT INTO " . DB_PREFIX . "product_custom_config SET product_id = '" . (int)$product_id . "', custom_data = '" . $this->db->escape($data) . "'");
            }
        }
    }

    public function apiSaveProductConfig() {
        $product_id = (int)$this->request->post['product_id'];
        $custom_data = $this->request->post['custom_data'];
        $custom_data = html_entity_decode($custom_data);
        $variant_config = isset($this->request->post['variant_config']) ? html_entity_decode($this->request->post['variant_config']) : '';
        $variant_title = isset($this->request->post['variant_title']) ? $this->request->post['variant_title'] : '';
        $variant_id = isset($this->request->post['variant_id']) ? (int)$this->request->post['variant_id'] : 0;

        $this->saveProductConfig($product_id, $custom_data, $variant_config, $variant_title, $variant_id);
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode(array('success' => true)));
    }

    public function getProductConfig() {
        $product_id = (int)$this->request->get['product_id'];
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_custom_config WHERE variant IS NOT NULL AND product_id = '" . (int)$product_id . "'");

        return $query->rows;
    }

    public function apiDeleteProductConfig() {
        $config_id = (int)$this->request->post['config_id'];
        $this->db->query("DELETE FROM " . DB_PREFIX . "product_custom_config WHERE config_id = '" . (int)$config_id . "'");
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode(array('success' => true)));
    }

    public function apiClearProductConfig() {
        $product_id = (int)$this->request->post['product_id'];
        if (!$product_id) {
            $this->response->setOutput(json_encode(array('success' => false, 'message' => 'Invalid product ID')));
            return;
        }

        $this->db->query("DELETE FROM " . DB_PREFIX . "product_custom_config WHERE product_id = '" . (int)$product_id . "'");
        $this->response->setOutput(json_encode(array('success' => true, 'message' => 'Product customization cleared')));
    }

    private function getOrderCustoms($order_id, $order_product_id) {
        $query = $this->db->query("SELECT custom_data FROM " . DB_PREFIX . "order_custom WHERE order_id = '" . (int)$order_id . "' AND order_product_id = '" . (int)$order_product_id . "'");

        return $query->row['custom_data'];
    }

    private function downloadOrderAssetsZip($jsonString, $orderId) {
        $data = json_decode($jsonString, true);
        if (!isset($data['elements']) || !is_array($data['elements'])) {
            die("Invalid JSON format.");
        }

        // Create a temporary directory
        $tmpDir = sys_get_temp_dir() . "/order_{$orderId}_" . uniqid();
        mkdir($tmpDir);

        $imageCount = 0;

        // Loop through elements and extract base64 images
        foreach ($data['elements'] as $index => $element) {
            if ($element['type'] === 'image' && !(isset($element['readonly']) && $element['readonly'] === true) && isset($element['src'])) {
                if (substr($element['src'], 0, 11) === 'data:image/') {
                    // Extract base64 data
                    preg_match('/data:image\/(\w+);base64,(.+)/', $element['src'], $matches);
                    if (count($matches) === 3) {
                        $ext = $matches[1];
                        $base64Data = $matches[2];
                        $imageData = base64_decode($base64Data);

                        // Save to file
                        $filename = $tmpDir . "/image_{$index}.{$ext}";
                        file_put_contents($filename, $imageData);
                        $imageCount++;
                    }
                } else if (substr($element['src'], 0, 4) === 'http') {
                    // Handle external image URLs if needed
                    $imageUrl = $element['src'];

                    if ($this->request->server['HTTPS']) {
                        $base = HTTPS_CATALOG;
                    } else {
                        $base = HTTP_CATALOG;
                    }
                    if (strpos($imageUrl, $base) === 0) {
                        $imageUrl = str_replace($base . '/image', DIR_IMAGE, $imageUrl);
                    }

                    $imageContents = file_get_contents($imageUrl);
                    if ($imageContents !== false) {
                        $pathInfo = pathinfo($imageUrl);
                        $ext = isset($pathInfo['extension']) ? $pathInfo['extension'] : 'jpg';
                        $filename = $tmpDir . "/image_{$index}.{$ext}";
                        file_put_contents($filename, $imageContents);
                        $imageCount++;
                    }
                }
            }
        }

        if ($imageCount === 0) {
            // Clean up temp dir
            foreach (glob("$tmpDir/*") as $file) {
                unlink($file);
            }
            rmdir($tmpDir);
            die("No images found to zip.");
        }

        // Create zip
        $zipFilename = tempnam(sys_get_temp_dir(), "order_{$orderId}_assets_") . ".zip";
        $zip = new ZipArchive();
        if ($zip->open($zipFilename, ZipArchive::CREATE) !== TRUE) {
            // Clean up temp dir
            foreach (glob("$tmpDir/*") as $file) {
                unlink($file);
            }
            rmdir($tmpDir);
            die("Could not create ZIP file.");
        }

        // Add files to zip
        $files = glob("$tmpDir/*");
        foreach ($files as $file) {
            $zip->addFile($file, basename($file));
        }
        $zip->close();

        // Clean up image files
        foreach ($files as $file) {
            unlink($file);
        }
        rmdir($tmpDir);

        // Send to browser
        if (file_exists($zipFilename)) {
            header('Content-Type: application/zip');
            header("Content-Disposition: attachment; filename=order_{$orderId}_assets.zip");
            header('Content-Length: ' . filesize($zipFilename));
            header('Pragma: public');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Content-Transfer-Encoding: binary');
            flush();
            readfile($zipFilename);
            unlink($zipFilename);
            exit;
        } else {
            die("ZIP file not found.");
        }
    }

    public function downloadOrderAssets() {
        $orderId = (int)$this->request->get['order_id'];
        $order_product_id = (int)$this->request->get['order_product_id'];
        $jsonString = $this->getOrderCustoms($orderId, $order_product_id);
        if (!$orderId || !$jsonString) {
            die("Invalid request.");
        }
        $this->downloadOrderAssetsZip($jsonString, $orderId);
    }

    public function copyData() {
        $this->load->language('extension/module/customdesigncart');
        
        $json = array();

        if (!$this->user->hasPermission('modify', 'extension/module/customdesigncart')) {
            $json['error'] = $this->language->get('error_permission');
        }

        if (!isset($this->request->post['source_product_id'])) {
            $json['error'] = 'Source product ID is required';
        }

        if (!isset($this->request->post['target_product_ids']) || !is_array($this->request->post['target_product_ids'])) {
            $json['error'] = 'Target product IDs are required';
        }

        if (!isset($json['error'])) {
            $source_product_id = (int)$this->request->post['source_product_id'];
            $target_product_ids = array_map('intval', $this->request->post['target_product_ids']);

            // Get source product custom config
            $query = $this->db->query("SELECT custom_data FROM " . DB_PREFIX . "product_custom_config WHERE product_id = '" . (int)$source_product_id . "'");

            if ($query->num_rows) {
                $custom_data = $query->row['custom_data'];

                // Copy to each target product
                foreach ($target_product_ids as $target_product_id) {
                    if ($target_product_id != $source_product_id) {  // Prevent copying to self
                        $this->saveProductConfig($target_product_id, $custom_data);
                    }
                }

                $json['success'] = 'Custom design data has been copied successfully to ' . count($target_product_ids) . ' products';
            } else {
                $json['error'] = 'No custom design data found for the source product';
            }
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    protected function validate() {
        if (!$this->user->hasPermission('modify', 'extension/module/customdesigncart')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }
}
