/*! Fabrik */

define(["jquery","fab/element"],function(a,b){return window.FbPassword=new Class({Extends:b,options:{progressbar:!1},initialize:function(a,b){this.parent(a,b),this.options.editable&&this.ini()},ini:function(){this.element&&this.element.addEvent("keyup",function(a){this.passwordChanged(a)}.bind(this)),!0===this.options.ajax_validation&&this.getConfirmationField().addEvent("blur",function(a){this.callvalidation(a)}.bind(this)),""===this.getConfirmationField().get("value")&&(this.getConfirmationField().value=this.element.value),Fabrik.addEvent("fabrik.form.doelementfx",function(b,c,d,e){if(b===this.form&&d===this.strElement)switch(c){case"disable":a(this.getConfirmationField()).prop("disabled",!0);break;case"enable":a(this.getConfirmationField()).prop("disabled",!1);break;case"readonly":a(this.getConfirmationField()).prop("readonly",!0);break;case"notreadonly":a(this.getConfirmationField()).prop("readonly",!1)}}.bind(this))},callvalidation:function(a){this.form.doElementValidation(a,!1,"_check")},cloned:function(a){console.log("cloned"),this.parent(a),this.ini()},passwordChanged:function(){var b=this.getContainer().getElement(".strength");if("null"!==typeOf(b)){var c=new RegExp("^(?=.{6,})(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*\\W).*$","g"),d=new RegExp("^(?=.{6,})(((?=.*[A-Z])(?=.*[a-z]))|((?=.*[A-Z])(?=.*[0-9]))|((?=.*[a-z])(?=.*[0-9]))).*$","g"),e=new RegExp("(?=.{6,}).*","g"),f=this.element,g="";if(this.options.progressbar){var h,i="";c.test(f.value)?(i=Joomla.JText._("PLG_ELEMENT_PASSWORD_STRONG"),h=a(Fabrik.jLayouts["fabrik-progress-bar-strong"])):d.test(f.value)?(i=Joomla.JText._("PLG_ELEMENT_PASSWORD_MEDIUM"),h=a(Fabrik.jLayouts["fabrik-progress-bar-medium"])):e.test(f.value)?(i=Joomla.JText._("PLG_ELEMENT_PASSWORD_WEAK"),h=a(Fabrik.jLayouts["fabrik-progress-bar-weak"])):(i=Joomla.JText._("PLG_ELEMENT_PASSWORD_MORE_CHARACTERS"),h=a(Fabrik.jLayouts["fabrik-progress-bar-more"]));var j={title:i};a(h).tooltip(j),a(b).replaceWith(h)}else g=!1===e.test(f.value)?"<span>"+Joomla.JText._("PLG_ELEMENT_PASSWORD_MORE_CHARACTERS")+"</span>":c.test(f.value)?'<span style="color:green">'+Joomla.JText._("PLG_ELEMENT_PASSWORD_STRONG")+"</span>":d.test(f.value)?'<span style="color:orange">'+Joomla.JText._("PLG_ELEMENT_PASSWORD_MEDIUM")+"</span>":'<span style="color:red">'+Joomla.JText._("PLG_ELEMENT_PASSWORD_WEAK")+"</span>",b.set("html",g)}},getConfirmationField:function(){return this.getContainer().getElement("input[name*=check]")}}),window.FbPassword});