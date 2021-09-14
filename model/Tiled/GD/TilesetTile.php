<?php
namespace Edisom\App\map\model\Tiled\GD;

// координаты с изоюбражения tilemap или отдельное изображение (спрайт) с координатами 
#[\Attribute]
class TilesetTile extends \Edisom\App\map\model\Tiled\TilesetTile 
{
	function load():static
	{
		// для отрисовки НЕ нужны данные по свйоствам этого класса где указаны атрибуты Reflection
		unset(static::$keys[static::class]['objects']);

		return parent::load();
	}
}