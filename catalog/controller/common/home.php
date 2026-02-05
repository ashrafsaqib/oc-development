<?php
class ControllerCommonHome extends Controller {
	public function index() {
        $this->load->language('common/home');

        // Load custom theme settings for current language
        $this->load->model('setting/setting');
        
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
		if ($this->config->getLanguage('theme_oc_ultra_featured_categories_status') && $this->config->getLanguage('theme_oc_ultra_featured_category')) {
			$featured_categories = $this->config->getLanguage('theme_oc_ultra_featured_category');

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
		if ($this->config->getLanguage('theme_oc_ultra_faq_status')) {
			$data['faq_heading'] = $this->config->getLanguage('theme_oc_ultra_faq_heading');
			$data['faq_description'] = $this->config->getLanguage('theme_oc_ultra_faq_description');
			$data['faq_support_text'] = $this->config->getLanguage('theme_oc_ultra_faq_support_text');
			$data['faq_support_hours'] = $this->config->getLanguage('theme_oc_ultra_faq_support_hours');

			$data['faqs'] = [];
			if ($this->config->getLanguage('theme_oc_ultra_faq')) {
				$data['faqs'] = $this->config->getLanguage('theme_oc_ultra_faq');
			}
		} else {
			$data['faqs'] = [];
		}
		// Add testimonials section data - only if enabled
		$data['testimonials'] = [];
		if ($this->config->getLanguage('theme_oc_ultra_testimonials_status')) {
			$data['testimonials_heading'] = $this->config->getLanguage('theme_oc_ultra_testimonials_heading') ? $this->config->getLanguage('theme_oc_ultra_testimonials_heading') : 'What our customers are saying';

			if ($this->config->getLanguage('theme_oc_ultra_testimonial')) {
				$testimonials = $this->config->getLanguage('theme_oc_ultra_testimonial');

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
		if ($this->config->getLanguage('theme_oc_ultra_features_status')) {
			if ($this->config->getLanguage('theme_oc_ultra_feature')) {
				$features = $this->config->getLanguage('theme_oc_ultra_feature');

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
