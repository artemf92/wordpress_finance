<?php
/**
 * Theme: Flat Bootstrap
 * 
 * The template used for displaying next / previous page links
 *
 * @package flat-bootstrap
 */
?>

<?php

/*
 * If page content split into multiple pages, display links
 */
the_posts_pagination(['mid_size' => 3]);