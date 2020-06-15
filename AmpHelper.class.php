<?php
/**
 * Copyright (c) 2020. Created By Leonidas Rousias
 */

class AmpHelper{

    private static $instagram  =   '/<blockquote.*?data-instgrm-permalink=".*instagram.com\/p\/(.*?)\/.*?".*?[\n|\r]*<\/blockquote>/s';
    private static $twitter    =   '/<blockquote.*?href=".*?twitter.com\/.*?.*?\/status\/(.*?)\?.*?<\/blockquote>/s';
    private static $tiktok    =   '/<blockquote.*?data-video-id="(.*?)".*?[\n|\r]*<\/blockquote>/s';
    private static $youtube    =   '/<iframe.*?src=".*?youtube.com\/embed\/(.*?)".*?<\/iframe>/s';
    private static $facebook   =   '/<iframe.*?src=".*?facebook.com.*?post.php\?href=(.*?)\&.*?". *?<\/iframe>/s';
    private static $images     =   '/<img.*?src=".*?\/images\/0\/0\/(.*?)".*?>/s';
    private static $replaces = [
        "<iframe"   => "<amp-iframe",
        "</iframe>" => "</amp-iframe>",
    ];


    public static function changeHTML($html)
    {
        self::checkInstagram($html);
        self::checkTwitter($html);
        self::checkYoutube($html);
        self::checkFacebook($html);
        self::checkImages($html);
        self::checkTiktok($html);
        list($from,$to) = self::fixArrayToReplace();
        $html = str_replace($from,$to,$html);
        return $html;
    }

    private static function checkTwitter(&$html){
        $html = preg_replace_callback(
            self::$twitter ,
            ['AmpHelper', 'replaceTwitter'], $html);
    }
    private static function checkYoutube(&$html){
        $html = preg_replace_callback(
            self::$youtube ,
            ['AmpHelper', 'replaceYoutube'], $html);
    }
    private static function checkFacebook(&$html){
        $html = preg_replace_callback(
            self::$facebook,
            ['AmpHelper', 'replaceFacebook'], $html);
    }
    private static function checkImages(&$html){
        $html = preg_replace_callback(
            self::$images  ,
            ['AmpHelper', 'replaceImage'], $html);
    }
    private static function checkTiktok(&$html){
        $html = preg_replace_callback(
            self::$tiktok ,
            ['AmpHelper', 'replaceTiktok'], $html);
    }
    private static function checkInstagram(&$html){
        $html = preg_replace_callback(
            self::$instagram,
            ['AmpHelper', 'replaceInstagram'], $html);
    }


    private static function replaceYoutube($a){
        return '<amp-youtube data-videoid="'.$a[1].'" layout="responsive" width="400" height="300"></amp-youtube>';
    }
    private static function replaceFacebook($a){
        return '<amp-facebook width="552" height="310" layout="responsive" data-href="'.urldecode($a[1]).'"></amp-facebook>';
    }
    private static function replaceTwitter($a){
        return '<amp-twitter width="375" height="472" layout="responsive" data-tweetid="'.$a[1].'"></amp-twitter>';
    }
    private static function replaceInstagram($a){
        return '<amp-instagram data-shortcode="'.$a[1].'" width="1" height="1" layout="responsive"></amp-instagram>';
    }
    private static function replaceImage($a){
        return '<amp-img src="'.configController::getImagePath($a[1],0,300) .'" width="1" height="1" layout="responsive"></amp-img>';
    }
    private static function replaceTiktok($a){
        return '<amp-iframe layout="responsive" width="600" height="480" src="https://www.tiktok.com/embed/v2/'.urldecode($a[1]).'" sandbox="allow-scripts allow-same-origin"></amp-iframe>';
    }

    private static function fixArrayToReplace(){
        $doubleReplaceArrays[0] = [];
        $doubleReplaceArrays[1] = [];
        foreach(self::$replaces as $key => $repl){
            $doubleReplaceArrays[0][] = $key;
            $doubleReplaceArrays[1][] = $repl;
        }
        return $doubleReplaceArrays;
    }
}
