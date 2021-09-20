<?php
namespace Edisom\App\map\model\Tiled;

abstract class Loader extends \Edisom\Core\Model
{	
	const TABLE = null;

	public static array $keys = array();
	protected static $folder = null;
	
	// тк можно переопределять классы то надо смотреть какой namespace у первого объявленого класса
	private static string $namespace;
	
	// в качестве атрибутов ставить лишь поля что будут записываться в бд (и приходить из нее)
	public function __construct()
	{	
		if(!static::$folder)
			throw new \Exception('не указана папка размещения фаилов а переменной static::$folder');
		

		parent::__construct();	
		static::keys();
	}	
	
	// получить данные текущего объекта ($name = вспомогательное поле)
	protected static function get(array $where)
	{
		return static::query('SELECT * FROM '.static::TABLE.' WHERE '.static::explode($where, 'AND'));
	}	
	
	// получить данные текущего объекта ($name = вспомогательное поле)
	protected static function update(array $carguments)
	{
		return static::query('REPLACE INTO '.static::TABLE.' SET '.static::explode($carguments));
	}
	
	// загружает в статическую переменную информацию о : полях что есть в базе на таблицу, вторичные ключи, свойства (объекты или массивы) текущего класса имеющие атрибуты Reflection (те какой класс загружать на это свойство)
	// необходимо тогда когда не требуется инициализировать класс но уже нужны данные эти (в методахъ load)
	final protected static function keys():bool
	{
		if(!isset(static::$keys[static::class]))
		{
			$reflection = new \ReflectionClass(static::class);
			
			if(empty(static::$namespace))
				static::$namespace = $reflection->getNamespaceName();
			
			// сохраним свйоства класса что пишутся в бд если у класса объявлена бд
			if(static::TABLE)
			{
				if(static::$keys[static::class]['fields'] = static::query('SELECT COLUMN_NAME, IFNULL(EXTRA, COLUMN_KEY) as EXTRA FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = "'.static::config('database')['bd'].'" AND TABLE_NAME = "'.static::TABLE.'"', MYSQl_FLAG_ASSOC))
				{
					static::$keys[static::class]['increment'] = array_search("auto_increment", static::$keys[static::class]['fields']);
					if(!static::$keys[static::class]['primary'] = (array)array_keys(static::$keys[static::class]['fields'], "PRI"))
						 static::$keys[static::class]['primary'] = (array)static::$keys[static::class]['increment'];
				}
				static::$keys[static::class]['foreigns'] = static::query('
					SELECT 
						REFERENCED_COLUMN_NAME, 
						COLUMN_NAME
					FROM
						INFORMATION_SCHEMA.KEY_COLUMN_USAGE
					WHERE
						REFERENCED_TABLE_SCHEMA = "'.static::config('database')['bd'].'" AND TABLE_NAME = "'.static::TABLE.'"', MYSQl_FLAG_ASSOC);
			}

			
			// список объектов и массивов класса
			foreach($reflection->getProperties() as $property)
			{
				if(!$property->isStatic())
				{
					if($attribute = $property->getAttributes())
					{						
						if(count($attribute)>1)
							throw new \Exception('Более одного атрибута Reflection у свойства '.$property);
						
						if(($class = static::$namespace.'\\'.end(explode('\\', $attribute[0]->getName()))) && class_exists($class))
							static::$keys[static::class]['objects'][$property->getName()] = $class;
						elseif(class_exists($attribute[0]->getName()))
							static::$keys[static::class]['objects'][$property->getName()] = $attribute[0]->getName();
						else
							throw new \Exception('Не существует класса атрибута '.$attribute[0]->getName());
					}
					// сохраним все оставшиеся параметры что объявляются в конткурукторе	
					elseif($property->isPromoted() && empty(static::$keys[static::class]['fields'][$property->getName()]))
						static::$keys[static::class]['construct'][$property->getName()]	= true;				
				}	
			}
		}
		
		return true;
	}
	
	function load():static
	{
		foreach(static::$keys[static::class]['objects'] as $name => $object)
		{
			if(!$this->$name && $object::keys())
			{
				if($object::TABLE)
				{	
					$where = array();
					foreach(static::$keys[$object]['foreigns'] as $key=>$foreign)
					{
						if(property_exists($this, $key))
						{
							$where[$foreign] = $this->$key;
						}
					}

					if($data = $object::get($where))
					{
						foreach($data as $row)
						{
							if(gettype($this->$name) == 'array'){
								$this->$name[] = (new $object(...$row))->load();	
							}								
							else
								$this->$name = (new $object(...$row))->load();		
						}
					}
				}
				else
				{
					if(gettype($this->$name) == 'array')
						throw new \Exception('свойство '.$name.' ('.$object.') в классе '.static::class.' не может быть массивом если не объявлена контстанта TABLE');
					
					if($data = array_filter(array_intersect_key((array)$this, (array)static::$keys[$object]['construct'])))
						$this->$name = (new $object(...$data))->load();	
				}
			}	
		}

		return $this;
	}
	
	function save():void
	{	
		if(static::$keys[static::class]['fields'])
		{	
			// аргументы что есть в объекте и что есть в бд
			static::update(array_intersect_key((array)$this, static::$keys[static::class]['fields']));
			
			// первичный ключ если есть
			if($increment = static::$keys[static::class]['increment'])
				$this->$increment = $this->last();
		}
		
		// здесь уже работаем с объектом существующим (не просто с классом что мы распарсили)  и надо проверить свойства
		if(static::$keys[static::class]['objects']){
			foreach(array_keys(static::$keys[static::class]['objects']) as $key)
			{
				if(!$this->$key) continue;				
				foreach((!is_array($this->$key)?array($this->$key):$this->$key) as $value)
				{
					if(is_object($value) && method_exists($value, 'save'))
					{					
						$this->set($value);	
						$value->save();
					}
					// остальное - все свойства вне конструктора не являющиеся объектами (для каких то внутренних целей)
				}
			}
		}
	}

	// передадим дочерним объектам либо зависимые (foreign)  ключи либо первичный ключ текущего обхекта
	private function set(object $value)
	{
		if (!$value::TABLE)
		{
			if(($increment = static::$keys[static::class]['increment']) && property_exists($value, $increment))
				$value->$increment = $this->$increment;
		}
		// если свойство - объект имеет таблицу для записи передадим все вторичные ключи
		elseif(static::$keys[$value::class]['foreigns'])
		{
			foreach(static::$keys[$value::class]['foreigns'] as $key=>$foreign)
			{
				if(property_exists($this, $key))
					$value->$foreign = $this->$key;
			}
		}	
	}
}