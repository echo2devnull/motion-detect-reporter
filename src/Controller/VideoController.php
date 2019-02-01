<?php

namespace App\Controller;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\Routing\Annotation\Route;

class VideoController
{
    /**
     * @Route("/video/{device}/{date}/{file}", name="video_show")
     * @param ParameterBagInterface $params
     * @param string $device
     * @param string $date
     * @param string $file
     * @return BinaryFileResponse
     * @throws \Exception
     */
    public function showAction(ParameterBagInterface $params, string $device, string $date, string $file)
    {
        if (!array_key_exists($device, $params->get('device_storage_path_map'))) {
            throw new \InvalidArgumentException('Unknown device: '.$device);
        }

        $path = $this->getVideoPath($params->get('device_storage_path_map'), $device, $date, $file);
        if (!is_readable($path)) {
            dump($path);
            throw new \Exception('File was not found');
        }

        $response = new BinaryFileResponse($path);
        $response->setAutoEtag();
        $response->headers->set('Content-Type', 'video/mp4');

        return $response;
    }

    /**
     * @param array $pathMaps
     * @param string $device
     * @param string $date
     * @param string $file
     * @return string
     */
    private function getVideoPath(array $pathMaps, string $device, string $date, string $file): string
    {
        return sprintf('%s/%s/%s', $pathMaps[$device], $date, $file);
    }
}
