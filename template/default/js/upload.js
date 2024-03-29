var points = {
	"first": {
		1: 13,
		2: 10,
		3: 8,
		4: 7,
		5: 6,
		6: 5,
		7: 4,
		8: 3,
		9: 2,
		10: 1
	},

	"final": {
		"bronze": {
			1: 16,
			2: 11,
			3: 8,
			4: 6,
			5: 5,
			6: 4,
			7: 3,
			8: 2,
			9: 1,
			10: 0
		},
		"silver": {
			1: 24,
			2: 18,
			3: 14,
			4: 11,
			5: 9,
			6: 7,
			7: 5,
			8: 3,
			9: 2,
			10: 1
		},
		"gold": {
			1: 36,
			2: 26,
			3: 22,
			4: 17,
			5: 13,
			6: 10,
			7: 7,
			8: 5,
			9: 3,
			10: 1
		}
	}
}

$(window).load
	(
		function () {
			//alert("upload.js loaded");
			$('button#submit').click(function (event) {
				event.preventDefault();
				if ($('#modal').length > 0) {
					$('#modal').remove();
				}
				// check what kind of upload and process it
				if ($('form#firstround').length > 0) {
					validate_firstround();
				} else if ($('form#final').length > 0) {
					validate_final();
				} else if ($('form#award').length > 0) {
					if ($('#file').val() == '') {
						var modal_header = "Error!";
						var modal = "<div id='modal' class='modal fade' role='dialog'>"
							+ "<div class='modal-dialog modal-lg'><div class='modal-content'>"
							+ "<div class='modal-header'><h4 class='modal-title text-danger'>"
							+ modal_header + "</h4></div><div class='modal-body'>"
							+ "No Image File selected!</div></div></div>";
						$(".middle").append(modal);
						$('#modal').modal();
						window.setTimeout(function () {
							$('#modal').modal("hide");
						}, hideNoticeTime);
					} else {
						$('form#award').submit();
					}
				}
			});

			if ($('form#award').length > 0) {
				var options = {
					success: showModal,  // post-submit callback 
					resetForm: true,        // reset the form after successful submit 
				};
				$('form#award').ajaxForm(options);
			}
		}
	);

function validate_firstround() {
	if ($('#modal').length > 0) {
		$('#modal').remove();
		$('.modal-backdrop').remove();
	}

	jObj = {
		"month": $('select#month').val(),
		"table": $('select#table').val(),
		"url": $('input#logLink').val(),
		"preview": true,
	}
	$.post(
		$('form#firstround').attr('action'),
		JSON.stringify(jObj)
	).done(function (data) {
		// showModal(data)
		data = JSON.parse(data);
		if (data.status == 'success') {
			var players = data.data.player_list[1];
			var modal_text = "<div class='row'>"
				+ "<div class='col-md-12'><p>Monthly Cup - <strong>"
				+ $('select#month').val() + "</strong> -  <strong>1st round</strong> - <strong>Table "
				+ $('select#table').val() + "	</strong>:</p>"
				+ "<table class='table table-hover table-bordered table-striped'><thead><tr><th>Pos.</th>"
				+ "<th>Player</th><th>"
				+ "Points</th></tr></thead><tbody>";
			for (var i = 0; i < players.length; i++) {
				modal_text += "<tr><td>"
					+ (i + 1) + ".</td><td>" + players[i]
					+ "</td><td>" + points.first[(i + 1)]
					+ "</td></tr>";
			}
			modal_text += "</tbody></table></div>";
			month = $('#month').val();
			table = $('#table').val();

			var modal_header = "The following upload will be performed:";
			var modal = "<div id='modal' class='modal fade' role='dialog'>"
				+ "<div class='modal-dialog modal-lg'><div class='modal-content'>"
				+ "<div class='modal-header'><h4 class='modal-title'>"
				+ modal_header + "</h4></div><div class='modal-body'>"
				+ modal_text + "</div><div class='modal-footer'>"
				+ "<button type='button' class='btn btn-danger'"
				+ " data-dismiss='modal' id='cancel'>Cancel</button>"
				+ "<button type='button' class='btn btn-success'"
				+ " data-dismiss='modal' id='upload'>Upload</button>"
				+ "</div></div></div>";
			$(".middle").append(modal);
			$('#modal').modal();

			$('button#cancel').click(function () {
				$('button#upload').unbind('click');
				$('#modal').modal("hide");
			});

			$('button#upload').click(function () {
				$('button#cancel').unbind('click');
				jObj = {
					"month": $('select#month').val(),
					"table": $('select#table').val(),
					"url": $('input#logLink').val(),
				}
				$.post(
					$('form#firstround').attr('action'),
					JSON.stringify(jObj)
				).done(function (data) { showModal(data) });
			});
		} else {
			showModal(data.data);
		}

	});
}

function validate_final() {
	if ($('#modal').length > 0) {
		$('#modal').remove();
		$('.modal-backdrop').remove();
	}


	jObj = {
		"month": $('select#month').val(),
		"table": $('select#table').val(),
		"url": $('input#logLink').val(),
		"preview": true,
	}
	$.post(
		$('form#final').attr('action'),
		JSON.stringify(jObj)
	).done(function (data) {
		// showModal(data)
		data = JSON.parse(data);
		if (data.status == 'success') {
			var players = data.data.player_list[1];

			var modal_text = "<div class='row'>"
				+ "<div class='col-md-12'><p>Monthly Cup - <strong>"
				+ $('select#month').val() + "</strong> -  <strong>" + $('select#table option:selected').text() + "Table"
				+ "</strong>:</p>"
				+ "<table class='table table-hover table-bordered table-striped'><thead><tr'><th>Pos.</th>"
				+ "<th>Player</th><th>"
				+ "Points</th></tr></thead><tbody>";
			for (var i = 0; i < players.length; i++) {
				modal_text += "<tr><td>"
					+ (i+1) + ".</td><td>" + players[i]
					+ "</td><td>" + points["final"][$('select#table').val()][(i+1)]
					+ "</td></tr>";
			}
			modal_text += "</tbody></table></div>";
			month = $('#month').val();
			table = $('#table').val();

			var modal_header = "The following upload will be performed:";
			var modal = "<div id='modal' class='modal fade' role='dialog'>"
				+ "<div class='modal-dialog modal-lg'><div class='modal-content'>"
				+ "<div class='modal-header'><h4 class='modal-title'>"
				+ modal_header + "</h4></div><div class='modal-body'>"
				+ modal_text + "</div><div class='modal-footer'>"
				+ "<button type='button' class='btn btn-danger'"
				+ " data-dismiss='modal' id='cancel'>Cancel</button>"
				+ "<button type='button' class='btn btn-success'"
				+ " data-dismiss='modal' id='upload'>Upload</button>"
				+ "</div></div></div>";
			$(".middle").append(modal);
			$('#modal').modal();

			$('button#cancel').click(function () {
				$('button#upload').unbind('click');
				$('#modal').modal("hide");
			});

			$('button#upload').click(function () {
				$('button#cancel').unbind('click');
				jObj = {
					"month": $('select#month').val(),
					"table": $('select#table').val(),
					"url": $('input#logLink').val(),
				}
				$.post(
					$('form#final').attr('action'),
					JSON.stringify(jObj)
				).done(function (data) { showModal(data) });
			});
		} else {
			showModal(data.data);
		}
	});
}
