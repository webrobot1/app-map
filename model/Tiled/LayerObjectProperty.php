<?php
namespace Edisom\App\map\model\Tiled;

// пользовательские поля
#[\Attribute]
class LayerObjectProperty extends Loader
{
	const TABLE = 'map__layer__object__property';
	
	function __construct
	(
		public ?int $object_id = null,
		public ?string $name = null,
		public ?string $value = null,
	){		
		parent::__construct();
	}
}