<?php
class ControllerExtensionModuleInventoryReport extends Controller
{
    public function index()
    {
        $this->load->language('extension/module/inventory');
        $this->load->model('extension/module/inventory_manager');
        $this->load->model('catalog/product');

        $this->document->setTitle($this->language->get('heading_title'));

        // Get all filter params from URL
        $filter_product_id = isset($this->request->get['filter_product_id']) ? $this->request->get['filter_product_id'] : '';
        $filter_reference_type = isset($this->request->get['filter_reference_type']) ? $this->request->get['filter_reference_type'] : '';
        $filter_notes = isset($this->request->get['filter_notes']) ? $this->request->get['filter_notes'] : '';
        $filter_date_start = isset($this->request->get['filter_date_start']) ? $this->request->get['filter_date_start'] : '';
        $filter_date_end = isset($this->request->get['filter_date_end']) ? $this->request->get['filter_date_end'] : '';

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/module/inventory_manager', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['heading_title'] = $this->language->get('heading_title');

        // Load language strings for the timeline
        $data['text_manual_entry'] = $this->language->get('text_manual_entry');
        $data['text_order_update'] = $this->language->get('text_order_update');
        $data['text_no_results']   = $this->language->get('text_no_results');

        $data['inventory_logs'] = array();

        // Support multiple reference types as array
        $reference_types = array();
        if ($filter_reference_type) {
            $reference_types = explode(',', $filter_reference_type);
        }

        $filter_data = array(
            'filter_product_id'      => $filter_product_id,
            'filter_reference_types' => $reference_types,
            'filter_notes'           => $filter_notes,
            'filter_date_start'      => $filter_date_start,
            'filter_date_end'        => $filter_date_end
        );

        $logs = $this->model_extension_module_inventory_manager->getInventoryLogs($filter_data);

        $data['action'] = $this->url->link('extension/module/inventory_manager', 'user_token=' . $this->session->data['user_token'], true);
        $data['clear_logs'] = $this->url->link('extension/module/inventory_report/clearlogs', 'user_token=' . $this->session->data['user_token'].'&filter_product_id='.$filter_product_id, true);

        $data['inventory_events'] = $logs;

        $data['product'] = '';
        if ($filter_product_id) {
            $product_info = $this->model_catalog_product->getProduct($filter_product_id);
            if ($product_info) {
                $data['product'] = $product_info['name'];
            }
        }

        $data['filter_product_id'] = $filter_product_id;
        $data['filter_reference_type'] = $filter_reference_type;
        $data['filter_notes'] = $filter_notes;
        $data['filter_date_start'] = $filter_date_start;
        $data['filter_date_end'] = $filter_date_end;
        $data['user_token'] = $this->session->data['user_token'];

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/inventory_report', $data));
    }

    public function autocomplete()
    {
        $json = array();

        if (isset($this->request->get['filter_name'])) {
            $this->load->model('catalog/product');

            $filter_data = array(
                'filter_name' => $this->request->get['filter_name'],
                'start'       => 0,
                'limit'       => 5
            );

            $results = $this->model_catalog_product->getProducts($filter_data);

            foreach ($results as $result) {
                $json[] = array(
                    'product_id' => $result['product_id'],
                    'name'       => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
                );
            }
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
    public function clearlogs() {
        $this->load->model('extension/module/inventory_manager');
        $this->load->language('extension/module/inventory');

        // Clear all inventory logs
        $this->model_extension_module_inventory_manager->clearProductInventoryLogs($this->request->get['filter_product_id']);

        // Set success message
        $this->session->data['success'] = $this->language->get('text_success_clear_logs');

        // Redirect back to the inventory report page
        $this->response->redirect($this->url->link('extension/module/inventory_manager', 'user_token=' . $this->session->data['user_token'], true));
    }
}
