<?php
declare(strict_types=1);

namespace App\Controller;

use App\Message\SendMessage;
use App\Repository\MessageRepository;
use App\Service\MessageDataValidationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Service\MessageMapperService;

/**
 * @see MessageControllerTest
 * TODO: review both methods and also the `openapi.yaml` specification
 *       Add Comments for your Code-Review, so that the developer can understand why changes are needed.
 */
class MessageController extends AbstractController
{
    /**
     * TODO: cover this method with tests, and refactor the code (including other files that need to be refactored)
     */
    
    private MessageDataValidationService $validationService;
    
    public function __construct(MessageDataValidationService $validationService)
    {
        $this->validationService = $validationService;
    }
    
    #[Route('/messages')]
    public function list(Request $request, MessageRepository $messageRep, MessageMapperService $mapperService): Response
    {
// it's unnecessary to transfer the whole request object to repository method
        /** @var string|null $status */
        $status = $request->query->get('status');
        /** @var string|null $limit */
        $limit = $request->query->get('limit');
        /** @var string|null $offset */
        $offset = $request->query->get('offset');
        
        $queryData = $request->query->all();
        
        /** @var array<string, string>|null $orderBy */
        $orderBy = isset($queryData['order_by'])
            ? (array) $queryData['order_by']
            : null;
        
// validate all allowed query parameters
        $errors = $this->validateParameters($status, $orderBy, $limit, $offset);
        if (!empty($errors)) {
            return new JsonResponse(['errors' => $errors], Response::HTTP_BAD_REQUEST);
        }
        
        if($orderBy && isset($orderBy['created_at'])) {
            $orderBy['createdAt'] = $orderBy['created_at'];
            unset($orderBy['created_at']);
        }
        
// naming variables with the same name leads to uncertainty
        $messages = $messageRep->findBy(
            empty($status) ? [] : ['status' => $status],
            $orderBy,
            $limit ? (int) $limit : null,
            $offset ? (int) $offset : null
        );

// separating logic from controller will help to achieve
// adhere to the single responsibility principle
        $responseData = $mapperService->serializeDTOsToArrays($messages);
        
// automatically sets the content type and handles JSON encoding errors
        return new JsonResponse(['messages' => $responseData]);
    }

// it's inappropriate and insecure to use GET method for sending data
    #[Route('/messages/send', methods: ['POST'])]
    public function send(Request $request, MessageBusInterface $bus): Response
    {
        $text = $request->request->getString('text');
        
// validate the send message request
        $validationErrors = $this->validationService->validateSendMessage(['text' => $text]);
        if (!empty($validationErrors)) {
            return new JsonResponse(['errors' => $validationErrors], Response::HTTP_BAD_REQUEST);
        }
        
// handling failure of dispatch()
        try {
            $bus->dispatch(new SendMessage($text));
        }
        catch (\Exception $exception){
            return new JsonResponse(['error' => 'Failed to send message'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        
// response with status 204 "No content" is not clear, use 201 "Created" instead
        return new Response('Successfully sent', Response::HTTP_CREATED);
    }
    
    /**
     * @param string|null $status
     * @param array<string>|null $orderBy
     * @param string|null $limit
     * @param string|null $offset
     * @return array<string|\Stringable>|null[]
     */
    private function validateParameters(?string $status, ?array $orderBy, ?string $limit, ?string $offset): array {
        $errors = [];
        
// validate the status parameter
        $statusErrors = $this->validationService->validateStatus($status);
        if (!empty($statusErrors)) {
            $errors = array_merge($errors, $statusErrors);
        }

// validate the orderBy parameter
        $orderByErrors = $this->validationService->validateOrderBy($orderBy);
        if (!empty($orderByErrors)) {
            $errors = array_merge($errors, $orderByErrors);
        }
        
// validate the limit and offset query parameters
        $queryParamsErrors = $this->validationService->validateQueryData([
            'limit' => $limit,
            'offset' => $offset
        ]);
        if (!empty($queryParamsErrors)) {
            $errors = array_merge($errors, $queryParamsErrors);
        }
        
        return $errors;
    }
}
