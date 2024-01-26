# developertest
Magento 2 Developer Test

**Magento Back-End Developer**

Please create the following Magento 2 module to demonstrate your understanding of the platform. You can do
this in your own time but please let me know you have received this and when you estimate to have it complete,
before you begin work.

**Brief**

There is a requirement for certain products to be limited by country. For example, visitors from France should not
be able to purchase a specific item.
Please create a module that does the following:

1. At a product level, allow the admin to BLOCK the product from being ordered from one or more
countries.
2. Use ‘IP 2 Country GeoIP’ (https://ip2country.info) to obtain the country for the current customer.
3. When a product is added to the cart, perform a check to make sure it can be ordered.
4. If the customer cannot order the product, show a message to the customer using the standard built-in
notices functionality that reads:
“I’m sorry, this product cannot be ordered from COUNTRY_NAME”.
5. The above message should be editable in the module configuration in the admin.
6. We need to be able to enable/disable the overall functionality in the configuration in the admin.
7. Make sure the module can be installed through composer as a standalone extension.
8. Develop on the latest version of Magento Open Source.
