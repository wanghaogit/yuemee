
/**
 * API调用器
 * @param object cfg
 * @returns Invoker
 */
function Invoker(cfg) {
	var config = {};
	if (cfg === undefined || cfg === null || typeof (cfg) !== 'object') {
		throw new Error('Invoker 需要配置');
	}
	if (cfg.applet_token === undefined || typeof (cfg.applet_token) !== 'string') {
		throw new Error('需要配置 applet_token');
	}
	config.applet_token = cfg.applet_token;

	if (cfg.url === undefined || typeof (cfg.url) !== 'string') {
		throw new Error('需要配置 url');
	}
	config.url = cfg.url;

	if (cfg.udid === undefined || typeof (cfg.udid) !== 'string') {
		config.udid = '000000000000000000000000';
	} else {
		config.udid = cfg.udid;
	}

	if (cfg.access_token === undefined) {
		throw new Error('需要配置 access_token');
	}
	if (typeof (cfg.access_token) === 'string') {
		config.access_token = cfg.access_token;
	} else if (typeof (cfg.access_token) === 'function') {
		config.access_token = cfg.access_token();
		if (typeof (config.access_token) !== 'string') {
			throw new Error('无法获取 access_token');
		} else {
			config.access_token = cfg.access_token;
		}
	}

	var _request_id = 0;
	this.invoke = function (module, action, params, onSuccess, onError) {
		var target = {
			module: module,
			action: action,
			id: ++_request_id
		};
		var request = {
			__udid: config.udid,
			__applet_token: config.applet_token,
			__access_token: (
					typeof (config.access_token) === 'string'
					? config.access_token
					: config.access_token()),
			__timestamp: new Date().format("yyyyMMddHHmmss"),
			__request_id: target.id
		};
		if (params !== undefined && params !== null) {
			for (var k in params) {
				if (k === 'file')
					throw new Error('附加参数名不能是 file');
				if (k === '__udid')
					throw new Error('附加参数名不能是 __udid');
				if (k === '__timestamp')
					throw new Error('附加参数名不能是 __timestamp');
				if (k === '__applet_token')
					throw new Error('附加参数名不能是 __applet_token');
				if (k === '__request_id')
					throw new Error('附加参数名不能是 __request_id');
				request[k] = params[k];
			}
		}
		$.ajax({
			url: config.url + '?call=' + module + '.' + action,
			type: 'POST',
			dataType: 'json',
//			contentType: "application/json; charset=utf-8",
			data: JSON.stringify(request),
			success: function (response) {
				if (response.__code === 'OK') {
					onSuccess(target, request, response);
				} else {
					onError(target, request, response);
				}
			}, error: function (x, s, e) {
				onError(target, request, {
					__code: 'E_NETWORK',
					__message: s
				});
			}
		});
	};

}

/**
 * 上传管理器
 * @param object cfg
 * @returns Uploader
 */
function Uploader(cfg) {
	var config = {
		url: null,
		udid: null,
		applet_token: null,
		access_token: null
	};
	if (cfg === undefined || cfg === null || typeof (cfg) !== 'object') {
		throw new Error('Invoker 需要配置');
	}
	if (cfg.applet_token === undefined || typeof (cfg.applet_token) !== 'string') {
		throw new Error('需要配置 applet_token');
	}
	config.applet_token = cfg.applet_token;

	if (cfg.url === undefined || typeof (cfg.url) !== 'string') {
		throw new Error('需要配置 url');
	}
	config.url = cfg.url;

	if (cfg.udid === undefined || typeof (cfg.udid) !== 'string') {
		config.udid = '000000000000000000000000';
	} else {
		config.udid = cfg.udid;
	}

	if (cfg.access_token === undefined) {
		throw new Error('需要配置 access_token');
	}
	if (typeof (cfg.access_token) === 'string') {
		config.access_token = cfg.access_token;
	} else if (typeof (cfg.access_token) === 'function') {
		config.access_token = cfg.access_token();
		if (typeof (config.access_token) !== 'string') {
			throw new Error('无法获取 access_token');
		} else {
			config.access_token = cfg.access_token;
		}
	}

	var _request_id = 0;

	this.create = function (id, params, onSuccess, onError) {
		var cfg = {
			picture: 'data:image/png;base64,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',
			width: 120,
			height: 120,
			css: {}
		};
		var target = document.getElementById(id);
		if (target === null) {
			throw new Error('初始化错误');
		}
		if (params !== undefined && typeof (params) === 'object') {
			for (var k in params) {
				if (k === '__width') {
					cfg.width = parseInt(params[k]);
				} else if (k === '__height') {
					cfg.height = parseInt(params[k]);
				} else if (k === '__picture') {
					cfg.picture = params[k];
				} else if (k === '__css') {
					var cg = params[k].split(';');
					for (var i = 0; i < cg.length; i++) {
						var ct = cg[i].split(':');
						if (ct.length >= 2) {
							cfg.css[ct[0].trim()] = ct[1].trim();
						}
					}
				}
			}
		}
		for (var n in cfg.css) {
			target.style[n] = cfg.css[n];
		}
		target.style.display = 'inline-block';
		target.style.width = cfg.width.toString() + 'px';
		target.style.height = cfg.height.toString() + 'px';
		target.style.position = 'relative';
		target.style.cursor = 'pointer';
		target.style.backgroundColor = 'white';
		target.style.backgroundRepeat = 'no-repeat';
		target.style.backgroundImage = "url('" + cfg.picture + "')";

		var progress = document.createElement('progress');
		progress.style.position = 'absolute';
		progress.style.display = 'none';
		progress.style.bottom = '3px';
		progress.style.left = '3px';
		progress.style.width = '90%';
		progress.min = 0;
		progress.max = 100;
		progress.value = 0;
		target.insertAdjacentElement('beforeEnd', progress);

		var input = document.createElement('input');
		input.type = 'file';
		input.accept = 'image/*';
		input.style.display = 'none';
		input.style.border = 'none';
		target.insertAdjacentElement('afterEnd', progress);

		var _target_is_busy = false;
		target.addEventListener('click', function () {
			if (_target_is_busy)
				return;
			input.click();
		}, false);

		input.addEventListener('change', function () {
			if (this.files.length < 1) {
				return;
			}
			if (this.files.length > 1) {
				onError(null, null, {
					__code: 'E_USER',
					__message: '每次只能上传一张图片'
				});
				return;
			}
			_target_is_busy = true;
			var arg_t = {};
			var arg_r = {
				__udid: config.udid,
				__timestamp: new Date().format("yyyyMMddHHmmss"),
				__applet_token: config.applet_token,
				__access_token: (typeof (config.access_token) === 'function'
						? config.access_token()
						: config.access_token),
				__request_id: ++_request_id
			};
			if (typeof (params) === 'object') {
				for (var k in params) {
					if (k === 'file')
						throw new Error('附加参数名不能是 file');
					if (k === '__udid')
						throw new Error('附加参数名不能是 __udid');
					if (k === '__timestamp')
						throw new Error('附加参数名不能是 __timestamp');
					if (k === '__applet_token')
						throw new Error('附加参数名不能是 __applet_token');
					if (k === '__request_id')
						throw new Error('附加参数名不能是 __request_id');
					arg_r[k] = params[k];
				}
			}
			var xhr = new XMLHttpRequest();
			arg_t.xhr = xhr;
			xhr.open('POST', config.url, true);
			xhr.onload = function (e) {
				_target_is_busy = false;
				input.value = '';
				progress.style.display = 'none';
				if (xhr.status !== 200) {
					onError(arg_t, arg_r, {
						__code: 'E_NETWORK',
						__message: '网络错误 ' + xhr.status
					});
					return;
				}
				var d = null;
				try {
					d = JSON.parse(xhr.responseText);
				} catch (ex) {
					onError(arg_t, arg_r, {
						__code: 'E_NETWORK',
						__message: '数据解析失败 ' + ex.message
					});
					return;
				}
				if (d.__code === undefined) {
					onError(arg_t, arg_r, {
						__code: 'E_PROTOCOL',
						__message: '协议错误'
					});
					return;
				} else if (d.__code !== 'OK') {
					onError(arg_t, arg_r, d);
					return;
				}
				onSuccess(arg_t, arg_r, d);
			};
			xhr.upload.addEventListener('progress', function (e) {
				progress.setAttribute('max', e.total);
				progress.setAttribute('value', e.loaded);
			}, false);
			progress.style.display = 'inline-block';
			var frm = new FormData();
			for (var k in arg_r) {
				frm.append(k, arg_r[k]);
			}
			frm.append('file', this.files[0]);
			xhr.send(frm);
		}, false);
	};
}
var YueMi = {
	API: {
		Admin: new Invoker({
			udid: '000000000000000000000000',
			url: '/api.php',
			applet_token: '9c8df78ed1fb8c55',
			access_token: function () {
				var m = /\bYMToken\=([a-z0-9]+)\b/i.exec(document.cookie);
				if (m && m.length > 0) {
					return m[1];
				}
				return '';
			}
		}),
		Open: new Invoker({
			udid: '861540038207471',
			url: 'https://a.yuemee.com/index.php',
			applet_token: '9c8df78ed1fb8c55',
			access_token: function () {
				var m = /\bYMToken\=([a-z0-9]+)\b/i.exec(document.cookie);
				if (m && m.length > 0) {
					return m[1];
				}
				return '';
			}
		}),
		Local: new Invoker({
			udid: '000000000000000000000000',
			url: 'http://a.ym.cn/index.php',
			applet_token: '9c8df78ed1fb8c55',
			access_token: function () {
				var m = /\bYMToken\=([a-z0-9]+)\b/i.exec(document.cookie);
				if (m && m.length > 0) {
					return m[1];
				}
				return '';
			}
		})
	},
	Upload: {
		Admin: new Uploader({
			udid: '000000000000000000000000',
			url: '/upload.php',
			applet_token: '9c8df78ed1fb8c55',
			access_token: function () {
				var m = /\bYMToken\=([a-z0-9]+)\b/i.exec(document.cookie);
				if (m && m.length > 0) {
					return m[1];
				}
				return '';
			}
		}),
		Open: new Uploader({
			udid: '000000000000000000000000',
			url: 'https://a.yuemee.com/upload.php',
			applet_token: '9c8df78ed1fb8c55',
			access_token: function () {
				var m = /\bYMToken\=([a-z0-9]+)\b/i.exec(document.cookie);
				if (m && m.length > 0) {
					return m[1];
				}
				return '';
			}
		}),
		Local: new Uploader({
			udid: '000000000000000000000000',
			url: 'http://a.ym.cn/upload.php',
			applet_token: '9c8df78ed1fb8c55',
			access_token: function () {
				var m = /\bYMToken\=([a-z0-9]+)\b/i.exec(document.cookie);
				if (m && m.length > 0) {
					return m[1];
				}
				return '';
			}
		})
	}
};

