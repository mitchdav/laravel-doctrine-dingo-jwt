<?php

namespace App\Transformers;

use Doctrine\Common\Proxy\Proxy;
use League\Fractal\TransformerAbstract;
use TempestTools\Scribe\Contracts\Orm\EntityContract;

class Transformer extends TransformerAbstract
{
	public function verifyItem(EntityContract $item)
	{
		if($item instanceof Proxy)
		{
			try
			{
				if($item->__isInitialized() == FALSE)
				{
					$item->__load();
				}
			} catch(\Exception $ex)
			{
				return FALSE;
			}
		}
		
		return TRUE;
	}
	
	public function item($data, $transformer, $resourceKey = NULL)
	{
		if($data != NULL)
		{
			try
			{
				return parent::item($data, $transformer, $resourceKey);
			} catch(\Exception $ex)
			{
				return NULL;
			}
		}
		
		else
		{
			return NULL;
		}
	}
	
	public function collection($data, $transformer, $resourceKey = NULL)
	{
		if($data != NULL)
		{
			try
			{
				return parent::collection($data, $transformer, $resourceKey);
			} catch(\Exception $ex)
			{
				return NULL;
			}
		}
		
		else
		{
			return NULL;
		}
	}
}