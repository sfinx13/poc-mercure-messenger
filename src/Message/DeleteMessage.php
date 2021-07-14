<?php


namespace App\Message;


class DeleteMessage
{
    /**
     * @var string
     */
    protected $extension;

    /**
     * @return string
     */
    public function getExtension(): string
    {
        return $this->extension;
    }

    /**
     * @param string $extension
     * @return DeleteMessage
     */
    public function setExtension(string $extension): DeleteMessage
    {
        $this->extension = $extension;
        return $this;
    }



}
