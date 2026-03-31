<?php
class ModelExtensionInventoryDashboard extends Model {

    /**
     * Gets the total count of enabled products.
     * @return int The total number of products.
     */
    public function getTotalProducts() {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "product` WHERE status = 1");
        return $query->row['total'];
    }

    /**
     * Calculates the total value of the inventory based on price and quantity.
     * @return float The total monetary value of the inventory.
     */
    public function getTotalValue() {
        $query = $this->db->query("SELECT SUM(price * quantity) AS total_value FROM `" . DB_PREFIX . "product` WHERE status = 1");
        return (float)$query->row['total_value'];
    }

    /**
     * Counts the number of products that are considered low in stock.
     * The low stock threshold is hard-coded as 5 in this example.
     * @return int The number of low stock products.
     */
    public function getLowStockItems() {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "product` WHERE status = 1 AND quantity <= 5 AND quantity > 0");
        return $query->row['total'];
    }

    /**
     * Counts the number of products that are out of stock.
     * @return int The number of out of stock products.
     */
    public function getOutOfStockItems() {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "product` WHERE status = 1 AND quantity = 0");
        return $query->row['total'];
    }

    /**
     * Gets product counts for each category for the dashboard chart.
     * @return array An array of categories and their respective product counts.
     */
    public function getProductsByCategory() {
        $sql = "SELECT 
                    cd.name AS category_name, 
                    COUNT(p2c.product_id) AS product_count
                FROM `" . DB_PREFIX . "category_description` cd
                JOIN `" . DB_PREFIX . "product_to_category` p2c ON cd.category_id = p2c.category_id
                JOIN `" . DB_PREFIX . "product` p ON p2c.product_id = p.product_id
                WHERE p.status = 1 AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "'
                GROUP BY cd.name
                ORDER BY product_count DESC";

        $query = $this->db->query($sql);
        return $query->rows;
    }

    /**
     * Retrieves a list of recent low stock products for the dashboard table.
     * @return array An array of low stock product details.
     */
    public function getRecentLowStockItems() {
        $sql = "SELECT p.product_id,
                    p.model AS sku,
                    p.quantity AS stock,
                    pd.name AS product_name,
                    cd.name AS category_name
                FROM `" . DB_PREFIX . "product` p
                LEFT JOIN `" . DB_PREFIX . "product_description` pd ON (p.product_id = pd.product_id AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "')
                LEFT JOIN `" . DB_PREFIX . "product_to_category` p2c ON p.product_id = p2c.product_id
                LEFT JOIN `" . DB_PREFIX . "category_description` cd ON (p2c.category_id = cd.category_id AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "')
                WHERE p.status = 1 AND p.quantity <= 5 AND p.quantity > 0
                ORDER BY p.date_modified DESC
                LIMIT 50";

        $query = $this->db->query($sql);
        return $query->rows;
    }
}
