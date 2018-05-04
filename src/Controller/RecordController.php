<?php

namespace App\Controller;

use App\Entity\Record;
use App\Form\RecordType;
use App\Repository\RecordRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/")
 */
class RecordController extends Controller
{
    /**
     * @Route("/", name="record_index", methods="GET")
     */
    public function index(RecordRepository $recordRepository): Response
    {
        return $this->json($recordRepository->findAll());
    }

    /**
     * @Route("/new", name="record_new", methods="POST")
     */
    public function new(Request $request): Response
    {
        $record = new Record();
        $form = $this->createForm(RecordType::class, $record);
        $form->submit(json_decode($request->getContent(), true));

        if (!$form->isValid()) {
            return $this->json([
                'message' => 'Data validation error',
                'errors' => $this->normalizeFormErrors($form)
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($record);
        $em->flush();

        return $this->json($record);
    }

    /**
     * @Route("/{id}", name="record_show", methods="GET")
     */
    public function show(Record $record): Response
    {
        return $this->json($record);
    }

    /**
     * @Route("/{id}", name="record_edit", methods="PATCH")
     */
    public function edit(Request $request, Record $record): Response
    {
        $form = $this->createForm(RecordType::class, $record);
        $form->submit(json_decode($request->getContent(), true));

        if (!$form->isValid()) {
            return $this->json([
                'message' => 'Data validation error',
                'errors' => $this->normalizeFormErrors($form)
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $this->getDoctrine()->getManager()->flush();

        return $this->json($record);
    }

    /**
     * @Route("/{id}", name="record_delete", methods="DELETE")
     */
    public function delete(Request $request, Record $record): Response
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($record);
        $em->flush();

        return $this->json('', Response::HTTP_NO_CONTENT);
    }

    /**
     * @param FormInterface $form
     *
     * @return array
     */
    private function normalizeFormErrors(FormInterface $form)
    {
        $errors = [];
        foreach ($form->getErrors() as $error) {
            $errors[] = $error->getMessage();
        }
        foreach ($form->all() as $childForm) {
            if ($childForm instanceof FormInterface) {
                if ($childErrors = $this->normalizeFormErrors($childForm)) {
                    $errors[$childForm->getName()] = $childErrors;
                }
            }
        }

        return $errors;
    }
}
