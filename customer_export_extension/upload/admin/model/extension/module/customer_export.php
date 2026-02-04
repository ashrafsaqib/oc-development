<?php
class ModelExtensionModuleCustomerExport extends Model {
	
	public function getCustomers($data = array()) {
		$sql = "SELECT c.*, cgd.name as customer_group 
				FROM " . DB_PREFIX . "customer c 
				LEFT JOIN " . DB_PREFIX . "customer_group_description cgd ON (c.customer_group_id = cgd.customer_group_id) 
				WHERE cgd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_customer_group_id'])) {
			$sql .= " AND c.customer_group_id = '" . (int)$data['filter_customer_group_id'] . "'";
		}

		if (isset($data['filter_status']) && $data['filter_status'] !== '') {
			$sql .= " AND c.status = '" . (int)$data['filter_status'] . "'";
		}

		if (isset($data['filter_approved']) && $data['filter_approved'] !== '') {
			$sql .= " AND c.approved = '" . (int)$data['filter_approved'] . "'";
		}

		if (!empty($data['filter_date_added_from'])) {
			$sql .= " AND DATE(c.date_added) >= '" . $this->db->escape($data['filter_date_added_from']) . "'";
		}

		if (!empty($data['filter_date_added_to'])) {
			$sql .= " AND DATE(c.date_added) <= '" . $this->db->escape($data['filter_date_added_to']) . "'";
		}

		if (isset($data['filter_newsletter']) && $data['filter_newsletter'] !== '') {
			$sql .= " AND c.newsletter = '" . (int)$data['filter_newsletter'] . "'";
		}

		if (!empty($data['filter_search'])) {
			$sql .= " AND (CONCAT(c.firstname, ' ', c.lastname) LIKE '%" . $this->db->escape($data['filter_search']) . "%' 
					  OR c.email LIKE '%" . $this->db->escape($data['filter_search']) . "%')";
		}

		$sql .= " ORDER BY c.date_added DESC";

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 10;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getTotalCustomers($data = array()) {
		$sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "customer c 
				LEFT JOIN " . DB_PREFIX . "customer_group_description cgd ON (c.customer_group_id = cgd.customer_group_id) 
				WHERE cgd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_customer_group_id'])) {
			$sql .= " AND c.customer_group_id = '" . (int)$data['filter_customer_group_id'] . "'";
		}

		if (isset($data['filter_status']) && $data['filter_status'] !== '') {
			$sql .= " AND c.status = '" . (int)$data['filter_status'] . "'";
		}

		if (isset($data['filter_approved']) && $data['filter_approved'] !== '') {
			$sql .= " AND c.approved = '" . (int)$data['filter_approved'] . "'";
		}

		if (!empty($data['filter_date_added_from'])) {
			$sql .= " AND DATE(c.date_added) >= '" . $this->db->escape($data['filter_date_added_from']) . "'";
		}

		if (!empty($data['filter_date_added_to'])) {
			$sql .= " AND DATE(c.date_added) <= '" . $this->db->escape($data['filter_date_added_to']) . "'";
		}

		if (isset($data['filter_newsletter']) && $data['filter_newsletter'] !== '') {
			$sql .= " AND c.newsletter = '" . (int)$data['filter_newsletter'] . "'";
		}

		if (!empty($data['filter_search'])) {
			$sql .= " AND (CONCAT(c.firstname, ' ', c.lastname) LIKE '%" . $this->db->escape($data['filter_search']) . "%' 
					  OR c.email LIKE '%" . $this->db->escape($data['filter_search']) . "%')";
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getCustomersForExport($data = array()) {
		$sql = "SELECT c.*, cgd.name as customer_group 
				FROM " . DB_PREFIX . "customer c 
				LEFT JOIN " . DB_PREFIX . "customer_group_description cgd ON (c.customer_group_id = cgd.customer_group_id) 
				WHERE cgd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_customer_group_id'])) {
			$sql .= " AND c.customer_group_id = '" . (int)$data['filter_customer_group_id'] . "'";
		}

		if (isset($data['filter_status']) && $data['filter_status'] !== '') {
			$sql .= " AND c.status = '" . (int)$data['filter_status'] . "'";
		}

		if (isset($data['filter_approved']) && $data['filter_approved'] !== '') {
			$sql .= " AND c.approved = '" . (int)$data['filter_approved'] . "'";
		}

		if (!empty($data['filter_date_added_from'])) {
			$sql .= " AND DATE(c.date_added) >= '" . $this->db->escape($data['filter_date_added_from']) . "'";
		}

		if (!empty($data['filter_date_added_to'])) {
			$sql .= " AND DATE(c.date_added) <= '" . $this->db->escape($data['filter_date_added_to']) . "'";
		}

		if (isset($data['filter_newsletter']) && $data['filter_newsletter'] !== '') {
			$sql .= " AND c.newsletter = '" . (int)$data['filter_newsletter'] . "'";
		}

		if (!empty($data['filter_search'])) {
			$sql .= " AND (CONCAT(c.firstname, ' ', c.lastname) LIKE '%" . $this->db->escape($data['filter_search']) . "%' 
					  OR c.email LIKE '%" . $this->db->escape($data['filter_search']) . "%')";
		}

		$sql .= " ORDER BY c.date_added DESC";

		// Apply limit if specified
		if (isset($data['export_limit']) && $data['export_limit'] > 0) {
			$sql .= " LIMIT " . (int)$data['export_limit'];
		}

		$query = $this->db->query($sql);

		$customers = array();

		foreach ($query->rows as $customer) {
			// Get total orders
			$order_query = $this->db->query("SELECT COUNT(*) as total, SUM(total) as amount FROM `" . DB_PREFIX . "order` WHERE customer_id = '" . (int)$customer['customer_id'] . "' AND order_status_id > 0");
			
			$total_orders = $order_query->row['total'];
			$total_spent = $order_query->row['amount'] ? number_format($order_query->row['amount'], 2) : '0.00';

			// Get addresses
			$address_query = $this->db->query("SELECT COUNT(*) as total FROM `" . DB_PREFIX . "address` WHERE customer_id = '" . (int)$customer['customer_id'] . "'");
			$total_addresses = $address_query->row['total'];

			// Get all addresses details
			$addresses_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "address` WHERE customer_id = '" . (int)$customer['customer_id'] . "'");
			$addresses_list = array();
			foreach ($addresses_query->rows as $address) {
				$addresses_list[] = $address['firstname'] . ' ' . $address['lastname'] . ', ' . $address['address_1'] . ', ' . $address['city'] . ', ' . $address['postcode'];
			}
			$addresses = implode(' | ', $addresses_list);

			// Get reward points
			$reward_query = $this->db->query("SELECT SUM(points) as total FROM `" . DB_PREFIX . "customer_reward` WHERE customer_id = '" . (int)$customer['customer_id'] . "'");
			$reward_points = $reward_query->row['total'] ? $reward_query->row['total'] : 0;

			// Get store credit
			$transaction_query = $this->db->query("SELECT SUM(amount) as total FROM `" . DB_PREFIX . "customer_transaction` WHERE customer_id = '" . (int)$customer['customer_id'] . "'");
			$store_credit = $transaction_query->row['total'] ? number_format($transaction_query->row['total'], 2) : '0.00';

			// Get wishlist products
			$wishlist_query = $this->db->query("SELECT pd.name FROM `" . DB_PREFIX . "customer_wishlist` cw LEFT JOIN `" . DB_PREFIX . "product_description` pd ON (cw.product_id = pd.product_id) WHERE cw.customer_id = '" . (int)$customer['customer_id'] . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'");
			$wishlist_products = array();
			foreach ($wishlist_query->rows as $product) {
				$wishlist_products[] = $product['name'];
			}
			$wishlist = implode(', ', $wishlist_products);

			// Get last login
			$login_query = $this->db->query("SELECT date_modified FROM `" . DB_PREFIX . "customer_login` WHERE email = '" . $this->db->escape($customer['email']) . "' ORDER BY date_modified DESC LIMIT 1");
			$last_login = $login_query->num_rows ? $login_query->row['date_modified'] : 'Never';

			// Get total logins
			$login_total_query = $this->db->query("SELECT SUM(total) as total_logins FROM `" . DB_PREFIX . "customer_login` WHERE email = '" . $this->db->escape($customer['email']) . "'");
			$total_logins = $login_total_query->row['total_logins'] ? $login_total_query->row['total_logins'] : 0;

			$customers[] = array(
				'customer_id' => $customer['customer_id'],
				'customer_group_id' => $customer['customer_group_id'],
				'address_id' => $customer['address_id'],
				'firstname' => $customer['firstname'],
				'lastname' => $customer['lastname'],
				'email' => $customer['email'],
				'telephone' => $customer['telephone'],
				'fax' => $customer['fax'],
				'customer_group' => $customer['customer_group'],
				'status' => $customer['status'],
				'approved' => $customer['approved'],
				'safe' => $customer['safe'],
				'newsletter' => $customer['newsletter'],
				'ip' => $customer['ip'],
				'date_added' => $customer['date_added'],
				'total_orders' => $total_orders,
				'total_spent' => $total_spent,
				'total_addresses' => $total_addresses,
				'reward_points' => $reward_points,
				'store_credit' => $store_credit,
				'addresses' => $addresses,
				'wishlist_products' => $wishlist,
				'last_login' => $last_login,
				'total_logins' => $total_logins
			);
		}

		return $customers;
	}
}
