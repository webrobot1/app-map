<?php
namespace Edisom\App\map\model\Tiled;

// анимация
#[\Attribute]
class TilesetTileFrame extends Loader 
{
	const TABLE = 'map__tileset__tile__animation';
	
	function __construct
	(
		public ?int $animation_id = null,		
		public ?int $tile_id = null,		
		public ?int $tileid = null,
		public ?int $duration = null,
	){
		parent::__construct();
	}
}