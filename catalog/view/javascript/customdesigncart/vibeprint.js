document.addEventListener("DOMContentLoaded", function () {
    const modal = document.getElementById("productDesigner");
    const iframeContainer = modal.querySelector(".iframe-container");

    // Helper function to collect selected options from product form
    function getSelectedOptions() {
        const options = {};
        const optionElements = document.querySelectorAll('[name^="option["]');

        optionElements.forEach(element => {
            // Match both option[123] and option[123][]
            const match = element.name.match(/option\[(\d+)\]/);
            if (match) {
                const optionId = match[1];
                let value = '';

                if (element.type === 'radio') {
                    if (element.checked) {
                        value = element.value;
                    }
                } else if (element.tagName === 'SELECT') {
                    value = element.value;
                }

                if (value) {
                    options[optionId] = value;
                }
            }
        });

        return options;
    }

    // Helper function to validate required options
    function validateRequiredOptions() {
        let valid = true;
        // Select form groups that have the 'required' class (with or without space)
        const requiredOptions = document.querySelectorAll('#product .form-group');

        requiredOptions.forEach(formGroup => {
            // Check if this form group is actually required
            if (!formGroup.classList.contains('required')) {
                return; // Skip non-required options
            }

            const inputs = formGroup.querySelectorAll('[name^="option["]');
            let hasValue = false;

            inputs.forEach(input => {
                if (input.type === 'radio') {
                    if (input.checked) {
                        hasValue = true;
                    }
                } else if (input.tagName === 'SELECT') {
                    if (input.value && input.value !== '') {
                        hasValue = true;
                    }
                } else {
                    // Skip text/textarea/file/date options for validation
                    hasValue = true;
                }
            });

            if (!hasValue) {
                valid = false;
                // Highlight the missing field
                formGroup.classList.add('has-error');
            } else {
                formGroup.classList.remove('has-error');
            }
        });

        return valid;
    }

    // Helper function to create and load iframe
    function loadIframe(iframeSrc) {
        const iframe = document.createElement("iframe");
        iframe.src = iframeSrc;
        iframe.allowFullscreen = true;
        iframe.style.width = "100%";
        iframe.style.height = "100vh";
        iframe.style.border = "none";

        iframeContainer.innerHTML = "";
        iframeContainer.appendChild(iframe);
    }

    // Helper function to load iframe without options/variants
    function loadIframeDefault(productId, cartId, orderProductId) {
        const baseUrl = modal.getAttribute('data-iframe-url');
        let iframeSrc = baseUrl + `&p_id=${productId}`;

        if (cartId) {
            iframeSrc += `&cart_id=${cartId}`;
        }
        if (orderProductId) {
            iframeSrc += `&op_id=${orderProductId}`;
        }

        loadIframe(iframeSrc);
    }

    // Helper function to get variant and load iframe (for products with options)
    function getVariantAndLoadIframe(productId, selectedOptions) {
        const baseUrl = modal.getAttribute('data-iframe-url');

        // Show loading state
        iframeContainer.innerHTML = '<div style="display: flex; justify-content: center; align-items: center; height: 100vh;"><p>Loading variant...</p></div>';

        // Find matching variant via AJAX before setting iframe
        $.ajax({
            url: 'index.php?route=extension/module/customdesigncart/getVariant&product_id=' + productId,
            type: 'POST',
            dataType: 'json',
            data: {
                options: selectedOptions
            },
            success: function (response) {
                let iframeSrc = baseUrl + `&p_id=${productId}`;

                // Add variant ID if found
                if (response.success && response.variant_id) {
                    iframeSrc += `&v_id=${response.variant_id}`;
                } else {
                    console.warn('No matching variant found, using default configuration');
                }
                loadIframe(iframeSrc);
            },
            error: function () {
                // On error, fall back to default
                loadIframeDefault(productId);
            }
        });
    }

    // On modal show
    document.querySelectorAll('[data-target="#productDesigner"]').forEach(button => {
        button.addEventListener('click', function (event) {
            const button = this;
            const productId = button.getAttribute('data-product-id');
            const cartId = button.getAttribute('data-cart-id');
            const orderProductId = button.getAttribute('data-order-product-id');
            const selectedOptions = getSelectedOptions();
            const hasOptions = Object.keys(selectedOptions).length > 0;

            // If no options exist, load iframe directly
            if (!hasOptions) {
                loadIframeDefault(productId, cartId, orderProductId);
                return;
            }

            // For new designs, validate options first
            if (validateRequiredOptions()) {
                getVariantAndLoadIframe(productId, selectedOptions);
            }

        });
    });

    // On modal hide → clean up iframe
    $('#productDesigner').on('hidden.bs.modal', function () {
        iframeContainer.innerHTML = "";
    });
});
window.addEventListener('load', function () {
    window.designState = null;

    $(document).ready(function () {
        const productDesigner = $('#productDesigner');
        const saveInModalHeaderBtn = $('#saveInModalHeader');

        window.addEventListener('message', function (event) {
            if (event.data && event.data.type === 'DESIGN_STATE') {
                window.designState = event.data.data;
            }
        });

        saveInModalHeaderBtn.on('click', function () {

            if (window.designState) {
                const customization = $('#customization');
                customization.text(JSON.stringify(window.designState, null, 2));
            }
            $('#button-cart').trigger('click');
            productDesigner.modal('hide');
        });
    });
});