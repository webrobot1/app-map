<?php
namespace Edisom\App\map\model\Tiled;

// пользовательские поля
#[\Attribute]
class TilesetTileProperty extends Loader
{
	const TABLE = 'map__tileset__tile__property';
	
	function __construct
	(
		public ?int $tile_id = null,
		public ?string $name = null,
		public ?string $value = null,
		public ?string $type = null,
	){
		parent::__construct();
	}
}