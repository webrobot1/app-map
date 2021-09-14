<?php
namespace Edisom\App\map\model\Tiled\Xml;

class Layer extends \Edisom\App\map\model\Tiled\Layer
{ 
	#####доп слои (если группа)###########
	#[Layer]
	public array $layer = array();		
	#[Layer]
	public array $group = array();	
	#[Layer]
	public array $imagelayer = array();	
	#[Layer]
	public array $objectgroup = array();
	######################################
	
	// тк у нас карты фиксированного размера тут эти данные не нужны
	//public ?float $width = null,
	//public ?float $height = null,
	// есть еще Parallax Scrolling Factor это типа небо как быстро скролится с движением камеры (по умолчанию любой слой движется синхронно с ней)	
	
	
	public function parse():static
	{
		// в зависимости от тега XML в базе запишется с определенным type 
		switch(Xml::$reader->name)
		{
			case "layer":
				$this->type = 'tilelayer';
			break;		
			default:
				$this->type = Xml::$reader->name;
			break;
		}
		Xml::deserialize($this);
		
		return $this;
	}
}