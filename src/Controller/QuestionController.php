<?php

namespace App\Controller;

use App\Entity\Hum;
use App\Entity\Language;
use App\Entity\Question;
use App\Form\QuestionType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class QuestionController extends AbstractController
{
    /**
     * @Route("/question", name="question")
     */
    public function index()
    {
        return $this->render('question/index.html.twig', [
            'controller_name' => 'QuestionController',
        ]);
    }

    /**
     * @Route("/question/{question}/add-translation", name="question_add_translation")
     */
    public function addTranslation(Question $question, Request $request)
    {
        $original = $question;
        $language = $this->getDoctrine()->getRepository(Language::class)
            ->findOneBy(["name" => "Svenska"]);
        $question = new Question();
        $question->setLanguage($language);
        $question->setTranslation($original);
        $question->setHum($original->getHum());
        $form = $this->createForm(QuestionType::class, $question);
        $form->add('submit', SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entitymanager = $this->getDoctrine()->getManager();
            $entitymanager->persist($question);
            $entitymanager->flush();

            return $this->redirectToRoute('hum_edit', ["hum" => $question->getHum()->getId()]);
        }

        return $this->render('question/add-translation.html.twig', [
            'form' => $form->createView(),
            'original' => $original
        ]);
    }

    /**
     * @Route("/question/{hum}/add", name="question_add_hum")
     */
    public function addToHum(Hum $hum, Request $request)
    {
        $language = $this->getDoctrine()->getRepository(Language::class)
            ->findOneBy(["name" => "English"]);
        $question = new Question();
        $question->setHum($hum);
        $question->setLanguage($language);
        $form = $this->createForm(QuestionType::class, $question);
        $form->add('submit', SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entitymanager = $this->getDoctrine()->getManager();
            $entitymanager->persist($question);
            $entitymanager->flush();

            return $this->redirectToRoute('hum_edit', ["hum" => $hum->getId()]);
        }

        return $this->render('question/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/question/{question}/edit", name="question_edit")
     */
    public function edit(Question $question, Request $request)
    {
        $form = $this->createForm(QuestionType::class, $question);
        $form->add("submit", SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entitymanager = $this->getDoctrine()->getManager();
            $entitymanager->persist($question);
            $entitymanager->flush();
        }

        return $this->render('question/edit.html.twig', [
            'form' => $form->createView(),
            'question' => $question
        ]);
    }

    /**
     * @Route("/question/{question}/delete", name="question_delete")
     */
    public function delete(Question $question, Request $request)
    {
        if ('POST' === $request->getMethod()) {
            $confirmation = $request->get("confirmation");
            if ($confirmation) {
                $entitymanager = $this->getDoctrine()->getManager();
                $entitymanager->remove($question);
                $entitymanager->flush();
                return $this->redirectToRoute('hum');
            }
        }

        return $this->render('policy/delete.html.twig', [
            'question' => $question
        ]);

    }
}
