(function(a){a.jgrid=a.jgrid||{};a.extend(a.jgrid,{showHideColumnMenu:{adjustGridWidth:!0,viewHideDlgColumnsAsDisabled:!1,allowHideInernalColumns:!1,shrink:!1,menuStyle:{"float":"left"},modifyMenuItem:function(c,d,p){0<=a.inArray(d.name,["rn","subgrid","cb"])?p.allowHideInernalColumns||c.hide():d.hidedlg&&(p.viewHideDlgColumnsAsDisabled?c.addClass("ui-state-disabled"):c.hide())}}});a.jgrid.extend({showHideColumnMenu:function(c){var d=a.extend(!0,{},a.jgrid.showHideColumnMenu,c);c=null!=a.ui&&"string"===
typeof a.ui.version?/^([0-9]+)\.([0-9]+)\.([0-9]+)$/.exec(a.ui.version):[];var p=null!=c&&4===c.length&&"1"===c[1]&&11>c[2];return this.each(function(){var c=a(this),n=function(){a(this.grid.hDiv).find(".ui-jqgrid-labels").contextmenu(function(m){for(var b=c.jqGrid("getGridParam"),h=b.colModel,f=b.colNames,v=h.length,e,q=b.groupHeader,n={},r={},t,u,w,g,k=a("<ul class='ui-jqgrid-showHideColumnMenu'></ul>"),b=0;b<v;b++)n[h[b].name]=b;if(null!=q&&null!=q.groupHeaders)for(t=0,w=q.groupHeaders.length;t<
w;t++)for(g=q.groupHeaders[t],u=0;u<g.numberOfColumns;u++)b=n[g.startColumnName]+u,e=h[b],r[b]=a.isFunction(d.buildItemText)?d.buildItemText.call(c[0],{iCol:b,cm:e,cmName:e.name,colName:f[b],groupTitleText:g.titleText}):a.jgrid.stripHtml(g.titleText)+": "+a.jgrid.stripHtml(""===f[b]?e.name:f[b]);for(b=0;b<v;b++)void 0===r[b]&&(e=h[b],r[b]=a.isFunction(d.buildItemText)?d.buildItemText.call(c[0],{iCol:b,cm:e,cmName:e.name,colName:f[b],groupTitleText:null}):a.jgrid.stripHtml(f[b]));for(b=0;b<v;b++)e=
h[b],f=a("<li></li>").data("iCol",b).html(r[b]),d.modifyMenuItem.call(c[0],f,e,d),f.prepend(e.hidden?d.checkboxUnChecked:d.checkboxChecked),p&&f.wrapInner("<a></a>"),f.appendTo(k);k.css(d.menuStyle);a("ul.ui-jqgrid-showHideColumnMenu").menu("destroy").remove();k.appendTo("body").menu({select:function(b,m){var f=parseInt(m.item.data("iCol"),10),e=m.item.find(d.checkboxSelector),l=h[f],g=d.isChecked.call(c[0],e,b,l);!isNaN(f)&&0<=f&&null!=l&&0<e.length&&(g?(d.toUnCheck.call(c[0],e,b,l),c.jqGrid("hideCol",
l.name)):(d.toCheck.call(c[0],e,b,l),c.jqGrid("showCol",l.name)),a(this).parent().css("zoom",1),k.menu("focus",b,m.item))},create:function(){var b=k.height(),a=window.innerHeight||document.documentElement.clientHeight;b>a&&k.height(a).css("overflow-y","scroll")}}).mouseleave(function(){a(this).menu("destroy").remove()}).position({of:a(m.target),my:"left top",at:"right center",collision:"flipfit flipfit"});return!1})};d=a.extend(!0,"fontAwesome"===this.p.iconSet||"fontAwesome"===d.iconSet?{checkboxChecked:'<i class="fa fa-check-square-o fa-fw fa-lg"></i>&nbsp;',
checkboxUnChecked:'<i class="fa fa-square-o fa-fw fa-lg"></i>&nbsp;',checkboxSelector:"i.fa",isChecked:function(a){return a.hasClass("fa-check-square-o")},toCheck:function(a){a.removeClass("fa-square-o").addClass("fa-check-square-o")},toUnCheck:function(a){a.removeClass("fa-check-square-o").addClass("fa-square-o")}}:{checkboxChecked:'<input disabled="disabled" checked="checked" type="checkbox"/>',checkboxUnChecked:'<input disabled="disabled" type="checkbox"/>',checkboxSelector:"input[type=checkbox]",
isChecked:function(a){return a.is(":checked")},toCheck:function(a){a.prop("checked",!0)},toUnCheck:function(a){a.prop("checked",!1)}},d);n.call(this);c.bind("jqGridAfterSetGroupHeaders",function(){n.call(this)})})}})})(jQuery);
