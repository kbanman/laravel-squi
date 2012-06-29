<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title><?php echo $title; ?> | Squi</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="description" content="Squi bundle for the Laravel PHP framework">
		<meta name="author" content="Kelly Banman">

		<?php echo HTML::style('bundles/squi/css/bootstrap.min.css'); ?>
		<style type="text/css">
			body {
				padding-top: 60px;
				padding-bottom: 40px;
			}
		</style>

		<?php echo HTML::style('bundles/squi/css/bootstrap-responsive.min.css'); ?>
		<?php echo HTML::style('bundles/squi/css/docs.css'); ?>
		<?php echo HTML::style('bundles/squi/css/prettify.css'); ?>

		<!--[if lt IE 9]>
			<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
	</head>

	<body>

		<div class="navbar navbar-fixed-top">
			<div class="navbar-inner">
				<div class="container">
					<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</a>
					<a class="brand" href="#"><span style="color:#777">Laravel</span><span style="color:#ddd">Squi</span></a>
					<div class="nav-collapse">
						<ul class="nav">
							<li <?php if (URI::current() == 'squi/docs/form') echo 'class="active"'; ?>><a href="<?php echo URL::to('squi/docs/form'); ?>">Form</a></li>
							<li <?php if (URI::current() == 'squi/docs/table') echo 'class="active"'; ?>><a href="<?php echo URL::to('squi/docs/table'); ?>">Table</a></li>
						</ul>
					</div>
				</div>
			</div>
		</div>

		<div class="container">
			<?php echo $content; ?>
			<hr>
			<footer>
				<p>&copy; 2012 Kelly Banman</p>
			</footer>
		</div>

		<?php echo HTML::script('bundles/squi/js/jquery.min.js'); ?>
		<?php echo HTML::script('bundles/squi/js/bootstrap.min.js'); ?>
		<?php echo HTML::script('bundles/squi/js/prettify.min.js'); ?>
		<?php echo HTML::script('bundles/squi/js/docs.js'); ?>
	</body>
</html>
