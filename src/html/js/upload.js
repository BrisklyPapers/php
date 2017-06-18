$(window).load(function () {
    $('#drop').click(function () {
        $('#fileBox').trigger('click');
    });
    //Remove item
    $('.fileCont span').click(function () {
        $(this).remove();
    });


    $('#upload').click(function () {
        var formData = new FormData(document.getElementById('upload-form'));

        for (var i = 0; i < uploadedFiles.length; i++) {
            formData.append('files[]', uploadedFiles[i]);
        }

        $("#tags").tagsinput('items').forEach(function(element) {
            formData.append("tags["+element.value+"]", element.text);
        });

        formData.append('action', 'upload');

        $.ajax({
            url: '/upload.php',
            data: formData,
            type: "POST",
            contentType: false,
            processData: false,
            success: function(jqXHR, textStatus) {
                console.log(textStatus, jqXHR);
            },
            error: function(jqXHR, textStatus) {
                console.log(textStatus, jqXHR);
            }
        })
    });
});

var uploadedFiles = [];

if (window.FileReader) {
    var drop;
    addEventHandler(window, 'load', function () {
        var status = document.getElementById('status');
        drop = document.getElementById('drop');
        fileBox = document.getElementById('fileBox');
        var list = document.getElementById('list');
        var listDropped = document.getElementById('listDropped');

        function cancel(e) {
            if (e.preventDefault) {
                e.preventDefault();
            }
        }

        function onDragOver(e) {
            cancel(e);
            this.className = 'hover';
            return false;
        }
        function onDragEnd(e) {
            cancel(e);
            this.className = '';
            return false;
        }

        // Tells the browser that we *can* drop on this target
        addEventHandler(drop, 'dragover', onDragOver);
        addEventHandler(drop, 'dragend', onDragEnd);
        addEventHandler(drop, 'dragleave', onDragEnd);
        addEventHandler(drop, 'dragenter', cancel);

        function printFileInfo(list, file) {
            this.className = '';

            var fileCont = document.createElement('div');
            fileCont.className = "fileCont";
            list.appendChild(fileCont);

            var newFile = document.createElement('div');
            newFile.innerHTML = file.name;
            newFile.className = "fileName";
            fileCont.appendChild(newFile);

            var fileSize = document.createElement('div');
            fileSize.className = "fileSize";
            fileSize.innerHTML = Math.round(file.size / 1024) + ' KB';
            fileCont.appendChild(fileSize);
        }
        
        addEventHandler(fileBox, 'change', function (e) {
            var files = e.target.files;

            list.innerHTML = "";
            for (var i = 0, file; file = files[i]; i++) {
                printFileInfo(list, file);
            }
        });

        addEventHandler(drop, 'drop', function (e) {
            this.className = '';

            e = e || window.event; // get window.event if e argument missing (in IE)
            if (e.preventDefault) {
                e.preventDefault();
            } // stops the browser from redirecting off to the image.

            var dt = e.dataTransfer;
            var files = dt.files;
            for (var i = 0; i < files.length; i++) {
                var file = files[i];
                var reader = new FileReader();

                uploadedFiles.push(file);

                reader.readAsDataURL(file);
                addEventHandler(reader, 'loadend', function (e, file) {
                    var bin = this.result;
                    printFileInfo(listDropped, file);
                }.bindToEventHandler(file));
            }
            return false;
        });
        Function.prototype.bindToEventHandler = function bindToEventHandler() {
            var handler = this;
            var boundParameters = Array.prototype.slice.call(arguments);
            //create closure
            return function (e) {
                e = e || window.event; // get window.event if e argument missing (in IE)
                boundParameters.unshift(e);
                handler.apply(this, boundParameters);
            }
        };
    });
} else {
    document.getElementById('msg-drop').innerHTML = "Click here to upload a file.";
}

function addEventHandler(obj, evt, handler) {
    if (obj.addEventListener) {
        // W3C method
        obj.addEventListener(evt, handler, false);
    } else if (obj.attachEvent) {
        // IE method.
        obj.attachEvent('on' + evt, handler);
    } else {
        // Old school method.
        obj['on' + evt] = handler;
    }
}