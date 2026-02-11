<?php
namespace Opencart\Catalog\Controller\Extension\Custom\Event;

class RegistrationUpload extends \Opencart\System\Engine\Controller {
	/**
	 * Add Field
	 * 
	 * Triggered by view/account/register/before event
	 * Ensures the custom field for file upload is present in the registration form data
	 *
	 * @param string $route
	 * @param array $data
	 * @param string $code
	 * @param mixed $output
	 * @return void
	 */
	public function addField(string &$route, array &$data, string &$code, string &$output): void {
		// Check if extension is enabled
		if (!$this->config->get('other_registration_upload_status')) {
			return;
		}

		$custom_field_id = (int)$this->config->get('other_registration_upload_custom_field_id');

		if (!$custom_field_id) {
			return;
		}

		if (!isset($data['custom_fields']) || !is_array($data['custom_fields'])) {
			$data['custom_fields'] = [];
		}

		// Check if custom field already exists
		foreach ($data['custom_fields'] as $custom_field) {
			if ((int)$custom_field['custom_field_id'] === $custom_field_id) {
				return;
			}
		}

		$this->load->model('account/custom_field');

		$custom_field_info = $this->model_account_custom_field->getCustomField($custom_field_id);

		if (!$custom_field_info) {
			return;
		}

		$data['custom_fields'][] = [
			'custom_field_value' => [],
			'required' => (bool)$this->config->get('other_registration_upload_required')
		] + $custom_field_info;
	}
}
