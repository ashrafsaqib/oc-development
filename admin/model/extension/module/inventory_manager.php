<?php
class ModelExtensionModuleInventoryManager extends Model {

    public function editProductQuantity($product_id, $stock_change) {
        // Update the product quantity based on stock change
        $this->db->query("UPDATE `" . DB_PREFIX . "product` SET quantity = (quantity + " . (int)$stock_change . ") WHERE product_id = '" . (int)$product_id . "'");
    }  
    public function addInventoryLog($data) {
        $this->db->query("INSERT INTO `" . DB_PREFIX . "inventory_log` SET product_id = '" . (int)$data['product_id'] . "', option_id = '" . (int)$data['option_id'] . "', option_value_id = '" . (int)$data['option_value_id'] . "', reference_type = '" . $this->db->escape($data['reference_type']) . "', reference_id = '" . (int)$data['reference_id'] . "', order_status_id = '" . (int)$data['order_status_id'] . "', stock_change = '" . (int)$data['stock_change'] . "', current_stock = '" . (int)$data['current_stock'] . "', user_id = '" . (int)$data['user_id'] . "', notes = '" . $this->db->escape($data['notes']) . "', date_added = NOW()");
    }

    public function getInventoryLogs($data = array()) {
        $sql = "SELECT i.inventory_log_id, i.product_id, i.stock_change, i.current_stock, i.notes, i.reference_type, i.reference_id, i.order_status_id, i.user_id, i.date_added FROM `" . DB_PREFIX . "inventory_log` i WHERE 1";

        if (!empty($data['filter_product_id'])) {
            $sql .= " AND i.product_id = '" . (int)$data['filter_product_id'] . "'";
        }

        if (!empty($data['filter_reference_type'])) {
            $sql .= " AND i.reference_type = '" . $this->db->escape($data['filter_reference_type']) . "'";
        }

        if (!empty($data['filter_reference_types']) && is_array($data['filter_reference_types']) && count($data['filter_reference_types'])) {
            $escaped_types = array_map([$this->db, 'escape'], $data['filter_reference_types']);
            $type_list = "'" . implode("','", $escaped_types) . "'";
            $sql .= " AND i.reference_type IN (" . $type_list . ")";
        }

        if (!empty($data['filter_notes'])) {
            $sql .= " AND i.notes LIKE '%" . $this->db->escape($data['filter_notes']) . "%'";
        }

        if (!empty($data['filter_date_start'])) {
            $sql .= " AND DATE(i.date_added) >= '" . $this->db->escape($data['filter_date_start']) . "'";
        }

        if (!empty($data['filter_date_end'])) {
            $sql .= " AND DATE(i.date_added) <= '" . $this->db->escape($data['filter_date_end']) . "'";
        }

        $sql .= " ORDER BY i.date_added DESC";

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }

        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function getTotalInventoryLogs($data = array()) {
        $sql = "SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "inventory_log` i WHERE 1";

        if (!empty($data['filter_product_id'])) {
            $sql .= " AND i.product_id = '" . (int)$data['filter_product_id'] . "'";
        }

        if (!empty($data['filter_reference_type'])) {
            $sql .= " AND i.reference_type = '" . $this->db->escape($data['filter_reference_type']) . "'";
        }

        if (!empty($data['filter_reference_types']) && is_array($data['filter_reference_types']) && count($data['filter_reference_types'])) {
            $escaped_types = array_map([$this->db, 'escape'], $data['filter_reference_types']);
            $type_list = "'" . implode("','", $escaped_types) . "'";
            $sql .= " AND i.reference_type IN (" . $type_list . ")";
        }

        if (!empty($data['filter_notes'])) {
            $sql .= " AND i.notes LIKE '%" . $this->db->escape($data['filter_notes']) . "%'";
        }

        if (!empty($data['filter_date_start'])) {
            $sql .= " AND DATE(i.date_added) >= '" . $this->db->escape($data['filter_date_start']) . "'";
        }

        if (!empty($data['filter_date_end'])) {
            $sql .= " AND DATE(i.date_added) <= '" . $this->db->escape($data['filter_date_end']) . "'";
        }

        $query = $this->db->query($sql);

        return $query->row['total'];
    }

    /**
     * Subtracts a product's stock and logs the change.
     * This function should be called from the order controller when a new order is placed
     * or when an order is updated to a status that triggers stock subtraction.
     *
     * @param int $order_id
     * @param int $order_product_id
     */
    public function subtractProductStock($order_id, $order_product_id) {
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
                    'user_id'         => $this->user->getId(),
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
    public function restockProductStock($order_id, $order_product_id) {
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
    public function clearInventoryLogs() {
        $this->db->query("TRUNCATE TABLE `" . DB_PREFIX . "inventory_log`");
    }
    public function clearProductInventoryLogs($product_id) {
        $this->db->query("DELETE FROM `" . DB_PREFIX . "inventory_log` WHERE product_id = '" . (int)$product_id . "'");
    }
    // search by sku, model, name, UPC, EAN, JAN, ISBN, MPN
    public function getProducts($data = array()) {
        $sql = "SELECT p.product_id, pd.name, p.model, p.sku, p.upc, p.ean, p.jan, p.isbn, p.mpn FROM " . DB_PREFIX . "product p left join " . DB_PREFIX . "product_description pd  on (p.product_id = pd.product_id ) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND ";

        if (!empty($data['filter_name'])) {
            $filter = $this->db->escape($data['filter_name']);

            $sql .= "pd.name LIKE '%" . $filter . "%' OR " .
                "p.model LIKE '%" . $filter . "%' OR " .
                "p.sku LIKE '%" . $filter . "%' OR " .
                "p.upc LIKE '%" . $filter . "%' OR " .
                "p.ean LIKE '%" . $filter . "%' OR " .
                "p.jan LIKE '%" . $filter . "%' OR " .
                "p.isbn LIKE '%" . $filter . "%' OR " .
                "p.mpn LIKE '%" . $filter . "%'";
        }

        $sql .= " ORDER BY pd.name ASC";

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }

        $query = $this->db->query($sql);

        return $query->rows;
    }
}