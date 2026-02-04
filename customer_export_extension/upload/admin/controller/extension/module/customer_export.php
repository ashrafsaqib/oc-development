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
			'filter_search' => isset($this->request->post['filter_search']) ? $this->request->post['filter_search'] : null
		);

		$customers = $this->model_extension_module_customer_export->getCustomersForExport($filter_data);

		// Set headers for CSV download
		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename=customers_export_' . date('Y-m-d_H-i-s') . '.csv');
		header('Pragma: no-cache');
		header('Expires: 0');

		$output = fopen('php://output', 'w');

		// Write UTF-8 BOM for Excel compatibility
		fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

		// CSV Headers
		$headers = array(
			'Customer ID',
			'First Name',
			'Last Name',
			'Email',
			'Telephone',
			'Fax',
			'Customer Group',
			'Status',
			'Approved',
			'Safe',
			'Newsletter',
			'IP',
			'Date Added',
			'Total Orders',
			'Total Spent',
			'Total Addresses',
			'Reward Points',
			'Store Credit',
			'Addresses',
			'Wishlist Products',
			'Last Login',
			'Total Logins'
		);

		fputcsv($output, $headers);

		// Write customer data
		foreach ($customers as $customer) {
			$row = array(
				$customer['customer_id'],
				$customer['firstname'],
				$customer['lastname'],
				$customer['email'],
				$customer['telephone'],
				$customer['fax'],
				$customer['customer_group'],
				$customer['status'] ? 'Enabled' : 'Disabled',
				$customer['approved'] ? 'Yes' : 'No',
				$customer['safe'] ? 'Yes' : 'No',
				$customer['newsletter'] ? 'Subscribed' : 'Unsubscribed',
				$customer['ip'],
				$customer['date_added'],
				$customer['total_orders'],
				$customer['total_spent'],
				$customer['total_addresses'],
				$customer['reward_points'],
				$customer['store_credit'],
				$customer['addresses'],
				$customer['wishlist_products'],
				$customer['last_login'],
				$customer['total_logins']
			);

			fputcsv($output, $row);
		}

		fclose($output);
		exit;
	}

	public function install() {
		// No installation needed for this module
	}

	public function uninstall() {
		// No uninstallation needed for this module
	}
}
