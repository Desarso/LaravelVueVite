var FileInputModule = (function (window, jQuery, undefined) {

	const  allowedFileExtensions = [
	'jpg', 'jpeg', 'png', 'gif','doc', 'docx', 'xls', 'xlsx', 
	'ppt', 'pptx', 'pps', 'zip', 'rar', 'tar', 'gzip', 'gz', '7z', 
	'txt', 'pdf', 'PDF', 'JPG', 'PNG'];

 	//Local flow variables
 	var item  				 = null;
 	var base64Files 		 = [];
 	var selectedFiles 		 = [];
 	var deletedFiles 		 = [];
 	var initialPreview  	 = []; 
 	var initialPreviewConfig = [];
 	var showBrowse 			 = true;
 	var fileContainer 		 = $('#imagesContainer'); 
 	var fileTemplate		 = null;
    var fileInputNames		 = [];

    //local UI variables
    var input = null;


    /**
    * Initializer
    *
    * @param _item
    * @param _input
    */
    function init(_item,_fileContainer, filesUrls = null, idticket = null) {
    	item  = _item;
    	fileContainer = _fileContainer;

		initialPreview = [];
		initialPreviewConfig = [];
    	if (filesUrls != null) loadFilesForUpdate(filesUrls, idticket);
    	else getSelectedImages(item.id);
    	unique();
    	destroyComponent();
    	createHTMLComponent();
    }
    function destroyComponent() {
        fileContainer.empty();
    }
    
    function createHTMLComponent() {
		console.log(initialPreview);
		console.log(initialPreviewConfig);
    	fileTemplate = '<input  id="images'+item.id+'" name="files'+item.id+'[]" type="file" multiple>';
    	fileContainer.append(fileTemplate);
    	setTimeout(function() {
    		input = $("#images"+item.id);
    		input.fileinput('destroy').fileinput({
	    		theme: 'fa',
	    		language: 'es',
	    		uploadUrl: '#',
	    		actionUpload: false,
				showUpload: false,
				showBrowse: showBrowse,
	    		showUploadedThumbs: false,
	    		showRemove: false,
	    		overwriteInitial: false,
	    		preferIconicPreview: true,
	    		allowedFileExtensions: allowedFileExtensions,
	    		previewFileIconSettings: {
	    			'doc': '<i class="fa fa-file-word-o fa-sm text-primary"></i>',
	    			'xls': '<i class="fa fa-file-excel-o fa-sm text-success"></i>',
	    			'ppt': '<i class="fa fa-file-powerpoint-o fa-sm text-warning"></i>',
	    			'zip': '<i class="fa fa-file-archive-o fa-sm text-muted"></i>',
	    		},
	    		initialPreviewFileType: 'image',
	    		initialPreview: initialPreview,
	    		initialPreviewAsData: true,
	    		initialPreviewConfig: initialPreviewConfig,
	    		previewFileExtSettings: {
	    			'doc': function (ext) {
	    				return ext.match(/(doc|docx)$/i);
	    			},
	    			'xls': function (ext) {
	    				return ext.match(/(xls|xlsx)$/i);
	    			},
	    			'ppt': function (ext) {
	    				return ext.match(/(ppt|pptx)$/i);
	    			},
	    			'zip': function (ext) {
	    				return ext.match(/(zip|rar|tar|gzip|gz|7z)$/i);
	    			},
	    			'txt': function (ext) {
	    				return ext.match(/(txt|ini|md)$/i);
	    			},
	    		},
	    		purifyHtml: true,
	    		fileActionSettings: {
		            // Deshabilita
		            showUpload: false,
		        }
		    });  
	    	$("button.fileinput-remove").remove();//quita la x de donde se arrastran las imagenes 
	    	initUIEvents();
    	},500);
	}

	function initUIEvents(){
		input.on('fileremoved', function (event, id, index) {
			removeObjectToArray(base64Files, id);
			let files = input.fileinput('getFileStack');
			for (let i = 0; i < files.length; i++) {
				selectedFiles.push({
					'id': item.id,
					'file': files[i],
					'key': '',
					'previewId': id
				});
			}
			removeObjectToArray(selectedFiles, id);

		})
		.on('fileloaded', function (event, file, previewId, index, reader) {
			selectedFiles.push({
				'id': item.id,
				'file': file,
				'key': '',
				'previewId': previewId
			});
			base64Files.push({
				'id': item.id,
				'base64': reader.result,
				'key': '',
				'previewId': previewId,
				'filename': file.name
			});
		})
		.on('filepredelete', function (event, key, jqXHR, data) {
			var abort = true;
			if (confirm("¿Estás seguro de eliminar el archivo?")) {
				abort = false;
				for (var i = 0; i < initialPreviewConfig.length; i++) {
					if (initialPreviewConfig[i].key == key) {
						deletedFiles.push(initialPreviewConfig[i]);
					}
				}
			}
			return abort;
		});
	}

	function clearImages()
	{
		showBrowse = true;
		$("#images"+item.id).fileinput('clear');
	}

	function loadFilesForUpdate(filesUrls, idticket) {
		let urls = filesUrls.split(",");
		for (var i = 0; i < urls.length; i++) {
			let filename = urls[i].split("/");
			let file = getInitialPreviewConfigByExtension(
				filename[filename.length - 1],
				i, 
				urls[i],
				true,
				idticket
				);
			initialPreviewConfig.push(file);
			initialPreview.push(urls[i]);
		}
		getSelectedImages(item.id);
	}

	function removeObjectToArray(array, key) {
		for (var i = array.length - 1; i >= 0; --i) {
			if (array[i].previewId == key) {
				array.splice(i, 1);
			}
		}
	}

	function getSelectedImages(id) {
		for (var i = 0; i < base64Files.length; i++) {
			if (id == base64Files[i].id) {
				initialPreview.push(base64Files[i].base64);
				initialPreviewConfig.push(
					getInitialPreviewConfigByExtension(base64Files[i].filename, id, i));
				base64Files[i].key = id + '-' + i;
				selectedFiles[i].key = id + '-' + i;
			}
		}
	}

	function getInitialPreviewConfigByExtension(filename, index, downloadUrl = null, isupdating = false, idticket = 100) {
		let splitString = filename.split(".");
		let ext = splitString[splitString.length - 1];
		let obj = {
			caption: filename,
			size: 847000,
			url: "api/deleteFile",
			key: downloadUrl,
			extra: {idticket: idticket}
		};

		if (downloadUrl != null) {
			obj = {
				type: "",
				caption: filename,
				downloadUrl: downloadUrl,
				size: 847000,
				url: "api/deleteFile",
				key: downloadUrl,
				extra: {idticket: idticket}
			};
		}

		if (ext == 'zip' || ext == 'rar' || ext == 'tar' 
			|| ext == 'gzip' || ext == 'gz' || ext == '7z') {
			delete obj.type;
		}
		if (ext == 'pdf' || ext == 'PDF') {
			obj.type = 'pdf'
		}
		if (ext == 'txt' || ext == 'ini' || ext == 'md') {
			obj.type = 'text'
		}
		if (ext == 'jpg' || ext == 'jpeg' || ext == 'png' || ext == 'gif' || ext == 'JPG' || ext == 'PNG') {
			obj.type = "image";
		}

		if (isupdating) {
			if (ext == 'doc' || ext == 'docx' || ext == 'xls' || ext == 'xlsx'
				|| ext == 'ppt' || ext == 'pptx' || ext == 'pps') {
				obj.type = "office";
			}
		}

		return obj;
	}

	function uploadFiles(idtickets, items, isupdating = false) {
		let formData = getFormData(idtickets, items, isupdating);
		var token = $("input[name=_token]").val();
		$.ajax({
			url: "uploadTicketFile",
			headers: { 'X-CSRF-TOKEN': token },
			type: 'POST',
			datatype: 'json',
			contentType: false,
			processData: false,
			data: formData,
			beforeSend: function (){
				PNotify.info({
					title: 'Procesando....',
					text: 'Cargando archivos',
					icon: 'fas fa-cog fa-spin'
				  });
			},
			success: function (data) {
				PNotify.removeAll();
				clearVariables();
			},
			error: function (data) {
				PNotify.removeAll();
				clearVariables();
			}
		});
	}

	function getFormData(idtickets, items, isupdating) {
		var data = new FormData();
		data.append('idtickets', idtickets);
		data.append('isupdating', isupdating);
		data.append('images', fileInputNames);

		for (let i = 0; i < items.length; i++) {
			let element = 'files'+items[i].id+'[]';
			let files = getFiles(items[i].id);
			for (let k = 0; k < files.length; k++) {
				data.append(element, files[k], files[k].name);
			}
		}

		if (deletedFiles.length != 0) {
			let urls = [];
			for (var i = 0; i < deletedFiles.length; i++) {
				urls.push(deletedFiles[i].downloadUrl);
			}
			data.append('deletedfiles', urls);
		}

		return data;
	}

	//it keep an unique name for a fileinput 
	function unique() {
		fileInputNames.unshift("files" + item.id);
	    let arrayFiltered = [];
	    $.each(fileInputNames, function (i, elements) {
	        if ($.inArray(elements, arrayFiltered) === -1)
	            arrayFiltered.push(elements);
	    });
	    fileInputNames = arrayFiltered;
	}

	function getFiles(id) {
		let files = []
		for (var i = 0; i < selectedFiles.length; i++) {
			if (id == selectedFiles[i].id) {
				files.push(selectedFiles[i].file);
			}
		}
		return files;
	}

	function clearVariables() {
		item  				 = null;
		input 				 = null;
		showBrowse 			 = true;
 		base64Files 		 = [];
 		selectedFiles 		 = [];
 		deletedFiles 		 = [];
 		initialPreview  	 = []; 
 		initialPreviewConfig = [];
 		fileInputNames		 = [];
	}

	function setBrowse(params) {
		showBrowse = params;
	}

	return {
		init           : init,
		uploadFiles    : uploadFiles,
		clearVariables : clearVariables,
		clearImages    : clearImages,
		setBrowse    : setBrowse,
	}
})(window, $);