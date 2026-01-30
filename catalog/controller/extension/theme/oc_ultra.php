<?php
class ControllerExtensionThemeOcUltra extends Controller {
    public function subscribe() {
        $json = array();

        $this->load->language('extension/theme/oc_ultra');

        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            if ((utf8_strlen($this->request->post['email']) > 96) || !filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL)) {
                $json['error'] = $this->language->get('error_email');
            }

            if (!isset($json['error'])) {
                // Check if email already exists
                $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "oc_ultra_newsletter WHERE email = '" . $this->db->escape($this->request->post['email']) . "'");
                
                if ($query->num_rows) {
                     $json['error'] = $this->language->get('error_exists');
                }
            }

            if (!isset($json['error'])) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "oc_ultra_newsletter SET email = '" . $this->db->escape($this->request->post['email']) . "', ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "', status = 1, date_added = NOW()");
                
                $json['success'] = $this->language->get('text_success');
            }
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
}
