<?php
class ControllerExtensionModuleInventory extends Controller
{
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
        // add log

        // Expecting $args[0] = order_id, $args[1] = order_status_id (typical signature)
        $order_id = isset($args[0]) ? (int)$args[0] : 0;
        $order_status_id = isset($args[1]) ? (int)$args[1] : 0;

        if (!$order_id) {
            return;
        }


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
                $this->subtractProductStock($order_id, $op['order_product_id']);
            }
        } elseif (in_array($order_status_id, $restock_status)) {
            foreach ($order_products->rows as $op) {
                $this->restockProductStock($order_id, $op['order_product_id']);
            }
        }
    }
    /**
     * Subtracts a product's stock and logs the change.
     * This function should be called from the order controller when a new order is placed
     * or when an order is updated to a status that triggers stock subtraction.
     *
     * @param int $order_id
     * @param int $order_product_id
     */
    private function subtractProductStock($order_id, $order_product_id)
    {
        $order_product_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_product` WHERE order_product_id = '" . (int)$order_product_id . "' AND order_id = '" . (int)$order_id . "'");

        if ($order_product_query->num_rows) {
            $product_id = $order_product_query->row['product_id'];
            $quantity = $order_product_query->row['quantity'];

            // Check if stock has already been logged for this order to prevent double-deduction
            $log_check = $this->db->query("SELECT * FROM `" . DB_PREFIX . "inventory_log` WHERE reference_type = 'order' AND reference_id = '" . (int)$order_id . "' AND product_id = '" . (int)$product_id . "' AND stock_change < 0");

            if (!$log_check->num_rows) {
                // Update product stock
                $this->db->query("UPDATE `" . DB_PREFIX . "product` SET quantity = (quantity - " . (int)$quantity . ") WHERE product_id = '" . (int)$product_id . "' AND subtract = '1'");

                // Get updated product info to get the new current stock
                $product_query = $this->db->query("SELECT quantity FROM `" . DB_PREFIX . "product` WHERE product_id = '" . (int)$product_id . "'");
                $current_stock = ($product_query->num_rows) ? $product_query->row['quantity'] : 0;

                // Add a log entry
                $this->addInventoryLog(array(
                    'product_id'      => $product_id,
                    'option_id'       => null,
                    'option_value_id' => null,
                    'reference_type'  => 'order',
                    'reference_id'    => $order_id,
                    'order_status_id' => $this->config->get('config_order_status_id'), // Use a generic order status if not available
                    'stock_change'    => (int)-$quantity,
                    'current_stock'   => $current_stock,
                    'user_id'         => $this->user ? $this->user->getId() : null,
                    'notes'           => 'Stock subtracted on order status change.'
                ));
            }
        }
    }

    /**
     * Restocks a product's stock.
     * This function should be called from the order controller when an order is updated to a status
     * that triggers a restock (e.g., Canceled, Failed).
     *
     * @param int $order_id
     * @param int $order_product_id
     */
    private function restockProductStock($order_id, $order_product_id)
    {
        $order_product_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_product` WHERE order_product_id = '" . (int)$order_product_id . "' AND order_id = '" . (int)$order_id . "'");

        if ($order_product_query->num_rows) {
            $product_id = $order_product_query->row['product_id'];
            $quantity = $order_product_query->row['quantity'];

            // Check if stock has already been restocked for this order to prevent double-restocking
            $log_check = $this->db->query("SELECT * FROM `" . DB_PREFIX . "inventory_log` WHERE reference_type = 'order' AND reference_id = '" . (int)$order_id . "' AND product_id = '" . (int)$product_id . "' AND stock_change > 0");

            if (!$log_check->num_rows) {
                // Update product stock
                $this->db->query("UPDATE `" . DB_PREFIX . "product` SET quantity = (quantity + " . (int)$quantity . ") WHERE product_id = '" . (int)$product_id . "' AND subtract = '1'");

                // Get updated product info to get the new current stock
                $product_query = $this->db->query("SELECT quantity FROM `" . DB_PREFIX . "product` WHERE product_id = '" . (int)$product_id . "'");
                $current_stock = ($product_query->num_rows) ? $product_query->row['quantity'] : 0;

                // Add a log entry
                $this->addInventoryLog(array(
                    'product_id'      => $product_id,
                    'option_id'       => null,
                    'option_value_id' => null,
                    'reference_type'  => 'order',
                    'reference_id'    => $order_id,
                    'order_status_id' => $this->config->get('config_order_status_id'), // Use a generic order status if not available
                    'stock_change'    => (int)$quantity,
                    'current_stock'   => $current_stock,
                    'user_id'         => $this->user->getId(),
                    'notes'           => 'Stock restocked on order status change.'
                ));
            }
        }
    }

    private function addInventoryLog($data)
    {
        $this->db->query("INSERT INTO `" . DB_PREFIX . "inventory_log` SET product_id = '" . (int)$data['product_id'] . "', option_id = '" . (int)$data['option_id'] . "', option_value_id = '" . (int)$data['option_value_id'] . "', reference_type = '" . $this->db->escape($data['reference_type']) . "', reference_id = '" . (int)$data['reference_id'] . "', order_status_id = '" . (int)$data['order_status_id'] . "', stock_change = '" . (int)$data['stock_change'] . "', current_stock = '" . (int)$data['current_stock'] . "', user_id = '" . (int)$data['user_id'] . "', notes = '" . $this->db->escape($data['notes']) . "', date_added = NOW()");
    }
}
