webpackJsonp([68],{G2GB:function(t,e,r){(t.exports=r("FZ+f")(!1)).push([t.i,"\n#sticky-table-scroll .q-table th[data-v-d44c3c2a]{\r\n  text-align: center;\n}\r\n",""])},SldL:function(t,e){!function(e){"use strict";var r,o=Object.prototype,n=o.hasOwnProperty,a="function"==typeof Symbol?Symbol:{},i=a.iterator||"@@iterator",s=a.asyncIterator||"@@asyncIterator",c=a.toStringTag||"@@toStringTag",l="object"==typeof t,f=e.regeneratorRuntime;if(f)l&&(t.exports=f);else{(f=e.regeneratorRuntime=l?t.exports:{}).wrap=b;var u="suspendedStart",p="suspendedYield",d="executing",h="completed",m={},v={};v[i]=function(){return this};var g=Object.getPrototypeOf,y=g&&g(g(P([])));y&&y!==o&&n.call(y,i)&&(v=y);var _=E.prototype=x.prototype=Object.create(v);C.prototype=_.constructor=E,E.constructor=C,E[c]=C.displayName="GeneratorFunction",f.isGeneratorFunction=function(t){var e="function"==typeof t&&t.constructor;return!!e&&(e===C||"GeneratorFunction"===(e.displayName||e.name))},f.mark=function(t){return Object.setPrototypeOf?Object.setPrototypeOf(t,E):(t.__proto__=E,c in t||(t[c]="GeneratorFunction")),t.prototype=Object.create(_),t},f.awrap=function(t){return{__await:t}},q(O.prototype),O.prototype[s]=function(){return this},f.AsyncIterator=O,f.async=function(t,e,r,o){var n=new O(b(t,e,r,o));return f.isGeneratorFunction(e)?n:n.next().then(function(t){return t.done?t.value:n.next()})},q(_),_[c]="Generator",_[i]=function(){return this},_.toString=function(){return"[object Generator]"},f.keys=function(t){var e=[];for(var r in t)e.push(r);return e.reverse(),function r(){for(;e.length;){var o=e.pop();if(o in t)return r.value=o,r.done=!1,r}return r.done=!0,r}},f.values=P,U.prototype={constructor:U,reset:function(t){if(this.prev=0,this.next=0,this.sent=this._sent=r,this.done=!1,this.delegate=null,this.method="next",this.arg=r,this.tryEntries.forEach(R),!t)for(var e in this)"t"===e.charAt(0)&&n.call(this,e)&&!isNaN(+e.slice(1))&&(this[e]=r)},stop:function(){this.done=!0;var t=this.tryEntries[0].completion;if("throw"===t.type)throw t.arg;return this.rval},dispatchException:function(t){if(this.done)throw t;var e=this;function o(o,n){return s.type="throw",s.arg=t,e.next=o,n&&(e.method="next",e.arg=r),!!n}for(var a=this.tryEntries.length-1;a>=0;--a){var i=this.tryEntries[a],s=i.completion;if("root"===i.tryLoc)return o("end");if(i.tryLoc<=this.prev){var c=n.call(i,"catchLoc"),l=n.call(i,"finallyLoc");if(c&&l){if(this.prev<i.catchLoc)return o(i.catchLoc,!0);if(this.prev<i.finallyLoc)return o(i.finallyLoc)}else if(c){if(this.prev<i.catchLoc)return o(i.catchLoc,!0)}else{if(!l)throw new Error("try statement without catch or finally");if(this.prev<i.finallyLoc)return o(i.finallyLoc)}}}},abrupt:function(t,e){for(var r=this.tryEntries.length-1;r>=0;--r){var o=this.tryEntries[r];if(o.tryLoc<=this.prev&&n.call(o,"finallyLoc")&&this.prev<o.finallyLoc){var a=o;break}}a&&("break"===t||"continue"===t)&&a.tryLoc<=e&&e<=a.finallyLoc&&(a=null);var i=a?a.completion:{};return i.type=t,i.arg=e,a?(this.method="next",this.next=a.finallyLoc,m):this.complete(i)},complete:function(t,e){if("throw"===t.type)throw t.arg;return"break"===t.type||"continue"===t.type?this.next=t.arg:"return"===t.type?(this.rval=this.arg=t.arg,this.method="return",this.next="end"):"normal"===t.type&&e&&(this.next=e),m},finish:function(t){for(var e=this.tryEntries.length-1;e>=0;--e){var r=this.tryEntries[e];if(r.finallyLoc===t)return this.complete(r.completion,r.afterLoc),R(r),m}},catch:function(t){for(var e=this.tryEntries.length-1;e>=0;--e){var r=this.tryEntries[e];if(r.tryLoc===t){var o=r.completion;if("throw"===o.type){var n=o.arg;R(r)}return n}}throw new Error("illegal catch attempt")},delegateYield:function(t,e,o){return this.delegate={iterator:P(t),resultName:e,nextLoc:o},"next"===this.method&&(this.arg=r),m}}}function b(t,e,r,o){var n=e&&e.prototype instanceof x?e:x,a=Object.create(n.prototype),i=new U(o||[]);return a._invoke=function(t,e,r){var o=u;return function(n,a){if(o===d)throw new Error("Generator is already running");if(o===h){if("throw"===n)throw a;return S()}for(r.method=n,r.arg=a;;){var i=r.delegate;if(i){var s=k(i,r);if(s){if(s===m)continue;return s}}if("next"===r.method)r.sent=r._sent=r.arg;else if("throw"===r.method){if(o===u)throw o=h,r.arg;r.dispatchException(r.arg)}else"return"===r.method&&r.abrupt("return",r.arg);o=d;var c=w(t,e,r);if("normal"===c.type){if(o=r.done?h:p,c.arg===m)continue;return{value:c.arg,done:r.done}}"throw"===c.type&&(o=h,r.method="throw",r.arg=c.arg)}}}(t,r,i),a}function w(t,e,r){try{return{type:"normal",arg:t.call(e,r)}}catch(t){return{type:"throw",arg:t}}}function x(){}function C(){}function E(){}function q(t){["next","throw","return"].forEach(function(e){t[e]=function(t){return this._invoke(e,t)}})}function O(t){var e;this._invoke=function(r,o){function a(){return new Promise(function(e,a){!function e(r,o,a,i){var s=w(t[r],t,o);if("throw"!==s.type){var c=s.arg,l=c.value;return l&&"object"==typeof l&&n.call(l,"__await")?Promise.resolve(l.__await).then(function(t){e("next",t,a,i)},function(t){e("throw",t,a,i)}):Promise.resolve(l).then(function(t){c.value=t,a(c)},i)}i(s.arg)}(r,o,e,a)})}return e=e?e.then(a,a):a()}}function k(t,e){var o=t.iterator[e.method];if(o===r){if(e.delegate=null,"throw"===e.method){if(t.iterator.return&&(e.method="return",e.arg=r,k(t,e),"throw"===e.method))return m;e.method="throw",e.arg=new TypeError("The iterator does not provide a 'throw' method")}return m}var n=w(o,t.iterator,e.arg);if("throw"===n.type)return e.method="throw",e.arg=n.arg,e.delegate=null,m;var a=n.arg;return a?a.done?(e[t.resultName]=a.value,e.next=t.nextLoc,"return"!==e.method&&(e.method="next",e.arg=r),e.delegate=null,m):a:(e.method="throw",e.arg=new TypeError("iterator result is not an object"),e.delegate=null,m)}function L(t){var e={tryLoc:t[0]};1 in t&&(e.catchLoc=t[1]),2 in t&&(e.finallyLoc=t[2],e.afterLoc=t[3]),this.tryEntries.push(e)}function R(t){var e=t.completion||{};e.type="normal",delete e.arg,t.completion=e}function U(t){this.tryEntries=[{tryLoc:"root"}],t.forEach(L,this),this.reset(!0)}function P(t){if(t){var e=t[i];if(e)return e.call(t);if("function"==typeof t.next)return t;if(!isNaN(t.length)){var o=-1,a=function e(){for(;++o<t.length;)if(n.call(t,o))return e.value=t[o],e.done=!1,e;return e.value=r,e.done=!0,e};return a.next=a}}return{next:S}}function S(){return{value:r,done:!0}}}(function(){return this}()||Function("return this")())},Xxa5:function(t,e,r){t.exports=r("jyFz")},bc3q:function(t,e,r){"use strict";Object.defineProperty(e,"__esModule",{value:!0});var o=r("Xxa5"),n=r.n(o),a=r("exGp"),i=r.n(a),s=r("Dd8w"),c=r.n(s),l=r("NYxO"),f=r("mtWM"),u=r.n(f),p={components:{},beforeRouteEnter:function(t,e,r){r(function(t){for(var o=!1,n=t.$store.getters["user/role"],a=0;a<n.length;a++)n[a].toUpperCase()!=="admin".toUpperCase()&&n[a].toUpperCase()!=="ROOT".toUpperCase()&&n[a].toUpperCase()!=="CLIENTE".toUpperCase()&&n[a].toUpperCase()!=="LIDER".toUpperCase()&&n[a].toUpperCase()!=="COORDINADOR".toUpperCase()&&n[a].toUpperCase()!=="GERENTE DE OPERACIONES".toUpperCase()&&n[a].toUpperCase()!=="PMO".toUpperCase()&&n[a].toUpperCase()!=="FINANZAS".toUpperCase()&&n[a].toUpperCase()!=="OPERACIÓN".toUpperCase()&&n[a].toUpperCase()!=="LICITACIONES".toUpperCase()&&n[a].toUpperCase()!=="COBRANZA".toUpperCase()&&n[a].toUpperCase()!=="INVENTARIOS".toUpperCase()||(o=!0);o?r():r("/"===e.path?"/dashboard":e.path)})},data:function(){return{reportes:[],views:{grid:!0,create:!1,update:!1,grid_direcciones:!1,create_direccion:!1,update_direccion:!1},form:{data:[],fields:{almacen:0,producto:0,fecha_inicio:null,fecha_fin:null},columns:[{name:"almacen",label:"Almacen",field:"almacen",sortable:!0,type:"string",align:"left"},{name:"categoria",label:"Categoría",field:"categoria",sortable:!0,type:"string",align:"left"},{name:"linea",label:"Línea",field:"linea",sortable:!0,type:"string",align:"left"},{name:"presentacion",label:"Presentación",field:"presentacion",sortable:!0,type:"string",align:"left"},{name:"codigo_categoria",label:"Código",field:"codigo_categoria",sortable:!0,type:"string",align:"left"},{name:"nombre",label:"Producto",field:"nombre",sortable:!0,type:"string",align:"left"},{name:"existencia",label:"Existencias",field:"existencia",sortable:!0,type:"string",align:"right"}],rowsOptions:[3,5,7,10,15,20,25,50],pagination:{rowsPerPage:50},selected:[],filter:"",loading:!1}}},mounted:function(){this.loadAll()},computed:{almacenesOptions:function(){var t=this.$store.getters["inv/almacenes/selectOptions"];return t.push({label:"Todos",value:0}),t},productosOptions:function(){var t=this.$store.getters["inv/productos/selectOptions"];return t.push({label:"Todos",value:0}),t}},methods:c()({},Object(l.b)({getAlmacenes:"inv/almacenes/refresh",getProductos:"inv/productos/refresh"}),{loadAll:function(){var t=this;return i()(n.a.mark(function e(){return n.a.wrap(function(e){for(;;)switch(e.prev=e.next){case 0:return e.next=2,t.getAlmacenes();case 2:return e.next=4,t.getProductos();case 4:return e.next=6,t.cargarDatosReporte();case 6:case"end":return e.stop()}},e,t)}))()},cargarDatosReporte:function(){var t=this;return i()(n.a.mark(function e(){return n.a.wrap(function(e){for(;;)switch(e.prev=e.next){case 0:return t.form.data=[],0===t.form.fields.almacen?t.almacen=null:t.almacen=t.form.fields.almacen,0===t.form.fields.producto?t.producto=null:t.producto=t.form.fields.producto,e.next=5,u.a.get("/reportes/existencias_productos/"+t.almacen+"/"+t.producto+"/"+t.form.fields.fecha_inicio+"/"+t.form.fields.fecha_fin).then(function(e){var r=e.data;t.form.data=r.existencias}).catch(function(t){console.error(t)});case 5:case"end":return e.stop()}},e,t)}))()},generarCsvExistencias:function(){0===this.form.fields.almacen&&(this.almacen=null),0===this.form.fields.producto&&(this.producto=null);var t="http://api.impact.beta.wasp.mx//reportes/existencias_productos_csv/"+this.almacen+"/"+this.producto+"/"+this.form.fields.fecha_inicio+"/"+this.form.fields.fecha_fin;window.open(t,"_blank")},borrar:function(){this.form.fields.almacen=0,this.form.fields.producto=0,this.form.fields.fecha_inicio=null,this.form.fields.fecha_fin=null},generarPdfExistencias:function(){this.form.data=[],0===this.form.fields.almacen?this.almacen=null:this.almacen=this.form.fields.almacen,0===this.form.fields.producto?this.producto=null:this.producto=this.form.fields.producto;var t="http://api.impact.beta.wasp.mx//reportes/existencias_productos_pdf/"+this.almacen+"/"+this.producto+"/"+this.form.fields.fecha_inicio+"/"+this.form.fields.fecha_fin;window.open(t,"_blank")}})},d=function(){var t=this,e=t.$createElement,r=t._self._c||e;return r("q-page",[r("div",{staticClass:"q-pa-sm panel-header"},[r("div",{staticClass:"row"},[r("div",{staticClass:"col-sm-6"},[r("div",{staticClass:"q-pa-sm q-gutter-sm"},[r("q-breadcrumbs",{attrs:{align:"left"}},[r("q-breadcrumbs-el",{attrs:{label:"",icon:"home",to:"/dashboard"}}),t._v(" "),r("q-breadcrumbs-el",{staticClass:"page-title",attrs:{label:"Existencias",to:"/"}})],1)],1)]),t._v(" "),r("div",{staticClass:"col-sm-6 pull-right"},[r("div",{staticClass:"col-xs-12 col-sm-12 col-md-12 pull-right"},[r("q-btn",{staticStyle:{"margin-top":"5px"},attrs:{color:"green",icon:"fas fa-file-excel"},on:{click:function(e){t.generarCsvExistencias()}}},[r("q-tooltip",[t._v("Generar CSV")])],1),t._v(" "),r("q-btn",{staticStyle:{"margin-top":"5px"},attrs:{color:"red-14",icon:"fas fa-file-pdf"},on:{click:function(e){t.generarPdfExistencias()}}},[r("q-tooltip",[t._v("Generar PDF")])],1),t._v(" "),r("q-btn",{staticStyle:{"margin-top":"5px"},attrs:{color:"green",icon:"fas fa-search",loading:t.loadingButton},on:{click:function(e){t.cargarDatosReporte()}}},[r("q-tooltip",[t._v("Buscar")])],1),t._v(" "),r("q-btn",{staticStyle:{"margin-top":"5px"},attrs:{color:"red",icon:"loop"},on:{click:function(e){t.borrar()}}},[r("q-tooltip",[t._v("Limpiar")])],1)],1)])])]),t._v(" "),r("div",{staticClass:"q-pa-md bg-grey-3"},[r("div",{staticClass:"row bg-white border-panel"},[r("div",{staticClass:"col q-pa-md"},[r("div",{staticClass:"row q-col-gutter-xs"},[r("div",{staticClass:"col-sm-3",attrs:{"icon-color":"dark"}},[r("q-field",{attrs:{icon:"store","icon-color":"dark"}},[r("q-select",{attrs:{inverted:"",color:"dark","float-label":"Almacen",options:t.almacenesOptions,filter:""},model:{value:t.form.fields.almacen,callback:function(e){t.$set(t.form.fields,"almacen",e)},expression:"form.fields.almacen"}})],1)],1),t._v(" "),r("div",{staticClass:"col-sm-3"},[r("q-field",{attrs:{icon:"fas fa-box","icon-color":"dark"}},[r("q-select",{attrs:{inverted:"",color:"dark","float-label":"Producto",options:t.productosOptions,filter:""},model:{value:t.form.fields.producto,callback:function(e){t.$set(t.form.fields,"producto",e)},expression:"form.fields.producto"}})],1)],1)])])])]),t._v(" "),r("div",{staticClass:"q-pa-md bg-grey-3"},[r("div",{staticClass:"row bg-white border-panel"},[r("div",{staticClass:"col q-pa-md",staticStyle:{padding:"0"}},[r("div",{staticClass:"col q-pa-md"},[r("div",{staticClass:"col-sm-12",staticStyle:{"padding-bottom":"10px"}},[r("q-search",{attrs:{color:"primary"},model:{value:t.form.filter,callback:function(e){t.$set(t.form,"filter",e)},expression:"form.filter"}})],1),t._v(" "),r("div",{staticClass:"col-sm-12 q-mt-sm",attrs:{id:"sticky-table-scroll"}},[r("q-table",{attrs:{id:"sticky-table-newstyle",data:t.form.data,columns:t.form.columns,selected:t.form.selected,filter:t.form.filter,color:"positive",title:"",dense:!0,pagination:t.form.pagination,loading:t.form.loading,"rows-per-page-options":t.form.rowsOptions},on:{"update:selected":function(e){t.$set(t.form,"selected",e)},"update:pagination":function(e){t.$set(t.form,"pagination",e)}},scopedSlots:t._u([{key:"body",fn:function(e){return[r("q-tr",{attrs:{props:e}},[r("q-td",{key:"almacen",attrs:{props:e}},[t._v(t._s(e.row.almacen))]),t._v(" "),r("q-td",{key:"categoria",attrs:{props:e}},[t._v(t._s(e.row.categoria))]),t._v(" "),r("q-td",{key:"linea",attrs:{props:e}},[t._v(t._s(e.row.linea))]),t._v(" "),r("q-td",{key:"presentacion",attrs:{props:e}},[t._v(t._s(e.row.presentacion))]),t._v(" "),r("q-td",{key:"codigo_categoria",staticStyle:{"text-align":"left"},attrs:{props:e}},[t._v(t._s(e.row.codigo_categoria)+" - "+t._s(e.row.codigo_linea)+" - "+t._s(e.row.codigo_producto))]),t._v(" "),r("q-td",{key:"nombre",attrs:{props:e}},[t._v(t._s(e.row.nombre))]),t._v(" "),r("q-td",{key:"existencia",attrs:{props:e}},[t._v(t._s(e.row.existencia))])],1)]}}])}),t._v(" "),r("q-inner-loading",{attrs:{visible:t.form.loading}},[r("q-spinner-dots",{attrs:{size:"64px",color:"primary"}})],1)],1)])])])])])},h=[];d._withStripped=!0;var m=r("XyMi"),v=!1;var g=function(t){v||r("tjyR")},y=Object(m.a)(p,d,h,!1,g,"data-v-d44c3c2a",null);y.options.__file="src/pages/rpt/Existencias.vue";e.default=y.exports},exGp:function(t,e,r){"use strict";e.__esModule=!0;var o,n=r("//Fk"),a=(o=n)&&o.__esModule?o:{default:o};e.default=function(t){return function(){var e=t.apply(this,arguments);return new a.default(function(t,r){return function o(n,i){try{var s=e[n](i),c=s.value}catch(t){return void r(t)}if(!s.done)return a.default.resolve(c).then(function(t){o("next",t)},function(t){o("throw",t)});t(c)}("next")})}}},jyFz:function(t,e,r){var o=function(){return this}()||Function("return this")(),n=o.regeneratorRuntime&&Object.getOwnPropertyNames(o).indexOf("regeneratorRuntime")>=0,a=n&&o.regeneratorRuntime;if(o.regeneratorRuntime=void 0,t.exports=r("SldL"),n)o.regeneratorRuntime=a;else try{delete o.regeneratorRuntime}catch(t){o.regeneratorRuntime=void 0}},tjyR:function(t,e,r){var o=r("G2GB");"string"==typeof o&&(o=[[t.i,o,""]]),o.locals&&(t.exports=o.locals);(0,r("rjj0").default)("130f2c7a",o,!1,{})}});