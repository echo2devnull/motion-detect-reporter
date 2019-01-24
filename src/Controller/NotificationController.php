<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Service\NotificationService;
use Knp\Component\Pager\Paginator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NotificationController extends AbstractController
{
    /**
     * @Route("/")
     * @param Request $request
     * @param NotificationService $service
     * @return Response
     */
    public function actionIndex(Request $request, NotificationService $service, Paginator $paginator)
    {
        $notification = new Notification();

        $form = $this->createFormBuilder($notification)
            ->add('device', ChoiceType::class, [
                'choices'  => array_combine(Notification::getDevices(), Notification::getDevices()),
                'required' => false,
            ])
            ->add('dateStart', DateTimeType::class, ['widget' => 'choice',])
            ->add('dateEnd', DateTimeType::class)
            ->add('submit', SubmitType::class, ['label' => 'Apply'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $notification = $form->getData();
        }
        $pagination = $paginator->paginate(
            $service->getFindQuery(
                $notification->getDevice(),
                $notification->getDateStart(),
                $notification->getDateEnd()
            ),
            $request->query->getInt('page', 1)
        );

        return $this->render(
            'notification/list.html.twig',
            [
                'form' => $form->createView(),
                'pagination' => $pagination
            ]
        );
    }
}
