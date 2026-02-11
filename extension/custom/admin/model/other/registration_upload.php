<?php
namespace Opencart\Admin\Model\Extension\Custom\Other;

class RegistrationUpload extends \Opencart\System\Engine\Model {
	/**
	 * Get Registration Uploads
	 * 
	 * Retrieves all customers who have uploaded files during registration
	 *
	 * @param int $custom_field_id The custom field ID for the file upload
	 * @return array List of customers with their uploaded files
	 */
	public function getRegistrationUploads(int $custom_field_id): array {
		$query = $this->db->query("
			SELECT 
				c.customer_id,
				c.firstname,
				c.lastname,
				c.email,
				c.custom_field,
				c.date_added
			FROM `" . DB_PREFIX . "customer` c
			WHERE c.custom_field IS NOT NULL 
			AND c.custom_field != ''
			AND c.custom_field != '{}'
			ORDER BY c.date_added DESC
		");

		$results = [];

		foreach ($query->rows as $row) {
			$custom_fields = json_decode($row['custom_field'], true);

			// Check if this customer has the upload custom field
			if (isset($custom_fields[$custom_field_id]) && !empty($custom_fields[$custom_field_id])) {
				$results[] = [
					'customer_id' => $row['customer_id'],
					'firstname' => $row['firstname'],
					'lastname' => $row['lastname'],
					'email' => $row['email'],
					'upload_code' => $custom_fields[$custom_field_id],
					'date_added' => $row['date_added']
				];
			}
		}

		return $results;
	}

	/**
	 * Get Total Registration Uploads
	 * 
	 * Count total customers with uploaded files
	 *
	 * @param int $custom_field_id The custom field ID for the file upload
	 * @return int Total count
	 */
	public function getTotalRegistrationUploads(int $custom_field_id): int {
		$uploads = $this->getRegistrationUploads($custom_field_id);
		return count($uploads);
	}
}
