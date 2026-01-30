<?php
class ControllerCommonHome extends Controller {
	public function index() {
        $this->load->language('common/home');

        // Load custom theme settings for current language
        $this->load->model('setting/setting');
        $current_language_id = (int)$this->config->get('config_language_id');
        $current_store_id = (int)$this->config->get('config_store_id');
        $theme_settings = $this->model_setting_setting->getSetting('theme_oc_ultra_' . $current_language_id, $current_store_id);
        
        foreach ($theme_settings as $key => $value) {
            $this->config->set($key, $value);
        }
        
        $data['text_shop_by_category'] = $this->language->get('text_shop_by_category');
        $data['text_shop_now'] = $this->language->get('text_shop_now');

		$this->document->setTitle($this->config->get('config_meta_title'));
		$this->document->setDescription($this->config->get('config_meta_description'));
		$this->document->setKeywords($this->config->get('config_meta_keyword'));

		if (isset($this->request->get['route'])) {
			$this->document->addLink($this->config->get('config_url'), 'canonical');
		}

		// Add featured categories to home page
		$this->load->model('catalog/category');
		$this->load->model('tool/image');

		$data['featured_categories'] = [];

		// Check if featured categories section is enabled
		if ($this->config->get('theme_oc_ultra_featured_categories_status') && $this->config->get('theme_oc_ultra_featured_category')) {
			$featured_categories = $this->config->get('theme_oc_ultra_featured_category');

			foreach ($featured_categories as $featured_category) {
				$category_info = $this->model_catalog_category->getCategory($featured_category['category_id']);

				if ($category_info && $featured_category['image'] && is_file(DIR_IMAGE . html_entity_decode($featured_category['image'], ENT_QUOTES, 'UTF-8'))) {
					$image = 'image/'.html_entity_decode($featured_category['image'], ENT_QUOTES, 'UTF-8');

					$data['featured_categories'][] = [
						'category_id' => $category_info['category_id'],
						'name'        => $category_info['name'],
						'image'       => $image,
						'width'       => isset($featured_category['width']) ? $featured_category['width'] : 4,
						'href'        => $this->url->link('product/category', 'path=' . $category_info['category_id'])
					];
				}
			}
		}

		// Add FAQ section data - only if enabled
		if ($this->config->get('theme_oc_ultra_faq_status')) {
			$data['faq_heading'] = $this->config->get('theme_oc_ultra_faq_heading') ? $this->config->get('theme_oc_ultra_faq_heading') : 'Have a question? We are here to help.';
			$data['faq_description'] = $this->config->get('theme_oc_ultra_faq_description') ? $this->config->get('theme_oc_ultra_faq_description') : 'Check out the most common questions our customers asked. Still have questions? Contact our customer support.';
			$data['faq_support_text'] = $this->config->get('theme_oc_ultra_faq_support_text') ? $this->config->get('theme_oc_ultra_faq_support_text') : 'Our customer support is available monday to friday: 8am-8:30pm.';
			$data['faq_support_hours'] = $this->config->get('theme_oc_ultra_faq_support_hours') ? $this->config->get('theme_oc_ultra_faq_support_hours') : 'Average answer time: 24h';

			$data['faqs'] = [];
			if ($this->config->get('theme_oc_ultra_faq')) {
				$data['faqs'] = $this->config->get('theme_oc_ultra_faq');
			}
		} else {
			$data['faqs'] = [];
		}

		// Add testimonials section data - only if enabled
		$data['testimonials'] = [];
		if ($this->config->get('theme_oc_ultra_testimonials_status')) {
			$data['testimonials_heading'] = $this->config->get('theme_oc_ultra_testimonials_heading') ? $this->config->get('theme_oc_ultra_testimonials_heading') : 'What our customers are saying';

			if ($this->config->get('theme_oc_ultra_testimonial')) {
				$testimonials = $this->config->get('theme_oc_ultra_testimonial');

				foreach ($testimonials as $testimonial) {
					if (isset($testimonial['image']) && $testimonial['image'] && is_file(DIR_IMAGE . html_entity_decode($testimonial['image'], ENT_QUOTES, 'UTF-8'))) {
						$image = $this->model_tool_image->resize(html_entity_decode($testimonial['image'], ENT_QUOTES, 'UTF-8'), 100, 100);
					} else {
						$image = $this->model_tool_image->resize('placeholder.png', 100, 100);
					}

					$data['testimonials'][] = [
						'name'     => isset($testimonial['name']) ? $testimonial['name'] : '',
						'location' => isset($testimonial['location']) ? $testimonial['location'] : '',
						'image'    => $image,
						'rating'   => isset($testimonial['rating']) ? (int)$testimonial['rating'] : 5,
						'text'     => isset($testimonial['text']) ? $testimonial['text'] : ''
					];
				}
			}
		}

		// Add features section data - only if enabled
		$data['features'] = [];
		if ($this->config->get('theme_oc_ultra_features_status')) {
			if ($this->config->get('theme_oc_ultra_feature')) {
				$features = $this->config->get('theme_oc_ultra_feature');

				foreach ($features as $feature) {
					$data['features'][] = [
						'icon'        => isset($feature['icon']) ? $feature['icon'] : 'bi bi-star',
						'title'       => isset($feature['title']) ? $feature['title'] : '',
						'description' => isset($feature['description']) ? $feature['description'] : ''
					];
				}
			}
		}

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('common/home', $data));
	}
}
