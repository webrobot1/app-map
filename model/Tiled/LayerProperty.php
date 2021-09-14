<?php
namespace Edisom\App\map\model\Tiled;

// пользовательские поля
#[\Attribute]
class LayerProperty extends Loader
{
	const TABLE = 'map__layer__property';
	
	function __construct
	(
		public ?int $layer_id = null,
		public ?string $name = null,
		public ?string $value = null,
	){		
		parent::__construct();
	}
}