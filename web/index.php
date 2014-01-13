<?php
// web/index.php
use \Bodul\Search\ElasticLocator2;
use \Bodul\Search\ElasticTagLocator;
use \Bodul\Search\Cache;
use Symfony\Component\HttpFoundation\Response;

require_once __DIR__ . '/../vendor/autoload.php';
include __DIR__ . '/../app/config/config.php';

$app = new Silex\Application();

$app->register(
    new EasyBib\Service\Elastica\ElasticaServiceProvider(),
    array(
         'elastica.client_options' => array(
             'host' => ELASTICA_HOST,
             'port' => ELASTICA_PORT,
         ),
    )
);
$app->error(
    function (\Exception $e, $code) {
        return new Response('We are sorry, but something went terribly wrong.');
    }
);

$app->get(
    '/suggestions/{name}.json', function ($name) use ($app) {

        // Load cache class.
        $cache = new Cache\Apc($name);

        // Fetch cached content.
        $cache->fetch();

        // If no cache.
        if (!$cache->exists()) {

            $results = array();

            // Instantiating locator.
            $locator = new ElasticTagLocator($app['elastica'], $name);

            // Iterate through results and prepare data,
            foreach ($locator->getData()->getResults() as $result) {

                $data = $result->getData();

                $results[] = array(
                    'id'     => $data['id'],
                    'value'  => $data['tagName'],
                    '_score' => $result->getScore()
                );
            }

            // If there were results cache them.
            if (count($results) > 0) {
                $cache->saveResult($results);
            }

        }

        //return json with results
        return $app->json($cache->getResult());

    }
);

$app->run();
