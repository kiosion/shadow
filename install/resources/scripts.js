// On DOM ready
$(document).ready(() => {
	// Step 3
	if (window.location.href.indexOf('/setup/3') > -1) {
		let res = check_env();
		if (res.status == 'success') {
			// Fill in form with env values
			$('#db-host').val(res.data.DB_HOST);
			$('#db-user').val(res.data.DB_USER);
			$('#db-pass').val(res.data.DB_PASS);
			$('#db-port').val(res.data.DB_PORT);
			// Change button text to 'Continue'
			$('#db-creds-check').text('Continue');
			btn_state('db-creds-check', true);
		}
		// On keyup of any form field, check if fields are valid
		$(document).on('keyup', '#db-creds-setup input', (e) => {
			validate_db_creds();
		});
		// Handle db credentials checking
		$(document).on('click', '#db-creds-check', (e) => {
			check_db_creds(e);
		});
	}
	// Step 4
	if (window.location.href.indexOf('/setup/4') > -1) {
		// Check stuff
		let envres = check_env();
		if (envres.status != 'success') {
			$('#db-setup-error').removeClass('d-none');
			$('#db-setup-error').text('Fatal error: Couldn\'t find .ENV file from previous step. You may need to manually create it with proper permissions.');
		}
		else {
			check_db();
			check_tables();
		}
		// On keyup of #db-table-setup input
		$('#db-table-setup').on('keyup', () => {
			validate_db_setup();
		});
		// On click of 'create' button for database name
		$(document).on('click', '#db-name-create', (e) => {
			create_db(e);
		});
		// On click of 'create' button for tables
		$(document).on('click', '#db-tables-create', (e) => {
			create_db_tables(e);
		});
		// On click of 'continue' button
		$(document).on('click', '#continue-btn', (e) => {
			e.preventDefault();
			if ($('#continue-btn').hasClass('disabled')) {
				return;
			}
			if ($('#db-name-create').text() == 'Created' && $('#db-tables-create').text() == 'Created') {
				window.location.href = '/setup/5';
			}
		});
	}
	// Step 5
	if (window.location.href.indexOf('/setup/5') > -1) {
		// Check stuff
		let envres = check_env();
		if (envres.status != 'success') {
			$('#admin-setup-error').removeClass('d-none');
			$('#admin-setup-error').text('Fatal error: Couldn\'t find .ENV file from previous step. You may need to manually create it with proper permissions.');
		}
		else {
			// Check if admin user already exists
			check_admin();
		}
		// On keyup of #admin-setup input
		$('#admin-setup').on('keyup', () => {
			validate_admin_setup();
		});
		// On click of 'continue' button
		$(document).on('click', '#continue-btn', (e) => {
			e.preventDefault();
			if ($('#continue-btn').hasClass('disabled')) {
				return;
			}
			else if ($('#continue-btn').text() == 'Create') {
				create_admin(e);
			}
			else if ($('#continue-btn').text() == 'Continue') {
				window.location.href = '/setup/6';
			}
		});
	}
	// Step 6
	if (window.location.href.indexOf('/setup/6') > -1) {
		// On keyup of #app-config-setup input
		$(document).on('keyup', '#app-config-setup', () => {
			validate_app_config();
		});
		// On click of 'continue' button
		$(document).on('click', '#continue-btn', () => {
			if ($('#continue-btn').hasClass('disabled')) {
				return;
			}
			else if ($('#continue-btn').text() == 'Create app config') {
				create_app_config();
			}
			else if ($('#continue-btn').text() == 'Continue') {
				window.location.href = '/setup/7';
			}
		});
	}
	// Step 7
	if (window.location.href.indexOf('/setup/7') > -1) {
		// On click of 'continue' button
		$(document).on('click', '#continue-btn', () => {
			finish_setup();
		});
	}
});

function create_db() {
	if ($('#db-name-create').text() == 'Created') {
		return;
	}
	validate_db_setup();
	if (!($('#db-name').hasClass('is-invalid')) && $('#db-name').val().length > 0 && /^[a-zA-Z0-9_-]+$/.test($('#db-name').val())) {
		$.ajax({
			type: 'POST',
			url: '/setup/create/db/',
			data: {
				db_name: $('#db-name').val()
			}
		}).done((res) => {
			if (JSON.parse(res).status == 'success') {
				console.log('Create: Database created');
				btn_state('db-name-create', false);
				$('#db-name-create').text('Created');
				$('#db-name').val(JSON.parse(res).data.split('"')[1]);
				$('#db-name').attr('disabled', true);
				btn_state('db-tables-create', true);
			}
			else {
				console.error('Create: Database creation failed');
				$('#db-setup-error').removeClass('d-none');
				$('#db-setup-error').text('Fatal error: Couldn\'t create database. This may be an issue with your .ENV file or credentials.');
			}
		});
	}
}

function create_db_tables() {
	if ($('#db-tables-create').text() == 'Created') {
		return;
	}
	validate_db_setup();
	if (!($('#db-prefix').hasClass('is-invalid')) && $('#db-prefix').val().length > 0 && /^[a-zA-Z0-9_-]+$/.test($('#db-prefix').val())) {
		$.ajax({
			type: 'POST',
			url: '/setup/create/tables/',
			data: {
				db_prefix: $('#db-prefix').val()
			}
		}).done((res) => {
			res = JSON.parse(res);
			if (res.status == 'success') {
				console.log('Create: Tables created');
				btn_state('db-tables-create', false);
				$('#db-tables-create').text('Created');
				btn_state('db-name-create', false);
				$('#db-name-create').text('Created');
				$('#db-prefix').attr('disabled', true);
				btn_state('continue-btn', true);
			}
			else {
				console.error('Create: Tables creation failed');
				$('#db-setup-error').removeClass('d-none');
				$('#db-setup-error').text('Fatal error: Couldn\'t create tables. This may be an issue with your .ENV file or credentials.');
			}
		});
	}
}

function create_admin() {
	validate_admin_setup();
	if (!($('#acc-un').hasClass('is-invalid')) && !($('#acc-pw').hasClass('is-invalid'))) {
		$.ajax({
			type: 'POST',
			url: '/setup/create/admin/',
			data: {
				acc_un: $('#acc-un').val(),
				acc_pass: $('#acc-pass').val()
			}
		}).done((res) => {
			res = JSON.parse(res);
			console.log(res);
			if (res.status == 'success') {
				console.log('Create: Account created');
				$('#acc-un').attr('disabled', true);
				$('#acc-pass').attr('disabled', true);
				$('#acc-pass-c').attr('disabled', true);
				btn_state('continue-btn', true);
				$('#continue-btn').text('Continue');
			}
			else {
				console.error('Create: Admin creation failed');
				$('#admin-setup-error').removeClass('d-none');
				$('#admin-setup-error').text('Fatal error: Couldn\'t create account.');
			}
		});
	}
}

function create_app_config() {
	validate_app_config();
	if (!($('#app-name').hasClass('is-invalid')) && !($('#app-url').hasClass('is-invalid'))) {
		$.ajax({
			type: 'POST',
			url: '/setup/create/app-config/',
			data: {
				app_name: $('#app-name').val(),
				app_url: $('#app-url').val()
			}
		}).done((res) => {
			res = JSON.parse(res);
			console.log(res);
			if (res.status == 'success') {
				console.log('Create: App config created');
				$('#app-name').attr('disabled', true);
				$('#app-webroot').attr('disabled', true);
				$('#app-lang').attr('disabled', true);
				btn_state('continue-btn', true);
				$('#continue-btn').text('Continue');
			}
			else {
				console.error('Create: App config creation failed');
				$('#app-config-setup-error').removeClass('d-none');
				$('#app-config-setup-error').text('Fatal error: Couldn\'t create app config file.');
			}
		});
	}
}

function check_env() {
	let data;
	$.ajax({
		type: 'POST',
		async: false,
		url: '/setup/check/env/'
	}).done((res) => {
		res = JSON.parse(res);
		if (res.status == 'success') {
			console.log('Check: ENV file exists');
		}
		else {
			console.error('Check: ENV file does not exist');
		}
		data = res;
	});
	return data;
}

function check_db() {
	$.ajax({
		type: 'POST',
		url: '/setup/check/db/'
	}).done((res) => {
		res = JSON.parse(res);
		if (res.status == 'success') {
			if (res.data == true) {
				console.log('Check: Database exists');
				btn_state('db-name-create', false);
				$('#db-name-create').text('Created');
				btn_state('db-tables-create', true);
				$('#db-name').val(res.msg.split('"')[1]);
				$('#db-name').attr('disabled', true);
			}
			else {
				console.log('Check: Database does not exist');
			}
			return;
		}
		else {
			console.error('Check: Database check failed');
			$('#db-setup-error').removeClass('d-none');
			$('#db-setup-error').text('Fatal error: Couldn\'t check database status. This may be an issue with your .ENV file or credentials.');
		}
	});
}

function check_tables() {
	$.ajax({
		type: 'POST',
		url: '/setup/check/tables/'
	}).done((res) => {
		res = JSON.parse(res);
		if (res.status == 'success') {
			if (res.data == true) {
				console.log('Check: Tables exist');
				btn_state('db-tables-create', false);
				$('#db-tables-create').text('Created');
				$('#db-prefix').val(res.msg.split('"')[1]);
				$('#db-prefix').attr('disabled', true);
				btn_state('continue-btn', true);
			}
			else {
				console.log('Check: Tables do not exist');
			}
		}
		else {
			console.error('Check: Tables check failed');
			$('#db-setup-error').removeClass('d-none');
			$('#db-setup-error').text('Fatal error: Couldn\'t check table status. This may be an issue with your .ENV file or credentials.');
		}
	});
}

function check_db_creds(e) {
	let host = $('#db-host').val();
	let user = $('#db-user').val();
	let pass = $('#db-pass').val();
	let port = $('#db-port').val();
	if ($(e.target).hasClass('disabled')) {
		return;
	}
	if ($(e.target).text() == 'Continue') {
		window.location.href = '/setup/4/';
		return;
	}
	if ($(e.target).text() == 'Add to .env') {
		$.ajax({
			type: 'POST',
			url: "/setup/create/env/",
			data: {
				db_host: host,
				db_user: user,
				db_pass: pass,
				db_port: port,
			}
		}).done((res) => {
			res = JSON.parse(res);
			if (res.status == 'success') {
				$(e.target).text('Continue');
				$('#db-creds-success').removeClass('d-none');
				$('#db-creds-success').text('Successfully added to .env file');
			}
			else {
				console.error(res.msg);
				btn_state('db-creds-check', false);
				$('#db-creds-success').addClass('d-none');
				$('#db-creds-error').removeClass('d-none');
				$('#db-creds-error').text('Error: Couldn\'t create .ENV file. You may need to manually create it with proper permissions.');
			}
		});
		return;
	}
	$.ajax({
		type: 'POST',
		url: '/setup/check/creds/',
		data: {
			db_host: host,
			db_user: user,
			db_pass: pass,
			db_port: port,
		}
	}).done((res) => {
		res = JSON.parse(res);
		if (res.status == 'success') {
			console.log('Valid: ' + res.data);
			$('#db-creds-error').addClass('d-none');
			$('#db-creds-success').removeClass('d-none');
			$('#db-creds-success').text('Success: Credentials are valid.');
			$(e.target).text('Add to .env');
		}
		else {
			console.warn('Invalid: ' + res.msg);
			$('#db-creds-success').addClass('d-none');
			$('#db-creds-error').removeClass('d-none');
			$('#db-creds-error').text(res.msg);
			$(e.target).text('Retry');
		}
	});
}

function check_admin() {
	$.ajax({
		type: 'POST',
		url: '/setup/check/admin/'
	}).done((res) => {
		res = JSON.parse(res);
		if (res.status == 'success') {
			if (res.data == true) {
				console.log('Check: Account exists');
				btn_state('continue-btn', true);
				$('#continue-btn').text('Continue');
				$('#acc-un').val(res.msg.split('"')[1]);
				$('#acc-un').attr('disabled', true);
				$('#acc-pass').val('xxxxxxxxxxxx');
				$('#acc-pass').attr('disabled', true);
				$('#acc-pass-c').val('xxxxxxxxxxxx');
				$('#acc-pass-c').attr('disabled', true);
			}
			else {
				console.log('Check: Account does not exist');
			}
			return;
		}
		else {
			console.error('Check: Account check failed');
			$('#admin-setup-error').removeClass('d-none');
			$('#admin-setup-error').text('Fatal error: Couldn\'t check account status. This may be an issue with database credentials.');
		}
	});
}

function validate_db_creds() {
	let host = $('#db-host').val();
	let user = $('#db-user').val();
	let pass = $('#db-pass').val();
	let port = $('#db-port').val();

	if (host.indexOf(' ') > -1 || host.indexOf('_') > -1) {
		$('#db-host').addClass('is-invalid');
	}
	else if (host.length > 0) {
		$('#db-host').removeClass('is-invalid');
	}
	if (user.indexOf(' ') > -1) {
		$('#db-user').addClass('is-invalid');
	}
	else if (user.length > 0) {
		$('#db-user').removeClass('is-invalid');
	}
	if (isNaN(port)) {
		$('#db-port').addClass('is-invalid');
	}
	else if (port.length > 0) {
		$('#db-port').removeClass('is-invalid');
	}
	// If all fields are valid, remove 'disabled' from submit button
	if (!($('#db-host').hasClass('is-invalid')) && host.length > 0 &&
		!($('#db-user').hasClass('is-invalid')) && user.length > 0 &&
		!($('#db-pass').hasClass('is-invalid')) && pass.length > 0 &&
		!($('#db-port').hasClass('is-invalid')) && port.length > 0) {
		btn_state('db-creds-check', true);
		$('#db-creds-check').text('Check');
	}
	else {
		btn_state('db-creds-check', false);
	}
}

function validate_db_setup() {
	let db_name = $('#db-name').val();
	let db_prefix = $('#db-prefix').val();
	// Check db-name has no spaces or special characters
	if ($('#db-name-create').text() != 'Created') {
		if (!/^[a-zA-Z0-9_-]+$/.test(db_name) || db_name.length == 0) {
			$('#db-name').addClass('is-invalid');
			btn_state('db-name-create', false);
		}
		else if (db_name.length > 0) {
			$('#db-name').removeClass('is-invalid');
			btn_state('db-name-create', true);
		}
	}
	// Check db-prefix has no spaces or special characters and is less than 12 characters
	if (!/^[a-zA-Z0-9_-]+$/.test(db_prefix) || db_prefix.length > 12 || db_prefix.length == 0) {
		$('#db-prefix').addClass('is-invalid');
		btn_state('db-tables-create', false);
	}
	else if (db_prefix.length > 0 && $('#db-name-create').text() == 'Created') {
		$('#db-prefix').removeClass('is-invalid');
		$('#db-prefix-demo').text('(' + db_prefix + 'files, ' + db_prefix + 'users)');
		btn_state('db-tables-create', true);
	}
	else if (db_prefix.length > 0) {
		$('#db-prefix').removeClass('is-invalid');
		$('#db-prefix-demo').text('(' + db_prefix + 'files, ' + db_prefix + 'users)');
	}
	// If all fields are complete, remove 'disabled' from continue button
	if ($('#db-name-create').text() == 'Created' && $('#db-tables-create').text() == 'Created') {
		btn_state('continue-btn', true);
	}
}

function validate_admin_setup() {
	let acc_un = $('#acc-un').val();
	let acc_pass = $('#acc-pass').val();
	let acc_pass_c = $('#acc-pass-c').val();
	if (!/^[a-zA-Z0-9_-]+$/.test(acc_un) || acc_un.length == 0) {
		$('#acc-un').addClass('is-invalid');
		btn_state('continue-btn', false);
	}
	else if (acc_un.length > 0) {
		$('#acc-un').removeClass('is-invalid');
	}
	if (!/^[a-zA-Z0-9_-]+$/.test(acc_pass) || acc_pass.length == 0) {
		$('#acc-pass').addClass('is-invalid');
		btn_state('continue-btn', false);
	}
	else if (acc_pass.length > 0) {
		$('#acc-pass').removeClass('is-invalid');
	}
	if (acc_pass != acc_pass_c || acc_pass_c.length == 0) {
		$('#acc-pass-c').addClass('is-invalid');
		btn_state('continue-btn', false);
	}
	else if (acc_pass == acc_pass_c && acc_pass_c.length > 0) {
		$('#acc-pass-c').removeClass('is-invalid');
	}
	if (!$('#acc-un').hasClass('is-invalid') && acc_un.length > 0 &&
		!$('#acc-pass').hasClass('is-invalid') && acc_pass.length > 0 &&
		!$('#acc-pass-c').hasClass('is-invalid') && acc_pass_c.length > 0) {
		btn_state('continue-btn', true);
	}
}

function validate_app_config() {
	let app_name = $('#app-name').val();
	let app_url = $('#app-webroot').val();
	if (!/^[a-zA-Z0-9\ _-]+$/.test(app_name) || app_name.length == 0 || app_name.length > 20) {
		$('#app-name').addClass('is-invalid');
		btn_state('continue-btn', false);
	}
	else if (app_name.length > 0) {
		$('#app-name').removeClass('is-invalid');
	}
	if (!/^[a-zA-Z0-9-.]+$/.test(app_url) || app_url.length == 0) {
		$('#app-webroot').addClass('is-invalid');
		btn_state('continue-btn', false);
	}
	else if (app_url.length > 0) {
		$('#app-webroot').removeClass('is-invalid');
	}
	if (!$('#app-name').hasClass('is-invalid') && app_name.length > 0 &&
		!$('#app-webroot').hasClass('is-invalid') && app_url.length > 0) {
		btn_state('continue-btn', true);
	}
}

function finish_setup() {
	$.ajax({
		url: '/setup/finish-setup/',
		type: 'POST',
	}).done(function(res) {
		res = JSON.parse(res);
		if (res.status == 'success') {
			window.location.href = '/';
		}
		else {
			// Show error div
			$('#setup-error').text(res.msg);
			$('#setup-error').removeClass('d-none');
			$('#continue-btn').text('Retry');
		}
	});
}

function btn_state(name, state) {
	if (!state) {
		$('#' + name).addClass('disabled');
		$('#' + name).removeClass('btn-primary');
		$('#' + name).addClass('btn-secondary');
	}
	else if (state) {
		$('#' + name).removeClass('disabled');
		$('#' + name).removeClass('btn-secondary');
		$('#' + name).addClass('btn-primary');
	}
}
