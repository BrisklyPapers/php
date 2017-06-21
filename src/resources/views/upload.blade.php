<!doctype html>
<html>
<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta name="robots" content="noindex, nofollow">
    <meta name="googlebot" content="noindex, nofollow">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="stylesheet" href="css/external/bootstrap.min.css">
    <link rel="stylesheet" href="css/external/bootstrap-theme.min.css">
    <link rel="stylesheet" href="css/external/bootstrap-tagsinput.css">
    <link rel="stylesheet" href="css/external/bootstrap-tagsinput-typeahead.css">
    <link rel="stylesheet" href="css/general.css">
    <link rel="stylesheet" href="css/upload.css">

    <title>Upload Files</title>
</head>

<body>
<div class="docbox">
    <div id="drop">
        <div class="msg-drop" id="msg-drop">
            <span class="glyphicon glyphicon-cloud-upload cloud"></span>
            Drop files here or click to <span id="browse">browse</span>.
        </div>
    </div>
    <form id="upload-form">
        <input id="fileBox" type="file" name="files[]" multiple>
        <input type="text" id="tags" data-role="tagsinput" placeholder="Enter tags ..."/>
    </form>
    <div id="listDropped"></div>
    <div id="list"></div>
    <button type="button" class="btn btn-success" id="upload">Upload</button>
    <div id="response"></div>
</div>


<script src="js/external/jquery.min.js"></script>
<script src="js/external/bootstrap.min.js"></script>
<script src="js/external/bootstrap-tagsinput.min.js"></script>
<script src="js/external/typeahead.bundle.min.js"></script>
<script src="js/upload.js"></script>
<script src="js/tags.js"></script>

</body>
</html>