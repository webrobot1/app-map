<?php
namespace Edisom\App\map\model\Tiled;

// объект отвечающий за изображения (из тайлов из tilemaps из слой изображения)
#[\Attribute]
class LayerObjectText extends Loader 
{
	public ?int $object_id = null;
	public string $text;
		
	#[LayerObjectProperty]
	public array $property = array();
}