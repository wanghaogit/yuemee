/* 
 * Ziima 前端框架
 */

/**********************************************************************************************************************
 * JQuery部分
 *********************************************************************************************************************/
(function($) {
	$.extend(String.prototype,{
		trim : function (){
			return this.replace(/(^\s*)|(\s*$)/g,'');  	
		},
		startsWith : function (prefix){
			return this.slice(0, prefix.length) === prefix;
		},
		endsWith : function(suffix) {
			return this.indexOf(suffix, this.length - suffix.length) !== -1;
		},
		getUrlParam : function (k){
			if(this.indexOf('?') < 0)
				return null;
			var r = new RegExp('(\\?|\\&)(' + k + ')(=([^&]*))?($|&)');
			var m = this.match(r);
			if(m === null)
				return null;
			if(m[4] === undefined)
				return null;
			return m[4];
		},
		setUrlParam : function (k,v){
			if(this.indexOf('?') < 0)
				return this + '?' + k + '=' + encodeURIComponent(v);
			var r = new RegExp('(\\?|\\&)(' + k + ')(=([^&]*))?($|&)');
			var m = this.match(r);
			if(m === null)
				return this + '&' + k + '=' + encodeURIComponent(v);
			return this.replace(r,'$1$2=' + encodeURIComponent(v) + '$5');
		},
		delUrlParam : function (k){
			if(this.indexOf('?') < 0)
				return this;
			var r = new RegExp('(\\?|\\&)(' + k + ')(=([^&]*))?($|&)');
			var m = this.match(r);
			if(m === null)
				return this;
			if(m[1] === '?')
				return this.replace(r,'$1');
			else
				return this.replace(r,'$5');
		}
	});

	//日期时间
	$.extend(Date.prototype, {
		format: function (fmt) {
			var o = {
				"M+": this.getMonth() + 1, //月份         
				"d+": this.getDate(), //日         
				"h+": this.getHours() % 12 === 0 ? 12 : this.getHours() % 12, //小时         
				"H+": this.getHours(), //小时         
				"m+": this.getMinutes(), //分         
				"s+": this.getSeconds(), //秒         
				"q+": Math.floor((this.getMonth() + 3) / 3), //季度         
				"S": this.getMilliseconds() //毫秒         
			};
			var week = {
				"0": "/u65e5",
				"1": "/u4e00",
				"2": "/u4e8c",
				"3": "/u4e09",
				"4": "/u56db",
				"5": "/u4e94",
				"6": "/u516d"
			};
			if (/(y+)/i.test(fmt)) {
				fmt = fmt.replace(RegExp.$1, (this.getFullYear() + "").substr(4 - RegExp.$1.length));
			}
			for (var k in o) {
				if (new RegExp("(" + k + ")").test(fmt)) {
					fmt = fmt.replace(RegExp.$1, (RegExp.$1.length == 1) ? (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)));
				}
			}
			return fmt;
		}
	});
	
	$.fn.extend({
		setUrlParamWithSelect : function (){
			var v = $(this).val();
			var k = $(this).attr('name');
			if(k === undefined || k === null)
				return;
			if(v === '' || v === undefined || v === null)
				window.location.href = window.location.href.delUrlParam(k);
			else
				window.location.href = window.location.href.setUrlParam(k,v);
		},
		createRegionSelector : function(configs){
			"use strict";
			configs = configs || {
				dataSource : '/scripts/region_data.json',	//数据源
				disabled : [820000,810000,710000],			//禁止选择区域，默认港澳台
				level : 'any',								//选择类型：province = 省/ city = 市 / country = 县 / any = 任意
				tip : '请选择地区'
			};
			if(typeof(configs.dataSource) === 'undefined')
				configs.dataSource = '/scripts/region_data.json';
			if(typeof(configs.disabled) === 'undefined')
				configs.disabled = [820000,810000,710000];
			if(typeof(configs.level) === 'undefined')
				configs.level = 'any';
			if(typeof(configs.tip) === 'undefined')
				configs.tip = '请选择地区';
			
			var target_id = $(this).attr('id');
			var target_value = $(this).val();
			if(! /^[1-9]\d{5}$/.test(target_value))
				target_value = '0';
			switch(configs.level.toString().toLowerCase()){
				case 'province':
				case 'p':
				case '1':
					if(target_value.length === 6)
						target_value = target_value.substring(0,2) + '0000';
					configs.level = 1;
					break;
				case 'city':
				case 'c':
				case '2':
					if(target_value.length === 6)
						target_value = target_value.substring(0,4) + '00';
					configs.level = 2;
					break;
				case 'country':
				case 'u':
				case 'd':
				case '3':
					configs.level = 3;
					break;
				case 'any':
				case '0':
				case '':
					configs.level = 0;
					break;
				default:
					alert('配置错误：level = ' + configs.level);
					return;
			}
			$(this).attr('type','hidden');
			target_value = parseInt(target_value);
			$(this).val(target_value);
			//建立各种组件
			$(this).after(
				'<div id="REGION_SELECTOR_BOX_' + target_id + '" class="input-region" style="display:inline-block;background-color:#FCFCFC;background-repeat:no-repeat;background-position:2px,2px;border:solid 1px #C0C0C0;padding:1px 10px 1px 20px;font-size:12px;height:22px;line-height:22px;cursor:pointer;width:auto;"></div>' +
				'<div id="REGION_SELECTOR_POPUP_' + target_id + '" style="position:absolute;z-index:9999;width:320px;height:140px;background-color:#FCFCFC;border:solid 1px #C0C0C0;padding:5px;box-shadow:2px 2px 2px rgba(0,0,0,.4);display:none;">' +
				'<ul class="SimpleTabPages">' +
					'<li id="REGION_SELECTOR_TABP_' + target_id + '" class="current">省份</li>' +
					'<li id="REGION_SELECTOR_TABC_' + target_id + '" style="display:none;">地市</li>' +
					'<li id="REGION_SELECTOR_TABD_' + target_id + '" style="display:none;">区县</li>' +
					'<li id="REGION_SELECTOR_CANCEL_' + target_id + '" style="float:right;">关闭</li>' +
				'</ul>' +
				'<div id="REGION_SELECTOR_CNTP_' + target_id + '"></div>' +
				'<div id="REGION_SELECTOR_CNTC_' + target_id + '" style="display:none;"></div>' +
				'<div id="REGION_SELECTOR_CNTD_' + target_id + '" style="display:none;"></div>' +
				'</div>'
			);
			//关闭按钮
			$('#REGION_SELECTOR_CANCEL_' + target_id).click(function(){
				$('#REGION_SELECTOR_POPUP_' + target_id).hide();
			});
			//弹出选择
			$('#REGION_SELECTOR_BOX_' + target_id).click(function(){
				$('#REGION_SELECTOR_POPUP_' + target_id)
						.css('left',$(this).position().left + 'px')
						.css('top',$(this).position().top + 27 + 'px')
						.toggle();
			});
			$('#REGION_SELECTOR_TABP_' + target_id).click(function (){
				$('#REGION_SELECTOR_TABP_' + target_id).addClass('current');
				$('#REGION_SELECTOR_TABC_' + target_id).removeClass('current');
				$('#REGION_SELECTOR_TABD_' + target_id).removeClass('current');
				$('#REGION_SELECTOR_CNTP_' + target_id).show();
				$('#REGION_SELECTOR_CNTC_' + target_id).hide();
				$('#REGION_SELECTOR_CNTD_' + target_id).hide();
			});

			$('#REGION_SELECTOR_TABC_' + target_id).click(function (){
				var pid = $(this).attr('data-id');
				if(pid === undefined || pid === null || ! /^[1-9]\d{5}$/.test(pid)){
					return;
				}
				$('#REGION_SELECTOR_TABP_' + target_id).removeClass('current');
				$('#REGION_SELECTOR_TABC_' + target_id).addClass('current');
				$('#REGION_SELECTOR_TABD_' + target_id).removeClass('current');
				$('#REGION_SELECTOR_CNTP_' + target_id).hide();
				$('#REGION_SELECTOR_CNTC_' + target_id).show();
				$('#REGION_SELECTOR_CNTD_' + target_id).hide();
			});
			if(window.__RegionData){
				$('#' + target_id).__reload_region_popup(configs);
			}else{
				$.ajax({
					url : configs.dataSource,
					type : 'GET',
					dataType : 'json',
					success : function(data){
						window.__RegionData = data;
						$('#' + target_id).__reload_region_popup(configs);
					},error : function(){

					}
				});
			}
		},
		__reload_region_popup : function(configs){
			var target_id = $(this).attr('id');
			var target_value = parseInt($(this).val());
			var phtml = '<ul class="SimpleList">';
			for(var pid in window.__RegionData){
				if($.inArray(parseInt(pid),configs.disabled) >= 0){
					phtml += '<li id="REGION_SELECTOR_ITMP_' + target_id + '_' + pid + '" class="disabled">' + window.__RegionData[pid].name + '</li>';
				}else if(parseInt(pid / 10000) === parseInt(target_value / 10000)){
					phtml += '<li id="REGION_SELECTOR_ITMP_' + target_id + '_' + pid + '" data-id="' + pid + '" class="REGION_SELECTOR_ITMP_' + target_id + ' selected">' + window.__RegionData[pid].name + '</li>';
				}else{
					phtml += '<li id="REGION_SELECTOR_ITMP_' + target_id + '_' + pid + '" data-id="' + pid + '" class="REGION_SELECTOR_ITMP_' + target_id + '">' + window.__RegionData[pid].name + '</li>';
				}
			}
			phtml += '</ul>';
			$('#REGION_SELECTOR_CNTP_' + target_id).html(phtml);
			$('.REGION_SELECTOR_ITMP_' + target_id).click(function(){
				var pid = $(this).attr('data-id');
				if(configs.level === 1){
					$('.REGION_SELECTOR_ITMP_' + target_id).removeClass('selected');
					$(this).addClass('selected');
					$('#' + target_id).val(pid);
					$('#REGION_SELECTOR_BOX_' + target_id).text($(this).text());
					$('#REGION_SELECTOR_POPUP_' + target_id).hide();
					return;
				}
				var sid = $('#REGION_SELECTOR_TABC_' + target_id).attr('data-id');
				$('#REGION_SELECTOR_TABP_' + target_id).removeClass('current');
				$('#REGION_SELECTOR_TABC_' + target_id).addClass('current').show();
				$('#REGION_SELECTOR_CNTP_' + target_id).hide();
				$('#REGION_SELECTOR_CNTC_' + target_id).show();
				$('.REGION_SELECTOR_ITMP_' + target_id).removeClass('selected');
				$(this).addClass('selected');
				if(pid !== sid){
					$('#REGION_SELECTOR_TABC_' + target_id).attr('data-id',pid);
					//加载城市列表
					var chtml = '<ul class="SimpleList">';
					if(configs.level === 0){
						chtml += '<li id="REGION_SELECTOR_ITMC_' + target_id + '_0" data-id="' + pid + '" class="REGION_SELECTOR_ITMC_' + target_id + ' fa fa-folder-o"> 选择全' + window.__RegionData[pid].name + '</li>';
					}
					for(var cid in window.__RegionData[pid].citys){
						if($.inArray(parseInt(cid),configs.disabled) >= 0){
							chtml += '<li id="REGION_SELECTOR_ITMC_' + target_id + '_' + cid + '" class="disabled">' + window.__RegionData[pid].citys[cid].name + '</li>';
						}else if(parseInt(cid / 100) === parseInt(target_value / 100)){
							chtml += '<li id="REGION_SELECTOR_ITMC_' + target_id + '_' + cid + '" data-id="' + cid + '" class="REGION_SELECTOR_ITMC_' + target_id + ' selected">' + window.__RegionData[pid].citys[cid].name + '</li>';
						}else{
							chtml += '<li id="REGION_SELECTOR_ITMC_' + target_id + '_' + cid + '" data-id="' + cid + '" class="REGION_SELECTOR_ITMC_' + target_id + '">' + window.__RegionData[pid].citys[cid].name + '</li>';
						}
					}
					chtml += '</ul>';
					$('#REGION_SELECTOR_CNTC_' + target_id).html(chtml);
					$('.REGION_SELECTOR_ITMC_' + target_id).click(function(){
						var pid = $('#REGION_SELECTOR_TABC_' + target_id).attr('data-id');
						var cid = $(this).attr('data-id');
						if(configs.level === 2){
							$('.REGION_SELECTOR_ITMC_' + target_id).removeClass('selected');
							$(this).addClass('selected');
							$('#' + target_id).val(cid);
							$('#REGION_SELECTOR_BOX_' + target_id).text(
								window.__RegionData[pid].name + '/' + $(this).text()
							);
							$('#REGION_SELECTOR_POPUP_' + target_id).hide();
							return;
						}else if($(this).attr('id') === 'REGION_SELECTOR_ITMC_' + target_id + '_0' && configs.level === 0){
							$('.REGION_SELECTOR_ITMC_' + target_id).removeClass('selected');
							$(this).addClass('selected');
							$('#' + target_id).val(cid);
							$('#REGION_SELECTOR_BOX_' + target_id).text(
								window.__RegionData[pid].name
							);
							$('#REGION_SELECTOR_POPUP_' + target_id).hide();
							return;
						}
						$('#REGION_SELECTOR_TABP_' + target_id).removeClass('current');
						$('#REGION_SELECTOR_TABC_' + target_id).removeClass('current');
						$('#REGION_SELECTOR_TABD_' + target_id).addClass('current').show();
						$('#REGION_SELECTOR_CNTP_' + target_id).hide();
						$('#REGION_SELECTOR_CNTC_' + target_id).hide();
						$('#REGION_SELECTOR_CNTD_' + target_id).show();
						$('.REGION_SELECTOR_ITMC_' + target_id).removeClass('selected');
						$(this).addClass('selected');
						var sid = $('#REGION_SELECTOR_TABD_' + target_id).attr('data-id');
						if(cid !== sid){
							$('#REGION_SELECTOR_TABD_' + target_id).attr('data-id',cid);
							var dhtml = '<ul class="SimpleList">';
							if(configs.level === 0){
								dhtml += '<li id="REGION_SELECTOR_ITMD_' + target_id + '_0" data-id="' + cid + '" class="REGION_SELECTOR_ITMD_' + target_id + ' fa fa-folder-o"> 选择全' + window.__RegionData[pid].citys[cid].name + '</li>';
							}
							for(var did in window.__RegionData[pid].citys[cid].countrys){
								if($.inArray(parseInt(did),configs.disabled) >= 0){
									dhtml += '<li id="REGION_SELECTOR_ITMD_' + target_id + '_' + did + '" class="disabled">' + window.__RegionData[pid].citys[cid].countrys[did] + '</li>';
								}else if(parseInt(did) === target_value){
									dhtml += '<li id="REGION_SELECTOR_ITMD_' + target_id + '_' + did + '" data-id="' + did + '" class="REGION_SELECTOR_ITMD_' + target_id + ' selected">' + window.__RegionData[pid].citys[cid].countrys[did] + '</li>';
								}else{
									dhtml += '<li id="REGION_SELECTOR_ITMD_' + target_id + '_' + did + '" data-id="' + did + '" class="REGION_SELECTOR_ITMD_' + target_id + '">' + window.__RegionData[pid].citys[cid].countrys[did] + '</li>';
								}
							}
							dhtml += '</ul>';
							$('#REGION_SELECTOR_CNTD_' + target_id).html(dhtml);
							$('.REGION_SELECTOR_ITMD_' + target_id).click(function(){
								var pid = $('#REGION_SELECTOR_TABC_' + target_id).attr('data-id');
								var cid = $('#REGION_SELECTOR_TABD_' + target_id).attr('data-id');
								var did = $(this).attr('data-id');
								$('.REGION_SELECTOR_ITMD_' + target_id).removeClass('selected');
								$(this).addClass('selected');
								if($(this).attr('id') === 'REGION_SELECTOR_ITMD_' + target_id + '_0' && configs.level === 0){
									$('#' + target_id).val(cid);
									$('#REGION_SELECTOR_BOX_' + target_id).text(
										window.__RegionData[pid].name + '/' +
										window.__RegionData[pid].citys[cid].name
									);
									$('#REGION_SELECTOR_POPUP_' + target_id).hide();
									return;
								}
								$('#' + target_id).val(did);
								$('#REGION_SELECTOR_BOX_' + target_id).text(
									window.__RegionData[pid].name + '/' +
									window.__RegionData[pid].citys[cid].name + '/' +
									$(this).text()
								);
								$('#REGION_SELECTOR_POPUP_' + target_id).hide();
							});
						}
					});
				}
			});
			//同步显示内容
			if(target_value === 0){
				$('#REGION_SELECTOR_BOX_' + target_id).text(configs.tip);
			}else{
				var pid = Math.floor(target_value / 10000) * 10000;
				var cid = Math.floor(target_value / 100) * 100;
				var did = target_value;
				var t = window.__RegionData[pid].name;
				if(cid !== pid){
					t += '/' + window.__RegionData[pid].citys[cid].name;
				}
				if(did !== cid){
					t += '/' + window.__RegionData[pid].citys[cid].countrys[did];
				}
				$('#REGION_SELECTOR_BOX_' + target_id).text(t);
			}
		}
	});
})(jQuery);

/**********************************************************************************************************************
 * sprintf
 *********************************************************************************************************************/
(function(window) {
    var re = {
        not_string: /[^s]/,
        number: /[dief]/,
        text: /^[^\x25]+/,
        modulo: /^\x25{2}/,
        placeholder: /^\x25(?:([1-9]\d*)\$|\(([^\)]+)\))?(\+)?(0|'[^$])?(-)?(\d+)?(?:\.(\d+))?([b-fiosuxX])/,
        key: /^([a-z_][a-z_\d]*)/i,
        key_access: /^\.([a-z_][a-z_\d]*)/i,
        index_access: /^\[(\d+)\]/,
        sign: /^[\+\-]/
    };

    function sprintf() {
        var key = arguments[0], cache = sprintf.cache;
        if (!(cache[key] && cache.hasOwnProperty(key))) {
            cache[key] = sprintf.parse(key);
        }
        return sprintf.format.call(null, cache[key], arguments);
    }

    sprintf.format = function(parse_tree, argv) {
        var cursor = 1, tree_length = parse_tree.length, node_type = "", arg, output = [], i, k, match, pad, pad_character, pad_length, is_positive = true, sign = "";
        for (i = 0; i < tree_length; i++) {
            node_type = get_type(parse_tree[i]);
            if (node_type === "string") {
                output[output.length] = parse_tree[i];
            }
            else if (node_type === "array") {
                match = parse_tree[i]; // convenience purposes only
                if (match[2]) { // keyword argument
                    arg = argv[cursor];
                    for (k = 0; k < match[2].length; k++) {
                        if (!arg.hasOwnProperty(match[2][k])) {
                            throw new Error(sprintf("[sprintf] property '%s' does not exist", match[2][k]));
                        }
                        arg = arg[match[2][k]];
                    }
                }
                else if (match[1]) { // positional argument (explicit)
                    arg = argv[match[1]];
                }
                else { // positional argument (implicit)
                    arg = argv[cursor++];
                }

                if (get_type(arg) === "function") {
                    arg = arg();
                }

                if (re.not_string.test(match[8]) && (get_type(arg) !== "number" && isNaN(arg))) {
                    throw new TypeError(sprintf("[sprintf] expecting number but found %s", get_type(arg)));
                }

                if (re.number.test(match[8])) {
                    is_positive = arg >= 0;
                }

                switch (match[8]) {
                    case "b":
                        arg = arg.toString(2);
                    break;
                    case "c":
                        arg = String.fromCharCode(arg);
                    break;
                    case "d":
                    case "i":
                        arg = parseInt(arg, 10);
                    break;
                    case "e":
                        arg = match[7] ? arg.toExponential(match[7]) : arg.toExponential();
                    break;
                    case "f":
                        arg = match[7] ? parseFloat(arg).toFixed(match[7]) : parseFloat(arg);
                    break;
                    case "o":
                        arg = arg.toString(8);
                    break
                    case "s":
                        arg = ((arg = String(arg)) && match[7] ? arg.substring(0, match[7]) : arg);
                    break;
                    case "u":
                        arg = arg >>> 0;
                    break
                    case "x":
                        arg = arg.toString(16);
                    break;
                    case "X":
                        arg = arg.toString(16).toUpperCase();
                    break
                }
                if (re.number.test(match[8]) && (!is_positive || match[3])) {
                    sign = is_positive ? "+" : "-";
                    arg = arg.toString().replace(re.sign, "");
                }
                else {
                    sign = "";
                }
                pad_character = match[4] ? match[4] === "0" ? "0" : match[4].charAt(1) : " ";
                pad_length = match[6] - (sign + arg).length;
                pad = match[6] ? (pad_length > 0 ? str_repeat(pad_character, pad_length) : "") : "";
                output[output.length] = match[5] ? sign + arg + pad : (pad_character === "0" ? sign + pad + arg : pad + sign + arg);
            }
        }
        return output.join("");
    };

    sprintf.cache = {};

    sprintf.parse = function(fmt) {
        var _fmt = fmt, match = [], parse_tree = [], arg_names = 0;
        while (_fmt) {
            if ((match = re.text.exec(_fmt)) !== null) {
                parse_tree[parse_tree.length] = match[0];
            }
            else if ((match = re.modulo.exec(_fmt)) !== null) {
                parse_tree[parse_tree.length] = "%";
            }
            else if ((match = re.placeholder.exec(_fmt)) !== null) {
                if (match[2]) {
                    arg_names |= 1;
                    var field_list = [], replacement_field = match[2], field_match = [];
                    if ((field_match = re.key.exec(replacement_field)) !== null) {
                        field_list[field_list.length] = field_match[1];
                        while ((replacement_field = replacement_field.substring(field_match[0].length)) !== "") {
                            if ((field_match = re.key_access.exec(replacement_field)) !== null) {
                                field_list[field_list.length] = field_match[1];
                            }
                            else if ((field_match = re.index_access.exec(replacement_field)) !== null) {
                                field_list[field_list.length] = field_match[1];
                            }
                            else {
                                throw new SyntaxError("[sprintf] failed to parse named argument key");
                            }
                        }
                    }
                    else {
                        throw new SyntaxError("[sprintf] failed to parse named argument key");
                    }
                    match[2] = field_list;
                }
                else {
                    arg_names |= 2;
                }
                if (arg_names === 3) {
                    throw new Error("[sprintf] mixing positional and named placeholders is not (yet) supported");
                }
                parse_tree[parse_tree.length] = match;
            }
            else {
                throw new SyntaxError("[sprintf] unexpected placeholder");
            }
            _fmt = _fmt.substring(match[0].length);
        }
        return parse_tree;
    };

    var vsprintf = function(fmt, argv, _argv) {
        _argv = (argv || []).slice(0);
        _argv.splice(0, 0, fmt);
        return sprintf.apply(null, _argv);
    };

    /**
     * helpers
     */
    function get_type(variable) {
        return Object.prototype.toString.call(variable).slice(8, -1).toLowerCase();
    }

    function str_repeat(input, multiplier) {
        return Array(multiplier + 1).join(input);
    }

    /**
     * export to either browser or node.js
     */
    if (typeof exports !== "undefined") {
        exports.sprintf = sprintf;
        exports.vsprintf = vsprintf;
    }
    else {
        window.sprintf = sprintf;
        window.vsprintf = vsprintf;

        if (typeof define === "function" && define.amd) {
            define(function() {
                return {
                    sprintf: sprintf,
                    vsprintf: vsprintf
                };
            });
        }
    }
})(typeof window === "undefined" ? this : window);
