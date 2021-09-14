<?php
namespace Edisom\App\map\model\Tiled\Xml;
use Edisom\App\map\model\Tiled\TilesetTileFrame; // nr мы не переопределяли ы xml этот класс используем родительский

class Map extends \Edisom\App\map\model\Tiled\Map
{	
	#[Layer]
	public array $imagelayer = array();		// слой изображения в корне (по сути просто канртинка на карте с позиционированием)
	#[Layer]
	public array $objectgroup = array();	// группы объектов на карте (кружочки, точки, картинки и части tilemaps которые можно тянуть , двигать и тп в рамках слоя)
	#[Layer]
	public array $group = array();			// группы (папки) слоев
	
	public function parse(string $source):static
	{
		Xml::deserialize($this, $source);	
		return $this;
	}	
	
	public function save():void
	{
		parent::save();
		foreach($this->query('SELECT animation_id , tileid FROM '.TilesetTileFrame::TABLE.' WHERE tile_id IN ('.implode(',', TilesetTile::$tileid).')') as $tiled)
		{
			$this->query('UPDATE '.TilesetTileFrame::TABLE.' SET tileid = '.TilesetTile::$tileid[$tiled['tileid']].' WHERE animation_id='.$tiled['animation_id']);
		}		
	}
}