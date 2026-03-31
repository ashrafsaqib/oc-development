<?php

class ControllerExtensionModuleinventory extends Controller
{

    public function index()
    {
        // Load the language file for the dashboard
        $this->load->language('extension/module/inventory');

        $this->document->setTitle($this->language->get('heading_title'));

        $data['user_token'] = $this->session->data['user_token'];

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        // Check install directory exists
        if (is_dir(DIR_APPLICATION . 'install')) {
            $data['error_install'] = $this->language->get('error_install');
        } else {
            $data['error_install'] = '';
        }

        // Load the inventory model
        $this->load->model('extension/inventory/dashboard');

        // --- Fetch data from the model and populate the $data array ---

        // Key Metrics
        $data['total_products'] = $this->model_extension_inventory_dashboard->getTotalProducts();
        $data['total_value'] = $this->currency->format($this->model_extension_inventory_dashboard->getTotalValue(), $this->config->get('config_currency'));
        $data['low_stock_items'] = $this->model_extension_inventory_dashboard->getLowStockItems();
        $data['out_of_stock'] = $this->model_extension_inventory_dashboard->getOutOfStockItems();

        // Chart Data
        $data['products_by_category'] = $this->model_extension_inventory_dashboard->getProductsByCategory();
        $data['recent_low_stock_items'] = $this->model_extension_inventory_dashboard->getRecentLowStockItems();
        

        // --- Prepare data for the view ---

        // Chart data for Products by Category
        $chart_labels = [];
        $chart_counts = [];
        foreach ($data['products_by_category'] as $category) {
            $chart_labels[] = $category['category_name'];
            $chart_counts[] = (int)$category['product_count'];
        }

        // safest encoding for JS context
        $data['chart_categories'] = json_encode($chart_labels, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
        $data['chart_category_counts'] = json_encode($chart_counts, JSON_NUMERIC_CHECK);

        // Table data for Recent Low Stock Items
        $data['low_stock_table_data'] = $data['recent_low_stock_items'];

        // The dashboard content array. Note: This assumes this controller is a standalone dashboard extension.
        // If you are adding this as a custom dashboard module, you will need to adjust your view logic.

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');


        $this->response->setOutput($this->load->view('extension/inventory/inventory', $data));
    }

    /**
     * Called when the extension is installed from the admin UI.
     * Creates the oc_inventory_log table if it does not exist.
     */
    public function install()
    {
        // Use DB_PREFIX so this works across installations with different prefixes
        $table = DB_PREFIX . "inventory_log";

        $sql = "CREATE TABLE IF NOT EXISTS `" . $this->db->escape($table) . "` (
                    `inventory_log_id` int NOT NULL AUTO_INCREMENT,
                    `product_id` int NOT NULL,
                    `option_id` int DEFAULT NULL,
                    `option_value_id` int DEFAULT NULL,
                    `reference_type` varchar(50) NOT NULL,
                    `reference_id` int NOT NULL,
                    `order_status_id` int DEFAULT NULL COMMENT 'The ID of the order status at which the change was logged',
                    `stock_change` int NOT NULL,
                    `current_stock` int NOT NULL,
                    `user_id` int DEFAULT NULL,
                    `notes` text,
                    `date_added` datetime NOT NULL,
                    PRIMARY KEY (`inventory_log_id`),
                    KEY `product_id` (`product_id`)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

        $this->db->query($sql);
        // Register events to handle order history updates (admin and catalog)
        $this->load->model('setting/event');

        // Admin order history (when status changed in admin)
        $this->model_setting_event->addEvent('inventory_order_history_admin', 'admin/model/sale/order/addOrderHistory/after', 'extension/module/inventory/onOrderHistoryUpdate', 1, 1);

        // Catalog order history (when status changed via checkout/account)
        $this->model_setting_event->addEvent('inventory_order_history_catalog', 'catalog/model/checkout/order/addOrderHistory/after', 'extension/module/inventory/onOrderHistoryUpdate', 1, 1);

        // Grant permissions to current user's group so admin can access the module immediately
        $this->load->model('user/user_group');

        $user_group_id = $this->user->getGroupId();

        if ($user_group_id) {
            $this->model_user_user_group->addPermission($user_group_id, 'access', 'extension/module/inventory_manager');
            $this->model_user_user_group->addPermission($user_group_id, 'modify', 'extension/module/inventory_manager');

            $this->model_user_user_group->addPermission($user_group_id, 'access', 'extension/module/inventory_report');
            $this->model_user_user_group->addPermission($user_group_id, 'modify', 'extension/module/inventory_report');
        }
    }

    /**
     * Called when the extension is uninstalled from the admin UI.
     * Drops the oc_inventory_log table.
     */
    public function uninstall()
    {
        $table = DB_PREFIX . "inventory_log";

        $this->db->query("DROP TABLE IF EXISTS `" . $this->db->escape($table) . "`");
        // Remove events created by this extension
        $this->load->model('setting/event');

        // Delete events by code
        $this->model_setting_event->deleteEventByCode('inventory_order_history_admin');
        $this->model_setting_event->deleteEventByCode('inventory_order_history_catalog');
        // Remove permissions from the current user's group
        $this->load->model('user/user_group');

        $user_group_id = $this->user->getGroupId();

        if ($user_group_id) {
            $this->model_user_user_group->removePermission($user_group_id, 'access', 'extension/module/inventory_manager');
            $this->model_user_user_group->removePermission($user_group_id, 'modify', 'extension/module/inventory_manager');

            $this->model_user_user_group->removePermission($user_group_id, 'access', 'extension/module/inventory_report');
            $this->model_user_user_group->removePermission($user_group_id, 'modify', 'extension/module/inventory_report');
        }
    }

    /**
     * Event handler called when an order history entry is added (admin or catalog).
     * This will check configured subtract/restock statuses and call the model helper
     * to subtract or restock per order product.
     *
     * @param string $route
     * @param array  $args
     */
    public function onOrderHistoryUpdate(&$route, &$args)
    {

        // Expecting $args[0] = order_id, $args[1] = order_status_id (typical signature)
        $order_id = isset($args[0]) ? (int)$args[0] : 0;
        $order_status_id = isset($args[1]) ? (int)$args[1] : 0;

        if (!$order_id) {
            return;
        }

        $this->load->model('extension/module/inventory_manager');

        // Read configured status arrays (set via settings/ocmod additions)
        $subtract_status = $this->config->get('config_subtract_status');
        $restock_status = $this->config->get('config_restock_status');

        if (!is_array($subtract_status)) {
            $subtract_status = array();
        }
        if (!is_array($restock_status)) {
            $restock_status = array();
        }

        // Load order products for this order
        $order_products = $this->db->query("SELECT order_product_id FROM `" . DB_PREFIX . "order_product` WHERE order_id = '" . (int)$order_id . "'");

        if (in_array($order_status_id, $subtract_status)) {
            foreach ($order_products->rows as $op) {
                $this->model_extension_module_inventory_manager->subtractProductStock($order_id, $op['order_product_id']);
            }
        } elseif (in_array($order_status_id, $restock_status)) {
            foreach ($order_products->rows as $op) {
                $this->model_extension_module_inventory_manager->restockProductStock($order_id, $op['order_product_id']);
            }
        }
    }
}
//disable admin_save_and_keep_editing.xml