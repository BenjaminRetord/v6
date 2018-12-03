/*! Fabrik */

var FbRepeatGroup=new Class({Implements:[Options,Events],options:{repeatmin:1},initialize:function(e,t){this.element=document.id(e),this.setOptions(t),this.counter=this.getCounter(),this.watchAdd(),this.watchDelete()},repeatContainers:function(){return this.element.getElements(".repeatGroup")},watchAdd:function(){this.element.getElement("a[data-button=addButton]").addEvent("click",function(e){e.stop();var t=this.repeatContainers().getLast();newc=this.counter+1;var n=t.id.replace("-"+this.counter,"-"+newc),i=new Element("div",{class:"repeatGroup",id:n}).set("html",t.innerHTML);i.inject(t,"after"),this.counter=newc,0!==this.counter&&(i.getElements("input, select").each(function(a){var s="",o=a.id;if(""!==a.id){var e=a.id.split("-");e.pop(),s=e.join("-")+"-"+this.counter,a.id=s}this.increaseName(a),$H(FabrikAdmin.model.fields).each(function(e,t){var n=!1;if("null"!==typeOf(FabrikAdmin.model.fields[t][o])){var i=FabrikAdmin.model.fields[t][o];n=Object.clone(i);try{n.cloned(s,this.counter)}catch(e){fconsole("no clone method available for "+a.id)}}!1!==n&&(FabrikAdmin.model.fields[t][a.id]=n)}.bind(this))}.bind(this)),i.getElements("img[src=components/com_fabrik/images/ajax-loader.gif]").each(function(e){var t=e.id.split("-");t.pop();var n=t.join("-")+"-"+this.counter+"_loader";e.id=n}.bind(this)))}.bind(this))},getCounter:function(){return this.repeatContainers().length},watchDelete:function(){this.element.getElements("a[data-button=deleteButton]").removeEvents(),this.element.getElements("a[data-button=deleteButton]").each(function(e,t){e.addEvent("click",function(e){(e.stop(),this.getCounter()>this.options.repeatmin)&&this.repeatContainers().getLast().destroy();this.rename(t)}.bind(this))}.bind(this))},increaseName:function(e){var t=e.name.split("]["),n=t[2].replace("]","").toInt()+1;t.splice(2,1,n),e.name=t.join("][")+"]"},rename:function(t){this.element.getElements("input, select").each(function(e){e.name=this._decreaseName(e.name,t)}.bind(this))},_decreaseName:function(e,t){var n=e.split("]["),i=n[2].replace("]","").toInt();return 1<=i&&t<i&&i--,3===n.length&&(i+="]"),n.splice(2,1,i),n.join("][")}});