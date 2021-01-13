<?php

/**
 * Handles main business logic for Student resources
 * 
 * @author      Steve Pedersen <pedersen@sfsu.edu>
 * @copyright   Copyright &copy; San Francisco State University
 */
class Syllabus_Resources_Controller extends Syllabus_Master_Controller {

    public static function getRouteMap ()
    {
        return [
            'resources'                 => ['callback' => 'studentResources'],
            'resources/feed'            => ['callback' => 'rss'],
            'resources/json'            => ['callback' => 'json'],
        ];
    }

    public function studentResources ()
    {
        $this->setResourcesTemplate();
        $schema = $this->schema('Syllabus_Syllabus_CampusResource');
        $tags = $this->schema('Syllabus_Resources_Tag');
        $resources = $schema->find($schema->deleted->isNull()->orIf($schema->deleted->isFalse()), ['orderBy' => 'title']);      
        $spotlight = $schema->get($this->request->getQueryParameter('spotlight', null));
        $spotlight = $spotlight && !$spotlight->deleted ? $spotlight : $resources[rand(0, count($resources) - 1)];
        
        $this->template->resources = $resources;
        $this->template->spotlight = $spotlight;
        $this->template->tags = $tags->getAll(['orderBy' => 'name']);
        $this->template->filter = $this->request->getQueryParameter('category');
    }

    public function rss ()
    {
        $resources = $this->schema('Syllabus_Syllabus_CampusResource');
        $resources = $resources->find($resources->deleted->isNull());      

        $random3 = array_rand($resources, 3);
        $url = $this->baseUrl('resources');

        echo '<?xml version="1.0" encoding="UTF-8"?>';
        echo "<rss version='2.0' xmlns:atom='http://www.w3.org/2005/Atom'>";
        echo "<channel>";

        echo "<title>Student Resources</title>";
        echo "<description>This feed shows random resources available on campus to SF State students.</description>";
        echo "<link>$url</link>";

        $descLength = 140;
        foreach ($random3 as $key)
        {
            $item = $resources[$key];
            $title = rtrim($item->title);
            $end = (strlen(strip_tags($item->description)) <= $descLength) ? '' : '...';
            // $end = (strlen($item->description) <= $descLength) ? '' : '';
            $desc = substr(trim(strip_tags(str_replace("\r", '', $item->description))), 0, $descLength) . $end;
            // $desc = substr(trim($item->description), 0, $descLength) . $end;
            $desc = str_replace("\"", "'", $desc);
            $link = rtrim($item->url);
            $link = $this->baseUrl('resources?spotlight=' . $item->id);
            $src = $this->baseUrl($item->imageSrc);
            $img = "<img src='".$src."' alt='$title logo' class='img-responsive' style='display:block;'>";

            echo "<item>";
                echo "<title>$title</title>";
                echo "<description><![CDATA[". $img . $desc ."]]></description>";
                echo "<description>$desc</description>";
                echo "<pubDate>" . date("D, d M Y H:i:s T", time()) . "</pubDate>";
                echo "<link>$link</link>";
                echo "<id>$link</id>";
                echo "<atom:link href='$link' rel='self' type='application/rss+xml'/>";
            echo "</item>";

            break; // only do 1 item
        }

        echo "</channel>";
        echo "</rss>";
        exit;
    }

    public function json ()
    {
        $resources = $this->schema('Syllabus_Syllabus_CampusResource');
        $resources = $resources->find($resources->deleted->isNull()->orIf($resources->deleted->isFalse()));
        $tags = $this->schema('Syllabus_Resources_Tag');
        $tags = $tags->findValues('name');

        $formatted = [];
        $formatted['website'] = $this->baseUrl('resources');
        $formatted['allCategories'] = $tags;
        $formatted['resources'] = [];
        foreach ($resources as $resource)
        {
        	// $formatted[$resource->id] = [];
        	$item = [];
        	$item['resourceId'] = $resource->id;
        	$item['title'] = rtrim($resource->title);
        	$item['description'] = trim(strip_tags(str_replace("\r", '', $resource->description)));
        	$item['url'] = $this->baseUrl('resources?spotlight=' . $resource->id);
        	$item['image'] = $this->baseUrl($resource->imageSrc);
        	$item['createdDate'] = $resource->createdDate->format('Y-m-d h:i a');
        	$item['modifiedDate'] = $resource->modifiedDate ? $resource->modifiedDate->format('Y-m-d h:i a') : $item['createdDate'];
        	$item['image'] = $this->baseUrl($resource->imageSrc);
        	$item['categories'] = [];
        	foreach ($resource->tags as $tag)
        	{
        		$item['categories'][] = $tag->name;
        	}

        	$formatted['resources'][] = $item;
        }

        echo json_encode($formatted);
        exit;
    }
}