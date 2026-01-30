<?php
class ControllerExtensionThemeOcUltra extends Controller {
    private $error = array();

    public function index() {
        $this->load->language('extension/theme/oc_ultra');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');
        $this->load->model('localisation/language');
        $data['languages'] = $this->model_localisation_language->getLanguages();

        if (isset($this->request->get['store_id'])) {
            $store_id = (int)$this->request->get['store_id'];
        } else {
            $store_id = 0;
        }

        if (isset($this->request->get['language_id'])) {
            $language_id = (int)$this->request->get['language_id'];
        } else {
             $language_id = (int)$this->config->get('config_language_id');
        }
        $data['active_language_id'] = $language_id;

        foreach ($data['languages'] as &$language) {
            $language['url'] = $this->url->link('extension/theme/oc_ultra', 'user_token=' . $this->session->data['user_token'] . '&store_id=' . $store_id . '&language_id=' . $language['language_id'], true);
        }

        // Handle Form Save
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('theme_oc_ultra_' . $language_id, $this->request->post, $store_id);
            $this->session->data['success'] = $this->language->get('text_success');
            $this->response->redirect($this->url->link('extension/theme/oc_ultra', 'user_token=' . $this->session->data['user_token'] . '&store_id=' . $store_id . '&language_id=' . $language_id, true));
        }

        $data['user_token'] = $this->session->data['user_token'];

        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=theme', true)
        );
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/theme/oc_ultra', 'user_token=' . $this->session->data['user_token'] . '&store_id=' . $store_id . '&language_id=' . $language_id, true)
        );

        $data['action'] = $this->url->link('extension/theme/oc_ultra', 'user_token=' . $this->session->data['user_token'] . '&store_id=' . $store_id . '&language_id=' . $language_id, true);
        $data['back'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=theme', true);
        $data['newsletter_entries'] = $this->url->link('extension/theme/oc_ultra/newsletter', 'user_token=' . $this->session->data['user_token']);

        // Load Settings
        $config_keys = array(
            'theme_oc_ultra_status',
            'theme_oc_ultra_header_text',
            'theme_oc_ultra_custom_css',
            'theme_oc_ultra_custom_js',
            'theme_oc_ultra_header_color_preset',
            'theme_oc_ultra_header_color_custom',
            'theme_oc_ultra_featured_categories_status',
            'theme_oc_ultra_faq_status',
            'theme_oc_ultra_testimonials_status',
            'theme_oc_ultra_features_status',
            'theme_oc_ultra_faq_heading',
            'theme_oc_ultra_faq_description',
            'theme_oc_ultra_faq_support_text',
            'theme_oc_ultra_faq_support_hours',
            'theme_oc_ultra_testimonials_heading',
            'theme_oc_ultra_footer_text',
            'theme_oc_ultra_newsletter_popup_status',
            'theme_oc_ultra_newsletter_popup_title',
            'theme_oc_ultra_newsletter_popup_subtitle',
            'theme_oc_ultra_newsletter_popup_delay',
            'theme_oc_ultra_newsletter_coupon_id',
            'theme_oc_ultra_newsletter_email_subject',
            'theme_oc_ultra_newsletter_email_message',
            'theme_oc_ultra_newsletter_popup_image',
            'theme_oc_ultra_image_category_width',
            'theme_oc_ultra_image_category_height',
            'theme_oc_ultra_image_thumb_width',
            'theme_oc_ultra_image_thumb_height',
            'theme_oc_ultra_image_popup_width',
            'theme_oc_ultra_image_popup_height',
            'theme_oc_ultra_image_product_width',
            'theme_oc_ultra_image_product_height',
            'theme_oc_ultra_image_additional_width',
            'theme_oc_ultra_image_additional_height',
            'theme_oc_ultra_image_related_width',
            'theme_oc_ultra_image_related_height',
            'theme_oc_ultra_image_compare_width',
            'theme_oc_ultra_image_compare_height',
            'theme_oc_ultra_image_wishlist_width',
            'theme_oc_ultra_image_wishlist_height',
            'theme_oc_ultra_image_cart_width',
            'theme_oc_ultra_image_cart_height',
            'theme_oc_ultra_image_location_width',
            'theme_oc_ultra_image_location_height',
            'theme_oc_ultra_product_limit',
            'theme_oc_ultra_product_description_length'
        );

        // Default Image Values
        $defaults = array(
            'theme_oc_ultra_image_category_width' => 80,
            'theme_oc_ultra_image_category_height' => 80,
            'theme_oc_ultra_image_thumb_width' => 228,
            'theme_oc_ultra_image_thumb_height' => 228,
            'theme_oc_ultra_image_popup_width' => 500,
            'theme_oc_ultra_image_popup_height' => 500,
            'theme_oc_ultra_image_product_width' => 228,
            'theme_oc_ultra_image_product_height' => 228,
            'theme_oc_ultra_image_additional_width' => 74,
            'theme_oc_ultra_image_additional_height' => 74,
            'theme_oc_ultra_image_related_width' => 80,
            'theme_oc_ultra_image_related_height' => 80,
            'theme_oc_ultra_image_compare_width' => 90,
            'theme_oc_ultra_image_compare_height' => 90,
            'theme_oc_ultra_image_wishlist_width' => 47,
            'theme_oc_ultra_image_wishlist_height' => 47,
            'theme_oc_ultra_image_cart_width' => 47,
            'theme_oc_ultra_image_cart_height' => 47,
            'theme_oc_ultra_image_location_width' => 268,
            'theme_oc_ultra_image_location_height' => 50,
            'theme_oc_ultra_product_limit' => 15,
            'theme_oc_ultra_product_description_length' => 100
        );

        $module_info = $this->model_setting_setting->getSetting('theme_oc_ultra_' . $language_id, $store_id);

        foreach ($config_keys as $key) {
            if (isset($this->request->post[$key])) {
                $data[$key] = $this->request->post[$key];
            } elseif (isset($module_info[$key])) {
                $data[$key] = $module_info[$key];
            } elseif ($this->config->has($key)) {
                $data[$key] = $this->config->get($key);
            } elseif (isset($defaults[$key])) {
                $data[$key] = $defaults[$key];
            } else {
                $data[$key] = $this->config->get($key);
            }
        }

        // Image Processing
        $this->load->model('tool/image');
        $this->load->model('catalog/category');

        // Featured Categories Logic
        $data['theme_oc_ultra_featured_categories'] = array();
        $featured_categories = $this->config->get('theme_oc_ultra_featured_category');
        if ($featured_categories) {
            foreach ($featured_categories as $fc) {
                $category_info = $this->model_catalog_category->getCategory($fc['category_id']);
                if ($category_info) {
                    $image = isset($fc['image']) ? $fc['image'] : '';
                    $thumb = ($image && is_file(DIR_IMAGE . $image)) ? $this->model_tool_image->resize($image, 200, 200) : $this->model_tool_image->resize('no_image.png', 200, 200);
                    
                    $data['theme_oc_ultra_featured_categories'][] = array(
                        'category_id' => $fc['category_id'],
                        'name'        => $category_info['name'],
                        'thumb'       => $thumb,
                        'image'       => $image,
                        'width'       => isset($fc['width']) ? $fc['width'] : 4
                    );
                }
            }
        }

        // Newsletter Image
        if ($data['theme_oc_ultra_newsletter_popup_image'] && is_file(DIR_IMAGE . $data['theme_oc_ultra_newsletter_popup_image'])) {
            $data['newsletter_thumb'] = $this->model_tool_image->resize($data['theme_oc_ultra_newsletter_popup_image'], 300, 300);
        } else {
            $data['newsletter_thumb'] = $this->model_tool_image->resize('no_image.png', 300, 300);
        }

        $data['placeholder'] = $this->model_tool_image->resize('no_image.png', 200, 200);

        // Coupons
        $this->load->model('marketing/coupon');
        $coupons = $this->model_marketing_coupon->getCoupons(array('sort' => 'name', 'order' => 'ASC'));
        $data['coupons'] = array();
        foreach ($coupons as $coupon) {
            $data['coupons'][] = array(
                'coupon_id' => $coupon['coupon_id'],
                'name'      => $coupon['name'],
                'code'      => $coupon['code']
            );
        }

        // Repeater Fields (FAQs, Testimonials, etc)
        $data['theme_oc_ultra_faqs'] = $this->config->get('theme_oc_ultra_faq') ? $this->config->get('theme_oc_ultra_faq') : array();
        
        $data['theme_oc_ultra_testimonials'] = array();
        if ($this->config->get('theme_oc_ultra_testimonial')) {
            $testimonials = $this->config->get('theme_oc_ultra_testimonial');
            foreach ($testimonials as $testimonial) {
                $image = isset($testimonial['image']) ? $testimonial['image'] : '';
                $thumb = ($image && is_file(DIR_IMAGE . $image)) ? $this->model_tool_image->resize($image, 100, 100) : $this->model_tool_image->resize('no_image.png', 100, 100);
                
                $data['theme_oc_ultra_testimonials'][] = array(
                    'name'     => isset($testimonial['name']) ? $testimonial['name'] : '',
                    'location' => isset($testimonial['location']) ? $testimonial['location'] : '',
                    'image'    => $image,
                    'thumb'    => $thumb,
                    'rating'   => isset($testimonial['rating']) ? $testimonial['rating'] : 5,
                    'text'     => isset($testimonial['text']) ? $testimonial['text'] : ''
                );
            }
        }
        
        $data['theme_oc_ultra_features'] = $this->config->get('theme_oc_ultra_feature') ? $this->config->get('theme_oc_ultra_feature') : array();
        $data['theme_oc_ultra_social_links'] = $this->config->get('theme_oc_ultra_social_link') ? $this->config->get('theme_oc_ultra_social_link') : array();
        $data['theme_oc_ultra_payment_methods'] = $this->config->get('theme_oc_ultra_payment_method') ? $this->config->get('theme_oc_ultra_payment_method') : array();

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/theme/oc_ultra', $data));
    }

    public function newsletter() {
        $this->load->language('extension/theme/oc_ultra');

        $this->document->setTitle($this->language->get('heading_title'));

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        $url = '';

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/theme/oc_ultra', 'user_token=' . $this->session->data['user_token'], true)
        );
        $data['breadcrumbs'][] = array(
            'text' => 'Newsletter',
            'href' => $this->url->link('extension/theme/oc_ultra/newsletter', 'user_token=' . $this->session->data['user_token'] . $url, true)
        );
        
        $data['export'] = $this->url->link('extension/theme/oc_ultra/export', 'user_token=' . $this->session->data['user_token'], true);
        $data['cancel'] = $this->url->link('extension/theme/oc_ultra', 'user_token=' . $this->session->data['user_token'], true);

        $data['newsletters'] = array();

        $filter_data = array(
            'start' => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit' => $this->config->get('config_limit_admin')
        );

        $this->load->model('extension/theme/oc_ultra');

        $newsletter_total = $this->model_extension_theme_oc_ultra->getTotalNewsletters();

        $results = $this->model_extension_theme_oc_ultra->getNewsletters($filter_data);

        foreach ($results as $result) {
            $data['newsletters'][] = array(
                'newsletter_id' => $result['newsletter_id'],
                'email'        => $result['email'],
                'ip'           => $result['ip'],
                'date_added'   => date($this->language->get('date_format_short'), strtotime($result['date_added']))
            );
        }

        $pagination = new Pagination();
        $pagination->total = $newsletter_total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url = $this->url->link('extension/theme/oc_ultra/newsletter', 'user_token=' . $this->session->data['user_token'] . '&page={page}', true);

        $data['pagination'] = $pagination->render();
        $data['results'] = sprintf($this->language->get('text_pagination'), ($newsletter_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($newsletter_total - $this->config->get('config_limit_admin'))) ? $newsletter_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $newsletter_total, ceil($newsletter_total / $this->config->get('config_limit_admin')));

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/theme/oc_ultra_newsletter', $data));
    }

    public function export() {
        $this->load->model('extension/theme/oc_ultra');

        $results = $this->model_extension_theme_oc_ultra->getNewsletters();

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="newsletter_subscribers.csv"');

        $output = fopen('php://output', 'w');

        fputcsv($output, array('ID', 'Email', 'IP', 'Date Added'));

        foreach ($results as $result) {
            fputcsv($output, array($result['newsletter_id'], $result['email'], $result['ip'], $result['date_added']));
        }

        fclose($output);
    }

    protected function validate() {
        if (!$this->user->hasPermission('modify', 'extension/theme/oc_ultra')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->request->post['theme_oc_ultra_product_limit']) {
            $this->error['product_limit'] = $this->language->get('error_limit');
        }

        if (!$this->request->post['theme_oc_ultra_product_description_length']) {
            $this->error['product_description_length'] = $this->language->get('error_limit');
        }

        if (!$this->request->post['theme_oc_ultra_image_category_width'] || !$this->request->post['theme_oc_ultra_image_category_height']) {
            $this->error['image_category'] = $this->language->get('error_image_category');
        }

        if (!$this->request->post['theme_oc_ultra_image_thumb_width'] || !$this->request->post['theme_oc_ultra_image_thumb_height']) {
            $this->error['image_thumb'] = $this->language->get('error_image_thumb');
        }

        if (!$this->request->post['theme_oc_ultra_image_popup_width'] || !$this->request->post['theme_oc_ultra_image_popup_height']) {
            $this->error['image_popup'] = $this->language->get('error_image_popup');
        }

        if (!$this->request->post['theme_oc_ultra_image_product_width'] || !$this->request->post['theme_oc_ultra_image_product_height']) {
            $this->error['image_product'] = $this->language->get('error_image_product');
        }

        if (!$this->request->post['theme_oc_ultra_image_additional_width'] || !$this->request->post['theme_oc_ultra_image_additional_height']) {
            $this->error['image_additional'] = $this->language->get('error_image_additional');
        }

        if (!$this->request->post['theme_oc_ultra_image_related_width'] || !$this->request->post['theme_oc_ultra_image_related_height']) {
            $this->error['image_related'] = $this->language->get('error_image_related');
        }

        if (!$this->request->post['theme_oc_ultra_image_compare_width'] || !$this->request->post['theme_oc_ultra_image_compare_height']) {
            $this->error['image_compare'] = $this->language->get('error_image_compare');
        }

        if (!$this->request->post['theme_oc_ultra_image_wishlist_width'] || !$this->request->post['theme_oc_ultra_image_wishlist_height']) {
            $this->error['image_wishlist'] = $this->language->get('error_image_wishlist');
        }

        if (!$this->request->post['theme_oc_ultra_image_cart_width'] || !$this->request->post['theme_oc_ultra_image_cart_height']) {
            $this->error['image_cart'] = $this->language->get('error_image_cart');
        }

        if (!$this->request->post['theme_oc_ultra_image_location_width'] || !$this->request->post['theme_oc_ultra_image_location_height']) {
            $this->error['image_location'] = $this->language->get('error_image_location');
        }

        return !$this->error;
    }

    public function install() {
        // Create newsletter table for OC3
        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "oc_ultra_newsletter` (
            `newsletter_id` int(11) NOT NULL AUTO_INCREMENT,
            `email` varchar(96) NOT NULL,
            `ip` varchar(40) NOT NULL,
            `status` tinyint(1) NOT NULL DEFAULT '1',
            `date_added` datetime NOT NULL,
            PRIMARY KEY (`newsletter_id`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8;");
        
        // Default settings with all options
        $this->load->model('setting/setting');
        $defaults = array(
            'theme_oc_ultra_status' => 1,
            'theme_oc_ultra_header_text' => 'Free shipping on orders over $100 | Use code WELCOME10 for 10% off',
            'theme_oc_ultra_custom_css' => '',
            'theme_oc_ultra_custom_js' => '',
            'theme_oc_ultra_header_color_preset' => 'jade',
            'theme_oc_ultra_header_color_custom' => '#86a88d',
            'theme_oc_ultra_featured_categories_status' => 1,
            'theme_oc_ultra_faq_status' => 1,
            'theme_oc_ultra_testimonials_status' => 1,
            'theme_oc_ultra_features_status' => 1,
            'theme_oc_ultra_featured_category' => array(),
            'theme_oc_ultra_faq_heading' => 'Have a question? We are here to help.',
            'theme_oc_ultra_faq_description' => 'Check out the most common questions our customers asked. Still have questions? Contact our customer support.',
            'theme_oc_ultra_faq_support_text' => 'Our customer support is available monday to friday: 8am-8:30pm.',
            'theme_oc_ultra_faq_support_hours' => 'Average answer time: 24h',
            'theme_oc_ultra_faq' => array(
                array(
                    'question' => 'What is your return policy?',
                    'answer' => 'We offer a 30-day return policy on all products. Items must be in original condition with all tags attached.'
                ),
                array(
                    'question' => 'How long does shipping take?',
                    'answer' => 'Standard shipping takes 5-7 business days. Express shipping options are available at checkout for faster delivery.'
                ),
                array(
                    'question' => 'Do you ship internationally?',
                    'answer' => 'Yes, we ship to most countries worldwide. International shipping costs and delivery times vary by location.'
                ),
                array(
                    'question' => 'How can I track my order?',
                    'answer' => 'Once your order ships, you will receive a tracking number via email. You can use this to track your package on our website or the carrier\'s site.'
                )
            ),
            'theme_oc_ultra_testimonials_heading' => 'What our customers are saying',
            'theme_oc_ultra_testimonial' => array(
                array(
                    'name' => 'Sarah Johnson',
                    'location' => 'New York, USA',
                    'image' => '',
                    'rating' => 5,
                    'text' => 'The sound quality is absolutely amazing! I can hear every detail in the music. Worth every penny.'
                ),
                array(
                    'name' => 'Michael Chen',
                    'location' => 'London, UK',
                    'image' => '',
                    'rating' => 5,
                    'text' => 'Excellent noise cancellation and very comfortable to wear for long periods. Highly recommended for commuters.'
                ),
                array(
                    'name' => 'Emma Davis',
                    'location' => 'Sydney, Australia',
                    'image' => '',
                    'rating' => 4,
                    'text' => 'Great design and build quality. The battery life is also impressive. A bit pricey, but you get what you pay for.'
                )
            ),
            'theme_oc_ultra_feature' => array(
                array(
                    'icon' => 'fa fa-map-marker',
                    'title' => 'Designed in NYC',
                    'description' => 'Products designed and developed in New York City.'
                ),
                array(
                    'icon' => 'fa fa-truck',
                    'title' => 'Free Shipping',
                    'description' => 'Free worldwide shipping on all orders over $99'
                ),
                array(
                    'icon' => 'fa fa-headphones',
                    'title' => 'Support',
                    'description' => 'Our support team is available 24/7'
                ),
                array(
                    'icon' => 'fa fa-shield',
                    'title' => 'Secure Payment',
                    'description' => 'All payments are processed securely'
                )
            ),
            'theme_oc_ultra_footer_text' => 'Sign up for news, updates & 10% off your first order.',
            'theme_oc_ultra_newsletter_popup_status' => 1,
            'theme_oc_ultra_newsletter_popup_title' => 'Get 10% Off Your First Order',
            'theme_oc_ultra_newsletter_popup_subtitle' => 'Sign up for exclusive deals, new arrivals & more!',
            'theme_oc_ultra_newsletter_popup_image' => '',
            'theme_oc_ultra_newsletter_popup_delay' => 2000,
            'theme_oc_ultra_newsletter_coupon_id' => 0,
            'theme_oc_ultra_newsletter_email_subject' => 'Welcome to {store_name} - Your Discount Code Inside!',
            'theme_oc_ultra_newsletter_email_message' => 'Thank you for subscribing! Use coupon code {coupon_code} to get your discount.',
            'theme_oc_ultra_social_link' => array(
                array(
                    'name' => 'Facebook',
                    'icon' => 'fa fa-facebook',
                    'url' => 'https://facebook.com'
                ),
                array(
                    'name' => 'Twitter',
                    'icon' => 'fa fa-twitter',
                    'url' => 'https://twitter.com'
                ),
                array(
                    'name' => 'Instagram',
                    'icon' => 'fa fa-instagram',
                    'url' => 'https://instagram.com'
                ),
                array(
                    'name' => 'YouTube',
                    'icon' => 'fa fa-youtube',
                    'url' => 'https://youtube.com'
                )
            ),
            'theme_oc_ultra_payment_method' => array(
                array(
                    'name' => 'Visa',
                    'icon' => 'fa fa-cc-visa'
                ),
                array(
                    'name' => 'Mastercard',
                    'icon' => 'fa fa-cc-mastercard'
                ),
                array(
                    'name' => 'PayPal',
                    'icon' => 'fa fa-cc-paypal'
                ),
                array(
                    'name' => 'American Express',
                    'icon' => 'fa fa-cc-amex'
                )
            ),
            'theme_oc_ultra_image_category_width' => 80,
            'theme_oc_ultra_image_category_height' => 80,
            'theme_oc_ultra_image_thumb_width' => 228,
            'theme_oc_ultra_image_thumb_height' => 228,
            'theme_oc_ultra_image_popup_width' => 500,
            'theme_oc_ultra_image_popup_height' => 500,
            'theme_oc_ultra_image_product_width' => 228,
            'theme_oc_ultra_image_product_height' => 228,
            'theme_oc_ultra_image_additional_width' => 74,
            'theme_oc_ultra_image_additional_height' => 74,
            'theme_oc_ultra_image_related_width' => 80,
            'theme_oc_ultra_image_related_height' => 80,
            'theme_oc_ultra_image_compare_width' => 90,
            'theme_oc_ultra_image_compare_height' => 90,
            'theme_oc_ultra_image_wishlist_width' => 47,
            'theme_oc_ultra_image_wishlist_height' => 47,
            'theme_oc_ultra_image_cart_width' => 47,
            'theme_oc_ultra_image_cart_height' => 47,
            'theme_oc_ultra_image_location_width' => 268,
            'theme_oc_ultra_image_location_height' => 50,
            'theme_oc_ultra_product_limit' => 15,
            'theme_oc_ultra_product_description_length' => 100
        );
        
        $this->model_setting_setting->editSetting('theme_oc_ultra', $defaults, 0);
    }

    public function uninstall() {
        $this->load->model('setting/setting');
        $this->model_setting_setting->deleteSetting('theme_oc_ultra');
    }
}