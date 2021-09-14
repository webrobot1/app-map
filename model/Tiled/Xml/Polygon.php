<?php
namespace Edisom\App\map\model\Tiled\Xml;

class Polygon extends \Edisom\App\map\model\Tiled\Polygon
{
	public ?string $points = null;	
		
	public function parse():static
    {
		Xml::deserialize($this);
		if($polygon = explode(' ', $this->points)){
			if(end($polygon)!=$polygon[0])
				$polygon[] = $polygon[0];
		
			array_walk($polygon, function(&$item){ $item = str_replace(',', ' ', $item); });

			$this->points = implode(',', $polygon);
		}
		
		return $this;
    }
	
	//просто так он ничего не выдает
	public function __toString():string
    {
        return $this->points;
    }
}