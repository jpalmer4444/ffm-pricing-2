<?php

namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;

/**
 * Description of Modal
 *
 * @author jasonpalmer
 */
class Modal extends AbstractHelper{
    /**
     <div id="UsersModal" class="modal fade in" style="display: block;" aria-hidden="false">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="container">
                        <div class="row">
                            <h3 class="pull-left">Create a New User</h3>
                            <a class="pull-right modal-icon ion-ios-close-outline" data-dismiss="modal"></a>
                        </div>
                    </div>
                </div>
                <form method="POST" name="user" action="/user/edit" class="form-horizontal ajaxForm addForm" id="user">
                    <div class="modal-body">
                        <div class="container">
                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-sm-12">
                                    <div class="form-group ">
                                        <label class="control-label col-lg-12" for="username">User Name</label>
                                        <div class="col-lg-12">
                                            <input name="username" id="username" class="form-control" value="" type="text">                                                                                                   
                                        </div>
                                    </div>                                                                                                                    
                                    <div class="form-group ">
                                        <label class="control-label col-lg-12" for="firstname">First Name</label>
                                        <div class="col-lg-12">
                                            <input name="firstname" id="firstname" class="form-control" value="" type="text">                                                                                                    
                                        </div>
                                    </div>
                                    <div class="form-group ">
                                        <label class="control-label col-lg-12" for="custom_role">Role</label>
                                        <div class="col-lg-12">
                                            <select name="custom_role" id="custom_role" class="modal-selectpicker form-control bs-select-hidden">
                                                <option value="3">admin</option>
                                                <option value="6">Assembly Screen</option>
                                                <option value="5">Fabrication Screen</option>
                                                <option value="1">guest</option>
                                                <option value="7">Packing Screen</option>
                                                <option value="4">Receiving Screen</option>
                                                <option value="10">Review Plus</option>
                                                <option value="8">Review Screen</option>
                                                <option value="9">TEST</option></select>
                                                <div class="btn-group bootstrap-select modal-form-control">
                                                    <button type="button" class="btn dropdown-toggle btn-default" data-toggle="dropdown" data-id="custom_role" title="admin">
                                                        <span class="filter-option pull-left">admin</span>
                                                        &nbsp;
                                                        <span class="caret"></span>
                                                    </button>
                                                    <div class="dropdown-menu open">
                                                        <ul class="dropdown-menu inner" role="menu">
                                                            <li data-original-index="0" class="selected">
                                                                <a tabindex="0" class="" style="" data-tokens="null">
                                                                    <span class="text">admin</span>
                                                                    <span class="glyphicon glyphicon-ok check-mark"></span>
                                                                </a>
                                                            </li>
                                                            <li data-original-index="1">
                                                                <a tabindex="0" class="" style="" data-tokens="null">
                                                                    <span class="text">Assembly Screen</span>
                                                                    <span class="glyphicon glyphicon-ok check-mark"></span>
                                                                </a>
                                                            </li>
                                                            <li data-original-index="2">
                                                                <a tabindex="0" class="" style="" data-tokens="null">
                                                                    <span class="text">Fabrication Screen</span>
                                                                    <span class="glyphicon glyphicon-ok check-mark"></span>
                                                                </a>
                                                            </li>
                                                        <li data-original-index="3">
                                                            <a tabindex="0" class="" style="" data-tokens="null">
                                                                <span class="text">guest</span>
                                                                <span class="glyphicon glyphicon-ok check-mark"></span>
                                                            </a>
                                                        </li>
                                                        <li data-original-index="4">
                                                            <a tabindex="0" class="" style="" data-tokens="null">
                                                                <span class="text">Packing Screen</span>
                                                                <span class="glyphicon glyphicon-ok check-mark"></span>
                                                            </a>
                                                        </li>
                                                        <li data-original-index="5">
                                                            <a tabindex="0" class="" style="" data-tokens="null">
                                                                <span class="text">Receiving Screen</span>
                                                                <span class="glyphicon glyphicon-ok check-mark"></span>
                                                            </a>
                                                        </li>
                                                        <li data-original-index="6">
                                                            <a tabindex="0" class="" style="" data-tokens="null">
                                                                <span class="text">Review Plus</span>
                                                                <span class="glyphicon glyphicon-ok check-mark"></span>
                                                            </a>
                                                        </li>
                                                        <li data-original-index="7">
                                                            <a tabindex="0" class="" style="" data-tokens="null">
                                                                <span class="text">Review Screen</span>
                                                                <span class="glyphicon glyphicon-ok check-mark"></span>
                                                            </a>
                                                        </li>
                                                        <li data-original-index="8">
                                                            <a tabindex="0" class="" style="" data-tokens="null">
                                                                <span class="text">TEST</span>
                                                                <span class="glyphicon glyphicon-ok check-mark"></span>
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group ">
                                        <label class="control-label col-lg-12" for="state">Status</label>                                                <div class="col-lg-12">
                                        <select name="state" id="state" class="modal-selectpicker form-control bs-select-hidden">
                                            <option value="1">Active</option>
                                            <option value="0" selected="selected">Inactive</option>
                                        </select>
                                    <div class="btn-group bootstrap-select modal- form-control">
                                        <button type="button" class="btn dropdown-toggle btn-default" data-toggle="dropdown" data-id="state" title="Inactive">
                                            <span class="filter-option pull-left">Inactive</span>
                                            &nbsp;
                                            <span class="caret"></span>
                                        </button>
                                    <div class="dropdown-menu open">
                                        <ul class="dropdown-menu inner" role="menu">
                                            <li data-original-index="0">
                                                <a tabindex="0" class="" style="" data-tokens="null">
                                                    <span class="text">Active</span><span class="glyphicon glyphicon-ok check-mark"></span>
                                                </a>
                                            </li>
                                            <li data-original-index="1" class="selected">
                                                <a tabindex="0" class="" style="" data-tokens="null">
                                                    <span class="text">Inactive</span>
                                                    <span class="glyphicon glyphicon-ok check-mark"></span>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12">
                        
                            <div class="form-group ">
                                <label class="control-label col-lg-12" for="email">Email</label>
                                <div class="col-lg-12">
                                    <input name="email" id="email" class="form-control" value="" type="email">                                                                                                    
                                </div>
                            </div>
                        
                            <div class="form-group ">
                                <label class="control-label col-lg-12" for="printers">Printers assigned</label>
                                <div class="col-lg-12">
                                    <select name="printers[]" id="printers" class="modal-selectpicker form-control bs-select-hidden" multiple="multiple">
                                        <option value="14">HL-L2340D ()</option>
                                        <option value="11">HLL2360D (standard)</option>
                                        <option value="12">HLL2360D ()</option>
                                        <option value="9">PM43-32221545007 (box_list)</option>
                                        <option value="13">PM43-32221545007 ()</option>
                                        <option value="8">PM43-32221545018 (package)</option>
                                        <option value="10">PM43-32221545052 (address)</option>
                                        <option value="7">RECEIVING (tote)</option>
                                    </select>
                                    <div class="btn-group bootstrap-select show-tick modal- form-control">
                                        <button type="button" class="btn dropdown-toggle btn-default" data-toggle="dropdown" data-id="printers" title="Nothing selected">
                                            <span class="filter-option pull-left">Nothing selected</span>
                                            &nbsp;
                                            <span class="caret"></span>
                                        </button>
                                        <div class="dropdown-menu open">
                                            <ul class="dropdown-menu inner" role="menu">
                                                <li data-original-index="0">
                                                    <a tabindex="0" class="" style="" data-tokens="null">
                                                        <span class="text">HL-L2340D ()</span>
                                                        <span class="glyphicon glyphicon-ok check-mark"></span>
                                                    </a>
                                                </li>
                                                <li data-original-index="1">
                                                    <a tabindex="0" class="" style="" data-tokens="null">
                                                        <span class="text">HLL2360D (standard)</span>
                                                        <span class="glyphicon glyphicon-ok check-mark"></span>
                                                    </a>
                                                </li>
                                                <li data-original-index="2">
                                                    <a tabindex="0" class="" style="" data-tokens="null">
                                                        <span class="text">HLL2360D ()</span>
                                                        <span class="glyphicon glyphicon-ok check-mark"></span>
                                                    </a>
                                                </li>
                                                <li data-original-index="3">
                                                    <a tabindex="0" class="" style="" data-tokens="null">
                                                        <span class="text">PM43-32221545007 (box_list)</span>
                                                        <span class="glyphicon glyphicon-ok check-mark"></span>
                                                    </a>
                                                </li>
                                                <li data-original-index="4">
                                                    <a tabindex="0" class="" style="" data-tokens="null">
                                                        <span class="text">PM43-32221545007 ()</span>
                                                        <span class="glyphicon glyphicon-ok check-mark"></span>
                                                    </a>
                                                </li>
                                                <li data-original-index="5">
                                                    <a tabindex="0" class="" style="" data-tokens="null">
                                                        <span class="text">PM43-32221545018 (package)</span>
                                                        <span class="glyphicon glyphicon-ok check-mark"></span>
                                                    </a>
                                                </li>
                                                <li data-original-index="6">
                                                    <a tabindex="0" class="" style="" data-tokens="null">
                                                        <span class="text">PM43-32221545052 (address)</span>
                                                        <span class="glyphicon glyphicon-ok check-mark"></span>
                                                    </a>
                                                </li>
                                                <li data-original-index="7">
                                                    <a tabindex="0" class="" style="" data-tokens="null">
                                                        <span class="text">RECEIVING (tote)</span>
                                                        <span class="glyphicon glyphicon-ok check-mark"></span>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>                                                                                                    
                                </div>
                            </div>
                            <div class="form-group ">
                                <label class="control-label col-lg-12" for="device">Device assigned</label>                                                
                                <div class="col-lg-12">
                                    <select name="device" id="device" class="modal-selectpicker form-control bs-select-hidden">
                                        <option value="">- No device -</option>
                                        <option value="2">Assembly Screen</option>
                                        <option value="3">Packing Screen</option>
                                        <option value="1">Receiving Screen</option>
                                    </select>
                                    <div class="btn-group bootstrap-select modal- form-control">
                                        <button type="button" class="btn dropdown-toggle btn-default" data-toggle="dropdown" data-id="device" title="- No device -">
                                            <span class="filter-option pull-left">- No device -</span>
                                            &nbsp;
                                            <span class="caret"></span>
                                        </button>
                                        <div class="dropdown-menu open">
                                            <ul class="dropdown-menu inner" role="menu">
                                                <li data-original-index="0" class="selected">
                                                    <a tabindex="0" class="" style="" data-tokens="null">
                                                        <span class="text">- No device -</span>
                                                        <span class="glyphicon glyphicon-ok check-mark"></span>
                                                    </a>
                                                </li>
                                                <li data-original-index="1">
                                                    <a tabindex="0" class="" style="" data-tokens="null">
                                                        <span class="text">Assembly Screen</span>
                                                        <span class="glyphicon glyphicon-ok check-mark"></span>
                                                    </a>
                                                </li>
                                                <li data-original-index="2">
                                                    <a tabindex="0" class="" style="" data-tokens="null">
                                                        <span class="text">Packing Screen</span>
                                                        <span class="glyphicon glyphicon-ok check-mark"></span>
                                                    </a>
                                                </li>
                                                <li data-original-index="3">
                                                    <a tabindex="0" class="" style="" data-tokens="null">
                                                        <span class="text">Receiving Screen</span>
                                                        <span class="glyphicon glyphicon-ok check-mark"></span>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>                                                                                                    
                                </div>
                            </div>
                                                                                                                                                                                        
                            <div class="form-group ">
                                <label class="control-label col-lg-12" for="newPassword">New Password</label>                                                
                                <div class="col-lg-12">
                                    <input name="newPassword" id="newPassword" class="form-control" value="" type="password">
                                </div>
                            </div>
                                                                                                                                                                                        
                            <div class="form-group ">
                                <label class="control-label col-lg-12" for="confirmPassword">Confirm Password</label>                                                
                                <div class="col-lg-12">
                                    <input name="confirmPassword" id="confirmPassword" class="form-control" value="" type="password">                                                                                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="container">
                    <div class="row">
                        <input class="btn reset-btn" name="reset" value="Reset Data" type="reset">
                        <input name="submit" id="save-user" class="btn btn-primary" value="Save User" type="submit">                    
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
</div>
</div>
     */
}
