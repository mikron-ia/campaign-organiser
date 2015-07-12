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

    private function prepareResult()
    {
        $replacedDates = $this->replaceDates($this->original, $this->data['date']);
        $replacedIncludes = $this->replaceIncludes($replacedDates);

        $this->result = $replacedIncludes;
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

    private function replaceIncludes($text)
    {
        return $text;
    }
}
