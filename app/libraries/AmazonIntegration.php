<?php
include('amazon_php/lib/AmazonECS.class.php');

class AmazonFetcher {
    
    private $amazonecs;
    
    function __construct()
    {
        $this->amazonecs = new \AmazonECS('AKIAIRNB4NTD6UQAGSFQ',
            'MtB7k+IBzPAiQsZHmcI8ROt2Ro0YdKWKJy8bvYwd',
            'COM',
            'isgasa-20');
    }
    
    function medium( $title )
    {
        return $this->amazonecs->
            category('All')->
            responseGroup('Medium')->
            search($title);
    }
    
}
         
?>
