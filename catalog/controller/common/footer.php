<?php
class ControllerCommonFooter extends Controller {
	public function index() {
		$this->load->language('common/footer');

		$this->load->model('catalog/information');

		$data['informations'] = array();

		foreach ($this->model_catalog_information->getInformations() as $result) {
			if ($result['bottom']) {
				$data['informations'][] = array(
					'title' => $result['title'],
					'href'  => $this->url->link('information/information', 'information_id=' . $result['information_id'])
				);
			}
		}

		$data['contact'] = $this->url->link('information/contact');
		$data['return'] = $this->url->link('account/return/add', '', true);
		$data['sitemap'] = $this->url->link('information/sitemap');
		$data['tracking'] = $this->url->link('information/tracking');
		$data['manufacturer'] = $this->url->link('product/manufacturer');
		$data['voucher'] = $this->url->link('account/voucher', '', true);
		$data['affiliate'] = $this->url->link('affiliate/login', '', true);
		$data['special'] = $this->url->link('product/special');
		$data['account'] = $this->url->link('account/account', '', true);
		$data['order'] = $this->url->link('account/order', '', true);
		$data['wishlist'] = $this->url->link('account/wishlist', '', true);
		$data['newsletter'] = $this->url->link('account/newsletter', '', true);

		// Custom Language Variables
		$data['text_newsletter_pro'] = $this->language->get('text_newsletter_pro');
		$data['text_email_placeholder'] = $this->language->get('text_email_placeholder');

		$data['powered'] = sprintf($this->language->get('text_powered'), $this->config->get('config_name'), date('Y', time()));

		// Whos Online
		if ($this->config->get('config_customer_online')) {
			$this->load->model('tool/online');

			if (isset($this->request->server['REMOTE_ADDR'])) {
				$ip = $this->request->server['REMOTE_ADDR'];
			} else {
				$ip = '';
			}

			if (isset($this->request->server['HTTP_HOST']) && isset($this->request->server['REQUEST_URI'])) {
				$url = ($this->request->server['HTTPS'] ? 'https://' : 'http://') . $this->request->server['HTTP_HOST'] . $this->request->server['REQUEST_URI'];
			} else {
				$url = '';
			}

			if (isset($this->request->server['HTTP_REFERER'])) {
				$referer = $this->request->server['HTTP_REFERER'];
			} else {
				$referer = '';
			}

			$this->model_tool_online->addOnline($ip, $this->customer->getId(), $url, $referer);
		}

		$data['scripts'] = $this->document->getScripts('footer');
		$data['styles'] = $this->document->getStyles('footer');
		
		// Add newsletter subscription URL
		$data['newsletter'] = $this->url->link('extension/oc_ultra/marketing/newsletter.subscribe');
		
		// Add footer newsletter text
		$data['footer_newsletter_text'] = $this->config->getLanguage('theme_oc_ultra_footer_text') ? $this->config->getLanguage('theme_oc_ultra_footer_text') : $this->language->get('text_newsletter_pro');

		// Add newsletter popup settings
		$data['newsletter_popup_enabled'] = $this->config->getLanguage('theme_oc_ultra_newsletter_popup_status');
		$data['newsletter_popup_title'] = $this->config->getLanguage('theme_oc_ultra_newsletter_popup_title') ? $this->config->getLanguage('theme_oc_ultra_newsletter_popup_title') : 'Get 10% Off Your First Order';
		$data['newsletter_popup_subtitle'] = $this->config->getLanguage('theme_oc_ultra_newsletter_popup_subtitle') ? $this->config->getLanguage('theme_oc_ultra_newsletter_popup_subtitle') : 'Sign up for exclusive deals, new arrivals & more!';
		$data['newsletter_popup_delay'] = $this->config->getLanguage('theme_oc_ultra_newsletter_popup_delay') ? $this->config->getLanguage('theme_oc_ultra_newsletter_popup_delay') : 2000;
		
		$this->load->model('tool/image');
		if ($this->config->getLanguage('theme_oc_ultra_newsletter_popup_image') && is_file(DIR_IMAGE . html_entity_decode($this->config->getLanguage('theme_oc_ultra_newsletter_popup_image'), ENT_QUOTES, 'UTF-8'))) {
			$data['newsletter_popup_image'] = $this->model_tool_image->resize(html_entity_decode($this->config->getLanguage('theme_oc_ultra_newsletter_popup_image'), ENT_QUOTES, 'UTF-8'), 300, 300);
		} else {
			$data['newsletter_popup_image'] = '';
		}

		// Add social links
		$data['social_links'] = [];
		if ($this->config->getLanguage('theme_oc_ultra_social_link')) {
			$social_links = $this->config->getLanguage('theme_oc_ultra_social_link');

			foreach ($social_links as $social) {
				$data['social_links'][] = [
					'name' => isset($social['name']) ? $social['name'] : '',
					'icon' => isset($social['icon']) ? $social['icon'] : '',
					'url'  => isset($social['url']) ? $social['url'] : '#'
				];
			}
		}

		// Add payment methods
		$data['payment_methods'] = [];
		if ($this->config->getLanguage('theme_oc_ultra_payment_method')) {
			$payment_methods = $this->config->getLanguage('theme_oc_ultra_payment_method');

			foreach ($payment_methods as $payment) {
				$data['payment_methods'][] = [
					'name' => isset($payment['name']) ? $payment['name'] : '',
					'icon' => isset($payment['icon']) ? $payment['icon'] : ''
				];
			}
		}

		// Add custom JavaScript
		if ($this->config->getLanguage('theme_oc_ultra_custom_js')) {
			$data['custom_js'] = $this->config->getLanguage('theme_oc_ultra_custom_js');
		}

		return $this->load->view('common/footer', $data);
	}
}
