<?php
namespace Edisom\App\map\model\Tiled\Xml;

class Image extends \Edisom\App\map\model\Tiled\Image
{	
	public ?string $source = null;
	public ?float $width = null;
	public ?float $height = null;		
	public ?string $trans = null;
	
	function parse():static
	{
		Xml::deserialize($this);
		
		if(($filename = static::$folder.$this->source) && !file_exists($filename))
			throw new \Exception("не найдено изображение ".$filename);
		
		return $this;
	}
	
	// мы можем использовать этот объект как строку тогда возвращает только source картинки
	public function __toString():string
    {
        return $this->source;
    }
}