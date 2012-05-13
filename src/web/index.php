<?php
require_once(__DIR__.'/../vendor/goutte.phar');
use Goutte\Client;

$image1 = filter_input(INPUT_GET, 'image1');
$image2 = filter_input(INPUT_GET, 'image2');
$image3 = filter_input(INPUT_GET, 'image3');

if ($image1 && $image2 && $image3) {
	$html = array();

	// Title
	$client = new Client();
	$crawler = $client->request('GET', sprintf('http://www.ma-confession.fr/page-%d.php', rand(1, 71)));
	$nodes = $crawler->filter('div.titre a');
	$titles = array();
	foreach ($nodes as $node) {
		$titles[] = $node->textContent;
	}
	shuffle($titles);
	$title = ucfirst(trim($titles[0], '"'));

	// Images
	$html[] = sprintf('<img src="data/%s/%s" />', basename(dirname($image1)), basename($image1));
	$html[] = sprintf('<img src="data/%s/%s" />', basename(dirname($image2)), basename($image2));
	$html[] = sprintf('<img src="data/%s/%s" />', basename(dirname($image3)), basename($image3));
} else {
	$dirData = __DIR__.'/data';
	$numImages = 3;
	if (isset($_GET['count'])) {
		$numImages = filter_input(INPUT_GET, 'count', FILTER_VALIDATE_INT);
	}

	// Get first image
	$imagesFirst = glob(sprintf('%s/*/1_*.jpg', $dirData));
	shuffle($imagesFirst);
	$imageFirst = $imagesFirst[0];

	// Get remaining images
	$images = glob(sprintf('%s/*/*.jpg', $dirData));
	shuffle($images);
	$images = array_splice($images, 0, $numImages);
	array_unshift($images, $imageFirst);
	$images = array_unique($images);
	for ($i = 0; $i < count($images); $i++) {
		$image = $images[$i];
		$parameters[] = sprintf('image%d=%s/%s',$i+1, basename(dirname($image)), basename($image));
	}
	$queryString = implode('&', $parameters);
	header('Location:?'.$queryString);
}
?>

<!doctype html>
<!-- paulirish.com/2008/conditional-stylesheets-vs-css-hacks-answer-neither/ -->
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="en"> <![endif]-->
<!-- Consider adding a manifest.appcache: h5bp.com/d/Offline -->
<!--[if gt IE 8]><!--> <html class="no-js" lang="fr" prefix="og: http://ogp.me/ns#"> <!--<![endif]-->
<head>
	<meta charset="utf-8">
	<title><?php echo $title ?> | Mille Milliards | Tirages Ingrats</title>
	<link href='http://fonts.googleapis.com/css?family=Vibur' rel='stylesheet' type='text/css'>
	<link href='http://fonts.googleapis.com/css?family=Comfortaa' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" type="text/css" href="style/main.css"></link>
	<meta property="og:description" content="Sordide fleuron de la littérature populaire, le roman photo aborde au travers de ses clichés empathiques les thèmes essentiels à l'épanouissement moral des lectrices et lecteurs civilisés. Tirages Ingrats est un générateur de fotonovelas aléatoires, absurdes et incongrues, prônant la décadence de l'empire Romance." />
	<script type="text/javascript">

	  var _gaq = _gaq || [];
	  _gaq.push(['_setAccount', 'UA-27467726-1']);
	  _gaq.push(['_trackPageview']);

	  (function() {
	    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
	    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
	    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
	  })();

	</script>
</head>

<body>
	<div id="container">
		<h1>Tirages Ingrats</h1>
		<h2>« <?php echo $title ?> »</h2>

		<p class="reload"><a href="<?php echo $_SERVER['PHP_SELF'] ?>">suivant >></a></p>

		<div class="images">
			<?php echo implode("\n", $html); ?>
		</div>

		<blockquote>Sordide fleuron de la littérature populaire, le roman photo aborde au travers de ses clichés empathiques les thèmes essentiels à l'épanouissement moral des lectrices et lecteurs civilisés. Tirages Ingrats est un générateur de fotonovelas aléatoires, absurdes et incongrues, prônant la décadence de l'empire Romance.</blockquote>

		<p class="footer"><a href="http://www.millemilliards.net/tiragesingrats/">Tirages Ingrats</a> est développé conjointement par <a href="http://templevengeance.incongru.org">Temple Vengeance</a> et <a href="http://www.constructions-incongrues.net">Constructions Incongrues</a>. Le code source est <a href="http://github.com/constructions-incongrues/net.millemilliards.tiragesingrats">diffusé</a> sous license <a href="http://millemilliards.net/identites/">AGPL3</a>. Le projet est hébergé par <a href="http://www.pastis-hosting.net">Pastis Hosting</a>.</p>
	</div>
</body>
</html>