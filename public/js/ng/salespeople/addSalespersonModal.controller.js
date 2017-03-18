(function () {
  'use strict';

  angular
          .module('salespeople')
          .controller('AddSalespersonModalController', [
            '$rootScope',
            '$uibModalInstance',
            'salesAttrId',
            'username',
            'email',
            'full_name',
            'phone1',
            'status',
            'id',
            'role',
            AddSalespersonModalController
          ]);

  function AddSalespersonModalController(
          $rootScope, 
          $uibModalInstance, 
          salesAttrId, 
          username, 
          email, 
          full_name, 
          phone1, 
          status,
          id,
          role
                  ) {

    var vm = this;
    
    vm.scenario = !id ? 'create' : 'edit';


    vm.salesAttrId = salesAttrId;
    vm.full_name = full_name;
    
    vm.username = username;
    vm.phone1 = phone1;
    vm.status = status;
    vm.email = email;
    vm.id = id;
    
    //we never pass in password
    //either we are creating a new Salesperson and the value will 
    //be entered or we are editing - and in that case, we either LEAVE
    //the password as is (when no value entered for password) OR
    //we edit it with form values.
    vm.password = '';
    vm.password_verify = '';
    
    vm.role = role;
    
    vm.label = {};
    
    //labels
    vm.label.email = 'Email';
    vm.label.role = 'Role';
    vm.label.password = 'Password';
    vm.label.password_verify = 'Verify Password';
    vm.label.status = 'Status';
    vm.label.username = 'Username';
    vm.label.phone1 = 'Phone';
    vm.label.full_name = 'Full Name';
    
    vm.message = {};
    //default messages
    vm.message.email = 'Email is not valid';
    vm.message.username = 'Username is not valid';
    vm.message.phone1 = 'Phone is not valid';
    vm.message.full_name = 'Full Name is not valid';
    
    //validators
    vm.pattern = {};
    //Standard email address
    vm.pattern.email = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
    
    //Alphabets, numbers and space(' ') no special characters min 3 and max 20 characters. 
    vm.pattern.full_name = /^[A-Za-z0-9 ]{3,20}$/;
    
    //Supports alphabets and numbers no special characters except underscore('_') min 3 and max 20 characters. 
    vm.pattern.username = /^[A-Za-z0-9_]{3,20}$/;
    
    //Standard Phone
    vm.pattern.phone1 = /^(\(?[0-9]{3}\)?)((\s|\-){1})?[0-9]{3}((\s|\-){1})?[0-9]{4}$/;
    
    //Password matching expression. Match all alphanumeric character 
    //and predefined wild characters. Password must consists of at 
    //least 4 characters and not more than 32 characters.
    vm.pattern.password = /^([a-zA-Z0-9@*#]{4,32})$/;

    vm.error = '';

    activate();

    function activate() {
      $rootScope.$on("dismissModals", function () {
        $uibModalInstance.dismiss("rootScope broadcast");
      });
    }

    vm.ok = function (valid) {

      if (valid) {

      //pass to calling script
        $uibModalInstance.close({
          "addSalespersonModal": {
            "username": vm.username,
            "id": vm.id,
            "password": vm.password,
            "password_verify": vm.password_verify,
            "scenario": vm.scenario,
            "role": vm.role,
            "full_name": vm.full_name,
            "status": vm.status,
            "phone1": vm.phone1,
            "email": vm.email,
            "scenario": vm.scenario,
            "salesAttrId": vm.salesAttrId
          }
        });

      }
    };
    
    vm.selectStatus = function (status) {
      console.log('Status: '+status);
      if (status !== vm.status) {
        vm.status = status;
      }
    };

    vm.cancel = function () {

      $uibModalInstance.dismiss("cancel button");
    };
  }
})();


