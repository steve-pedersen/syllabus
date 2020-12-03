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
        ];
    }

    public function studentResources ()
    {
        $this->setResourcesTemplate();
        $schema = $this->schema('Syllabus_Syllabus_CampusResource');
        $resources = $schema->find($schema->deleted->isNull()->orIf($schema->deleted->isFalse()), ['orderBy' => 'title']);      
        
        $spotlight = $this->request->getQueryParameter('spotlight', null);
        $spotlight = $spotlight ? $schema->get($spotlight) : $resources[rand(0, count($resources) - 1)];
        
        $this->template->resources = $resources;
        $this->template->spotlight = $spotlight;
    }

    public function rss ()
    {
        $resources = $this->schema('Syllabus_Syllabus_CampusResource');
        $resources = $resources->find($resources->deleted->isNull());      

        $random3 = array_rand($resources, 2);
        $url = $this->baseUrl('resources');

        echo '<?xml version="1.0" encoding="UTF-8"?>';
        echo "<rss version='2.0' xmlns:atom='http://www.w3.org/2005/Atom'>";
        echo "<channel>";

        echo "<title>Student Resources</title>";
        echo "<description>This feed shows random resources available on campus to SF State students.</description>";
        echo "<link>$url</link>";

        foreach ($random3 as $key)
        {
            $item = $resources[$key];
            $title = rtrim($item->title);
            $desc = substr(rtrim($item->description), 0, 150) . '...';
            $link = rtrim($item->url);
            $link = $this->baseUrl('resources?spotlight=' . $item->id);
            $src = $this->baseUrl($item->imageSrc);

            echo "<item>";
                echo "<title>$title</title>";
                echo "<description><![CDATA[$desc]]></description>";
                echo "<pubDate>" . date("D, d M Y H:i:s T", time()) . "</pubDate>";
                echo "<link>$link</link>";
                echo "<atom:link href='$link' rel='self' type='application/rss+xml'/>";
            echo "</item>";
        }

        echo "</channel>";
        echo "</rss>";
        exit;
    }


}