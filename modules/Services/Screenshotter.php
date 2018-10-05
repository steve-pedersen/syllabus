<?php

use GuzzleHttp\Psr7;
use GuzzleHttp\Client;
use GuzzleHttp\Promise;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Exception\ConnectException;
use Psr\Http\Message\ResponseInterface;

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

    // The image to use for when Screenshotter can't access a given URL
    private $defaultImgName;


    public function __construct($app, $options=array(), $defaultImgName='')
    {
        $siteSettings = $app->siteSettings;
        $defaultOptions = array(
            'appName'   => ($app->configuration->appName ?? get_class($this)),
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
        $this->client = new Client( array('base_uri' => $this->apiUrl) );
    }

    public function concurrentRequests ($captureUrls, $cachedVersions=true)
    {
        $results = $imageUrls = $responses = $messages = $promises = array();

        // Initiate each request but do not block
        try {
            $query = $this->formatQueryString($cachedVersions);
            foreach ($captureUrls as $key => $url)
            {          
                $imageUrls[$key] = $this->defaultImgName;
                $promises[$key] = $this->client->getAsync('?url=' . urlencode($url) . $query);
            }

        } catch (ConnectException $e) {
            $messages[] = 'Could not connect to the Screenshotter service.';
        } 

        // Wait on all of the requests to complete. Throws a ConnectException
        // if any of the requests fail
        try {
            $responses = Promise\unwrap($promises);          
        } catch (Exception $e) {
            $messages[] = "Failed to obtain screenshots.";
        } 

        // Wait for the requests to complete, even if some of them fail
        try {
            $responses = Promise\settle($promises)->wait();        
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
        $imageName = 'screenshotter_sfsu_default.jpg'; // the fail-safe default
        if (($defaultImgName !== '') && (file_exists('assets/images/' . $defaultImgName)))
        {
            $imageName = $defaultImgName;
        }
        elseif ($settingsImage = $app->siteSettings->getProperty('screenshotter-default-img-name'))
        {
            if (file_exists('assets/images/' . $settingsImage))
            {
                $imageName = $settingsImage;
            }
        }

        return $imageName;
    }
}