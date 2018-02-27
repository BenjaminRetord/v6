/*! Fabrik */

Date.CultureInfo={name:"it-IT",englishName:"Italian (Italy)",nativeName:"italiano (Italia)",dayNames:["domenica","lunedì","martedì","mercoledì","giovedì","venerdì","sabato"],abbreviatedDayNames:["dom","lun","mar","mer","gio","ven","sab"],shortestDayNames:["do","lu","ma","me","gi","ve","sa"],firstLetterDayNames:["d","l","m","m","g","v","s"],monthNames:["gennaio","febbraio","marzo","aprile","maggio","giugno","luglio","agosto","settembre","ottobre","novembre","dicembre"],abbreviatedMonthNames:["gen","feb","mar","apr","mag","giu","lug","ago","set","ott","nov","dic"],amDesignator:"",pmDesignator:"",firstDayOfWeek:1,twoDigitYearMax:2029,dateElementOrder:"dmy",formatPatterns:{shortDate:"dd/MM/yyyy",longDate:"dddd d MMMM yyyy",shortTime:"H.mm",longTime:"H.mm.ss",fullDateTime:"dddd d MMMM yyyy H.mm.ss",sortableDateTime:"yyyy-MM-ddTHH:mm:ss",universalSortableDateTime:"yyyy-MM-dd HH:mm:ssZ",rfc1123:"ddd, dd MMM yyyy HH:mm:ss GMT",monthDay:"dd MMMM",yearMonth:"MMMM yyyy"},regexPatterns:{jan:/^gen(naio)?/i,feb:/^feb(braio)?/i,mar:/^mar(zo)?/i,apr:/^apr(ile)?/i,may:/^mag(gio)?/i,jun:/^giu(gno)?/i,jul:/^lug(lio)?/i,aug:/^ago(sto)?/i,sep:/^set(tembre)?/i,oct:/^ott(obre)?/i,nov:/^nov(embre)?/i,dec:/^dic(embre)?/i,sun:/^do(m(enica)?)?/i,mon:/^lu(n(edì)?)?/i,tue:/^ma(r(tedì)?)?/i,wed:/^me(r(coledì)?)?/i,thu:/^gi(o(vedì)?)?/i,fri:/^ve(n(erdì)?)?/i,sat:/^sa(b(ato)?)?/i,future:/^next/i,past:/^last|past|prev(ious)?/i,add:/^(\+|aft(er)?|from|hence)/i,subtract:/^(\-|bef(ore)?|ago)/i,yesterday:/^yes(terday)?/i,today:/^t(od(ay)?)?/i,tomorrow:/^tom(orrow)?/i,now:/^n(ow)?/i,millisecond:/^ms|milli(second)?s?/i,second:/^sec(ond)?s?/i,minute:/^mn|min(ute)?s?/i,hour:/^h(our)?s?/i,week:/^w(eek)?s?/i,month:/^m(onth)?s?/i,day:/^d(ay)?s?/i,year:/^y(ear)?s?/i,shortMeridian:/^(a|p)/i,longMeridian:/^(a\.?m?\.?|p\.?m?\.?)/i,timezone:/^((e(s|d)t|c(s|d)t|m(s|d)t|p(s|d)t)|((gmt)?\s*(\+|\-)\s*\d\d\d\d?)|gmt|utc)/i,ordinalSuffix:/^\s*(st|nd|rd|th)/i,timeContext:/^\s*(\:|a(?!u|p)|p)/i},timezones:[{name:"UTC",offset:"-000"},{name:"GMT",offset:"-000"},{name:"EST",offset:"-0500"},{name:"EDT",offset:"-0400"},{name:"CST",offset:"-0600"},{name:"CDT",offset:"-0500"},{name:"MST",offset:"-0700"},{name:"MDT",offset:"-0600"},{name:"PST",offset:"-0800"},{name:"PDT",offset:"-0700"}]};