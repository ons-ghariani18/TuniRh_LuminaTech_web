<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Smalot\PdfParser\Parser;

class SkillsMatchingService
{
    private $huggingFaceService;
    private $pdfParser;
    private $logger;

    public function __construct(HuggingFaceService $huggingFaceService, Parser $pdfParser, LoggerInterface $logger)
    {
        $this->huggingFaceService = $huggingFaceService;
        $this->pdfParser = $pdfParser;
        $this->logger = $logger;
    }

    public function calculateCandidateScore(string $candidateSkillsText, ?string $cvPath, string $jobSkillsText): float
    {
        $candidateSkills = [];

        // Si un CV est fourni, extraire les compétences à partir du CV
        if ($cvPath && file_exists($cvPath)) {
            try {
                $pdf = $this->pdfParser->parseFile($cvPath);
                $cvText = $pdf->getText();
                $this->logger->debug('Extracted CV text', ['text' => substr($cvText, 0, 100)]);

                // Utiliser HuggingFace pour extraire les compétences du CV
                $extractedSkills = $this->huggingFaceService->extractSkills($cvText);
                $this->logger->debug('Extracted skills from CV (raw)', ['skills' => $extractedSkills]);

                // Nettoyer les compétences extraites
                foreach ($extractedSkills as $skill) {
                    // Supprimer les caractères inutiles comme "*" et les espaces en début/fin
                    $skill = trim($skill, " *");
                    // Ignorer les lignes qui ne sont pas des compétences (comme "Compétences techniques :")
                    if (empty($skill) || stripos($skill, 'Compétences') !== false) {
                        continue;
                    }
                    $candidateSkills[] = strtolower($skill);
                }
                $this->logger->debug('Extracted skills from CV (cleaned)', ['skills' => $candidateSkills]);
            } catch (\Exception $e) {
                $this->logger->error('Error parsing CV', ['error' => $e->getMessage()]);
            }
        }

        // Si aucune compétence n'a été extraite, retourner un score de 0
        if (empty($candidateSkills)) {
            $this->logger->warning('No skills extracted from CV');
            return 0.0;
        }

        // Extraire et nettoyer les compétences requises pour le poste
        $jobSkillsRaw = array_map('trim', explode(',', strtolower($jobSkillsText)));
        $jobSkills = [];
        foreach ($jobSkillsRaw as $skill) {
            // Supprimer les préfixes comme "languages de programmation: "
            $skill = preg_replace('/^languages de programmation:\s*/i', '', $skill);
            $skill = trim($skill);
            if (!empty($skill)) {
                $jobSkills[] = $skill;
            }
        }
        $jobSkills = array_filter($jobSkills);

        if (empty($jobSkills)) {
            $this->logger->warning('No job skills provided');
            return 0.0;
        }

        // Calculer le score en recherchant des correspondances partielles
        $matchingSkills = [];
        foreach ($candidateSkills as $candidateSkill) {
            foreach ($jobSkills as $jobSkill) {
                // Vérifier si la compétence du candidat contient ou est contenue dans la compétence requise
                if (stripos($candidateSkill, $jobSkill) !== false || stripos($jobSkill, $candidateSkill) !== false) {
                    $matchingSkills[] = $candidateSkill;
                    break; // Éviter les doublons
                }
            }
        }
        $matchingSkills = array_unique($matchingSkills);

        $score = (count($matchingSkills) / count($jobSkills)) * 100;
        $this->logger->info('Score calculated', [
            'candidate_skills' => $candidateSkills,
            'job_skills' => $jobSkills,
            'matching_skills' => $matchingSkills,
            'score' => $score
        ]);

        return round($score, 2);
    }
}