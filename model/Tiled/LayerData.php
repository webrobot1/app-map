<?php
namespace Edisom\App\map\model\Tiled;

// данные слоя (layer)
#[\Attribute]
class LayerData extends Loader
{	
	#[LayerTile]
	public array $tiles = array();

	function __construct
	(
		public ?int $layer_id = null // заполниться сама после сохранения layer
	){
		parent::__construct();
	}
}