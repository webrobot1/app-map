<?php
namespace Edisom\App\map\model\Tiled;

// данные слоя (layer)
#[\Attribute]
class LayerTile extends Loader
{	
	const TABLE = 'map__layer__tile';
	
	function __construct
	(
		public ?int $tile_id = null,
		public ?int $layer_id = null,
		public ?int $horizontal = null,	
		public ?int $vertical = null,
		public ?int $diagonal = null
	){
		parent::__construct();
	}
}