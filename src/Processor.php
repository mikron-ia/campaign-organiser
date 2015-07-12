<?php

namespace Mikron\CampaignOrganiser;

class Processor
{
    private $original;
    private $result;
    private $data;

    public function __construct($text, $data)
    {
        $this->original = $text;
        $this->data = $data;
    }

    public function getResult()
    {
        if(empty($this->result)) {
            $this->prepareResult();
        }

        return $this->result;
    }

	/**
	 * Formats the text via tag replacements and sets it to result field
	 */
    private function prepareResult()
    {
        $contentWithReplacedConsts = $this->replaceConst($this->original, $this->data['constTagReplacements']);
        $contentWithReplacedIncludes = $this->replaceIncludes($contentWithReplacedConsts, $this->data['baseForShortFilenames']);

        $this->result = $contentWithReplacedIncludes;
    }

    /**
     * Replaces {date} with provided string
     * @param string $text Text to be worked
     * @param string[] $constTagReplacements Replacement data in form of 'tag' => 'replacement'
     * @return string
     */
    private function replaceConst($text, $constTagReplacements)
    {
		foreach($constTagReplacements as $tag => $replacement) {
			$text = str_replace('{'.$tag.'}', $replacement, $text);
		}
		return $text;
    }

	/**
	 *
	 * @param string $text Text to be worked
	 * @param string $path Base path for short includes
	 * @return string
	 */
    private function replaceIncludes($text, $path)
    {
		$short = [];
		$long = [];
        preg_match_all('@{include-short:(.+?)}@', $text, $short, PREG_SET_ORDER);
		preg_match_all('@{include-long:(.+?)}@', $text, $long, PREG_SET_ORDER);

		$filenames = [];

		foreach($short as $row) {
			if(!empty($path)) {
				$filename = $path.'-'.$row[1];
			} else {
				$filename = $row[1];
			}
			$filenames[$row[0]] = $filename;
		}

		foreach($long as $row) {
			$filenames[$row[0]] = $filename;
		}

		foreach($filenames as $tag => $filename) {
			$page = new Page($filename, null);
			$text = str_replace($tag, $page->getBody(), $text);
		}

        return $text;
    }
}
