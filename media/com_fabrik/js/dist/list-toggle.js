/*! Fabrik */

define(["jquery"],function(n){return new Class({initialize:function(e){n("#"+e.id+" .togglecols .dropdown-menu a, #"+e.id+" .togglecols .dropdown-menu li").click(function(e){e.stopPropagation()}),e.addEvent("mouseup:relay(a[data-toggle-col])",function(e,t){var a=t.get("data-toggle-state"),o=t.get("data-toggle-col");this.toggleColumn(o,a,t)}.bind(this));e.getElements("a[data-toggle-group]");e.addEvent("mouseup:relay(a[data-toggle-group])",function(e,t){var a,o=t.get("data-toggle-state"),n=t.get("data-toggle-group");document.getElements("a[data-toggle-parent-group="+n+"]").each(function(e){var t=e.get("data-toggle-col");this.toggleColumn(t,o,e)}.bind(this)),a="open"===(o="open"===o?"close":"open")?"":" muted",t.getElement("i").className="icon-eye-"+o+a,t.set("data-toggle-state",o)}.bind(this))},toggleColumn:function(e,t,a){var o;"open"===(t="open"===t?"close":"open")?(n(".fabrik___heading ."+e).show(),n(".fabrikFilterContainer ."+e).show(),n(".fabrik_row  ."+e).show(),n(".fabrik_calculations  ."+e).show(),o=""):(n(".fabrik___heading ."+e).hide(),n(".fabrikFilterContainer ."+e).hide(),n(".fabrik_row  ."+e).hide(),n(".fabrik_calculations  ."+e).hide(),o=" muted"),a.getElement("i").className="icon-eye-"+t+o,a.set("data-toggle-state",t)}})});