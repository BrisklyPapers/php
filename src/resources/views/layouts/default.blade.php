<!doctype html>
<html>
<head>
    @include('includes.head')
</head>
<body>

<header>
    @include('includes.header')
</header>

<div class="container">
    <div id="main" class="row">
        <!-- sidebar content -->
        <div id="sidebar" class="col-md-12">
            @include('includes.sidebar')
        </div>

        <!-- main content -->
        <div id="content" class="col-md-12">
            @yield('content')
        </div>

    </div>
</div>

<footer>
    <div class="container">
        @include('includes.footer')
    </div>
</footer>

@yield('bottom-js')

</body>
</html>