<?php

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Controller;

use function json_decode;
use function json_last_error;
use Superdesk\ContentApiSdk\Exception\InvalidDataException;
use SWP\Bundle\BridgeBundle\Doctrine\ORM\PackageRepository;
use SWP\Component\Bridge\Model\ExternalDataInterface;
use SWP\Component\Bridge\Model\PackageInterface;
use SWP\Component\Common\Exception\NotFoundHttpException;
use SWP\Component\Common\Response\ResponseContext;
use SWP\Component\Common\Response\SingleResourceResponse;
use SWP\Component\Common\Response\SingleResourceResponseInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ExternalDataController extends Controller
{
    /**
     * @Route("/api/{version}/packages/extra/{slug}", options={"expose"=true}, defaults={"version"="v2"}, methods={"PUT"}, name="swp_api_core_add_extra_data")
     */
    public function setAction(Request $request, string $slug): SingleResourceResponseInterface
    {
        /** @var PackageRepository $packageRepository */
        $packageRepository = $this->get('swp.repository.package');
        /** @var PackageInterface $existingPackage */
        $existingPackage = $packageRepository->findOneBy(['slugline' => $slug]);
        if (null === $existingPackage) {
            throw new NotFoundHttpException(sprintf('Package with slug %s was not found', $slug));
        }

        $externalData = $existingPackage->getExternalData();
        if (null === $externalData) {
            foreach ($externalData as $data) {
                $packageRepository->remove($data);
            }
            $packageRepository->flush();
        }

        if (null !== $request->getContent()) {
            $validJson = self::getArrayFromJson($request->getContent());
            $responseData = [];
            foreach ($validJson as $key => $value) {
                /** @var ExternalDataInterface $externalData */
                $externalData = $this->get('swp.factory.external_data')->create();
                $externalData->setKey($key);
                $externalData->setValue($value);
                $externalData->setPackage($existingPackage);
                $packageRepository->persist($externalData);
                $responseData[$externalData->getKey()] = $externalData->getValue();
            }
            $packageRepository->flush();

            return new SingleResourceResponse($responseData, new ResponseContext(200));
        }

        return new SingleResourceResponse(['Provided request content is not valid JSON'], new ResponseContext(400));
    }

    /**
     * @Route("/api/{version}/packages/extra/{slug}", options={"expose"=true}, defaults={"version"="v2"}, methods={"GET"}, name="swp_api_core_get_extra_data")
     */
    public function getAction(string $slug): SingleResourceResponseInterface
    {
        /** @var PackageRepository $packageRepository */
        $packageRepository = $this->get('swp.repository.package');
        /** @var PackageInterface $existingPackage */
        $existingPackage = $packageRepository->findOneBy(['slugline' => $slug]);
        if (null === $existingPackage) {
            throw new NotFoundHttpException(sprintf('package with slug %s was not found', $slug));
        }

        $externalData = $existingPackage->getExternalData();
        if (null === $externalData) {
            return new SingleResourceResponse([], new ResponseContext(200));
        }

        $responseData = [];
        /** @var ExternalDataInterface $data */
        foreach ($externalData as $data) {
            $responseData[$data->getKey()] = $data->getValue();
        }

        return new SingleResourceResponse($responseData, new ResponseContext(200));
    }

    private static function getArrayFromJson($jsonString)
    {
        $jsonArray = json_decode($jsonString, true);
        if (null === $jsonArray || JSON_ERROR_NONE !== json_last_error()) {
            throw new InvalidDataException('Provided request content is not valid JSON');
        }

        return $jsonArray;
    }
}
