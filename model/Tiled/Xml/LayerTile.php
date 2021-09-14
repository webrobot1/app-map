<?php
namespace Edisom\App\map\model\Tiled\Xml;

class LayerTile extends \Edisom\App\map\model\Tiled\LayerTile
{	
	function __construct
	(
		public ?int $id = null,
		public ?int $horizontal = null,	
		public ?int $vertical = null,
		public ?int $diagonal = null
	){
		parent::__construct();
	}
	
	function save():void
	{
		if(!$this->tile_id = TilesetTile::$tileid[$this->id])
		{
			throw new \Exception("не найден tile_id ".$this->id);	
		}			
		parent::save();
	}	
}