<?php
namespace Edisom\App\map\model\Tiled;

// пользовательские поля
#[\Attribute]
class MapProperty extends Loader
{
	const TABLE = 'map__property';
	
	function __construct
	(
		public ?int $map_id = null,
	
		public ?string $name = null,
		public ?string $value = null,
	){		
		parent::__construct();
	}
}