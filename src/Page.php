<?php

namespace Mikron\CampaignOrganiser;

use \Parsedown;

class Page
{
    private $content;

	/**
	 * Class constructor
	 * @param string $file Name of the file to be processed and parsed
	 * @param Slim\Slim $app Slim application object
	 */
    public function __construct($file, $app)
    {
        $req = $app->request;
        $baseUrl = $req->getUrl()."".$req->getRootUri()."/";

        $parser = new Parsedown();
        $path = $this->preparePath($file);

        if ($path) {
            $text = file_get_contents($path);

            $content = $this->render(ucfirst($file), $parser->text($text), $baseUrl);
            $date = date("Y-m-d H:i", filemtime($path));

            $replacedDates = $this->replaceDates($content,$date);

            $this->content = $replacedDates;
        } else {
            $this->content = $this->notFound($parser, $baseUrl);
        }

    }

	/**
	 * Simple renderer for the page
	 * @param string $title Page title
	 * @param string $body Page body content
	 * @param string $baseUrl Base URL used for CSS file
	 * @return string Page to be displayed
	 * @todo Replace with proper view
	 */
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

	/**
	 * Returns 404 page
	 * @param Parsedown $parser Data object
	 * @param string $baseUrl Base URL
	 * @return string 404 page
	 */
    public function notFound($parser, $baseUrl)
    {
        $text = file_get_contents("404.md");
        return $this->render("Not found", $parser->text($text), $baseUrl);
    }

    /**
	 * Parsed content getter
	 * @return string
	 */
    public function getContent()
    {
        return $this->content;
    }

	/**
	 * Magic method implementation
	 * @return string
	 */
    public function __toString()
    {
        return $this->getContent();
    }

	/**
	 * Replaces {date} with provided string
	 * @param string $text Text to be worked
	 * @param string $date String to replace {date} tag
	 * @return string
	 */
    private function replaceDates($text, $date)
    {
        return str_replace('{date}', $date, $text);
    }

	/**
	 * Locates file and provides proper path
	 * @param string $file
	 * @return mixed String with proper path to existing file, or null if not found
	 */
    private function preparePath($file)
    {
		$baseDirectory = "data";
        $uriBasic = $baseDirectory . "/" . $file . ".md";
		$uriComplicated = $baseDirectory . "/" . str_replace('-', '/', $file) . ".md";

        if (file_exists($uriComplicated)) {
            $path = $uriComplicated;
        } elseif (file_exists($uriBasic)) {
            $path = $uriBasic;
        } else {
            $path = null;
        }
        return $path;
    }
}
