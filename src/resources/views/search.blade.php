<!doctype html>
<html>
<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta name="robots" content="noindex, nofollow">
    <meta name="googlebot" content="noindex, nofollow">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="stylesheet" href="css/external/bootstrap.min.css">
    <link rel="stylesheet" href="css/external/bootstrap-theme.min.css">
    <link rel="stylesheet" href="css/general.css">
    <link rel="stylesheet" href="css/search.css">

    <title>Search Files</title>
</head>

<body>
<div class="docbox">
    <form id="search-form">
        <input type="text" id="search" placeholder="Enter search string" autocomplete="off"/>
    </form>
    <div id="response"></div>
</div>


<script src="js/external/jquery.min.js"></script>
<script src="js/external/bootstrap.min.js"></script>
<script src="js/search.js"></script>

</body>
</html>