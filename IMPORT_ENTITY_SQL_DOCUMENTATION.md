# OpenCart 2 Entity Import SQL Documentation

This document outlines the complete SQL insertion process for each entity type in OpenCart 2, showing all related tables that must be populated for proper functionality.

---

## 1. CATEGORY IMPORT

A category requires **4 related tables** to be fully inserted and displayed correctly in admin:

### Tables Required:
1. `oc_category` - Main category data
2. `oc_category_description` - Category name, description, meta tags (per language)
3. `oc_category_to_store` - Store assignment
4. `oc_category_path` - **CRITICAL** - Hierarchical path structure (required for admin display)

### Example: Inserting Category ID 25 (Components - Top Level)

```sql
-- Step 1: Insert main category record
INSERT INTO `oc_category` (
    `category_id`, 
    `image`, 
    `parent_id`,  -- 0 for top-level categories
    `top`,        -- 1 if displayed in top menu
    `column`,     -- Number of columns for subcategories
    `sort_order`, 
    `status`,     -- 1 = enabled, 0 = disabled
    `date_added`, 
    `date_modified`
) VALUES (
    25,
    '',
    0,      -- No parent (top-level)
    1,      -- Show in top menu
    1,      -- 1 column layout
    3,      -- Sort order
    1,      -- Enabled
    '2009-01-31 01:04:25',
    '2011-05-30 12:14:55'
);

-- Step 2: Insert category description (language-specific)
INSERT INTO `oc_category_description` (
    `category_id`, 
    `language_id`,  -- 1 for English
    `name`, 
    `description`, 
    `meta_title`, 
    `meta_description`, 
    `meta_keyword`
) VALUES (
    25,
    1,
    'Components',
    '',  -- Can include HTML description
    'Components',
    '',
    ''
);

-- Step 3: Assign to store (0 = default store)
INSERT INTO `oc_category_to_store` (
    `category_id`, 
    `store_id`
) VALUES (
    25,
    0  -- Default store
);

-- Step 4: Insert category path (REQUIRED FOR ADMIN DISPLAY)
-- For top-level category: only one path entry pointing to itself
INSERT INTO `oc_category_path` (
    `category_id`,  -- The category we're creating
    `path_id`,      -- The category in the path
    `level`         -- 0 for self, increments for parents
) VALUES (
    25,
    25,  -- Points to itself
    0    -- Level 0 = self
);
```

### Example: Inserting Category ID 28 (Monitors - Child of Components)

```sql
-- Step 1: Insert main category
INSERT INTO `oc_category` VALUES (
    28,     -- category_id
    '',     -- image
    25,     -- parent_id (Components)
    0,      -- not in top menu (parent handles it)
    0,      -- columns
    1,      -- sort_order
    1,      -- status
    '2009-02-02 13:11:12',
    '2010-08-22 06:32:46'
);

-- Step 2: Insert description
INSERT INTO `oc_category_description` VALUES (
    28,
    1,
    'Monitors',
    '',
    'Monitors',
    '',
    ''
);

-- Step 3: Assign to store
INSERT INTO `oc_category_to_store` VALUES (28, 0);

-- Step 4: Insert category path (2 ENTRIES REQUIRED)
-- First: copy parent's path
INSERT INTO `oc_category_path` VALUES (
    28,     -- category_id
    25,     -- path_id (parent category)
    0       -- level 0
);

-- Second: add own path
INSERT INTO `oc_category_path` VALUES (
    28,     -- category_id
    28,     -- path_id (self)
    1       -- level 1 (child level)
);
```

### Example: Inserting Category ID 35 (test 1 - Grandchild: Components > Monitors > test 1)

```sql
-- Step 1: Main category
INSERT INTO `oc_category` VALUES (35, '', 28, 0, 0, 0, 1, '2010-09-17 10:06:48', '2010-09-18 14:02:42');

-- Step 2: Description
INSERT INTO `oc_category_description` VALUES (35, 1, 'test 1', '', 'test 1', '', '');

-- Step 3: Store
INSERT INTO `oc_category_to_store` VALUES (35, 0);

-- Step 4: Category path (3 ENTRIES REQUIRED for grandchild)
-- Copy grandparent path
INSERT INTO `oc_category_path` VALUES (35, 25, 0);  -- Components (level 0)

-- Copy parent path
INSERT INTO `oc_category_path` VALUES (35, 28, 1);  -- Monitors (level 1)

-- Add own path
INSERT INTO `oc_category_path` VALUES (35, 35, 2);  -- test 1 (level 2)
```

### Category Path Logic Summary:
- **Top-level category**: 1 path entry (self at level 0)
- **Child category**: 2 path entries (parent at level 0, self at level 1)
- **Grandchild category**: 3 path entries (grandparent at level 0, parent at level 1, self at level 2)
- **Pattern**: Copy all parent's paths with same level, then add own path with level = parent's max level + 1

---

## 2. PRODUCT IMPORT

A product requires **3 minimum required tables** + optional related tables:

### Minimum Required Tables:
1. `oc_product` - Main product data
2. `oc_product_description` - Product name, description, meta tags (per language)
3. `oc_product_to_store` - Store assignment

### Optional Related Tables:
4. `oc_product_to_category` - Category assignments
5. `oc_product_attribute` - Custom attributes
6. `oc_product_discount` - Quantity-based discounts
7. `oc_product_image` - Additional images
8. `oc_product_option` - Product options (size, color, etc.)
9. `oc_product_option_value` - Option values with pricing
10. `oc_product_related` - Related products
11. `oc_product_reward` - Reward points
12. `oc_product_special` - Special prices

### Example: Inserting Product ID 28 (HTC Touch HD)

```sql
-- Step 1: Insert main product record
INSERT INTO `oc_product` (
    `product_id`, 
    `model`,           -- REQUIRED: Product model/SKU
    `sku`, 
    `upc`, 
    `ean`, 
    `jan`, 
    `isbn`, 
    `mpn`, 
    `location`, 
    `quantity`,        -- Stock quantity
    `stock_status_id`, -- Stock status (5=Out of Stock, 6=2-3 Days, 7=In Stock)
    `image`,           -- Main product image path
    `manufacturer_id`, 
    `shipping`,        -- 1=requires shipping, 0=no shipping
    `price`, 
    `points`,          -- Reward points
    `tax_class_id`, 
    `date_available`, 
    `weight`, 
    `weight_class_id`, -- 1=kg, 2=g, 3=lb, 4=oz
    `length`, 
    `width`, 
    `height`, 
    `length_class_id`, -- 1=cm, 2=mm, 3=inch
    `subtract`,        -- 1=subtract stock, 0=don't subtract
    `minimum`,         -- Minimum order quantity
    `sort_order`, 
    `status`,          -- 1=enabled, 0=disabled
    `viewed`,          -- View count
    `date_added`, 
    `date_modified`
) VALUES (
    28,
    'Product 1',                        -- model
    '',                                 -- sku
    '', '', '', '', '', '',             -- various IDs
    '',                                 -- location
    939,                                -- quantity
    7,                                  -- In Stock
    'catalog/demo/htc_touch_hd_1.jpg',  -- image
    5,                                  -- HTC manufacturer
    1,                                  -- requires shipping
    100.0000,                           -- price
    200,                                -- reward points
    9,                                  -- tax class
    '2009-02-03',                       -- date_available
    146.40000000,                       -- weight
    2,                                  -- grams
    0.00000000, 0.00000000, 0.00000000, -- dimensions
    1,                                  -- cm
    1,                                  -- subtract stock
    1,                                  -- minimum quantity
    0,                                  -- sort order
    1,                                  -- enabled
    0,                                  -- viewed
    '2009-02-03 16:06:50',
    '2011-09-30 01:05:39'
);

-- Step 2: Insert product description (language-specific)
INSERT INTO `oc_product_description` (
    `product_id`, 
    `language_id`, 
    `name`,           -- REQUIRED: Product name
    `description`,    -- HTML description
    `tag`,            -- Comma-separated tags
    `meta_title`,     -- SEO meta title
    `meta_description`, 
    `meta_keyword`
) VALUES (
    28,
    1,
    'HTC Touch HD',
    '<p>HTC Touch - in High Definition. Watch music videos...</p>',  -- Full HTML
    '',
    'HTC Touch HD',
    '',
    ''
);

-- Step 3: Assign to store
INSERT INTO `oc_product_to_store` (
    `product_id`, 
    `store_id`
) VALUES (
    28,
    0  -- Default store
);

-- Step 4: Assign to categories (optional but recommended)
-- A product can belong to multiple categories
INSERT INTO `oc_product_to_category` (
    `product_id`, 
    `category_id`
) VALUES 
    (28, 20),  -- Desktops category
    (28, 24);  -- Phones & PDAs category

-- Step 5: Add product attributes (optional)
INSERT INTO `oc_product_attribute` (
    `product_id`, 
    `attribute_id`,  -- Links to oc_attribute table
    `language_id`, 
    `text`           -- Attribute value
) VALUES 
    (28, 1, 1, '4GB'),      -- RAM: 4GB
    (28, 2, 1, '32GB');     -- Storage: 32GB

-- Step 6: Add additional images (optional)
INSERT INTO `oc_product_image` (
    `product_image_id`, 
    `product_id`, 
    `image`,          -- Image path
    `sort_order`
) VALUES 
    (1, 28, 'catalog/demo/htc_touch_hd_2.jpg', 1),
    (2, 28, 'catalog/demo/htc_touch_hd_3.jpg', 2);

-- Step 7: Add product options (optional) - e.g., Size, Color
INSERT INTO `oc_product_option` (
    `product_option_id`, 
    `product_id`, 
    `option_id`,    -- Links to oc_option table
    `value`,        -- Default value
    `required`      -- 1=required, 0=optional
) VALUES 
    (1, 28, 5, '', 1);  -- Color option, required

-- Step 8: Add option values with pricing (optional)
INSERT INTO `oc_product_option_value` (
    `product_option_value_id`, 
    `product_option_id`, 
    `product_id`, 
    `option_id`, 
    `option_value_id`,  -- Links to oc_option_value table
    `quantity`,         -- Additional stock for this option
    `subtract`,         -- Subtract stock
    `price`,            -- Price modifier
    `price_prefix`,     -- '+' or '-'
    `points`,           -- Point modifier
    `points_prefix`,    -- '+' or '-'
    `weight`,           -- Weight modifier
    `weight_prefix`     -- '+' or '-'
) VALUES 
    (1, 1, 28, 5, 41, 0, 1, 10.0000, '+', 0, '+', 0.0000, '+'),  -- Red: +$10
    (2, 1, 28, 5, 42, 0, 1, 15.0000, '+', 0, '+', 0.0000, '+');  -- Blue: +$15

-- Step 9: Add related products (optional)
INSERT INTO `oc_product_related` (
    `product_id`, 
    `related_id`  -- ID of related product
) VALUES 
    (28, 29),  -- Related to product 29
    (28, 30);  -- Related to product 30

-- Step 10: Add quantity discounts (optional)
INSERT INTO `oc_product_discount` (
    `product_discount_id`, 
    `product_id`, 
    `customer_group_id`,  -- Customer group (1=Default)
    `quantity`,           -- Minimum quantity
    `priority`,           -- Discount priority
    `price`,              -- Discounted price
    `date_start`,         -- Start date
    `date_end`            -- End date
) VALUES 
    (1, 28, 1, 10, 1, 90.0000, '0000-00-00', '0000-00-00'),  -- 10+ units: $90
    (2, 28, 1, 50, 1, 85.0000, '0000-00-00', '0000-00-00');  -- 50+ units: $85

-- Step 11: Add special prices (optional)
INSERT INTO `oc_product_special` (
    `product_special_id`, 
    `product_id`, 
    `customer_group_id`, 
    `priority`, 
    `price`,       -- Special price
    `date_start`,  -- Sale start date
    `date_end`     -- Sale end date
) VALUES 
    (1, 28, 1, 1, 79.9900, '2024-01-01', '2024-12-31');  -- Special: $79.99

-- Step 12: Add reward points (optional)
INSERT INTO `oc_product_reward` (
    `product_reward_id`, 
    `product_id`, 
    `customer_group_id`, 
    `points`  -- Points earned for purchase
) VALUES 
    (1, 28, 1, 100);  -- Earn 100 points
```

---

## 3. MANUFACTURER IMPORT

A manufacturer requires **2 related tables**:

### Tables Required:
1. `oc_manufacturer` - Main manufacturer data
2. `oc_manufacturer_to_store` - Store assignment

### Example: Inserting Manufacturer ID 8 (Apple)

```sql
-- Step 1: Insert main manufacturer record
INSERT INTO `oc_manufacturer` (
    `manufacturer_id`, 
    `name`,        -- REQUIRED: Manufacturer name
    `image`,       -- Logo image path
    `sort_order`
) VALUES (
    8,
    'Apple',
    'catalog/demo/apple_logo.jpg',
    0
);

-- Step 2: Assign to store
INSERT INTO `oc_manufacturer_to_store` (
    `manufacturer_id`, 
    `store_id`
) VALUES (
    8,
    0  -- Default store
);
```

---

## 4. CUSTOMER IMPORT

A customer requires **1 main table** + optional address tables:

### Tables Required:
1. `oc_customer` - Main customer data
2. `oc_address` (optional) - Customer addresses

### Example: Inserting a Customer

```sql
-- Step 1: Insert main customer record
INSERT INTO `oc_customer` (
    `customer_id`, 
    `customer_group_id`,  -- 1=Default customer group
    `store_id`,           -- Store registration
    `language_id`, 
    `firstname`,          -- REQUIRED
    `lastname`,           -- REQUIRED
    `email`,              -- REQUIRED (unique)
    `telephone`,          -- REQUIRED
    `fax`, 
    `password`,           -- Hashed password
    `salt`,               -- Password salt
    `cart`,               -- Serialized cart data
    `wishlist`,           -- Serialized wishlist
    `newsletter`,         -- 0=no, 1=yes
    `address_id`,         -- Default address ID
    `custom_field`, 
    `ip`, 
    `status`,             -- 1=enabled, 0=disabled
    `approved`,           -- 1=approved, 0=pending
    `safe`,               -- 1=safe, 0=not safe
    `token`, 
    `code`, 
    `date_added`
) VALUES (
    1,
    1,                    -- Default group
    0,                    -- Default store
    1,                    -- English
    'John',
    'Doe',
    'john.doe@example.com',
    '123-456-7890',
    '',
    'hashed_password_here',  -- Use password_hash() in PHP
    'random_salt',
    '',                   -- Empty cart
    '',                   -- Empty wishlist
    1,                    -- Subscribed to newsletter
    0,                    -- No default address yet
    '',
    '192.168.1.1',
    1,                    -- Enabled
    1,                    -- Approved
    0,                    -- Not marked safe
    '',
    '',
    NOW()
);

-- Step 2: Insert customer address (optional)
INSERT INTO `oc_address` (
    `address_id`, 
    `customer_id`, 
    `firstname`, 
    `lastname`, 
    `company`, 
    `address_1`,          -- REQUIRED
    `address_2`, 
    `city`,               -- REQUIRED
    `postcode`,           -- REQUIRED (may vary by country)
    `country_id`,         -- REQUIRED
    `zone_id`,            -- State/Province ID
    `custom_field`
) VALUES (
    1,
    1,                    -- Customer ID
    'John',
    'Doe',
    '',
    '123 Main Street',
    'Apt 4B',
    'New York',
    '10001',
    223,                  -- United States
    3655,                 -- New York state
    ''
);

-- Step 3: Update customer with default address
UPDATE `oc_customer` 
SET `address_id` = 1 
WHERE `customer_id` = 1;
```

---

## 5. ORDER IMPORT (Advanced)

An order requires **4 minimum tables** + optional tables:

### Minimum Required Tables:
1. `oc_order` - Main order data
2. `oc_order_product` - Products in the order
3. `oc_order_total` - Order totals (subtotal, shipping, tax, total)
4. `oc_order_history` - Order status history

### Optional Tables:
5. `oc_order_option` - Product options selected
6. `oc_order_voucher` - Gift vouchers
7. `oc_order_recurring` - Recurring payments

### Example: Inserting Order ID 1

```sql
-- Step 1: Insert main order
INSERT INTO `oc_order` (
    `order_id`, 
    `invoice_no`, 
    `invoice_prefix`, 
    `store_id`, 
    `store_name`, 
    `store_url`, 
    `customer_id`, 
    `customer_group_id`, 
    `firstname`, 
    `lastname`, 
    `email`, 
    `telephone`, 
    `fax`, 
    `custom_field`, 
    `payment_firstname`, 
    `payment_lastname`, 
    `payment_company`, 
    `payment_address_1`, 
    `payment_address_2`, 
    `payment_city`, 
    `payment_postcode`, 
    `payment_country`, 
    `payment_country_id`, 
    `payment_zone`, 
    `payment_zone_id`, 
    `payment_address_format`, 
    `payment_custom_field`, 
    `payment_method`, 
    `payment_code`, 
    `shipping_firstname`, 
    `shipping_lastname`, 
    `shipping_company`, 
    `shipping_address_1`, 
    `shipping_address_2`, 
    `shipping_city`, 
    `shipping_postcode`, 
    `shipping_country`, 
    `shipping_country_id`, 
    `shipping_zone`, 
    `shipping_zone_id`, 
    `shipping_address_format`, 
    `shipping_custom_field`, 
    `shipping_method`, 
    `shipping_code`, 
    `comment`, 
    `total`,                    -- Order total
    `order_status_id`,          -- Order status (1=Pending, 2=Processing, 5=Complete)
    `affiliate_id`, 
    `commission`, 
    `marketing_id`, 
    `tracking`, 
    `language_id`, 
    `currency_id`, 
    `currency_code`, 
    `currency_value`, 
    `ip`, 
    `forwarded_ip`, 
    `user_agent`, 
    `accept_language`, 
    `date_added`, 
    `date_modified`
) VALUES (
    1,
    0,                          -- Not yet invoiced
    'INV-',
    0,                          -- Default store
    'Your Store',
    'http://yourstore.com',
    1,                          -- Customer ID
    1,                          -- Default group
    'John',
    'Doe',
    'john.doe@example.com',
    '123-456-7890',
    '',
    '',
    'John',                     -- Payment info
    'Doe',
    '',
    '123 Main Street',
    '',
    'New York',
    '10001',
    'United States',
    223,
    'New York',
    3655,
    '',
    '',
    'Cash On Delivery',
    'cod',
    'John',                     -- Shipping info
    'Doe',
    '',
    '123 Main Street',
    '',
    'New York',
    '10001',
    'United States',
    223,
    'New York',
    3655,
    '',
    '',
    'Flat Rate',
    'flat.flat',
    '',                         -- Order comment
    150.0000,                   -- Total amount
    1,                          -- Pending status
    0,
    0.0000,
    0,
    '',
    1,                          -- English
    1,
    'USD',
    1.00000000,
    '192.168.1.1',
    '',
    'Mozilla/5.0...',
    'en-US',
    NOW(),
    NOW()
);

-- Step 2: Insert order products
INSERT INTO `oc_order_product` (
    `order_product_id`, 
    `order_id`, 
    `product_id`, 
    `name`,                     -- Product name
    `model`,                    -- Product model
    `quantity`, 
    `price`,                    -- Unit price
    `total`,                    -- Line total (price * quantity)
    `tax`,                      -- Tax per unit
    `reward`                    -- Reward points
) VALUES (
    1,
    1,                          -- Order ID
    28,                         -- Product ID
    'HTC Touch HD',
    'Product 1',
    2,                          -- Quantity
    100.0000,                   -- Price per unit
    200.0000,                   -- Total (100 * 2)
    0.0000,
    0
);

-- Step 3: Insert order product options (if product has options)
INSERT INTO `oc_order_option` (
    `order_option_id`, 
    `order_id`, 
    `order_product_id`, 
    `product_option_id`, 
    `product_option_value_id`, 
    `name`,                     -- Option name (e.g., "Color")
    `value`,                    -- Option value (e.g., "Red")
    `type`                      -- Option type (select, radio, checkbox, text)
) VALUES (
    1,
    1,
    1,                          -- order_product_id from above
    1,
    41,
    'Color',
    'Red',
    'select'
);

-- Step 4: Insert order totals (REQUIRED - multiple entries)
-- Subtotal
INSERT INTO `oc_order_total` (
    `order_total_id`, 
    `order_id`, 
    `code`,                     -- Total type code
    `title`,                    -- Display title
    `value`,                    -- Amount
    `sort_order`                -- Display order
) VALUES (
    1,
    1,
    'sub_total',
    'Sub-Total',
    200.0000,
    1
);

-- Shipping
INSERT INTO `oc_order_total` VALUES (
    2,
    1,
    'shipping',
    'Flat Shipping Rate',
    5.0000,
    3
);

-- Tax
INSERT INTO `oc_order_total` VALUES (
    3,
    1,
    'tax',
    'VAT (20%)',
    40.0000,
    5
);

-- Total
INSERT INTO `oc_order_total` VALUES (
    4,
    1,
    'total',
    'Total',
    245.0000,                   -- 200 + 5 + 40
    9
);

-- Step 5: Insert order history (status changes)
INSERT INTO `oc_order_history` (
    `order_history_id`, 
    `order_id`, 
    `order_status_id`,          -- New status
    `notify`,                   -- 1=customer notified, 0=not notified
    `comment`,                  -- Status comment
    `date_added`
) VALUES (
    1,
    1,
    1,                          -- Pending status
    1,                          -- Customer notified
    'Order placed',
    NOW()
);
```

---

## SUMMARY OF IMPORT REQUIREMENTS

### Minimum Required Inserts Per Entity:

| Entity | Required Tables | Optional Tables | Critical Notes |
|--------|----------------|-----------------|----------------|
| **Category** | 4 tables | 1 (layout) | `oc_category_path` is CRITICAL - without it, categories don't show in admin |
| **Product** | 3 tables | 9+ tables | Can have extensive related data (options, images, attributes) |
| **Manufacturer** | 2 tables | 0 | Simple structure |
| **Customer** | 1 table | 1 (address) | Password must be properly hashed |
| **Order** | 4 tables | 3+ tables | Complex structure with totals and history required |

### Key Import Order Dependencies:

1. **Categories** must exist before assigning products to them
2. **Manufacturers** must exist before assigning to products
3. **Customers** must exist before creating orders
4. **Products** must exist before adding to orders
5. **Language ID** = 1 for English (default)
6. **Store ID** = 0 for default store
7. **Status** = 1 for enabled, 0 for disabled

### Critical Path Hierarchy Logic:

**Category Path Formula:**
```
FOR each parent in hierarchy (from root to immediate parent):
    INSERT (category_id, parent_path_id, parent_level)
END FOR
INSERT (category_id, category_id, final_level)
```

**Example:** Category at depth 3
- Level 0: Grandparent path
- Level 1: Parent path  
- Level 2: Self path

This path structure enables:
- Breadcrumb navigation
- Category tree display in admin
- Efficient parent/child queries
- Category filtering and search

### Common Import Pitfalls:

1. ❌ **Forgetting `oc_category_path`** - Categories won't appear in admin
2. ❌ **Checking `category_id > 0` instead of database existence** - Skips valid inserts
3. ❌ **Not copying parent paths** - Breaks category hierarchy
4. ❌ **Wrong import mode logic** - Insert mode runs updates anyway
5. ❌ **Missing `oc_product_to_store`** - Products won't display
6. ❌ **Missing `oc_product_description`** - Products have no name/description
7. ❌ **Not handling multi-language** - Only works for one language

---

## CODE IMPLEMENTATION REFERENCE

Based on this documentation, the import model should:

### For Categories:
```php
// 1. Insert to oc_category
// 2. Insert to oc_category_description
// 3. Insert to oc_category_to_store
// 4. Build and insert oc_category_path:
if (parent_id > 0) {
    // Copy all parent paths
    $parent_paths = SELECT * FROM oc_category_path WHERE category_id = parent_id ORDER BY level;
    foreach ($parent_paths as $level => $path) {
        INSERT INTO oc_category_path (category_id, path_id, level) VALUES (new_id, path.path_id, level);
    }
    // Add own path at next level
    INSERT INTO oc_category_path (category_id, path_id, level) VALUES (new_id, new_id, next_level);
} else {
    // Top-level: just insert self
    INSERT INTO oc_category_path (category_id, path_id, level) VALUES (new_id, new_id, 0);
}
```

### For Products:
```php
// 1. Insert to oc_product
// 2. Insert to oc_product_description
// 3. Insert to oc_product_to_store
// 4. If categories: Insert to oc_product_to_category (can be multiple)
// 5. Optional: Insert related data (images, options, attributes, etc.)
```

### Existence Checking:
```php
// CORRECT: Query database
$exists = (SELECT COUNT(*) FROM oc_category WHERE category_id = X) > 0;

// WRONG: Just check if ID exists
$exists = ($category_id > 0);  // CSV might have ID that doesn't exist in DB!
```

---

*Document created: 2026-02-05*  
*Based on OpenCart 2.3 SQL dump analysis*
