console.log("plupload_setup.js being included...");
function showBrowseButton(suffix, doShow) {
	$("#upload_browse_" + suffix).css("display", doShow ? "inline-block" : "none");
	if (doShow) {
		// $("#upload_browse_" + suffix).html("Choose a new file");
	}
}
function showFilelist(suffix, doShow) { $("#upload_filelist_" + suffix).css("display", doShow ? "block" : "none"); }

function cancel_upload(up, fieldName) {
	uploadsPending--;
	up.stop();
	showFilelist(fieldName, false);
	showBrowseButton(fieldName, true);
}

var revertData = {};

function setupRevertInterface(suffix) {
	$("div#upload_revert_" + suffix).remove();
	var d = revertData[suffix];
	if (d == null) { return; }

	$("<div id='upload_revert_" + suffix + "'/>").html("<a href='javascript:;'>Revert to original</a>").insertBefore($("div#upload_filelist_image"));
	$("div#upload_revert_" + suffix + " a").click(function() {
		for (var i = 0 ; i < pluploadConfig.length ; i++) {
			var cfg = pluploadConfig[i];
			if (cfg.fieldName == suffix) {
				// console.log("MATCH");
				// console.log(cfg.destructionUrl);

				d = revertData[suffix];
				// console.log("USE REVERT DATA [" + JSON.stringify(d) + "]");

				$.ajax({
					type: "POST",
					url: cfg.destructionUrl,
					data: {
						// upload_sid is part of the destructionUrl already
						name: suffix,
						index: 0 // if we ever do mlutiple files at once might need to revisit this...we do get an index back from receive.php, do we need to use that?
					},
					success: function() {
						$("div#upload_revert_" + suffix).remove();
						renderUploadPreview(suffix, {
							formattedSize: d.filesize,
							uri: d.src,
							dimensions: {
								width: d.width,
								height: d.height
							},
							orig_dimensions: {
								width: d.width,
								height: d.height
							}
						});
					},
					error: function() {
						console.log("error reverting file...");
					}
				});
			}
		}
	});
}

function storeRevertData(suffix) {
	var container = $("div#upload_filelist_" + suffix).prevAll("div.uploaded_file");
	var img = container.children("img.representation");
	var filesize = container.find("span.filesize");

	// console.log("IMG: [" + img.length + "]/[" + img.attr("src") + "]");

	if (img.length == 0) {
		// we're dealing with a non-image file upload; asset for instance

		if ($("div.uploaded_file span.filename").html() != "") {
			// we're editing an existing file
			repositionPreview(suffix);
		} else {
			// new file; do nothing
		}
	} else if (img.attr("src") != "") {
		// we're dealing with an image
		var data = {
			width: img.width(),
			height: img.height(),
			src: img.attr("src"),
			filesize: filesize.html()
		};
		revertData[suffix] = data;
		repositionPreview(suffix);
		// console.log("data is [" + JSON.stringify(data) + "]");
	} else {
		// console.log("NO EXISTING DATA!");
	}
}

function buildFiltersForUploader(fieldName, cfg) {
	var filters = {};
	if (cfg.constraints != null) {
		if (cfg.constraints.extensions != null) {
			var extensions = "";
			for (var i = 0 ; i < cfg.constraints.extensions.length ; i++) {
				extensions += (i == 0 ? "" : ",") + cfg.constraints.extensions[i];
			}
			filters["mime_types"] = [ { title: "disco configured filetype restriction", extensions: extensions } ];
		}

		if (cfg.constraints.max_size != null) {
			filters["max_file_size"] = cfg.constraints.max_size;
		}
	}

	return filters;
}

function prepForUpload(up, files) {
	var localUploaderReference = up;
	var localScopedFieldName = up.settings.multipart_params.rvFieldName;

	// set the key/Filename on the uploader to whatever was just added...
	for (var i = 0 ; i < pluploadConfig.length ; i++) {
		var cfg = pluploadConfig[i];
		if (cfg.fieldName == up.settings.multipart_params.rvFieldName && (cfg.amzn_policy != undefined && cfg.amzn_policy != "")) {
			var f = files[0];
			var dotIdx = f.name.lastIndexOf(".");
			var extension = dotIdx == -1 ? "no_extension" : f.name.substring(dotIdx+1);
			// key is the file's name in the Amazon bucket. Filename is used in only some runtimes, so give it the same value.
			up.settings.multipart_params.key = (cfg.amzn_tempDir != "" ? cfg.amzn_tempDir + "/" : "") + cfg.entityId + "." + extension;
			up.settings.multipart_params.Filename = up.settings.multipart_params.key;
			up.settings.multipart_params['x-amz-meta-original_filename'] = f.name;
			break;
		}
	}

	var html = '';
	plupload.each(files, function(file) {
		html += '<span id="' + file.id + '">' + file.name +
				' (' + plupload.formatSize(file.size) + ') <b></b> <a class="canceller" href="javascript:;">Cancel</a></span>';
	});
	$("#upload_filelist_" + localScopedFieldName).html(html);
	console.log("use [" + html + "] for [" + "#upload_filelist_" + localScopedFieldName + "]");

	$("#upload_filelist_" + localScopedFieldName + " a.canceller").click(function() { cancel_upload(localUploaderReference, localScopedFieldName); });

	showFilelist(localScopedFieldName, true);
	showBrowseButton(localScopedFieldName, false);
	$('#upload_console_' + localScopedFieldName).html("");

	kickoffUpload(localUploaderReference);
}

$(document).ready(function() {
	// pluploadConfig is generated by the upload plasmature element in PHP and passed through to this js...see core/classes/plasmature/upload.php
	// console.log("we should use [" + pluploadFieldName + "]");
	console.log("set up plupload elements...");
	// console.log(pluploadConfig);

	for (var i = 0 ; i < pluploadConfig.length ; i++) {
		var cfg = pluploadConfig[i];

		storeRevertData(cfg.fieldName);

		var multipartParams = {
			"rvFieldName": cfg.fieldName
		};

		if (cfg.amzn_policy != undefined && cfg.amzn_policy != "") {
			// we'll set key/Filename later in the callback function for s3_prep.php / amzn_prepPage, b/c we want it to include the extension.
			multipartParams.acl = 'public-read';
			multipartParams['Content-Type'] = '';
			multipartParams.AWSAccessKeyId = cfg.amzn_accessKeyId;
			multipartParams.policy = cfg.amzn_policy;
			multipartParams.signature = cfg.amzn_signature;

			multipartParams['x-amz-meta-reason_id'] = cfg.entityId;
			// delete multipartParams.rvFieldName;
		}

		var uploadFilters = buildFiltersForUploader(cfg.fieldName, cfg);

		var uploader = new plupload.Uploader({
			browse_button: 'upload_browse_' + cfg.fieldName,
			drop_element: 'upload_browse_' + cfg.fieldName,
			multi_selection: false,
			// url: '/reason_package/reason_4.0/www/scripts/upload/receive.php?user_id=0&upload_sid=' + uploadSessionId,
			url: cfg.submissionUrl,
			multipart_params : multipartParams,
			filters: uploadFilters
		});
		// console.log("SETTING UP [" + cfg.fieldName + "] / [" + uploader.settings.multipart_params.rvFieldName + "] / [" + uploader.id + "]");

		uploader.bind('Error', (function(localScopedFieldName, up, err) { return function(up, err) {
			console.log("detected an error...");
			console.log(err);
			var uploadConsole = $('#upload_console_' + localScopedFieldName);

			if (err.response) {
				var r = JSON.parse(err.response);
				if (r.message) {
					uploadConsole.html(r.message);
				} else {
					uploadConsole.html(uploadConsole.html() + "\nError #" + err.code + ": " + err.message);
				}
			} else if (err.message == "File size error.") {
				uploadConsole.html("That file is too large.");
			} else if (err.message) {
				uploadConsole.html(r.message);
			} else {
				uploadConsole.html("An unknown error occurred: " + JSON.stringify(err));
			}

			cancel_upload(up, localScopedFieldName);
		} })(cfg.fieldName));

		uploader.bind('FilesAdded', (function(localScopedFieldName, up, files) { return function(up, files) {
			// throughout this function, "this" refers to the relevant uploader object that bound this event in the first place....if you pass in "uploader"
			// you'll get whatever the last uploaed was.
			console.log("files added firing with [" + localScopedFieldName + "]");

			for (var i = 0 ; i < pluploadConfig.length ; i++) {
				var cfg = pluploadConfig[i];
				if (cfg.fieldName == up.settings.multipart_params.rvFieldName) {
					if (cfg.amzn_policy != undefined && cfg.amzn_policy != "") {
						$.getJSON(cfg.amzn_prepPage, {id: cfg.entityId }, function(data, textStatus) {
							prepForUpload(up, files);
						});
					} else {
						prepForUpload(up, files);
					}
				}
			}

		} })(cfg.fieldName));

		uploader.bind('FileUploaded', function(up, file, response) {
			// image_manager.js, image_crop.js, etc. look for this event...
			// old upload used to emit a "uploadSuccess" event on div.file_upload
			var whichUploader = up.settings.multipart_params.rvFieldName;
			$(document).trigger("backgroundUploadComplete", [whichUploader]);

			showFilelist(whichUploader, false); // hide the filename / 100% line
			setupRevertInterface(whichUploader);

			response = JSON.parse(response.response);

			var info;
			if (response != null) {
				info = response[whichUploader];
			} else {
				info = {
					filename: file.name,
					size: file.origSize
				};
			}
			uploadsPending--;
			renderUploadPreview(whichUploader, info);


			if (uploadsPending <= 0 && onUploadCompletion) {
				$(".submit_waiting").html("Uploads finished. Continuing&hellip;").removeClass('submit_waiting').addClass('submitting');
				onUploadCompletion();
			}

			$("#" + file.id + " a.canceller").remove();
			showBrowseButton(up.settings.multipart_params.rvFieldName, true);
		});

		uploader.bind('UploadProgress', function(up, file) {
			// console.log("getting feedback [" + file.percent + "],[" + up.settings.multipart_params.rvFieldName + "]/[" + up.id + "]");

			// document.getElementById(file.id).getElementsByTagName('b')[0].innerHTML = '<span>' + file.percent + "%</span>";
			// if (file.percent == "100") { }
			// $("span#" + up.id + " b").html(file.percent + "%!!!");
			$("div#upload_filelist_" + up.settings.multipart_params.rvFieldName + " span b").html(file.percent + "%");
		});

		// drag and drop setup - most of the work is handled by just setting "drop_element" in plupload config, but this gives us customized look&feel...
		var dropzone = $("#upload_browse_" + cfg.fieldName);
		dropzone.on('dragenter', function(e) {
			e.stopPropagation();
			e.preventDefault();
			$(this).addClass("hover_dropzone");
		});

		dropzone.on('dragleave', function(e) {
			e.stopPropagation();
			e.preventDefault();
			$(this).removeClass("hover_dropzone");
		});

		dropzone.on('drop', function(e) {
			e.stopPropagation();
			e.preventDefault();
			$(this).removeClass("hover_dropzone");
		});

		// prevent form submission while uploads are pending
		// reason_package/reason_4.0/www/flash_upload/rich_upload.js
		var form = dropzone.parents("form").eq(0);
		var submitters = $("button[type=submit], input[type=submit], " + "input[type=image]", form);
		submitters.click(submission_attempt);
		form.submit(submission_attempt);

		uploader.bind("Init", function(up, params) {
			var whichUploader = up.settings.multipart_params.rvFieldName;
			var placeholderText = "Click to add file...";
			if (up.features.dragdrop) {
				placeholderText = "Click to add file, or drag/drop onto this zone...";
			}
			$("#upload_browse_" + whichUploader + " span.default_text").html(placeholderText);
		});

		uploader.init();
	}

	function submission_attempt() {
		var submitter = this;
		
		if (uploadsPending > 0) {
			onUploadCompletion = function deferred_submission() {
				if (submitter.click)
					submitter.click();
				else if (submitter.submit)
					submitter.submit();
			};

			if ($(".submit_waiting").length == 0) {
				var targetBar = $('#discoSubmitRow td:last');
				$('<span class="submit_waiting">Waiting for files to finish uploading&hellip;</span>').appendTo(targetBar);
			}
			return false;
		}
	}

	/*

	uploader.bind('Error', function(up, err) {
		var uploadConsole = $('#upload_console');
		uploadConsole.html(uploadConsole.html() + "\nError #" + err.code + ": " + err.message);
	});

	$('#upload_start').click(function() {
		uploader.start();
	});
	*/
});

function renderUploadPreview(suffix, info) {
	// console.log("WITH FIXES: rendering preview for [" + suffix + "] with " +JSON.stringify(info) + "...");

	var cfg = null;;
	for (var i = 0 ; i < pluploadConfig.length ; i++) {
		if ((pluploadConfig[i]).fieldName == suffix) {
			cfg = pluploadConfig[i];
			break;
		}
	}

	var selectorStart = "div#file_upload_" + suffix + " ";

	if (info == undefined) {
		$('#upload_console_' + suffix).html("Unknown error occurred during upload.");
		return;
	}

	var image = $(selectorStart + "img.representation");
	// profile uploads for instance has the representation elsewhere...
	if (image.length == 0) {
		image = $("img.representation");
		if (image.length != 0) {
			selectorStart = "";
		}
	}

	// we normally want to reposition the preview so that it is the dropzone - but if we have a croppable element (like profile photos)
	// we can't do that. So, check for one of the croppable hidden fields. Little ugly, but works.
	if ($('input[name="_reason_upload_orig_h"]').length == 0) { repositionPreview(suffix); }

	$(selectorStart + ".uploaded_file").show();

	if (image.length == 0) {
		// console.log("NON-IMAGE-PREVIEW!");
		$(selectorStart + "div.uploaded_file span.filename").html(info.filename);
	} else {
		var previewImgSrc;

		if (info.uri != null) {
			console.log("use full uri");
			previewImgSrc = info.uri;
		} else {
			console.log("use preview img");
			previewImgSrc = cfg.previewUrl + info.tempName;
		}
		image.attr("src", previewImgSrc);

		// console.log("IMAGE-PREVIEW!");
		var dims = info.dimensions;
		var orig_dims = info.orig_dimensions;

		if (dims) {
			image.css(dims);
			$(selectorStart + '.dimensions').html(dims.width + "x" + dims.height);
		}
		if (orig_dims) {
			$('span#' + suffix + 'Item input[name="_reason_upload_orig_h"]').val(orig_dims.height);
			$('span#' + suffix + 'Item input[name="_reason_upload_orig_w"]').val(orig_dims.width);
		}
	}

	if (info.formattedSize != null) {
		$(selectorStart + '.filesize').text(info.formattedSize);
	} else {
		$(selectorStart + '.filesize').text(format_size(info.size));
	}
	
	// resize the shim
	var shim = $(selectorStart + "div.moxie-shim-html5");
	var browseBtn = $(selectorStart + "div#upload_browse_" + suffix);
	shim.width(browseBtn.width());
	shim.height(browseBtn.height());
}

function repositionPreview(suffix) {
	// let's try to stuff the preview image into the plupload click/drop zone...
	var previewImg = $("div#upload_filelist_" + suffix).prevAll("div.uploaded_file");
	if (previewImg.length == 1){
		previewImg.detach();
		var dropzone = $("div#upload_browse_" + suffix);
		dropzone.append(previewImg);
		dropzone.addClass("loadedItem");
	}
}

var uploadsPending = 0;
var onUploadCompletion = null;
function kickoffUpload(uploader) {
	console.log("STARTING UPLOAD FOR [" + uploader.settings.multipart_params.rvFieldName + "] / [" + uploader.id + "]");
	// $("div.moxie-shim-html5").hide();
	// console.log(uploader);
	uploader.start();
	uploadsPending++;
	// console.log("DISABLED");
	// console.log(uploader);
}

// more drag-and-drop...disable normal behavior - if the user misses the dropzone we probably don't want the browser to load the dropped file for instance...
$(document).on('dragenter', function (e) {
    e.stopPropagation();
    e.preventDefault();
});
$(document).on('dragover', function (e) {
  e.stopPropagation();
  e.preventDefault();
});
$(document).on('drop', function (e) {
	if (isDropOnPlupload(e.target)) {
		console.log("drop was on plupload!");
	} else {
		console.log("drop was outside!");
		e.stopPropagation();
		e.preventDefault();
	}
});

// firefox workaround...
function isDropOnPlupload(pageElement) {
	var el = $(pageElement);

	var parentId = el.parent().attr("id");
	var grandparentId = el.parent().parent().attr("id");

	parentId = parentId ? parentId : "";
	grandparentId = grandparentId ? grandparentId : "";

	if (parentId.indexOf("file_upload_") == 0 || grandparentId.indexOf("file_upload_") == 0) {
		return true;
	} else {
		return false;
	}
}
