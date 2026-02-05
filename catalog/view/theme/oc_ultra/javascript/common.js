function getURLVar(key) {
	var value = [];

	var query = String(document.location).split('?');

	if (query[1]) {
		var part = query[1].split('&');

		for (i = 0; i < part.length; i++) {
			var data = part[i].split('=');

			if (data[0] && data[1]) {
				value[data[0]] = data[1];
			}
		}

		if (value[key]) {
			return value[key];
		} else {
			return '';
		}
	}
}

$(document).ready(function() {
	// Highlight any found errors
	$('.text-danger').each(function() {
		var element = $(this).parent().parent();

		if (element.hasClass('form-group')) {
			element.addClass('has-error');
		}
	});

	// Currency
	$('#form-currency .currency-select').on('click', function(e) {
		e.preventDefault();

		$('#form-currency input[name=\'code\']').val($(this).attr('name'));

		$('#form-currency').submit();
	});

	// Language
	$('#form-language .language-select').on('click', function(e) {
		e.preventDefault();

		$('#form-language input[name=\'code\']').val($(this).attr('name'));

		$('#form-language').submit();
	});

	/* Search */
	$('#search input[name=\'search\']').parent().find('button').on('click', function() {
		var url = $('base').attr('href') + 'index.php?route=product/search';

		var value = $('#search input[name=\'search\']').val();

		if (value) {
			url += '&search=' + encodeURIComponent(value);
		}

		location = url;
	});

	$('#search input[name=\'search\']').on('keydown', function(e) {
		if (e.keyCode == 13) {
			$('#search input[name=\'search\']').parent().find('button').trigger('click');
		}
	});

	// Menu
	$('#menu .dropdown-menu').each(function() {
		var menu = $('#menu').offset();
		var dropdown = $(this).parent().offset();

		var i = (dropdown.left + $(this).outerWidth()) - (menu.left + $('#menu').outerWidth());

		if (i > 0) {
			$(this).css('margin-left', '-' + (i + 10) + 'px');
		}
	});

	// Product List
	$('#list-view').click(function() {
		$('#content .product-grid > .clearfix').remove();

		$('#content .row > .product-grid').attr('class', 'product-layout product-list col-xs-12');
		$('#grid-view').removeClass('active');
		$('#list-view').addClass('active');

		localStorage.setItem('display', 'list');
	});

	// Product Grid
	$('#grid-view').click(function() {
		// What a shame bootstrap does not take into account dynamically loaded columns
		var cols = $('#column-right, #column-left').length;

		if (cols == 2) {
			$('#content .product-list').attr('class', 'product-layout product-grid col-lg-6 col-md-6 col-sm-12 col-xs-12');
		} else if (cols == 1) {
			$('#content .product-list').attr('class', 'product-layout product-grid col-lg-4 col-md-4 col-sm-6 col-xs-12');
		} else {
			$('#content .product-list').attr('class', 'product-layout product-grid col-lg-3 col-md-3 col-sm-6 col-xs-12');
		}

		$('#list-view').removeClass('active');
		$('#grid-view').addClass('active');

		localStorage.setItem('display', 'grid');
	});

	if (localStorage.getItem('display') == 'list') {
		$('#list-view').trigger('click');
		$('#list-view').addClass('active');
	} else {
		$('#grid-view').trigger('click');
		$('#grid-view').addClass('active');
	}

	// Checkout
	$(document).on('keydown', '#collapse-checkout-option input[name=\'email\'], #collapse-checkout-option input[name=\'password\']', function(e) {
		if (e.keyCode == 13) {
			$('#collapse-checkout-option #button-login').trigger('click');
		}
	});

	// tooltips on hover
	$('[data-toggle=\'tooltip\']').tooltip({container: 'body'});

	// Makes tooltips work on ajax generated content
	$(document).ajaxStop(function() {
		$('[data-toggle=\'tooltip\']').tooltip({container: 'body'});
	});
});

// Cart add remove functions
var cart = {
	'add': function(product_id, quantity) {
		$.ajax({
			url: 'index.php?route=checkout/cart/add',
			type: 'post',
			data: 'product_id=' + product_id + '&quantity=' + (typeof(quantity) != 'undefined' ? quantity : 1),
			dataType: 'json',
			beforeSend: function() {
				$('#cart > button').button('loading');
			},
			complete: function() {
				$('#cart > button').button('reset');
			},
			success: function(json) {
				$('.alert-dismissible, .text-danger').remove();

				if (json['redirect']) {
					location = json['redirect'];
				}

				if (json['success']) {
					$('#content').parent().before('<div class="alert alert-success alert-dismissible"><i class="fa fa-check-circle"></i> ' + json['success'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');

					// Need to set timeout otherwise it wont update the total
					setTimeout(function () {
						$('#cart > button').html('<span id="cart-total"><i class="fa fa-shopping-cart"></i> ' + json['total'] + '</span>');
					}, 100);

					$('html, body').animate({ scrollTop: 0 }, 'slow');

					$('#cart > ul').load('index.php?route=common/cart/info ul li');
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	},
	'update': function(key, quantity) {
		$.ajax({
			url: 'index.php?route=checkout/cart/edit',
			type: 'post',
			data: 'key=' + key + '&quantity=' + (typeof(quantity) != 'undefined' ? quantity : 1),
			dataType: 'json',
			beforeSend: function() {
				$('#cart > button').button('loading');
			},
			complete: function() {
				$('#cart > button').button('reset');
			},
			success: function(json) {
				// Need to set timeout otherwise it wont update the total
				setTimeout(function () {
					$('#cart > button').html('<span id="cart-total"><i class="fa fa-shopping-cart"></i> ' + json['total'] + '</span>');
				}, 100);

				if (getURLVar('route') == 'checkout/cart' || getURLVar('route') == 'checkout/checkout') {
					location = 'index.php?route=checkout/cart';
				} else {
					$('#cart > ul').load('index.php?route=common/cart/info ul li');
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	},
	'remove': function(key) {
		$.ajax({
			url: 'index.php?route=checkout/cart/remove',
			type: 'post',
			data: 'key=' + key,
			dataType: 'json',
			beforeSend: function() {
				$('#cart > button').button('loading');
			},
			complete: function() {
				$('#cart > button').button('reset');
			},
			success: function(json) {
				// Need to set timeout otherwise it wont update the total
				setTimeout(function () {
					$('#cart > button').html('<span id="cart-total"><i class="fa fa-shopping-cart"></i> ' + json['total'] + '</span>');
				}, 100);

				if (getURLVar('route') == 'checkout/cart' || getURLVar('route') == 'checkout/checkout') {
					location = 'index.php?route=checkout/cart';
				} else {
					$('#cart > ul').load('index.php?route=common/cart/info ul li');
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	}
}

var voucher = {
	'add': function() {

	},
	'remove': function(key) {
		$.ajax({
			url: 'index.php?route=checkout/cart/remove',
			type: 'post',
			data: 'key=' + key,
			dataType: 'json',
			beforeSend: function() {
				$('#cart > button').button('loading');
			},
			complete: function() {
				$('#cart > button').button('reset');
			},
			success: function(json) {
				// Need to set timeout otherwise it wont update the total
				setTimeout(function () {
					$('#cart > button').html('<span id="cart-total"><i class="fa fa-shopping-cart"></i> ' + json['total'] + '</span>');
				}, 100);

				if (getURLVar('route') == 'checkout/cart' || getURLVar('route') == 'checkout/checkout') {
					location = 'index.php?route=checkout/cart';
				} else {
					$('#cart > ul').load('index.php?route=common/cart/info ul li');
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	}
}

var wishlist = {
	'add': function(product_id) {
		$.ajax({
			url: 'index.php?route=account/wishlist/add',
			type: 'post',
			data: 'product_id=' + product_id,
			dataType: 'json',
			success: function(json) {
				$('.alert-dismissible').remove();

				if (json['redirect']) {
					location = json['redirect'];
				}

				if (json['success']) {
					$('#content').parent().before('<div class="alert alert-success alert-dismissible"><i class="fa fa-check-circle"></i> ' + json['success'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
				}

				$('#wishlist-total span').html(json['total']);
				$('#wishlist-total').attr('title', json['total']);

				$('html, body').animate({ scrollTop: 0 }, 'slow');
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	},
	'remove': function() {

	}
}

var compare = {
	'add': function(product_id) {
		$.ajax({
			url: 'index.php?route=product/compare/add',
			type: 'post',
			data: 'product_id=' + product_id,
			dataType: 'json',
			success: function(json) {
				$('.alert-dismissible').remove();

				if (json['success']) {
					$('#content').parent().before('<div class="alert alert-success alert-dismissible"><i class="fa fa-check-circle"></i> ' + json['success'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');

					$('#compare-total').html(json['total']);

					$('html, body').animate({ scrollTop: 0 }, 'slow');
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	},
	'remove': function() {

	}
}

/* Agree to Terms */
$(document).delegate('.agree', 'click', function(e) {
	e.preventDefault();

	$('#modal-agree').remove();

	var element = this;

	$.ajax({
		url: $(element).attr('href'),
		type: 'get',
		dataType: 'html',
		success: function(data) {
			html  = '<div id="modal-agree" class="modal">';
			html += '  <div class="modal-dialog">';
			html += '    <div class="modal-content">';
			html += '      <div class="modal-header">';
			html += '        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>';
			html += '        <h4 class="modal-title">' + $(element).text() + '</h4>';
			html += '      </div>';
			html += '      <div class="modal-body">' + data + '</div>';
			html += '    </div>';
			html += '  </div>';
			html += '</div>';

			$('body').append(html);

			$('#modal-agree').modal('show');
		}
	});
});

// Theme specific functionality
$(document).ready(function() {
	// Toggle Search Slider
	$('#search-toggle').on('click', function(e) {
		e.preventDefault();
		$('.search-slider').addClass('show');
		$('.search-slider-overlay').addClass('show');
		$('body').css('overflow', 'hidden');
	});

	// Close Search Slider
	$('.close-search, .search-slider-overlay').on('click', function() {
		$('.search-slider').removeClass('show');
		$('.search-slider-overlay').removeClass('show');
		$('body').css('overflow', '');
	});

	// Toggle Cart Slider with Dynamic Load
	$('#cart-toggle').on('click', function(e) {
		e.preventDefault();
		// Load cart content dynamically
		$.ajax({
			url: 'index.php?route=common/cart/info',
			dataType: 'html',
			success: function(html) {
				// Remove existing slider/overlay if any
				$('.cart-slider').remove();
				$('.cart-slider-overlay').remove();
				
				// Append new content
				$('body').append(html);
				
				// Trigger reflow to ensure transition works
				setTimeout(function() {
					$('.cart-slider').addClass('show');
					$('.cart-slider-overlay').addClass('show');
					$('body').css('overflow', 'hidden');
				}, 10);
			}
		});
	});

	// Close Cart Slider (Delegated for dynamically loaded content)
	$(document).on('click', '.close-cart, .cart-slider-overlay', function() {
		$('.cart-slider').removeClass('show');
		$('.cart-slider-overlay').removeClass('show');
		$('body').css('overflow', '');
	});

	// Override standard cart.remove to work with our slider
	// Store original reference if needed, but we are replacing the behavior for the slider update
	var originalCartRemove = cart.remove;
	cart.remove = function(key) {
		$.ajax({
			url: 'index.php?route=checkout/cart/remove',
			type: 'post',
			data: 'key=' + key,
			dataType: 'json',
			beforeSend: function() {
			   // Optional: Loading state
			},
			success: function(json) {
				// Refresh Cart Slider
				 $.ajax({
					url: 'index.php?route=common/cart/info',
					dataType: 'html',
					success: function(html) {
						$('.cart-slider').remove();
						$('.cart-slider-overlay').remove();
						$('body').append(html);
						setTimeout(function() {
							$('.cart-slider').addClass('show');
							$('.cart-slider-overlay').addClass('show');
							$('body').css('overflow', 'hidden');
						}, 10);
					}
				});

				// Update other totals if visible
				setTimeout(function () {
					$('#cart-total').html(json['total']);
				}, 100);

				if (getURLVar('route') == 'checkout/cart' || getURLVar('route') == 'checkout/checkout') {
					location = 'index.php?route=checkout/cart';
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	}

	// Footer Newsletter Form
	$('#footer-newsletter-form').on('submit', function(e) {
		e.preventDefault();
		$.ajax({
			url: 'index.php?route=extension/theme/oc_ultra/subscribe',
			type: 'post',
			dataType: 'json',
			data: $(this).serialize(),
			beforeSend: function() {
				$('#footer-newsletter-form button').button('loading');
			},
			complete: function() {
				$('#footer-newsletter-form button').button('reset');
			},
			success: function(json) {
				$('.alert-dismissible').remove();
				
				if (json['error']) {
					$('#newsletter-message').html('<div class="text-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + '</div>');
				}
				
				if (json['success']) {
					$('#newsletter-message').html('<div class="text-success"><i class="fa fa-check-circle"></i> ' + json['success'] + '</div>');
					$('#footer-newsletter-form input[name=\'email\']').val('');
				}
			}
		});
	});
});

// Product Page Functionality
function initProductPage() {
	// Product thumbnail switching
	document.querySelectorAll('.product-thumbnails img').forEach(function(thumb) {
		thumb.addEventListener('click', function() {
			document.querySelectorAll('.product-thumbnails img').forEach(function(img) {
				img.classList.remove('active');
			});
			this.classList.add('active');
			document.getElementById('main-product-image').src = this.dataset.image;
		});
	});

	// Quantity selector
	var qtyDecrease = document.querySelector('.qty-decrease');
	if (qtyDecrease) {
		qtyDecrease.addEventListener('click', function() {
			let qty = parseInt(document.querySelector('.qty-display').textContent);
			let minQtyEl = document.querySelector('[data-minimum]');
			let minQty = minQtyEl ? parseInt(minQtyEl.getAttribute('data-minimum')) : 1;
			if (qty > minQty) {
				qty--;
				document.querySelector('.qty-display').textContent = qty;
				document.getElementById('input-quantity').value = qty;
			}
		});
	}

	var qtyIncrease = document.querySelector('.qty-increase');
	if (qtyIncrease) {
		qtyIncrease.addEventListener('click', function() {
			let qty = parseInt(document.querySelector('.qty-display').textContent);
			qty++;
			document.querySelector('.qty-display').textContent = qty;
			document.getElementById('input-quantity').value = qty;
		});
	}

	// Form submission
	$('#form-product').on('submit', function(e) {
		e.preventDefault();

		$.ajax({
			url: 'index.php?route=checkout/cart/add',
			type: 'post',
			data: $('#form-product').serialize(),
			dataType: 'json',
			beforeSend: function() {
				$('#button-cart').prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> ' + $('#button-cart').data('text'));
			},
			complete: function() {
				$('#button-cart').prop('disabled', false).html($('#button-cart').data('text'));
			},
			success: function(json) {
				$('.invalid-feedback').removeClass('d-block');
				$('.text-danger').remove();
				$('.form-group').removeClass('has-error');

				if (json['error']) {
					if (json['error']['option']) {
						for (i in json['error']['option']) {
							var element = $('#input-option-' + i);
							$('#error-option-' + i).html(json['error']['option'][i]).addClass('d-block');
							element.closest('.form-group').addClass('has-error');
						}
					}
					 if (json['error']['recurring']) {
						$('select[name=\'recurring_id\']').after('<div class="text-danger">' + json['error']['recurring'] + '</div>');
					}
				}

				if (json['success']) {
					// Update side cart if exists or show alert
					 $('html, body').animate({ scrollTop: 0 }, 'slow');
					$('#content').parent().before('<div class="alert alert-success alert-dismissible"><i class="fa-solid fa-circle-check"></i> ' + json['success'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
					
					// Refresh cart module
					$('#cart > button').html('<span id="cart-total"><i class="fa fa-shopping-cart"></i> ' + json['total'] + '</span>');
					$('#cart > ul').load('index.php?route=common/cart/info ul li');
					
					// If slide-in cart exists
					 if (typeof openSlideCart === 'function') {
						openSlideCart();
					 }
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	});

	// File upload for product options
	$('button[id^=\'button-upload\']').on('click', function() {
		var node = this;

		$('#form-upload').remove();

		$('body').prepend('<form enctype="multipart/form-data" id="form-upload" style="display: none;"><input type="file" name="file" /></form>');

		$('#form-upload input[name=\'file\']').trigger('click');

		if (typeof timer != 'undefined') {
			clearInterval(timer);
		}

		timer = setInterval(function() {
			if ($('#form-upload input[name=\'file\']').val() != '') {
				clearInterval(timer);

				$.ajax({
					url: 'index.php?route=tool/upload',
					type: 'post',
					dataType: 'json',
					data: new FormData($('#form-upload')[0]),
					cache: false,
					contentType: false,
					processData: false,
					beforeSend: function() {
						$(node).button('loading');
					},
					complete: function() {
						$(node).button('reset');
					},
					success: function(json) {
						$('.text-danger').remove();

						if (json['error']) {
							$(node).parent().find('input').after('<div class="text-danger">' + json['error'] + '</div>');
						}

						if (json['success']) {
							alert(json['success']);

							$(node).parent().find('input').val(json['code']);
						}
					},
					error: function(xhr, ajaxOptions, thrownError) {
						alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					}
				});
			}
		}, 500);
	});

	// Reviews handling
	var productId = $('#form-product input[name="product_id"]').val();
	if (productId) {
		$('#review').delegate('.pagination a', 'click', function(e) {
			e.preventDefault();
			$('#review').fadeOut('slow');
			$('#review').load(this.href);
			$('#review').fadeIn('slow');
		});

		$('#review').load('index.php?route=product/product/review&product_id=' + productId);

		$('#button-review').on('click', function() {
			$.ajax({
				url: 'index.php?route=product/product/write&product_id=' + productId,
				type: 'post',
				dataType: 'json',
				data: $("#form-review").serialize(),
				beforeSend: function() {
					$('#button-review').button('loading');
				},
				complete: function() {
					$('#button-review').button('reset');
				},
				success: function(json) {
					$('.alert-dismissible').remove();

					if (json['error']) {
						$('#review').after('<div class="alert alert-danger alert-dismissible"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + '</div>');
					}

					if (json['success']) {
						$('#review').after('<div class="alert alert-success alert-dismissible"><i class="fa fa-check-circle"></i> ' + json['success'] + '</div>');

						$('input[name=\'name\']').val('');
						$('textarea[name=\'text\']').val('');
						$('input[name=\'rating\']:checked').prop('checked', false);
					}
				}
			});
		});
	}

	// MagnificPopup for product images
	if (typeof $.fn.magnificPopup !== 'undefined') {
		$('.thumbnails').magnificPopup({
			type:'image',
			delegate: 'a',
			gallery: {
				enabled:true
			}
		});
	}
}

// Initialize Swiper Slideshow
function initSlideshow(moduleId) {
	var slideshowId = '#slideshow' + moduleId;
	var paginationClass = '.slideshow' + moduleId;
	
	if ($(slideshowId).length && typeof Swiper !== 'undefined') {
		$(slideshowId).swiper({
			mode: 'horizontal',
			slidesPerView: 1,
			pagination: paginationClass,
			paginationClickable: true,
			nextButton: '.slideshow .swiper-button-next',
			prevButton: '.slideshow .swiper-button-prev',
			spaceBetween: 30,
			autoplay: 2500,
			autoplayDisableOnInteraction: true,
			loop: true,
			paginationBulletRender: function (swiper, index, className) {
				return '<span class="' + className + '">' + (index + 1) + '</span>';
			}
		});
	}
}

// Auto-init functions when DOM is ready
$(document).ready(function() {
	// Check if we're on product page
	if ($('#form-product').length) {
		initProductPage();
	}
});

// Autocomplete */
(function($) {
	$.fn.autocomplete = function(option) {
		return this.each(function() {
			this.timer = null;
			this.items = new Array();

			$.extend(this, option);

			$(this).attr('autocomplete', 'off');

			// Focus
			$(this).on('focus', function() {
				this.request();
			});

			// Blur
			$(this).on('blur', function() {
				setTimeout(function(object) {
					object.hide();
				}, 200, this);
			});

			// Keydown
			$(this).on('keydown', function(event) {
				switch(event.keyCode) {
					case 27: // escape
						this.hide();
						break;
					default:
						this.request();
						break;
				}
			});

			// Click
			this.click = function(event) {
				event.preventDefault();

				value = $(event.target).parent().attr('data-value');

				if (value && this.items[value]) {
					this.select(this.items[value]);
				}
			}

			// Show
			this.show = function() {
				var pos = $(this).position();

				$(this).siblings('ul.dropdown-menu').css({
					top: pos.top + $(this).outerHeight(),
					left: pos.left
				});

				$(this).siblings('ul.dropdown-menu').show();
			}

			// Hide
			this.hide = function() {
				$(this).siblings('ul.dropdown-menu').hide();
			}

			// Request
			this.request = function() {
				clearTimeout(this.timer);

				this.timer = setTimeout(function(object) {
					object.source($(object).val(), $.proxy(object.response, object));
				}, 200, this);
			}

			// Response
			this.response = function(json) {
				html = '';

				if (json.length) {
					for (i = 0; i < json.length; i++) {
						this.items[json[i]['value']] = json[i];
					}

					for (i = 0; i < json.length; i++) {
						if (!json[i]['category']) {
							html += '<li data-value="' + json[i]['value'] + '"><a href="#">' + json[i]['label'] + '</a></li>';
						}
					}

					// Get all the ones with a categories
					var category = new Array();

					for (i = 0; i < json.length; i++) {
						if (json[i]['category']) {
							if (!category[json[i]['category']]) {
								category[json[i]['category']] = new Array();
								category[json[i]['category']]['name'] = json[i]['category'];
								category[json[i]['category']]['item'] = new Array();
							}

							category[json[i]['category']]['item'].push(json[i]);
						}
					}

					for (i in category) {
						html += '<li class="dropdown-header">' + category[i]['name'] + '</li>';

						for (j = 0; j < category[i]['item'].length; j++) {
							html += '<li data-value="' + category[i]['item'][j]['value'] + '"><a href="#">&nbsp;&nbsp;&nbsp;' + category[i]['item'][j]['label'] + '</a></li>';
						}
					}
				}

				if (html) {
					this.show();
				} else {
					this.hide();
				}

				$(this).siblings('ul.dropdown-menu').html(html);
			}

			$(this).after('<ul class="dropdown-menu"></ul>');
			$(this).siblings('ul.dropdown-menu').delegate('a', 'click', $.proxy(this.click, this));

		});
	}
})(window.jQuery);
