<?php
namespace Edisom\App\map\model\Tiled\Xml;
use Edisom\App\map\model\Tiled\LayerObjectProperty;

class LayerObjectText extends \Edisom\App\map\model\Tiled\LayerObjectText
{
	public string $fontfamily = 'Arial';
	public int $pixelsize = 12;
	public string $halign = 'left';
	public string $color = '#000000';
	public string $valign = 'top';
	
	public function __toString():string
    {
        return $this->text;
    }
	
	public function parse():static
	{		
		$this->text = Xml::text();
		Xml::deserialize($this);
		
		if(($font = (substr(php_uname(), 0, 7) == "Windows"?'C:/Windows/Fonts/':'/usr/share/fonts/truetype/msttcorefonts/').$this->fontfamily.'.ttf') && !file_exists($font))
			throw new \Exception('не найден шрифт '.$font);
	
		foreach(['fontfamily', 'pixelsize', 'halign', 'color', 'valign'] as $property)
		{
			$this->property[] = new LayerObjectProperty(...['name'=>$property, 'value'=>$this->$property]);
		}
		return $this;
	}
}