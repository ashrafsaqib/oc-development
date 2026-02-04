<?php
class ControllerExtensionFeedCustomerExport extends Controller {
	
	public function index() {
		$this->load->model('setting/setting');
		
		$settings = $this->model_setting_setting->getSetting('customer_export_feed');
		
		// Check if feed is enabled
		if (empty($settings['customer_export_feed_status'])) {
			$this->response->setOutput('Feed is disabled');
			return;
		}
		
		$feed_format = isset($settings['customer_export_feed_format']) ? $settings['customer_export_feed_format'] : 'json';
		$feed_limit = isset($settings['customer_export_feed_limit']) ? (int)$settings['customer_export_feed_limit'] : 100;
		$cache_time = isset($settings['customer_export_feed_cache']) ? (int)$settings['customer_export_feed_cache'] : 3600;
		
		// Check cache
		$cache_key = 'customer_export_feed.' . $feed_format . '.' . $feed_limit;
		$output = $this->cache->get($cache_key);
		
		if (!$output) {
			// Generate feed
			$this->load->model('extension/feed/customer_export');
			
			$customers = $this->model_extension_feed_customer_export->getCustomers($feed_limit);
			
			if ($feed_format == 'xml') {
				$output = $this->generateXML($customers);
				header('Content-Type: application/xml; charset=utf-8');
			} else {
				$output = $this->generateJSON($customers);
				header('Content-Type: application/json; charset=utf-8');
			}
			
			// Store in cache
			$this->cache->set($cache_key, $output, $cache_time);
		} else {
			// Set appropriate header based on format
			if ($feed_format == 'xml') {
				header('Content-Type: application/xml; charset=utf-8');
			} else {
				header('Content-Type: application/json; charset=utf-8');
			}
		}
		
		$this->response->setOutput($output);
	}
	
	private function generateXML($customers) {
		$dom = new DOMDocument('1.0', 'UTF-8');
		$dom->formatOutput = true;
		$root = $dom->createElement('customers');
		$dom->appendChild($root);

		foreach ($customers as $customer_data) {
			$customer = $dom->createElement('customer');
			$root->appendChild($customer);
			
			foreach ($customer_data as $key => $value) {
				// Sanitize key name for XML element
				$key = preg_replace('/[^a-zA-Z0-9_-]/', '_', $key);
				
				// Remove null bytes and control characters
				$value = str_replace("\0", '', $value);
				$value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $value);
				
				// Use CDATA for values that might contain special characters
				$node = $dom->createElement($key);
				if (preg_match('/[<>&"]/', $value)) {
					$cdata = $dom->createCDATASection($value);
					$node->appendChild($cdata);
				} else {
					$node->appendChild($dom->createTextNode($value));
				}
				$customer->appendChild($node);
			}
		}

		return $dom->saveXML();
	}
	
	private function generateJSON($customers) {
		// Sanitize data for JSON
		$sanitized_data = array();
		foreach ($customers as $row) {
			$sanitized_row = array();
			foreach ($row as $key => $value) {
				// Remove null bytes and control characters
				$value = str_replace("\0", '', $value);
				$value = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
				$value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $value);
				$sanitized_row[$key] = $value;
			}
			$sanitized_data[] = $sanitized_row;
		}
		
		$output = json_encode(
			array(
				'customers' => $sanitized_data,
				'total' => count($sanitized_data),
				'generated_at' => date('Y-m-d H:i:s')
			),
			JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE
		);
		
		if ($output === false) {
			$output = json_encode(array('error' => 'Failed to encode data: ' . json_last_error_msg()));
		}
		
		return $output;
	}
}
