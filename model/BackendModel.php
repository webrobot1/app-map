<?php
namespace Edisom\App\map\model;

class BackendModel extends \Edisom\Core\Model
{	
	function get(array $callback = null):array
	{
		return $this->query('SELECT * FROM map '.($callback?'WHERE '.static::explode($callback,' AND '):''));
	}	
		
	function replace(int $id)
	{	
		if($_FILES['file'] && ($folder = SITE_PATH."/data/".static::app().'/'.$id))
		{
			switch($_FILES['file']['type'])
			{
				case "application/zip":
				case "application/x-zip-compressed":
				
					$zip = new \ZipArchive();
					if ($zip->open($_FILES['file']['tmp_name'], \ZipArchive::RDONLY)!==TRUE) 
						throw new \Exception("Невозможно открыть архив");
					
					$access = array();
					for($i=0; $i<$zip->count(); $i++)
					{
						if(($filename = $zip->getNameIndex($i)) && ($path = pathinfo($filename)) && $path['extension'] == 'tmx')
						{
							if($path['dirname']!='.')
								throw new \Exception("Фаил карты фаил карты (.tmx) должен распологаться в корне проекта");
							if(!empty($content))
								throw new \Exception("Допустим только один фаил карты (.tmx)");
							else
								$content = $zip->getFromIndex($i);
						}
						elseif(in_array($path['extension'], ['png', 'jpg', 'jpeg', 'gif', 'tsx']))
						{
							$access[] = $filename;
						}
					}
					
					if(!$content)
						throw new \Exception("не найден фаил карты (.tmx) в архиве");
					
					if(file_exists($folder))
					{
						$files = new \RecursiveIteratorIterator(
							new \RecursiveDirectoryIterator($folder, \RecursiveDirectoryIterator::SKIP_DOTS),
							\RecursiveIteratorIterator::CHILD_FIRST
						);

						foreach ($files as $fileinfo) {
							$todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
							$todo($fileinfo->getRealPath());
						}
						rmdir($folder);
					}
					
					mkdir($folder);	
					
					if($access)
						$zip->extractTo($folder, $access);
					
					$zip->close();					
				break;				
				case "application/x-tiled-tmx":
					$content = file_get_contents($_FILES['file']['tmp_name']);
				break;
				default:
					throw new \Exception('неизвестный тип ('.$_FILES['file']['type'].'). Только ZIP архив или фаил карты (.tmx)');
				break;
			}
			
			// благодаря постоянному конекту транзакция распрочстраняется на все вызовы моделей по цепочки
			(new Tiled\Xml\Map($id))->parse($content)->save();						
		}	
	}
}