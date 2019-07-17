<?php

/**
 * The service functionality to connect to the Screenshotter API
 *
 * @author      Steve Pedersen (pedersen@sfsu.edu)
 * @copyright   Copyright &copy; San Francisco State University
 */
class Syllabus_Services_Screenshotter
{
    // Services API Gateway endpoint for Screenshotter
    private $apiUrl;

    // Services API Gateway Key     -- not implemented yet
    private $apiKey;

    // Services API Gateway Secret  -- not implemented yet
    private $apiSecret;

    // Screenshotter & Puppeteer options
    // https://pptr.dev/#?product=Puppeteer&version=v1.7.0&show=api-pagescreenshotoptions
    private $options;

    // GuzzleHttp client
    private $client;

    // Redis client - saves Access-Tokens for access to protected screenshot URL endpoints
    private $redisClient;

    // The image to use for when Screenshotter can't access a given URL
    private $defaultImgName;

    public function __construct($app, $options=array(), $defaultImgName='')
    {
        // echo "<pre>"; var_dump($test); die;
        $siteSettings = $app->siteSettings;
        $defaultOptions = array(
            // 'appName'   => ($app->configuration->appName ?? get_class($this)),
            'appName'   => sha1($app->baseUrl('')),
            'type'      => 'jpeg',
            'quality'   => '100',
            'width'     => '1024',
            'height'    => '768',
            'clip'      => $this->formatClip(
                array(
                    'x'     => '0',
                    'y'     => '75',
                    'width' => '1024',
                    'height'=> '768'
                )
            ),
        );
      
        $this->apiUrl = $siteSettings->getProperty('screenshotter-api-url');
        $this->apiKey = $siteSettings->getProperty('screenshotter-api-key');
        $this->apiSecret = $siteSettings->getProperty('screenshotter-api-secret');      
        $this->options = !empty($options) ? $this->parseOptions($options) : $defaultOptions;
        $this->defaultImgName = $this->setDefaultImage($app, $defaultImgName);
        $this->client = new GuzzleHttp\Client( array('base_uri' => $this->apiUrl) );
        $this->redisClient = new Predis\Client();
    }

    public static function CutUid ($key='')
    {
        $client = new Predis\Client();
        if ($uid = $client->get($key))
        {
            $client->del($key);
        }
        return $uid;
    }

    public function saveUids ($eid, $sids)
    {
        if (!is_array($sids)) $sids = array($sids);

        foreach ($sids as $sid)
        {
            $this->redisClient->set("{$eid}-{$sid}", uniqid());
        }
    }

    public function concurrentRequests ($captureUrls, $cachedVersions=true, $tokenPrefix='')
    {
        $results = $imageUrls = $responses = $messages = $promises = $options = array();

        // Initiate each request but do not block
        try {
            $query = $this->formatQueryString($cachedVersions);
            foreach ($captureUrls as $key => $url)
            {
                $imageUrls[$key] = $this->defaultImgName;
                $token = $this->redisClient->get($tokenPrefix . $key);
				$options = array('headers' => array('X-Custom-Header' => $token));
                $promises[$key] = $this->client->getAsync('?url=' . urlencode($url) . $query, $options);
            }
        // } catch (GuzzleHttp\Exception\ConnectException $e) {
        } catch (Exception $e) {
            $messages[] = 'Could not connect to the Screenshotter service.';
        }

        // Wait on all of the requests to complete. Throws a ConnectException
        // if any of the requests fail
        try {
            $responses = GuzzleHttp\Promise\unwrap($promises);
        } catch (Exception $e) {
            $messages[] = 'Failed to obtain some screenshots.';
        }

        // Wait for the requests to complete, even if some of them fail
        try {
            $responses = GuzzleHttp\Promise\settle($promises)->wait();
        } catch (Exception $e) {
            $messages[] = 'Screenshotter service failed.';
        } 

        // Replace the default image name with the one-time download image url returned from Screenshotter
        foreach ($responses as $key => $result)
        {
            if (isset($result['value']) && $result['state'] === 'fulfilled')
            {
                $imageUrls[$key] = json_decode($result['value']->getBody()->getContents())->imageurl;
            }
        }

        $results['imageUrls'] = $imageUrls;
        $results['messages'] = $messages;

        return json_encode($results);
    }

    protected function formatQueryString ($cachedVersions=true)
    {
        $query = '';
        foreach ($this->options as $key => $value)
        {
            $query .= "&{$key}={$value}";
        }
        $query .= $cachedVersions ? '&version=cached' : '&version=new';

        return $query;
    }

    // right now, 'clip' is the only one that requires special attention
    protected function parseOptions ($options)
    {
        if (isset($options['clip']))
        {
            $options['clip'] = $this->formatClip($options['clip']);
        }

        return $options;
    }

    protected function formatClip ($clipOptions=array())
    {
        $clip = '';
        if (is_string($clipOptions) && (strpos($clipOptions, ',') !== false))
        {
            $clip = $clipOptions;
        }
        else
        {
            $i = 0;
            foreach ($clipOptions as $key => $value)
            {
                $i++;
                $clip .= $key . '=' . $value;
                if ($i !== count($clipOptions))
                {
                    $clip .= ',';
                }
            }
        }

        return $clip;
    }

    // TODO: Update this to use file upload...
    protected function setDefaultImage ($app, $defaultImgName='')
    {
        $path = 'assets/images/';
        $imageName = 'screenshotter_sfsu_default.jpg';
        $imageName = 'testing01.jpg';

        if (($defaultImgName !== '') && (file_exists($path . $defaultImgName)))
        {
            $imageName = $defaultImgName;
        }
        elseif ($settingsImage = $app->siteSettings->getProperty('screenshotter-default-img-name'))
        {
            $imageName = $settingsImage;
        }

        return $path . $imageName;
    }
}