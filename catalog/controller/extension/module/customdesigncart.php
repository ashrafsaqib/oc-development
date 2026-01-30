<?php
class ControllerExtensionModuleCustomDesignCart extends Controller
{
  public function getVariant()
  {

    $product_id = (int)$this->request->get['product_id'];

    // Get all variants for this product
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_custom_config WHERE variant IS NOT NULL and product_id = '" . (int)$product_id . "' order by variant DESC");

    // If options are provided, find matching variant
    if (isset($this->request->post['options']) && is_array($this->request->post['options'])) {
      $selected_options = $this->request->post['options'];

      // Convert product_option_id => product_option_value_id to option_id => option_value_id
      $converted_options = array();
      foreach ($selected_options as $product_option_id => $product_option_value_id) {

        // Handle single values (radio, select)
        $convert_query = $this->db->query("SELECT option_id, option_value_id FROM " . DB_PREFIX . "product_option_value WHERE product_option_value_id = '" . (int)$product_option_value_id . "' and product_id = '" . (int)$product_id . "'");

        if ($convert_query->num_rows) {
          $converted_options[$convert_query->row['option_id']] = $convert_query->row['option_value_id'];
        }
      }

      


      foreach ($query->rows as $variant) {
        if (!empty($variant['variant'])) {
          $variant_config = json_decode($variant['variant'], true);

          
          if ($variant_config && is_array($variant_config)) {
            // Sort variant options for comparison
            $matched = false;
            // Compare the two arrays
            // Normalize variant_config to match converted_options format
            foreach ($variant_config as $option_id => $values) {
              if (is_array($values) && count($values) == 1) {
                if (isset($converted_options[$option_id]) && $converted_options[$option_id] == $values[0]) {
                  $matched = true;
                } else {
                  $matched = false;
                  break;
                }
              } 
            }

            // Compare the two arrays
            if ($matched) {
              $this->response->setOutput(json_encode([
                'success' => true,
                'variant_id' => $variant['config_id']
              ]));
              return;
            }
          }
        }
      }

      // No matching variant found
      $this->response->setOutput(json_encode([
        'success' => false,
        'message' => 'No matching variant found for selected options'
      ]));
      return;
    }

    // Return all variants if no options specified
    $this->response->setOutput(json_encode([
      'success' => true,
      'variants' => $query->rows
    ]));
  }

  public function index()
  {
    // set headers to allow cross origin requests
    $this->response->addHeader('Access-Control-Allow-Origin: ' . $this->config->get('module_customdesigncart_iframe_url'));
    $this->response->addHeader('Access-Control-Allow-Methods: GET');
    $this->response->addHeader('Access-Control-Allow-Headers: Content-Type, Authorization');
    // Set JSON response header
    $this->response->addHeader('Content-Type: application/json');

    // Auto-detect type and id
    if (isset($this->request->get['op_id'])) {
      $type = 'order';
      $id = (int)$this->request->get['op_id'];
    } elseif (isset($this->request->get['cart_id'])) {
      $type = 'cart';
      $id = (int)$this->request->get['cart_id'];
    } elseif (isset($this->request->get['p_id'])) {
      $type = 'product';
      $id = (int)$this->request->get['p_id'];
    } else {
      $this->response->setOutput(json_encode([
        'success' => false,
        'message' => 'Missing or invalid id'
      ]));
      return;
    }

    $table = '';
    $where = '';
    switch ($type) {
      case 'cart':
        $table = DB_PREFIX . 'cart';
        $where = "cart_id = '" . (int)$id . "'";
        break;
      case 'order':
        $table = DB_PREFIX . 'order_custom';
        $where = "order_product_id = '" . (int)$id . "'";
        break;
      case 'product':
      default:
        $table = DB_PREFIX . 'product_custom_config';
        if (isset($this->request->get['v_id'])) {
          $where = "config_id = '" . (int)$this->request->get['v_id'] . "'";
        } else {
          $where = "product_id = '" . (int)$id . "' and variant is NULL ";
        }
        break;
    }

    $query = $this->db->query("SELECT custom_data FROM `" . $table . "` WHERE " . $where);

    if ($query->num_rows) {
      $this->response->setOutput($query->row['custom_data']);
    } else {
      $this->response->setOutput(json_encode([
        'success' => false,
        'message' => 'No custom data found for this id'
      ]));
    }
  }
}
