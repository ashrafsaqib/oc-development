<?php
namespace Opencart\Admin\Controller\Extension\Custom\Other;

class RegistrationUpload extends \Opencart\System\Engine\Controller {
	public function index(): void {
		$data = $this->load->language('extension/custom/other/registration_upload');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = [];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('extension/other', 'user_token=' . $this->session->data['user_token'])
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/custom/other/registration_upload', 'user_token=' . $this->session->data['user_token'])
		];

		$data['save'] = $this->url->link('extension/custom/other/registration_upload.save', 'user_token=' . $this->session->data['user_token']);
		$data['back'] = $this->url->link('extension/other', 'user_token=' . $this->session->data['user_token']);

		// Get uploaded files list
		$data['uploads'] = [];

		$custom_field_id = (int)$this->config->get('other_registration_upload_custom_field_id');

		if ($custom_field_id) {
			$this->load->model('extension/custom/other/registration_upload');

			$uploads = $this->model_extension_custom_other_registration_upload->getRegistrationUploads($custom_field_id);

			$this->load->model('tool/upload');

			foreach ($uploads as $upload) {
				$upload_info = [];
				
				if ($upload['upload_code']) {
					$upload_info = $this->model_tool_upload->getUploadByCode($upload['upload_code']);
				}

				$data['uploads'][] = [
					'customer_id' => $upload['customer_id'],
					'customer_name' => $upload['firstname'] . ' ' . $upload['lastname'],
					'customer_email' => $upload['email'],
					'upload_code' => $upload['upload_code'],
					'filename' => $upload_info['name'] ?? 'N/A',
					'file_path' => $upload_info['filename'] ?? '',
					'date_added' => $upload['date_added'],
					'customer_link' => $this->url->link('customer/customer.form', 'user_token=' . $this->session->data['user_token'] . '&customer_id=' . $upload['customer_id']),
					'download_link' => $upload_info ? $this->url->link('tool/upload.download', 'user_token=' . $this->session->data['user_token'] . '&code=' . $upload['upload_code']) : ''
				];
			}
		}

		// Load settings
		$data['other_registration_upload_status'] = $this->config->get('other_registration_upload_status');
		$data['other_registration_upload_required'] = $this->config->get('other_registration_upload_required');
		$data['other_registration_upload_allowed_extensions'] = $this->config->get('other_registration_upload_allowed_extensions') ?: 'jpg,jpeg,png,gif,pdf,doc,docx,txt';
		$data['other_registration_upload_max_size'] = $this->config->get('other_registration_upload_max_size') ?: '2';

		$data['user_token'] = $this->session->data['user_token'];

		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');

		$this->response->setOutput($this->load->view('extension/custom/other/registration_upload', $data));
	}

	public function save(): void {
		$this->load->language('extension/custom/other/registration_upload');

		$json = [];

		if (!$this->user->hasPermission('modify', 'extension/custom/other/registration_upload')) {
			$json['error'] = $this->language->get('error_permission');
		}

		// Validate allowed extensions
		if (isset($this->request->post['other_registration_upload_allowed_extensions'])) {
			$extensions = $this->request->post['other_registration_upload_allowed_extensions'];
			if (empty(trim($extensions))) {
				$json['error']['allowed_extensions'] = $this->language->get('error_allowed_extensions');
			}
		}

		// Validate max size
		if (isset($this->request->post['other_registration_upload_max_size'])) {
			$max_size = (float)$this->request->post['other_registration_upload_max_size'];
			if ($max_size <= 0 || $max_size > 50) {
				$json['error']['max_size'] = $this->language->get('error_max_size');
			}
		}

		if (!$json) {
			$this->load->model('setting/setting');
			$this->load->model('customer/custom_field');

			$custom_field_id = (int)$this->config->get('other_registration_upload_custom_field_id');

			// Update custom field required status if custom field exists
			if ($custom_field_id) {
				$custom_field_info = $this->model_customer_custom_field->getCustomField($custom_field_id);

				if ($custom_field_info) {
					$this->load->model('customer/customer_group');
					$customer_groups = $this->model_customer_customer_group->getCustomerGroups();

					// Update required status for all customer groups
					$this->model_customer_custom_field->deleteCustomerGroups($custom_field_id);

					foreach ($customer_groups as $customer_group) {
						$custom_field_customer_group = [
							'customer_group_id' => (int)$customer_group['customer_group_id']
						];

						if (isset($this->request->post['other_registration_upload_required']) && $this->request->post['other_registration_upload_required']) {
							$custom_field_customer_group['required'] = 1;
						}

						$this->model_customer_custom_field->addCustomerGroup($custom_field_id, $custom_field_customer_group);
					}
				}
			}

			// Save settings
			$settings = [
				'other_registration_upload_status' => isset($this->request->post['other_registration_upload_status']) ? 1 : 0,
				'other_registration_upload_required' => isset($this->request->post['other_registration_upload_required']) ? 1 : 0,
				'other_registration_upload_allowed_extensions' => $this->request->post['other_registration_upload_allowed_extensions'] ?? 'jpg,jpeg,png,gif,pdf,doc,docx,txt',
				'other_registration_upload_max_size' => (float)($this->request->post['other_registration_upload_max_size'] ?? 2),
				'other_registration_upload_custom_field_id' => $custom_field_id,
				'other_registration_upload_created' => $this->config->get('other_registration_upload_created')
			];

			$this->model_setting_setting->editSetting('other_registration_upload', $settings);

			$json['success'] = $this->language->get('text_success');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function install(): void {
		$this->load->language('extension/custom/other/registration_upload');

		$this->load->model('setting/setting');
		$this->load->model('setting/event');
		$this->load->model('setting/extension');
		$this->load->model('customer/custom_field');
		$this->load->model('customer/customer_group');
		$this->load->model('localisation/language');

		// Ensure extension install record exists
		$this->ensureExtensionInstall();

		$setting_info = $this->model_setting_setting->getSetting('other_registration_upload');

		$custom_field_id = (int)($setting_info['other_registration_upload_custom_field_id'] ?? 0);
		$created = (int)($setting_info['other_registration_upload_created'] ?? 0);

		if (!$custom_field_id) {
			$custom_field_id = $this->getExistingCustomFieldId();

			if ($custom_field_id) {
				$created = 0;
			} else {
				$custom_field_id = $this->createCustomField();
				$created = 1;
			}
		}

		$this->model_setting_setting->editSetting('other_registration_upload', [
			'other_registration_upload_status' => 1,
			'other_registration_upload_custom_field_id' => $custom_field_id,
			'other_registration_upload_created' => $created
		]);

		$this->model_setting_event->deleteEventByCode('custom_registration_upload');

		$this->model_setting_event->addEvent([
			'code'        => 'custom_registration_upload',
			'description' => 'Registration upload field on account/register',
			'trigger'     => 'view/account/register/before',
			'action'      => 'extension/custom/event/registration_upload.addField',
			'status'      => 1,
			'sort_order'  => 0
		]);
	}

	public function uninstall(): void {
		$this->load->model('setting/setting');
		$this->load->model('setting/event');
		$this->load->model('customer/custom_field');

		$setting_info = $this->model_setting_setting->getSetting('other_registration_upload');

		$custom_field_id = (int)($setting_info['other_registration_upload_custom_field_id'] ?? 0);
		$created = (int)($setting_info['other_registration_upload_created'] ?? 0);

		if ($created && $custom_field_id) {
			$this->model_customer_custom_field->deleteCustomField($custom_field_id);
		}

		$this->model_setting_event->deleteEventByCode('custom_registration_upload');
		$this->model_setting_setting->deleteSetting('other_registration_upload');
	}

	private function getExistingCustomFieldId(): int {
		$custom_fields = $this->model_customer_custom_field->getCustomFields([
			'filter_name' => $this->language->get('entry_custom_field_name'),
			'filter_location' => 'account'
		]);

		if ($custom_fields) {
			return (int)$custom_fields[0]['custom_field_id'];
		}

		return 0;
	}

	private function createCustomField(): int {
		$custom_field_description = [];
		$languages = $this->model_localisation_language->getLanguages();

		foreach ($languages as $language) {
			$custom_field_description[$language['language_id']] = [
				'name' => $this->language->get('entry_custom_field_name')
			];
		}

		$custom_field_customer_group = [];
		$customer_groups = $this->model_customer_customer_group->getCustomerGroups();

		foreach ($customer_groups as $customer_group) {
			$custom_field_customer_group[] = [
				'customer_group_id' => (int)$customer_group['customer_group_id']
			];
		}

		$custom_field_data = [
			'custom_field_description' => $custom_field_description,
			'type' => 'file',
			'value' => '',
			'validation' => '',
			'location' => 'account',
			'status' => 1,
			'sort_order' => 0,
			'custom_field_customer_group' => $custom_field_customer_group
		];

		return $this->model_customer_custom_field->addCustomField($custom_field_data);
	}

	private function ensureExtensionInstall(): void {
		// Check if extension install record exists
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "extension_install` WHERE `code` = 'custom'");

		if (!$query->num_rows) {
			// Add extension install record so autoloader can register it
			$this->db->query("INSERT INTO `" . DB_PREFIX . "extension_install` SET 
				`extension_id` = '0',
				`extension_download_id` = '0',
				`name` = 'Registration Upload Field',
				`description` = 'Adds file upload to registration form',
				`code` = 'custom',
				`version` = '1.0.0',
				`author` = 'Custom',
				`link` = '',
				`status` = '1',
				`date_added` = NOW()
			");
		}
	}
}
