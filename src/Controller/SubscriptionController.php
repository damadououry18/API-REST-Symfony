<?php

namespace App\Controller;
use App\Entity\Subscription;
use App\Repository\SubscriptionRepository;
use App\Entity\Contact;
use App\Repository\ContactRepository;
use App\Entity\Product;

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
        // Décoder les données JSON
        $data = json_decode($request->getContent(), true);

        // Vérifiez si le JSON est valide
        if (json_last_error() !== JSON_ERROR_NONE) {
            return $this->json(['error' => 'Invalid JSON: ' . json_last_error_msg()], 400);
        }

        // Vérifiez que les champs nécessaires sont présents
        if (!isset($data['contact_id'], $data['product_id'], $data['beginDate'], $data['endDate'])) {
            return $this->json([
                'error' => 'Missing required fields: contact_id, product_id, beginDate, endDate'
            ], 400);
        }

        try {
            // Créez une nouvelle instance de Subscription
            $subscription = new Subscription();
            $subscription->setBeginDate(new \DateTime($data['beginDate']));
            $subscription->setEndDate(new \DateTime($data['endDate']));

            // Récupérez et associez le Contact
            $contact = $em->getRepository(Contact::class)->find($data['contact_id']);
            if (!$contact) {
                return $this->json(['error' => 'Contact not found.'], 404);
            }

            // Récupérez et associez le Product
            $product = $em->getRepository(Product::class)->find($data['product_id']);
            if (!$product) {
                return $this->json(['error' => 'Product not found.'], 404);
            }

            $subscription->setContact($contact);
            $subscription->setProduct($product);

            // Sauvegarder l'abonnement
            $em->persist($subscription);
            $em->flush();

            // Retourner l'abonnement créé
            return $this->json([
                'message' => 'Subscription created successfully.',
                'subscription' => $subscription
            ], 201);

        } catch (\Exception $e) {
            return $this->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    // PUT /subscription/{idSubscription}

    #[Route('/subscription/{idSubscription}', methods: ['PUT'])]
    public function updateSubscription(int $idSubscription, Request $request, EntityManagerInterface $em, SubscriptionRepository $subscriptionRepo): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Vérifiez si le JSON est valide
        if (json_last_error() !== JSON_ERROR_NONE) {
            return $this->json(['error' => 'Invalid JSON: ' . json_last_error_msg()], 400);
        }

        // Vérifiez que les champs nécessaires sont présents
        if (!isset($data['beginDate'], $data['endDate'])) {
            return $this->json([
                'error' => 'Missing required fields: beginDate, endDate'
            ], 400);
        }

        // Récupérez l'abonnement à mettre à jour 
        if (!$subscription = $subscriptionRepo->find($idSubscription)) {
            return $this->json(['error' => 'Subscription not found'], 404);
        }

        try {
            // Mettre à jour les données
            $subscription->setBeginDate(new \DateTime($data['beginDate']));
            $subscription->setEndDate(new \DateTime($data['endDate']));

            // Enregistrez les modifications
            $em->flush();

            return $this->json($subscription, 200);
        } catch (\Exception $e) {
            return $this->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    //DELETE /subscription/{idSubscription}
    #[Route('/subscription/{idSubscription}', methods: ['DELETE'])]
    public function deleteSubscription(int $idSubscription, EntityManagerInterface $em, SubscriptionRepository $subscriptionRepo): JsonResponse
    {
        try {

            if (!$subscription = $subscriptionRepo->find($idSubscription)) {
                return $this->json(['error' => 'Subscription not found'], 404);
            }
    
            $em->remove($subscription);
            $em->flush();
    
            return $this->json(['message' => 'Subscription deleted']);
        } catch (\Exception $e) {
            return $this->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }
    
}
