(function() {
  "use strict";

  angular
    .module("app", ["header", "users", "screen", "ffm", "customer", "product", "salespeople", "ngSanitize", "LocalStorageModule"])
    .config(["$httpProvider", "$provide", "$compileProvider", "localStorageServiceProvider", configApp])
    .constant("config", (function() {
      //console.log('Init config');
      //add constants here.
      var loginUrl = (typeof window.loginUrl !== "undefined" ? window.loginUrl : '');
      var username = (typeof window.username !== "undefined" ? window.username : '');
      var salesAttrId = (typeof window.salesAttrId !== "undefined" ? window.salesAttrId : '');
      var usersTableAjax = (typeof window.usersTableAjax !== "undefined" ? window.usersTableAjax : '');
      var productsTableAjax = (typeof window.productsTableAjax !== "undefined" ? window.productsTableAjax : '');
      var customersTableAjax = (typeof window.customersTableAjax !== "undefined" ? window.customersTableAjax : '');
      var salespeopleTableAjax = (typeof window.salespeopleTableAjax !== "undefined" ? window.salespeopleTableAjax : '');
      var usersTableUpdateStatusAjax = (typeof window.usersTableUpdateStatusAjax !== "undefined" ? window.usersTableUpdateStatusAjax : '');
      var addSalespersonUrl = (typeof window.addSalespersonUrl !== "undefined" ? window.addSalespersonUrl : '');
      //console.log('loginUrl: '+loginUrl);
      //addSalespersonUrl
      console.log('salespeopleTableAjax: '+salespeopleTableAjax);
      return {
        salesAttrId: salesAttrId,
        username: username,
        urls: {
          login : loginUrl,
          addSalespersonUrl : addSalespersonUrl,
          usersTableAjax : usersTableAjax,
          productsTableAjax : productsTableAjax,
          customersTableAjax : customersTableAjax,
          salespeopleTableAjax : salespeopleTableAjax,
          usersTableUpdateStatusAjax : usersTableUpdateStatusAjax
        },
      };
    })())
    
    .factory("checkSession", ["$q", "$window", "config", checkSession]);

//this interceptor will re-direct users to the login page when session either times out or is rejected for some other reason.
  function checkSession($q, $window, config) {
    var checkSessionInterceptors = {
      response: function(response) {
        if (typeof response.data === "string" && response.data.indexOf('id="login-form"') > 0) {
          $window.location.href = config.urls.login;
          return $q.reject("session expired");
        }
        return response;
      }
    };
    return checkSessionInterceptors;
  }

  function configApp($httpProvider, $provide, $compileProvider, localStorageServiceProvider){
    $httpProvider.defaults.headers.post["Content-Type"] = "application/x-www-form-urlencoded; charset=utf-8";
    $httpProvider.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";
    $httpProvider.defaults.withCredentials = true;
    localStorageServiceProvider.setPrefix('ff');
    localStorageServiceProvider
    .setStorageCookie(30, '/', true);
    $httpProvider.interceptors.push("checkSession");
    $provide.decorator('$locale', function ($delegate) {
      var value = $delegate.DATETIME_FORMATS;
      value.SHORTDAY = [
          "Su",
          "Mo",
          "Tu",
          "We",
          "Th",
          "Fr",
          "Sa"
      ];
      return $delegate;
    });
    // $compileProvider.debugInfoEnabled(false);
    $compileProvider.commentDirectivesEnabled(false);
    $compileProvider.cssClassDirectivesEnabled(false);
  }
})();



