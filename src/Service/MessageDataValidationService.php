<?php
declare(strict_types=1);

namespace App\Service;

use Stringable;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

class MessageDataValidationService
{
    private ValidatorInterface $validator;
    
    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }
    
    /**
     * Validates the status parameter.
     * @param string|null $status
     * @return array<string>|null[]
     */
    public function validateStatus(?string $status): array
    {
        if (!empty($status)) {
            $validStatuses = ['read', 'sent'];
            if (!in_array($status, $validStatuses, true)) {
                return ['Invalid status provided'];
            }
        }

        return [];
    }
    
    /**
     * Validates the orderBy parameter.
     * @return array<string>|null[]
     * @param array<string, string>|null $orderBy
     */
    public function validateOrderBy(?array $orderBy): array
    {
        $errors = [];
        
        if(!empty($orderBy)) {
            $validKeys = ['id', 'created_at'];
            $validDirections = ['desc', 'asc'];
            
            $keyErrors = array_diff(array_keys($orderBy), $validKeys);
            $directionErrors = array_diff($orderBy, $validDirections);
            
            
            if ($keyErrors) {
                $errors[] = 'Invalid order keys provided in order_by parameter: ' . implode(', ', $keyErrors);
            }
            
            if ($directionErrors) {
                $errors[] = 'Invalid order directions provided in order_by parameter: ' . implode(', ', $directionErrors);
            }
        }
        
        return $errors;
    }
    
    /**
     * Validates the query params 'limit' and 'offset'.
     * @param array<string, string>|null[] $data
     * @return array<string|Stringable>|null[]
     */
    public function validateQueryData(?array $data): array
    {
        $errors = [];
        $constraints = new Assert\Collection([
            'limit' => [
                new Assert\Regex([
                    'pattern' => '/^\d+$/',
                    'message' => 'Only numbers are allowed for limit parameter.'
                ]),
            ],
            
            'offset' => [
                new Assert\Regex([
                    'pattern' => '/^\d+$/',
                    'message' => 'Only numbers are allowed for offset parameter.'
                ]),
            ],
        ]);
        
        $validationResult = $this->validator->validate($data, $constraints);
        
        foreach ($validationResult as $violation) {
            $errors[] = $violation->getMessage();
        }
        
        return $errors;
    }
    
    /**
     * Validates the send message request.
     * @param array<string, string>|null $data
     * @return array<int<0, max>, string|Stringable>|null[]
     */
    public function validateSendMessage(?array $data): array
    {
        $constraints = new Assert\Collection([
            'text' => [
                new Assert\NotBlank(['message' => 'Text is required']),
                new Assert\Length([
                    'max' => 250,
                    'maxMessage' => 'Text cannot be longer than 250 characters.',
                ]),
            ],
        ]);
        
        $validationResult = $this->validator->validate($data, $constraints);
        
        $errors = [];
        foreach ($validationResult as $violation) {
            $errors[] = $violation->getMessage();
        }
        
        return $errors;
    }
}
