**Magento Back-End Developer**

Please create the following Magento 2 module to demonstrate your understanding of the platform. You can do this in your own time but please let me know you have received this and when you estimate to have it complete, before you begin work.

**Brief**

There is a requirement for certain products to be limited by country. For example, visitors from France should not be able to purchase a specific item. Please create a module that does the following:

1. At a product level, allow the admin to BLOCK the product from being ordered from one or more countries.
2. Use ‘IP 2 Country GeoIP’ (https://ip2country.info) to obtain the country for the current customer.
3. When a product is added to the cart, perform a check to make sure it can be ordered.
4. If the customer cannot order the product, show a message to the customer using the standard built-in notices functionality that reads: “I’m sorry, this product cannot be ordered from COUNTRY_NAME”.
5. The above message should be editable in the module configuration in the admin.
6. We need to be able to enable/disable the overall functionality in the configuration in the admin.
7. Make sure the module can be installed through composer as a standalone extension.
8. Develop on the latest version of Magento Open Source.

**Status:**

There is a requirement for certain products to be limited by country. For example, visitors from
France should not be able to purchase a specific item.
Please create a module that does the following:

1. At a product level, allow the admin to BLOCK the product from being ordered from one or more
countries.
Status – Covered
  • New Product attribute added with countries dropdown with multiselect option in ‘Restricted Countries
  Setting’ tab.
  • Admin can select multiple countries to block product from purchase

2. Use ‘IP 2 Country GeoIP’ (https://ip2country.info) to obtain the country for the current customer.
Status : Point Covered
  • API is configuration added at the backend.
  • Customer IP is checking at page load event.
  • Once module enabled, customer IP location information is stored in session storage.
  • It will be stored once if module enabled and location information not available.
  • If module disabled, location information set to empty in session storage.
  • While testing locally, IP address to be 127.0.0.1 because its a localhost address. To check with real remote
  address IP, I have added IP setting at the backend configuration to check with specific IP address.
  • After deploy website to server, remote address will be customers respective IP address.

3. When a product is added to the cart, perform a check to make sure it can be ordered.
Status : Covered
  • If product is blocked from UK, error message will be displayed while adding to cart.
  • If product is eligible, then product will be added to cart successfully.

  **Additional Checks:**

  1. At Cart Page
  - If customer added product in cart some days before product getting blocked in ‘UK’
  and trying to purchase after blocked then cart page will be displayed error as below.
  2. At Checkout page (If customer still try to reach at the shipping/payment step)
  - If customer added product in cart from some other country and and trying to checkout
  in blocked country ‘UK’. Validation added at the shipping address country selection and
  place order event.
  3. Reorder from Customer Account
   - If customer tries to reorder product from customer account, restricted products will not
  add and displays error.
  4. From Wishlist
  Tested with adding product from ‘My Wish List’ section.
  5. From Magento Backend
  - Admin also will not be able to place order for the product in blocked countries.
    
4. If the customer cannot order the product, show a message to the customer using the standard
built-in notices functionality that reads:
“I’m sorry, this product cannot be ordered from COUNTRY_NAME”.
Status – Covered

5. The above message should be editable in the module configuration in the admin.
Status : Covered

6. We need to be able to enable/disable the overall functionality in the configuration in the admin.
Status : Covered
All possible required configuration added at the backend.

7. Make sure the module can be installed through composer as a standalone extension.
Status : Covered
  • composer.json file added in module.

8. Develop on the latest version of Magento Open Source.
Status : Covered
  • Module developed on latest version 2.4.6
