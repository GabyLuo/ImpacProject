webpackJsonp([67],{"OW/K":function(t,e,r){var o=r("mHnL");"string"==typeof o&&(o=[[t.i,o,""]]),o.locals&&(t.exports=o.locals);(0,r("rjj0").default)("7149e95f",o,!1,{})},SldL:function(t,e){!function(e){"use strict";var r,o=Object.prototype,i=o.hasOwnProperty,a="function"==typeof Symbol?Symbol:{},n=a.iterator||"@@iterator",s=a.asyncIterator||"@@asyncIterator",l=a.toStringTag||"@@toStringTag",c="object"==typeof t,f=e.regeneratorRuntime;if(f)c&&(t.exports=f);else{(f=e.regeneratorRuntime=c?t.exports:{}).wrap=b;var p="suspendedStart",d="suspendedYield",u="executing",m="completed",h={},v={};v[n]=function(){return this};var g=Object.getPrototypeOf,y=g&&g(g(S([])));y&&y!==o&&i.call(y,n)&&(v=y);var _=C.prototype=$.prototype=Object.create(v);x.prototype=_.constructor=C,C.constructor=x,C[l]=x.displayName="GeneratorFunction",f.isGeneratorFunction=function(t){var e="function"==typeof t&&t.constructor;return!!e&&(e===x||"GeneratorFunction"===(e.displayName||e.name))},f.mark=function(t){return Object.setPrototypeOf?Object.setPrototypeOf(t,C):(t.__proto__=C,l in t||(t[l]="GeneratorFunction")),t.prototype=Object.create(_),t},f.awrap=function(t){return{__await:t}},k(q.prototype),q.prototype[s]=function(){return this},f.AsyncIterator=q,f.async=function(t,e,r,o){var i=new q(b(t,e,r,o));return f.isGeneratorFunction(e)?i:i.next().then(function(t){return t.done?t.value:i.next()})},k(_),_[l]="Generator",_[n]=function(){return this},_.toString=function(){return"[object Generator]"},f.keys=function(t){var e=[];for(var r in t)e.push(r);return e.reverse(),function r(){for(;e.length;){var o=e.pop();if(o in t)return r.value=o,r.done=!1,r}return r.done=!0,r}},f.values=S,F.prototype={constructor:F,reset:function(t){if(this.prev=0,this.next=0,this.sent=this._sent=r,this.done=!1,this.delegate=null,this.method="next",this.arg=r,this.tryEntries.forEach(L),!t)for(var e in this)"t"===e.charAt(0)&&i.call(this,e)&&!isNaN(+e.slice(1))&&(this[e]=r)},stop:function(){this.done=!0;var t=this.tryEntries[0].completion;if("throw"===t.type)throw t.arg;return this.rval},dispatchException:function(t){if(this.done)throw t;var e=this;function o(o,i){return s.type="throw",s.arg=t,e.next=o,i&&(e.method="next",e.arg=r),!!i}for(var a=this.tryEntries.length-1;a>=0;--a){var n=this.tryEntries[a],s=n.completion;if("root"===n.tryLoc)return o("end");if(n.tryLoc<=this.prev){var l=i.call(n,"catchLoc"),c=i.call(n,"finallyLoc");if(l&&c){if(this.prev<n.catchLoc)return o(n.catchLoc,!0);if(this.prev<n.finallyLoc)return o(n.finallyLoc)}else if(l){if(this.prev<n.catchLoc)return o(n.catchLoc,!0)}else{if(!c)throw new Error("try statement without catch or finally");if(this.prev<n.finallyLoc)return o(n.finallyLoc)}}}},abrupt:function(t,e){for(var r=this.tryEntries.length-1;r>=0;--r){var o=this.tryEntries[r];if(o.tryLoc<=this.prev&&i.call(o,"finallyLoc")&&this.prev<o.finallyLoc){var a=o;break}}a&&("break"===t||"continue"===t)&&a.tryLoc<=e&&e<=a.finallyLoc&&(a=null);var n=a?a.completion:{};return n.type=t,n.arg=e,a?(this.method="next",this.next=a.finallyLoc,h):this.complete(n)},complete:function(t,e){if("throw"===t.type)throw t.arg;return"break"===t.type||"continue"===t.type?this.next=t.arg:"return"===t.type?(this.rval=this.arg=t.arg,this.method="return",this.next="end"):"normal"===t.type&&e&&(this.next=e),h},finish:function(t){for(var e=this.tryEntries.length-1;e>=0;--e){var r=this.tryEntries[e];if(r.finallyLoc===t)return this.complete(r.completion,r.afterLoc),L(r),h}},catch:function(t){for(var e=this.tryEntries.length-1;e>=0;--e){var r=this.tryEntries[e];if(r.tryLoc===t){var o=r.completion;if("throw"===o.type){var i=o.arg;L(r)}return i}}throw new Error("illegal catch attempt")},delegateYield:function(t,e,o){return this.delegate={iterator:S(t),resultName:e,nextLoc:o},"next"===this.method&&(this.arg=r),h}}}function b(t,e,r,o){var i=e&&e.prototype instanceof $?e:$,a=Object.create(i.prototype),n=new F(o||[]);return a._invoke=function(t,e,r){var o=p;return function(i,a){if(o===u)throw new Error("Generator is already running");if(o===m){if("throw"===i)throw a;return N()}for(r.method=i,r.arg=a;;){var n=r.delegate;if(n){var s=E(n,r);if(s){if(s===h)continue;return s}}if("next"===r.method)r.sent=r._sent=r.arg;else if("throw"===r.method){if(o===p)throw o=m,r.arg;r.dispatchException(r.arg)}else"return"===r.method&&r.abrupt("return",r.arg);o=u;var l=w(t,e,r);if("normal"===l.type){if(o=r.done?m:d,l.arg===h)continue;return{value:l.arg,done:r.done}}"throw"===l.type&&(o=m,r.method="throw",r.arg=l.arg)}}}(t,r,n),a}function w(t,e,r){try{return{type:"normal",arg:t.call(e,r)}}catch(t){return{type:"throw",arg:t}}}function $(){}function x(){}function C(){}function k(t){["next","throw","return"].forEach(function(e){t[e]=function(t){return this._invoke(e,t)}})}function q(t){var e;this._invoke=function(r,o){function a(){return new Promise(function(e,a){!function e(r,o,a,n){var s=w(t[r],t,o);if("throw"!==s.type){var l=s.arg,c=l.value;return c&&"object"==typeof c&&i.call(c,"__await")?Promise.resolve(c.__await).then(function(t){e("next",t,a,n)},function(t){e("throw",t,a,n)}):Promise.resolve(c).then(function(t){l.value=t,a(l)},n)}n(s.arg)}(r,o,e,a)})}return e=e?e.then(a,a):a()}}function E(t,e){var o=t.iterator[e.method];if(o===r){if(e.delegate=null,"throw"===e.method){if(t.iterator.return&&(e.method="return",e.arg=r,E(t,e),"throw"===e.method))return h;e.method="throw",e.arg=new TypeError("The iterator does not provide a 'throw' method")}return h}var i=w(o,t.iterator,e.arg);if("throw"===i.type)return e.method="throw",e.arg=i.arg,e.delegate=null,h;var a=i.arg;return a?a.done?(e[t.resultName]=a.value,e.next=t.nextLoc,"return"!==e.method&&(e.method="next",e.arg=r),e.delegate=null,h):a:(e.method="throw",e.arg=new TypeError("iterator result is not an object"),e.delegate=null,h)}function O(t){var e={tryLoc:t[0]};1 in t&&(e.catchLoc=t[1]),2 in t&&(e.finallyLoc=t[2],e.afterLoc=t[3]),this.tryEntries.push(e)}function L(t){var e=t.completion||{};e.type="normal",delete e.arg,t.completion=e}function F(t){this.tryEntries=[{tryLoc:"root"}],t.forEach(O,this),this.reset(!0)}function S(t){if(t){var e=t[n];if(e)return e.call(t);if("function"==typeof t.next)return t;if(!isNaN(t.length)){var o=-1,a=function e(){for(;++o<t.length;)if(i.call(t,o))return e.value=t[o],e.done=!1,e;return e.value=r,e.done=!0,e};return a.next=a}}return{next:N}}function N(){return{value:r,done:!0}}}(function(){return this}()||Function("return this")())},Srjf:function(t,e,r){"use strict";Object.defineProperty(e,"__esModule",{value:!0});var o=r("Xxa5"),i=r.n(o),a=r("exGp"),n=r.n(a),s=r("Dd8w"),l=r.n(s),c=r("NYxO"),f=r("mtWM"),p=r.n(f),d={components:{},beforeRouteEnter:function(t,e,r){r(function(t){for(var o=!1,i=t.$store.getters["user/role"],a=0;a<i.length;a++)i[a].toUpperCase()!=="admin".toUpperCase()&&i[a].toUpperCase()!=="ROOT".toUpperCase()&&i[a].toUpperCase()!=="CLIENTE".toUpperCase()&&i[a].toUpperCase()!=="LIDER".toUpperCase()&&i[a].toUpperCase()!=="COORDINADOR".toUpperCase()&&i[a].toUpperCase()!=="GERENTE DE OPERACIONES".toUpperCase()&&i[a].toUpperCase()!=="PMO".toUpperCase()&&i[a].toUpperCase()!=="FINANZAS".toUpperCase()&&i[a].toUpperCase()!=="OPERACIÓN".toUpperCase()&&i[a].toUpperCase()!=="LICITACIONES".toUpperCase()&&i[a].toUpperCase()!=="COBRANZA".toUpperCase()&&i[a].toUpperCase()!=="INVENTARIOS".toUpperCase()&&i[a].toUpperCase()!=="AUXILIAR TESORERIA".toUpperCase()||(o=!0);o?r():r("/"===e.path?"/dashboard":e.path)})},data:function(){return{reportes:[],views:{grid:!0},form:{data:[],fields:{cliente:0,empresa:0,folio:"",ffiscal:"",remision:"",fecha_inicio:"",fecha_fin:""},columns:[{name:"tipo",label:"Tipo",field:"tipo",sortable:!0,type:"string",align:"left"},{name:"fecha_venta",label:"Fecha factura",field:"fecha_venta",sortable:!0,type:"string",align:"left"},{name:"year",label:"Año",field:"year",sortable:!0,type:"string",align:"left"},{name:"municipio",label:"Municipio",field:"municipio",sortable:!0,type:"string",align:"left"},{name:"estado",label:"Estado",field:"estado",sortable:!0,type:"string",align:"left"},{name:"num_contrato",label:"Número de contrato",field:"num_contrato",sortable:!0,type:"string",align:"left"},{name:"folio_remision",label:"Folio remisión",field:"folio_remision",sortable:!0,type:"string",align:"left"},{name:"codigo",label:"Código del proyecto",field:"codigo",sortable:!0,type:"string",align:"left"},{name:"proyecto",label:"Nombre del proyecto",field:"proyecto",sortable:!0,type:"string",align:"left"},{name:"monto_contrato",label:"Importe del contrato",field:"monto_contrato",sortable:!0,type:"string",align:"right"},{name:"empresa",label:"Empresa",field:"empresa",sortable:!0,type:"string",align:"left"},{name:"cliente",label:"Cliente",field:"cliente",sortable:!0,type:"string",align:"left"},{name:"folio",label:"Folio",field:"folio",sortable:!0,type:"string",align:"left"},{name:"folio_fiscal",label:"Folio fiscal",field:"folio_fiscal",sortable:!0,type:"string",align:"left"},{name:"status",label:"Status",field:"status",sortable:!0,type:"string",align:"left"},{name:"monto_factura",label:"Monto total",field:"monto_factura",sortable:!0,type:"string",align:"right"},{name:"amortizacion",label:"Amortizacion",field:"amortizacion",sortable:!0,type:"string",align:"left"},{name:"abonos",label:"Abonos",field:"abonos",sortable:!0,type:"string",align:"right"},{name:"falta_cobrar",label:"Falta cobrar",field:"falta_cobrar",sortable:!0,type:"string",align:"right"}],rowsOptions:[3,5,7,10,15,20,25,50],pagination:{rowsPerPage:50},selected:[],filter:"",loading:!1}}},mounted:function(){this.loadAll(),console.log(this.form.fields)},computed:{clientesOptions:function(){var t=this.$store.getters["ventas/clientes/selectOptions"];return t.splice(0,0,{label:"Todos",value:0}),t},empresasOptions:function(){var t=this.$store.getters["vnt/empresa/selectOptions"];return t.splice(0,0,{label:"Todos",value:0}),t}},methods:l()({},Object(c.b)({getClientes:"ventas/clientes/refresh",getEmpresas:"vnt/empresa/refresh"}),{loadAll:function(){var t=this;return n()(i.a.mark(function e(){return i.a.wrap(function(e){for(;;)switch(e.prev=e.next){case 0:return e.next=2,t.getClientes();case 2:return e.next=4,t.getEmpresas();case 4:return e.next=6,t.cargarDatosReporte();case 6:case"end":return e.stop()}},e,t)}))()},cargarDatosReporte:function(){var t=this;return n()(i.a.mark(function e(){var r,o,a,n,s;return i.a.wrap(function(e){for(;;)switch(e.prev=e.next){case 0:return r=t.form.fields.folio,""===t.form.fields.folio&&(r="$$$FOL$$$"),o=t.form.fields.ffiscal,""===t.form.fields.ffiscal&&(o="$$$FFIS$$$"),a=t.form.fields.remision,""===t.form.fields.remision&&(a="$$$DEL$$$"),n=t.form.fields.fecha_inicio,""===t.form.fields.fecha_inicio&&(n="$$$FINI$$$"),s=t.form.fields.fecha_fin,""===t.form.fields.fecha_fin&&(s="$$$FIN$$$"),e.next=12,p.a.get("/reportes/get_facturas/"+t.form.fields.cliente+"/"+t.form.fields.empresa+"/"+r+"/"+o+"/"+a+"/"+n+"/"+s).then(function(e){var r=e.data;t.form.data=r.facturas}).catch(function(t){console.error(t)});case 12:case"end":return e.stop()}},e,t)}))()},exportCSV:function(){var t=this.form.fields.fecha_fin,e=this.form.fields.folio;""===this.form.fields.folio&&(e="$$$FOL$$$");var r=this.form.fields.ffiscal;""===this.form.fields.ffiscal&&(r="$$$FFIS$$$");var o=this.form.fields.remision;""===this.form.fields.remision&&(o="$$$DEL$$$");var i=this.form.fields.fecha_inicio;""===this.form.fields.fecha_inicio&&(i="$$$FINI$$$");var a=t.slice(0,11)+"23:59:59";""===this.form.fields.fecha_fin&&(a="$$$FIN$$$");var n="http://api.impact.antfarm.mx/reportes/exportCSV_facturas/"+this.form.fields.cliente+"/"+this.form.fields.empresa+"/"+e+"/"+r+"/"+o+"/"+i+"/"+a;window.open(n,"_blank")},borrar:function(){this.form.fields.cliente=0,this.form.fields.empresa=0,this.form.fields.folio="",this.form.fields.ffiscal="",this.form.fields.remision="",this.form.fields.fecha_inicio="",this.form.fields.fecha_fin=""}})},u=function(){var t=this,e=t.$createElement,r=t._self._c||e;return r("q-page",[r("div",{staticClass:"q-pa-sm panel-header"},[r("div",{staticClass:"row"},[r("div",{staticClass:"col-sm-6"},[r("div",{staticClass:"q-pa-sm q-gutter-sm"},[r("q-breadcrumbs",{attrs:{align:"left"}},[r("q-breadcrumbs-el",{attrs:{label:"",icon:"home",to:"/dashboard"}}),t._v(" "),r("q-breadcrumbs-el",{staticClass:"page-title",attrs:{label:"Reporte de facturas",to:"/"}})],1)],1)]),t._v(" "),r("div",{staticClass:"col-sm-6 pull-right"},[r("div",{staticClass:"col-xs-12 col-sm-12 col-md-12 pull-right"},[r("q-btn",{staticStyle:{"margin-top":"5px"},attrs:{color:"green",icon:"fas fa-search",loading:t.loadingButton},on:{click:function(e){t.cargarDatosReporte()}}},[r("q-tooltip",[t._v("Buscar")])],1),t._v(" "),r("q-btn",{staticStyle:{"margin-top":"5px"},attrs:{color:"green",icon:"fas fa-file-excel"},on:{click:function(e){t.exportCSV()}}},[r("q-tooltip",[t._v("Generar CSV")])],1),t._v(" "),r("q-btn",{staticStyle:{"margin-top":"5px"},attrs:{color:"red",icon:"loop"},on:{click:function(e){t.borrar()}}},[r("q-tooltip",[t._v("Limpiar")])],1)],1)])])]),t._v(" "),r("div",{staticClass:"q-pa-md bg-grey-3"},[r("div",{staticClass:"row bg-white border-panel"},[r("div",{staticClass:"col q-pa-md"},[r("div",{staticClass:"row q-col-gutter-xs"},[r("div",{staticClass:"col-sm-3",attrs:{"icon-color":"dark"}},[r("q-field",{attrs:{icon:"person","icon-color":"dark"}},[r("q-select",{attrs:{inverted:"",color:"dark","float-label":"Cliente",options:t.clientesOptions,filter:""},model:{value:t.form.fields.cliente,callback:function(e){t.$set(t.form.fields,"cliente",e)},expression:"form.fields.cliente"}})],1)],1),t._v(" "),r("div",{staticClass:"col-sm-3"},[r("q-field",{attrs:{icon:"fas fa-building","icon-color":"dark"}},[r("q-select",{attrs:{inverted:"",color:"dark","float-label":"Empresa",options:t.empresasOptions,filter:""},model:{value:t.form.fields.empresa,callback:function(e){t.$set(t.form.fields,"empresa",e)},expression:"form.fields.empresa"}})],1)],1),t._v(" "),r("div",{staticClass:"col-sm-2"},[r("q-field",{attrs:{icon:"fas fa-folder-open","icon-color":"dark"}},[r("q-input",{attrs:{inverted:"",color:"dark","float-label":"Folio",filter:""},model:{value:t.form.fields.folio,callback:function(e){t.$set(t.form.fields,"folio",e)},expression:"form.fields.folio"}})],1)],1),t._v(" "),r("div",{staticClass:"col-sm-2"},[r("q-field",{attrs:{icon:"fas fa-folder","icon-color":"dark"}},[r("q-input",{attrs:{inverted:"",color:"dark","float-label":"Folio Fiscal",filter:""},model:{value:t.form.fields.ffiscal,callback:function(e){t.$set(t.form.fields,"ffiscal",e)},expression:"form.fields.ffiscal"}})],1)],1),t._v(" "),r("div",{staticClass:"col-sm-2"},[r("q-field",{attrs:{icon:"fas fa-clipboard","icon-color":"dark"}},[r("q-input",{attrs:{inverted:"",color:"dark","float-label":"Remisión",filter:""},model:{value:t.form.fields.remision,callback:function(e){t.$set(t.form.fields,"remision",e)},expression:"form.fields.remision"}})],1)],1),t._v(" "),r("div",{staticClass:"col-sm-3"},[r("q-field",{attrs:{icon:"date_range","icon-color":"dark"}},[r("q-datetime",{attrs:{mask:"YYYY/MM/DD",type:"date",inverted:"",color:"dark","float-label":"Fecha inicio",align:"center"},model:{value:t.form.fields.fecha_inicio,callback:function(e){t.$set(t.form.fields,"fecha_inicio",e)},expression:"form.fields.fecha_inicio"}})],1)],1),t._v(" "),r("div",{staticClass:"col-sm-3"},[r("q-field",{attrs:{icon:"date_range","icon-color":"dark"}},[r("q-datetime",{attrs:{mask:"YYYY/MM/DD",type:"date",inverted:"",color:"dark","float-label":"Fecha fin",align:"center"},model:{value:t.form.fields.fecha_fin,callback:function(e){t.$set(t.form.fields,"fecha_fin",e)},expression:"form.fields.fecha_fin"}})],1)],1)])])])]),t._v(" "),r("div",{staticClass:"q-pa-md bg-grey-3"},[r("div",{staticClass:"row bg-white border-panel"},[r("div",{staticClass:"col q-pa-md",staticStyle:{padding:"0"}},[r("div",{staticClass:"col q-pa-mdl"},[r("div",{staticClass:"col-sm-12",staticStyle:{"padding-bottom":"10px"}},[r("q-search",{attrs:{color:"primary"},model:{value:t.form.filter,callback:function(e){t.$set(t.form,"filter",e)},expression:"form.filter"}})],1),t._v(" "),r("div",{staticClass:"col-sm-12 q-mt-sm",attrs:{id:"sticky-table-scroll"}},[r("q-table",{attrs:{id:"sticky-table-newstyle",data:t.form.data,columns:t.form.columns,selected:t.form.selected,filter:t.form.filter,color:"positive",title:"",dense:!0,pagination:t.form.pagination,loading:t.form.loading,"rows-per-page-options":t.form.rowsOptions},on:{"update:selected":function(e){t.$set(t.form,"selected",e)},"update:pagination":function(e){t.$set(t.form,"pagination",e)}},scopedSlots:t._u([{key:"body",fn:function(e){return[r("q-tr",{attrs:{props:e}},[r("q-td",{key:"tipo",staticStyle:{"text-align":"left"},attrs:{props:e}},[t._v(t._s(e.row.tipo))]),t._v(" "),r("q-td",{key:"fecha_venta",attrs:{props:e}},[t._v(t._s(e.row.fecha_venta))]),t._v(" "),r("q-td",{key:"year",attrs:{props:e}},[t._v(t._s(e.row.year))]),t._v(" "),r("q-td",{key:"municipio",attrs:{props:e}},[t._v(t._s(e.row.municipio))]),t._v(" "),r("q-td",{key:"estado",attrs:{props:e}},[t._v(t._s(e.row.estado))]),t._v(" "),r("q-td",{key:"num_contrato",attrs:{props:e}},[t._v(t._s(e.row.num_contrato))]),t._v(" "),r("q-td",{key:"folio_remision",attrs:{props:e}},[t._v(t._s(e.row.folio_remision))]),t._v(" "),r("q-td",{key:"codigo",attrs:{props:e}},[t._v(t._s(e.row.codigo))]),t._v(" "),r("q-td",{key:"proyecto",attrs:{props:e}},[t._v(t._s(e.row.proyecto))]),t._v(" "),r("q-td",{key:"monto_contrato",attrs:{props:e}},[t._v("$"+t._s(e.row.monto_contrato))]),t._v(" "),r("q-td",{key:"empresa",attrs:{props:e}},[t._v(t._s(e.row.empresa))]),t._v(" "),r("q-td",{key:"cliente",attrs:{props:e}},[t._v(t._s(e.row.cliente))]),t._v(" "),r("q-td",{key:"folio",attrs:{props:e}},[t._v(t._s(e.row.folio))]),t._v(" "),r("q-td",{key:"folio_fiscal",attrs:{props:e}},[t._v(t._s(e.row.folio_fiscal))]),t._v(" "),r("q-td",{key:"status",attrs:{props:e}},[t._v(t._s(e.row.status))]),t._v(" "),r("q-td",{key:"monto_factura",attrs:{props:e}},[t._v("$"+t._s(e.row.monto_factura))]),t._v(" "),r("q-td",{key:"amortizacion",attrs:{props:e}},[t._v(t._s(e.row.amortizacion))]),t._v(" "),r("q-td",{key:"abonos",attrs:{props:e}},[t._v("$"+t._s(e.row.abonos))]),t._v(" "),r("q-td",{key:"falta_cobrar",attrs:{props:e}},[t._v("$"+t._s(e.row.falta_cobrar))])],1)]}}])}),t._v(" "),r("q-inner-loading",{attrs:{visible:t.form.loading}},[r("q-spinner-dots",{attrs:{size:"64px",color:"primary"}})],1)],1)])])])])])},m=[];u._withStripped=!0;var h=r("XyMi"),v=!1;var g=function(t){v||r("OW/K")},y=Object(h.a)(d,u,m,!1,g,"data-v-dff066d0",null);y.options.__file="src/pages/rpt/Facturas.vue";e.default=y.exports},Xxa5:function(t,e,r){t.exports=r("jyFz")},exGp:function(t,e,r){"use strict";e.__esModule=!0;var o,i=r("//Fk"),a=(o=i)&&o.__esModule?o:{default:o};e.default=function(t){return function(){var e=t.apply(this,arguments);return new a.default(function(t,r){return function o(i,n){try{var s=e[i](n),l=s.value}catch(t){return void r(t)}if(!s.done)return a.default.resolve(l).then(function(t){o("next",t)},function(t){o("throw",t)});t(l)}("next")})}}},jyFz:function(t,e,r){var o=function(){return this}()||Function("return this")(),i=o.regeneratorRuntime&&Object.getOwnPropertyNames(o).indexOf("regeneratorRuntime")>=0,a=i&&o.regeneratorRuntime;if(o.regeneratorRuntime=void 0,t.exports=r("SldL"),i)o.regeneratorRuntime=a;else try{delete o.regeneratorRuntime}catch(t){o.regeneratorRuntime=void 0}},mHnL:function(t,e,r){(t.exports=r("FZ+f")(!1)).push([t.i,"\n#sticky-table-scroll .q-table th[data-v-dff066d0]{\r\n  text-align: center;\n}\r\n",""])}});