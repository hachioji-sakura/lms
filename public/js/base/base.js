/**
* https://github.com/hideo-yasui/base.git
* baseプラグイン
* 汎用的なページ処理をまとめる
* @namespace
* @class base
**/
;(function(root, undefined) {
	"use strict";
	var _saveTimer = null;
	var _isUpdate = false;
	var _isImport = false;
	var _maxInt = Number.MAX_SAFE_INTEGER;
	var _cache = {
		"_fileUI" : {},
		"userSetting" : {
			"pageSize" : 20,
			"maxPageSize" : 200
		},
		"_url" : {
		},
	};
	var _currentForm = "main";
	//list_table用のIF
	var _currentRequest = null;
	var _listTable = {};
	var _listInfo = {"page" : 0, "maxpage" : 0, "count" : 0, "option" : null};
	var _editPageId = "sub";
	var _dialogId = "subDialog";
	var _isPageLoad = false;
	var public_method = {
		//初期処理
		pageSettinged : pageSettinged,
		getFileForm : getFileForm,
		listRefresh : listRefresh,
		linkProc : linkProc,
		exportProc : exportProc,
		showPage : showPage,
		pageOpen : pageOpen,
		pageClose : pageClose
	};
	//---------------------------------------------------
	//onload系～
	//---------------------------------------------------
	//---------------------------------------------------
	//画面表示系～
	//---------------------------------------------------
	//ページ設定後処理：各フォームUIのロード
	function pageSettinged(form_id, formData){
		_currentForm = form_id;
		$("input[accesskey='enter']", $("#"+form_id)).on("keypress", function(e){
			if(e.keyCode==13){
				$("button.btn-submit").click();
			}
		});

		$("input[type=file]", $("#"+form_id)).each(function(){
			var name = $(this).attr("name");
			if(util.isEmpty(name)) return;
			$("img[alt="+name+"][accesskey=preview]", $("#"+form_id)).hide();
			_cache["_fileUI"][name] = $(this).fileUI({
				"form_id" : form_id,
				"dragdrop" : ".dragdropupload",
				"onChange" : function(element, fileData){
					var name = $(element).attr("name");
					var accesskey = $(element).attr("accesskey");
					var filename = fileData["name"];
					if(util.isEmpty(filename)) filename = "ファイルが指定されていません";
					$("img[alt="+name+"][accesskey=preview]", $("#"+form_id)).hide();
					$("*[alt="+name+"][accesskey=filename]", $("#"+form_id)).html(filename);
					$("*[alt="+name+"][accesskey=filesize]", $("#"+form_id)).html(fileData["size"]);
					$("*[alt="+name+"][accesskey=filetype]", $("#"+form_id)).html(fileData["type"]);
					if(fileData["file"]===null) return false;
					switch(accesskey){
						case "upload":
							fileUpload(name);
							break;
						case "auto_upload":
							_fileUpload(name, _currentRequest["query_code"], function(result){
								dom.setFileForm(form_id, name, result["data"]["fileid"]);
							});
							break;
						case "import":
							fileImport(name);
							break;
					}
				}
			});
		});
		$("select[accesskey]", $("#"+form_id)).each(function(i){
			var _defaultSelect = $(this).attr("defaultSelect");
			dom.selectFormLoad(_currentForm, this, _defaultSelect, null, formData);
		});
		$("div[uitype=radio]", $("#"+form_id)).each(function(i){
			var _defaultSelect = $(this).attr("defaultSelect");
			dom.selectFormLoad(_currentForm, this, _defaultSelect, $(this).html(), formData);
		});
		$("select.select2", $("#"+form_id)).each(function(e){
			var _placeholder = '選択してください';
			if($(this).attr('placeholder')) _placeholder = $(this).attr('placeholder');
			var _parent_id = $(this).attr("parent_id");
			var _width = $(this).attr("width");
			var _parent = $("body");
			var _name = $(this).attr('name');

			if(!util.isEmpty(_parent_id)){
				_parent = $("#"+_parent_id);
			}
			if(formData!=null && formData[_name] && !util.isEmpty(formData[_name])){
				$(this).select2('destroy');
				$(this).val(formData[_name]);
			}
			$(this).select2({
				width: _width,
				placeholder: _placeholder,
			});
		});
		var colors = ['green', 'aero', 'red', 'blue', 'grey'];
		$.each(colors, function(i,color){
			$('input[type="checkbox"].icheck.flat-'+color+', input[type="radio"].icheck.flat-'+color).iCheck({
				checkboxClass: 'icheckbox_flat-'+color+' mr-1',
				radioClass   : 'iradio_flat-'+color+' mr-1'
			})
		});
		$('input[type="checkbox"].icheck, input[type="radio"].icheck').on('ifChanged', function (e) {
			$(this).trigger("change", e);
		});
		//郵便番号入力
		dom.setPostnoForm(form_id);

		/*
		//数値入力
		dom.setNumberForm(form_id);
		*/
		//日付入力
		dom.setCalendarForm(form_id);

		//html入力
		dom.setEditorForm(form_id);

		//値自動調整
		front.setInputAdjust(form_id);
		dom.setEditForm(formData, form_id, _isUpdate);
		$("textarea", $("#"+form_id)).each(function(){
			var val = ($(this).val()+"\n").split("\n");
			var len = val.length;
			if(len > 15) len = 15;
			else if(len < 3) len = 3;
			$(this).attr("rows", len);
		});
	}

	//---------------------------------------------------
	//button
	//---------------------------------------------------
	//ボタンフォームの制御
	function buttonControl(option){
		if(option["control"] && !util.isEmpty(option["control"])){
			var control = option["control"];
			var buttonForm = option["button"];
			var permission = util.getLocalData("permission");
			var button = {
					"back" : { "control" : "hidden", "target" : "left", "text" : "戻る", "icon" : "reply"},
					"newadd" : { "control" : "hidden", "target" : "left", "align" : "right_text", "icon" : "plus", "text" : "追加", "alt" : "add"},
					"setting" : { "control" : "hidden", "target" : "left", "align" : "right_text", "icon" : "cogs", "text" : "一括更新"},
					"refresh" : { "control" : "hidden", "target" : "left", "align" : "right_text", "icon" : "sync", "text" : "再表示"},
					"import" : { "control" : "hidden", "target" : "left", "align" : "right_text", "icon" : "upload", "text" : "ｲﾝﾎﾟｰﾄ"},
					"export" : { "control" : "hidden", "target" : "left", "align" : "right_text", "icon" : "download", "text" : "ｴｸｽﾎﾟｰﾄ"},
					"rowedit" : { "control" : "enabled"},
					"rowcopy" : { "control" : "enabled"},
					"rowdelete" : { "control" : "enabled"}
				};


			if(buttonForm){
				$.extend(button, buttonForm);
			}
			if(control){
				for(var i=0;i<control.length;i++){
					if(util.isEmpty(control[i])) continue;
					if(!button[control[i]]) button[control[i]] = {};
					var _status = "enabled";
					/*
					if((control[i] == "pagestart" || control[i] == "pageprev") && _listInfo["page"] == 0){
						_status = "disabled";
					}
					else if((control[i] == "pagenext" || control[i] == "pageend") && _listInfo["page"] >= _listInfo["maxpage"]){
						_status = "disabled";
					}
					*/
					button[control[i]]["control"] = _status;
				}
			}

			if(!util.isEmpty(_cache["pageCode"]) && !util.isEmpty(permission) &&
			!util.isEmpty(permission[_cache["pageCode"]]) && !util.isEmpty(permission[_cache["pageCode"]]["button"])){
				//パーミションが存在する場合、ボタン制御追加
				//存在しない場合は無制御運用
				var buttonPermission = permission[_cache["pageCode"]]["button"];
				for(var key in buttonPermission){
					if(util.isEmpty(buttonPermission[key]["control"])) continue;
					if(util.isEmpty(button[key])) continue;
					button[key]["control"] = buttonPermission[key]["control"];
				}
			}

			$("#buttonmenu", $("#main")).empty();
			$("*[accesskey=detailsearch]").hide();
			$("*[accesskey=search]", $("#main")).hide();
			$("*[accesskey=pagestart]", $("#main")).hide();
			$("*[accesskey=pageprev]", $("#main")).hide();
			$("*[accesskey=pagenext]", $("#main")).hide();
			$("*[accesskey=pageend]", $("#main")).hide();
			$("input[name='p_search_word']").unbind("keypress");
			var _tpl = [
				'<button type="button" #attr# class="btn btn-info btn-sm mr-1">',
				'<i class="fa fa-#icon# mr-1"></i></i>',
				'<span class="btn-label">#text#</span>',
				'</button>',
			].join('');

			for(var i=0;i<control.length;i++){
				var key = control[i];
				if(util.isEmpty(button[key])) continue;
				//紐づくフォームの表示制御を行うので、a[accesskey...とはしない
				if(button[key]["control"]=="hidden") continue;
				var _html = _tpl;
				var _align = "";
				var _accesskey = key;
				var _attr = "";

				if(!button[key]["accesskey"]) button[key]["accesskey"] = key;
				if(button[key]["text"]) _html = _html.replace("#text#", button[key]["text"]);
				if(button[key]["icon"]) _html = _html.replace("#icon#", button[key]["icon"]);

				if(_listInfo["maxpage"] > 0 && (
						key=="pagestart" || key=="pageprev" ||
						key=="pagenext" || key=="pageend" ||
						key=="search" || key=="detailsearch")){
					$("*[accesskey="+key+"]", $("#main")).css("display", "inline-block");
				}
				if(key == "search"){
					$("*[accesskey="+key+"]", $("#main")).css("display", "inline-block");

					$("input[name='p_search_word']").on("keypress", function(e){
						if(e.keyCode==13){
							//検索入力～Enterで、検索ボタン押下
							$("button.btn[accesskey=search]").click();
						}
					});
				}
				else if(key == "detailsearch"){
					$("*[accesskey="+key+"]").css("display", "inline-block");
				}

				for(var attribute in button[key]){
					if(attribute == "class" || attribute == "target" || attribute == "control" ||
						attribute == "align" || attribute == "icon" || attribute == "text" ) continue;
					_attr += " "+attribute+"="+button[key][attribute];
				}

				if(button[key]["control"]=="disabled"){
					_attr += ' disabled="disabled"';
					$("*[accesskey="+key+"]", $("#main")).prop("disabled", true);
				}
				if(button[key]["target"]) {
					_html = dom.textFormat(_html, {"text" : "", "icon" : "", "key" : key, "attr" : _attr});
					$("#buttonmenu", $("#main")).append(_html);
				}
			}
			$("button.btn[accesskey]").unbind('click');
			$("button.btn[accesskey]").click(function(e){
				//var type = $("span", this).attr("class").replace("icon ", "");
				var accesskey = $(this).attr("accesskey");
				var alt = $(this).attr("alt");
				//console.log("accesskey="+accesskey);

				//疑似的にlinkProcの仕様にあわせて、linkを作成する
				var _linkDOM = $("<a></a>");
				if(util.isEmpty(alt)) alt = accesskey;
				_linkDOM.attr("target", alt);
				_linkDOM.attr("alt", accesskey);
				_linkDOM.text("target", $(this).text());

				if(util.isEmpty(alt)) alt = accesskey;
				switch(accesskey){
					case "newadd":
					case "export":
					case "subpage" :
						_linkDOM.attr("accesskey", "subpage");
						linkProc(_linkDOM, _currentRequest);
						break;
					case "detaFilsearch":
					case "import":
					case "dialog" :
					case "scansearch":
					case "scanupd":
						_linkDOM.attr("accesskey", "dialog");
						linkProc(_linkDOM, _currentRequest);
						break;
					case "search":
						var keyword = $("input[name='p_search_word']").val();
						$("input, textarea, select", $("#"+_currentForm)).val("");
						//console.log("検索"+keyword);
						keyword = keyword.replace_all("　", " "); //全角スペースを半角スペースに変換
						_searchProc({"searchword" : keyword});
						break;
					case "setting":
						var selectData = _listTable["listTable"].getSelectData(null);
						if(selectData.length<1){
							service.alert("E_LIST_NOSELECT", "", null);
						}
						else {
							_linkDOM.attr("accesskey", "dialog");
							linkProc(_linkDOM, _currentRequest);
						}
						break;
					case "refresh":
						_isPageLoad = true;
						listRefresh(true);
						service.clearRequestCache();
						break;
					case "listinit":
						_linkDOM.attr("accesskey", "listinit");
						linkProc(_linkDOM, _currentRequest);
						break;
					case "back":
					case "close":
						//pageClose();
						window.history.back();
						break;
					case "preview":
						//プレビューボタン
						break;
					case "sendmail":
						service.sendMail();
						break;
					case "pageprev":
						listPageMove(-1);
						break;
					case "pagenext":
						listPageMove(1);
						break;
					case "pagestart":
						listPageStart();
						break;
					case "pageend":
						listPageEnd();
						break;
				}
				e.preventDefault();
			});

		}
		var sp = service.getQueryParam("sp");
		if(!util.isEmpty(sp)){
		}

	}
	//---------------------------------------------------
	//ajax処理系～
	//---------------------------------------------------
	//ajax file export
	function exportProc(target){
		if(!front.validateFormValue(_currentForm)) return false;
		service.confirm("C_POST_EXP", "", function(){
			var url = "/export/"+target;
			var _req = front.getFormValue(_currentForm);
			service.downloadAjax(url, _req,
				function(result, st, xhr) {
					if(result["status"]==200){
						service.fileDownload(result.url);
					}
				},
				function(xhr, st, err) {
					service.error("exportProc\n"+err.message+"\n"+xhr.responseText);
				});
		});
	}

	//---------------------------------------------------
	//汎用イベント処理系～
	//---------------------------------------------------
	//ファイルアップロード
	function fileUpload(formname, callback){
		if(!front.validateFormValue(_currentForm)) return false;
		service.confirm("C_POST_UPL", "", function(){
			_fileUpload(formname, _currentRequest["query_code"], function(result){
					dom.setFileForm(_currentForm, formname, result["data"]["fileid"]);
					if(util.isFunction(callback)) callback(fileid);
			});
		});
	}
	function getFileForm(formname){
		if(!_cache["_fileUI"][formname]) return null;
		return _cache["_fileUI"][formname].fileUI("getFile");
	}

	//ファイルアップロード(バックグラウンド）
	function _fileUpload(formname, remark, callback){
		var _file = $("input[name="+formname+"][type=file]");
		var fileid = _file.attr("id");
		var fileData = _cache["_fileUI"][formname].fileUI("getFile");
		var fval = fileData["file"];
		var fsize  = fileData["sizeVal"];
		var fname = fileData["name"];
		var _req  = new FormData();
		_req.append("form_id", fileid);
		_req.append(formname,  fval);
		_req.append("filename", fname);
		_req.append("remark", remark);
		service.uploadAjax("/upload", _req,
			function(result, st, xhr) {
				result["form_id"] = fileid;
				result["filename"] = fname;
				result["remark"] = remark;
				result["fsize"] = fsize;
				if(util.isFunction(callback)) callback(result);
			},
			function(xhr, st, err) {
				service.error("fileUpload\n"+err.message+"\n"+xhr.responseText);
			}
		);
	}
	//ファイルインポート
	function fileImport(formname){
		if(!front.validateFormValue(_currentForm)) return false;
		var _file = $("input[name="+formname+"]", $("#"+_currentForm));
		var fileid = _file.attr("id");
		var query = _file.attr("query");
		var fileData = _cache["_fileUI"][formname].fileUI("getFile");
		var fval = fileData["file"];
		var fsize  = fileData["sizeVal"];
		var fname = fileData["name"];

		service.confirm("C_POST_IMP", "", function(){
			var _current = front.getFormValue(_currentForm);
			var _req  = new FormData();
			for(var key in _current){
				_req.append(key, _current[key]);
			}
			_req.append("form_id", fileid);
			_req.append(formname,  fval);
			_req.append("filename", fname);
			_req.append("remark", _currentRequest["query_code"]);
			service.uploadAjax("/import/"+query, _req,
				function(result, st, xhr) {
					if(result["status"]==200){
						_listInfo["page"] = 0;
						var _req = {"query_code" :  query};
						_isImport = true;
						listInit("listTable",_req);
					}
					else {
						service.alert(result["message"], result["description"], null);
					}
				},
				function(xhr, st, err) {
					service.error("fileImport\n"+err.message+"\n"+xhr.responseText);
				}
			);
		});
	}
	//ダイアログ表示
	function showPage(type, form_id, page_title, page_url, callback){
    $("#"+form_id+" .page_contents").load(page_url, function(response, status, xhr){
			$("#"+form_id+" .page_title").html(page_title);
			if(status !== "success"){
				service.alert("E_ERROR", {});
				return;
			}
			//翻訳機能
			//translate.translate_start("#"+form_id);

			$("#"+form_id+' form select.select2').attr('parent_id', form_id);
      base.pageSettinged(form_id+' form', null);
      //サブページ内のsubmit
      $("#"+form_id+" .btn.btn-submit[accesskey]").on("click", function(){
				var accesskey = $(this).attr("accesskey");
        var form = form_id+" .page_contents #"+accesskey+" form";
        if(!front.validateFormValue(form)) return false;
				var _confirm = $(this).attr("confirm");
				if(!util.isEmpty(_confirm)){
					if(!confirm(_confirm)) return false;
				}
				util.clearLocalData();
        $("#"+form).submit();
      });
      //サブページ内のreset
      $("#"+form_id+" .btn[type=reset]").on("click", function(){
				if(type==="dialog"){
        	$("#"+form_id).modal('hide');
				}
				else {
					$("#"+form_id).collapse('hide');
				}
				if(util.isFunction(callback)) callback();
      });
			if(type==="dialog"){
				console.log('modal open');
      	$("#"+form_id).modal('show');
				// モーダルが開いたら、bodyにfixedを付与
				/*
			  document.body.style.position = 'absolute';
			  document.body.style.top = `-${window.scrollY}px`;
				*/
				$("#"+form_id).unbind("hide.bs.modal");
				$("#"+form_id).unbind("hidden.bs.modal");
				$("#"+form_id).on('hide.bs.modal', function () {
					console.log('modal close');
					if(front.isInput(form_id)==true){
						return confirm("このフォームを閉じますか？\n入力したデータは残りません。");
					}
				});
				$("#"+form_id).on('hidden.bs.modal', function () {
					console.log('modal close');
					// モーダルが閉じられ時
					/*
					document.body.style.position = '';
					document.body.style.top = '';
					window.scrollTo(0, parseInt(scrollY || '0') * -1);
					*/
					pageClose(form_id);
				});
			}
			else{
				$("#"+form_id).collapse('show');
			}
    });
	}

	function paramPageLoad(form_id, param, formData, callback){
		var title = param["NAME"];
		var p = $("<p></p>");
		p.html(param["OPTION_STRING"]);
		var option = p.html();
		if(option && option!="" && option.indexOf(":")>=0) {
			if(!util.isJson(option) && util.isJson("{"+option+"}")){
				option = "{"+option+"}";
			}
			option = util.toJson(option);
			if(option["title"] && !util.isEmpty(option["title"])) title = option["title"];
			//更新モードの場合、OPTION_STRING.edit_title設定があれば、優先的に使う
			if(_isUpdate && option["edit_title"] && !util.isEmpty(option["edit_title"])) title = option["edit_title"];
			var type=$("#"+form_id).attr("type");
			$(".content-sub-title", $("#"+form_id)).html(title);
			switch(param["TYPE"]){
				case "param":
					var _pageContents = dom.paramPageLoad(option["form"],option["button"],_listTable["listTable"]);
					$(".content-sub-body", $("#"+form_id)).html(_pageContents);
					break;
				case "url":
					break;
			}
			pageSettinged(form_id, formData);
			buttonControl(option);
			if(callback && $.type(callback)=="function") callback(option);
		}
	}
	//---------------------------------------------------
	//list_table UI系～
	//---------------------------------------------------
	function listInit(id, req, isNoCache, callback){
		pageClose();
		//初期表示・（未対応：ソート）・ページング・検索にて通過(ページはクリアしない）
		if(!req["_order_"]) req["_order_"] = 1;
		req["_limit_"] = _cache["userSetting"]["pageSize"];
		req["_offset_"] = _cache["userSetting"]["pageSize"]*_listInfo["page"];
		_currentRequest = req;
		$("[accesskey=search] select, [accesskey=search] input").each(function(){
			//検索フォームの値をパラメータと同期
			var _name = $(this).attr("name");
			if(!util.isEmpty(req[_name])) $(this).val(req[_name]);
			else $(this).val("");
		});

		_cache["_url"]["listInit"] = req;
		delete _cache["_url"]["showPage"];
		delete req["ta"];
		delete req["to"];
		delete req["lt"];
		service.getAjax(true, "/search/"+req["query_code"], req,
			function(result, st, xhr) {
				var data =  result["data"];
				var count =  result["count"];
				var option =  result["option"];
				var type  =  result["type"];
				var title = result["name"];
				if(option && option!="" && option.indexOf(":")>=0){
					if(!util.isJson(option) && util.isJson("{"+option+"}")){
						option = "{"+option+"}";
					}
					option = util.toJson(option);
					_listInfo["option"] = option;
					if(option["title"] && !util.isEmpty(option["title"])) title = option["title"];
				}
				if(count==0) data = [];
				_listInfo["count"] = count;
				var _pageSize = data.length;
				if(util.diffVal(_cache["userSetting"]["pageSize"], _pageSize)>0) _pageSize = _cache["userSetting"]["pageSize"]|0;
				if(util.diffVal(_cache["userSetting"]["maxPageSize"], _pageSize)<0) _pageSize = _cache["userSetting"]["maxPageSize"]|0;
				_listInfo["maxpage"] = (count / _pageSize)|0;
				if(count==_listInfo["maxpage"]*_pageSize && _listInfo["maxpage"]>0) _listInfo["maxpage"]-=1;

				buttonControl(option);

				var _listParam = {
					"data" : data,
					"header" : option["header"],
					"tableStyleName" : "table table-bordered table-striped table-responsive",
					"zeroPaddingSize" : 3,
					"maxPageSize" : _cache["userSetting"]["maxPageSize"],
					"sortField" : "",
					"onSort" : listSort,
					"filterVal" : null,
					"onFilter" : listFilter,
					"onButtonClick" : editProc,
					"onLinkClick" : linkProc
				};
				if(_listTable[id]){
					_listTable[id].publish(_listParam);
				}
				else {
					switch(type+""){
						case "0":
							_listTable[id] = new ListTable($("#"+id), _listParam);
							break;
						case "1":
							_listTable[id] = new CardTable($("#"+id), _listParam);
							break;
					}
					/*
					var _component = $("#"+id).attr("alt");
					switch(_component.toLowerCase()){
						case "cardtable":
							_listTable[id] = new CardTable($("#"+id), _listParam);
							break;
						case "listtable":
							_listTable[id] = new ListTable($("#"+id), _listParam);
							break;
					}
					*/
				}
				_listTable[id].refresh();

				var _fromRecord = _listInfo["page"]*_pageSize+1;
				var _toRecord = _fromRecord+_pageSize-1;
				if(_toRecord > _listInfo["count"]) _toRecord = _listInfo["count"];
				var _pageInfo = (_fromRecord)+"-"+(_toRecord)+"件/"+_listInfo["count"]+"件中";
				if(count==0) _pageInfo = "-";
				$("#pageInfo").html(""+(_listInfo["page"]+1) + " ページ /  " + (_listInfo["maxpage"]+1)+"ページ中");
				$("#pageInfo").attr("title" , _pageInfo);
				//切り替えた場合は、画面をスクロールトップにする
				$(".content-title").html(title);
				$("title").html(title);
				$('body, html').scrollTop(0);
				if(callback && $.type(callback)=="function") callback(option);
			},
			function(xhr, st, err) {
				service.error("listInit\n"+err.message+"\n"+xhr.responseText);
			}, isNoCache
		);
	}
	function listRefresh(isRefresh, params){
		var _req = $.extend({}, _currentRequest);
		if(!util.isEmpty(params)){
			_req = $.extend(_req, params);
		}
		if(_currentRequest!=null) listInit("listTable", _req, isRefresh);
	}
	function listPageMove(v){
		var _p = _listInfo["page"];
		_listInfo["page"]+=v;
		if(_listInfo["page"]<0) _listInfo["page"]=0;
		else if(_listInfo["page"]>_listInfo["maxpage"]) _listInfo["page"]=_listInfo["maxpage"];
		var _param = $.extend({}, _listInfo);
		var _req = $.extend({}, _currentRequest);

		if(_p == _listInfo["page"]) return;
		listRefresh();
	}
	function listPageStart(){
		listPageMove(-_maxInt);
	}
	function listPageEnd(){
		listPageMove(_maxInt);
	}
	function listFilter(data, field){
		if(util.isEmpty(filter)) return  data;
		var filterVal = $("*[accesskey=scan][alt=codes]").val();
		filterVal = (filterVal+",").split(",");
		var result = [];
		for(var i=0,n=filterVal.length;i<n;i++){
			if(util.isEmpty(filterVal[i])) continue;
			var rowNo = _listTable["listTable"].existData(null, field, filterVal[i]);
			result.push(data[rowNo]);
		}
		return result;
	}
	function listSort(data, sortField){
		if(util.isEmpty(sortField)) return  data;
		var _isLocal = false;

		if(_listInfo["page"]==0 && _listInfo["maxpage"]==0) _isLocal=true;
		if(_isLocal){
			var _sortField = [];
			for(var key in sortField){
				_sortField.unshift(key);
			}
			for(var i=0,n=_sortField.length;i<n;i++){
				var key = _sortField[i];
				var _order = 0;
				if(sortField[key]=="desc") _order = -1;
				else if(sortField[key]=="asc") _order = 1;
				if(_order==0) continue;
				data.timsort(function(first, second){
					var diff = util.diffVal(first[key] , second[key]);
					if (diff<0) return _order;
					else if (diff>0) return -_order;
					else return 0;
				});
			}
		}
		else {
			var _order = [];
			for(var key in sortField){
				if(util.isEmpty(sortField[key])) continue;
				_order.push(key+" "+sortField[key]);
			}
			if(!util.isEmpty(_order) && _order.length>0){
			/*
				var _req = {
					"_order_":	_order
				};
				$.extend(_req, _currentRequest);
				_req["_offset_"] = _cache["userSetting"]["pageSize"]*_listInfo["page"];
				_req["_limit_"] = _cache["userSetting"]["pageSize"];
			*/
				_currentRequest["_order_"] = _order;
				service.getAjax(false, "/search/"+_currentRequest["query_code"], _currentRequest,
					function(result, st, xhr) {
						data =  result["data"];
					},
					function(xhr, st, err) {
						service.error("listSort\n"+err.message+"\n"+xhr.responseText);
					}, true
				);
			}
		}
		return data;
	}
	//編集操作時の制御（編集・削除・コピー）
	function editProc(button, data){
		var _accesskey = $( button).attr("accesskey");
		var _alt = $( button).attr("alt");
		var _pageId = _editPageId;
		if(_alt==="dialog"){
			_pageId = _dialogId;
		}
		if(util.isEmpty(_accesskey)) return;
		if(_accesskey=="rowedit" || _accesskey=="rowcopy"){
			var isEdit = false;
			if(_accesskey=="rowedit") isEdit = true;
			showEditPage(_pageId, _currentRequest["query_code"]+"_add",_currentRequest["query_code"], data, isEdit);
		}
		else if(_accesskey == "rowdelete"){
			service.confirm("C_POST_DEL", "", function(){
				var _req = {
						"DELETE_FLAG":	 		1
						,"ID" :  data["ID"]
				};

				service.postAjax("/delete/"+_currentRequest["query_code"], _req,
					function(result, st, xhr) {
						listRefresh(true);
					},
					function(xhr, st, err) {
						service.error("editProc\n"+err.message+"\n"+xhr.responseText);
					}
				);
			});
		}
		else {
			linkProc(button, data);
		}
	}
	//リンククリック時の制御
	function linkProc(link, data){
		var accesskey = $(link).attr("accesskey");
		var target = $(link).attr("target");
		var alt = $(link).attr("alt");
		var windowName  = $(link).attr("windowName");
		var field = $(link).attr("name");
		var val = $(link).text();
		if(util.isEmpty(val) && !util.isEmpty($(link).val())) val = $(link).val();
		_isUpdate = true;

		if(!util.isEmpty(alt) && alt=="newadd") _isUpdate = false;
		switch(accesskey.toLowerCase()){
			case "listinit":
				_listInfo["page"] = 0;
				//var _req = service.extendRequestJson({"query_code" : target}, data);
				var _req = {"query_code" : target, "PID" : data["ID"], "PPID" : data["PID"]};
				listInit("listTable",_req, false, function(){
					var _title = $(".content-title").html();
					_title = _title.replace("#param#", val);
					for(var key in data){
						_title = _title.replace("#"+key+"#", data[key]);
					}
					$(".content-title").html(_title);
				});
				break;
			case "dialog":
				var pagecode = _currentRequest["query_code"]+"_"+target;
				showEditPage(_dialogId, pagecode,_currentRequest["query_code"], data, _isUpdate);
				break;
			case "subpage":
				var pagecode = _currentRequest["query_code"]+"_"+target;
				showEditPage(_editPageId, pagecode,_currentRequest["query_code"], data, _isUpdate);
				break;
			case "message":
				if(!util.isEmpty(target) && !util.isEmpty(alt)){
					service.alert(data[target],data[alt]);
				}
				break;
			case "filedownload":
				if(!util.isEmpty(alt) && data[alt]) val=data[alt];
				service.fileDownload("/download/"+val);
				break;
			case "getfile":
				if(!util.isEmpty(alt) && data[alt]) val=data[alt];
				window.open("/getfile/"+val);
				break;
		}
	}
	//編集画面表示
	function showEditPage(form_id, pagecode, querycode, data, isEdit){
		//編集値取得→編集画面表示→フォームセット
		var _req = service.extendRequestJson({}, data);
		_cache["_url"]["showPage"] = {};
		_cache["_url"]["showPage"]["sf"] = form_id;
		_cache["_url"]["showPage"]["sp"] = pagecode;
		 _cache["_url"]["showPage"]["su"] = "";
		if(isEdit) _cache["_url"]["showPage"]["su"] ="1";
		_isUpdate = isEdit;
		if(util.isEmpty(querycode) || util.isEmpty(_req["ID"])){
			showPage(form_id, pagecode, data, function(){
				pageOpen();
			});
		}
		else {
			service.getAjax(true, "/getedit/"+querycode+"/"+_req["ID"], {},
				function(result, st, xhr) {
					var data =  result["data"][0];
					showPage(form_id, pagecode, data, function(){
						pageOpen();
						autoSave();
					});
				},
				function(xhr, st, err) {
					service.error("showEditPage\n"+err.message+"\n"+xhr.responseText);
				}
			);
		}
		/*
		else {
			service.getAjax(true, "/getpage/"+pagecode+"/edit/"+_req["ID"], {},
				function(result, st, xhr) {
					var data =  result["data"][0];
					var page =  result["page"][0];
					_isUpdate = isEdit;
					paramPageLoad(form_id, page, function(){
						dom.setEditForm(data, form_id, _isUpdate);
						pageOpen();
						autoSave();
					});
				},
				function(xhr, st, err) {
					service.error("showEditPage\n"+err.message+"\n"+xhr.responseText);
				}
			);
		}
		*/
	}

	function pageOpen(form_id){
		if(util.isEmpty(form_id)) form_id = _currentForm;
		if($("#"+form_id).hasClass("modal")){
			$("#"+form_id).modal('show');
		}
		else {
			$("#"+form_id).show();
			$("#main").hide();
		}
		$(':focus').blur();
	}
	function pageClose(form_id){
		if(util.isEmpty(form_id)) form_id = _currentForm;
		if(util.isEmpty(form_id)) return;
		if(form_id=="main") return;
		delete _cache["_url"]["showPage"];
		if($("#"+form_id).hasClass("modal")){
			$("#"+form_id).modal('hide');
		}
		else if($("#"+form_id).hasClass("collapse")){

		}
		else {
			$("#"+form_id).hide();
			$("#main").show();
		}
	}
	function saveProc(type, query_code, callback, noConfirm){
		var msg = "INS";
		if(type=="upd") {
			msg = "SAVE";
		}
		if(!front.validateFormValue(_currentForm)) return false;
		var _callback = function(){
			var _req = front.getFormValue(_currentForm);
			_isImport = false;
			service.postAjax("/save/"+type+"/"+query_code, _req,
				function(result, st, xhr) {
					if(callback && $.type(callback)=="function") callback(result);
				},
				function(xhr, st, err) {
					service.error("service.postAjax\n"+err.message+"\n"+xhr.responseText);
				}
			);
		};
		if(noConfirm){
			_callback();
		}
		else {
			service.confirm("C_POST_"+msg ,"",  _callback);
		}
	}
	function _saveProc(callback){
		if(!front.validateFormValue(_currentForm)) return false;
		var type = "ins";
		if(_isUpdate) {
			type="upd";
		}
		saveProc(type, _currentRequest["query_code"], callback);
	}
	/*保存処理後の再表示*/
	function _savedReload(){
		pageClose(_currentForm);
		listRefresh(true);
		/*
		window.history.back();
		*/
	}
	function autoSave(){
		if(_saveTimer!=null){
			clearTimeout(_saveTimer);
		}
		var _req = front.getFormValue(_currentForm);
		util.setLocalData("autosave", _req);
		_saveTimer = setTimeout(autoSave, 10000);
	}
	function autoSaveDispose(){
		if(_saveTimer!=null){
			clearTimeout(_saveTimer);
		}
		util.setLocalData("autosave", {});
	}
	function getTreeStatus(){
		var ta = "";
		$(".nav-link.active").each(function(t){
			var id = $(this).attr("id");
			ta += id+",";
		});
		var to = "";
		$(".nav-item.menu-open>.nav-link").each(function(t){
			var id = $(this).attr("id");
			to += id+",";
		});
		return {"ta" : ta, "to" : to};
	}
	//検索処理
	function _searchProc(data){
		var _param = $.extend({}, data);
		var _req = $.extend({}, _currentRequest);
		var _req = service.extendRequestJson({}, _currentRequest);
		_req = service.extendRequestJson(_req, data, true);
		_listInfo["page"] = 0;
		listInit("listTable",_req);
	}
	//検索ダイアログからの検索処理
	function searchProc(){
		var _req = front.getFormValue(_currentForm);
		_searchProc(_req);
		pageClose();
	}

	root.base = $.extend({}, root.base, public_method);

})(this);
