<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<!-- CSRF Token -->
	<meta name="csrf-token" content="{{ csrf_token() }}">

	<title>@yield('title', setting('web_name', '一只独立行走的喵'))</title>

	<!-- Styles -->
	<link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
<h1 class="text-danger"><strong>一只独立行走的喵</strong></h1>
<p id="app">
	<nav class="navbar navbar-default navbar-static-top">
		<div class="container">
			<div class="navbar-header">

				<!-- Collapsed Hamburger -->
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
						data-target="#app-navbar-collapse" aria-expanded="false">
					<span class="sr-only">Toggle Navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>

				<!-- Branding Image -->
				<a class="navbar-brand" href="{{ url('/') }}">
					Home
				</a>

				<div class="navbar-brand visible-xs-block" style="padding:4px 0 0 50px">
					<form class="navbar-form navbar-left search" style="margin:0;border:0;float:right" role="search"
						  action="{{ route('articles.search.post') }}" method="post">
						{{ csrf_field() }}
						<div class="form-group">
							<span class="glyphicon glyphicon-search" style="line-height:inherit"></span>
							<input type="text" name="key" style="border: none;margin-left:5px;width:100px"
								   placeholder="search..">
						</div>
					</form>
				</div>
			</div>

			<div class="collapse navbar-collapse" id="app-navbar-collapse">
				<!-- Left Side Of Navbar -->
				<ul class="nav navbar-nav">
					<li><a href="{{ route('articles.list') }}">全部文章</a></li>
				</ul>
{{--				TODO--}}
				<ul class="nav navbar-nav">
					<li><a href="{{ route('movies.list') }}">案例欣赏</a></li>
				</ul>

				<!-- Right Side Of Navbar -->
				<ul class="nav navbar-nav navbar-right">
					<li class="hidden-xs" style="margin-top:6px">
						<form class="navbar-form navbar-left search" role="search"
							  action="{{ route('articles.search.post') }}" method="post">
							{{ csrf_field() }}
							<div class="form-group">
								<span class="glyphicon glyphicon-search"></span>
								<input type="text" name="key" style="border: none;margin-left:5px;width:100px"
									   placeholder="search..">
							</div>
						</form>
					</li>
				@guest
					<!-- <li><a href="{{ route('login') }}">Login</a></li>
                            <li><a href="{{ route('register') }}">Register</a></li> -->
					@else
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
							   aria-expanded="false" aria-haspopup="true">
								{{ Auth::user()->name }} <span class="caret"></span>
							</a>

							<ul class="dropdown-menu">
								@if (Auth::check())
									@if (Auth::id() === 1)
										<li><a href="/admin">管理后台</a></li>
										<li role="separator" class="divider"></li>
									@endif
								@endif
								<li>
									<a href="{{ route('logout') }}"
									   onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
										Logout
									</a>

									<form id="logout-form" action="{{ route('logout') }}" method="POST"
										  style="display: none;">
										{{ csrf_field() }}
									</form>
								</li>
							</ul>
						</li>
					@endguest
				</ul>
			</div>
		</div>
	</nav>

	@yield('content')

	<footer class="z-footer">
<p class="z-text">©2018 版权所有&nbsp;<a href="https://beian.miit.gov.cn/" target="_blank">备案号:辽ICP备18003786号-1</a></p>
</footer>
</div>

<!-- Scripts -->
<script src="{{ asset('js/app.js') }}"></script>
@yield('scripts')
</body>
</html>
