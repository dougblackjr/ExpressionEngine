/*
 * ExpressionEngine - by EllisLab
 *
 * @package		ExpressionEngine
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2003 - 2010, EllisLab, Inc.
 * @license		http://expressionengine.com/docs/license.html
 * @link		http://expressionengine.com
 * @since		Version 2.0
 * @filesource
 */
$(document).ready(function(){$(".paginationLinks .first").hide();$(".paginationLinks .previous").hide();$(".toggle_all").toggle(function(){$("input.toggle").each(function(){this.checked=true})},function(){var i=this.checked;$("input.toggle").each(function(){this.checked=false})});$("#custom_date_start_span").datepicker({dateFormat:"yy-mm-dd",prevText:"<<",nextText:">>",onSelect:function(i){$("#custom_date_start").val(i);a()}});$("#custom_date_end_span").datepicker({dateFormat:"yy-mm-dd",prevText:"<<",nextText:">>",onSelect:function(i){$("#custom_date_end").val(i);a()}});$("#custom_date_start, #custom_date_end").focus(function(){if($(this).val()=="yyyy-mm-dd"){$(this).val("")}});$("#custom_date_start, #custom_date_end").keypress(function(){if($(this).val().length>=9){a()}});var d=EE.edit.channelInfo,g=new RegExp("!-!","g");(function(){jQuery.each(d,function(j,i){jQuery.each(i,function(m,k){var l=new Array();jQuery.each(k,function(o,n){l.push(new Option(n[1].replace(g,String.fromCharCode(160)),n[0]))});d[j][m]=$(l)})})})();function c(j){var i="null";if(d[j]===undefined){j=0}jQuery.each(d[j],function(k,l){switch(k){case"categories":$("select#f_cat_id").empty().append(l);break;case"statuses":$("select#f_status").empty().append(l);break}})}$("#f_channel_id").change(function(){c(this.value)});function a(){if($("#custom_date_start").val()!="yyyy-mm-dd"&&$("#custom_date_end").val()!="yyyy-mm-dd"){focus_number=$("#date_range").children().length;$("#date_range").append('<option id="custom_date_option">'+$("#custom_date_start").val()+" to "+$("#custom_date_end").val()+"</option>");document.getElementById("date_range").options[focus_number].selected=true;$("#custom_date_picker").slideUp("fast")}}$("#date_range").change(function(){if($("#date_range").val()=="custom_date"){$("#custom_date_start").val("yyyy-mm-dd");$("#custom_date_end").val("yyyy-mm-dd");$("#custom_date_option").remove();$("#custom_date_picker").slideDown("fast")}else{$("#custom_date_picker").hide()}});$("#entries_form").submit(function(){if(!$("input:checkbox",this).is(":checked")){$.ee_notice(EE.lang.selection_required,{type:"error"});return false}});var f={iCacheLower:-1};function e(k,n,m){for(var l=0,j=k.length;l<j;l++){if(k[l].name==n){k[l].value=m}}}function h(k,m){for(var l=0,j=k.length;l<j;l++){if(k[l].name==m){return k[l].value}}return null}function b(v,t,o){var z=EE.edit.pipe,s=false,j=h(t,"sEcho"),k=h(t,"iDisplayStart"),m=h(t,"iDisplayLength"),y=k+m,r=document.getElementById("keywords"),w=document.getElementById("f_status"),q=document.getElementById("f_channel_id"),p=document.getElementById("f_cat_id"),u=document.getElementById("f_search_in"),l=document.getElementById("date_range"),n="&ajax=true&keywords="+r.value+"&channel_id="+q.value;if(u.value=="comments"){window.location=EE.BASE+"&C=content_edit&M=view_comments"+n}t.push({name:"keywords",value:r.value},{name:"status",value:w.value},{name:"channel_id",value:q.value},{name:"cat_id",value:p.value},{name:"search_in",value:u.value},{name:"date_range",value:l.value});f.iDisplayStart=k;if(f.iCacheLower<0||k<f.iCacheLower||y>f.iCacheUpper){s=true}if(f.lastRequest&&!s){for(var A=0,x=t.length;A<x;A++){if(t[A].name!="iDisplayStart"&&t[A].name!="iDisplayLength"&&t[A].name!="sEcho"){if(t[A].value!=f.lastRequest[A].value){s=true;break}}}}f.lastRequest=t.slice();if(s){if(k<f.iCacheLower){k=k-(m*(z-1));if(k<0){k=0}}f.iCacheLower=k;f.iCacheUpper=k+(m*z);f.iDisplayLength=h(t,"iDisplayLength");e(t,"iDisplayStart",k);e(t,"iDisplayLength",m*z);t.push({name:"keywords",value:r.value},{name:"status",value:w.value},{name:"channel_id",value:q.value},{name:"cat_id",value:p.value},{name:"search_in",value:u.value},{name:"date_range",value:l.value});$.getJSON(v,t,function(i){f.lastJson=jQuery.extend(true,{},i);if(f.iCacheLower!=f.iDisplayStart){i.aaData.splice(0,f.iDisplayStart-f.iCacheLower)}i.aaData.splice(f.iDisplayLength,i.aaData.length);o(i)})}else{json=jQuery.extend(true,{},f.lastJson);json.sEcho=j;json.aaData.splice(0,k-f.iCacheLower);json.aaData.splice(m,json.aaData.length);o(json);return}}if(EE.edit.tableColumns==9){MyCols=[null,null,{bSortable:false},null,null,null,null,null,{bSortable:false}];MySortCol=5}else{MyCols=[null,null,{bSortable:false},null,null,null,null,{bSortable:false}];MySortCol=4}oTable=$("#entries_form .mainTable").dataTable({sPaginationType:"full_numbers",bLengthChange:false,aaSorting:[[MySortCol,"desc"]],bFilter:false,sWrapper:false,sInfo:false,bAutoWidth:false,iDisplayLength:EE.edit.perPage,aoColumns:MyCols,oLanguage:{sZeroRecords:EE.lang.noEntries,oPaginate:{sFirst:'<img src="'+EE.edit.themeUrl+'images/pagination_first_button.gif" width="13" height="13" alt="&lt; &lt;" />',sPrevious:'<img src="'+EE.edit.themeUrl+'images/pagination_prev_button.gif" width="13" height="13" alt="&lt; &lt;" />',sNext:'<img src="'+EE.edit.themeUrl+'images/pagination_next_button.gif" width="13" height="13" alt="&lt; &lt;" />',sLast:'<img src="'+EE.edit.themeUrl+'images/pagination_last_button.gif" width="13" height="13" alt="&lt; &lt;" />'}},bProcessing:true,bServerSide:true,sAjaxSource:EE.BASE+"&C=content_edit&M=edit_ajax_filter",fnServerData:b});$("#keywords").keyup(function(){oTable.fnDraw()});$("select#f_channel_id").change(function(){oTable.fnDraw()});$("select#f_cat_id").change(function(){oTable.fnDraw()});$("select#f_status").change(function(){oTable.fnDraw()});$("select#f_search_in").change(function(){oTable.fnDraw()});$("select#date_range").change(function(){oTable.fnDraw()})});