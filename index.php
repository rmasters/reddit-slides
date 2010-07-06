<!DOCTYPE html>
<html>
<head>
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
    <script type="text/javascript" src="./script.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            <?php if (isset($_GET["r"])): ?>
            subreddit = "<?= $_GET["r"] ?>";
            <?php elseif (isset($_GET["url"])): ?>
            customUrl = "<?= $_GET["url"] ?>";
            <?php else: ?><?php endif; ?>
            
			loadImages(null);
        });
    </script>
	<style type="text/css">
		#images .current {
			display: block;
			text-align: center;
			margin: 0 auto;
		}
		
		#images .current img {
			max-height: 100%;
			max-width: 100%;
		}
	</style>
</head>
<body id="images"></body>
</html>