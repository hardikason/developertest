define([
    'mage/translate',
    "jquery",
    'mage/url'
], function($t, $, url) {
    'use strict';

    return function(rules) {
        rules['blocked-country'] = {
            handler: function (value) {
                var allowed = false;
                if (value) {
                    $.ajax({
                        showLoader: true,
                        url: url.build('developertest/index/index'),
                        data: {
                            countryCode: value
                        },
                        type: "POST",
                        dataType: 'json',
                        cache: false,
                        async: false, //This is required so that it wait for the response
                        success: function (data) {
                            if(data.allowed === true) {
                                allowed = true;
                            }
                        }
                    });
                    return allowed;
                }
                return true;
            },
            message: $t('Some of products are blocked from this country.')
        };
        return rules;
    };
});
