<?php

namespace Core;

/**
 *
 * @method put($file, $name = null)
 * @method remove($file)
 * @method getServerId()
 * @method getUrl($path)
 * @method setServerId($server_id)
 * @method __returnObject()
 */
abstract class CDN
{
    public function __call($method, $args)
    {
        return null;
    }
}