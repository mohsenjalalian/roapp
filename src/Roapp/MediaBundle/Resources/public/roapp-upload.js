Dropzone.autoDiscover = false;

$('.roapp-image-upload').each(function () {
    var id = 'upload-' + $(this).attr('id');
    $(this).after($('<div>', {
        class: "dropzone ui segment",
        id: id
    }));

    var uploadOptions = {};
    uploadOptions.url = $(this).data('path');
    uploadOptions.maxFilesize = $(this).data('maxFilesize');
    uploadOptions.addRemoveLinks = true;
    uploadOptions.parallelUploads = $(this).data('parallelUploads');
    uploadOptions.maxFiles = $(this).data('maxFiles');
    uploadOptions.acceptedFiles = $(this).data('acceptedFiles');

    uploadOptions.dictDefaultMessage = 'لطفا فایل مورد نظر خود را برای بارگذاری در اینجا قرار دهید.';
    uploadOptions.dictInvalidFileType = 'شما مجاز به بارگذاری این نوع فایل نیستید.';
    uploadOptions.dictFileTooBig = 'اندازه فایل باید کوچکتر از {{maxFilesize}} مگابایت باشد.';
    uploadOptions.dictFileTooBig = 'اندازه فایل باید کوچکتر از {{maxFilesize}} مگابایت باشد.';
    uploadOptions.dictRemoveFile = 'حذف فایل';
    uploadOptions.init = function (event) {
        var hiddenElementId = this.element.id.substring(7);
        $hiddenElement = $('#'+hiddenElementId);
        hiddenElementValue = $hiddenElement.val();
        hiddenElementFileIds = (hiddenElementValue) ? hiddenElementValue.split(',') : [];
        if (hiddenElementFileIds.length > 0) {
            var oldImagesContainerWrapper = $('<div>', {
                class: "ui segment",
            })[0];
            var oldImagesContainer = $('<div>', {
                class: "ui four cards"
            })[0];
            $(oldImagesContainerWrapper).append(oldImagesContainer);
            $hiddenElement.after(oldImagesContainerWrapper);

            hiddenElementFileIds.forEach(function (el) {
                var linkUrl = $hiddenElement.data('linkTemplateUrl').replace('__replaceme__', el);
                $.get(linkUrl, function (data) {
                    $(oldImagesContainer).append(createImageCard(data));
                })
            })

        }
    };

    var myDropzone = new Dropzone("div#" + id, uploadOptions);
    myDropzone.on('success', function (file, response) {
        var hiddenElementId = this.element.id.substring(7);
        $(file.previewElement).data('remoteTempFileName', response);
        $hiddenElement = $('#'+hiddenElementId);
        hiddenElementValue = $hiddenElement.val();
        hiddenElementFileNames = (hiddenElementValue) ? hiddenElementValue.split(',') : [];
        hiddenElementFileNames.push(response);
        $hiddenElement.val(hiddenElementFileNames.join());
    });

    myDropzone.on('removedfile', function (file) {
        var hiddenElementId = this.element.id.substring(7);
        remoteTempFileName = $(file.previewElement).data('remoteTempFileName');
        $hiddenElement = $('#'+hiddenElementId);
        hiddenElementValue = $hiddenElement.val();
        hiddenElementFileNames = (hiddenElementValue) ? hiddenElementValue.split(',') : [];

        var index = hiddenElementFileNames.indexOf(remoteTempFileName);
        if (index > -1) {
            hiddenElementFileNames.splice(index, 1);
        }

        $hiddenElement.val(hiddenElementFileNames.join());
    });
});

function createImageCard(url) {
    var imageEl = $('<img>', {
        src: url
    })[0];

    var wrapper = $('<div>', {
        class: "image"
    })[0];

    var anchor = $('<a>', {
       class: "orange card"
    })[0];

    $(wrapper).append(imageEl);
    $(anchor).append(wrapper);

    return anchor;
}