$(document).ready(function() {
    // ============================================
    // SEARCH FUNCTIONALITY
    // ============================================
    
    // Search button click handler
    $(document).on('click', '#search-button', function() {
        var url = $('base').attr('href') + 'index.php?route=product/search';
        var value = $('#search input[name=\'search\']').val();
        
        if (value) {
            url += '&search=' + encodeURIComponent(value);
        }
        
        location = url;
    });
    
    // Search on Enter key
    $(document).on('keydown', '#search input[name=\'search\']', function(e) {
        if (e.keyCode == 13) {
            e.preventDefault();
            $('#search-button').trigger('click');
        }
    });

    // ============================================
    // HEADER SLIDER FUNCTIONALITY
    // ============================================
    
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
    };

    // ============================================
    // FOOTER NEWSLETTER FORM
    // ============================================
    
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