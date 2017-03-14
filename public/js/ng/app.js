(function() {
  "use strict";

  angular
    .module("app", ["header", "users", "screen", "ffm", "ngSanitize"])
    .config(["$httpProvider", "$provide", "$compileProvider", configApp])
    .constant("config", (function() {
      //console.log('Init config');
      //add constants here.
      var loginUrl = (typeof window.loginUrl !== "undefined" ? window.loginUrl : '');
      var usersTableAjax = (typeof window.usersTableAjax !== "undefined" ? window.usersTableAjax : '');
      var usersTableUpdateStatusAjax = (typeof window.usersTableUpdateStatusAjax !== "undefined" ? window.usersTableUpdateStatusAjax : '');
      //console.log('loginUrl: '+loginUrl);
      //console.log('usersTableAjax: '+usersTableAjax);
      return {
        urls: {
          login : loginUrl,
          usersTableAjax : usersTableAjax,
          usersTableUpdateStatusAjax : usersTableUpdateStatusAjax
        }
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

  function configApp($httpProvider, $provide, $compileProvider){
    $httpProvider.defaults.headers.post["Content-Type"] = "application/x-www-form-urlencoded; charset=utf-8";
    $httpProvider.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";
    $httpProvider.defaults.withCredentials = true;
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



