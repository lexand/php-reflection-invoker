<?php

namespace reflection;
/**
 * Class ReflectionInvoker
 * <p/>
 * For invoking protected or private methods.
 *
 * @package reflection
 */
class ReflectionInvoker
{

    /**
     * @var ReflectionInvoker
     */
    private static $instance = null;

    /**
     * Get singleton instance
     *
     * @return ReflectionInvoker
     */
    public static function getSingleInstance()
    {
        if (self::$instance === null)
        {
            self::$instance = self::getInstance();
        }

        return self::$instance;
    }

    /**
     * Get new instance every time
     *
     * @return ReflectionInvoker
     */
    public static function getInstance()
    {
        return new ReflectionInvoker();
    }

    private function __construct()
    {
    }

    /**
     * @var \ReflectionObject[]
     */
    private $objectCache = [];
    /**
     * @var \ReflectionMethod[]
     */
    private $methodCache = [];

    /**
     * Invoke object method
     *
     * @param object|string $object String is used for invoking static methods of a class.
     * @param string        $method
     * @param array         $params
     * @return mixed
     */
    public function invoke($object, $method, array $params = [])
    {
        $method = self::getReflectionMethod($object, $method);
        $method->setAccessible(true);
        if (empty($params))
        {
            return $method->invoke($object);
        }
        else
        {
            if (is_object($object))
            {
                return $method->invokeArgs($object, $params);
            }
            else
            {
                return $method->invokeArgs(null, $params);
            }
        }
    }

    /**
     * Invoke class/object method
     *
     * @param string      $method Full qualified method name
     * @param object|null $object null for static methods
     * @param array       $args
     * @throws \InvalidArgumentException
     * @return mixed
     */
    public function invokeFQMN($method, $object = null, array $args = [])
    {
        $pos = strpos($method, '::');
        if ($pos === false)
        {
            throw new \InvalidArgumentException('FQMN must be specified');
        }
        $className = '\\' . ltrim(substr($method, 0, $pos), '\\');
        $methodName = substr($method, $pos + 2);
        if (empty($object))
        {
            return $this->invoke($className, $methodName, $args);
        }
        else
        {
            if (!is_object($object))
            {
                throw new \InvalidArgumentException('$object is not an object');
            }
            $objectClass = '\\' . get_class($object);
            if ($objectClass != $className)
            {
                throw new \InvalidArgumentException("{$objectClass} does not correspond to {$method}");
            }

            return $this->invoke($object, $methodName, $args);
        }
    }

    /**
     * @param $object
     * @param $method
     * @return \ReflectionMethod
     */
    private function getReflectionMethod($object, $method)
    {
        $cacheKey = md5(self::getObjectCacheKey($object) . $method);
        if (!isset($this->methodCache[$cacheKey]))
        {
            $obj = self::getReflectionObject($object);
            $this->methodCache[$cacheKey] = $obj->getMethod($method);
        }

        return $this->methodCache[$cacheKey];
    }

    /**
     * @param $object
     * @return \ReflectionObject
     */
    private function getReflectionObject($object)
    {
        $cacheKey = self::getObjectCacheKey($object);

        if (!isset($this->objectCache[$cacheKey]))
        {
            if (is_object($object))
            {

                $obj = new \ReflectionObject($object);
            }
            else
            {
                $obj = new \ReflectionClass($object);
            }
            $this->objectCache[$cacheKey] = $obj;
        }

        return $this->objectCache[$cacheKey];
    }

    /**
     * @param $object
     * @return string
     */
    private function getObjectCacheKey($object)
    {
        if (is_object($object))
        {
            $cacheKey = md5(spl_object_hash($object));

            return $cacheKey;
        }
        else
        {
            $cacheKey = md5($object);

            return $cacheKey;
        }
    }
}