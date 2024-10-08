<?php

declare(strict_types=1);

namespace Pyz\Zed\AntelopeGui\Communication\Controller;

use Generated\Shared\Transfer\AntelopeTransfer;
use Pyz\Zed\AntelopeGui\Communication\Form\AntelopeCreateForm;
use Spryker\Service\UtilText\Model\Url\Url;
use Spryker\Zed\Kernel\Communication\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method \Pyz\Zed\AntelopeGui\Communication\AntelopeGuiCommunicationFactory getFactory()
 */
class CreateController extends AbstractController
{
    protected const URL_ANTELOPE_OVERVIEW = '/antelope-gui';

    protected const MESSAGE_ANTELOPE_CREATED_SUCCESS = 'Antelope was successfully created.';

    /**
     * @param Request $request
     * @return RedirectResponse|array<string,mixed>
     */
    public function indexAction(Request $request): RedirectResponse|array
    {
        $options[AntelopeCreateForm::LOCATION_CHOICES] = $this->getLocations();
        $antelopeCreateForm = $this->getFactory()
            ->createAntelopeCreateForm(new AntelopeTransfer(), $options)
            ->handleRequest($request);

        if ($antelopeCreateForm->isSubmitted() && $antelopeCreateForm->isValid()) {
            return $this->createAntelope($antelopeCreateForm);
        }

        return $this->viewResponse([
            'antelopeCreateForm' => $antelopeCreateForm->createView(),
            'backUrl' => $this->getAntelopeOverviewUrl(),
        ]);
    }

    private function getLocations()
    {
        $res = [];
        $result = $this->getFactory()->getAntelopeLocationPropelQuery()
            ->orderBy('location_name')->find();
        foreach ($result as $location) {
            $res[$location->getIdAntelopeLocation()] = $location->getLocationName();
        }
        return $res;
    }

    protected function createAntelope(FormInterface $antelopeCreateForm
    ): RedirectResponse {
        /** @var AntelopeTransfer|null $antelopeTransfer */
        $antelopeTransfer = $antelopeCreateForm->getData();
       
        $this->getFactory()
            ->getAntelopeFacade()
            ->createAntelope($antelopeTransfer);

        $this->addSuccessMessage(static::MESSAGE_ANTELOPE_CREATED_SUCCESS);

        return $this->redirectResponse($this->getAntelopeOverviewUrl());
    }

    protected function getAntelopeOverviewUrl(): string
    {
        return (string)Url::generate(static::URL_ANTELOPE_OVERVIEW);
    }
}
