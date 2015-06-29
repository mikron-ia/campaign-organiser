<?php

namespace Mikron\CampaignOrganiser;

use \Parsedown;

class Page
{
    private $content;

    public function __construct($file)
    {
        $parser = new Parsedown();
        $path = "data/" . $file . ".md";
        if (file_exists($path)) {
            $text = file_get_contents($path);
            $this->content = $this->render("Organizacja Kampanii CthulhuTech-a - $file", $parser->text($text));
        } else {
            $this->content = $this->notFound($parser);
        }
    }

    public function render($title, $body)
    {
        return '<!DOCTYPE HTML>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<link rel="stylesheet" href="style.css" type="text/css">
	<title>' . $title . '</title>
</head>
<body>
' . $body . '
</body>
</html>';
    }

    public function notFound($parser)
    {
        $text = file_get_contents("404.md");
        return $this->render("Organizacja Kampanii CthulhuTech-a - 404", $parser->text($text));

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
}