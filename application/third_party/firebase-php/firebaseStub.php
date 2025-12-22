<?php

namespace Firebase;

/**
 * Class FirebaseStub
 *
 * Stubs the Firebase interface without issuing any cURL requests.
 *
 * @package Firebase
 */
class FirebaseStub extends FirebaseLib implements FirebaseInterface
{
    protected $response;

    /**
     * @param string $path
     * @param mixed $data
     * @param array $options
     * @return mixed
     */
    public function set($path, $data, $options = [])
    {
        return $this->getSetResponse();
    }

    /**
     * @param string $path
     * @param mixed $data
     * @param array $options
     * @return mixed
     */
    public function push($path, $data, $options = [])
    {
        return $this->set($path, $data);
    }

    /**
     * @param string $path
     * @param mixed $data
     * @param array $options
     * @return mixed
     */
    public function update($path, $data, $options = [])
    {
        return $this->set($path, $data);
    }

    /**
     * @param string $path
     * @param array $options
     * @return mixed
     */
    public function get($path, $options = [])
    {
        return $this->getGetResponse();
    }

    /**
     * @param string $path
     * @param array $options
     * @return null
     */
    public function delete($path, $options = [])
    {
        return $this->getDeleteResponse();
    }

    /**
     * @param $expectedResponse
     */
    public function setResponse($expectedResponse)
    {
        $this->response = $expectedResponse;
    }

    /**
     * @return mixed
     */
    private function getSetResponse()
    {
        return $this->response;
    }

    /**
     * @return mixed
     */
    private function getGetResponse()
    {
        return $this->response;
    }

    /**
     * @return mixed
     */
    private function getDeleteResponse()
    {
        return $this->response;
    }
}
