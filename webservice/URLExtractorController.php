<?php

/**
 * Description of URLExtractorController
 *
 * @author padman
 */
class URLExtractorController {
    public function authorize()
    {
        if(isset($_SESSION['user']))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Extract URL content
     * @noAuth
     * @url GET /extracturl
     */
    public function extractURL()
    {
        $tags = get_meta_tags($_GET['url']);
        $title = $this->get_title($_GET['url']);
        $desc = '';
        if(isset($tags['description'])){
          $desc = $tags['description'];
        }
        $json['title'] = $title;
        $json['desc'] = $desc;
        return $json;
    }

    function get_title($url){
      $fp = file_get_contents($url);
        if (!$fp)
            return null;

        $res = preg_match("/<title>(.*)<\/title>/siU", $fp, $title_matches);
        if (!$res)
            return null;

        // Clean up title: remove EOL's and excessive whitespace.
        $title = preg_replace('/\s+/', ' ', $title_matches[1]);
        $title = trim($title);
        return $title;
    }
}
