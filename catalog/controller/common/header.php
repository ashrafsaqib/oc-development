<?php
class ControllerCommonHeader extends Controller {
	public function index() {
		// Analytics
		$this->load->model('setting/extension');

		$data['analytics'] = array();

		$analytics = $this->model_setting_extension->getExtensions('analytics');

		foreach ($analytics as $analytic) {
			if ($this->config->get('analytics_' . $analytic['code'] . '_status')) {
				$data['analytics'][] = $this->load->controller('extension/analytics/' . $analytic['code'], $this->config->get('analytics_' . $analytic['code'] . '_status'));
			}
		}

		if ($this->request->server['HTTPS']) {
			$server = $this->config->get('config_ssl');
		} else {
			$server = $this->config->get('config_url');
		}

		if (is_file(DIR_IMAGE . $this->config->get('config_icon'))) {
			$this->document->addLink($server . 'image/' . $this->config->get('config_icon'), 'icon');
		}

		$data['title'] = $this->document->getTitle();

		$data['base'] = $server;
		$data['description'] = $this->document->getDescription();
		$data['keywords'] = $this->document->getKeywords();
		$data['links'] = $this->document->getLinks();
		$data['styles'] = $this->document->getStyles();
		$data['scripts'] = $this->document->getScripts('header');
		$data['lang'] = $this->language->get('code');
		$data['direction'] = $this->language->get('direction');

		$data['name'] = $this->config->get('config_name');

		if (is_file(DIR_IMAGE . $this->config->get('config_logo'))) {
			$data['logo'] = $server . 'image/' . $this->config->get('config_logo');
		} else {
			$data['logo'] = '';
		}

		$this->load->language('common/header');

		// Wishlist
		if ($this->customer->isLogged()) {
			$this->load->model('account/wishlist');

			$data['text_wishlist'] = sprintf($this->language->get('text_wishlist'), $this->model_account_wishlist->getTotalWishlist());
		} else {
			$data['text_wishlist'] = sprintf($this->language->get('text_wishlist'), (isset($this->session->data['wishlist']) ? count($this->session->data['wishlist']) : 0));
		}

		$data['text_logged'] = sprintf($this->language->get('text_logged'), $this->url->link('account/account', '', true), $this->customer->getFirstName(), $this->url->link('account/logout', '', true));
		
		// Custom Language Variables
		$data['text_search_header'] = $this->language->get('text_search_header');
		$data['text_toggle_nav'] = $this->language->get('text_toggle_nav');

		$data['home'] = $this->url->link('common/home');
		$data['wishlist'] = $this->url->link('account/wishlist', '', true);
		$data['logged'] = $this->customer->isLogged();
		$data['account'] = $this->url->link('account/account', '', true);
		$data['register'] = $this->url->link('account/register', '', true);
		$data['login'] = $this->url->link('account/login', '', true);
		$data['order'] = $this->url->link('account/order', '', true);
		$data['transaction'] = $this->url->link('account/transaction', '', true);
		$data['download'] = $this->url->link('account/download', '', true);
		$data['logout'] = $this->url->link('account/logout', '', true);
		$data['shopping_cart'] = $this->url->link('checkout/cart');
		$data['checkout'] = $this->url->link('checkout/checkout', '', true);
		$data['contact'] = $this->url->link('information/contact');
		$data['telephone'] = $this->config->get('config_telephone');
		
		$data['language'] = $this->load->controller('common/language');
		$data['currency'] = $this->load->controller('common/currency');
		$data['search'] = $this->load->controller('common/search');
		$data['cart'] = $this->load->controller('common/cart');
		$data['menu'] = $this->load->controller('common/menu');

		// Add theme header text setting to header data
		if ($this->config->getLanguage('theme_oc_ultra_header_text')) {
			$data['theme_header_text'] = html_entity_decode($this->config->getLanguage('theme_oc_ultra_header_text'), ENT_QUOTES, 'UTF-8');
		} else {
			$data['theme_header_text'] = 'Free shipping on orders over $100 | Use code WELCOME10 for 10% off';
		}

		// Add custom CSS
		if ($this->config->getLanguage('theme_oc_ultra_custom_css')) {
			$data['custom_css'] = $this->config->getLanguage('theme_oc_ultra_custom_css');
		}
		
		// Add dynamic header color
		$color_preset = $this->config->getLanguage('theme_oc_ultra_header_color_preset') ? $this->config->getLanguage('theme_oc_ultra_header_color_preset') : 'jade';
		
		$preset_colors = [
			'jade' => '#86a88d',
			'slate' => '#545A62',
			'midnight' => '#2C3E50',
			'sage' => '#606C5D',
			'charcoal' => '#333333'
		];
		
		if ($color_preset === 'custom') {
			$data['header_bg_color'] = $this->config->getLanguage('theme_oc_ultra_header_color_custom') ? $this->config->getLanguage('theme_oc_ultra_header_color_custom') : '#86a88d';
		} else {
			$data['header_bg_color'] = isset($preset_colors[$color_preset]) ? $preset_colors[$color_preset] : '#86a88d';
		}

		return $this->load->view('common/header', $data);
	}
}
