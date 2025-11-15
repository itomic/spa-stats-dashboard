<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CourtCountAnalyzer
{
    protected string $apiKey;
    protected string $baseUrl = 'https://api.openai.com/v1';
    protected string $model = 'gpt-4o-mini'; // Model with web search support
    protected bool $useWebSearch = true; // Use OpenAI's native web search via Responses API

    public function __construct()
    {
        $this->apiKey = config('services.openai.api_key');
        
        if (empty($this->apiKey)) {
            throw new \RuntimeException('OpenAI API key is not configured. Please set OPENAI_API_KEY in your .env file.');
        }
    }

    /**
     * Analyze venue to extract the number of squash courts using OpenAI's web search.
     *
     * @param string $venueName
     * @param string|null $venueAddress
     * @param string|null $venueWebsite
     * @return array{court_count: int|null, confidence: string, reasoning: string, source_url: string|null, source_type: string|null, evidence_found: bool}
     */
    public function analyzeCourtCount(string $venueName, ?string $venueAddress = null, ?string $venueWebsite = null): array
    {
        try {
            // Build query for OpenAI web search
            $query = "How many squash courts does {$venueName}";
            if ($venueAddress) {
                $query .= " located at {$venueAddress}";
            }
            $query .= " have?";
            
            if ($venueWebsite) {
                $query .= " Check their website: {$venueWebsite}";
            }
            
            $query .= " Look for explicit numbers like '3 courts', 'three glass-back courts', etc. Convert written numbers to digits (three=3, two=2, etc.). If 'squash court' (singular), infer 1 court. If 'squash courts' (plural) without a number, infer 2 courts.";
            
            Log::info("Court count analysis: Using OpenAI Responses API with web search", [
                'venue_name' => $venueName,
                'query' => $query,
            ]);
            
            // Use the Responses API endpoint for web search
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(60)->post("{$this->baseUrl}/responses", [
                'model' => $this->model,
                'tools' => [
                    [
                        'type' => 'web_search',
                    ],
                ],
                'input' => $query . "\n\nIMPORTANT: After searching, return ONLY valid JSON in this format: {\"court_count\": <integer or null>, \"confidence\": \"HIGH|MEDIUM|LOW\", \"reasoning\": \"<explanation with source URL>\", \"source_url\": \"<url where info was found>\", \"source_type\": \"VENUE_WEBSITE|SOCIAL_MEDIA|BOOKING_PAGE|GOOGLE_REVIEWS|OTHER\", \"evidence_found\": <boolean>}.\n\nCRITICAL: Set evidence_found to TRUE if you find ANY mention of squash courts existing (even if you can't determine the exact count). Only set evidence_found to FALSE if there is NO evidence that squash courts exist at this venue (e.g., venue closed, no squash facilities, etc.).",
                'temperature' => 0.2,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                // Responses API returns different structure
                // The 'output' field is an array of message objects with 'content' arrays
                $content = '';
                
                if (isset($data['text']) && is_string($data['text'])) {
                    $content = $data['text'];
                } elseif (isset($data['output']) && is_array($data['output'])) {
                    foreach ($data['output'] as $outputItem) {
                        if (isset($outputItem['content']) && is_array($outputItem['content'])) {
                            // Content is an array of content parts
                            foreach ($outputItem['content'] as $contentPart) {
                                if (isset($contentPart['text'])) {
                                    $content .= $contentPart['text'];
                                }
                            }
                            if (!empty($content)) {
                                break;
                            }
                        } elseif (isset($outputItem['content']) && is_string($outputItem['content'])) {
                            $content = $outputItem['content'];
                            break;
                        }
                    }
                }
                
                Log::info("Court count analysis: OpenAI response received", [
                    'venue_name' => $venueName,
                    'status' => $data['status'] ?? 'unknown',
                    'output_count' => is_array($data['output'] ?? null) ? count($data['output']) : 0,
                    'content_length' => strlen($content),
                    'content_preview' => substr($content, 0, 200),
                ]);
                
                return $this->parseAIResponse($content);
            }

            // Handle API errors
            $errorMessage = $response->json('error.message') ?? 'Unknown error';
            $statusCode = $response->status();
            
            Log::warning('OpenAI API error in court count analysis', [
                'venue_name' => $venueName,
                'status_code' => $statusCode,
                'error' => $errorMessage,
                'response_body' => substr($response->body(), 0, 500),
            ]);

            return [
                'court_count' => null,
                'confidence' => 'LOW',
                'reasoning' => "OpenAI API error ({$statusCode}): {$errorMessage}",
                'source_url' => null,
                'source_type' => null,
                'evidence_found' => false,
            ];

        } catch (\Exception $e) {
            Log::error('Court count analysis exception', [
                'venue_name' => $venueName,
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'court_count' => null,
                'confidence' => 'LOW',
                'reasoning' => 'Exception: ' . $e->getMessage(),
                'source_url' => null,
                'source_type' => null,
                'evidence_found' => false,
            ];
        }
    }

    /**
     * Build the prompt for OpenAI.
     */
    protected function buildPrompt(string $venueName, array $searchResults): string
    {
        $prompt = "Analyze the following search results to determine how many squash courts the venue '{$venueName}' has.\n\n";
        $prompt .= "Search Results:\n";
        $prompt .= "===============\n\n";

        foreach ($searchResults as $index => $result) {
            $prompt .= "Result " . ($index + 1) . ":\n";
            $prompt .= "Source Type: {$result['source_type']}\n";
            $prompt .= "URL: {$result['url']}\n";
            $prompt .= "Title: {$result['title']}\n";
            $prompt .= "Content: {$result['snippet']}\n\n";
        }

        $prompt .= "Instructions:\n";
        $prompt .= "- Look for explicit numbers: '1 court', '2 courts', 'three courts', '3 glass-back courts', etc.\n";
        $prompt .= "- Convert written numbers to digits: 'three' = 3, 'two' = 2, 'one' = 1, 'four' = 4, etc.\n";
        $prompt .= "- If you find explicit mention of the number of courts (written or numeric), use that number.\n";
        $prompt .= "- If you see 'squash court' (singular) without a number, infer 1 court.\n";
        $prompt .= "- If you see 'squash courts' (plural) without a number, infer 2 courts (most common).\n";
        $prompt .= "- Look for phrases like 'three glass-back squash courts', '2 courts available', 'court 1', 'court 2', etc.\n";
        $prompt .= "- Prioritize information from venue websites and social media over other sources.\n";
        $prompt .= "- If booking pages show available courts, count those.\n";
        $prompt .= "- If NO evidence of squash courts is found at all, set evidence_found to false.\n";
        $prompt .= "- Return your analysis as valid JSON only (no markdown, no code blocks): {\"court_count\": <integer or null>, \"confidence\": \"HIGH|MEDIUM|LOW\", \"reasoning\": \"<detailed explanation>\", \"source_url\": \"<url or null>\", \"source_type\": \"VENUE_WEBSITE|SOCIAL_MEDIA|BOOKING_PAGE|GOOGLE_REVIEWS|OTHER\", \"evidence_found\": <boolean>}\n";

        return $prompt;
    }

    /**
     * Parse the AI response.
     */
    protected function parseAIResponse(string $content): array
    {
        // Try to parse as JSON
        $json = json_decode($content, true);
        
        if (json_last_error() === JSON_ERROR_NONE && is_array($json)) {
            return [
                'court_count' => isset($json['court_count']) && is_numeric($json['court_count']) ? (int) $json['court_count'] : null,
                'confidence' => $this->normalizeConfidence($json['confidence'] ?? 'LOW'),
                'reasoning' => $json['reasoning'] ?? 'AI analysis completed',
                'source_url' => $json['source_url'] ?? null,
                'source_type' => $this->normalizeSourceType($json['source_type'] ?? null),
                'evidence_found' => $json['evidence_found'] ?? true,
            ];
        }

        // Fallback: try to extract from text response
        Log::warning('Failed to parse OpenAI JSON response, attempting text extraction', [
            'content' => substr($content, 0, 200),
        ]);

        return $this->extractFromText($content);
    }

    /**
     * Extract court count from text response (fallback).
     */
    protected function extractFromText(string $content): array
    {
        // Look for patterns like "court_count": 2 or "2 courts"
        if (preg_match('/["\']?court_count["\']?\s*:\s*(\d+)/i', $content, $matches)) {
            $count = (int) $matches[1];
            return [
                'court_count' => $count,
                'confidence' => 'MEDIUM',
                'reasoning' => 'Extracted from AI response text',
                'source_url' => null,
                'source_type' => 'OTHER',
                'evidence_found' => true,
            ];
        }

        // Look for explicit numbers with "court" or "courts"
        if (preg_match('/(\d+)\s+(?:squash\s+)?court/i', $content, $matches)) {
            $count = (int) $matches[1];
            return [
                'court_count' => $count,
                'confidence' => 'MEDIUM',
                'reasoning' => 'Extracted number from text pattern',
                'source_url' => null,
                'source_type' => 'OTHER',
                'evidence_found' => true,
            ];
        }

        // Check for singular/plural indicators
        $hasSingular = preg_match('/squash\s+court\b(?!s)/i', $content);
        $hasPlural = preg_match('/squash\s+courts\b/i', $content);

        if ($hasSingular && !$hasPlural) {
            return [
                'court_count' => 1,
                'confidence' => 'MEDIUM',
                'reasoning' => 'Found singular "squash court" reference',
                'source_url' => null,
                'source_type' => 'OTHER',
                'evidence_found' => true,
            ];
        }

        if ($hasPlural && !$hasSingular) {
            return [
                'court_count' => 2,
                'confidence' => 'MEDIUM',
                'reasoning' => 'Found plural "squash courts" reference (assuming 2, most common)',
                'source_url' => null,
                'source_type' => 'OTHER',
                'evidence_found' => true,
            ];
        }

        // No evidence found
        return [
            'court_count' => null,
            'confidence' => 'LOW',
            'reasoning' => 'Could not extract court count from AI response',
            'source_url' => null,
            'source_type' => null,
            'evidence_found' => false,
        ];
    }

    /**
     * Normalize confidence level.
     */
    protected function normalizeConfidence(?string $confidence): string
    {
        $confidence = strtoupper($confidence ?? 'LOW');
        return in_array($confidence, ['HIGH', 'MEDIUM', 'LOW']) ? $confidence : 'LOW';
    }

    /**
     * Normalize source type.
     */
    protected function normalizeSourceType(?string $sourceType): ?string
    {
        if (!$sourceType) {
            return null;
        }

        $sourceType = strtoupper($sourceType);
        $validTypes = ['VENUE_WEBSITE', 'SOCIAL_MEDIA', 'BOOKING_PAGE', 'GOOGLE_REVIEWS', 'OTHER'];
        
        return in_array($sourceType, $validTypes) ? $sourceType : 'OTHER';
    }
}

