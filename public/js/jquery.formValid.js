/* ===================================================
 *  jquery.formValid.js v1.0.0
 *  https://github.com/mrygielski/formValid
 * ===================================================
 *  Copyright (c) 2018 Michał Rygielski
 *  The MIT License (MIT)
 *
 *  Permission is hereby granted, free of charge, to any person obtaining a copy
 *  of this software and associated documentation files (the "Software"), to deal
 *  in the Software without restriction, including without limitation the rights
 *  to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 *  copies of the Software, and to permit persons to whom the Software is
 *  furnished to do so, subject to the following conditions:
 *
 *  The above copyright notice and this permission notice shall be included in all
 *  copies or substantial portions of the Software.
 *
 *  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 *  FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 *  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 *  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 *  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 *  SOFTWARE.*
 *
 *  Usage: https://mrygielski.github.io/jquery.formValid/
 * ========================================================== */

function isset(variable) {
    return typeof variable !== 'undefined' ? true : false;
}

(function ( $ ) {

    $.fn.formValid = function(options) {

        var opts = $.extend( {}, $.fn.formValid.defaults, options );
        var _formValid = {};

        _formValid = {

            /**
             * Validation test methods
             * @param el
             * @param type
             * @returns {boolean}
             */
            test : function(el, type) {
            
                var value = el.val();
            
                if (type == 'postcode') {

                    if (value.match('^[0-9]{2}\-[0-9]{3}$')) return true; else return false;

                }

                if (type == 'email') {

                    var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
                    return regex.test(value);

                }

                if (type == 'number') {

                   return /^-?[\d.]+(?:e-?\d+)?$/.test(value);

                }

                if (type == 'letters') {

                   return /^[a-zA-Z]*$/.test(value);

                }

                if (type == 'phone') {

                    var regex = /^\+?(\d.*){3,}$/;
                    return regex.test(value);

                }
            
            },
        
            /**
             * Adds an error mark
             * @param el
             * @param message
             */
            addError : function(el, message) {
                
                el.addClass('error');
                el.find('input,textarea').addClass('error');
                if (message == '') {
                    el.find('.valid-message').hide();
                } else el.find('.valid-message').html(message);

            },

            /**
             * Removes the error mark
             * @param el
             */
            removeError : function(el) {

                el.removeClass('error');
                el.find('input,textarea').removeClass('error');
                el.find('.valid-message').html('');

            },

            /**
             * Management method for individual tests
             * @param required
             * @param type
             * @param fValue
             * @param testMessage
             * @param field
             * @private
             */
            _tests : function(required, type, fValue, testMessage, field, regex) {
                    var xx=field.val();
                if (!isset(regex)) regex = "";
                
                if (type == 'null' && required) { 
                    if (fValue.errors == 0) 
                        if (field.val() == '') {
                            _formValid.addError(field.parent(), testMessage);
                            fValue.errors++; 
                        } else _formValid.removeError(field.parent());
                }    
                if (regex != "") {
                    if (fValue.errors == 0) 
                        if (!xx.match(new RegExp(regex))) {
                            _formValid.addError(field.parent(), testMessage);
                            fValue.errors++; 
                        } else _formValid.removeError(field.parent());
                }                
                if (type == 'number') { 
                    if (fValue.errors == 0) 
                        if (!_formValid.test(field, 'number')) {
                            _formValid.addError(field.parent(), testMessage);
                            fValue.errors++; 
                        } else _formValid.removeError(field.parent());
                } 
                if (type == 'email') { 
                    if (fValue.errors == 0) 
                        if (!_formValid.test(field, 'email')) {
                            _formValid.addError(field.parent(), testMessage);
                            fValue.errors++; 
                        } else _formValid.removeError(field.parent());
                } 
                if (type == 'letters') { 
                    if (fValue.errors == 0) 
                        if (!_formValid.test(field, 'letters')) {
                            _formValid.addError(field.parent(), testMessage);
                            fValue.errors++; 
                        } else _formValid.removeError(field.parent());
                } 
                if (type == 'postcode') { 
                    if (fValue.errors == 0) 
                        if (!_formValid.test(field, 'postcode')) {
                            _formValid.addError(field.parent(), testMessage);
                            fValue.errors++; 
                        } else _formValid.removeError(field.parent());
                } 
                if (type == 'phone') { 
                    if (fValue.errors == 0) 
                        if (!_formValid.test(field, 'phone')) {
                            _formValid.addError(field.parent(), testMessage);
                            fValue.errors++; 
                        } else _formValid.removeError(field.parent());
                } 
                
            }
            
        };
        
        return {

            /**
             * Carries out a validation test for the modified field after a specific timeout value.
             * @param timeout
             */
            keypress : function(timeout) {
                
                $('[data-field]').on("change keyup paste", function() {
                    
                    var _this = $(this);
                    var getFieldName = _this.attr('data-field'); 
                
                    $(this).delayKeyup(function(e) { 
                        
                        $.each(opts.fields, function(fKey, fValue) {
            
                            var field = $('[data-field="' + fKey + '"]');
                            var fieldName = fKey;
                            var fieldRequired = isset(fValue.required) ? fValue.required : false;
                            
                            if (fieldName == getFieldName) {
                                
                                if (isset(fValue.change)) fValue.change(_this);

                                fValue.errors = 0;
                                            
                                if (isset(fValue.tests)) {

                                    $.each(fValue.tests, function(tKey, tValue) {                                        
                                                    
                                        var testType = tValue.type;
                                        var testRegEx = tValue.regex;
                                        var testMessage = tValue.message;
                                        
                                        if (fieldRequired) {
                                                    
                                            _formValid._tests(true, testType, fValue, testMessage, field, testRegEx);
                                                                                                        
                                        } else {
                                            
                                            if (field.val() != '') {
                                         
                                                _formValid._tests(false, testType, fValue, testMessage, field, testRegEx);
                                                
                                            }
                                            
                                        }
                                        
                                    });

                                }
                            
                            }
                          
                        });
                    
                    }, timeout);    
                    
                }); 
                
            },

            /**
             * Performs a validation test of all defined fields.
             */
            test : function() {
                        
                $.each(opts.fields, function(fKey, fValue) {
    
                    var field = $('[data-field="' + fKey + '"]');
                    var fieldName = fKey;
                    var fieldRequired = isset(fValue.required) ? fValue.required : false;
                    
                    fValue.errors = 0;
                                
                    if (isset(fValue.tests)) {

                        $.each(fValue.tests, function(tKey, tValue) {                                        
                                        
                            var testType = tValue.type;
                            var testMessage = tValue.message;
                            
                            if (fieldRequired) {
                                        
                                if (testType != 'null') _formValid._tests(true, 'null', fValue, 'This field is required', field); else
                                _formValid._tests(true, testType, fValue, testMessage, field);
                                                                                            
                            } else {
                                
                                if (field.val() != '') {
                             
                                    _formValid._tests(false, testType, fValue, testMessage, field);
                                    
                                }
                                
                            }
                            
                        });

                    }
                  
                });
                    
            },

            /**
             * Returns information in the form of a number if the form has validation errors.
             * @returns {number}
             */
            errors : function() {

                var num = 0;
                $.each(opts.fields, function(fKey, fValue) {

                    num += fValue.errors;

                });

                return num;

            }
            
        }
        
    };
    
    $.fn.formValid.defaults = {
        
        fields: {}
        
    };
    
    $.fn.delayKeyup = function(callback, ms) {
        var timer = 0;
        var el = $(this);
        $(this).keyup(function() {                   
            clearTimeout (timer);
                timer = setTimeout(function() {
                callback(el)
            }, ms);
        });
        return $(this);
    };

}( jQuery ));