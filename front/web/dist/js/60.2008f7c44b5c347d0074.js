webpackJsonp([60],{"+lKe":function(t,e,r){var o=r("dYhC");"string"==typeof o&&(o=[[t.i,o,""]]),o.locals&&(t.exports=o.locals);(0,r("rjj0").default)("abb103be",o,!1,{})},"5dBV":function(t,e,r){t.exports={default:r("quu5"),__esModule:!0}},CHlY:function(t,e,r){var o=r("kM2E"),n=r("bs6G");o(o.S+o.F*(Number.parseFloat!=n),"Number",{parseFloat:n})},SldL:function(t,e){!function(e){"use strict";var r,o=Object.prototype,n=o.hasOwnProperty,i="function"==typeof Symbol?Symbol:{},a=i.iterator||"@@iterator",s=i.asyncIterator||"@@asyncIterator",c=i.toStringTag||"@@toStringTag",l="object"==typeof t,u=e.regeneratorRuntime;if(u)l&&(t.exports=u);else{(u=e.regeneratorRuntime=l?t.exports:{}).wrap=_;var p="suspendedStart",f="suspendedYield",d="executing",h="completed",m={},v={};v[a]=function(){return this};var g=Object.getPrototypeOf,y=g&&g(g(U([])));y&&y!==o&&n.call(y,a)&&(v=y);var b=O.prototype=w.prototype=Object.create(v);C.prototype=b.constructor=O,O.constructor=C,O[c]=C.displayName="GeneratorFunction",u.isGeneratorFunction=function(t){var e="function"==typeof t&&t.constructor;return!!e&&(e===C||"GeneratorFunction"===(e.displayName||e.name))},u.mark=function(t){return Object.setPrototypeOf?Object.setPrototypeOf(t,O):(t.__proto__=O,c in t||(t[c]="GeneratorFunction")),t.prototype=Object.create(b),t},u.awrap=function(t){return{__await:t}},E(k.prototype),k.prototype[s]=function(){return this},u.AsyncIterator=k,u.async=function(t,e,r,o){var n=new k(_(t,e,r,o));return u.isGeneratorFunction(e)?n:n.next().then(function(t){return t.done?t.value:n.next()})},E(b),b[c]="Generator",b[a]=function(){return this},b.toString=function(){return"[object Generator]"},u.keys=function(t){var e=[];for(var r in t)e.push(r);return e.reverse(),function r(){for(;e.length;){var o=e.pop();if(o in t)return r.value=o,r.done=!1,r}return r.done=!0,r}},u.values=U,j.prototype={constructor:j,reset:function(t){if(this.prev=0,this.next=0,this.sent=this._sent=r,this.done=!1,this.delegate=null,this.method="next",this.arg=r,this.tryEntries.forEach(S),!t)for(var e in this)"t"===e.charAt(0)&&n.call(this,e)&&!isNaN(+e.slice(1))&&(this[e]=r)},stop:function(){this.done=!0;var t=this.tryEntries[0].completion;if("throw"===t.type)throw t.arg;return this.rval},dispatchException:function(t){if(this.done)throw t;var e=this;function o(o,n){return s.type="throw",s.arg=t,e.next=o,n&&(e.method="next",e.arg=r),!!n}for(var i=this.tryEntries.length-1;i>=0;--i){var a=this.tryEntries[i],s=a.completion;if("root"===a.tryLoc)return o("end");if(a.tryLoc<=this.prev){var c=n.call(a,"catchLoc"),l=n.call(a,"finallyLoc");if(c&&l){if(this.prev<a.catchLoc)return o(a.catchLoc,!0);if(this.prev<a.finallyLoc)return o(a.finallyLoc)}else if(c){if(this.prev<a.catchLoc)return o(a.catchLoc,!0)}else{if(!l)throw new Error("try statement without catch or finally");if(this.prev<a.finallyLoc)return o(a.finallyLoc)}}}},abrupt:function(t,e){for(var r=this.tryEntries.length-1;r>=0;--r){var o=this.tryEntries[r];if(o.tryLoc<=this.prev&&n.call(o,"finallyLoc")&&this.prev<o.finallyLoc){var i=o;break}}i&&("break"===t||"continue"===t)&&i.tryLoc<=e&&e<=i.finallyLoc&&(i=null);var a=i?i.completion:{};return a.type=t,a.arg=e,i?(this.method="next",this.next=i.finallyLoc,m):this.complete(a)},complete:function(t,e){if("throw"===t.type)throw t.arg;return"break"===t.type||"continue"===t.type?this.next=t.arg:"return"===t.type?(this.rval=this.arg=t.arg,this.method="return",this.next="end"):"normal"===t.type&&e&&(this.next=e),m},finish:function(t){for(var e=this.tryEntries.length-1;e>=0;--e){var r=this.tryEntries[e];if(r.finallyLoc===t)return this.complete(r.completion,r.afterLoc),S(r),m}},catch:function(t){for(var e=this.tryEntries.length-1;e>=0;--e){var r=this.tryEntries[e];if(r.tryLoc===t){var o=r.completion;if("throw"===o.type){var n=o.arg;S(r)}return n}}throw new Error("illegal catch attempt")},delegateYield:function(t,e,o){return this.delegate={iterator:U(t),resultName:e,nextLoc:o},"next"===this.method&&(this.arg=r),m}}}function _(t,e,r,o){var n=e&&e.prototype instanceof w?e:w,i=Object.create(n.prototype),a=new j(o||[]);return i._invoke=function(t,e,r){var o=p;return function(n,i){if(o===d)throw new Error("Generator is already running");if(o===h){if("throw"===n)throw i;return R()}for(r.method=n,r.arg=i;;){var a=r.delegate;if(a){var s=q(a,r);if(s){if(s===m)continue;return s}}if("next"===r.method)r.sent=r._sent=r.arg;else if("throw"===r.method){if(o===p)throw o=h,r.arg;r.dispatchException(r.arg)}else"return"===r.method&&r.abrupt("return",r.arg);o=d;var c=x(t,e,r);if("normal"===c.type){if(o=r.done?h:f,c.arg===m)continue;return{value:c.arg,done:r.done}}"throw"===c.type&&(o=h,r.method="throw",r.arg=c.arg)}}}(t,r,a),i}function x(t,e,r){try{return{type:"normal",arg:t.call(e,r)}}catch(t){return{type:"throw",arg:t}}}function w(){}function C(){}function O(){}function E(t){["next","throw","return"].forEach(function(e){t[e]=function(t){return this._invoke(e,t)}})}function k(t){var e;this._invoke=function(r,o){function i(){return new Promise(function(e,i){!function e(r,o,i,a){var s=x(t[r],t,o);if("throw"!==s.type){var c=s.arg,l=c.value;return l&&"object"==typeof l&&n.call(l,"__await")?Promise.resolve(l.__await).then(function(t){e("next",t,i,a)},function(t){e("throw",t,i,a)}):Promise.resolve(l).then(function(t){c.value=t,i(c)},a)}a(s.arg)}(r,o,e,i)})}return e=e?e.then(i,i):i()}}function q(t,e){var o=t.iterator[e.method];if(o===r){if(e.delegate=null,"throw"===e.method){if(t.iterator.return&&(e.method="return",e.arg=r,q(t,e),"throw"===e.method))return m;e.method="throw",e.arg=new TypeError("The iterator does not provide a 'throw' method")}return m}var n=x(o,t.iterator,e.arg);if("throw"===n.type)return e.method="throw",e.arg=n.arg,e.delegate=null,m;var i=n.arg;return i?i.done?(e[t.resultName]=i.value,e.next=t.nextLoc,"return"!==e.method&&(e.method="next",e.arg=r),e.delegate=null,m):i:(e.method="throw",e.arg=new TypeError("iterator result is not an object"),e.delegate=null,m)}function L(t){var e={tryLoc:t[0]};1 in t&&(e.catchLoc=t[1]),2 in t&&(e.finallyLoc=t[2],e.afterLoc=t[3]),this.tryEntries.push(e)}function S(t){var e=t.completion||{};e.type="normal",delete e.arg,t.completion=e}function j(t){this.tryEntries=[{tryLoc:"root"}],t.forEach(L,this),this.reset(!0)}function U(t){if(t){var e=t[a];if(e)return e.call(t);if("function"==typeof t.next)return t;if(!isNaN(t.length)){var o=-1,i=function e(){for(;++o<t.length;)if(n.call(t,o))return e.value=t[o],e.done=!1,e;return e.value=r,e.done=!0,e};return i.next=i}}return{next:R}}function R(){return{value:r,done:!0}}}(function(){return this}()||Function("return this")())},Xxa5:function(t,e,r){t.exports=r("jyFz")},bQiY:function(t,e,r){"use strict";Object.defineProperty(e,"__esModule",{value:!0});var o=r("5dBV"),n=r.n(o),i=r("Xxa5"),a=r.n(i),s=r("exGp"),c=r.n(s),l=r("Dd8w"),u=r.n(l),p=r("NYxO"),f=r("mtWM"),d=r.n(f),h={components:{},beforeRouteEnter:function(t,e,r){r(function(t){for(var o=!1,n=t.$store.getters["user/role"],i=0;i<n.length;i++)n[i].toUpperCase()!=="admin".toUpperCase()&&n[i].toUpperCase()!=="ROOT".toUpperCase()&&n[i].toUpperCase()!=="CLIENTE".toUpperCase()&&n[i].toUpperCase()!=="LIDER".toUpperCase()&&n[i].toUpperCase()!=="COORDINADOR".toUpperCase()&&n[i].toUpperCase()!=="GERENTE DE OPERACIONES".toUpperCase()&&n[i].toUpperCase()!=="PMO".toUpperCase()&&n[i].toUpperCase()!=="FINANZAS".toUpperCase()&&n[i].toUpperCase()!=="OPERACIÓN".toUpperCase()&&n[i].toUpperCase()!=="LICITACIONES".toUpperCase()&&n[i].toUpperCase()!=="GERENTE".toUpperCase()||(o=!0);o?r():r("/"===e.path?"/dashboard":e.path)})},data:function(){return{reportes:[],municipiosOptions:[{label:"---Seleccione---",value:0}],tiposOptions:[{label:"Projects con cantidad ejercida en actividades no afectables",value:0},{label:"Projects con actividades que tengan cantidad ejercida en negativo",value:1}],views:{grid:!0,create:!1,update:!1},form:{data:[],fields:{user_id:0,lider_id:0,empresa_id:0,estado_id:0,municipio_id:0,tipo:0},columns:[{name:"codigo",label:"Código",field:"codigo",sortable:!0,type:"string",align:"left"},{name:"proyecto",label:"Proyecto",field:"proyecto",sortable:!0,type:"string",align:"left"},{name:"project",label:"Project",field:"project",sortable:!0,type:"string",align:"left"},{name:"estado",label:"Estado",field:"estado",sortable:!0,type:"string",align:"left"},{name:"municipio",label:"Municipio",field:"municipio",sortable:!0,type:"string",align:"left"}],rowsOptions:[3,5,7,10,15,20,25,50],pagination:{rowsPerPage:50},selected:[],filter:"",loading:!1}}},mounted:function(){this.loadAll()},computed:u()({},Object(p.c)({proyectosOptions:"pmo/proyecto/selectOptions",empresasOptions:"vnt/empresa/selectOptions",proveedoresOptions:"pmo/proveedor/selectOptions"}),{estadosOptions:function(){var t=this.$store.getters["vnt/estado/selectOptions"];return t.push({label:"---Seleccione---",value:0}),t.sort(function(t,e){return t.nombre>e.nombre?1:t.nombre<e.nombre?-1:0}),t},proyectosOptions2:function(){var t=this.$store.getters["pmo/proyecto/selectOptions"];return t.push({label:"Todos",value:0}),t},empresasOptions2:function(){var t=this.$store.getters["vnt/empresa/selectOptions"];return t.push({label:"Todos",value:0}),t},usuariosOptions:function(){var t=this.$store.getters["sys/users/selectOptionsName"];return t.push({label:"Todos",value:0}),t}}),methods:u()({},Object(p.b)({getUser:"user/refresh",getEstados:"vnt/estado/refresh",getUsers:"sys/users/refresh",getEmpresas:"vnt/empresa/refresh",getMunicipiosByEstado:"vnt/municipio/getByEstado"}),{loadAll:function(){var t=this;return c()(a.a.mark(function e(){return a.a.wrap(function(e){for(;;)switch(e.prev=e.next){case 0:return t.form.loading=!0,e.next=3,t.isAdmin();case 3:if(43!==t.form.fields.user_id&&1!==t.form.fields.user_id){e.next=12;break}return e.next=6,t.cargarDatosReporte();case 6:return e.next=8,t.getUsers();case 8:return e.next=10,t.getEmpresas();case 10:return e.next=12,t.getEstados();case 12:t.form.loading=!1;case 13:case"end":return e.stop()}},e,t)}))()},isAdmin:function(){var t=this;return c()(a.a.mark(function e(){return a.a.wrap(function(e){for(;;)switch(e.prev=e.next){case 0:return e.next=2,t.getUser().then(function(e){var r=e.data;t.form.fields.user_id=r.user.id}).catch(function(t){console.log(t)});case 2:case"end":return e.stop()}},e,t)}))()},cargarDatosReporte:function(){var t=this;return c()(a.a.mark(function e(){return a.a.wrap(function(e){for(;;)switch(e.prev=e.next){case 0:return t.form.loading=!0,t.form.data=[],e.next=4,d.a.get("/reportes/getReporteAuditoria/"+t.form.fields.estado_id+"/"+t.form.fields.municipio_id+"/"+t.form.fields.tipo).then(function(e){var r=e.data;t.form.data=r.auditoria}).catch(function(t){console.error(t)});case 4:t.form.loading=!1;case 5:case"end":return e.stop()}},e,t)}))()},exportCSV:function(){var t="http://api.impact.beta.wasp.mx/reportes/exportCSV_reporteAuditoria/"+this.form.fields.estado_id+"/"+this.form.fields.municipio_id+"/"+this.form.fields.tipo;window.open(t,"_blank")},borrar:function(){this.form.fields.empresa_id=0,this.form.fields.lider_id=0,this.form.fields.estado_id=0,this.form.fields.municipio_id=0},cargaMunicipios:function(){var t=this;return c()(a.a.mark(function e(){return a.a.wrap(function(e){for(;;)switch(e.prev=e.next){case 0:return t.municipiosOptions=[],t.form.fields.municipio_id=0,e.next=4,t.getMunicipiosByEstado({estado_id:t.form.fields.estado_id}).then(function(e){var r=e.data;t.municipiosOptions.push({label:"---Seleccione---",value:0}),r.municipios.length>0&&r.municipios.forEach(function(e){t.municipiosOptions.push({label:e.nombre,value:e.id})})}).catch(function(t){console.error(t)});case 4:case"end":return e.stop()}},e,t)}))()},currencyFormat:function(t){return n()(t).toFixed(2).replace(/(\d)(?=(\d{3})+(?!\d))/g,"$1,")}})},m=function(){var t=this,e=t.$createElement,r=t._self._c||e;return r("q-page",[r("div",{staticClass:"q-pa-sm panel-header"},[r("div",{staticClass:"row"},[r("div",{staticClass:"col-sm-6"},[r("div",{staticClass:"q-pa-sm q-gutter-sm"},[r("q-breadcrumbs",{attrs:{align:"left"}},[r("q-breadcrumbs-el",{attrs:{label:"",icon:"home",to:"/dashboard"}}),t._v(" "),r("q-breadcrumbs-el",{staticClass:"page-title",attrs:{label:"Auditoría de projects",to:"/"}})],1)],1)]),t._v(" "),r("div",{staticClass:"col-sm-6 pull-right"},[r("div",{staticClass:"col-xs-12 col-sm-12 col-md-12 pull-right"},[r("q-btn",{staticStyle:{"margin-top":"5px"},attrs:{color:"green",icon:"fas fa-search",loading:t.loadingButton},on:{click:function(e){t.cargarDatosReporte()}}},[r("q-tooltip",[t._v("Buscar")])],1),t._v(" "),r("q-btn",{staticStyle:{"margin-top":"5px"},attrs:{color:"green",icon:"fas fa-file-excel"},on:{click:function(e){t.exportCSV()}}},[r("q-tooltip",[t._v("Generar CSV")])],1),t._v(" "),r("q-btn",{staticStyle:{"margin-top":"5px"},attrs:{color:"red",icon:"loop"},on:{click:function(e){t.borrar()}}},[r("q-tooltip",[t._v("Limpiar")])],1)],1)])])]),t._v(" "),r("div",{staticClass:"q-pa-md bg-grey-3"},[r("div",{staticClass:"row bg-white border-panel"},[r("div",{staticClass:"col q-pa-md"},[r("div",{staticClass:"row q-col-gutter-xs"},[r("div",{staticClass:"col-sm-3",attrs:{"icon-color":"dark"}},[r("q-field",{attrs:{icon:"label","icon-color":"dark"}},[r("q-select",{attrs:{inverted:"",color:"dark","float-label":"Tipo",options:t.tiposOptions,filter:""},model:{value:t.form.fields.tipo,callback:function(e){t.$set(t.form.fields,"tipo",e)},expression:"form.fields.tipo"}})],1)],1),t._v(" "),r("div",{staticClass:"col-sm-3"},[r("q-field",{attrs:{icon:"fas fa-globe","icon-color":"dark"}},[r("q-select",{attrs:{inverted:"",color:"dark","float-label":"Estado",options:t.estadosOptions,filter:""},on:{input:function(e){t.cargaMunicipios()}},model:{value:t.form.fields.estado_id,callback:function(e){t.$set(t.form.fields,"estado_id",e)},expression:"form.fields.estado_id"}})],1)],1),t._v(" "),r("div",{staticClass:"col-sm-3"},[r("q-field",{attrs:{icon:"fas fa-map-pin","icon-color":"dark"}},[r("q-select",{attrs:{inverted:"",color:"dark","float-label":"Municipio",options:t.municipiosOptions,filter:""},model:{value:t.form.fields.municipio_id,callback:function(e){t.$set(t.form.fields,"municipio_id",e)},expression:"form.fields.municipio_id"}})],1)],1)])])])]),t._v(" "),r("div",{staticClass:"q-pa-md bg-grey-3"},[r("div",{staticClass:"row bg-white border-panel"},[r("div",{staticClass:"col q-pa-md",staticStyle:{padding:"0"}},[r("div",{staticClass:"col q-pa-md"},[r("div",{staticClass:"col-sm-12",staticStyle:{"padding-bottom":"10px"}},[r("q-search",{attrs:{color:"primary"},model:{value:t.form.filter,callback:function(e){t.$set(t.form,"filter",e)},expression:"form.filter"}})],1),t._v(" "),r("div",{staticClass:"col-sm-12 q-mt-sm",attrs:{id:"sticky-table-scroll"}},[r("q-table",{attrs:{id:"sticky-table-newstyle",data:t.form.data,columns:t.form.columns,selected:t.form.selected,filter:t.form.filter,color:"positive",title:"",dense:!0,pagination:t.form.pagination,loading:t.form.loading,"rows-per-page-options":t.form.rowsOptions},on:{"update:selected":function(e){t.$set(t.form,"selected",e)},"update:pagination":function(e){t.$set(t.form,"pagination",e)}},scopedSlots:t._u([{key:"body",fn:function(e){return[r("q-tr",{attrs:{props:e}},[r("q-td",{key:"codigo",staticStyle:{"text-align":"left"},attrs:{props:e}},[t._v(t._s(e.row.codigo))]),t._v(" "),r("q-td",{key:"proyecto",staticStyle:{"text-align":"left"},attrs:{props:e}},[t._v(t._s(e.row.proyecto))]),t._v(" "),r("q-td",{key:"project",staticStyle:{"text-align":"left"},attrs:{props:e}},[t._v(t._s(e.row.project))]),t._v(" "),r("q-td",{key:"estado",attrs:{props:e}},[t._v(t._s(e.row.estado))]),t._v(" "),r("q-td",{key:"municipio",attrs:{props:e}},[t._v(t._s(e.row.municipio))])],1)]}}])}),t._v(" "),r("q-inner-loading",{attrs:{visible:t.form.loading}},[r("q-spinner-dots",{attrs:{size:"64px",color:"primary"}})],1)],1)])])])])])},v=[];m._withStripped=!0;var g=r("XyMi"),y=!1;var b=function(t){y||r("+lKe")},_=Object(g.a)(h,m,v,!1,b,"data-v-a2319a3c",null);_.options.__file="src\\pages\\rpt\\Auditoria.vue";e.default=_.exports},bs6G:function(t,e,r){var o=r("7KvD").parseFloat,n=r("mnVu").trim;t.exports=1/o(r("hyta")+"-0")!=-1/0?function(t){var e=n(String(t),3),r=o(e);return 0===r&&"-"==e.charAt(0)?-0:r}:o},dYhC:function(t,e,r){(t.exports=r("FZ+f")(!1)).push([t.i,"\n#sticky-table-scroll .q-table th[data-v-a2319a3c]{\r\n  text-align: center;\n}\r\n",""])},exGp:function(t,e,r){"use strict";e.__esModule=!0;var o,n=r("//Fk"),i=(o=n)&&o.__esModule?o:{default:o};e.default=function(t){return function(){var e=t.apply(this,arguments);return new i.default(function(t,r){return function o(n,a){try{var s=e[n](a),c=s.value}catch(t){return void r(t)}if(!s.done)return i.default.resolve(c).then(function(t){o("next",t)},function(t){o("throw",t)});t(c)}("next")})}}},hyta:function(t,e){t.exports="\t\n\v\f\r   ᠎             　\u2028\u2029\ufeff"},jyFz:function(t,e,r){var o=function(){return this}()||Function("return this")(),n=o.regeneratorRuntime&&Object.getOwnPropertyNames(o).indexOf("regeneratorRuntime")>=0,i=n&&o.regeneratorRuntime;if(o.regeneratorRuntime=void 0,t.exports=r("SldL"),n)o.regeneratorRuntime=i;else try{delete o.regeneratorRuntime}catch(t){o.regeneratorRuntime=void 0}},mnVu:function(t,e,r){var o=r("kM2E"),n=r("52gC"),i=r("S82l"),a=r("hyta"),s="["+a+"]",c=RegExp("^"+s+s+"*"),l=RegExp(s+s+"*$"),u=function(t,e,r){var n={},s=i(function(){return!!a[t]()||"​"!="​"[t]()}),c=n[t]=s?e(p):a[t];r&&(n[r]=c),o(o.P+o.F*s,"String",n)},p=u.trim=function(t,e){return t=String(n(t)),1&e&&(t=t.replace(c,"")),2&e&&(t=t.replace(l,"")),t};t.exports=u},quu5:function(t,e,r){r("CHlY"),t.exports=r("FeBl").Number.parseFloat}});