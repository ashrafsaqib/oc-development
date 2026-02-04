<?php
class ControllerExtensionModuleCustomerExport extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/module/customer_export');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['heading_title'] = $this->language->get('heading_title');
		$data['text_list'] = $this->language->get('text_list');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_all_customer_groups'] = $this->language->get('text_all_customer_groups');
		$data['text_all_status'] = $this->language->get('text_all_status');
		$data['text_all_approved'] = $this->language->get('text_all_approved');
		$data['text_yes'] = $this->language->get('text_yes');
		$data['text_no'] = $this->language->get('text_no');
		$data['text_newsletter_all'] = $this->language->get('text_newsletter_all');
		$data['text_newsletter_subscribed'] = $this->language->get('text_newsletter_subscribed');
		$data['text_newsletter_unsubscribed'] = $this->language->get('text_newsletter_unsubscribed');

		$data['entry_customer_group'] = $this->language->get('entry_customer_group');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_approved'] = $this->language->get('entry_approved');
		$data['entry_date_added_from'] = $this->language->get('entry_date_added_from');
		$data['entry_date_added_to'] = $this->language->get('entry_date_added_to');
		$data['entry_newsletter'] = $this->language->get('entry_newsletter');
		$data['entry_search'] = $this->language->get('entry_search');
		$data['entry_export_limit'] = $this->language->get('entry_export_limit');
		$data['entry_export_format'] = $this->language->get('entry_export_format');
		$data['entry_include_ids'] = $this->language->get('entry_include_ids');
		
		$data['text_export_settings'] = $this->language->get('text_export_settings');
		$data['text_select_columns'] = $this->language->get('text_select_columns');
		$data['text_format_csv'] = $this->language->get('text_format_csv');
		$data['text_format_xml'] = $this->language->get('text_format_xml');
		$data['text_format_json'] = $this->language->get('text_format_json');
		$data['help_export_limit'] = $this->language->get('help_export_limit');
		$data['help_include_ids'] = $this->language->get('help_include_ids');

		$data['column_customer_id'] = $this->language->get('column_customer_id');
		$data['column_name'] = $this->language->get('column_name');
		$data['column_email'] = $this->language->get('column_email');
		$data['column_customer_group'] = $this->language->get('column_customer_group');
		$data['column_status'] = $this->language->get('column_status');
		$data['column_approved'] = $this->language->get('column_approved');
		$data['column_date_added'] = $this->language->get('column_date_added');

		$data['button_filter'] = $this->language->get('button_filter');
		$data['button_export'] = $this->language->get('button_export');

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/customer_export', 'token=' . $this->session->data['token'], true)
		);

		$data['export'] = $this->url->link('extension/module/customer_export/export', 'token=' . $this->session->data['token'], true);

		// Get customer groups
		$this->load->model('customer/customer_group');
		$data['customer_groups'] = $this->model_customer_customer_group->getCustomerGroups();

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$data['token'] = $this->session->data['token'];

		$this->response->setOutput($this->load->view('extension/module/customer_export', $data));
	}

	public function getList() {
		$this->load->language('extension/module/customer_export');
		$this->load->model('extension/module/customer_export');

		$filter_data = array(
			'filter_customer_group_id' => isset($this->request->get['filter_customer_group_id']) ? $this->request->get['filter_customer_group_id'] : null,
			'filter_status' => isset($this->request->get['filter_status']) ? $this->request->get['filter_status'] : null,
			'filter_approved' => isset($this->request->get['filter_approved']) ? $this->request->get['filter_approved'] : null,
			'filter_date_added_from' => isset($this->request->get['filter_date_added_from']) ? $this->request->get['filter_date_added_from'] : null,
			'filter_date_added_to' => isset($this->request->get['filter_date_added_to']) ? $this->request->get['filter_date_added_to'] : null,
			'filter_newsletter' => isset($this->request->get['filter_newsletter']) ? $this->request->get['filter_newsletter'] : null,
			'filter_search' => isset($this->request->get['filter_search']) ? $this->request->get['filter_search'] : null,
			'start' => isset($this->request->get['start']) ? $this->request->get['start'] : 0,
			'limit' => isset($this->request->get['limit']) ? $this->request->get['limit'] : 10
		);

		$customer_total = $this->model_extension_module_customer_export->getTotalCustomers($filter_data);
		$results = $this->model_extension_module_customer_export->getCustomers($filter_data);

		$data = array();

		foreach ($results as $result) {
			$data[] = array(
				'customer_id' => $result['customer_id'],
				'name' => $result['firstname'] . ' ' . $result['lastname'],
				'email' => $result['email'],
				'customer_group' => $result['customer_group'],
				'status' => $result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
				'approved' => $result['approved'] ? $this->language->get('text_yes') : $this->language->get('text_no'),
				'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added']))
			);
		}

		$json = array(
			'customers' => $data,
			'total' => $customer_total
		);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function export() {
		$this->load->language('extension/module/customer_export');
		$this->load->model('extension/module/customer_export');

		$filter_data = array(
			'filter_customer_group_id' => isset($this->request->post['filter_customer_group_id']) ? $this->request->post['filter_customer_group_id'] : null,
			'filter_status' => isset($this->request->post['filter_status']) ? $this->request->post['filter_status'] : null,
			'filter_approved' => isset($this->request->post['filter_approved']) ? $this->request->post['filter_approved'] : null,
			'filter_date_added_from' => isset($this->request->post['filter_date_added_from']) ? $this->request->post['filter_date_added_from'] : null,
			'filter_date_added_to' => isset($this->request->post['filter_date_added_to']) ? $this->request->post['filter_date_added_to'] : null,
			'filter_newsletter' => isset($this->request->post['filter_newsletter']) ? $this->request->post['filter_newsletter'] : null,
			'filter_search' => isset($this->request->post['filter_search']) ? $this->request->post['filter_search'] : null,
			'export_limit' => isset($this->request->post['export_limit']) && $this->request->post['export_limit'] ? (int)$this->request->post['export_limit'] : null
		);

		$export_format = isset($this->request->post['export_format']) ? $this->request->post['export_format'] : 'csv';
		$include_ids = isset($this->request->post['include_ids']) ? (int)$this->request->post['include_ids'] : 0;
		$selected_columns = isset($this->request->post['export_columns']) ? $this->request->post['export_columns'] : array();

		$customers = $this->model_extension_module_customer_export->getCustomersForExport($filter_data);

		// Define all available columns
		$all_columns = array(
			'customer_id' => 'Customer ID',
			'customer_group_id' => 'Customer Group ID',
			'address_id' => 'Default Address ID',
			'firstname' => 'First Name',
			'lastname' => 'Last Name',
			'email' => 'Email',
			'telephone' => 'Telephone',
			'fax' => 'Fax',
			'customer_group' => 'Customer Group',
			'status' => 'Status',
			'approved' => 'Approved',
			'safe' => 'Safe',
			'newsletter' => 'Newsletter',
			'ip' => 'IP',
			'date_added' => 'Date Added',
			'total_orders' => 'Total Orders',
			'total_spent' => 'Total Spent',
			'total_addresses' => 'Total Addresses',
			'reward_points' => 'Reward Points',
			'store_credit' => 'Store Credit',
			'addresses' => 'Addresses',
			'wishlist_products' => 'Wishlist Products',
			'last_login' => 'Last Login',
			'total_logins' => 'Total Logins'
		);

		// Filter columns based on selection and include_ids setting
		if (!empty($selected_columns)) {
			$columns_to_export = array();
			foreach ($selected_columns as $column) {
				if (isset($all_columns[$column])) {
					$columns_to_export[$column] = $all_columns[$column];
				}
			}
		} else {
			$columns_to_export = $all_columns;
		}

		// Remove ID columns if not selected
		if (!$include_ids) {
			unset($columns_to_export['customer_id']);
			unset($columns_to_export['customer_group_id']);
			unset($columns_to_export['address_id']);
		}

		// Prepare data for export
		$export_data = array();
		foreach ($customers as $customer) {
			$row = array();
			foreach ($columns_to_export as $key => $label) {
				if ($key == 'status') {
					$row[$key] = $customer[$key] ? 'Enabled' : 'Disabled';
				} elseif ($key == 'approved' || $key == 'safe') {
					$row[$key] = $customer[$key] ? 'Yes' : 'No';
				} elseif ($key == 'newsletter') {
					$row[$key] = $customer[$key] ? 'Subscribed' : 'Unsubscribed';
				} else {
					$row[$key] = isset($customer[$key]) ? $customer[$key] : '';
				}
			}
			$export_data[] = $row;
		}

		// Export based on format
		if ($export_format == 'xml') {
			$this->exportXML($export_data, $columns_to_export);
		} elseif ($export_format == 'json') {
			$this->exportJSON($export_data);
		} else {
			$this->exportCSV($export_data, $columns_to_export);
		}
	}

	private function exportCSV($data, $columns) {
		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename=customers_export_' . date('Y-m-d_H-i-s') . '.csv');
		header('Pragma: no-cache');
		header('Expires: 0');

		$output = fopen('php://output', 'w');
		fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

		fputcsv($output, array_values($columns));

		foreach ($data as $row) {
			fputcsv($output, array_values($row));
		}

		fclose($output);
		exit;
	}

	private function exportXML($data, $columns) {
		header('Content-Type: text/xml; charset=utf-8');
		header('Content-Disposition: attachment; filename=customers_export_' . date('Y-m-d_H-i-s') . '.xml');
		header('Pragma: no-cache');
		header('Expires: 0');

		$xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><customers></customers>');

		foreach ($data as $customer_data) {
			$customer = $xml->addChild('customer');
			foreach ($customer_data as $key => $value) {
				$customer->addChild($key, htmlspecialchars($value));
			}
		}

		echo $xml->asXML();
		exit;
	}

	private function exportJSON($data) {
		header('Content-Type: application/json; charset=utf-8');
		header('Content-Disposition: attachment; filename=customers_export_' . date('Y-m-d_H-i-s') . '.json');
		header('Pragma: no-cache');
		header('Expires: 0');

		echo json_encode(array('customers' => $data), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
		exit;
	}

	public function install() {
		// No installation needed for this module
	}

	public function uninstall() {
		// No uninstallation needed for this module
	}
}
