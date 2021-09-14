<?php
namespace Edisom\App\map\model\Tiled\Xml;

class TilesetTile extends \Edisom\App\map\model\Tiled\TilesetTile 
{
	// порядковый (не абсолютный) номер тайла в наборе начиная с 0? может использоваться в конкутторе
	public ?int $id = null;
	public static array $tileid = array();
	
	public function save():void
	{			
		parent::save();		
		if(!static::$tileid[$this->id] = $this->tile_id)
		{
			throw new \Exception('Не удалось присвоить tile_id');
		}
	}
}