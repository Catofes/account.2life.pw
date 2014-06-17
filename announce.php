<?php
	include("./module/config.php");
	session_start();
	if($_SESSION['login']!==true){
		header("Location: /login.php");
		exit(0);
	}
 
	spl_autoload_register(function($class){
		require preg_replace('{\\\\|_(?!.*\\\\)}', DIRECTORY_SEPARATOR, ltrim($class, '\\')).'.php';
	});
	use \Michelf\Markdown;

	function getannounce($link)
	{
		$top_artical=mysqli_fetch_all(mysqli_query($link,"select * from announce where at_top = 1 order by -date;"),MYSQLI_ASSOC);
		$normal_artical=mysqli_fetch_all(mysqli_query($link,"select * from announce where at_top = 0 order by -date;"),MYSQLI_ASSOC);
		return [$top_artical,$normal_artical];
	}

	function showannounce($link)
	{
		list($top_artical,$normal_artical)=getannounce($link);
		echo " <div class=\"container\" style=\"max-width:970px\">
            <div class=\"row row-offcanvas row-offcanvas-right\">
                <div class=\"col-xs-12\">
                    <div class=\"jumbotron\">
                        <h1>通知</h1>
					</div></div></div><div class=\"row\">";
		while(list($key,$var)=each($top_artical)){
			echo "<div class=\"panel panel-primary\">
				<div class=\"panel-heading\" id=\"userinfo\">
					<h4>".$var['title']."</h4>
				</div>";
			echo "<div class=\"panel-body\">".Markdown::defaultTransform($var['content'])."</div>";
			echo "<div class=\"panel-footer\">".$var['date']."</div></div>";
		}
		if($normal_artical){
			echo "<h4>其他通知:</h4>";
			echo "<ul class=\"list-group\">";
			while(list($key,$var)=each($normal_artical))
				echo "<li class=\"list-group-item\"><span class=\"badge\">".$var['date']."</span><a href=\"announce.php?id=".$var['id']."\">".$var['title']."</a></li>";
			echo "</ul>";
		}	
		echo "</div></div>";
	}
	function showpage($link)
	{
		if($stmt=mysqli_prepare($link,"select * from announce where id=?")){
			mysqli_stmt_bind_param($stmt,"s",$_GET['id']);
			mysqli_stmt_execute($stmt);
			$row=mysqli_fetch_array(mysqli_stmt_get_result($stmt));
			echo " <div class=\"container\" style=\"max-width:970px\"><h2>".$row['title']."</h2><h5>".$row['date']."</h5>".Markdown::defaultTransform($row['content'])."</div>";
		}
	}
 
?>
<!DOCTYPE html>
<html>
    <head>
        <title>物院量子传输研究所</title>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="">
        <link href="dist/css/bootstrap.css" rel="stylesheet">
        <link href="css/login.css" rel="stylesheet">
    </head>
    <body>
        <div class="navbar navbar-inverse navbar-static-top">
            <div class="container">
                <div class="navbar-header">
                    <button class="navbar-toggle" type="button" data-toggle="collapse" data-target=".navbar-collapse">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a href="index.php" class="navbar-brand">物院量子传输研究所</a>
                </div>
                <div class="navbar-collapse collapse">
                    <ul class="nav navbar-nav">
						<li><a href="announce.php">通知</a></li>
						<li><a href="status.php">服务器信息</a></li>
						<li><a href="about.php">关于</a></li>
					</ul>
                    <ul class="nav navbar-nav navbar-right">
                        <li><a href="module/account.php?function=logout">登出</a></li>
                    </ul>
                </div>
            </div>
		</div>
<?php
	if($_GET['id'])
		showpage($link);
	else
		showannounce($link);	
		?>
<script type="text/javascript" src="/dist/jquery-1.10.2.min.js"></script>
<script src="dist/js/bootstrap.min.js"></script>

	</body>
</html>

