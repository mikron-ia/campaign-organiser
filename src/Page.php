<?php

namespace Mikron\CampaignOrganiser;

use \Parsedown;

class Page
{
    private $content;
    private $body;

    /**
     * Class constructor
     * @param string $file Name of the file to be processed and parsed
     * @param Slim\Slim $app Slim application object
     */
    public function __construct($file, $app = null)
    {
		if($app) {
			$req = $app->request;
			$baseUrl = $req->getUrl() . "" . $req->getRootUri() . "/";
		} else {
			$baseUrl = null;
		}

        $parser = new Parsedown();
        $path = $this->preparePath($file);

        if ($path) {
            $text = file_get_contents($path);

            $this->body = $parser->text($text);

            $content = $this->render(ucfirst($file), $this->body, $baseUrl);

            $dataForProcessor = [
				'baseForShortFilenames' => $this->prepareBaseForShortFilenames($file),
				'constTagReplacements' => [
					'date' => date("Y-m-d H:i", filemtime($path)),
				]
            ];

            $processor = new Processor($content, $dataForProcessor);

            $this->content = $processor->getResult();
        } else {
            $this->body = "[BODY NOT FOUND]";
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
        if (file_exists('data/style.css')) {
            $additionalCSS = $baseUrl . 'data/style.css';
        } else {
            $additionalCSS = '';
        }

        return '<!DOCTYPE HTML>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<link rel="stylesheet" href="' . $baseUrl . 'style.css" type="text/css">'
        . (!empty($additionalCSS) ? '<link rel="stylesheet" href="' . $additionalCSS . '" type="text/css">' : '')
        . '<title>' . $title . '</title>
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
     * Full-packed content getter
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Parsed content getter
     * @return string
     */
    public function getBody()
    {
        return $this->body;
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

	/**
	 * Prepares base for short filenames, suitable for includes
	 * @param string $file Filename for data extraction
	 * @return string Base for short filenames - everything but the last segment
	 */
	private function prepareBaseForShortFilenames($file)
	{
		$spread = explode('-',$file);
		array_pop($spread);
		$cut = implode('-',$spread);

		return $cut;
	}
}
