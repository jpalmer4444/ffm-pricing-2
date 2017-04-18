(function () {

  'use strict';

  /*
   * Directive needs:
   * ffm-validate-salesperson-field {email || username}
   * ffm-validate-salesperson-scenario {edit || create}
   * ffm-validate-salesperson-id {only required when scenario == edit}
   */

  angular.module('ffm').
          directive('ffmValidateSalesperson', ['config', '$http', function (config, $http) {
              return {
                require: 'ngModel',
                scope: {
                  ffmValidateSalespersonField: "@ffmValidateSalespersonField",
                  ffmValidateSalespersonId: "&ffmValidateSalespersonId",
                  ffmValidateSalespersonScenario: "&ffmValidateSalespersonScenario"
                },
                link: function (scope, element, attr, mCtrl) {
                  function myValidation(value) {

                    var field = scope['ffmValidateSalespersonField'];
                    var scenario = scope['ffmValidateSalespersonScenario'];
                    var id = scenario === 'edit' ? scope['ffmValidateSalespersonId'] : '';//create has no id

                    if (field !== 'email' && field !== 'username') {
                      console.log('Validate Salesperson case: ' + field + ' Case not found. Scenario: ' + scenario);
                      mCtrl.$setValidity('validateSalespersonCaseNotFound', false);
                    } else {

                      var data = {
                        id: id,
                        validationcase: field,
                        scenario: scenario,
                        value: value
                      };
                      
                      $http
                              .post(config.urls.validateAddSalespersonUrl, $.param(data))
                              .then(function (response) {
                                //will be one of validateSalespersonemail or validateSalespersonusername
                                var messageName = 'validateSalesperson' + field;

                                if(response && response.data && response.data.success){
                                  
                                  mCtrl.$setValidity(messageName, true);
                                }else{
                                  console.log(JSON.stringify(response.data.messages));
                                  mCtrl.$setValidity(messageName, false);
                                }

                              }, function () {

                                console.log('Server Error!');
                              });

                    }

                    return value;
                  }
                  mCtrl.$parsers.push(myValidation);
                }
              };
            }]);
})();


