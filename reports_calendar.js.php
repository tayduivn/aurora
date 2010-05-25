var Event = YAHOO.util.Event;
var Dom   = YAHOO.util.Dom;

function quick_link(){
tipo=this.id;
location.href=link+'?tipo='+tipo;
};

function submit_interval(){

from=Dom.get('v_calpop1').value;
to=Dom.get('v_calpop2').value;
location.href=link+"?tipo=f&from="+from+"&to="+to

}

function init(){
Event.addListener(['quick_all','quick_this_month','quick_this_week','quick_yesterday','quick_today'], "click", quick_link);
Event.addListener('submit_interval', "click", submit_interval);

 cal2 = new YAHOO.widget.Calendar("cal2","cal2Container", { title:"<?php echo _('Choose a date')?>:", close:true } );

 cal2.update=updateCal;

 cal2.id='2';
 cal2.render();
 cal2.update();
 cal2.selectEvent.subscribe(handleSelect, cal2, true); 

 cal1 = new YAHOO.widget.Calendar("cal1","cal1Container", { title:"<?php echo _('Choose a date')?>:", close:true } );
 cal1.update=updateCal;
 cal1.id='1';
 cal1.render();
 cal1.update();
 cal1.selectEvent.subscribe(handleSelect, cal1, true); 



//cal2.cfg.setProperty("iframe", true);
//cal2.cfg.setProperty("zIndex", 10);



YAHOO.util.Event.addListener("calpop1", "click", cal1.show, cal1, true);
YAHOO.util.Event.addListener("calpop2", "click", cal2.show, cal2, true);
}

YAHOO.util.Event.onDOMReady(init);