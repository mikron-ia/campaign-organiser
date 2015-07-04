<?php

namespace Mikron\CampaignOrganiser;

use \Parsedown;

class Page
{
    private $content;

    public function __construct($file, $app)
    {
        $req = $app->request;
        $baseUrl = $req->getUrl()."".$req->getRootUri()."/";

        $parser = new Parsedown();
        $path = "data/" . $file . ".md";

        if (file_exists($path)) {
            $text = file_get_contents($path);

            $content = $this->render(ucfirst($file), $parser->text($text), $baseUrl);
            $date = date("Y-m-d H:i", filemtime($path));

            $replacedDates = $this->replaceDates($content,$date);

            $this->content = $replacedDates;
        } else {
            $this->content = $this->notFound($parser, $baseUrl);
        }

    }

    public function render($title, $body, $baseUrl)
    {
        return '<!DOCTYPE HTML>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<link rel="stylesheet" href="'.$baseUrl.'style.css" type="text/css">
	<title>' . $title . '</title>
</head>
<body>
' . $body . '
</body>
</html>';
    }

    public function notFound($parser, $baseUrl)
    {
        $text = file_get_contents("404.md");
        return $this->render("Not found", $parser->text($text), $baseUrl);
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    public function __toString()
    {
        return $this->getContent();
    }

    private function replaceDates($text, $date)
    {
        return str_replace('{date}', $date, $text);
    }
}
