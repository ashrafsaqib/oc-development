<?php
class ModelExtensionFeedCustomerExport extends Model {
	
	public function getCustomers($limit = 100) {
		$sql = "SELECT c.customer_id, c.firstname, c.lastname, c.email, c.telephone, c.customer_group_id, c.status, c.approved, c.date_added, cgd.name as customer_group 
				FROM " . DB_PREFIX . "customer c 
				LEFT JOIN " . DB_PREFIX . "customer_group_description cgd ON (c.customer_group_id = cgd.customer_group_id) 
				WHERE cgd.language_id = '" . (int)$this->config->get('config_language_id') . "' 
				AND c.status = '1' 
				AND c.approved = '1' 
				ORDER BY c.date_added DESC";
		
		if ($limit > 0) {
			$sql .= " LIMIT " . (int)$limit;
		}
		
		$query = $this->db->query($sql);
		
		$customers = array();
		
		foreach ($query->rows as $customer) {
			$customers[] = array(
				'customer_id' => $customer['customer_id'],
				'firstname' => $customer['firstname'],
				'lastname' => $customer['lastname'],
				'email' => $customer['email'],
				'telephone' => $customer['telephone'],
				'customer_group' => $customer['customer_group'],
				'customer_group_id' => $customer['customer_group_id'],
				'status' => $customer['status'] ? 'Active' : 'Inactive',
				'approved' => $customer['approved'] ? 'Yes' : 'No',
				'date_added' => $customer['date_added']
			);
		}
		
		return $customers;
	}
}
