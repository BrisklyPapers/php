@extends('layouts.default')

@section('content')
    <div class="row col-sm-12" id="upload_area">
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
        <div id="listDropped" class="col-sm-12"></div>
        <div id="list" class="col-sm-12"></div>
        <button type="button" class="btn btn-primary" id="upload">Upload</button>
    </div>
    <div id="response" class="col-sm-12"></div>
@stop

@section('bottom-js')
    <script src="js/external/jquery.min.js"></script>
    <script src="js/external/bootstrap.min.js"></script>
    <script src="js/search.js"></script>
    <script src="js/external/bootstrap-tagsinput.min.js"></script>
    <script src="js/external/typeahead.bundle.min.js"></script>
    <script src="js/upload.js"></script>
    <script src="js/tags.js"></script>
@stop
