<?php

namespace App\Controller;
use App\Entity\Subscription;
use App\Repository\SubscriptionRepository;
use App\Entity\Contact;
use App\Repository\ContactRepository;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;


class SubscriptionController extends AbstractController
{
    private $contactRepository;

     #[Route('/', name: 'app_index')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/SubscriptionController.php',
        ]);
    }
  /*
    #[Route('/subscription', name: 'app_subscription')]
    public function subscription(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller subcription!',
            'path' => 'src/Controller/SubscriptionController.php',
        ]);
    
    }
   */

  // GET /subscription/{idContact} 
    #[Route('/subscription/{idContact}', methods: ['GET'])]
    public function getSubscriptionsByContact(int $idContact, SubscriptionRepository $subscriptionRepo): JsonResponse
    {
        $subscriptions = $subscriptionRepo->findBy(['contact' => $idContact]);
        return $this->json($subscriptions);
    }
     
    public function __construct(ContactRepository $contactRepository)
    {
        $this->contactRepository = $contactRepository;
    }

   // POST /subscription
  
   #[Route('/subscription', methods: ['POST'])]
    public function createSubscription(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $subscription = new Subscription();
        $subscription->setBeginDate(new \DateTime($data['beginDate']));
        $subscription->setEndDate(new \DateTime($data['endDate']));

        // Associer contact et produit
        $contact = $em->getRepository(Contact::class)->find($data['contact']);
        $product = $em->getRepository(Product::class)->find($data['product']);
        $subscription->setContact($contact);
        $subscription->setProduct($product);

        $em->persist($subscription);
        $em->flush();

        return $this->json($subscription, 201);
    }

    // PUT /subscription/{idSubscription}
     #[Route('/subscription/{id}', methods: ['PUT'])]
    public function updateSubscription(int $id, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $subscription = $em->getRepository(Subscription::class)->find($id);
        if (!$subscription) {
            return $this->json(['error' => 'Subscription not found'], 404);
        }

        $subscription->setBeginDate(new \DateTime($data['beginDate']));
        $subscription->setEndDate(new \DateTime($data['endDate']));
        $em->flush();

        return $this->json($subscription);
    }

    //DELETE /subscription/{idSubscription}
    #[Route('/subscription/{id}', methods: ['DELETE'])]
    /* public function deleteSubscription(int $id, EntityManagerInterface $em): JsonResponse */
    public function deleteSubscription(int $id, SubscriptionRepository $em): JsonResponse
    {
        $subscription = $em->findBy(['subscription' => $id]);

        /* $subscription = $em->getRepository(Subscription::class)->find($id); */

        if (!$subscription) {
            return $this->json(['error' => 'Subscription not found'], 404);
        }

        $em->remove($subscription);
        $em->flush();

        return $this->json(['message' => 'Subscription deleted']);
    }


}
