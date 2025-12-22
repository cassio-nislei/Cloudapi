<?php

namespace Firebase;

/**
 * Interface FirebaseInterface
 *
 * @package Firebase
 */
interface FirebaseInterface
{
    /**
     * @param string $token Token
     * @return mixed
     */
    public function setToken($token);

    /**
     * @param string $baseURI Base URI
     * @return mixed
     */
    public function setBaseURI($baseURI);

    /**
     * @param int $seconds Seconds
     * @return mixed
     */
    public function setTimeOut($seconds);

    /**
     * @param string $path
     * @param mixed $data
     * @param array $options
     * @return mixed
     */
    public function set($path, $data, $options = []);

    /**
     * @param string $path
     * @param mixed $data
     * @param array $options
     * @return mixed
     */
    public function push($path, $data, $options = []);

    /**
     * @param string $path
     * @param mixed $data
     * @param array $options
     * @return mixed
     */
    public function update($path, $data, $options = []);

    /**
     * @param string $path
     * @param array $options
     * @return mixed
     */
    public function delete($path, $options = []);

    /**
     * @param string $path
     * @param array $options
     * @return mixed
     */
    public function get($path, $options = []);
}
