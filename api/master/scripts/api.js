
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
 * 阅米本地数据结构
 * @type type
 */
var YueMi = {
	/**
	 * 阅米API调用入口
	 * @type Invoker
	 */
	API: new Invoker({
		applet_token: '3d496ab662d08e75',
		url: 'https://a.yuemee.com/index.php',
		udid: '3d496ab662d08e75',
		access_token: function () {
			var m = /\buser_token\=([a-z0-9]+)\b/i.exec(document.cookie);
			if (m && m.length > 0) {
				return m[1];
			}
			return '';
		}
	}),
	Local: new Invoker({
		udid: '3d496ab662d08e75',
		url: 'https://a.yuemee.com/index.php',
		applet_token: '3d496ab662d08e75',
		access_token: function () {
			var m = /\bYMToken\=([a-z0-9]+)\b/i.exec(document.cookie);
			if (m && m.length > 0) {
				return m[1];
			}
			return '';
		}
	})
};
