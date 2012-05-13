<?php
namespace Net\Millemilliards\Romance;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Goutte\Client;

class UpdateCacheCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('romance:update-cache')
            ->setDescription('Updates local cache from girls.fr');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
    	$urlBase = 'http://www.girls.fr/roman-photo_234.php';
    	$dirData = '/home/trivoallan/dev/sources/constructions-incongrues/net.millemilliards.tiragesingrats/src/web/data';

    	// Get pagination URLs
    	$pages = array();
    	$client = new Client();
    	$crawler = $client->request('GET', $urlBase);
    	$nodes = $crawler->filter('div.pagination li a');
		foreach ($nodes as $node) {
			$pages[] = $node->getAttribute('href');
		}
		$pages = array_unique($pages);

    	// For each page, get list of romans photos
    	$romans = array();
    	foreach ($pages as $page) {
    		$urlPage = sprintf('http://www.girls.fr/%s', $page);
    		$output->writeln(sprintf('Fetching list of romans for page %s', $urlPage));
    		$crawler = $client->request('GET', $urlPage);
    		$nodes = $crawler->filter('ol.liste h2 a');
    		foreach ($nodes as $node) {
    			$roman = array('url' => 'http://www.girls.fr/'.$node->getAttribute('href'), 'title' => $node->textContent, 'id' => basename($node->getAttribute('href'), '.html'));	
    			$romans[] = $roman;
    			$output->writeln(sprintf('Processing roman %s (%s)', $roman['title'], $roman['id']));
    			$dirRoman = $dirData.'/'.$roman['id'];
    			if (!is_dir($dirRoman)) {
    				$output->writeln(sprintf('Creating cache for roman "%s"', $roman['id']));
    				mkdir($dirRoman);
    				file_put_contents($dirRoman.'/manifest.json', json_encode($roman));
    				$crawler = $client->request('GET', $roman['url']);
    				$link = $crawler->selectLink('Tout voir')->link();
    				$crawler = $client->click($link);
    				$nodes = $crawler->filter('div.mosaique li a');
    				foreach ($nodes as $node) {
	    				$crawler = $client->request('GET', $node->getAttribute('href'));
	    				$nodes = $crawler->filter('div.visuel img');
	    				foreach ($nodes as $node) {
	    					$output->writeln(sprintf('Downloading image "%s"', $node->getAttribute('src')));
	    					$data = file_get_contents($node->getAttribute('src'));
	    					file_put_contents($dirRoman.'/'.basename($node->getAttribute('src')), $data);
	    				}
    				}
    			}
    		}
    	}

    	// For each roman photo, update or create local cache
    }
}